<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password123');

        $users = [
            // id=1: Super Admin
            ['id' => 1, 'name' => 'superadmin',      'email' => 'superadmin@cms-pos.com',  'password' => $password, 'email_verified_at' => $now],
            // id=2: Admin
            ['id' => 2, 'name' => 'admin',           'email' => 'admin@cms-pos.com',       'password' => $password, 'email_verified_at' => $now],
            // id=3: Manager
            ['id' => 3, 'name' => 'manager',         'email' => 'manager@cms-pos.com',     'password' => $password, 'email_verified_at' => $now],
            // id=4: Cashier
            ['id' => 4, 'name' => 'cashier',         'email' => 'cashier@cms-pos.com',     'password' => $password, 'email_verified_at' => $now],
            // id=5: Accountant
            ['id' => 5, 'name' => 'accountant',      'email' => 'accountant@cms-pos.com',  'password' => $password, 'email_verified_at' => $now],
            // id=6: Warehouse Staff
            ['id' => 6, 'name' => 'warehouse',       'email' => 'warehouse@cms-pos.com',   'password' => $password, 'email_verified_at' => $now],
            // id=7: HR Staff
            ['id' => 7, 'name' => 'hr',              'email' => 'hr@cms-pos.com',          'password' => $password, 'email_verified_at' => $now],
            // id=8: Content Manager
            ['id' => 8, 'name' => 'content',          'email' => 'content@cms-pos.com',     'password' => $password, 'email_verified_at' => $now],
        ];

        $insertData = array_map(fn($u) => array_merge($u, [
            'remember_token' => null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]), $users);

        DB::table('users')->insert($insertData);

        // Assign roles in user_roles pivot
        // Role IDs: 1=super-admin, 2=admin, 3=manager, 4=cashier, 5=accountant, 6=warehouse, 7=hr, 8=content
        DB::table('user_roles')->insert([
            ['user_id' => 1, 'role_id' => 1],
            ['user_id' => 2, 'role_id' => 2],
            ['user_id' => 3, 'role_id' => 3],
            ['user_id' => 4, 'role_id' => 4],
            ['user_id' => 5, 'role_id' => 5],
            ['user_id' => 6, 'role_id' => 6],
            ['user_id' => 7, 'role_id' => 7],
            ['user_id' => 8, 'role_id' => 8],
        ]);

        // User profiles
        DB::table('profiles')->insert([
            ['user_id' => 1, 'phone' => '+855-12-000-001', 'address' => 'Phnom Penh, Cambodia', 'image' => 'storage/profiles/superadmin.jpg',  'type' => 'SYSTEM GENERAL'],
            ['user_id' => 2, 'phone' => '+855-12-000-002', 'address' => 'Phnom Penh, Cambodia', 'image' => 'storage/profiles/admin.jpg',       'type' => 'SYSTEM GENERAL'],
            ['user_id' => 3, 'phone' => '+855-12-000-003', 'address' => 'Phnom Penh, Cambodia', 'image' => 'storage/profiles/manager.jpg',     'type' => 'STAFF'],
            ['user_id' => 4, 'phone' => '+855-12-000-004', 'address' => 'Kandal, Cambodia',     'image' => 'storage/profiles/cashier.jpg',     'type' => 'STAFF'],
            ['user_id' => 5, 'phone' => '+855-12-000-005', 'address' => 'Phnom Penh, Cambodia', 'image' => 'storage/profiles/accountant.jpg',  'type' => 'STAFF'],
            ['user_id' => 6, 'phone' => '+855-12-000-006', 'address' => 'Phnom Penh, Cambodia', 'image' => 'storage/profiles/warehouse.jpg',   'type' => 'STAFF'],
            ['user_id' => 7, 'phone' => '+855-12-000-007', 'address' => 'Phnom Penh, Cambodia', 'image' => 'storage/profiles/hr.jpg',          'type' => 'STAFF'],
            ['user_id' => 8, 'phone' => '+855-12-000-008', 'address' => 'Siem Reap, Cambodia',  'image' => 'storage/profiles/content.jpg',     'type' => 'STAFF'],
        ]);
    }
}
