<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_run_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspector_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained()->nullOnDelete();
            $table->date('inspection_date');
            $table->string('inspection_type', 50)->default('first_piece');
            $table->integer('sample_size');
            $table->integer('passed_quantity');
            $table->integer('failed_quantity');
            $table->decimal('acceptance_rate', 5, 2)->default(0)->comment('Calculated in model');
            $table->text('measurements')->nullable()->comment('JSON of measurement values');
            $table->text('remarks')->nullable();
            $table->enum('result', ['pass', 'fail', 'conditional'])->default('pass');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'inspection_date']);
            $table->index(['inspector_id', 'inspection_date']);
            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_inspections');
    }
};
