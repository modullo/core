<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_modules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('lms_courses')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->string('duration')->nullable();
            $table->bigInteger('module_no')->index();
            $table->text('skills_to_be_acquired')->nullable();
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
        Schema::dropIfExists('lms_modules');
    }
}
