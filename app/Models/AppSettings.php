<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_detail_id',
        'appAuthKey1',
        'appAuthKey2',
        'appCacheId',
        'appDetailsDatabaseVersion',
        'appSharedPrefId',
        'leaguesDatabaseVersion',
        'schedulesDatabaseVersion',
        'serversDatabaseVersion',
        'serverApiBaseUrl',
        'streamKey',
        'isAppClearCache',
        'isAppClearSharedPref',
        'isAppDetailsDatabaseClear',
        'isAppDetailsDatabaseSave',
        'isFirebaseDatabaseAccess',
        'isAppAuthKeysUsed',
        'isServerLocalAuthKeyUsed',
        'isSuspendApp',
        'suspendAppMessage',
        'minimumVersionSupport',
        'serverAuthKey1',
        'serverAuthKey2',
        'appDetailsDatabaseClearVersion',
        'isMessageDialogDismiss',
        'isServerTokenFetch',
        'sslSha256Key',
        'checkIpAddressApiUrl',
        'isIpAddressApiCall',
        'isAppSigningKeyUsed',
    ];
}
