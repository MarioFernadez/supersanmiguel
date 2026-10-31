<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'web_order_id')) {
            Schema::table('sales', function (Blueprint $table) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['web_order_id']);
                }
                $table->dropColumn('web_order_id');
            });
        }

        Schema::dropIfExists('web_orders');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('pages');

        if (Schema::hasTable('store_settings')) {
            DB::table('store_settings')
                ->where('key', 'like', 'shop_%')
                ->orWhere('key', 'like', 'promo_banner_%')
                ->orWhere('key', 'license_ecommerce_enabled')
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('slug')->unique();
                $table->longText('content')->nullable();
                $table->boolean('is_published')->default(true);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('url')->nullable();
                $table->string('type')->default('custom');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('menu_items')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('image_path');
                $table->string('mobile_image_path')->nullable();
                $table->string('link')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('web_orders')) {
            Schema::create('web_orders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->text('customer_address')->nullable();
                $table->foreignUuid('product_id')->nullable()->constrained()->onDelete('cascade');
                $table->integer('total');
                $table->string('status')->default('Pendiente WhatsApp');
                $table->string('payment_method')->default('efectivo');
                $table->json('items_json')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'web_order_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->uuid('web_order_id')->nullable()->after('customer_id');
                $table->foreign('web_order_id')->references('id')->on('web_orders')->onDelete('set null');
            });
        }
    }
};

