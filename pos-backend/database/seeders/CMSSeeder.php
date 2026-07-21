<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CMSSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ===================== LANGUAGES =====================
        DB::table('languages')->insert([
            ['id' => 1, 'name' => 'English',  'code' => 'en', 'is_default' => true,  'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Khmer',    'code' => 'km', 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Chinese',  'code' => 'zh', 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ===================== ABOUTS (CMS pages) =====================
        DB::table('abouts')->insert([
            [
                'title'       => 'Enterprise Technology Co., Ltd',
                'sub_title'   => 'Your Trusted Technology Partner in Cambodia',
                'description' => 'Enterprise Technology Co., Ltd is a leading technology company in Cambodia specializing in consumer electronics, mobile devices, laptops, and IT solutions. Founded in 2018, we have grown to be one of the most trusted retailers and distributors of premium technology products across multiple provinces. Our commitment to quality products, exceptional customer service, and competitive pricing sets us apart from the competition.',
                'image'       => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200',
                'button_text' => 'Learn More',
                'button_url'  => '/about',
                'email'       => 'info@enterprise-tech.kh',
                'phone'       => '+855-23-999-001',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'title'       => 'Smart Mobile Cambodia',
                'sub_title'   => 'Cambodia\'s Premier Smartphone Destination',
                'description' => 'Smart Mobile Cambodia is the go-to destination for premium smartphones and mobile accessories. We carry the latest models from Apple, Samsung, Xiaomi, Oppo, and more. Our knowledgeable staff provides expert guidance to help customers find the perfect device for their needs and budget. With multiple branches across Cambodia, we bring convenience to our customers.',
                'image'       => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=1200',
                'button_text' => 'Shop Now',
                'button_url'  => '/products',
                'email'       => 'hello@smart-mobile.kh',
                'phone'       => '+855-23-999-002',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        // ===================== PROVINCES =====================
        DB::table('provinces')->insert([
            ['name' => 'Phnom Penh',     'code' => 'PNP', 'description' => 'Capital city of Cambodia',                  'distand_from_city' => 0,    'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Siem Reap',      'code' => 'SRP', 'description' => 'Home of Angkor Wat, tourism capital',       'distand_from_city' => 314,  'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Battambang',     'code' => 'BTB', 'description' => 'Second largest city of Cambodia',            'distand_from_city' => 293,  'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sihanoukville', 'code' => 'SHV', 'description' => 'Coastal province, port city',               'distand_from_city' => 230,  'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kampot',         'code' => 'KPT', 'description' => 'Southern province, pepper capital',         'distand_from_city' => 148,  'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kandal',         'code' => 'KDL', 'description' => 'Province surrounding Phnom Penh',           'distand_from_city' => 15,   'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kampong Cham',  'code' => 'KPC', 'description' => 'Eastern province on Mekong River',          'distand_from_city' => 124,  'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Takeo',          'code' => 'TKO', 'description' => 'Southern province near Vietnam border',     'distand_from_city' => 79,   'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Koh Kong',       'code' => 'KKG', 'description' => 'Western coastal province',                  'distand_from_city' => 256,  'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kratie',         'code' => 'KRT', 'description' => 'Province of Irrawaddy dolphins',            'distand_from_city' => 348,  'status' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ===================== TRANSLATIONS =====================
        $translations = [
            // About section translations to Khmer
            ['lang_id' => 2, 'table_name' => 'abouts', 'table_id' => '1', 'field_name' => 'title',       'translated_value' => 'ក្រុមហ៊ុន អេនធឺប្រីស ទេក្នូឡូជី ខូ.លីមីតធីត'],
            ['lang_id' => 2, 'table_name' => 'abouts', 'table_id' => '1', 'field_name' => 'sub_title',   'translated_value' => 'ដៃគូបច្ចេកវិទ្យាជំនឿទុកចិត្ដបំផុតរបស់អ្នកនៅកម្ពុជា'],
            ['lang_id' => 2, 'table_name' => 'abouts', 'table_id' => '1', 'field_name' => 'description', 'translated_value' => 'ក្រុមហ៊ុន Enterprise Technology Co., Ltd គឺជាក្រុមហ៊ុនបច្ចេកវិទ្យាឈានមុខគេនៅកម្ពុជា ដែលឯកទេសលើ consumer electronics, ទូរស័ព្ទ, Laptop និងដំណោះស្រាយ IT'],

            // Category translations
            ['lang_id' => 2, 'table_name' => 'categories', 'table_id' => '1', 'field_name' => 'name', 'translated_value' => 'ស្មាតហ្វូន'],
            ['lang_id' => 2, 'table_name' => 'categories', 'table_id' => '2', 'field_name' => 'name', 'translated_value' => 'ឡាបតូប'],
            ['lang_id' => 2, 'table_name' => 'categories', 'table_id' => '3', 'field_name' => 'name', 'translated_value' => 'ថេប្លេត'],
            ['lang_id' => 2, 'table_name' => 'categories', 'table_id' => '4', 'field_name' => 'name', 'translated_value' => 'គ្រឿងបន្ថែម'],
            ['lang_id' => 2, 'table_name' => 'categories', 'table_id' => '5', 'field_name' => 'name', 'translated_value' => 'សមាតខ្នាតកុំព្យូទ័រ'],
            ['lang_id' => 2, 'table_name' => 'categories', 'table_id' => '6', 'field_name' => 'name', 'translated_value' => 'ឧបករណ៍បណ្ដាញ'],
        ];

        $insertTranslations = array_map(fn($t) => array_merge($t, [
            'created_at' => $now,
            'updated_at' => $now,
        ]), $translations);

        DB::table('translations')->insert($insertTranslations);
    }
}
