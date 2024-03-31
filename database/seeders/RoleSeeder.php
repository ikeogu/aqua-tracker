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

        ];

        $permissions = [];


        collect($permissions)->map(function (array $permission) {
            Permission::create([
                'name' => $permission['name'],
                'description' => $permission['description'],
                'group' => $permission['group']
            ]);
        });

        collect($roles)->each(function (array $role) {
            ModelsRole::create([
                'name' => $role['name'],
                'title' => $role['title'],
                'description' => $role['description'],
                'guard_name' => 'api'
            ]);
        });


    }
}
