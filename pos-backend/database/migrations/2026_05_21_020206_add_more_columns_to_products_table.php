<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->nullable()->after('barcode');
            }
            if (!Schema::hasColumn('products', 'min_stock_alert')) {
                $table->integer('min_stock_alert')
                    ->default(5)
                    ->comment('Low stock alert');
            }
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')
                    ->default(false)
                    ->comment('Featured product');
            }
            if (!Schema::hasColumn('products', 'gallery')) {
                $table->json('gallery')
                    ->nullable()
                    ->comment('Multiple product images');
            }
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
