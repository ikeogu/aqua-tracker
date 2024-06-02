<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as ModelsRole;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $roles =  [


            [
                'title' => 'Organization Owner',
                'name' => Role::ORGANIZATION_OWNER->value,
                'description' => 'Can manage and info abt organization'
            ],
            [
                'title' => 'Organization Team Member',
                'name' => Role::ORGANIZATION_TEAM_MEMBER->value,
                'description' => 'those who are part of the organization'
            ],

            [
                'title' => 'Farm Team Owner',
                'name' => Role::FARM_TEAM_OWNER->value,
                'description' => 'Can manage and info abt farm'
            ],
            [
                'title' => 'Farm Admin',
                'name' => Role::FARM_ADMIN->value,
                'description' => 'Can manage and info abt farm'
            ],
            [
                'title' => 'Farm Employee',
                'name' => Role::FARM_EMPLOYEE->value,
                'description' => 'Can manage and info abt farm'
            ],
            [
                'title' => 'Super Admin',
                'name' => Role::SUPER_ADMIN->value,
                'description' => 'Can manage and info abt farm'
            ],
            [
                'title' => 'Admin',
                'name' => Role::ADMIN->value,
                'description' => 'Can mamange entire application'
            ],

            [
                'title' => 'View Farms',
                'name' => Role::VIEW_FARMS->value,
                'description' => 'Can view farms'
            ],
            [
                'title' => 'Edit Farms',
                'name' => Role::EDIT_FARMS->value,
                'description' => 'Can edit farms'
            ],

        ];

        $permissions = [

            [
                'name' => 'can view',
                'description' => 'Can view',
                'group' => Role::ADMIN->value,
            ],

            [
                'name' => 'can edit',
                'description' => 'Can edit',
                'group' => Role::ADMIN->value,
            ],

            [
                'name' => 'can view',
                'description' => 'Can view',
                'group' => Role::SUPER_ADMIN->value,
            ],
            [
                'name' => 'can edit',
                'description' => 'Can edit',
                'group' => Role::SUPER_ADMIN->value,
            ],
            [
                'name' => 'remove',
                'description' => 'Can remove',
                'group' => Role::SUPER_ADMIN->value,
            ],
            [
                'name' => 'can view',
                'group' => Role::VIEW_FARMS->value,
                'description' => 'Can view farms'
            ],
            [
                'name' => 'can edit',
                'group' => Role::EDIT_FARMS->value,
                'description' => 'Can edit farms'
            ],
            [
                'name' => 'can create',
                'group' => Role::EDIT_FARMS->value,
                'description' => 'Can edit farms'
            ],
            [
                'name' => 'can view',
                'group' => Role::EDIT_FARMS->value,
                'description' => 'Can edit farms'
            ],
            // farm admin
            [
                'name' => 'can view',
                'group' => Role::FARM_ADMIN->value,
                'description' => 'Can view farms'
            ],
            [
                'name' => 'can edit',
                'group' => Role::FARM_ADMIN->value,
                'description' => 'Can edit in farm'
            ],
            [
                'name' => 'can create',
                'group' => Role::FARM_ADMIN->value,
                'description' => 'Can create in farm'
            ],
            [
                'name' => 'can delete',
                'group' => Role::FARM_ADMIN->value,
                'description' => 'Can delete in farm'
            ],
            // farm owner
            [
                'name' => 'can view',
                'group' => Role::ORGANIZATION_OWNER->value,
                'description' => 'Can view farms'
            ],
            [
                'name' => 'can edit',
                'group' => Role::ORGANIZATION_OWNER->value,
                'description' => 'Can edit in farm'
            ],
            [
                'name' => 'can create',
                'group' => Role::ORGANIZATION_OWNER->value,
                'description' => 'Can create in farm'
            ],
            [
                'name' => 'can delete',
                'group' => Role::ORGANIZATION_OWNER->value,
                'description' => 'Can delete in farm'
            ],
            // farm team owner
            [
                'name' => 'can view',
                'group' => Role::FARM_TEAM_OWNER->value,
                'description' => 'Can view farms'
            ],
            [
                'name' => 'can edit',
                'group' => Role::FARM_TEAM_OWNER->value,
                'description' => 'Can edit in farm'
            ],
            [
                'name' => 'can create',
                'group' => Role::FARM_TEAM_OWNER->value,
                'description' => 'Can create in farm'
            ],
            [
                'name' => 'can delete',
                'group' => Role::FARM_TEAM_OWNER->value,
                'description' => 'Can delete in farm'
            ],
        ];


        collect($permissions)->map(function (array $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'group' => $permission['group']
                ]
            );
        });

        collect($roles)->each(function (array $role) use ($permissions) {
            $role =  ModelsRole::updateOrCreate(
                [
                    'name' => $role['name'],
                    'title' => $role['title']
                ],
                [
                    'description' => $role['description'],
                    'guard_name' => 'api'
                ]
            );

            if ($role['name'] === Role::VIEW_FARMS->value || $role['name'] === Role::EDIT_FARMS->value) {
                $mappedPermissions = collect($permissions)->filter(function ($permission) use ($role) {

                    return $permission['group'] === $role['name'];
                })->map(function ($permission) {
                    return $permission['name'];
                });

                $role->syncPermissions($mappedPermissions);
            }
        });
    }
}
