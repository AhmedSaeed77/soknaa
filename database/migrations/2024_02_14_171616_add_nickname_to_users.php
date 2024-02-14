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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->unique();
            $table->string('phone');
            $table->string('parent_phone')->nullable();
            $table->string('type');
            $table->string('age');
            $table->string('child_num');
            $table->string('membership_num');
            $table->string('status')->dafult(0);
            $table->string('sex');
            $table->string('block')->dafult(0);
            $table->string('is_active')->dafult(0);
            $table->string('is_ordered')->dafult(0);
            $table->string('is_showprofile')->dafult(1);
            $table->string('typemerrage');
            $table->string('familysitiation');
            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
