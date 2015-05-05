<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoreCreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('environment');
            $table->string('namespace')->nullable();
            $table->string('group')->nullable();
            $table->string('item')->nullable();
            $table->text('value')->nullable();

            $table->engine = 'InnoDB';
            $table->unique(['environment', 'group', 'namespace', 'item']);
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
