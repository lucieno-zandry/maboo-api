<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Sections table
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('order');
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Subsections table
        Schema::create('subsections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('order');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Paragraphs table
        Schema::create('paragraphs', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('subsection_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Images table
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('caption')->nullable();
            $table->integer('order')->nullable();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
        Schema::dropIfExists('paragraphs');
        Schema::dropIfExists('subsections');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('articles');
    }
};
