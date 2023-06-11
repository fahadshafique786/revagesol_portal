<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->index();
            $table->string('packageId')->index()->nullable();
            $table->string('appName')->nullable();
            $table->string('appLogo')->nullable();
            $table->string('admobAppId')->nullable();
            $table->string('adsIntervalTime')->nullable();
            $table->integer('adsIntervalCount')->default(1);
            $table->string('checkIpAddressApiUrl')->nullable();
            $table->enum('isAdmobAdsShow',['0','1'])->default('0');
            $table->enum('isAdmobOnline',['0','1'])->default('0');
            $table->enum('isAdsInterval',['0','1'])->default('0');
            $table->enum('isBannerPlayer',['0','1'])->default('0');
            $table->enum('isIpAddressApiCall',['0','1'])->default('0');
            $table->enum('isMessageDialogDismiss',['0','1'])->default('0');
            $table->enum('isSponsorAdsShow',['0','1'])->default('0');
            $table->enum('isStartAppAdsShow',['0','1'])->default('0');
            $table->enum('isStartAppOnline',['0','1'])->default('0');
            $table->enum('isScreenAdsLimit',['0','1'])->default('0');
            $table->integer('appOpenIntervalHour')->default(3);
            $table->integer('minimumVersionSupport')->nullable();
            $table->string('startAppId')->default('0000');
            $table->string('newAppPackage')->nullable();
            $table->string('ourAppPackage')->nullable();
            $table->enum('isSuspendApp',['0','1'])->default('0');
            $table->string('suspendAppMessage')->nullable();
            $table->enum('isProxyEnable',['0','1'])->default('0');
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
        Schema::dropIfExists('app_details');
    }
}
