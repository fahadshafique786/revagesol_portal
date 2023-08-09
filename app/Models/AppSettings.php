<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'app_detail_id',
        'appCacheId',
        'appDetailsDatabaseVersion',
        'appSharedPrefId',
        'serverApiBaseUrl',
        'authHelperKey',
        'isAppClearCache',
        'isAppClearSharedPref',
        'isAppDetailsDatabaseClear',
        'isAppDetailsDatabaseSave',
        'isFirebaseDatabaseAccess',
        'isServerLocalAuthKeyUsed',
        'isSuspendApp',
        'suspendAppMessage',
        'minimumVersionSupport',
        'serverAuthKey1',
        'serverAuthKey2',
        'appDetailsDatabaseClearVersion',
        'isMessageDialogDismiss',
        'sslSha256Key',
        'checkIpAddressApiUrl',
        'isIpAddressApiCall',
        'isAppSigningKeyUsed',
    ];
}
