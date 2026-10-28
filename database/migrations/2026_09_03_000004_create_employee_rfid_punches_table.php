<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_rfid_punches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->foreignId('machine_id')->constrained()->restrictOnDelete();
            $table->foreignId('rfid_punch_log_id')->nullable()->constrained('rfid_punch_logs')->nullOnDelete();
            $table->string('rfid_card_number', 100);
            $table->string('device_id', 100);
            $table->enum('punch_type', ['LOGIN', 'LOGOUT']);
            $table->timestamp('punch_time');
            $table->timestamps();

            $table->index(['employee_id', 'punch_time']);
            $table->index(['machine_id', 'punch_time']);
            $table->index(['device_id', 'punch_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_rfid_punches');
    }
};
