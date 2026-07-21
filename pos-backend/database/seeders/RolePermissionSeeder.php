<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Role IDs:
        // 1 = Super Admin
        // 2 = Admin
        // 3 = Manager
        // 4 = Cashier
        // 5 = Accountant
        // 6 = Warehouse Staff
        // 7 = HR Staff
        // 8 = Content Manager

        $pivotData = [];

        // 1. Super Admin: All permissions (1 to 103)
        for ($i = 1; $i <= 103; $i++) {
            $pivotData[] = ['role_id' => 1, 'permission_id' => $i];
        }

        // 2. Admin: Dashboard, User, Role, Permission, Settings, CMS/Abouts
        $adminPermissions = array_merge(
            [1],                     // Dashboard
            range(77, 80),           // Users
            range(81, 85),           // Roles
            [86],                    // Permissions
            range(87, 98),           // Settings (Lang, Translations, Provinces, Currency, Payment Methods)
            range(99, 103)           // CMS / Abouts
        );
        foreach ($adminPermissions as $pid) {
            $pivotData[] = ['role_id' => 2, 'permission_id' => $pid];
        }

        // 3. Manager: Dashboard, Catalog, Orders, Purchases, Customers, Suppliers, Reports, Expenses
        $managerPermissions = array_merge(
            [1],                     // Dashboard
            [3],                     // Orders
            range(4, 7),             // Reports
            range(8, 17),            // Customers & Customer Types
            range(18, 33),           // Products, Categories, Brands
            range(34, 39),           // Purchases
            range(40, 44),           // Suppliers
            range(45, 54)            // Expenses & Expense Types
        );
        foreach ($managerPermissions as $pid) {
            $pivotData[] = ['role_id' => 3, 'permission_id' => $pid];
        }

        // 4. Cashier: Dashboard, POS, Sales View/Create, Customer View, Payment Methods View
        $cashierPermissions = array_merge(
            [1, 2, 3],               // Dashboard, POS, Orders
            [8, 9],                  // Customer view
            [94, 95]                 // Payment methods view
        );
        foreach ($cashierPermissions as $pid) {
            $pivotData[] = ['role_id' => 4, 'permission_id' => $pid];
        }

        // 5. Accountant: Dashboard, Reports, Expenses, Payment Methods View
        $accountantPermissions = array_merge(
            [1],                     // Dashboard
            range(4, 7),             // Reports
            range(45, 54),           // Expenses & Expense Types
            [94, 95]                 // Payment methods view
        );
        foreach ($accountantPermissions as $pid) {
            $pivotData[] = ['role_id' => 5, 'permission_id' => $pid];
        }

        // 6. Warehouse Staff: Dashboard, Products, Categories, Brands, Purchases, Suppliers
        $warehousePermissions = array_merge(
            [1],                     // Dashboard
            range(18, 33),           // Products, Categories, Brands
            range(34, 39),           // Purchases
            range(40, 44)            // Suppliers
        );
        foreach ($warehousePermissions as $pid) {
            $pivotData[] = ['role_id' => 6, 'permission_id' => $pid];
        }

        // 7. HR Staff: Dashboard, Employees, Payrolls, Employee Payrolls, Positions
        $hrPermissions = array_merge(
            [1],                     // Dashboard
            range(55, 76)            // Employees, Employee Payrolls, Payrolls, Positions
        );
        foreach ($hrPermissions as $pid) {
            $pivotData[] = ['role_id' => 7, 'permission_id' => $pid];
        }

        // 8. Content Manager: Dashboard, Abouts/CMS, Settings (Provinces, Lang, Translations)
        $contentPermissions = array_merge(
            [1],                     // Dashboard
            range(99, 103),          // Abouts / CMS
            range(87, 92)            // Lang, Translations, Provinces
        );
        foreach ($contentPermissions as $pid) {
            $pivotData[] = ['role_id' => 8, 'permission_id' => $pid];
        }

        DB::table('permission_role')->insert($pivotData);
    }
}
