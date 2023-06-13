<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_credentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->unsignedBigInteger('app_detail_id')->index();
            $table->string('package_id')->nullable();
            $table->string('server_auth_key')->index()->nullable();
            $table->string('appSigningKey')->index()->nullable();
            $table->integer('versionCode')->default(0);
            $table->string('stream_key')->index()->nullable();
            $table->text('token_key')->index()->nullable();
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
        Schema::dropIfExists('app_credentials');
    }
}
