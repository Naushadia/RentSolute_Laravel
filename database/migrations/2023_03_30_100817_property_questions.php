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
        Schema::create('property_questions', function (Blueprint $table) {
            $table->timestamps();
            $table->id();
            $table->unsignedBigInteger('questionId')->nullable();
            $table->foreign('questionId')->references('id')->on('questions')->onDelete('cascade');
            $table->unsignedBigInteger('propertyId')->nullable();
            $table->foreign('propertyId')->references('id')->on('properties')->onDelete('cascade');
            $table->unsignedBigInteger('optionId')->nullable();
            $table->foreign('optionId')->references('id')->on('options')->onDelete('cascade');
            $table->integer('preferred')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_questions');
    }
};
