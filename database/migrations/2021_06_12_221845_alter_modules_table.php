<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_modules',function (Blueprint $table){
            $table->dropColumn('module_no');
            $table->dropColumn('skills_to_be_acquired');
            $table->bigInteger('module_number');
            $table->string('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_modules',function (Blueprint $table){
            $table->dropColumn('module_number');
            $table->dropColumn('description');
        });
    }
}
