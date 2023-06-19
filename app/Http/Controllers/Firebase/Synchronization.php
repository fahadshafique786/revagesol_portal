<?php

namespace App\Http\Controllers\Firebase;

use App\Models\AdmobAds;
use App\Models\AppCredentials;
use App\Models\AppSettings;
use App\Models\Schedules;
use App\Models\SponsorAds;
use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\AppDetails;
use App\Models\Leagues;
use App\Models\Servers;
use App\Models\FirebaseCredentials;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Response;
use function PHPUnit\Framework\isNull;

class Synchronization extends BaseController
{

    public $imageUrl;
    public $sponsorImageUrl;
    public $leaguesImageUrl;
    public $teamsImageUrl;
    public $schedulesimageUrl;
    public $serversImageUrl;
    protected $roleAssignedAccounts;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-manage-sync_accounts_data', ['only' => ['syncDataToFirebase']]);
        $this->middleware('role_or_permission:super-admin|view-manage-sync_apps_data', ['only' => ['syncAppKeys','syncAppCredentials','syncAppDetails']]);

        $this->imageUrl = config('app.appsImagePath');
        $this->sponsorImageUrl = config('app.sponsoradsImagePath');
        $this->leaguesImageUrl = config('app.leaguesImagePath');
        $this->schedulesimageUrl = config('app.schedulesImagePath');
        $this->teamsImageUrl = config('app.teamsImagePath');
        $this->serversImageUrl = config('app.serversImagePath');

    }

    public function index()
    {
        $appsList = AppDetails::all();
        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        $assignedAppsList = [];
        if(!empty($this->roleAssignedApplications)){
            $assignedAppsList = AppDetails::whereIn('id',$this->roleAssignedApplications)->get();
        }


        $accountsList = Accounts::orderBy('id','DESC')->get();

        return view('firebase.synchronization')
            ->with('accountsList',$accountsList)
            ->with('appsList',$appsList)
            ->with('assignedAppsList',$assignedAppsList);
    }

    public function syncDataToFirebase(Request $request)
    {
        $syncType = $request->synchronization_type;

        $firebaseStatus = "";
        $status = "";
        $message = "";

        $errors = [];  $response = []; $jsonData = [];

        switch ($syncType) {

            case "leagues_url":

                $accountsId = $request->account_id;
                $accountsDetail = getAccountDetailsById($accountsId);

                $firebaseURL = "";
                if(count($request->app_detail_ids) > 0) {
                    foreach ($request->app_detail_ids as $appDetailId) {
                        $listOfApplications = getAppListByAccountsId($request->account_id, $appDetailId);


                        if (!empty($listOfApplications)) {
                            foreach ($listOfApplications as $obj) {

                                $jsonData = $this->generateLeaguesJson($accountsId, $obj->application_id);

                                if (!empty($jsonData)) {

                                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id', $obj->application_id)->select('leagues_url', 'reCaptchaKeyId', 'firebaseConfigJson')->first();

                                    if (isset($firebaseCredentials->leagues_url)) {

                                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                                        $node = "Leagues";

                                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);

                                        if (!empty($parseFirebaseConfigJson)) {
                                            $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->leagues_url;
                                            $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

                                            $response[] = [
                                                'app_detail' => $obj->appName . ' - ' . $obj->packageId,
                                                'firebase_status' => "success",
                                                'message' => "Leagues Synced successfully!",
                                                'firebaseData' => $jsonData,
                                                'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                                                'firebaseConfigJson' => $firebaseConfigJson,
                                                'node' => $node,
                                                'appPackageId' => $obj->packageId . rand(111, 222) . time()
                                            ];
                                        } else {
                                            $errors[] = ['app_detail' => $obj->appName . ' - ' . $obj->packageId, 'message' => 'Firebase Config JSON is missing!'];
                                        }
                                    } else {
                                        $errors[] = ['app_detail' => $obj->appName . ' - ' . $obj->packageId, 'message' => 'Firebase Credentials not found!'];
                                    }

                                } else {
                                    // data not found
                                    $errors[] = ['app_detail' => $obj->appName . ' - ' . $obj->packageId, 'message' => 'Data not found!'];
                                }
                            }
                        } else {
                            // application not found!
                            $errors[] = ['app_detail' => $accountsDetail->name, 'message' => 'Application not found!'];
                        }
                    }
                }

                break;

            case "apps_url":

                $accountsId = $request->account_id;
                $accountsDetail = getAccountDetailsById($accountsId);

                $firebaseURL = "";
                if(count($request->app_detail_ids) > 0){
                    foreach($request->app_detail_ids as $appDetailId) {
                        $listOfApplications = getAppListByAccountsId($request->account_id, $appDetailId);

                        if(!empty($listOfApplications)){
                            foreach($listOfApplications as $obj) {

                                $jsonData = $this->generateAppDetailJson($obj->application_id);

                                if(!empty($jsonData)) {

                                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$obj->application_id)->select('apps_url','reCaptchaKeyId','firebaseConfigJson')->first();

                                    if(isset($firebaseCredentials->apps_url)) {

                                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                                        $node = "AppDetails";

                                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);

                                        if(!empty($parseFirebaseConfigJson)){
                                            $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->apps_url;
                                            $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

                                            $response[] = [
                                                'app_detail' => $obj->appName . ' - ' . $obj->packageId,
                                                'firebase_status' => "success",
                                                'message' => "App Details Synced Successfully!",
                                                'firebaseData' => $jsonData,
                                                'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                                                'firebaseConfigJson' => $firebaseConfigJson,
                                                'node' => $node,
                                                'appPackageId' => $obj->packageId . rand(333,444) . time()
                                            ];
                                        }
                                        else{
                                            $errors[] = ['app_detail' =>  $obj->appName . ' - ' . $obj->packageId , 'message' => 'Firebase Config JSON is missing!' ];
                                        }
                                    }
                                    else{
                                        $errors[] = ['app_detail' =>  $obj->appName . ' - ' . $obj->packageId , 'message' => 'Firebase Credentials not found!' ];
                                    }

                                }
                                else{
                                    // data not found
                                    $errors[] = ['app_detail' => $obj->appName . ' - ' . $obj->packageId , 'message' => 'Data not found!' ];
                                }
                            }
                        }
                        else{
                            // application not found!
                            $errors[] = ['app_detail' => $accountsDetail->name , 'message' => 'Application not found!' ];
                        }

                    }
                }

                break;


            case "schedules_url":

                $accountsId = $request->account_id;
                $accountsDetail = getAccountDetailsById($accountsId);

                $firebaseURL = "";

                if(count($request->app_detail_ids) > 0) {
                    foreach ($request->app_detail_ids as $appDetailId) {
                        $listOfApplications = getAppListByAccountsId($request->account_id, $appDetailId);

                        if(!empty($listOfApplications)){
                            foreach($listOfApplications as $obj) {

                                $jsonData = $this->generateSchedulesJson($accountsId,$obj->application_id);

                                if(!empty($jsonData)) {

                                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$obj->application_id)->select('schedules_url','reCaptchaKeyId','firebaseConfigJson')->first();

                                    if(isset($firebaseCredentials->schedules_url)) {

                                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                                        $node = "Schedules";

                                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);

                                        if(!empty($parseFirebaseConfigJson)){
                                            $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->schedules_url;
                                            $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

                                            $response[] = [
                                                'app_detail' => $obj->appName . ' - ' . $obj->packageId,
                                                'firebase_status' => "success",
                                                'message' => "Schedules Synced Successfully!",
                                                'firebaseData' => $jsonData,
                                                'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                                                'firebaseConfigJson' => $firebaseConfigJson,
                                                'node' => $node,
                                                'appPackageId' => $obj->packageId . rand(333,444) . time()
                                            ];
                                        }
                                        else{
                                            $errors[] = ['app_detail' =>  $obj->appName . ' - ' . $obj->packageId , 'message' => 'Firebase Config JSON is missing!' ];
                                        }
                                    }
                                    else{
                                        $errors[] = ['app_detail' =>  $obj->appName . ' - ' . $obj->packageId , 'message' => 'Firebase Credentials not found!' ];
                                    }

                                }
                                else{
                                    // data not found
                                    $errors[] = ['app_detail' => $obj->appName . ' - ' . $obj->packageId , 'message' => 'Data not found!' ];
                                }
                            }
                        }
                        else{
                            // application not found!
                            $errors[] = ['app_detail' => $accountsDetail->name , 'message' => 'Application not found!' ];
                        }
                    }
                }

                break;

            case "servers_url":

                $accountsId = $request->account_id;
                $accountsDetail = getAccountDetailsById($accountsId);

                $firebaseURL = "";
                if(count($request->app_detail_ids) > 0) {
                    foreach ($request->app_detail_ids as $appDetailId) {
                        $listOfApplications = getAppListByAccountsId($request->account_id, $appDetailId);

                        if (!empty($listOfApplications)) {
                            foreach ($listOfApplications as $obj) {

                                $jsonData = $this->generateServersJson($accountsId);

                                if (!empty($jsonData)) {

                                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id', $obj->application_id)->select('servers_url', 'reCaptchaKeyId', 'firebaseConfigJson')->first();

                                    if (isset($firebaseCredentials->servers_url)) {

                                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                                        $node = "Servers";

                                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);

                                        if (!empty($parseFirebaseConfigJson)) {
                                            $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->servers_url;
                                            $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

                                            $response[] = [
                                                'app_detail' => $obj->appName . ' - ' . $obj->packageId,
                                                'firebase_status' => "success",
                                                'message' => "Server Synced!",
                                                'firebaseData' => $jsonData,
                                                'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                                                'firebaseConfigJson' => $firebaseConfigJson,
                                                'node' => $node,
                                                'appPackageId' => $obj->packageId . rand(333, 444) . time()
                                            ];
                                        } else {
                                            $errors[] = ['app_detail' => $obj->appName . ' - ' . $obj->packageId, 'message' => 'Firebase Config JSON is missing!'];
                                        }
                                    } else {
                                        $errors[] = ['app_detail' => $obj->appName . ' - ' . $obj->packageId, 'message' => 'Firebase Credentials not found!'];
                                    }

                                } else {
                                    // data not found
                                    $errors[] = ['app_detail' => $obj->appName . ' - ' . $obj->packageId, 'message' => 'Data not found!'];
                                }
                            }
                        }
                        else {
                            // application not found!
                            $errors[] = ['app_detail' => $accountsDetail->name, 'message' => 'Application not found!'];
                        }
                    }
                }

                break;



            default:
                return response()->json(['error' => 'Synchronization Failed!'],422);
        }

        return response()->json([
            'data' => [
                'success' => $response,
                'failed' => $errors,
            ]
        ]);


    }

    public function syncDataToFirebaseOldVersion(Request $request)
    {
        $syncType = $request->synchronization_type;
        $appId = $request->app_detail_id;

        $firebaseStatus = "";
        $status = "";
        $message = "";

        switch ($syncType) {

            case "apps_url":

                $jsonData = $this->generateAppDetailJson($appId);
                $firebaseURL = "";

                if(!empty($jsonData)){

                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$appId)->select('apps_url','reCaptchaKeyId','firebaseConfigJson')->first();
                    if(isset($firebaseCredentials->apps_url)){

                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                        $node = "AppDetails";

                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);

//                        dd($parseFirebaseConfigJson,isNull($parseFirebaseConfigJson),!empty($parseFirebaseConfigJson));
                        if(empty($parseFirebaseConfigJson)){
                            return response()->json(['error' => 'Firebase Configuration Parameters not found for this Application!'],422);
                        }
                        $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->apps_url;

                        $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

//                        dd(json_decode($firebaseConfigJson));

/*                        $NODE = "AppDetails.json";
                        $RequestMethod = "PUT";
                        $firebaseURL =  $firebaseCredentials->apps_url;

                        $firebaseResponse = $this->pushJsonToFirebaseDatabase($jsonData,$firebaseURL,$NODE,$RequestMethod);
*/

/*                        if($firebaseResponse['status']){
                            return response()->json(['success' => 'Data has been pushed successfully!']);
                        }
                        else{
                            return response()->json(['error' => 'Synchronization Failed due to'. $firebaseResponse['message'].'!'],422);
                        }*/

                        return response()->json(['status' => $status,
                            'firebase_status' => $firebaseStatus,
                            'message' => $message,
                            'firebaseData' => $jsonData,
                            'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                            'firebaseConfigJson' => $firebaseConfigJson,
                            'node' => $node
                        ]);


                    }
                    else{
                        return response()->json(['error' => 'Firebase Credentials not found for this Application!'],422);
                    }
                }
                else{
                    return response()->json(['error' => 'Data not found!'],422);
                }

                break;

            case "leagues_url":

                $accountsId = $request->account_id;
                $jsonData = $this->generateLeaguesJson($accountsId);
                $firebaseURL = "";

                if(!empty($jsonData)){

                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$appId)->select('leagues_url','reCaptchaKeyId','firebaseConfigJson')->first();
                    if(isset($firebaseCredentials->leagues_url)){

                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                        $node = "Leagues";

                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);
                        $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->leagues_url;

                        $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

                        return response()->json(['status' => $status,
                            'firebase_status' => $firebaseStatus,
                            'message' => $message,
                            'firebaseData' => $jsonData,
                            'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                            'firebaseConfigJson' => $firebaseConfigJson,
                            'node' => $node
                        ]);


