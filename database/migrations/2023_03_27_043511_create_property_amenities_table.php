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
        Schema::create('property_amenities', function (Blueprint $table) {
            $table->timestamps();
            $table->id();
            $table->unsignedBigInteger('amenityId')->nullable();
            $table->foreign('amenityId')->references('id')->on('amenities')->onDelete('cascade');
            $table->unsignedBigInteger('propertyId')->nullable();
            $table->foreign('propertyId')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_amenities');
    }
};
