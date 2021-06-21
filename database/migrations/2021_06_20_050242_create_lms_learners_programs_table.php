<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsLearnersProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_learners_programs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->unsignedBigInteger('learner_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('lms_tenants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('program_id')->references('id')->on('lms_programs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('learner_id')->references('id')->on('lms_learners')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('overall_progress')->default(0);
            $table->boolean('program_commencement')->default(false);
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
        Schema::dropIfExists('lms_learners_programs');
    }
}
