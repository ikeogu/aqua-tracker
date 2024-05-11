<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum Role: string
{
    use EnumValues;

    case FARM_EMPLOYEE = "FARM EMPLOYEE";
    case ORGANIZATION_OWNER = "ORGANIZATION OWNER";
    case FARM_ADMIN = "FARM ADMIN";
    case VIEW_FARMS = "VIEW FARMS";
    case EDIT_FARMS = "EDIT FARMS";
    case ORGANIZATION_TEAM_MEMBER = "ORGANIZATION TEAM MEMBER";

    case FARM_TEAM_OWNER = "FARM TEAM OWNER";
    case SUPER_ADMIN = "SUPER ADMIN";
    case ADMIN = "ADMIN";


    public static function getRoleTextName(string|Role $role)
    {
        return match ($role) {
            Role::FARM_EMPLOYEE => "Farm Employee",
            Role::ORGANIZATION_OWNER => "Organization Owner",
            Role::FARM_ADMIN => "Farm Admin",
            Role::ORGANIZATION_TEAM_MEMBER => "Organization Team Member",
            Role::FARM_TEAM_OWNER => "Farm Team Owner",
            Role::SUPER_ADMIN => "Super Admin",
            Role::ADMIN => "Admin",
            Role::VIEW_FARMS => "View Farms",
            Role::EDIT_FARMS => "Edit Farms",
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
