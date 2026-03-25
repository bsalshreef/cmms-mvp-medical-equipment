<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();

            // Core references
            $table->foreignId('device_id')
                ->constrained('devices')
                ->restrictOnDelete();

            $table->foreignId('service_category_id')
                ->nullable()
                ->constrained('service_categories')
                ->nullOnDelete();

            // Workflow fields
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('MEDIUM');

            $table->enum('workflow_status', [
                'OPEN',
                'IN_PROGRESS',
                'ON_HOLD',
                'COMPLETED',
                'CLOSED',
                'CANCELLED',
            ])->default('OPEN');

            $table->enum('resolution_status', [
                'RESOLVED',
                'UNRESOLVED',
                'PARTIAL',
            ])->nullable();

            $table->text('description');
            $table->text('resolution_notes')->nullable();
            $table->dateTime('target_start_datetime')->nullable();

            // User references
            $table->foreignId('created_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('closed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('closed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Indexes for common dashboard queries
            $table->index('workflow_status');
            $table->index('priority');
            $table->index('created_by_user_id');
            $table->index('assigned_to_user_id');
            $table->index('closed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
