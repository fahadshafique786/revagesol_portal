<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sports_id')->index();
            $table->bigInteger('leagues_id')->nullable();
            $table->string('name')->nullable();
            $table->longText('link')->nullable();
            $table->enum('isHeader',['0','1'])->default('0');
            $table->enum('isPremium',['0','1'])->default('0');
            $table->enum('isTokenAdded',['0','1'])->default('0');
            $table->enum('isSponsorAd',['0','1'])->default('0');
            $table->enum('isIpAddressApiCall',['0','1'])->default('0');
            $table->longText('sponsorAdClickUrl')->nullable();
            $table->longText('sponsorAdImageUrl')->nullable();
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
        Schema::dropIfExists('servers');
    }
}
