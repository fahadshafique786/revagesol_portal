<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirebaseCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firebase_credentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id')->index();
            $table->unsignedBigInteger('app_detail_id')->index();
            $table->string('apps_url')->nullable();
            $table->string('leagues_url')->nullable();
            $table->string('schedules_url')->nullable();
            $table->string('servers_url')->nullable();
            $table->string('app_setting_url')->nullable();
            $table->string('reCaptchaKeyId')->nullable();
            $table->string('notificationKey')->nullable();
            $table->json('firebaseConfigJson')->nullable();
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
        Schema::dropIfExists('firebase_credentials');
    }
}
