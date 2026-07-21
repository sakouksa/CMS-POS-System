<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            
            $table->integer('min_stock_alert')
                ->default(5)
                ->comment('Low stock alert');

            $table->boolean('is_featured')
                ->default(false)
                ->comment('Featured product');

            $table->json('gallery')
                ->nullable()
                ->comment('Multiple product images');

        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn([
                'sku',
                'barcode',
                'cost_price',
                'discount_price',
                'min_stock_alert',
                'is_featured',
                'gallery',
            ]);

        });
    }
};
