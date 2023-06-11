<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\LeaguesController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServersController;
use App\Http\Controllers\ServerTypeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\AppDetailsController;
use App\Http\Controllers\SponsorsController;
use App\Http\Controllers\AdmobAdsController;

use \Illuminate\Support\Facades\Auth;

use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\AppCredentialsController;
use App\Http\Controllers\AppSettingsController;
use App\Http\Controllers\Firebase\DatabaseCredentials;
use App\Http\Controllers\Firebase\Synchronization;

use App\Http\Controllers\Firebase\PushNotificationsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


 Route::get('/', function () {
     if(!empty(Auth::user())){
        return redirect()->route('dashboard');
     }
     else{
         return redirect()->route('login');
     }
 });


// Route::get('/admin/', function () {
    // if(!empty(Auth::user())){
        // return redirect()->route('dashboard');
    // }
    // else{
        // return redirect()->route('login');
    // }
// });


#Remember me functionality in Laravel
Route::get('/user-register',[CustomAuthController::class,'registerform'])->name('user.register');
Route::post('/post-registration',[CustomAuthController::class,'postRegistration'])->name('post.register');

Route::get('/login',[CustomAuthController::class,'loginform'])->name('login');
Route::post('/verify-login',[CustomAuthController::class,'checklogin'])->name('post.login');

Route::post('logout', [CustomAuthController::class, 'logout'])->name('logout');

