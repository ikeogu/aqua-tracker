<?php

namespace Database\Seeders;

use App\Enums\Role;
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

        $user->assignRole(Role::SUPER_ADMIN->value);

       $this->call(
            RoleSeeder::class,
       );
    }
}