/*                      $NODE = "Leagues.json";
                        $RequestMethod = "PUT";
                        $firebaseURL =  $firebaseCredentials->leagues_url;

                        $firebaseResponse = $this->pushJsonToFirebaseDatabase($jsonData,$firebaseURL,$NODE,$RequestMethod);

                        if($firebaseResponse['status']){
                            return response()->json(['success' => 'Data has been pushed successfully!']);
                        }
                        else{
                            return response()->json(['error' => 'Synchronization Failed due to'. $firebaseResponse['message'].'!'],422);
                        }
 */

                    }
                    else{
                        return response()->json(['error' => 'Firebase Credentials not found for this Application!'],422);
                    }
                }
                else{
                    return response()->json(['error' => 'Data not found!'],422);
                }

                break;

            case "schedules_url":

                $accountsId = $request->account_id;
                $jsonData = $this->generateSchedulesJson($accountsId);
                $firebaseURL = "";

                if(!empty($jsonData)){

                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$appId)->select('schedules_url','reCaptchaKeyId','firebaseConfigJson')->first();
                    if(isset($firebaseCredentials->schedules_url)){

                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                        $node = "Schedules";

                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);
                        $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->schedules_url;

                        $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

                        return response()->json(['status' => $status,
                            'firebase_status' => $firebaseStatus,
                            'message' => $message,
                            'firebaseData' => $jsonData,
                            'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                            'firebaseConfigJson' => $firebaseConfigJson,
                            'node' => $node
                        ]);

