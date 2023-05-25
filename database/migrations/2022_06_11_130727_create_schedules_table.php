<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('scheduleName')->nullable();
            $table->bigInteger('sports_id')->index();
            $table->bigInteger('leagues_id')->index()->nullable();
            $table->bigInteger('home_team_id')->index();
            $table->bigInteger('away_team_id')->index();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('is_live',['yes','no','0','1'])->default('no');
            $table->enum('isSponsorAd',['0','1'])->default('0');
            $table->string('sponsorAdClickUrl')->nullable();
            $table->string('sponsorAdImageUrl')->nullable();
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
        Schema::dropIfExists('schedules');
    }
}
