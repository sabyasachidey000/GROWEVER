<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_run_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('rejection_reason', [
                'crack', 'dimension_error', 'surface_defect',
                'burr', 'material_defect', 'tool_mark',
                'porosity', 'misrun', 'other'
            ]);
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->text('corrective_action')->nullable();
            $table->date('rejection_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'rejection_date']);
            $table->index(['machine_id', 'rejection_date']);
            $table->index('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rejections');
    }
};
