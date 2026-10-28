<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('energy_readings')) {
            Schema::table('energy_readings', function (Blueprint $table) {
                $this->addMissingColumns($table);
            });

            return;
        }

        Schema::create('energy_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->nullable()->constrained('machines')->nullOnDelete();
            $table->timestamp('timestamp')->useCurrent();

            $table->decimal('voltage_l1', 12, 3)->nullable();
            $table->decimal('voltage_l2', 12, 3)->nullable();
            $table->decimal('voltage_l3', 12, 3)->nullable();
            $table->decimal('voltage_avg', 12, 3)->nullable();

            $table->decimal('current_l1', 12, 3)->nullable();
            $table->decimal('current_l2', 12, 3)->nullable();
            $table->decimal('current_l3', 12, 3)->nullable();
            $table->decimal('current_sum', 12, 3)->nullable();

            $table->decimal('watt_l1', 14, 3)->nullable();
            $table->decimal('watt_l2', 14, 3)->nullable();
            $table->decimal('watt_l3', 14, 3)->nullable();
            $table->decimal('watt_sum', 14, 3)->nullable();

            $table->decimal('va_l1', 14, 3)->nullable();
            $table->decimal('va_l2', 14, 3)->nullable();
            $table->decimal('va_l3', 14, 3)->nullable();
            $table->decimal('vah', 14, 3)->nullable();

            $table->decimal('pf_l1', 8, 3)->nullable();
            $table->decimal('pf_l2', 8, 3)->nullable();
            $table->decimal('pf_l3', 8, 3)->nullable();
            $table->decimal('pf_avg', 8, 3)->nullable();

            $table->decimal('frequency', 8, 3)->nullable();
            $table->decimal('wh_import', 18, 3)->nullable();
            $table->decimal('wh_export', 18, 3)->nullable();

            $table->timestamps();

            $table->index(['machine_id', 'timestamp']);
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_readings');
    }

    private function addMissingColumns(Blueprint $table): void
    {
        if (!Schema::hasColumn('energy_readings', 'machine_id')) {
            $table->foreignId('machine_id')->nullable()->constrained('machines')->nullOnDelete();
        }

        if (!Schema::hasColumn('energy_readings', 'timestamp')) {
            $table->timestamp('timestamp')->useCurrent();
        }

        foreach ([
            'voltage_l1' => [12, 3],
            'voltage_l2' => [12, 3],
            'voltage_l3' => [12, 3],
            'voltage_avg' => [12, 3],
            'current_l1' => [12, 3],
            'current_l2' => [12, 3],
            'current_l3' => [12, 3],
            'current_sum' => [12, 3],
            'watt_l1' => [14, 3],
            'watt_l2' => [14, 3],
            'watt_l3' => [14, 3],
            'watt_sum' => [14, 3],
            'va_l1' => [14, 3],
            'va_l2' => [14, 3],
            'va_l3' => [14, 3],
            'vah' => [14, 3],
            'pf_l1' => [8, 3],
            'pf_l2' => [8, 3],
            'pf_l3' => [8, 3],
            'pf_avg' => [8, 3],
            'frequency' => [8, 3],
            'wh_import' => [18, 3],
            'wh_export' => [18, 3],
        ] as $column => [$precision, $scale]) {
            if (!Schema::hasColumn('energy_readings', $column)) {
                $table->decimal($column, $precision, $scale)->nullable();
            }
        }

        if (!Schema::hasColumn('energy_readings', 'created_at')) {
            $table->timestamp('created_at')->nullable();
        }

        if (!Schema::hasColumn('energy_readings', 'updated_at')) {
            $table->timestamp('updated_at')->nullable();
        }
    }
};
