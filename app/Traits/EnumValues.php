<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait EnumValues
{
    public static function toArray(): array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values[$case->value] = $case->name;
        }

        return $values;
    }

    public static function valuesToArray(): array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values = [...$values, $case->value];
        }

        return $values;
    }

    public static function toArrayExcept(array $except): array
    {
        $values = self::toArray();

        return Arr::except($values, $except);
    }

    public static function valuesToArrayExcept(array $except): array
    {
        $values = self::valuesToArray();

        return array_diff($values, $except);
    }

    public static function getCase(string $case) : self
    {
        return self::cases()[$case];
    }
}
