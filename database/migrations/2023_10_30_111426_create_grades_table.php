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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrolled_student_id');
            $table->unsignedBigInteger('assessment_id');
            $table->string('points');
            $table->integer('fg_grade');
            $table->integer('midterms_grade');
            $table->integer('finals_grade');
            $table->boolean('published')->default(false);
            $table->boolean('published_midterms')->default(false);
            $table->boolean('published_finals')->default(false);
            $table->string('status');
            $table->string('midterms_status');
            $table->string('finals_status');
            $table->timestamps();

            $table->foreign('enrolled_student_id')->references('id')->on('enrolled_students');
            $table->foreign('assessment_id')->references('id')->on('assessments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
