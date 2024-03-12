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
            $table->unsignedBigInteger('from_user')->nullable();
            $table->foreign('from_user')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('from_admin')->nullable();
            $table->foreign('from_admin')->references('id')->on('admins')->onDelete('cascade');
            $table->unsignedBigInteger('to_user')->nullable();
            $table->foreign('to_user')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('to_admin')->nullable();
            $table->foreign('to_admin')->references('id')->on('admins')->onDelete('cascade');
            $table->string('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
