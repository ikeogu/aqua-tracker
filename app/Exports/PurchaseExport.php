<?php

namespace App\Exports;

use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

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
            'Price',
            'Size (Kg)',
            'Pieces (pcs)',
            'Amount (N)',
            'status'
        ];
    }

    public function collection()
    {
        return $this->customers->flatMap(function ($customer, $key) {
            $customerData = [
                $key + 1,
                $customer->name,
                ($customer->purchases->count() == 0) ? ''
                : ((($customer->purchases->where('status', 'paid')->count() == $customer->purchases->count()))
                ? 'completed' : 'incomplete'),

            ];

            $purchasesData = $customer->purchases->flatMap(function ($purchase) {
                return [
                     // Empty row to separate purchases data
                    [
                        '',
                        '',
                        '',
                        number_format($purchase->price_per_unit),
                        $purchase->size,
                        $purchase->pieces,
                        number_format($purchase->amount),
                        $purchase->status

                    ],

                    ['', '', '', '', '']
                ];
            });

            $totalAmount =  [
                '',
                '',
                '',
                '',
                $customer->purchases->sum('size') . ' (kg)',
                $customer->purchases->sum('pieces') . ' (pcs)',
                "₦". number_format( intval($customer->purchases->sum('amount'))),

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
