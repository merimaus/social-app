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
        Schema::create('reactions', function (Blueprint $table) {
            $table->ulid('id')->primary(); // Explicit ULID Primary key
            $table->foreignUlid('user_id')->constrained()->onDelete('cascade'); // Matches User ULID
            $table->foreignUlid('post_id')->constrained()->onDelete('cascade'); // Matches Post ULID
            $table->string('type')->default('like');
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reaction_tables');
    }
};
