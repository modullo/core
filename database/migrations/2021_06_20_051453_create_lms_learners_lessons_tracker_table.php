<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsLearnersLessonsTrackerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_learners_lessons_tracker', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->unsignedBigInteger('learner_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('course_id')->references('id')->on('lms_courses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('learner_id')->references('id')->on('lms_learners')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('lesson_id')->references('id')->on('lms_lessons')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('status')->default(false);
            $table->boolean('completed')->default(false);
            $table->timestamp('completion_time')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_learners_lessons_tracker');
    }
}
