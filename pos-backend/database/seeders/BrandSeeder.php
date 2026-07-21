<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('brands')->insert([
            ['id' => 1,  'name' => 'Apple',   'code' => 'APPLE',   'from_country' => 'United States', 'image' => 'storage/brands/apple.png',   'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2,  'name' => 'Samsung', 'code' => 'SAMSUNG', 'from_country' => 'South Korea',   'image' => 'storage/brands/samsung.png', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3,  'name' => 'Xiaomi',  'code' => 'XIAOMI',  'from_country' => 'China',         'image' => 'storage/brands/xiaomi.png',  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4,  'name' => 'Oppo',    'code' => 'OPPO',    'from_country' => 'China',         'image' => 'storage/brands/oppo.png',    'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5,  'name' => 'Vivo',    'code' => 'VIVO',    'from_country' => 'China',         'image' => 'storage/brands/vivo.png',    'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6,  'name' => 'Dell',    'code' => 'DELL',    'from_country' => 'United States', 'image' => 'storage/brands/dell.png',    'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7,  'name' => 'HP',      'code' => 'HP',      'from_country' => 'United States', 'image' => 'storage/brands/hp.png',      'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8,  'name' => 'Lenovo',  'code' => 'LENOVO',  'from_country' => 'China',         'image' => 'storage/brands/lenovo.png',  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9,  'name' => 'Asus',    'code' => 'ASUS',    'from_country' => 'Taiwan',        'image' => 'storage/brands/asus.png',    'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'name' => 'Huawei',  'code' => 'HUAWEI',  'from_country' => 'China',         'image' => 'storage/brands/huawei.png',  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'name' => 'Sony',    'code' => 'SONY',    'from_country' => 'Japan',         'image' => 'storage/brands/sony.png',    'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'name' => 'Realme',  'code' => 'REALME',  'from_country' => 'China',         'image' => 'storage/brands/realme.png',  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 13, 'name' => 'TP-Link', 'code' => 'TPLINK',  'from_country' => 'China',         'image' => 'storage/brands/tplink.png',  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'name' => 'Acer',    'code' => 'ACER',    'from_country' => 'Taiwan',        'image' => 'storage/brands/acer.png',    'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'name' => 'MSI',     'code' => 'MSI',     'from_country' => 'Taiwan',        'image' => 'storage/brands/msi.png',     'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
