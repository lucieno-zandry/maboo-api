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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('chats_users', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreignId('chat_id');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->timestamp('read_at')->nullable();
            $table->longText('content');
            $table->string('images')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->foreignId('chat_id');
            $table->foreignId('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
        Schema::dropIfExists('chats_users');
        Schema::dropIfExists('messages');
    }
};
