<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // ទាញយក Purchase រួមជាមួយ Supplier
        return Purchase::with(['supplier', 'paymentMethod'])->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Reference',
            'Supplier',
            'Date',
            'Total Amount',
            'Discount',
            'Tax',
            'Grand Total',
            'Paid Amount',
            'Due Amount',
            'Payment Method',
            'Status'
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->purchase_no,
            $purchase->reference_no,
            $purchase->supplier->name ?? 'N/A',
            $purchase->purchase_date,
            $purchase->total_amount,
            $purchase->discount,
            $purchase->tax,
            $purchase->grand_total,
            $purchase->paid_amount,
            $purchase->due_amount,
            $purchase->paymentMethod->name ?? 'N/A',
            strtoupper($purchase->payment_status),
        ];
    }
}
