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
        Schema::create('perjalanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supir_id')->constrained('supirs')->onDelete('cascade');
            $table->foreignId('truk_id')->constrained('truks')->onDelete('cascade');
            $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('cascade');
            $table->string('lat_berangkat');
            $table->string('lng_berangkat');
            $table->string('lat_tujuan');
            $table->string('lng_tujuan');
            $table->string('bensin_awal');
            $table->string('bensin_akhir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjalanans');
    }
};
