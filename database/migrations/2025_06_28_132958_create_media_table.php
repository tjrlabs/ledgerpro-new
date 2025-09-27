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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('file_type'); // e.g., 'image', 'video', 'audio'
            $table->string('resource_type')->nullable(); // e.g., 'post', 'comment', 'profile'
            $table->string('resource_id'); // ID of the resource this media is associated with, if any
            $table->string('file_path'); // Path to the file in storage
            $table->string('file_name'); // Original file name
            $table->string('file_size'); // Size of the file in bytes
            $table->string('file_extension'); // e.g., 'jpg', 'mp4', 'mp3'
            $table->string('visibility')->default('public'); // e.g., 'public', 'private'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
