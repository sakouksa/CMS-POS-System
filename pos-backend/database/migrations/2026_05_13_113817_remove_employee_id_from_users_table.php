<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('users', 'employee_id')) {
            Schema::table('users', function (Blueprint $table) {
                // Drop foreign key if it exists before dropping column
                try {
                    $table->dropForeign(['employee_id']);
                } catch (\Exception $e) {
                    // Foreign key may not exist, continue
                }
                $table->dropColumn('employee_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('cascade');
        });
    }
};
