<?php

namespace App\Exports;

use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PurchaseExport implements FromCollection, WithHeadings, ShouldAutoSize
{


    public function __construct(private $customers)
    {
    }

    public function headings(): array
    {
        return [
            'S/N',
            'Name',
            'Payment Status',
            'Total Amount',
            'Price',
            'Size',
            'Pieces',
            'Amount',
            'status'


        ];
    }

    public function collection()
    {
        return $this->customers->flatMap(function ($customer, $key) {
            $customerData = [
                $key + 1,
                $customer->name,
                ($customer->purchases->count() == 0) ? '' :
                         ((($customer->purchases->where('status', 'paid')->count() == $customer->purchases->count())) ? 'completed' : 'incomplete'),
                $customer->purchases->sum('amount'),
            ];

            $purchasesData = $customer->purchases->flatMap(function ($purchase) {
                return [
                    ['', '', '', '', ''], // Empty row to separate purchases data
                    [
                        '',
                        '',
                        '',
                        '',
                        $purchase->price_per_unit,
                        $purchase->size,
                        $purchase->pieces,
                        $purchase->amount,
                        $purchase->status

                    ],
                ];
            });

            return [$customerData, $purchasesData];
        });
    }





    /**
     * @throws ValidationException
     */
    public function failed()
    {
        throw ValidationException::withMessages(['message' => 'Something went wrong, please try again.']);
    }
}
