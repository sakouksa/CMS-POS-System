<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Expense Types
        DB::table('expense_types')->insert([
            ['id' => 1, 'name' => 'Rent',         'description' => 'Office and store rental expenses',          'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Electricity',   'description' => 'Electricity and utility bills',             'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Internet',      'description' => 'Internet and connectivity expenses',        'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Salary',        'description' => 'Employee salary and wages',                 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Marketing',     'description' => 'Advertising, promotions and marketing',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Transport',     'description' => 'Delivery, fuel and vehicle expenses',      'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'name' => 'Maintenance',   'description' => 'Equipment and premises maintenance',       'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'name' => 'Office Supply',  'description' => 'Stationery, paper and office items',      'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'name' => 'Insurance',     'description' => 'Business and employee insurance',          'created_at' => $now, 'updated_at' => $now],
            ['id' => 10,'name' => 'Miscellaneous', 'description' => 'Other business expenses',                  'created_at' => $now, 'updated_at' => $now],
        ]);

        $expenseNames = [
            1  => ['Phnom Penh HQ Monthly Rent',      'Siem Reap Branch Rent Q2',     'Battambang Store Rent',         'Warehouse Rental Phnom Penh'],
            2  => ['HQ Electricity Bill June',        'Branch Electricity May',         'Warehouse Power Bill',          'Server Room AC Bill'],
            3  => ['Fiber Internet HQ Monthly',       'Branch WiFi Subscription',       '4G SIM for Staff',              'VPN Service Annual'],
            4  => ['Monthly Salary June 2026',        'Bonus Q2 Staff',                 'Overtime Payment May',          'Part-time Staff Wages'],
            5  => ['Facebook Ads Campaign',           'Google Ads July',               'Flyer Printing',                 'Promotional Banner'],
            6  => ['Fuel Reimbursement June',         'Delivery Van Service',           'Grab Delivery Fees',            'Motorcycle Maintenance'],
            7  => ['AC Service HQ',                  'Computer Repair Office',         'CCTV System Upgrade',           'Printer Ink & Maintenance'],
            8  => ['A4 Paper & Toner',               'Stationery Monthly',             'USB Drives Bulk',               'Office Chairs'],
            9  => ['Employee Health Insurance Q2',   'Property Insurance Annual',      'Vehicle Insurance Renewal',     'Fire Insurance Policy'],
            10 => ['Miscellaneous June',              'Emergency Expenses',            'Staff Team Building',           'Meeting Room Rental'],
        ];

        $expenses = [];
        $expenseId = 1;
        for ($t = 1; $t <= 10; $t++) {
            foreach ($expenseNames[$t] as $idx => $name) {
                $baseAmounts = [
                    1 => 2500.00, 2 => 350.00, 3 => 120.00, 4 => 15000.00,
                    5 => 800.00,  6 => 250.00, 7 => 450.00, 8 => 180.00,
                    9 => 1200.00, 10 => 300.00
                ];
                $amount = $baseAmounts[$t] + ($idx * 50);
                $date   = date('Y-m-d', strtotime("-{$expenseId} weeks", strtotime('2026-07-15')));
                $statuses = ['paid', 'paid', 'pending', 'cancel'];

                $expenses[] = [
                    'name'           => $name,
                    'description'    => "Business expense: {$name}",
                    'expense_type_id'=> $t,
                    'amount'         => $amount,
                    'expense_status' => $statuses[$idx % count($statuses)],
                    'expense_date'   => $date,
                    'create_by'      => ($expenseId % 2 === 0) ? 6 : 2, // accountant or admin
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
                $expenseId++;
            }
        }

        // Add more to reach 100 total
        $additionalExpenses = [
            ['January 2026 HQ Rent',    'Monthly HQ office rent payment',            1, 2500.00, 'paid',    '2026-01-05'],
            ['February 2026 HQ Rent',   'Monthly HQ office rent payment',            1, 2500.00, 'paid',    '2026-02-05'],
            ['March 2026 HQ Rent',      'Monthly HQ office rent payment',            1, 2500.00, 'paid',    '2026-03-05'],
            ['April 2026 HQ Rent',      'Monthly HQ office rent payment',            1, 2500.00, 'paid',    '2026-04-05'],
            ['May 2026 HQ Rent',        'Monthly HQ office rent payment',            1, 2500.00, 'paid',    '2026-05-05'],
            ['Google Analytics Premium', 'Google Analytics 360 annual subscription', 5, 1500.00, 'paid',    '2026-01-15'],
            ['Meta Business Suite Pro', 'Meta social media management tool',         5, 250.00,  'paid',    '2026-02-10'],
            ['Staff Medical Insurance', 'AIA Cambodia staff insurance plan',         9, 3500.00, 'paid',    '2026-01-20'],
            ['Server Hosting Annual',   'AWS server hosting 2026',                   3, 1800.00, 'paid',    '2026-01-01'],
            ['Office Renovation',       'Phnom Penh office interior renovation',     7, 5000.00, 'paid',    '2026-02-01'],
            ['January Electricity',     'Phnom Penh HQ power bill January',          2, 380.00,  'paid',    '2026-01-31'],
            ['February Electricity',    'Phnom Penh HQ power bill February',         2, 420.00,  'paid',    '2026-02-28'],
            ['March Electricity',       'Phnom Penh HQ power bill March',            2, 395.00,  'paid',    '2026-03-31'],
            ['April Electricity',       'Phnom Penh HQ power bill April',            2, 410.00,  'paid',    '2026-04-30'],
            ['May Electricity',         'Phnom Penh HQ power bill May',              2, 405.00,  'paid',    '2026-05-31'],
            ['Lunar New Year Bonus',    'Staff bonus for Khmer New Year 2026',       4, 5000.00, 'paid',    '2026-04-10'],
            ['Year End Bonus Q1',       'Staff performance bonus Q1 2026',           4, 3000.00, 'paid',    '2026-03-31'],
            ['Repair CCTV System',      'CCTV repair and new camera installation',   7, 650.00,  'paid',    '2026-03-15'],
            ['Annual Domain Renewal',   'Company website domain renewal',            3, 35.00,   'paid',    '2026-01-10'],
            ['Social Media Manager',    'Freelance social media content creator',    5, 400.00,  'paid',    '2026-05-15'],
            ['Laptop for New Staff',    'Dell Latitude 5540 for new accountant',     8, 999.00,  'paid',    '2026-04-01'],
            ['Staff Team Building Q1',  'Q1 team building event at resort',          10, 1200.00, 'paid',   '2026-03-20'],
            ['Bank Transfer Fees',      'Monthly bank wire transfer processing fees',10, 50.00,  'paid',    '2026-06-30'],
            ['Shipping & Courier',      'DHL and FedEx courier charges Q2',          6, 350.00,  'pending', '2026-06-25'],
            ['Office Plants & Decor',   'Office interior plants and decoration',     8, 180.00,  'paid',    '2026-05-01'],
            ['Legal & Compliance Fee',  'Business license renewal and legal advisory',10, 800.00, 'paid',   '2026-01-15'],
            ['Security Guard Service',  'Monthly security guard service fee',        7, 400.00,  'paid',    '2026-06-30'],
            ['Water & Cleaning',        'Monthly water bill and cleaning service',   2, 120.00,  'paid',    '2026-06-30'],
            ['Training Workshop IT',    'Cybersecurity training for IT team',        10, 500.00, 'pending', '2026-07-10'],
            ['Canteen & Pantry Supply', 'Coffee, water dispenser and snacks',        8, 150.00,  'paid',    '2026-07-01'],
            ['POS System License',      'POS software annual license renewal',       10, 1200.00,'paid',    '2026-01-01'],
            ['Accounting Software',     'QuickBooks Online annual plan',             10, 360.00, 'paid',    '2026-01-15'],
            ['Network Equipment',       'New router and switch for warehouse',       7, 350.00,  'paid',    '2026-02-20'],
            ['HR Management Software',  'HR module subscription 2026',              10, 480.00, 'paid',    '2026-01-01'],
            ['Printed Catalog',         'Product catalog printing 500 copies',       5, 300.00,  'paid',    '2026-06-01'],
            ['Trade Show Booth',        'CamTech 2026 exhibition booth rental',      5, 2000.00, 'paid',    '2026-05-20'],
            ['Customer Gift Campaign',  'VIP customer gift boxes Q2',               5, 600.00,  'paid',    '2026-06-15'],
            ['Photography Service',     'Product photography for website update',    5, 450.00,  'paid',    '2026-04-20'],
            ['Corporate Video',         'Company brand video production',            5, 1500.00, 'paid',    '2026-03-01'],
            ['Staff Uniform',           'Staff uniform for all branches',            8, 800.00,  'paid',    '2026-01-20'],
            ['Vehicle Fuel June',       'Company delivery van fuel card June',       6, 280.00,  'paid',    '2026-06-30'],
            ['Vehicle Fuel May',        'Company delivery van fuel card May',        6, 265.00,  'paid',    '2026-05-31'],
            ['Fire Extinguisher',       'Annual fire safety equipment check',        9, 250.00,  'paid',    '2026-02-14'],
            ['Phone Bill Corporate',    'Company mobile plans all branches',         3, 200.00,  'paid',    '2026-06-30'],
            ['Waste Disposal Service',  'Monthly waste disposal contractor',         7, 80.00,   'paid',    '2026-06-30'],
            ['Employee Lunch Subsidy',  'Monthly staff meal allowance program',      4, 1500.00, 'paid',    '2026-06-30'],
            ['Ink & Toner Cartridges',  'Monthly printer supplies reorder',          8, 120.00,  'paid',    '2026-06-15'],
            ['Emergency Repairs June',  'Emergency AC compressor repair HQ',         7, 320.00,  'paid',    '2026-06-22'],
            ['Visa & Work Permit',      'Foreign expert work permit renewal',        10, 650.00, 'paid',    '2026-03-05'],
            ['Board Meeting Expenses',  'Quarterly board meeting catering & venue',  10, 550.00, 'paid',    '2026-04-25'],
            ['New Year Event',          'Khmer New Year celebration for all staff',  10, 1000.00,'paid',    '2026-04-14'],
            ['Accounting Audit Fee',    'Annual audit by external auditor',          10, 1500.00,'paid',    '2026-02-28'],
            ['IT Security Audit',       'Penetration testing and security audit',    3, 800.00,  'paid',    '2026-03-10'],
            ['Cloud Backup Service',    'Veeam cloud backup subscription annual',    3, 600.00,  'paid',    '2026-01-15'],
            ['Delivery Motorcycle',     'New delivery motorcycle purchase',          6, 1200.00, 'paid',    '2026-02-15'],
            ['Antivirus License',       'Kaspersky Enterprise antivirus annual',     3, 350.00,  'paid',    '2026-01-05'],
            ['Office Coffee Machine',   'Nespresso machine for office pantry',       8, 250.00,  'paid',    '2026-01-25'],
            ['Packaging Supplies',      'Boxes, bubble wrap and tape bulk order',    6, 180.00,  'paid',    '2026-06-01'],
            ['Disability Insurance',    'Employee disability insurance plan',         9, 800.00,  'paid',    '2026-01-30'],
            ['ERP Consulting Fee',      'System integration consulting fee',         10, 2000.00,'pending', '2026-07-15'],
        ];

        foreach ($additionalExpenses as $ae) {
            $expenses[] = [
                'name'           => $ae[0],
                'description'    => $ae[1],
                'expense_type_id'=> $ae[2],
                'amount'         => $ae[3],
                'expense_status' => $ae[4],
                'expense_date'   => $ae[5],
                'create_by'      => 6,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        DB::table('expenses')->insert($expenses);
    }
}
