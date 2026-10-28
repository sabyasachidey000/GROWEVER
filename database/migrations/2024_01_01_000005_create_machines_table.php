<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('machine_code', 50)->unique();
            $table->string('name', 150);
            $table->string('type', 100);
            $table->enum('operation_type', ['cnc', 'manual', 'semi_auto', 'auto'])->default('manual');
            $table->string('manufacturer')->nullable();
            $table->string('model_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('plc_model')->nullable();
            $table->string('plc_ip_address')->nullable();
            $table->string('location')->nullable();
            $table->decimal('production_capacity_per_hour', 10, 2)->nullable();
            $table->decimal('production_capacity_per_day', 10, 2)->nullable();
            $table->text('specifications')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->enum('status', ['running', 'idle', 'breakdown', 'maintenance', 'offline'])->default('idle');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['machine_code', 'status']);
            $table->index('operation_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
