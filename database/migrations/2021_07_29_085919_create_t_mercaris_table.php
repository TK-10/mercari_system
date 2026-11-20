<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTMercarisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mercaris', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mid');
            $table->string('item_name');
            $table->integer('item_price');
            $table->string('buyer_name');
            $table->integer('shorikubun');
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
        Schema::dropIfExists('mercari');
    }
}
