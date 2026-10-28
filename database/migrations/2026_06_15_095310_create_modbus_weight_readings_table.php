<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modbus_weight_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->integer('weight')->comment('Weight value read from PLC register 100');
            $table->string('source')->default('modbus_tcp')->comment('Data source identifier');
            $table->ipAddress('plc_ip')->nullable()->comment('PLC IP address that sent the reading');
            $table->boolean('processed')->default(false)->comment('Whether the reading was incorporated into a production run');
            $table->text('raw_response')->nullable()->comment('Raw API response for debugging');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['machine_id', 'created_at']);
            $table->index('processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modbus_weight_readings');
    }
};
