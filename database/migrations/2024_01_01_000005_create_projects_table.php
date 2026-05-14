<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('center_lat', 10, 7)->nullable();
            $table->decimal('center_lng', 10, 7)->nullable();
            $table->unsignedTinyInteger('zoom')->default(15);
            $table->timestamps();
        });

        // Seed with the two projects already in your surveys table
        DB::table('projects')->insert([
            [
                'id'         => 1,
                'name'       => 'Yelahanka Project',
                'center_lat' => 13.2105,
                'center_lng' => 77.5775,
                'zoom'       => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'name'       => 'Bidadi Project',
                'center_lat' => 12.9888,
                'center_lng' => 77.4402,
                'zoom'       => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Add foreign key on surveys
        // Schema::table('surveys', function (Blueprint $table) {
        //     $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
        // });
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });
        Schema::dropIfExists('projects');
    }
};