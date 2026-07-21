<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Products and their approximate cost prices (roughly 70-80% of selling price)
        // We'll use product IDs 1-30 for variety
        $purchaseData = [];
        $purchaseItemData = [];

        $statuses        = ['received', 'pending', 'ordered'];
        $paymentStatuses = ['paid', 'partial', 'due'];
        $paymentMethods  = [1, 2, 3]; // cash, bank, KHQR
        $suppliers       = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]; // supplier IDs

        // Product ID => [unit_cost, typical_qty]
        $productCosts = [
            1  => [980.00, 5],   // iPhone 16 Pro Max 256GB
            2  => [1130.00, 3],  // iPhone 16 Pro Max 512GB
            3  => [830.00, 8],   // iPhone 16 Pro 256GB
            4  => [900.00, 5],   // iPhone 15 Pro Max
            5  => [600.00, 10],  // iPhone 15
            6  => [530.00, 10],  // iPhone 14
            7  => [320.00, 15],  // iPhone SE
            8  => [980.00, 8],   // Samsung S25 Ultra
            9  => [750.00, 8],   // Samsung S25+
            10 => [600.00, 10],  // Samsung S25
            11 => [260.00, 20],  // Samsung A55
            12 => [210.00, 25],  // Samsung A35
            13 => [600.00, 5],   // Xiaomi 15 Pro
            14 => [225.00, 15],  // Redmi Note 14 Pro
            15 => [105.00, 30],  // Redmi 13C
            16 => [380.00, 8],   // Oppo Reno 13 Pro
            17 => [150.00, 20],  // Oppo A3 Pro
            18 => [530.00, 5],   // Vivo X100 Pro
            19 => [135.00, 25],  // Vivo Y36
            20 => [300.00, 8],   // Huawei Nova 12 Pro
            21 => [195.00, 15],  // Realme 12 Pro+
            22 => [750.00, 3],   // iPad Pro M4 11"
            23 => [990.00, 2],   // iPad Pro M4 13"
            24 => [450.00, 5],   // iPad Air M2
            25 => [260.00, 8],   // iPad 10th
            26 => [830.00, 3],   // Samsung Tab S9 Ultra
            27 => [210.00, 10],  // Samsung Tab A9+
            28 => [1500.00, 4],  // MacBook Pro M4 14"
            29 => [2250.00, 2],  // MacBook Pro M4 Pro 16"
            30 => [825.00, 6],   // MacBook Air M3 13"
        ];

        $purchaseId = 1;
        for ($p = 1; $p <= 100; $p++) {
            $supplierId    = $suppliers[$p % count($suppliers)];
            $status        = $statuses[$p % count($statuses)];
            $payStatus     = $paymentStatuses[$p % count($paymentStatuses)];
            $payMethodId   = $paymentMethods[$p % count($paymentMethods)];
            $createdBy     = ($p % 2 === 0) ? 2 : 3; // admin or manager

            // Pick 2-4 products per purchase
            $numItems    = ($p % 3) + 2;
            $totalAmount = 0;

            $purchaseDate = date('Y-m-d', strtotime("-{$p} days", strtotime('2026-07-20')));

            $itemsForPurchase = [];
            $productKeys = array_keys($productCosts);
            for ($i = 0; $i < $numItems; $i++) {
                $productId = $productKeys[($p + $i) % count($productKeys)];
                [$unitCost, $defQty] = $productCosts[$productId];
                $qty      = max(1, (int)($defQty * (0.5 + ($p % 3) * 0.3)));
                $subTotal = round($unitCost * $qty, 2);
                $totalAmount += $subTotal;

                $itemsForPurchase[] = [
                    'purchase_id'        => $p,
                    'product_id'         => $productId,
                    'quantity'           => $qty,
                    'purchase_unit_cost' => $unitCost,
                    'sub_total'          => $subTotal,
                    'expiry_date'        => null,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }

            $discount   = ($p % 5 === 0) ? round($totalAmount * 0.02, 2) : 0.00;
            $tax        = round(($totalAmount - $discount) * 0.10, 2);
            $grandTotal = round($totalAmount - $discount + $tax, 2);
            $paidAmount = ($payStatus === 'paid') ? $grandTotal : (($payStatus === 'partial') ? round($grandTotal * 0.5, 2) : 0.00);
            $dueAmount  = round($grandTotal - $paidAmount, 2);

            $purchaseData[] = [
                'purchase_no'      => 'PO-2026-' . str_pad($p, 4, '0', STR_PAD_LEFT),
                'reference_no'     => 'REF-' . strtoupper(substr(md5($p), 0, 8)),
                'supplier_id'      => $supplierId,
                'purchase_date'    => $purchaseDate,
                'total_amount'     => round($totalAmount, 2),
                'discount'         => $discount,
                'tax'              => $tax,
                'grand_total'      => $grandTotal,
                'paid_amount'      => $paidAmount,
                'due_amount'       => $dueAmount,
                'payment_method_id'=> $payMethodId,
                'payment_status'   => $payStatus,
                'status'           => $status,
                'created_by'       => $createdBy,
                'description'      => "Purchase order #PO-2026-" . str_pad($p, 4, '0', STR_PAD_LEFT),
                'created_at'       => $now,
                'updated_at'       => $now,
            ];

            foreach ($itemsForPurchase as $item) {
                $purchaseItemData[] = $item;
            }
        }

        DB::table('purchases')->insert($purchaseData);
        DB::table('purchase_items')->insert($purchaseItemData);
    }
}
