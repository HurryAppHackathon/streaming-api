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
        Schema::create('user_parties', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('image_url');
            $table->boolean('is_public');
            $table->foreignId('user_id')->constrained('users'); // Owner of party
            $table->string('invite_code');
            $table->timestamp('finished_at')->nullable(); // If null, party is ongoing

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_parties');
    }
};
