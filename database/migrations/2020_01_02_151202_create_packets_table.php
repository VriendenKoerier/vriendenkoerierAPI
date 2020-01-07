<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('deliverer_id')->unsigned()->nullable();
            $table->foreign('deliverer_id')->references('id')->on('users');
            $table->string('title', 50);
            $table->text('description');
            $table->integer('height')->unsigned();
            $table->integer('width')->unsigned();
            $table->integer('length')->unsigned();
            $table->integer('weight')->unsigned();
            $table->string('photo', 255);
            $table->string('contact', 255);
            $table->string('postcode_a', 7);
            $table->string('postcode_b', 7);
            $table->string('adres_a', 65);
            $table->string('aders_b', 65);
            $table->boolean('avg_confirmed');
            $table->string('show_hash')->nullable();
            $table->string('deny_hash')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packets');
    }
}
