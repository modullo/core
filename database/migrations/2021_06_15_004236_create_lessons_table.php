<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_lessons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('resource_id')->index();
            $table->foreign('tenant_id')
                ->references('id')
                ->on('lms_tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('module_id')
                ->references('id')
                ->on('lms_modules')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('course_id')
                ->references('id')
                ->on('lms_courses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('title');
            $table->string('description');
            $table->string('duration');
            $table->string('lesson_image')->nullable();
            $table->string('lesson_type');
            $table->bigInteger('lesson_number');
            $table->text('skills_gained')->nullable();
            $table->text('additional_resources')->nullable();
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
        Schema::dropIfExists('lms_lessons');
    }
}
