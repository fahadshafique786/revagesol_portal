<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppDetails extends Model
{
    use HasFactory;

//    protected $casts = [
//        'id' => 'string',
//        'account_id' => 'string',
//    ];

    protected $fillable = [

        'account_id',
        'packageId',
        'appName',
        'appLogo',
        'admobAppId',
        'adsIntervalTime',
        'adsIntervalCount',
        'checkIpAddressApiUrl',
        'isAdmobAdsShow',
        'isAdmobOnline',
        'isAdsInterval',
        'isBannerPlayer',
        'isIpAddressApiCall',
        'isMessageDialogDismiss',
        'isSponsorAdsShow',
        'isStartAppAdsShow',
        'isStartAppOnline',
        'isScreenAdsLimit',
        'appOpenIntervalHour',
        'minimumVersionSupport',
        'startAppId',
        'newAppPackage',
        'ourAppPackage',
        'isSuspendApp',
        'suspendAppMessage',
        'isProxyEnable'

    ];



}
