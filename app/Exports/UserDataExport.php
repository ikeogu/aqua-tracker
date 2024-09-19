<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Validation\ValidationException;

class UserDataExport implements FromCollection, WithHeadings, ShouldQueue
{
    public function  __construct(private $users) {}

    public function headings(): array
    {
        return [
            'S/N',
            'Name',
            'Email',
            'Telephone',
            'Subscription Plan',
        ];
    }

    public function collection()
    {
        $users = $this->users->map(function ($user, $key) {
            return [
                $key + 1,
                $user->name,
                $user->email,
                $user->telephone,
                $user->subscription_plan,

            ];
        });

        return $users;
    }

    /**
     * @throws ValidationException
     */
    public function failed()
    {
        throw ValidationException::withMessages(['message' => 'Something went wrong, please try again.']);
    }
}