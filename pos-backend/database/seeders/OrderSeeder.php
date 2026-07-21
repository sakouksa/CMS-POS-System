<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Currencies: USD (default), KHR
        DB::table('currencies')->insert([
            ['id' => 1, 'name' => 'US Dollar',      'code' => 'USD', 'symbol' => '$',  'exchange_rate' => 1.0000,   'is_default' => true,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Cambodian Riel',  'code' => 'KHR', 'symbol' => '៛', 'exchange_rate' => 4100.0000,'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Thai Baht',       'code' => 'THB', 'symbol' => '฿', 'exchange_rate' => 35.5000,  'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Product IDs and their selling prices
        $products = [
            ['id' => 1,  'price' => 1299.00],
            ['id' => 3,  'price' => 1099.00],
            ['id' => 5,  'price' => 799.00],
            ['id' => 6,  'price' => 699.00],
            ['id' => 7,  'price' => 429.00],
            ['id' => 8,  'price' => 1299.00],
            ['id' => 10, 'price' => 799.00],
            ['id' => 11, 'price' => 349.00],
            ['id' => 12, 'price' => 279.00],
            ['id' => 14, 'price' => 299.00],
            ['id' => 15, 'price' => 139.00],
            ['id' => 17, 'price' => 199.00],
            ['id' => 19, 'price' => 179.00],
            ['id' => 24, 'price' => 599.00],
            ['id' => 25, 'price' => 349.00],
            ['id' => 28, 'price' => 1999.00],
            ['id' => 30, 'price' => 1099.00],
            ['id' => 32, 'price' => 999.00],
            ['id' => 33, 'price' => 649.00],
            ['id' => 35, 'price' => 599.00],
            ['id' => 37, 'price' => 1499.00],
            ['id' => 38, 'price' => 549.00],
            ['id' => 39, 'price' => 499.00],
            ['id' => 41, 'price' => 499.00],
            ['id' => 43, 'price' => 249.00],
            ['id' => 44, 'price' => 129.00],
            ['id' => 45, 'price' => 199.00],
            ['id' => 46, 'price' => 349.00],
            ['id' => 47, 'price' => 89.00],
            ['id' => 50, 'price' => 59.00],
            ['id' => 51, 'price' => 29.00],
            ['id' => 52, 'price' => 19.00],
            ['id' => 53, 'price' => 49.00],
            ['id' => 54, 'price' => 59.00],
            ['id' => 55, 'price' => 399.00],
            ['id' => 56, 'price' => 279.00],
            ['id' => 57, 'price' => 249.00],
            ['id' => 58, 'price' => 89.00],
            ['id' => 59, 'price' => 35.00],
            ['id' => 60, 'price' => 19.00],
        ];

        $orderStatuses  = ['completed', 'completed', 'completed', 'pending', 'cancelled'];
        $paymentMethods = [1, 2, 3, 5, 7]; // cash, bank, KHQR, ABA Pay, Card
        $cashierIds     = [4, 5]; // cashier 1 and 2

        $orders    = [];
        $orderItems= [];

        for ($o = 1; $o <= 200; $o++) {
            $customerId   = ($o % 100) + 1;   // cycle through 100 customers
            $payMethodId  = $paymentMethods[$o % count($paymentMethods)];
            $status       = $orderStatuses[$o % count($orderStatuses)];
            $cashierId    = $cashierIds[$o % count($cashierIds)];
            $orderDate    = date('Y-m-d H:i:s', strtotime("-{$o} days +{$o} hours", strtotime('2026-07-20')));

            // Pick 1-4 products per order
            $numItems    = ($o % 4) + 1;
            $totalAmount = 0;
            $itemsForOrder = [];

            for ($i = 0; $i < $numItems; $i++) {
                $product  = $products[($o + $i) % count($products)];
                $qty      = ($i % 3) + 1;
                $unitPrice= $product['price'];
                $discount = ($o % 7 === 0) ? round($unitPrice * 0.05, 2) : 0.00;
                $subTotal = round(($unitPrice - $discount) * $qty, 2);
                $totalAmount += $subTotal;

                $itemsForOrder[] = [
                    'order_id'   => $o,
                    'product_id' => $product['id'],
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'discount'   => $discount,
                    'sub_total'  => $subTotal,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $orderDiscount = ($o % 10 === 0) ? round($totalAmount * 0.03, 2) : 0.00;
            $grandTotal    = round($totalAmount - $orderDiscount, 2);

            $orders[] = [
                'order_no'          => 'INV-2026-' . str_pad($o, 4, '0', STR_PAD_LEFT),
                'customer_id'       => $customerId,
                'payment_method_id' => $payMethodId,
                'currency_id'       => 1, // USD
                'total_amount'      => round($totalAmount, 2),
                'discount'          => $orderDiscount,
                'grand_total'       => $grandTotal,
                'order_status'      => $status,
                'created_by'        => $cashierId,
                'created_at'        => $orderDate,
                'updated_at'        => $orderDate,
            ];

            foreach ($itemsForOrder as $item) {
                $orderItems[] = $item;
            }
        }

        DB::table('orders')->insert($orders);
        DB::table('order_items')->insert($orderItems);
    }
}
