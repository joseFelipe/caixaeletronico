<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('balance');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
