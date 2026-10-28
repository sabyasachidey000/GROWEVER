<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operator_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->dateTime('login_time');
            $table->dateTime('logout_time')->nullable();
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'login_time']);
            $table->index(['machine_id', 'status']);
            $table->index(['shift_id', 'login_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_sessions');
    }
};
