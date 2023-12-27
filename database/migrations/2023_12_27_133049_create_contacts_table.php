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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anagraphic_id');
            $table->foreign('anagraphic_id')->references('id')->on('anagraphics');
            $table->string('contact');
            $table->enum('type', ['home', 'work', 'telephone', 'email', 'fax', 'WhatsApp', 'altro']);
            $table->string('notes')->nullable();
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {

            $table->dropForeign('contacts_anagraphic_id_foreign');
            $table->dropColumn('anagraphic_id');
        });
    }
};
