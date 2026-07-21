<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->insert([
            [
                'id'          => 1,
                'name'        => 'Super Admin',
                'code'        => 'super-admin',
                'description' => 'Full system access bypasses all checks',
                'status'      => true,
                'test'        => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 2,
                'name'        => 'Admin',
                'code'        => 'admin',
                'description' => 'System administrator manages users, roles, settings, and CMS',
                'status'      => true,
                'test'        => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 3,
                'name'        => 'Manager',
                'code'        => 'manager',
                'description' => 'Business manager handles catalog, sales, purchases, and finance',
                'status'      => true,
                'test'        => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 4,
                'name'        => 'Cashier',
                'code'        => 'cashier',
                'description' => 'Point of sale cashier creates orders and prints receipts',
                'status'      => true,
                'test'        => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 5,
                'name'        => 'Accountant',
                'code'        => 'accountant',
                'description' => 'Finance controller manages expenses, payments, and reviews financial reports',
                'status'      => true,
                'test'        => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 6,
                'name'        => 'Warehouse Staff',
                'code'        => 'warehouse',
                'description' => 'Inventory controller handles products, categories, and purchases',
                'status'      => true,
                'test'        => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 7,
                'name'        => 'HR Staff',
                'code'        => 'hr',
                'description' => 'Human resources manages employees, positions, and payrolls',
                'status'      => true,
                'test'        => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 8,
                'name'        => 'Content Manager',
                'code'        => 'content',
                'description' => 'CMS manager updates company profiles, languages, and translations',
                'status'      => true,
                'test'        => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }
}
