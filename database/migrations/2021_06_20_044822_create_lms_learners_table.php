<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsLearnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_learners', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->unsignedBigInteger('lms_user_id');
            $table->unsignedBigInteger('tenant_id');
            $table->foreign('lms_user_id')->references('id')->on('lms_users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tenant_id')->references('id')->on('lms_tenants')->onDelete('cascade')->onUpdate('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->string('image')->nullable();
            $table->string('gender')->nullable();
            $table->string('location')->nullable();
            $table->text('extra_config')->nullable();
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
        Schema::dropIfExists('lms_learners');
    }
}
