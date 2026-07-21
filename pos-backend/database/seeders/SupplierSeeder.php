<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $suppliers = [
            ['name' => 'Apple Cambodia Authorized Reseller', 'contact_person' => 'Mr. Sokha Chan', 'tel' => '+855-23-211-001', 'email' => 'supplier@apple-cambodia.com', 'address' => 'No. 168, Monivong Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001001', 'opening_balance' => 50000.00, 'current_balance' => 45000.00, 'is_active' => true],
            ['name' => 'Samsung Distributor Cambodia', 'contact_person' => 'Ms. Dara Kim', 'tel' => '+855-23-211-002', 'email' => 'sales@samsung-kh.com', 'address' => 'No. 72, Preah Sihanouk Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001002', 'opening_balance' => 80000.00, 'current_balance' => 60000.00, 'is_active' => true],
            ['name' => 'Xiaomi Cambodia Official Distributor', 'contact_person' => 'Mr. Vibol Prak', 'tel' => '+855-23-211-003', 'email' => 'sales@xiaomi-cambodia.com', 'address' => 'No. 45, Street 271, Phnom Penh', 'vat_number' => 'VAT-KH-001003', 'opening_balance' => 35000.00, 'current_balance' => 28000.00, 'is_active' => true],
            ['name' => 'Oppo Cambodia Distributor', 'contact_person' => 'Ms. Chanthy Lim', 'tel' => '+855-23-211-004', 'email' => 'info@oppo-cambodia.com', 'address' => 'No. 10, Street 169, Phnom Penh', 'vat_number' => 'VAT-KH-001004', 'opening_balance' => 25000.00, 'current_balance' => 20000.00, 'is_active' => true],
            ['name' => 'Vivo Cambodia Official', 'contact_person' => 'Mr. Bunna Ros', 'tel' => '+855-23-211-005', 'email' => 'business@vivo-kh.com', 'address' => 'No. 33, Street 163, Phnom Penh', 'vat_number' => 'VAT-KH-001005', 'opening_balance' => 20000.00, 'current_balance' => 15000.00, 'is_active' => true],
            ['name' => 'Dell Technologies Cambodia', 'contact_person' => 'Mr. Rithy Nhem', 'tel' => '+855-23-211-006', 'email' => 'sales@dell-cambodia.com', 'address' => 'No. 88, Confederation de la Russie Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001006', 'opening_balance' => 40000.00, 'current_balance' => 35000.00, 'is_active' => true],
            ['name' => 'HP Inc. Cambodia Authorized Partner', 'contact_person' => 'Ms. Sreytoch Ouk', 'tel' => '+855-23-211-007', 'email' => 'partner@hp-cambodia.com', 'address' => 'No. 99, Nehru Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001007', 'opening_balance' => 35000.00, 'current_balance' => 30000.00, 'is_active' => true],
            ['name' => 'Lenovo Cambodia Business Partner', 'contact_person' => 'Mr. Panha Seng', 'tel' => '+855-23-211-008', 'email' => 'lenovo@techpartner.com.kh', 'address' => 'No. 22, Mao Tse Toung Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001008', 'opening_balance' => 30000.00, 'current_balance' => 25000.00, 'is_active' => true],
            ['name' => 'Asus Cambodia Distributor', 'contact_person' => 'Ms. Sokmony Heng', 'tel' => '+855-23-211-009', 'email' => 'asus@distributor.kh', 'address' => 'No. 55, Street 360, Phnom Penh', 'vat_number' => 'VAT-KH-001009', 'opening_balance' => 25000.00, 'current_balance' => 20000.00, 'is_active' => true],
            ['name' => 'Huawei Cambodia Official Distributor', 'contact_person' => 'Mr. Chamroeun Uy', 'tel' => '+855-23-211-010', 'email' => 'info@huawei-cambodia.com', 'address' => 'No. 77, Russian Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001010', 'opening_balance' => 20000.00, 'current_balance' => 18000.00, 'is_active' => true],
            ['name' => 'Sony Cambodia Electronics', 'contact_person' => 'Ms. Kolap Pen', 'tel' => '+855-23-211-011', 'email' => 'sony@elec-cambodia.com', 'address' => 'No. 44, Street 214, Phnom Penh', 'vat_number' => 'VAT-KH-001011', 'opening_balance' => 15000.00, 'current_balance' => 12000.00, 'is_active' => true],
            ['name' => 'TP-Link Cambodia Network Distributor', 'contact_person' => 'Mr. Piseth Tep', 'tel' => '+855-23-211-012', 'email' => 'tplink@network-kh.com', 'address' => 'No. 11, Street 143, Phnom Penh', 'vat_number' => 'VAT-KH-001012', 'opening_balance' => 18000.00, 'current_balance' => 15000.00, 'is_active' => true],
            ['name' => 'Computer Wholesale Phnom Penh', 'contact_person' => 'Mr. Sopheap Mey', 'tel' => '+855-23-211-013', 'email' => 'wholesale@comp-pnp.com', 'address' => 'Phsar Orussey, Khan 7 Makara, Phnom Penh', 'vat_number' => 'VAT-KH-001013', 'opening_balance' => 100000.00, 'current_balance' => 80000.00, 'is_active' => true],
            ['name' => 'Tech Parts Depot Cambodia', 'contact_person' => 'Ms. Sreyleak Khy', 'tel' => '+855-23-211-014', 'email' => 'parts@techdepot.kh', 'address' => 'No. 66, Street 51, Phnom Penh', 'vat_number' => 'VAT-KH-001014', 'opening_balance' => 30000.00, 'current_balance' => 25000.00, 'is_active' => true],
            ['name' => 'Realme Cambodia Authorized', 'contact_person' => 'Mr. Tharith Som', 'tel' => '+855-23-211-015', 'email' => 'realme@cambodia-dist.com', 'address' => 'No. 37, Street 173, Phnom Penh', 'vat_number' => 'VAT-KH-001015', 'opening_balance' => 12000.00, 'current_balance' => 10000.00, 'is_active' => true],
            ['name' => 'Accessories Plus Cambodia', 'contact_person' => 'Ms. Bopha Noun', 'tel' => '+855-23-211-016', 'email' => 'info@accessories-plus.kh', 'address' => 'No. 19, Street 107, Phnom Penh', 'vat_number' => 'VAT-KH-001016', 'opening_balance' => 8000.00, 'current_balance' => 6500.00, 'is_active' => true],
            ['name' => 'Acer Cambodia Business', 'contact_person' => 'Mr. Vanna Net', 'tel' => '+855-23-211-017', 'email' => 'acer@business.kh', 'address' => 'No. 52, Monireth Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001017', 'opening_balance' => 20000.00, 'current_balance' => 16000.00, 'is_active' => true],
            ['name' => 'MSI Gaming Cambodia', 'contact_person' => 'Mr. Davan Mol', 'tel' => '+855-23-211-018', 'email' => 'msi@gaming.kh', 'address' => 'No. 28, Street 360, Phnom Penh', 'vat_number' => 'VAT-KH-001018', 'opening_balance' => 25000.00, 'current_balance' => 20000.00, 'is_active' => true],
            ['name' => 'SmartPhone Hub Co., Ltd', 'contact_person' => 'Ms. Leakena Sen', 'tel' => '+855-23-211-019', 'email' => 'contact@smartphone-hub.kh', 'address' => 'Central Market Area, Phnom Penh', 'vat_number' => 'VAT-KH-001019', 'opening_balance' => 50000.00, 'current_balance' => 42000.00, 'is_active' => true],
            ['name' => 'Digital Solution Group Supplier', 'contact_person' => 'Mr. Chhenghout Em', 'tel' => '+855-23-211-020', 'email' => 'supply@digitalsolution.kh', 'address' => 'No. 83, Tep Vong Street, Phnom Penh', 'vat_number' => 'VAT-KH-001020', 'opening_balance' => 60000.00, 'current_balance' => 55000.00, 'is_active' => true],
            ['name' => 'Siem Reap Tech Supplier', 'contact_person' => 'Mr. Sorin Vith', 'tel' => '+855-63-211-001', 'email' => 'supply@sr-tech.kh', 'address' => 'No. 14, Sivatha Blvd, Siem Reap', 'vat_number' => 'VAT-KH-001021', 'opening_balance' => 15000.00, 'current_balance' => 12000.00, 'is_active' => true],
            ['name' => 'Battambang IT Distributor', 'contact_person' => 'Ms. Channary Oem', 'tel' => '+855-53-211-001', 'email' => 'it@bb-distributor.kh', 'address' => 'No. 88, Street 1, Battambang', 'vat_number' => 'VAT-KH-001022', 'opening_balance' => 12000.00, 'current_balance' => 10000.00, 'is_active' => true],
            ['name' => 'Vietnam Import Electronics KH', 'contact_person' => 'Mr. Nguyen Thanh', 'tel' => '+855-23-211-023', 'email' => 'vn.import@electronics.kh', 'address' => 'No. 6, Street 221, Phnom Penh', 'vat_number' => 'VAT-KH-001023', 'opening_balance' => 40000.00, 'current_balance' => 35000.00, 'is_active' => true],
            ['name' => 'China Direct Import Co., Ltd', 'contact_person' => 'Mr. Li Wei', 'tel' => '+855-23-211-024', 'email' => 'cn.import@direct.kh', 'address' => 'No. 121, Street 245, Phnom Penh', 'vat_number' => 'VAT-KH-001024', 'opening_balance' => 75000.00, 'current_balance' => 65000.00, 'is_active' => true],
            ['name' => 'Pacific Electronics Cambodia', 'contact_person' => 'Ms. Sona Hour', 'tel' => '+855-23-211-025', 'email' => 'sales@pacific-elec.kh', 'address' => 'No. 98, Kampuchea Krom Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001025', 'opening_balance' => 30000.00, 'current_balance' => 27000.00, 'is_active' => true],
            ['name' => 'Kampot Province IT Supply', 'contact_person' => 'Mr. Sophea Kea', 'tel' => '+855-33-211-001', 'email' => 'it@kampot-supply.kh', 'address' => 'No. 23, Kampot Market, Kampot', 'vat_number' => 'VAT-KH-001026', 'opening_balance' => 8000.00, 'current_balance' => 6000.00, 'is_active' => true],
            ['name' => 'Smart Technology Solutions', 'contact_person' => 'Ms. Rathana Kang', 'tel' => '+855-23-211-027', 'email' => 'smart@tech-solutions.kh', 'address' => 'No. 57, Street 378, Phnom Penh', 'vat_number' => 'VAT-KH-001027', 'opening_balance' => 22000.00, 'current_balance' => 18000.00, 'is_active' => true],
            ['name' => 'Enterprise Mobile Cambodia', 'contact_person' => 'Mr. Kiry Mao', 'tel' => '+855-23-211-028', 'email' => 'mobile@enterprise.kh', 'address' => 'No. 200, Monivong Blvd, Phnom Penh', 'vat_number' => 'VAT-KH-001028', 'opening_balance' => 45000.00, 'current_balance' => 38000.00, 'is_active' => true],
            ['name' => 'ProTech Import Export', 'contact_person' => 'Ms. Pich Sreymom', 'tel' => '+855-23-211-029', 'email' => 'import@protech.kh', 'address' => 'No. 34, Street 110, Phnom Penh', 'vat_number' => 'VAT-KH-001029', 'opening_balance' => 35000.00, 'current_balance' => 30000.00, 'is_active' => true],
            ['name' => 'NextGen Device Supplier KH', 'contact_person' => 'Mr. Daro Sour', 'tel' => '+855-23-211-030', 'email' => 'nextgen@devices.kh', 'address' => 'No. 9, Street 93, Phnom Penh', 'vat_number' => 'VAT-KH-001030', 'opening_balance' => 18000.00, 'current_balance' => 15000.00, 'is_active' => true],
        ];

        $insertData = array_map(fn($s) => array_merge($s, [
            'created_at' => $now,
            'updated_at' => $now,
        ]), $suppliers);

        DB::table('suppliers')->insert($insertData);
    }
}
