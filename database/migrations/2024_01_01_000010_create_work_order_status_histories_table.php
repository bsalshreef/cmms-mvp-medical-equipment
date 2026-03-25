<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the work_order_status_histories table used by WorkOrderSeeder
 * to record every status transition for audit purposes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')
                  ->constrained('work_orders')
                  ->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('change_note')->nullable();
            $table->timestamps();

            $table->index('work_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_status_histories');
    }
};
