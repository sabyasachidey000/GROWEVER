<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 50)->unique();
            $table->string('name', 150);
            $table->string('category', 100);
            $table->enum('product_type', ['ferrous_waste_scrap', 'iron_steel_flanges', 'bolts_nuts_washers', 'industrial_fasteners', 'raw_material', 'other'])->default('other');
            $table->string('unit_of_measure', 20)->default('pcs');
            $table->decimal('target_cycle_time', 10, 2)->nullable()->comment('in seconds');
            $table->decimal('standard_production_rate', 10, 2)->nullable()->comment('units per hour');
            $table->text('specifications')->nullable();
            $table->text('drawing_reference')->nullable();
            $table->string('material_grade')->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->string('dimensions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_code', 'category']);
            $table->index('product_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
