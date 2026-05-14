<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('survey_number')->unique();
            $table->json('centroid');           // {"lat":13.21,"lng":77.57}
            $table->json('survey_records');     // array of polygon rings
            $table->enum('status', [
                'available',
                'need_to_acquire',
                'acquired',
                'reserved',
                'sold'
            ])->default('available');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('area_sqft', 10, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};