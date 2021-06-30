<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignConstraintsLmsNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('lms_notes', function (Blueprint $table) {
			$table->foreign('learner_id')->references('id')->on('lms_learners')->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('module_id')->references('id')->on('lms_modules')->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('course_id')->references('id')->on('lms_courses')->onDelete('cascade')->onUpdate('cascade');
			$table->foreign('tenant_id')->references('id')->on('lms_tenants')->onDelete('cascade')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table(function (Blueprint $table) {

		});
	}
}
