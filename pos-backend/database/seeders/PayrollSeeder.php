<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Create 6 monthly payrolls (last 6 months)
        $payrolls = [
            ['id' => 1, 'title' => 'January 2026 Payroll',  'payment_date' => '2026-01-31', 'status' => 'Paid',    'created_by' => 6, 'approved_by' => 'Dara Vann (Admin)',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'title' => 'February 2026 Payroll', 'payment_date' => '2026-02-28', 'status' => 'Paid',    'created_by' => 6, 'approved_by' => 'Dara Vann (Admin)',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'title' => 'March 2026 Payroll',    'payment_date' => '2026-03-31', 'status' => 'Paid',    'created_by' => 6, 'approved_by' => 'Pisey Chan (Manager)', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'title' => 'April 2026 Payroll',    'payment_date' => '2026-04-30', 'status' => 'Paid',    'created_by' => 6, 'approved_by' => 'Dara Vann (Admin)',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'title' => 'May 2026 Payroll',      'payment_date' => '2026-05-31', 'status' => 'Approved','created_by' => 6, 'approved_by' => 'Dara Vann (Admin)',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'title' => 'June 2026 Payroll',     'payment_date' => '2026-06-30', 'status' => 'Pending', 'created_by' => 6, 'approved_by' => null,                   'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'title' => 'July 2026 Payroll',     'payment_date' => '2026-07-31', 'status' => 'Draft',   'created_by' => 6, 'approved_by' => null,                   'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('payrolls')->insert($payrolls);

        // Employee salaries mapping (position-based)
        $employeeSalaries = [];
        for ($e = 1; $e <= 50; $e++) {
            $posGroup = $e % 12 ?: 12;
            $baseSalaryMap = [
                1  => 2500.00, 2  => 1800.00, 3  => 700.00,  4  => 600.00,
                5  => 900.00,  6  => 650.00,  7  => 700.00,  8  => 550.00,
                9  => 850.00,  10 => 700.00,  11 => 950.00,  12 => 680.00,
            ];
            $employeeSalaries[$e] = $baseSalaryMap[$posGroup];
        }

        // Generate employee_payrolls for each payroll period (7 payrolls x 50 employees = 350 records)
        $employeePayrolls = [];
        foreach ($payrolls as $payroll) {
            for ($e = 1; $e <= 50; $e++) {
                $baseSalary    = $employeeSalaries[$e];
                $otAmount      = ($e % 4 === 0) ? round($baseSalary * 0.1, 2) : 0.00;
                $allowance     = ($e % 3 === 0) ? 50.00 : 25.00; // transportation/meal allowance
                $deduction     = ($e % 5 === 0) ? 30.00 : 0.00;  // late deduction
                $netSalary     = round($baseSalary + $otAmount + $allowance - $deduction, 2);

                $employeePayrolls[] = [
                    'payroll_id'       => $payroll['id'],
                    'employee_id'      => $e,
                    'base_salary'      => $baseSalary,
                    'ot_amount'        => $otAmount,
                    'allowance'        => $allowance,
                    'deduction_amount' => $deduction,
                    'net_salary'       => $netSalary,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
        }

        DB::table('employee_payrolls')->insert($employeePayrolls);
    }
}
