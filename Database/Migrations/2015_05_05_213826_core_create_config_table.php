<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoreCreateConfigTable extends Migration
{
    public function __construct()
    {
        // Get the prefix
        $this->prefix = config('cms.core.config.table-prefix', 'core_');
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->prefix.'config', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('environment');
            $table->string('namespace')->nullable();
            $table->string('group')->nullable();
            $table->string('item')->nullable();
            $table->text('value')->nullable();

            $table->unique(['environment', 'group', 'namespace', 'item']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop($this->prefix.'config');
    }
}
