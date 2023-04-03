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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('UserId')->nullable();
            $table->foreign('UserId')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('tenancy_status');
            $table->integer('postal_code');
            $table->string('name');
            $table->string('property_type');
            $table->text('description');
            $table->string('street');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('furnishing_status');
            $table->string('furnishing_detailes');
            $table->string('share_property_url');
            $table->string('area');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