/*
                        $NODE = "Schedules.json";
                        $RequestMethod = "PUT";
                        $firebaseURL =  $firebaseCredentials->schedules_url;

                        $firebaseResponse = $this->pushJsonToFirebaseDatabase($jsonData,$firebaseURL,$NODE,$RequestMethod);

                        if($firebaseResponse['status']){
                            return response()->json(['success' => 'Data has been pushed successfully!']);
                        }
                        else{
                            return response()->json(['error' => 'Synchronization Failed due to'. $firebaseResponse['message'].'!'],422);
                        }
 */
                    }
                    else{
                        return response()->json(['error' => 'Firebase Credentials not found for this Application!'],422);
                    }
                }
                else{
                    return response()->json(['error' => 'Data not found!'],422);
                }

                break;

            case "servers_url":

                $accountsId = $request->account_id;
                $jsonData = $this->generateServersJson($accountsId);
                $firebaseURL = "";

                if(!empty($jsonData)){

                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$appId)->select('servers_url','reCaptchaKeyId','firebaseConfigJson')->first();
                    if(isset($firebaseCredentials->servers_url)){

                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                        $node = "Servers";

                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);
                        $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->servers_url;

                        $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

                        return response()->json(['status' => $status,
                            'firebase_status' => $firebaseStatus,
                            'message' => $message,
                            'firebaseData' => $jsonData,
                            'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                            'firebaseConfigJson' => $firebaseConfigJson,
                            'node' => $node
                        ]);


