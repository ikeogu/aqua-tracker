<?php

namespace App\Http\Controllers;

use App\Actions\TeamMemberInvitation as ActionsTeamMemberInvitation;
use App\Enums\HttpStatusCode;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamMemberInvitation extends Controller
{
    //

    public function __invoke(Request $request) : JsonResponse

    {
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

    public function listTeamMembers(Request $request) : JsonResponse
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
}