Route::group(
    [
        'middleware' => ['auth'],
        'prefix' => 'admin/',
    ],
    function() {

        /******* User Module ***********/

        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/fetchusersdata', [UserController::class, 'fetchusersdata']);
        Route::post('/add-update-user', [UserController::class, 'store']);
        Route::post('/edit-user', [UserController::class, 'edit']);
        Route::post('/delete-user', [UserController::class, 'destroy']);
        Route::post('/edit-profile', [UserController::class, 'editProfile']);
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
        Route::post('/update-password', [UserController::class, 'changePassword']);
        Route::post('/get-schedule-list', [HomeController::class, 'getSchedulesList']);

        /******* Accounts Module ***********/

        Route::get('/accounts', [AccountsController::class, 'index']);
        Route::get('/fetchaccountsdata', [AccountsController::class, 'fetchaccountsdata']);
        Route::post('/add-update-Account', [AccountsController::class, 'store']);
        Route::post('/edit-Account', [AccountsController::class, 'edit']);
        Route::post('/delete-account', [AccountsController::class, 'destroy']);
        Route::delete('/accountsDeleteAll', [AccountsController::class, 'deleteAll']);

        /******* RBAC Management ***********/

        Route::get('/roles', [RolesController::class, 'index']);;
        Route::get('/fetchrolesdata', [RolesController::class, 'fetchRolesdata']);
        Route::post('/add-update-role', [RolesController::class, 'store']);
        Route::post('/edit-role', [RolesController::class, 'edit']);
        Route::post('/update-role', [RolesController::class, 'updateRole']);
        Route::post('/delete-role', [RolesController::class, 'destroy']);
        Route::get('/permissions', [PermissionsController::class, 'index']);
        Route::get('/fetchpermissionsdata', [PermissionsController::class, 'fetchpermissionsdata']);
        Route::post('/edit-permission', [PermissionsController::class, 'edit']);
        Route::post('/update-permission', [PermissionsController::class, 'store']);
        Route::post('/delete-permission', [PermissionsController::class, 'destroy']);


        /******* LeaguesApi Module ***********/
        Route::get('/leagues', [LeaguesController::class, 'index']);
        Route::post('/fetch-leagues-data', [LeaguesController::class, 'fetchleaguesdata']);
        Route::post('/add-update-leagues', [LeaguesController::class, 'store']);
        Route::post('/edit-league', [LeaguesController::class, 'edit']);
        Route::post('/delete-league', [LeaguesController::class, 'destroy']);
        Route::post('/leagueslistbysport', [LeaguesController::class, 'getLeaguesOptionByAccounts']);
        Route::delete('/leaguesDeleteAll', [LeaguesController::class, 'deleteAll']);

        /******* Teams Module ***********/
        Route::get('/teams/{account_id}', [TeamsController::class, 'index']);
        Route::post('/fetch-teams-data/{account_id}', [TeamsController::class, 'fetchteamsdata']);
        Route::post('/add-update-teams/{account_id}', [TeamsController::class, 'store']);
        Route::post('/delete-team', [TeamsController::class, 'destroy']);
        Route::post('/edit-team', [TeamsController::class, 'edit']);
        Route::post('/getTeamsByLeagueId', [TeamsController::class, 'getTeamsByLeagueId']);
        Route::delete('/teamsDeleteAll', [TeamsController::class, 'deleteAll']);


        /******* Schedules Module ***********/
        Route::get('/schedules/{account_id}', [ScheduleController::class, 'index']);
        Route::post('/fetch-schedules-data/{account_id}', [ScheduleController::class, 'fetchschedulesdata']);
        Route::post('/add-update-schedules/{account_id}', [ScheduleController::class, 'store']);
        Route::post('/delete-schedule', [ScheduleController::class, 'destroy']);
        Route::post('/edit-schedule', [ScheduleController::class, 'edit']);
        Route::post('/update-schedule-live-status', [ScheduleController::class, 'updateScheduleLiveStatus']);
        Route::post('/change-league-status', [LeaguesController::class, 'updateLeagueStatus']);
        Route::delete('/schedulesDeleteAll', [ScheduleController::class, 'deleteAll']);
        Route::post('/schedules-apps-card-view', [ScheduleController::class, 'getSchedulesAppByScheduleId']);

        /******* Servers Module ***********/
        Route::get('/servers', [ServersController::class, 'index']);
        Route::post('/fetch-servers-data', [ServersController::class, 'fetchserversdata']);
        Route::post('/add-update-servers', [ServersController::class, 'store']);
        Route::post('/delete-server', [ServersController::class, 'destroy']);
        Route::post('/edit-server', [ServersController::class, 'edit']);
        Route::post('/getServersList', [ServersController::class, 'getServersList']);
        Route::delete('/serversDeleteAll', [ServersController::class, 'deleteAll']);

        /******* Schedule Servers  ***********/
        Route::get('/servers/{schedule_id}', [ServersController::class, 'fetchScheduleServersView']);;
        Route::get('/fetch-servers-data/{schedule_id}', [ServersController::class, 'fetchserversdata']);
        Route::post('/add-update-servers/{schedule_id}', [ServersController::class, 'store']);
        Route::post('/attach-servers/{schedule_id}', [ServersController::class, 'attachServers']);
        Route::post('/delete-server/{schedule_id}', [ServersController::class, 'destroy']);
        Route::delete('/scheduleServersDeleteAll', [ServersController::class, 'deleteAll1']);


        /******* Application Module  ***********/
        Route::get('/app', [AppDetailsController::class, 'index']);;
        Route::get('/app/create', [AppDetailsController::class, 'create'])->name('app.create');
        Route::get('/app/{app_id}', [AppDetailsController::class, 'edit'])->name('app.edit');
        Route::post('/add-update-apps', [AppDetailsController::class, 'store']);
        Route::post('/add-update-apps/{app_id}', [AppDetailsController::class, 'store']);
        Route::post('/delete-app', [AppDetailsController::class, 'destroy']);
        Route::delete('/app/remove/all', [AppDetailsController::class, 'removeAllAppDetails']);
        Route::post('/apps-card-view', [AppDetailsController::class, 'getApplicationCardView']);
        Route::post('/apps-list-options', [AppDetailsController::class, 'getApplicationListOptions']);
        Route::post('/apps-list-options-no-permissions', [AppDetailsController::class, 'getApplicationListOptionsNoPermission']);
        Route::post('/apps-list-options/all', [AppDetailsController::class, 'getAppsListWithAllOption']);
        Route::post('/apps-list-options-no-permissions/all', [AppDetailsController::class, 'getAppsListWithAllOptionNoPermissions']);
        Route::post('/roles/accounts/apps-options', [AppDetailsController::class, 'getRolesAppsListByAccounts']);
        Route::post('/remaining-apps-options', [AppDetailsController::class, 'getRemainingAppsForAppSettingOptions']);
        Route::get('/pagination/applications/fetch_data', [AppDetailsController::class, 'fetchData']);
        Route::post('/app-detail/proxy/change-status', [AppDetailsController::class, 'updateProxyStatus']);


        /******* Sponsor Ads Module  ***********/
        Route::get('/sponsors', [SponsorsController::class, 'index']);;
        Route::post('/add-update-sponsorads', [SponsorsController::class, 'store']);
        Route::post('/fetch-sponsor-data/', [SponsorsController::class, 'fetchSponsorAdsList']);
        Route::post('/edit-sponsor-ads', [SponsorsController::class, 'edit']);
        Route::post('/delete-sponsor-ads', [SponsorsController::class, 'destroy']);
        Route::delete('/sponsorsDeleteAll', [SponsorsController::class, 'deleteAll']);


        /******* Admob Ads Module  ***********/
        Route::get('/admob_ads', [AdmobAdsController::class, 'index']);;
        Route::post('/add-update-admob_ads', [AdmobAdsController::class, 'store']);
        Route::post('/fetch-admob_ads-data/', [AdmobAdsController::class, 'fetchAdmobAdsList']);
        Route::post('/edit-admob-ads', [AdmobAdsController::class, 'edit']);
        Route::post('/delete-admob-ads', [AdmobAdsController::class, 'destroy']);
        Route::delete('/admobsDeleteAll', [AdmobAdsController::class, 'deleteAll']);

        /******* App Settings Module  ***********/
        Route::get('/app_settings', [AppSettingsController::class, 'index']);;
        Route::get('/app_settings/create', [AppSettingsController::class, 'create'])->name('app_setting.create');
        Route::get('/app_settings/{app_setting_id}', [AppSettingsController::class, 'create'])->name('app_setting.edit');
        Route::post('/add-update-app_settings', [AppSettingsController::class, 'store']);
        Route::post('/add-update-app_settings/{app_setting_id}', [AppSettingsController::class, 'store']);
        Route::post('/delete-app_settings', [AppSettingsController::class, 'destroy']);
        Route::post('/app-settings-card-view', [AppSettingsController::class, 'getApplicationSettingsCardView']);
        Route::get('/pagination/fetch_data', [AppSettingsController::class, 'fetchData']);
        Route::post('/app-settings/update-database-version', [AppSettingsController::class, 'updateDatabaseVersion']);
        Route::delete('/app-setting/remove/all', [AppSettingsController::class, 'removeAllAppSettings']);


        /******* API Credentials Module  ***********/
        Route::get('/credentials', [AppCredentialsController::class, 'index']);;
        Route::post('/add-update-credentials', [AppCredentialsController::class, 'store']);
        Route::post('/fetch-credentials-data/', [AppCredentialsController::class, 'fetchAppCredentialsList']);
        Route::post('/edit-credentials', [AppCredentialsController::class, 'edit']);
        Route::post('/delete-credentials', [AppCredentialsController::class, 'destroy']);
        Route::post('/get-applist-options', [AppCredentialsController::class, 'getAppsOptions']);
        Route::delete('/credentialsDeleteAll', [AppCredentialsController::class, 'deleteAll']);

        /******* Firebase Credentials Module  ***********/

        Route::get('/firebase-credentials', [DatabaseCredentials::class, 'index']);;
        Route::post('/fetch-firebase-credentials/', [DatabaseCredentials::class, 'getDatabaseCredentialsList']);
        Route::post('/firebase/get-applist-options', [DatabaseCredentials::class, 'getAppsOptions']);
        Route::post('/firebase/add-update-credentials', [DatabaseCredentials::class, 'store']);
        Route::post('/firebase/edit-credentials', [DatabaseCredentials::class, 'edit']);
        Route::post('/firebase/delete-credentials', [DatabaseCredentials::class, 'destroy']);
        Route::delete('/firebase/credentialsDeleteAll', [DatabaseCredentials::class, 'deleteAll']);


        Route::get('/sync-data', [Synchronization::class, 'index']);
        Route::post('/firebase/push-data', [Synchronization::class, 'syncDataToFirebase']);
        Route::post('/sync/app-list-options', [Synchronization::class, 'getApplicationListOptions']);

        /******* Server Type Module ***********/

        Route::get('/server-types', [ServerTypeController::class, 'index']);
        Route::get('/fetch-server-types-data', [ServerTypeController::class, 'fetchServerTypesData']);
        Route::post('/add-update-server-type', [ServerTypeController::class, 'store']);
        Route::post('/delete-server-type', [ServerTypeController::class, 'destroy']);
        Route::post('/edit-server-type', [ServerTypeController::class, 'edit']);
        Route::delete('/server-types/remove-all', [ServerTypeController::class, 'deleteAll']);


        /******* Country  Module ***********/

        Route::get('/country', [CountryController::class, 'index']);
        Route::get('/fetch-country-data', [CountryController::class, 'fetchCountryData']);
        Route::post('/fetch-blocked-apps', [CountryController::class, 'fetchBlockedAppsList']);
        Route::post('/add-update-blocked-applications', [CountryController::class, 'storeBlockedApplications']);
        Route::get('/block-applications', [CountryController::class, 'showBlockedAppsView']);
        Route::post('/edit-blocked-application', [CountryController::class, 'edit']);
        Route::post('/delete-blocked-application', [CountryController::class, 'destroy']);
        Route::post('/remaining-block-apps-option', [CountryController::class, 'getRemainingAppsForBlockedCountriesOptions']);
        Route::delete('/remove/all-block-apps', [CountryController::class, 'deleteAll']);


        /******* Firebase Push Notifications Module ***********/

        Route::get('/firebase/notifications', [PushNotificationsController::class, 'index']);
        Route::post('/firebase/notifications/data', [PushNotificationsController::class, 'getNotificationsList']);
        Route::post('/firebase/notifications/save', [PushNotificationsController::class, 'saveNotifications']);
        Route::post('/firebase/notifications/show', [PushNotificationsController::class, 'showNotificationDetails']);
        Route::post('/firebase/notifications/remove', [PushNotificationsController::class, 'removeNotifications']);
        Route::delete('/firebase/notifications/remove-all', [PushNotificationsController::class, 'removeAllNotifications']);


        Route::post('/sync/app-keys', [Synchronization::class, 'syncAppKeys']);
        Route::post('/sync/app-credentials', [Synchronization::class, 'syncAppCredentials']);
        Route::post('/sync/app-details', [Synchronization::class, 'syncAppDetails']);
    });
