<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Positions (departments as parent, roles as children)
        DB::table('positions')->insert([
            // Parent departments (id 1-6)
            ['id' => 1,  'name' => 'Management',   'description' => 'Executive management team',              'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2,  'name' => 'Sales',         'description' => 'Sales and retail team',                  'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3,  'name' => 'IT',            'description' => 'Information Technology department',      'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4,  'name' => 'Warehouse',     'description' => 'Stock and warehouse management',         'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5,  'name' => 'Finance',       'description' => 'Finance and accounting department',      'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6,  'name' => 'HR',            'description' => 'Human Resources department',             'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // Child positions
            ['id' => 7,  'name' => 'General Manager','description' => 'Head of company operations',           'parent_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8,  'name' => 'Branch Manager', 'description' => 'Branch level operations',              'parent_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9,  'name' => 'Sales Executive', 'description' => 'Front-line sales representative',     'parent_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'name' => 'Cashier',        'description' => 'POS cashier and transaction handler',  'parent_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'name' => 'IT Engineer',    'description' => 'System and network engineer',          'parent_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'name' => 'IT Support',     'description' => 'Technical support specialist',         'parent_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 13, 'name' => 'Stock Controller','description' => 'Inventory and stock controller',      'parent_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'name' => 'Warehouse Staff', 'description' => 'Warehouse operations staff',          'parent_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'name' => 'Accountant',     'description' => 'Financial accounting officer',         'parent_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'name' => 'Finance Officer', 'description' => 'Finance operations officer',          'parent_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 17, 'name' => 'HR Manager',     'description' => 'Human resources manager',              'parent_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 18, 'name' => 'HR Officer',     'description' => 'HR recruitment and payroll officer',   'parent_id' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $firstNames = ['Sokha', 'Dara', 'Pisey', 'Bopha', 'Sothea', 'Chanthy', 'Rithy', 'Sreymom', 'Buntha', 'Vichet',
                        'Leakena', 'Vanna', 'Panha', 'Kolap', 'Piseth', 'Chamroeun', 'Sokmony', 'Tharith', 'Sopheap', 'Channary',
                        'Ratanak', 'Davan', 'Phalla', 'Nimol', 'Srey', 'Vathana', 'Sokun', 'Bunthan', 'Sreylin', 'Makara',
                        'Vireak', 'Seila', 'Kanika', 'Bora', 'Thida', 'Sambath', 'Rotha', 'Lyda', 'Seyha', 'Sarum',
                        'Sovann', 'Bunrith', 'Reaksa', 'Nary', 'Sothearith', 'Phearum', 'Sreyleap', 'Davy', 'Monika', 'Sovanna'];

        $lastNames  = ['Chan', 'Sok', 'Kim', 'Lim', 'Meas', 'Nget', 'Prak', 'Uy', 'Vong', 'Heng',
                        'Seng', 'Ouk', 'Men', 'Em', 'Ros', 'Keo', 'Long', 'Mol', 'Sar', 'Touch',
                        'Noun', 'Net', 'Phen', 'Sen', 'Hour', 'Kang', 'Mao', 'Sour', 'Rith', 'Pen',
                        'Tep', 'Khy', 'Oem', 'Som', 'Kea', 'Nov', 'Phorn', 'Hor', 'Ly', 'Ngov',
                        'Horn', 'Chhun', 'Hout', 'Pov', 'Deth', 'Yun', 'Van', 'Hin', 'Sreng', 'Kong'];

        $positionMap = [
            1  => [7,  2500.00, 'Full-time'],  // General Manager
            2  => [8,  1800.00, 'Full-time'],  // Branch Manager
            3  => [9,  700.00,  'Full-time'],  // Sales Executive
            4  => [10, 600.00,  'Full-time'],  // Cashier
            5  => [11, 900.00,  'Full-time'],  // IT Engineer
            6  => [12, 650.00,  'Full-time'],  // IT Support
            7  => [13, 700.00,  'Full-time'],  // Stock Controller
            8  => [14, 550.00,  'Full-time'],  // Warehouse Staff
            9  => [15, 850.00,  'Full-time'],  // Accountant
            10 => [16, 700.00,  'Full-time'],  // Finance Officer
            11 => [17, 950.00,  'Full-time'],  // HR Manager
            12 => [18, 680.00,  'Full-time'],  // HR Officer
        ];

        $genders = ['Male', 'Female', 'Other'];
        $bankNames = ['ABA Bank', 'ACLEDA Bank', 'Canadia Bank', 'Vattanac Bank', 'Wing'];
        $employmentStatuses = ['Full-time', 'Full-time', 'Full-time', 'Part-time', 'Probation'];

        $employees = [];
        for ($e = 1; $e <= 50; $e++) {
            $firstName  = $firstNames[($e - 1) % count($firstNames)];
            $lastName   = $lastNames[($e - 1) % count($lastNames)];
            $posEntry   = $positionMap[$e % 12 ?: 12];
            $positionId = $posEntry[0];
            $salary     = $posEntry[1];
            $empStatus  = $employmentStatuses[$e % count($employmentStatuses)];
            $gender     = $genders[$e % 3];
            $dobYear    = rand(1985, 2000);
            $dobMonth   = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $dobDay     = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
            $joinYear   = rand(2020, 2025);

            $employees[] = [
                'card_id'             => 'EMP-' . str_pad($e, 4, '0', STR_PAD_LEFT),
                'image'               => "storage/employees/emp-{$e}.jpg",
                'first_name'          => $firstName,
                'last_name'           => $lastName,
                'gender'              => $gender,
                'dob'                 => "{$dobYear}-{$dobMonth}-{$dobDay}",
                'email'               => strtolower($firstName . '.' . $lastName . $e) . '@cms-pos.kh',
                'tel'                 => '+855-' . rand(10, 99) . '-' . rand(100, 999) . '-' . str_pad($e, 3, '0', STR_PAD_LEFT),
                'position_id'         => $positionId,
                'salary'              => $salary,
                'employment_status'   => $empStatus,
                'payment_method_id'   => ($e % 3) + 1,
                'bank_account_number' => '00' . rand(10000, 99999) . str_pad($e, 4, '0', STR_PAD_LEFT),
                'bank_account_name'   => strtoupper($firstName . ' ' . $lastName),
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }

        DB::table('employees')->insert($employees);
    }
}
