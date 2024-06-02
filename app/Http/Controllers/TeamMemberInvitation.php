<?php

namespace App\Http\Controllers;

use App\Actions\TeamMemberInvitation as ActionsTeamMemberInvitation;
use App\Enums\HttpStatusCode;
use App\Enums\Role as EnumsRole;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use BootstrapTeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamMemberInvitation extends Controller
{
    //

    public function __invoke(Request $request): JsonResponse

    {
         /** @var User $user */
         $user = auth()->user();
         if ($user->hasAnyRole([EnumsRole::VIEW_FARMS->value, EnumsRole::EDIT_FARMS->value])) {
             return $this->error(
                 message: "unathourized area.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }

        $request->validate([
            'emails' => 'required|array',
            'emails.*' => 'required|email',
            'role' => 'required|exists:roles,id',
        ]);


        /** @var Role $role */
        $role = Role::find($request->role);
        ActionsTeamMemberInvitation::execute($request->emails, $role);

        return $this->success(
            message: "Invitation sent successfully",
            code: HttpStatusCode::CREATED->value
        );
    }

    public function listTeamMembers(Request $request): JsonResponse
    {
        /** @var Tenant $tenant */
        $tenant = auth()->user()->tenant;

        $teamMembers = $tenant->teamMembers()->paginate($request->per_page ?? 20)
            ->map(function ($teamMember) {

                return [
                    'id' => $teamMember->id,
                    'first_name' => $teamMember->first_name,
                    'last_name' => $teamMember->last_name,
                    'email' => $teamMember->email,
                    'phone_number' => $teamMember->pivot->data['phone_number'] ?? null,
                    'role' => $teamMember->role,
                    'status' => $teamMember->pivot->status
                ];
            });

        return $this->success(
            message: "Team members fetched successfully",
            data: $teamMembers,
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function updateTeamMember(Request $request, string $teamMemberId): JsonResponse
    {
         /** @var User $user */
         $user = auth()->user();
         if ($user->hasAnyRole([EnumsRole::VIEW_FARMS->value, EnumsRole::EDIT_FARMS->value])) {
             return $this->error(
                 message: "unathourized area.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }
        $request->validate([
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string',
            'role' => 'nullable|exists:roles,id',
            'status' => 'nullable|in:active,inactive'
        ]);

        /** @var Tenant $tenant */
        $tenant = auth()->user()->tenant;

        $teamMember = $tenant->teamMembers()->where('user_id', $teamMemberId)->first();
        /** @var Role $role */
        $role = Role::find($request->role);

        $teamMember->update($request->only(['first_name', 'last_name', 'email', 'phone_number']));
        $teamMember->pivot->update([
            'role' => $role->name ?? $teamMember->pivot->role,
            'status' => $request->status
        ]);


        $teamMember->syncRoles($role);


        return $this->success(
            message: "Team member updated successfully",
            data: $teamMember,
            code: HttpStatusCode::SUCCESSFUL->value

        );
    }

    public function deleteTeamMember(string $teamMemberId): JsonResponse
    {

         /** @var User $user */
         $user = auth()->user();
         if ($user->hasAnyRole([EnumsRole::VIEW_FARMS->value, EnumsRole::EDIT_FARMS->value])) {
             return $this->error(
                 message: "unathourized area.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }

        /** @var Tenant $tenant */
        $tenant = auth()->user()->tenant;

        $teamMember = $tenant->teamMembers()->where('users.id', $teamMemberId)->first();
        $tenant->teamMembers()->detach($teamMemberId);

        if (DB::table('tenant_user')->where('user_id', $teamMemberId)->count() === 0) {
            $teamMember->delete();
        }

        return $this->success(
            message: "Team member deleted successfully",
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function onboardTeamMember(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        BootstrapTeamMember::execute($user, $request);

        return $this->success(
            message: "Team member onbaorded successfully",
            data: new UserResource($user),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
