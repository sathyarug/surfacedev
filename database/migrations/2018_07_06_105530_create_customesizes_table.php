<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomesizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customesizes', function (Blueprint $table) {
            $table->increments('size_id');
            $table->integer('size_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('division_id')->nullable();
            $table->string('size_name')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customesizes');
    }
}
