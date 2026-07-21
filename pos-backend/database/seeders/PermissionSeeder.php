<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $permissions = [
            // Dashboard
            ['id' => 1, 'name' => 'dashboard.view', 'group' => 'Dashboard', 'is_menu_web' => true, 'web_route_key' => '/'],

            // POS
            ['id' => 2, 'name' => 'pos.access', 'group' => 'POS', 'is_menu_web' => true, 'web_route_key' => 'pos'],

            // Orders
            ['id' => 3, 'name' => 'orders.view', 'group' => 'Orders', 'is_menu_web' => true, 'web_route_key' => 'orders'],

            // Reports
            ['id' => 4, 'name' => 'reports.view', 'group' => 'Reports', 'is_menu_web' => true, 'web_route_key' => 'report/top_sales'],
            ['id' => 5, 'name' => 'order_report.view', 'group' => 'Reports', 'is_menu_web' => true, 'web_route_key' => 'order'],
            ['id' => 6, 'name' => 'purchase_report.view', 'group' => 'Reports', 'is_menu_web' => true, 'web_route_key' => 'report/purchase'],
            ['id' => 7, 'name' => 'expense_report.view', 'group' => 'Reports', 'is_menu_web' => true, 'web_route_key' => 'report/expense'],

            // Customers
            ['id' => 8, 'name' => 'customer.view', 'group' => 'Customers', 'is_menu_web' => true, 'web_route_key' => 'customer'],
            ['id' => 9, 'name' => 'customer.view_single', 'group' => 'Customers', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 10, 'name' => 'customer.create', 'group' => 'Customers', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 11, 'name' => 'customer.update', 'group' => 'Customers', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 12, 'name' => 'customer.delete', 'group' => 'Customers', 'is_menu_web' => false, 'web_route_key' => null],
            
            // Customer Types
            ['id' => 13, 'name' => 'customer-type.view', 'group' => 'Customers', 'is_menu_web' => true, 'web_route_key' => 'customer_type'],
            ['id' => 14, 'name' => 'customer-type.viewone', 'group' => 'Customers', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 15, 'name' => 'customer-type.create', 'group' => 'Customers', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 16, 'name' => 'customer-type.update', 'group' => 'Customers', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 17, 'name' => 'customer-type.delete', 'group' => 'Customers', 'is_menu_web' => false, 'web_route_key' => null],

            // Products
            ['id' => 18, 'name' => 'product.view', 'group' => 'Inventory', 'is_menu_web' => true, 'web_route_key' => 'product'],
            ['id' => 19, 'name' => 'product_card.view', 'group' => 'Inventory', 'is_menu_web' => true, 'web_route_key' => 'product_card'],
            ['id' => 20, 'name' => 'product.create', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 21, 'name' => 'product.update', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 22, 'name' => 'product.delete', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 23, 'name' => 'product.export', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],

            // Categories
            ['id' => 24, 'name' => 'category.view', 'group' => 'Inventory', 'is_menu_web' => true, 'web_route_key' => 'category'],
            ['id' => 25, 'name' => 'category.viewone', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 26, 'name' => 'category.create', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 27, 'name' => 'category.update', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 28, 'name' => 'category.delete', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],

            // Brands
            ['id' => 29, 'name' => 'brand.view', 'group' => 'Inventory', 'is_menu_web' => true, 'web_route_key' => 'brand'],
            ['id' => 30, 'name' => 'brand.viewone', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 31, 'name' => 'brand.create', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 32, 'name' => 'brand.update', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 33, 'name' => 'brand.delete', 'group' => 'Inventory', 'is_menu_web' => false, 'web_route_key' => null],

            // Purchase
            ['id' => 34, 'name' => 'purchase.view', 'group' => 'Purchases', 'is_menu_web' => true, 'web_route_key' => 'purchase'],
            ['id' => 35, 'name' => 'purchase.viewone', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 36, 'name' => 'purchase.create', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 37, 'name' => 'purchase.update', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 38, 'name' => 'purchase.delete', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 39, 'name' => 'purchase.export', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],

            // Suppliers
            ['id' => 40, 'name' => 'supplier.view', 'group' => 'Purchases', 'is_menu_web' => true, 'web_route_key' => 'supplier'],
            ['id' => 41, 'name' => 'supplier.view_single', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 42, 'name' => 'supplier.create', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 43, 'name' => 'supplier.update', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 44, 'name' => 'supplier.delete', 'group' => 'Purchases', 'is_menu_web' => false, 'web_route_key' => null],

            // Expenses
            ['id' => 45, 'name' => 'expense.view', 'group' => 'Expenses', 'is_menu_web' => true, 'web_route_key' => 'expense'],
            ['id' => 46, 'name' => 'expense.viewone', 'group' => 'Expenses', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 47, 'name' => 'expense.create', 'group' => 'Expenses', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 48, 'name' => 'expense.update', 'group' => 'Expenses', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 49, 'name' => 'expense.delete', 'group' => 'Expenses', 'is_menu_web' => false, 'web_route_key' => null],

            // Expense Types
            ['id' => 50, 'name' => 'expense-type.view', 'group' => 'Expenses', 'is_menu_web' => true, 'web_route_key' => 'expense_type'],
            ['id' => 51, 'name' => 'expense-type.viewone', 'group' => 'Expenses', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 52, 'name' => 'expense-type.create', 'group' => 'Expenses', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 53, 'name' => 'expense-type.update', 'group' => 'Expenses', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 54, 'name' => 'expense-type.delete', 'group' => 'Expenses', 'is_menu_web' => false, 'web_route_key' => null],

            // Employees
            ['id' => 55, 'name' => 'employee.view', 'group' => 'Employees', 'is_menu_web' => true, 'web_route_key' => 'employee'],
            ['id' => 56, 'name' => 'employee.viewone', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 57, 'name' => 'employee.create', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 58, 'name' => 'employee.update', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 59, 'name' => 'employee.delete', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],

            // Employee Payrolls
            ['id' => 60, 'name' => 'employee-payroll.view', 'group' => 'Employees', 'is_menu_web' => true, 'web_route_key' => 'employee/payrolls'],
            ['id' => 61, 'name' => 'employee-payroll.viewone', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 62, 'name' => 'employee-payroll.create', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 63, 'name' => 'employee-payroll.update', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 64, 'name' => 'employee-payroll.delete', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 65, 'name' => 'employee-payroll.approve', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 66, 'name' => 'employee-payroll.export', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],

            // Payrolls
            ['id' => 67, 'name' => 'payroll.view', 'group' => 'Employees', 'is_menu_web' => true, 'web_route_key' => 'payroll'],
            ['id' => 68, 'name' => 'payroll.viewone', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 69, 'name' => 'payroll.create', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 70, 'name' => 'payroll.update', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 71, 'name' => 'payroll.delete', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],

            // Positions
            ['id' => 72, 'name' => 'position.view', 'group' => 'Employees', 'is_menu_web' => true, 'web_route_key' => 'position'],
            ['id' => 73, 'name' => 'position.viewone', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 74, 'name' => 'position.create', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 75, 'name' => 'position.update', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 76, 'name' => 'position.delete', 'group' => 'Employees', 'is_menu_web' => false, 'web_route_key' => null],

            // Users
            ['id' => 77, 'name' => 'users.view', 'group' => 'Users', 'is_menu_web' => true, 'web_route_key' => 'list'],
            ['id' => 78, 'name' => 'users.create', 'group' => 'Users', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 79, 'name' => 'users.update', 'group' => 'Users', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 80, 'name' => 'users.delete', 'group' => 'Users', 'is_menu_web' => false, 'web_route_key' => null],

            // Roles
            ['id' => 81, 'name' => 'role.view', 'group' => 'Users', 'is_menu_web' => true, 'web_route_key' => 'role'],
            ['id' => 82, 'name' => 'role.view_single', 'group' => 'Users', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 83, 'name' => 'role.create', 'group' => 'Users', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 84, 'name' => 'role.edit', 'group' => 'Users', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 85, 'name' => 'role.delete', 'group' => 'Users', 'is_menu_web' => false, 'web_route_key' => null],

            // Permissions
            ['id' => 86, 'name' => 'permission.view', 'group' => 'Users', 'is_menu_web' => true, 'web_route_key' => 'permission'],

            // Languages
            ['id' => 87, 'name' => 'lang.view', 'group' => 'Settings', 'is_menu_web' => true, 'web_route_key' => 'lang'],

            // Translations
            ['id' => 88, 'name' => 'translations.view', 'group' => 'Settings', 'is_menu_web' => true, 'web_route_key' => '/translations'],

            // Provinces
            ['id' => 89, 'name' => 'province.view', 'group' => 'Settings', 'is_menu_web' => true, 'web_route_key' => 'province'],
            ['id' => 90, 'name' => 'province.create', 'group' => 'Settings', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 91, 'name' => 'province.update', 'group' => 'Settings', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 92, 'name' => 'province.delete', 'group' => 'Settings', 'is_menu_web' => false, 'web_route_key' => null],

            // Currencies
            ['id' => 93, 'name' => 'currency.view', 'group' => 'Settings', 'is_menu_web' => true, 'web_route_key' => 'currency'],

            // Payment Methods
            ['id' => 94, 'name' => 'paymentmethod.view', 'group' => 'Settings', 'is_menu_web' => true, 'web_route_key' => 'payment_method'],
            ['id' => 95, 'name' => 'paymentmethod.viewone', 'group' => 'Settings', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 96, 'name' => 'paymentmethod.create', 'group' => 'Settings', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 97, 'name' => 'paymentmethod.update', 'group' => 'Settings', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 98, 'name' => 'paymentmethod.delete', 'group' => 'Settings', 'is_menu_web' => false, 'web_route_key' => null],

            // About (CMS pages)
            ['id' => 99, 'name' => 'about.view', 'group' => 'CMS', 'is_menu_web' => true, 'web_route_key' => 'about'],
            ['id' => 100, 'name' => 'about.viewone', 'group' => 'CMS', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 101, 'name' => 'about.create', 'group' => 'CMS', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 102, 'name' => 'about.update', 'group' => 'CMS', 'is_menu_web' => false, 'web_route_key' => null],
            ['id' => 103, 'name' => 'about.delete', 'group' => 'CMS', 'is_menu_web' => false, 'web_route_key' => null],
        ];

        $insertData = array_map(fn($p) => array_merge($p, [
            'created_at' => $now,
            'updated_at' => $now,
        ]), $permissions);

        DB::table('permissions')->insert($insertData);
    }
}
