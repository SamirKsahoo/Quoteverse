<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('quote_text');
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('text_color')->default('#ffffff');
            $table->enum('text_position', ['top-left','top-center','top-right','center-left','center','center-right','bottom-left','bottom-center','bottom-right'])->default('center');
            $table->string('font_size')->default('2xl');
            $table->string('font_style')->default('serif');
            $table->integer('overlay_opacity')->default(40);
            $table->string('overlay_color')->default('#000000');
            $table->enum('status', ['publish', 'draft'])->default('draft');
            $table->integer('views')->default(0);
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
