<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('production_runs', function (Blueprint $table) {
            $table->decimal('weight', 10, 2)->nullable()->after('start_time');
        });

        // Populate weight from existing production_quantity where available
        DB::table('production_runs')
            ->whereNotNull('production_quantity')
            ->update([
                'weight' => DB::raw('CAST(production_quantity AS DECIMAL(10,2))'),
            ]);

        Schema::table('production_runs', function (Blueprint $table) {
            $table->dropColumn(['cycle_count', 'target_quantity', 'production_quantity', 'rejection_quantity', 'good_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_runs', function (Blueprint $table) {
            $table->integer('cycle_count')->default(0)->after('runtime_minutes');
            $table->integer('production_quantity')->default(0)->after('cycle_count');
            $table->integer('rejection_quantity')->default(0)->after('production_quantity');
            $table->integer('good_quantity')->default(0)->after('rejection_quantity');
            $table->decimal('target_quantity', 10, 2)->nullable()->after('good_quantity');
        });

        Schema::table('production_runs', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
