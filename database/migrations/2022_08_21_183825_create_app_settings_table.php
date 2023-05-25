<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_detail_id')->index();
            $table->text('appAuthKey1')->nullable();
            $table->text('appAuthKey2')->nullable();
            $table->decimal('appCacheId')->default(1.1);
            $table->decimal('appDetailsDatabaseVersion')->default(1.1);
            $table->decimal('appSharedPrefId')->default(1.1);
            $table->decimal('leaguesDatabaseVersion')->default(1.1);
            $table->decimal('schedulesDatabaseVersion')->default(1.1);
            $table->decimal('serversDatabaseVersion')->default(1.1);
            $table->string('serverApiBaseUrl')->nullable();
            $table->string('streamKey')->nullable();
            $table->enum('isAppClearCache',['0','1'])->default('0');
            $table->enum('isAppClearSharedPref',['0','1'])->default('0');
            $table->enum('isAppDetailsDatabaseClear',['0','1'])->default('0');
            $table->enum('isAppDetailsDatabaseSave',['0','1'])->default('0');
            $table->enum('isFirebaseDatabaseAccess',['0','1'])->default('0');
            $table->enum('isAppAuthKeysUsed',['0','1'])->default('0');
            $table->enum('isServerLocalAuthKeyUsed',['0','1'])->default('0');
            $table->enum('isSuspendApp',['0','1'])->default('0');
            $table->string('suspendAppMessage')->nullable();
            $table->integer('minimumVersionSupport')->nullable();
            $table->text('serverAuthKey1')->nullable();
            $table->text('serverAuthKey2')->nullable();
            $table->decimal('appDetailsDatabaseClearVersion')->default(1.1);
            $table->decimal('isMessageDialogDismiss')->default(1.1);
            $table->enum('isServerTokenFetch',['0','1'])->default('0');
            $table->text('sslSha256Key')->nullable();
            $table->string('checkIpAddressApiUrl')->nullable();
            $table->enum('isIpAddressApiCall',['0','1'])->default('0');
            $table->enum('isAppSigningKeyUsed',['0','1'])->default('0');
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
        Schema::dropIfExists('app_settings');
    }
}
