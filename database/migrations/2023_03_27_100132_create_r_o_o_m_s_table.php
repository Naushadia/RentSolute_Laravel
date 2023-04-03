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
        Schema::create('r_o_o_m_s', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('caption');
            $table->integer('room_type');
            $table->unsignedBigInteger('imageId');
            $table->foreign('imageId')->references('id')->on('images')->onDelete('cascade');
            $table->unsignedBigInteger('propertyId');
            $table->foreign('propertyId')->references('id')->on('properties')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('r_o_o_m_s');
    }
};
