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
        Schema::create('cms', function (Blueprint $table) {
            $table->id();
            $table->string('ppcw');
            $table->string('container');
            $table->string('seal')->nullable();
            $table->string('shipper');
            $table->string('consignee');
            $table->string('status');
            $table->string('commodity')->nullable();
            $table->string('size');
            $table->integer('berat')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('cm');
            $table->date('atd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms');
    }
};
