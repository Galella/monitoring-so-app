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
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->string('cm')->nullable();
            $table->integer('order_number')->unique(); // order is reserved keyword
            $table->string('container');
            $table->string('seal');
            $table->integer('p20');
            $table->integer('p40');
            $table->string('po')->nullable();
            $table->string('kereta');
            $table->date('atd');
            $table->string('customer');
            $table->string('stasiun_asal');
            $table->string('stasiun_tujuan');
            $table->string('gudang_asal')->nullable();
            $table->string('gudang_tujuan')->nullable();
            $table->string('jenis');
            $table->string('service');
            $table->string('payment');
            $table->string('so');
            $table->date('submit_so')->nullable();
            
            // Financials (Integers for simplicity, could be BigInteger/Decimal)
            $table->integer('nominal_ppn')->nullable();
            $table->integer('sa_ppn')->nullable();
            $table->integer('loading_ppn')->nullable();
            $table->integer('unloading_ppn')->nullable();
            $table->integer('trucking_orig_ppn')->nullable();
            $table->integer('trucking_dest_ppn')->nullable();
            $table->integer('sa')->nullable();
            $table->integer('loading')->nullable();
            $table->integer('unloading')->nullable();
            $table->integer('trucking_orig')->nullable();
            $table->integer('trucking_dest')->nullable();
            $table->integer('nominal')->nullable();
            
            $table->string('klaim')->nullable();
            $table->string('dokumen')->nullable();
            $table->string('alur_dokumen')->nullable();
            $table->integer('berat')->nullable();
            $table->string('isi_barang')->nullable();
            $table->string('ppcw')->nullable();
            $table->string('owner')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coins');
    }
};
