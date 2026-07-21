<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // First insert customer types (needed as FK)
        DB::table('customer_types')->insert([
            ['id' => 1, 'name' => 'Retail',    'description' => 'Standard retail customer, regular pricing',       'discount_value' => 0.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'VIP',       'description' => 'VIP member with exclusive discounts and priority','discount_value' => 5.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Wholesale', 'description' => 'Bulk buyer with wholesale pricing',               'discount_value' => 10.00,'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Corporate', 'description' => 'Corporate account with payment terms',            'discount_value' => 7.50, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 100 Customers
        $firstNames = ['Sokha', 'Dara', 'Pisey', 'Bopha', 'Sothea', 'Chanthy', 'Rithy', 'Sreymom', 'Buntha', 'Vichet',
                        'Leakena', 'Vanna', 'Panha', 'Kolap', 'Piseth', 'Chamroeun', 'Sokmony', 'Tharith', 'Sopheap', 'Channary',
                        'Lina', 'Rathana', 'Kiry', 'Pich', 'Davo', 'Sona', 'Sophea', 'Sorin', 'Chenda', 'Malis',
                        'Ratanak', 'Davan', 'Chantrea', 'Phalla', 'Nimol', 'Srey', 'Kunthy', 'Vathana', 'Sokun', 'Davuth',
                        'Bunthan', 'Sreylin', 'Chanlina', 'Makara', 'Vireak', 'Seila', 'Sophy', 'Kanika', 'Bora', 'Vicheka',
                        'Thida', 'Chhunly', 'Sambath', 'Rotha', 'Lyda', 'Seyha', 'Sarum', 'Sovann', 'Bunrith', 'Chhorvy',
                        'Reaksa', 'Nary', 'Sothearith', 'Phearum', 'Virak', 'Sreyleap', 'Davy', 'Monika', 'Sovanna', 'Pov',
                        'Dina', 'Ratana', 'Chom', 'Rada', 'Kagna', 'Srun', 'Sophanna', 'Chandarith', 'Ponleak', 'Kimheng',
                        'Menglong', 'Vithida', 'Sodalen', 'Sreynich', 'Kosal', 'Dary', 'Cham', 'Cheat', 'Kesor', 'Picha',
                        'Lyno', 'Raksmey', 'Channara', 'Voeun', 'Bunna', 'Thearith', 'Sreynika', 'Sinat', 'Phirum', 'Kakada'];

        $lastNames  = ['Chan', 'Sok', 'Kim', 'Lim', 'Meas', 'Nget', 'Prak', 'Uy', 'Vong', 'Heng',
                        'Seng', 'Ouk', 'Men', 'Em', 'Ros', 'Keo', 'Long', 'Mol', 'Sar', 'Touch',
                        'Noun', 'Net', 'Phen', 'Sen', 'Hour', 'Kang', 'Mao', 'Sour', 'Rith', 'Pen',
                        'Tep', 'Khy', 'Oem', 'Som', 'Kea', 'Nov', 'Phorn', 'Hor', 'Ly', 'Ngov',
                        'Horn', 'Chhun', 'Hout', 'Pov', 'Deth', 'Yun', 'Van', 'Hin', 'Sreng', 'Kong',
                        'Khon', 'Sat', 'Lorn', 'Rath', 'Thun', 'Dich', 'Chiv', 'Sam', 'Pich', 'Vuthy'];

        $addresses = [
            'No. 12, Street 51, BKK1, Phnom Penh',
            'No. 34, Street 63, Toul Tom Pong, Phnom Penh',
            'No. 5, Street 118, Daun Penh, Phnom Penh',
            'No. 89, Monivong Blvd, Chamkarmorn, Phnom Penh',
            'No. 22, Street 271, Toul Kork, Phnom Penh',
            'No. 67, Street 360, Khan Russey Keo, Phnom Penh',
            'No. 15, Sivatha Blvd, Siem Reap',
            'No. 44, Street 6, Battambang City',
            'No. 11, National Road 4, Sihanoukville',
            'No. 8, Street 2, Kampot City',
            'Village 4, Commune Prek Eng, Kandal Province',
            'No. 29, Street 109, Mean Chey, Phnom Penh',
            'No. 78, Preah Sihanouk Blvd, Chamkarmorn, Phnom Penh',
            'No. 3, Street 400, Toul Kork, Phnom Penh',
            'No. 55, Charles de Gaulle Blvd, Phnom Penh',
        ];

        $genders    = ['Male', 'Female', 'Other'];
        $types      = [1, 1, 1, 1, 2, 2, 3, 4]; // mostly retail

        $customers  = [];
        for ($i = 1; $i <= 100; $i++) {
            $firstName  = $firstNames[($i - 1) % count($firstNames)];
            $lastName   = $lastNames[($i - 1) % count($lastNames)];
            $gender     = $genders[($i % 3 === 0) ? 2 : ($i % 2)];
            $typeId     = $types[$i % count($types)];
            $dobYear    = rand(1975, 2000);
            $dobMonth   = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $dobDay     = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

            $customers[] = [
                'customer_type_id' => $typeId,
                'first_name'       => $firstName,
                'last_name'        => $lastName,
                'gender'           => $gender,
                'dob'              => "{$dobYear}-{$dobMonth}-{$dobDay}",
                'tel'              => '+855-' . rand(10, 99) . '-' . rand(100, 999) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'address'          => $addresses[$i % count($addresses)],
                'created_by'       => ($i % 3 === 0) ? 4 : 5, // cashier 1 or 2
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        DB::table('customers')->insert($customers);
    }
}
