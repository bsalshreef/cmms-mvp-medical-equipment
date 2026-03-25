<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Patch migration — adds columns and tables required by the seeders
 * that were not present in the original schema migrations.
 *
 * Changes:
 *  1. vendors          → add `vendor_code` (unique string)
 *  2. device_categories → new table
 *  3. devices          → add `category_id` (FK → device_categories)
 *                        add `vendor_id`   (FK → vendors)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. vendors: add vendor_code ───────────────────────────────────
        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'vendor_code')) {
                $table->string('vendor_code')->unique()->nullable()->after('id');
            }
        });

        // ── 2. device_categories ──────────────────────────────────────────
        if (! Schema::hasTable('device_categories')) {
            Schema::create('device_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // ── 3. devices: add category_id and vendor_id ─────────────────────
        Schema::table('devices', function (Blueprint $table) {
            if (! Schema::hasColumn('devices', 'category_id')) {
                $table->foreignId('category_id')
                      ->nullable()
                      ->after('name')
                      ->constrained('device_categories')
                      ->nullOnDelete();
            }
            if (! Schema::hasColumn('devices', 'vendor_id')) {
                $table->foreignId('vendor_id')
                      ->nullable()
                      ->after('category_id')
                      ->constrained('vendors')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Vendor::class);
            $table->dropColumn('vendor_id');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('device_categories');

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropUnique(['vendor_code']);
            $table->dropColumn('vendor_code');
        });
    }
};
