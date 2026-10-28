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
        Schema::table('products', function (Blueprint $table) {
            $table->string('hsn_code')->nullable()->after('category');
            $table->string('product_category_master_base')->nullable()->after('hsn_code');
            $table->string('product_type_master_base')->nullable()->after('product_category_master_base');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_type_master_base', 'product_category_master_base', 'hsn_code']);
        });
    }
};
