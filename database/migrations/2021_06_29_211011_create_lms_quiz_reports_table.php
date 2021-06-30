<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsQuizReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('lms_quiz_reports', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->index();
			$table->unsignedBigInteger('learner_id');
			$table->unsignedBigInteger('lesson_id');
			$table->unsignedBigInteger('quiz_id');
			$table->foreign('learner_id')->references('id')->on('lms_learners')->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('lesson_id')->references('id')->on('lms_lessons')->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('quiz_id')->references('id')->on('lms_quiz')->onDelete('cascade')->onUpdate('cascade');
			$table->text('submission')->nullable();
			$table->bigInteger('score')->default(0);
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('lms_quiz_reports');
	}
}
