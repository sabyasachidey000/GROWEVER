<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfid_punch_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->string('device_id', 100);
            $table->string('uid', 100);
            $table->timestamp('punch_time');
            $table->string('ip_address', 45)->nullable();

            // Keep a snapshot so old punches remain meaningful after employee data changes.
            $table->string('employee_code', 50);
            $table->string('employee_name', 150);
            $table->string('department_name', 150)->nullable();
            $table->string('designation_name', 150)->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'punch_time']);
            $table->index(['device_id', 'punch_time']);
            $table->index(['uid', 'punch_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_punch_logs');
    }
};
