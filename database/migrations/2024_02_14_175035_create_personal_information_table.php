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
        Schema::create('personal_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('weight');
            $table->string('length');
            $table->string('skin_colour');
            $table->string('physique');
            $table->string('health_statuse');
            $table->string('religion');
            $table->string('prayer');
            $table->string('smoking');
            $table->string('beard');
            $table->string('educational_level');
            $table->string('financial_statuse');
            $table->string('employment');
            $table->string('job');
            $table->string('monthly_income');
            $table->longText('life_partner_info');
            $table->longText('my_information');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_information');
    }
};
