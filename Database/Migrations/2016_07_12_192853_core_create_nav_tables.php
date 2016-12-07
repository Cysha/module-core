<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoreCreateNavTables extends Migration
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
        Schema::create($this->prefix.'navigation', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('class');

            $table->timestamps();
        });

        Schema::create($this->prefix.'navigation_links', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('navigation_id')->unsigned()->index();
            $table->string('title');
            $table->string('url')->nullable()->default(null);
            $table->string('route')->nullable()->default(null);
            $table->string('class')->nullable()->default(null);
            $table->boolean('blank')->default(false);
            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop($this->prefix.'navigation_links');
        Schema::drop($this->prefix.'navigation');
    }
}
