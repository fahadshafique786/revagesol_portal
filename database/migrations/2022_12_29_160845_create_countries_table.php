<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{

    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_code',4)->nullable();
            $table->string('country_name',100)->nullable();
            $table->enum('status',['0','1'])->default('1');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
