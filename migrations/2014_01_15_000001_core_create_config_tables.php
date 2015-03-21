<?php

use Illuminate\Database\Migrations\Migration;

class CoreCreateConfigTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function ($table) {

            $table->increments('id')->unsigned();
            $table->string('environment');
            $table->string('namespace')->nullable();
            $table->string('group')->nullable();
            $table->string('item')->nullable();
            $table->text('value')->nullable();

            $table->engine = 'InnoDB';
            $table->unique(array('environment', 'group', 'namespace', 'item'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('config');
    }
}
