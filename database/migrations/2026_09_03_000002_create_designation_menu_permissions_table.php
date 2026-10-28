<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designation_menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('designation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->boolean('can_view')->default(false);
            $table->timestamps();

            $table->unique(['designation_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designation_menu_permissions');
    }
};
