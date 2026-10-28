<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operator_session_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('runtime_minutes')->default(0)->comment('Actual runtime in minutes');
            $table->integer('cycle_count')->default(0);
            $table->integer('production_quantity')->default(0);
            $table->integer('rejection_quantity')->default(0);
            $table->integer('good_quantity')->default(0)->comment('Calculated: production - rejection');
            $table->decimal('target_quantity', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'stopped'])->default('in_progress');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['machine_id', 'start_time']);
            $table->index(['operator_id', 'start_time']);
            $table->index(['product_id', 'start_time']);
            $table->index(['shift_id', 'start_time']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_runs');
    }
};
