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
        Schema::create('user_videos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users'); // Id of owner
            $table->string('name');
            $table->string('description');
            $table->string('url'); // Url of video
            $table->string('thumbnail_url'); // Url of video's thumbnail 
            $table->boolean('is_public'); // Flag on whether video is public or not

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_videos');
    }
};
