<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumText('body');
            $table->integer('template_id')->unsigned();
            $table->integer('parent_version_id')->unsigned()->nullable();
            $table->integer('created_by')->unsigned();
            $table->dateTime('created_at');

            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('template_versions');
    }
}
