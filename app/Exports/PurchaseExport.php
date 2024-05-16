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
                ($customer->purchases->count() == 0) ? '' : ((($customer->purchases->where('status', 'paid')->count() == $customer->purchases->count())) ? 'completed' : 'incomplete'),

            ];

            $purchasesData = $customer->purchases->flatMap(function ($purchase) {
                return [
                     // Empty row to separate purchases data
                    [
                        '',
                        '',
                        '',
                        '',
                        number_format($purchase->price_per_unit,2),
                        $purchase->size,
                        $purchase->pieces,
                        number_format($purchase->amount,2),
                        $purchase->status

                    ],

                    ['', '', '', '', '']
                ];
            });

            $totalAmount =  [
                number_format($customer->purchases->sum('amount'),2),
            ];

            return [$customerData, $purchasesData, $totalAmount];
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
