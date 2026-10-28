<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oee_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->date('record_date');
            $table->integer('planned_production_time')->comment('in minutes');
            $table->integer('runtime')->comment('in minutes');
            $table->integer('actual_output');
            $table->integer('ideal_output');
            $table->integer('total_quantity');
            $table->integer('good_quantity');
            $table->decimal('availability', 5, 4)->default(0)->comment('Calculated in model');
            $table->decimal('performance', 5, 4)->default(0)->comment('Calculated in model');
            $table->decimal('quality_rate', 5, 4)->default(0)->comment('Calculated in model');
            $table->decimal('oee', 5, 4)->default(0)->comment('Calculated in model');
            $table->timestamps();

            $table->index(['machine_id', 'record_date']);
            $table->index(['shift_id', 'record_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oee_records');
    }
};
