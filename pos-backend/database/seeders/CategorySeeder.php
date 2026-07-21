<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Parent categories
        DB::table('categories')->insert([
            ['id' => 1,  'name' => 'Smartphone',     'description' => 'All mobile phones and smartphones',             'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2,  'name' => 'Laptop',          'description' => 'Laptops and notebooks for personal & business', 'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3,  'name' => 'Tablet',          'description' => 'Tablets and iPad devices',                      'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4,  'name' => 'Accessories',     'description' => 'Phone and laptop accessories',                  'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5,  'name' => 'Computer Parts',  'description' => 'Desktop and computer components',               'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6,  'name' => 'Network Device',  'description' => 'Routers, switches, and networking equipment',  'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7,  'name' => 'Desktop',         'description' => 'Desktop computers and all-in-one PCs',         'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8,  'name' => 'Printer',         'description' => 'Printers, scanners, and printing supplies',    'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9,  'name' => 'Monitor',         'description' => 'Computer monitors and displays',                'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'name' => 'Gaming',          'description' => 'Gaming peripherals and consoles',               'status' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // Sub-categories
            ['id' => 11, 'name' => 'iPhone',          'description' => 'Apple iPhone series',                           'status' => true, 'parent_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'name' => 'Android Phone',   'description' => 'Android smartphones',                           'status' => true, 'parent_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 13, 'name' => 'MacBook',         'description' => 'Apple MacBook series',                          'status' => true, 'parent_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'name' => 'Windows Laptop',  'description' => 'Windows-based laptops',                         'status' => true, 'parent_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'name' => 'Cases & Covers',  'description' => 'Phone cases, covers and protection',            'status' => true, 'parent_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'name' => 'Chargers & Cable','description' => 'Chargers, cables and adapters',                 'status' => true, 'parent_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 17, 'name' => 'Earphone',        'description' => 'Earphones, headphones and earbuds',             'status' => true, 'parent_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 18, 'name' => 'RAM & Storage',   'description' => 'RAM, SSD, HDD and storage',                    'status' => true, 'parent_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 19, 'name' => 'Power Bank',      'description' => 'Portable power banks and chargers',             'status' => true, 'parent_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'name' => 'Smart Watch',     'description' => 'Smartwatches and fitness trackers',             'status' => true, 'parent_id' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
