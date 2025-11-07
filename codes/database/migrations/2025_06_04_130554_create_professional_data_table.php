<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('professional_data', function (Blueprint $table) {
            $table->timestamps();
            $table->string('title')->nullable();
            $table->string('specialization')->nullable();
            $table->float('experience')->nullable();
            $table->float('rating')->nullable();
            $table->text('description')->nullable();
            $table->json('services')->nullable();
            $table->foreignIdFor(User::class)->constrained('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_data');
    }
};
