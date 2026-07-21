<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('payment_methods')->insert([
            ['id' => 1, 'name' => 'Cash',          'code' => 'CASH',   'logo' => 'storage/payment/cash.png',         'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Bank Transfer',  'code' => 'BANK',   'logo' => 'storage/payment/bank.png',         'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'KHQR',           'code' => 'KHQR',   'logo' => 'storage/payment/khqr.png',         'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Stripe',         'code' => 'STRIPE', 'logo' => 'storage/payment/stripe.png',       'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'ABA Pay',        'code' => 'ABAPAY', 'logo' => 'storage/payment/abapay.png',       'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Wing Money',     'code' => 'WING',   'logo' => 'storage/payment/wing.png',         'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'name' => 'Credit Card',    'code' => 'CARD',   'logo' => 'storage/payment/credit-card.png',  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