/*                        $NODE = ".json";
                        $RequestMethod = "PUT";
                        $firebaseURL =  $firebaseCredentials->servers_url;

                        $firebaseResponse = $this->pushJsonToFirebaseDatabase($jsonData,$firebaseURL,$NODE,$RequestMethod);

                        if($firebaseResponse['status']){
                            return response()->json(['success' => 'Data has been pushed successfully!']);
                        }
                        else{
                            return response()->json(['error' => 'Synchronization Failed due to'. $firebaseResponse['message'].'!'],422);
                        }
*/
                    }
                    else{
                        return response()->json(['error' => 'Firebase Credentials not found for this Application!'],422);
                    }
                }
                else{
                    return response()->json(['error' => 'Data not found!'],422);
                }

                break;
            default:
                return response()->json(['error' => 'Synchronization Failed!'],422);
        }

    }

    function generateAppDetailJson($appId){

        $dataObject = AppDetails::select()
            ->where('id',$appId)
            ->orderBy('id','asc');

        if($dataObject->exists()){
            $dataObject = $dataObject->get();


            $app_detail_id = $dataObject[0]->id;


            $adsListObject = AdmobAds::select(DB::raw('
				id AS admobAdId,
				app_detail_id AS appDetailId,
				adName,
				adUId,
				isAdShow
				'))
                ->where('app_detail_id',$app_detail_id)->get();


            $sponsorListObject = SponsorAds::select(DB::raw('
				id AS sponsorAdId,
				app_detail_id AS appDetailId,
				adName,
				adUrlImage,
				clickAdToGo,
				isAdShow
				'))
                ->where('app_detail_id',$app_detail_id)->get();


            foreach($adsListObject as $index => $arr){
                $arr->isAdShow = (int) $arr->isAdShow;
                $arr->isAdShow = getBoolean($arr->isAdShow);
            }

            $adsList = $adsListObject;

            foreach($sponsorListObject as $index => $arr1){

                $arr1->isAdShow = (int) $arr1->isAdShow;

                if($arr1->isAdShow){
                    $arr1->adUrlImage = $this->sponsorImageUrl.$arr1->adUrlImage;
                }
                else{
                    $arr1->adUrlImage = "";
                }

                $arr1->isAdShow = getBoolean($arr1->isAdShow);

            }

            $sponsorList = $sponsorListObject;

            $dataObject[0]->suspendAppMessage = (!empty($dataObject[0]->suspendAppMessage)) ? $dataObject[0]->suspendAppMessage : "";

            foreach($dataObject as $index => $obj){

                $obj->accountsId = $obj->account_id;

                unset($obj->account_id);
                unset($obj->created_at);
                unset($obj->updated_at);
                unset($obj->isProxyEnable);
                unset($obj->isIpAddressApiCall);

                $obj->adsIntervalTime = (int)($obj->adsIntervalTime);
                $obj->minimumVersionSupport = (int)($obj->minimumVersionSupport);

                $obj->isAdmobAdsShow = getBoolean($obj->isAdmobAdsShow);
                $obj->isAdmobOnline = getBoolean($obj->isAdmobOnline);
                $obj->isAdsInterval = getBoolean($obj->isAdsInterval);
                $obj->isBannerPlayer = getBoolean($obj->isBannerPlayer);

//              $obj->isIpAddressApiCall = getBoolean($obj->isIpAddressApiCall);

                $obj->isMessageDialogDismiss = getBoolean($obj->isMessageDialogDismiss);
                $obj->isSponsorAdsShow = getBoolean($obj->isSponsorAdsShow);
                $obj->isStartAppAdsShow = getBoolean($obj->isStartAppAdsShow);
                $obj->isStartAppOnline = getBoolean($obj->isStartAppOnline);
                $obj->isScreenAdsLimit = getBoolean($obj->isScreenAdsLimit);
                $obj->isSuspendApp = getBoolean($obj->isSuspendApp);
                $obj->appLogo = $this->imageUrl.$obj->appLogo;


            }


            $responseData = $dataObject[0];
            $responseData->admobAds = $adsList;
            $responseData->sponsorAds = $sponsorList;

            return json_encode($responseData);
        }
        else{
            return false;
        }

    }

    function generateLeaguesJson($accountsId,$applicationId){

        $data = Leagues::where('account_id',$accountsId)
            ->select(DB::raw('
                id as leagueId,name as leagueName,account_id as accountsId,IFNULL(CONCAT("'.$this->leaguesImageUrl.'","",icon),"") AS leagueIcon ,
                isSponsorAd , IFNULL(sponsorAdClickUrl,"") AS sponsorAdClickUrl,
                IFNULL(CONCAT("'.$this->leaguesImageUrl.'","",sponsorAdImageUrl),"") AS sponsorAdImageUrl'
            ));

        $leaguesArray = [];
        if($data->exists()){
            $data = $data->orderBy('start_datetime','ASC')->get();

            foreach($data as $index => $arr){

                $totalSchedules = 0;
                $totalSchedules = DB::table('schedules')
                    ->join('schedules_apps' , function ($join) {
                        $join->on('schedules_apps.schedule_id','=','schedules.id');
                    })
                    ->where('schedules_apps.application_id',$applicationId)
                    ->where('leagues_id',$arr->leagueId)
                    ->where('is_live','1')->count();

                if($totalSchedules > 0){
                    $arr->totalSchedules = $totalSchedules;
                    $arr->isSponsorAd = (int) $arr->isSponsorAd;
                    $arr->isSponsorAd = getBoolean($arr->isSponsorAd);
                    $leaguesArray[] = $arr;
                }
            }

            $response[$accountsId] = $leaguesArray;
            return json_encode($response);
        }
        else{
            return false;
        }

    }

    function generateSchedulesJson($accountsId,$applicationId){

        $response = [];
        $leaguesObj = Leagues::where('account_id',$accountsId)
            ->select(DB::raw('id as leagueId'));


        if($leaguesObj->exists()) {

            $leaguesList = $leaguesObj->get();

            foreach($leaguesList as $key => $object) {

                $schedulesData = Schedules::
                select(DB::raw('
                schedules.id as scheduleId,
                schedules.scheduleName,
                homeTeam.name as homeTeamName,homeTeam.points as homeTeamScore,
                IFNULL(CONCAT("'.$this->teamsImageUrl.'","",homeTeam.icon),"") as homeTeamImage,
                IFNULL(CONCAT("'.$this->teamsImageUrl.'","",awayTeam.icon),"") as awayTeamImage,
                awayTeam.name as awayTeamName,awayTeam.points as awayTeamScore,
                schedules.isSponsorAd , IFNULL(schedules.sponsorAdClickUrl,"") AS sponsorAdClickUrl,
                IFNULL(CONCAT("'.$this->schedulesimageUrl.'","",schedules.sponsorAdImageUrl),"") AS sponsorAdImageUrl
                '))
                    ->leftJoin('teams as homeTeam', function ($join) {
                        $join->on('schedules.home_team_id', '=', 'homeTeam.id');
                    })
                    ->leftJoin('teams as awayTeam', function ($join) {
                        $join->on('schedules.away_team_id', '=', 'awayTeam.id');
                    })
                    ->join('schedules_apps' , function ($join) {
                        $join->on('schedules_apps.schedule_id','=','schedules.id');
                    })
                    ->where('schedules_apps.application_id',$applicationId)
                    ->where('schedules.leagues_id',$object->leagueId)
                    ->where('schedules.is_live','1')
                    ->orderBy('schedules.start_time','ASC');

                if($schedulesData->exists()){
                    $schedulesList = $schedulesData->get();

                    foreach($schedulesList as $keys => $obj){
                        $obj->isSponsorAd = (int) $obj->isSponsorAd;
                        $obj->isSponsorAd = getBoolean($obj->isSponsorAd);
                    }

                    $response[$object->leagueId] = $schedulesList;
                }

                $responseData[$accountsId]  = $response;
            }

            return json_encode($responseData);
        }
        else{
            return false;
        }

    }

    function generateServersJson($accountsId){

        $response = [];
        $scheduleObj = Schedules::where('account_id',$accountsId)
            ->select(DB::raw('id as scheduleId'));

        if($scheduleObj->exists()) {

            $scheduleList = $scheduleObj->get();

            foreach($scheduleList as $key => $object) {

                $serversData = Servers::
                select(DB::raw('
                servers.id as serverId,servers.name as serverName,server_types.label as serverType,servers.link as serverUrl,isHeader,isPremium,
                isTokenAdded,isIpAddressApiCall,isSponsorAd,IFNULL(sponsorAdClickUrl,"") AS sponsorAdClickUrl,
                IFNULL(CONCAT("'.$this->serversImageUrl.'","",sponsorAdImageUrl), "") AS sponsorAdImageUrl
            '))
                    ->join('scheduled_servers as SS', function ($join) {
                        $join->on('SS.server_id', '=', 'servers.id');
                    })
                    ->leftJoin('server_types', function ($join) {
                        $join->on('server_types.id', '=', 'servers.server_type_id');
                    })
                    ->where('SS.schedule_id','=',$object->scheduleId);

                if($serversData->exists()){
                    $serversList = $serversData->get();

                    foreach($serversList as $keys => $obj){

                        $obj->isHeader = (int) $obj->isHeader;

                        $serversList[$keys]->headers = [];
                        if($obj->isHeader){

                            $serverHeaders = DB::table('server_headers')
                                ->select(['key_name as keyName','key_value as keyValue'])
                                ->where('server_id',$obj->serverId)
                                ->get();

                            if(isset($serversList[$key])){
                                $serversList[$key]->headers = (sizeof($serverHeaders) > 0) ? $serverHeaders : [];
                            }    

                        }

                        unset($obj->keyName);
                        unset($obj->keyValue);

                        $obj->isHeader = getBoolean($obj->isHeader);

                        $obj->isPremium = (int) $obj->isPremium;
                        $obj->isPremium = getBoolean($obj->isPremium);

                        $obj->isTokenAdded = (int) $obj->isTokenAdded;
                        $obj->isTokenAdded = getBoolean($obj->isTokenAdded);

                        $obj->isIpAddressApiCall = (int) $obj->isIpAddressApiCall;
                        $obj->isIpAddressApiCall = getBoolean($obj->isIpAddressApiCall);

                        $obj->isSponsorAd = (int) $obj->isSponsorAd;
                        $obj->isSponsorAd = getBoolean($obj->isSponsorAd);

                    }

					$response[$object->scheduleId] = $serversList;
                }
            }
            return json_encode($response);
        }
        else{
            return false;
        }

    }

    function pushJsonToFirebaseDatabase($JSON,$FIREBASE_URL,$NODE,$REQUEST_METHOD){
        $curl = curl_init();


        curl_setopt( $curl, CURLOPT_URL, $FIREBASE_URL .'/'. $NODE);
        curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $REQUEST_METHOD);
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $JSON );

        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

        $response = curl_exec( $curl );

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            $dt['status'] = false;
            $dt['message'] = $error_msg;
            return $dt;
        }
		else{
			$rest = json_decode($response);
			if(isset($rest->error)){
				$dt['status'] = false;
				$dt['message'] = $rest->error;
				return $dt;
				curl_close($curl);
				exit();
			}
		}

        curl_close($curl);
				$dt['status'] = true;
				$dt['message'] = "Data Push";

		return $dt;
        exit();

    }

    public function getApplicationListOptions(Request $request){

        if(!empty($request->account_id) && $request->account_id != "-1"){
            $appList = AppDetails::where('account_id',$request->account_id)->get();
        }
        else{
            $appList = AppDetails::all();
        }
        $options = '<option value="">Select App </option>';
        if(!empty($appList)){
            foreach($appList as $obj){
                $options .= '<option value="'.$obj->id.'" data-account_id="'.$obj->account_id.'">   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
            }
        }

        return $options;
    }

    public function syncAppKeys(Request $request){

        $errors = [];  $response = []; $jsonData = [];

        if(count($request->app_key_app_detail_ids) > 0){
            foreach($request->app_key_app_detail_ids as $appDetailId){

                $listOfApplications = getAppListByAccountsId($request->appKeyAccountsId,$appDetailId);

                $updateAppSettingData = [];
                if(!empty($listOfApplications)){
                    foreach ($listOfApplications as $obj) {

                        $firebaseStatus = "";
                        $message = "";
                        $node = "";

                        $appSettings = AppSettings::where('app_detail_id',$obj->application_id);
                        if($appSettings->exists()){

                            $appSettings = $appSettings->first()->toArray();

                            if(isset($request->appAuthKey1) && !empty($request->appAuthKey1)){
                                $updateAppSettingData['appAuthKey1'] =  $request->appAuthKey1;
                            }

                            if(isset($request->appAuthKey2) && !empty($request->appAuthKey2)){
                                $updateAppSettingData['appAuthKey2'] =  $request->appAuthKey2;
                            }

                            if(isset($request->serverAuthKey1) && !empty($request->serverAuthKey1)){
                                $updateAppSettingData['serverAuthKey1'] =  $request->serverAuthKey1;
                            }

                            if(isset($request->serverAuthKey2) && !empty($request->serverAuthKey2)){
                                $updateAppSettingData['serverAuthKey2'] =  $request->serverAuthKey2;
                            }

                            if(isset($request->isAppSigningKeyUsed) && ($request->isAppSigningKeyUsed >= 0)){
                                $updateAppSettingData['isAppSigningKeyUsed'] =  $request->isAppSigningKeyUsed;
                            }

                            if(isset($request->isFirebaseDatabaseAccess) && ($request->isFirebaseDatabaseAccess >= 0)){
                                $updateAppSettingData['isFirebaseDatabaseAccess'] =  $request->isFirebaseDatabaseAccess;
                            }

                            if(isset($request->isServerTokenFetch) && ($request->isServerTokenFetch >= 0)){
                                $updateAppSettingData['isServerTokenFetch'] =  $request->isServerTokenFetch;
                            }

                            if(isset($request->isAppAuthKeysUsed) && ($request->isAppAuthKeysUsed >= 0)){
                                $updateAppSettingData['isAppAuthKeysUsed'] =  $request->isAppAuthKeysUsed;
                            }

                            if(isset($request->isServerLocalAuthKeyUsed) && ($request->isServerLocalAuthKeyUsed >= 0)){
                                $updateAppSettingData['isServerLocalAuthKeyUsed'] =  $request->isServerLocalAuthKeyUsed;
                            }

                            DB::table('app_settings')
                                ->where('app_detail_id',$obj->application_id)
                                ->update($updateAppSettingData);

                            $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$obj->application_id)->select('app_setting_url','reCaptchaKeyId','firebaseConfigJson')->first();

                            if(isset($firebaseCredentials->app_setting_url)){

                                /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                                $appSettings = getAppSettingDataByAppId($obj->application_id);

                                $jsonData  = $this->createFirebaseJsonFormat($appSettings);
                                $node = "AppSettings";

                                $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

                                $parseFirebaseConfigJson = json_decode($firebaseConfigJson);

                                if(!empty($parseFirebaseConfigJson)){
                                    $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->app_setting_url;
                                    $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

                                    $response[] = [
                                        'app_detail' => $obj->appName . ' - ' . $obj->packageId,
                                        'firebase_status' => "success",
                                        'message' => "Keys Successfully Pushed!",
                                        'firebaseData' => $jsonData,
                                        'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                                        'firebaseConfigJson' => $firebaseConfigJson,
                                        'node' => $node,
                                        'appPackageId' => $obj->packageId.time()
                                    ];
                                }
                                else{
                                    $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Firebase Config JSON is missing!' ];
                                }
                            }
                            else{
                                $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Firebase Credentials not found!' ];
                            }
                        }
                        else{
                            $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'App Setting not found!' ];
                        }
                    }
                }

            }

        }
        else{
            $errors[] = ['code' => 422 , 'message' => 'No Application Selected!' ];
        }

        return response()->json([
            'data' => [
                'success' => $response,
                'failed' => $errors,
            ]
        ]);

    }

    public function createFirebaseJsonFormat($request){

        $requestArray = [];
        $requestArray['appAuthKey1'] = $request->appAuthKey1;
        $requestArray['appAuthKey2'] = $request->appAuthKey2;
        $requestArray['appCacheId'] =  (float) number_format($request->appCacheId,1);
        $requestArray['appDetailsDatabaseVersion'] = (float) number_format($request->appDetailsDatabaseVersion,1);
        $requestArray['appSharedPrefId'] =  (float) number_format($request->appSharedPrefId,1);
        $requestArray['serverApiBaseUrl'] = $request->serverApiBaseUrl;
        $requestArray['streamKey'] = $request->streamKey;
        $requestArray['isAppClearCache'] = getBoolean($request->isAppClearCache);
        $requestArray['isAppClearSharedPref'] = getBoolean($request->isAppClearSharedPref);
        $requestArray['isAppDetailsDatabaseClear'] = getBoolean($request->isAppDetailsDatabaseClear);
        $requestArray['isAppDetailsDatabaseSave'] = getBoolean($request->isAppDetailsDatabaseSave);
        $requestArray['isFirebaseDatabaseAccess'] = getBoolean($request->isFirebaseDatabaseAccess);
        $requestArray['isAppSigningKeyUsed'] = getBoolean($request->isAppSigningKeyUsed);
        $requestArray['isAppAuthKeysUsed'] = getBoolean($request->isAppAuthKeysUsed);
        $requestArray['isServerLocalAuthKeyUsed'] = getBoolean($request->isServerLocalAuthKeyUsed);
        $requestArray['minimumVersionSupport'] = (int) $request->minimumVersionSupport;
        $requestArray['serverAuthKey1'] = $request->serverAuthKey1;
        $requestArray['serverAuthKey2'] = $request->serverAuthKey2;
        $requestArray['appDetailsDatabaseClearVersion'] = (float) $request->appDetailsDatabaseClearVersion;
        $requestArray['isMessageDialogDismiss'] = getBoolean($request->isMessageDialogDismiss);
        $requestArray['isServerTokenFetch'] = getBoolean($request->isServerTokenFetch);
        $requestArray['sslSha256Key'] = $request->sslSha256Key;
        $requestArray['checkIpAddressApiUrl'] = $request->checkIpAddressApiUrl;
//        $requestArray['isIpAddressApiCall'] = $request->isIpAddressApiCall;

        return  json_encode($requestArray);
    }

    public function syncAppCredentials(Request $request){

        $errors = [];  $response = []; $jsonData = [];

        if(count($request->app_credentials_app_detail_id) > 0){
            foreach($request->app_credentials_app_detail_id as $appDetailId){

                $listOfApplications = getAppListByAccountsId($request->appCredentialsAccountsId,$appDetailId);

                $updateAppCredentialsData = [];
                if(!empty($listOfApplications)){
                    foreach ($listOfApplications as $obj) {

                        $firebaseStatus = "";
                        $message = "";
                        $node = "";

                        $appCredentials = AppCredentials::where('app_detail_id',$obj->application_id);
                        if($appCredentials->exists()){

                            $appCredentials = $appCredentials->first()->toArray();

                            if(isset($request->server_auth_key) && !empty($request->server_auth_key)){
                                $updateAppCredentialsData['server_auth_key'] =  $request->server_auth_key;
                            }

                            if(isset($request->stream_key) && !empty($request->stream_key)){
                                $updateAppCredentialsData['stream_key'] =  $request->stream_key;
                            }

                            if(isset($request->token_key) && !empty($request->token_key)){
                                $updateAppCredentialsData['token_key'] =  $request->token_key;
                            }

                            if(isset($request->appSigningKey) && !empty($request->appSigningKey)){
                                $updateAppCredentialsData['appSigningKey'] =  $request->appSigningKey;
                            }

                            DB::table('app_credentials')
                                ->where('app_detail_id',$obj->application_id)
                                ->update($updateAppCredentialsData);

                            $response[] = [
                                'app_detail' => $obj->appName . ' - ' . $obj->packageId,
                                'firebase_status' => "success",
                                'message' => "Credentials Successfully Updated",
                                'appPackageId' => $obj->packageId
                            ];

                        }
                        else{
                            $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'App Credentials not found!' ];
                        }
                    }
                }

            }

        }
        else{
            $errors[] = ['code' => 422 , 'message' => 'No Application Selected!' ];
        }

        return response()->json([
            'data' => [
                'success' => $response,
                'failed' => $errors,
            ]
        ]);

    }

    public function syncAppDetails(Request $request){

        $errors = [];  $response = []; $jsonData = [];

        if(count($request->app_details_application_ids) > 0){
            foreach($request->app_details_application_ids as $appDetailId){

                $listOfApplications = getAppListByAccountsId($request->appDetailsAccountsId,$appDetailId);

                $updateAppDetailsData = [];
                if(!empty($listOfApplications)){
                    foreach ($listOfApplications as $obj) {

                        $firebaseStatus = "";
                        $message = "";
                        $node = "";

                        $appDetails = AppDetails::where('id',$obj->application_id);
                        if($appDetails->exists()){

                            $appDetails = $appDetails->first()->toArray();

                            if(isset($request->isAdsInterval) && ($request->isAdsInterval >= 0)){
                                $updateAppDetailsData['isAdsInterval'] =  $request->isAdsInterval;
                            }

                            if(isset($request->adsIntervalTime) && !empty($request->adsIntervalTime)){
                                $updateAppDetailsData['adsIntervalTime'] =  $request->adsIntervalTime;
                            }

                            if(isset($request->isAdmobAdsShow) && ($request->isAdmobAdsShow >= 0)){
                                $updateAppDetailsData['isAdmobAdsShow'] =  $request->isAdmobAdsShow;
                            }

                            if(isset($request->isStartAppAdsShow) && ($request->isStartAppAdsShow >= 0)){
                                $updateAppDetailsData['isStartAppAdsShow'] =  $request->isStartAppAdsShow;
                            }


                            if(isset($request->isScreenAdsLimit) && ($request->isScreenAdsLimit >= 0)){
                                $updateAppDetailsData['isScreenAdsLimit'] =  $request->isScreenAdsLimit;
                            }

                            if(isset($request->isSponsorAdsShow) && ($request->isSponsorAdsShow >= 0)){
                                $updateAppDetailsData['isSponsorAdsShow'] =  $request->isSponsorAdsShow;
                            }

                            if(isset($request->startAppId) && !empty($request->startAppId)){
                                $updateAppDetailsData['startAppId'] =  $request->startAppId;
                            }

                            if(isset($request->adsIntervalCount) && !empty($request->adsIntervalCount)){
                                $updateAppDetailsData['adsIntervalCount'] =  $request->adsIntervalCount;
                            }

                            DB::table('app_details')
                                ->where('id',$obj->application_id)
                                ->update($updateAppDetailsData);


                            $appSettings = AppSettings::where('app_detail_id',$obj->application_id);
                            if($appSettings->exists()){
                                $appSettings = $appSettings->first()->toArray();
                                $finalValue = prepareNewVersionNumbers('appDetailsDatabaseVersion',$appSettings['appDetailsDatabaseVersion']);

                                if($finalValue){
                                    DB::table('app_settings')->where('app_detail_id',$obj->application_id)->update(['appDetailsDatabaseVersion' => $finalValue]);

                                    /******* App Details Sync to Firebase **********/
                                    $jsonData = $this->generateAppDetailJson($obj->application_id);

                                    if(!empty($jsonData)){

                                        $appDetailFirebaseCredentials = FirebaseCredentials::where('app_detail_id',$obj->application_id)->select('apps_url','app_setting_url','reCaptchaKeyId','firebaseConfigJson')->first();
                                        if(isset($appDetailFirebaseCredentials->apps_url) && isset($appDetailFirebaseCredentials->app_setting_url)) {

                                            /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                                            $appNode = "AppDetails";

                                            $appDetailFirebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $appDetailFirebaseCredentials->firebaseConfigJson));

                                            $parseAppDetailFirebaseConfigJson = json_decode($appDetailFirebaseConfigJson);

                                            if (!empty($parseAppDetailFirebaseConfigJson)) {
//                                                $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Firebase Configuration Parameters not found for this Application!' ];

                                                $parseAppDetailFirebaseConfigJson->databaseURL = $appDetailFirebaseCredentials->apps_url;

                                                $appDetailFirebaseConfigJson = json_encode($parseAppDetailFirebaseConfigJson);

                                                $response[] = [
                                                    'app_detail' => $obj->appName . ' - ' . $obj->packageId,
                                                    'firebase_status' => "success",
                                                    'message' => "Keys Successfully Pushed!",
                                                    'firebaseData' => $jsonData,
                                                    'reCaptchaKeyId' => (!empty($appDetailFirebaseCredentials->reCaptchaKeyId)) ? $appDetailFirebaseCredentials->reCaptchaKeyId : "",
                                                    'firebaseConfigJson' => $appDetailFirebaseConfigJson,
                                                    'node' => $appNode,
                                                    'appPackageId' => $obj->packageId . rand(222,333).time()
                                                ];

                                                /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

                                                $appSettings = getAppSettingDataByAppId($obj->application_id);

                                                $jsonData  = $this->createFirebaseJsonFormat($appSettings);
                                                $node = "AppSettings";

                                                $appDetailFirebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $appDetailFirebaseCredentials->firebaseConfigJson));

                                                $parseAppDetailFirebaseConfigJson = json_decode($appDetailFirebaseConfigJson);
                                                $parseAppDetailFirebaseConfigJson->databaseURL = $appDetailFirebaseCredentials->app_setting_url;
                                                $appDetailFirebaseConfigJson = json_encode($parseAppDetailFirebaseConfigJson);

                                                $response[] = [
                                                    'app_detail' => $obj->appName . ' - ' . $obj->packageId,
                                                    'firebase_status' => "success",
                                                    'message' => "Keys Successfully Pushed!",
                                                    'firebaseData' => $jsonData,
                                                    'reCaptchaKeyId' => (!empty($appDetailFirebaseCredentials->reCaptchaKeyId)) ? $appDetailFirebaseCredentials->reCaptchaKeyId : "",
                                                    'firebaseConfigJson' => $appDetailFirebaseConfigJson,
                                                    'node' => $node,
                                                    'appPackageId' => $obj->packageId . rand(444,555).time()
                                                ];

                                            }
                                            else{
                                                $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Firebase Configuration Parameters not found for this Application!' ];
                                            }
                                        }
                                        else{
                                            $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Firebase Credentials not found!' ];
                                        }
                                    }
                                    else{
                                        $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Data not found!' ];
                                    }

                                    /******* App Settings Sync to Firebase **********/

//                                    $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$obj->application_id)->select('app_setting_url','reCaptchaKeyId','firebaseConfigJson')->first();

//                                    if(isset($firebaseCredentials->app_setting_url)){
//
//                                        /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/
//
//                                        $appSettings = getAppSettingDataByAppId($obj->application_id);
//
//                                        $jsonData  = $this->createFirebaseJsonFormat($appSettings);
//                                        $node = "AppSettings";
//
//                                        $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));
//
//                                        $parseFirebaseConfigJson = json_decode($firebaseConfigJson);
//
//                                        if(!empty($parseFirebaseConfigJson)){
//                                            $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->app_setting_url;
//                                            $firebaseConfigJson = json_encode($parseFirebaseConfigJson);
//
//                                            $response[] = [
//                                                'app_detail' => $obj->accountsName . ' - ' . $obj->packageId,
//                                                'firebase_status' => "success",
//                                                'message' => "Keys Successfully Found!",
//                                                'firebaseData' => $jsonData,
//                                                'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
//                                                'firebaseConfigJson' => $firebaseConfigJson,
//                                                'node' => $node,
//                                                'appPackageId' => $obj->packageId
//                                            ];
//                                        }
//                                        else{
//                                            $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Firebase Config JSON is missing!' ];
//                                        }
//                                    }
//                                    else{
//                                        $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Firebase Credentials for App Setting URL not found!' ];
//                                    }

                                }
                                else{
                                    $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'Failed due to incorrect decimal value!' ];
                                }
                            }
                            else{
                                $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'App Setting not found!' ];
                            }
                        }
                        else{
                            $errors[] = ['app_detail' =>  $obj->accountsName . ' - ' . $obj->packageId , 'message' => 'App Setting not found!' ];
                        }
                    }
                }

            }

        }
        else{
            $errors[] = ['code' => 422 , 'message' => 'No Application Selected!' ];
        }

        return response()->json([
            'data' => [
                'success' => $response,
                'failed' => $errors,
            ]
        ]);

    }

}
