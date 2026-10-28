<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('energy_readings') || Schema::hasColumn('energy_readings', 'machine_id')) {
            return;
        }

        Schema::table('energy_readings', function (Blueprint $table) {
            $table->foreignId('machine_id')
                ->nullable()
                ->constrained('machines')
                ->nullOnDelete();

            $table->index(['machine_id', 'timestamp']);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('energy_readings') || !Schema::hasColumn('energy_readings', 'machine_id')) {
            return;
        }

        Schema::table('energy_readings', function (Blueprint $table) {
            $table->dropIndex(['machine_id', 'timestamp']);
            $table->dropConstrainedForeignId('machine_id');
        });
    }
};
