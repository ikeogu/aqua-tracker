<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum Role: string
{
    use EnumValues;

    case FARM_EMPLOYEE = "FARM_EMPLOYEE";
    case ORGANIZATION_OWNER = "ORGANIZATION_OWNER";
    case FARM_ADMIN = "FARM_ADMIN";
    case ORGANIZATION_TEAM_MEMBER = "ORGANIZATION_TEAM_MEMBER";

    case FARM_TEAM_OWNER = "FARM_TEAM_OWNER";
    case SUPER_ADMIN = "SUPER_ADMIN";


    public static function getRoleTextName(string|Role $role)
    {
        return match ($role) {
            Role::FARM_EMPLOYEE => "Farm Employee",
            Role::ORGANIZATION_OWNER => "Organization Owner",
            Role::FARM_ADMIN => "Farm Admin",
            Role::ORGANIZATION_TEAM_MEMBER => "Organization Team Member",
            Role::FARM_TEAM_OWNER => "Farm Team Owner",
            Role::SUPER_ADMIN => "Super Admin",
            default => null,
        };
    }

    public static function getRoleNames(string $role ): string
    {
        return match ($role) {
            "Farm Employee" => Role::FARM_EMPLOYEE->value,
            "Organization Owner" => Role::ORGANIZATION_OWNER->value,
           "Farm Admin"  => Role::FARM_ADMIN->value
        };
    }
}
