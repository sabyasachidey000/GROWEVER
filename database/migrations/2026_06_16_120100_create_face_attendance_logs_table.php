<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('face_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->decimal('confidence', 6, 4)->nullable();
            $table->decimal('distance', 6, 4)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('raw_descriptor')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'created_at']);
            $table->index(['machine_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_attendance_logs');
    }
};
