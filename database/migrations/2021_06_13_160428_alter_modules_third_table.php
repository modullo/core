<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterModulesThirdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_modules',function (Blueprint $table){
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')
                ->references('id')
                ->on('lms_tenants')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
            $table->dropColumn('tenant_id');
            $table->dropForeign('tenant_id');
        });
    }
}
