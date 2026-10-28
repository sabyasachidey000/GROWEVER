<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('breakdown_type', [
                'mechanical', 'electrical', 'hydraulic',
                'pneumatic', 'tool_failure', 'material_shortage',
                'software', 'other'
            ]);
            $table->string('severity', 20)->default('medium');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('duration_minutes')->nullable()->comment('Auto-calculated on close');
            $table->text('description');
            $table->text('root_cause')->nullable();
            $table->text('repair_action')->nullable();
            $table->text('spare_parts_used')->nullable();
            $table->decimal('repair_cost', 10, 2)->nullable();
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['machine_id', 'status']);
            $table->index(['start_time', 'end_time']);
            $table->index('breakdown_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_breakdowns');
    }
};
