<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_no')->unique();
            $table->string('reference_no')->nullable();

            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('restrict');

            $table->date('purchase_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->decimal('due_amount', 15, 2)->default(0.00);

            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->enum('payment_status', ['paid', 'partial', 'due'])->default('due');
            $table->enum('status', ['ordered', 'received', 'pending'])->default('received');

            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');

            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
