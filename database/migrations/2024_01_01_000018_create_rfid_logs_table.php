<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfid_logs', function (Blueprint $table) {
            $table->id();
            $table->string('rfid_card_number', 100);
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('action', ['swipe_in', 'swipe_out', 'invalid_card', 'access_denied']);
            $table->text('raw_data')->nullable()->comment('Raw RFID payload');
            $table->string('reader_id', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['rfid_card_number', 'created_at']);
            $table->index(['employee_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_logs');
    }
};
