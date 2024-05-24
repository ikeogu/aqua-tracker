<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Role as ModelsRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $user = User::updateOrCreate([
            'first_name' => 'Super Admin',
            "last_name" => "User",
            'email' => 'aquatrack.services@gmail.com',
       ],
        [
            'password' => Hash::make('@Biology29'),
            'email_verified_at' => now(),
            'fully_onboarded' => true
        ]);

        $role =ModelsRole::where('name',Role::SUPER_ADMIN->value)->first();
        $user->assignRole($role);

       $this->call(
            RoleSeeder::class,
       );
    }
}
