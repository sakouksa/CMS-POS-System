<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * CMS-POS-System Enterprise Database Seeder
     * Stack: Laravel + PostgreSQL
     *
     * Seeding Order (respects foreign key constraints):
     * 1.  Roles
     * 2.  Permissions
     * 3.  PermissionRole (pivot)
     * 4.  PaymentMethods
     * 5.  Users + Profiles + UserRoles
     * 6.  Categories
     * 7.  Brands
     * 8.  Products
     * 9.  Customer Types + Customers
     * 10. Suppliers
     * 11. Currencies + Orders + OrderItems
     * 12. Purchases + PurchaseItems
     * 13. Positions + Employees
     * 14. Payrolls + EmployeePayrolls
     * 15. ExpenseTypes + Expenses
     * 16. CMS (Languages, Abouts, Provinces, Translations)
     */
    public function run(): void
    {
        // ── RBAC ─────────────────────────────────────────
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);

        // ── PAYMENT METHODS ───────────────────────────────
        $this->call(PaymentMethodSeeder::class);

        // ── USERS (requires roles + payment_methods) ──────
        $this->call(UserSeeder::class);

        // ── CATALOG ───────────────────────────────────────
        $this->call(CategorySeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(ProductSeeder::class);

        // ── CUSTOMERS & SUPPLIERS ─────────────────────────
        $this->call(CustomerSeeder::class);   // also seeds customer_types
        $this->call(SupplierSeeder::class);

        // ── SALES (requires currencies, customers, products, payment_methods, users) ──
        $this->call(OrderSeeder::class);       // also seeds currencies

        // ── PURCHASES (requires suppliers, products, payment_methods, users) ─────────
        $this->call(PurchaseSeeder::class);

        // ── HR (requires payment_methods) ─────────────────
        $this->call(EmployeeSeeder::class);    // also seeds positions
        $this->call(PayrollSeeder::class);

        // ── FINANCE ───────────────────────────────────────
        $this->call(ExpenseSeeder::class);     // also seeds expense_types

        // ── CMS ───────────────────────────────────────────
        $this->call(CMSSeeder::class);         // languages, abouts, provinces, translations
    }
}
