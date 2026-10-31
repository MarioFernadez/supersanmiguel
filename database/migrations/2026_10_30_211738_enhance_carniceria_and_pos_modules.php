<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'tax_rate')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('tax_rate')->default(10)->after('price_sale');
            });
        }

        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'credit_limit')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->decimal('credit_limit', 15, 0)->nullable()->after('is_active');
            });
        }

        if (Schema::hasTable('purchase_items') && Schema::hasColumn('purchase_items', 'quantity')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->decimal('quantity', 12, 3)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'tax_rate')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('tax_rate');
            });
        }

        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'credit_limit')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('credit_limit');
            });
        }

        if (Schema::hasTable('purchase_items') && Schema::hasColumn('purchase_items', 'quantity')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->decimal('quantity', 15, 2)->change();
            });
        }
    }
};
