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
        Schema::create('imported_classlist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subjects_id');
            $table->unsignedBigInteger('instructor_id');
            $table->string('days');
            $table->string('time');
            $table->string('room');
            $table->timestamps();

            $table->foreign('subjects_id')->references('id')->on('subjects');
            $table->foreign('instructor_id')->references('id')->on('users')->where('role', 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_classlist');
    }
};
