<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsor_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_detail_id')->index();
            $table->string('adName')->nullable();
            $table->string('adUrlImage')->nullable();
            $table->string('clickAdToGo')->nullable();
            $table->enum('isAdShow',['0','1'])->default('0');
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
        Schema::dropIfExists('sponsor_ads');
    }
}
