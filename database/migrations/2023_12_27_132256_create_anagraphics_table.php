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
        Schema::create('anagraphics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('notes')->nullable();
            $table->binary('photo')->nullable();
            $table->boolean('deleted')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anagraphics');
    }
};
