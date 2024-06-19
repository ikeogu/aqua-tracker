<?php


namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\Role as ModelsRole;
use App\Models\User;
use App\Notifications\TeamMemberInvitationNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Tenant;

class TeamMemberInvitation
{
    public static function execute(array $data, ModelsRole $role): void
    {
        /** @var Tenant $tenant */
        $tenant = auth()->user()->tenant;

        Arr::map($data, function ($email) use ($tenant, $role) {

            $pwd = Str::random(8);

            $user = User::withTrashed()->where('email', $email)->first();
            if ($user) {
                $user->restore();
            }

            if (!$user) {
                /** @var User $user */
                $user = User::query()->create(['email' => $email], [
                    'status' => Status::PENDING->value,
                    'password' => Hash::make($pwd),
                    'fully_onboarded' => false,
                    'team_member_onboarded' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $tenant->users()->attach($user, [
                'role' => $role->name,
                'status' => Status::PENDING->value,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $user->assignRole($role);
            $user->markEmailAsVerified();


            $user->notify(new TeamMemberInvitationNotification($tenant, $role->title, $pwd));
        });
    }
}
