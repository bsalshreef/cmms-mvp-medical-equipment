<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')
                ->constrained('devices')
                ->cascadeOnDelete();
            $table->foreignId('maintenance_type_id')
                ->nullable()
                ->constrained('maintenance_types')
                ->nullOnDelete();
            $table->enum('frequency_type', ['DAILY', 'WEEKLY', 'MONTHLY', 'QUARTERLY', 'ANNUAL'])
                ->default('MONTHLY');
            $table->unsignedSmallInteger('frequency_value')->default(1);
            $table->date('last_done_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('next_due_date');
            $table->index('is_active');
        });

        Schema::create('maintenance_plan_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_plan_id')
                ->constrained('maintenance_plans')
                ->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->date('executed_date')->nullable();
            $table->foreignId('executed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('status', ['PENDING', 'DONE', 'SKIPPED'])->default('PENDING');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('scheduled_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_plan_executions');
        Schema::dropIfExists('maintenance_plans');
        Schema::dropIfExists('maintenance_types');
    }
};
