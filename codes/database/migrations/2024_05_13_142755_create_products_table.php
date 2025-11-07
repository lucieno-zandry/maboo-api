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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->longText('description')->nullable();
            $table->float('price')->default(0);
            $table->smallInteger('inStock')->default(0);
            $table->foreignId('user_id');
            $table->foreignId('category_id');
            $table->string('slug');
            $table->timestamps();
            $table->float('sale_price')->default(0);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('product_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name', 20);
            $table->foreignId('product_id');
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('name');
            $table->float('price')->default(0);
            $table->smallInteger('inStock')->default(0);
            $table->foreignId('product_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_colors');
        Schema::dropIfExists('product_variants');
    }
};
