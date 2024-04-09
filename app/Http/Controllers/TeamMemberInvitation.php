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
}
