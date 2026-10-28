<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'revised'])->default('pending');
            $table->text('review_comments')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->dateTime('submitted_at');
            $table->dateTime('reviewed_at')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->index(['production_run_id', 'status']);
            $table->index(['submitted_by', 'submitted_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_approvals');
    }
};
