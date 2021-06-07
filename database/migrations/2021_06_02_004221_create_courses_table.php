<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_courses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('tenant_id');
            $table->foreign('program_id')->references('id')->on('lms_programs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tenant_id')->references('id')->on('lms_tenants')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('course_image');
            $table->text('html_formatted_description')->nullable();
            $table->string('duration')->nullable();
            $table->text('course_requirements')->nullable();
            $table->text('skills_to_be_gained')->nullable();
            $table->string('slug')->nullable();
            $table->string('overview_video')->nullable();
            $table->enum('course_level',['compulsory','elective'])->nullable();
            $table->enum('course_state',['draft','published'])->default('draft');
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
        Schema::dropIfExists('lms_courses');
    }
}
