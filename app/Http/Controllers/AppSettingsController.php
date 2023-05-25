<?php

namespace App\Http\Controllers;

use App\Models\AppDetails;
use App\Models\AppSettings;
use App\Models\FirebaseCredentials;
use App\Models\Sports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

class AppSettingsController extends Controller
{
    protected $roleAssignedApplications;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-app_settings', ['only' => ['index','fetchAppSettingsList','getApplicationSettingsCardView']]);
        $this->middleware('role_or_permission:super-admin|manage-app_settings',['only' => ['edit','store','destroy','deleteAll']]);
        $this->middleware('role_or_permission:super-admin|view-manage-sync_sports_data',['only' => ['updateDatabaseVersion']]);
    }

    public function index()
    {
        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        $appsList = AppSettings::select('app_settings.id as id','appName','appLogo','packageId','sports.name as sports_name')
            ->join('app_details', function ($join) {
                $join->on('app_details.id', '=', 'app_settings.app_detail_id');
            })
            ->join('sports', function ($join) {
                $join->on('sports.id', '=', 'app_details.sports_id');
            })
            ->get();

        $sportsList = Sports::orderBy('id','DESC')->get();

        return view('app_settings.index')
            ->with('appsList',$appsList)
            ->with('sportsList',$sportsList);

    }

    public function create($appSettingId = false)
    {
        if(!$appSettingId){

            // for create new form

//            $appListWithoutCredentials = DB::select(DB::raw('
//
//            SELECT *
//            FROM app_details app
//            WHERE NOT EXISTS
//                (SELECT *
//                    FROM app_settings s
//                    WHERE s.app_detail_id = app.id
//                );
//            '));
//
            $sportsList = Sports::orderBy('id','DESC')->get();

            $appData = [];

          $appListWithoutCredentials = [];

            return view('app_settings.create')
                ->with('appsList',$appListWithoutCredentials)
                ->with('appData',$appData )
                ->with('sportsList',$sportsList);

        }
        else{

            $appSettingData = AppSettings::where('id',$appSettingId)->first();
            $appId = $appSettingData->app_detail_id;
            $appDetail = AppDetails::where('id',$appId)->select("sports_id")->first();
            $sportsId = $appDetail->sports_id;

            $appIdClause = "";

            if(!empty($sportsId)){
                $appIdClause = " AND app.sports_id = ". $sportsId;
            }

            if(!empty($appSettingId)){
                $appIdClause .= " OR app.id = ". $appId;
            }

            DB::enableQueryLog();
            $excludedApps = DB::select(DB::raw('
            SELECT *
            FROM app_details app
            WHERE NOT EXISTS (SELECT *
                                    FROM app_settings s
                                    WHERE s.app_detail_id = app.id
                            )
            '.$appIdClause.'
            '));

//            dd(DB::getQueryLog());

            $sportsList = Sports::orderBy('id','DESC')->get();

            return view('app_settings.create')
                ->with('appData',$appSettingData)
                ->with('appsList',$excludedApps)
                ->with('sportsList',$sportsList)
                ->with('appSettingId',$appSettingId)
                ->with('sportsId',$sportsId);
        }

    }

    public function store(Request $request , $app_setting_id = false)
    {

        

        /*** Validation BEGIN ****/
        if(!empty($app_setting_id))
        {
            $appSetting = AppSettings::where('id',$app_setting_id)->select(['app_detail_id'])->first();

            $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
            if(!in_array($appSetting->app_detail_id,$roleAssignedApplications)){
                return Response::json(["message"=>"You are not allowed to perform this action!"],403);
            }
    

            $appDetailId = $appSetting->app_detail_id;

            $validationResponse = [];
            $validation = AppSettings::where('app_detail_id',$request->app_detail_id)
                ->where('id','!=',$app_setting_id);

            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['app_detail_id'] = "The app  already exists!";

                return Response::json($validationResponse,422);
            }
        }
        else{

            $appDetailId = $request->app_detail_id;

            $validationResponse = [];

            $validation = AppSettings::where('app_detail_id',$request->app_detail_id);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['app_detail_id'] = "This app already exists!";

                return Response::json($validationResponse,422);

            }
        }

        /*** Validation END ****/

        $firebaseStatus = "";
        $status = "";
        $message = "";
        $node = "";
        $jsonData = [];

        $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$appDetailId)->select('app_setting_url','reCaptchaKeyId','firebaseConfigJson')->first();

        if(isset($firebaseCredentials->app_setting_url)){

            /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

            $jsonData  = $this->createFirebaseJsonFormat($request);
            $node = "AppSettings";

            $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));

            $parseFirebaseConfigJson = json_decode($firebaseConfigJson);

            if(!empty($parseFirebaseConfigJson)){
                $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->app_setting_url;
            }

            $firebaseConfigJson = json_encode($parseFirebaseConfigJson);

        }
        else{
            $firebaseConfigJson = [];
            $firebaseStatus = "failed";
            $message  =  "Firebase credentials not found!";
        }

        $input = $request->all();

        $appSetting   =   AppSettings::updateOrCreate(
            [
                'id' => $app_setting_id
            ],
            $input);

        if(!empty($appSetting)){
            $status = "success";
        }

        return response()->json(['status' => $status,
            'firebase_status' => $firebaseStatus,
            'message' => $message,
            'appSetting' => $appSetting->id,
            'firebaseData' => $jsonData,
            'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
            'firebaseConfigJson' => $firebaseConfigJson,
            'node' => $node
        ]);
    }

    public function destroy(Request $request)
    {
        $database = AppSettings::where('id',$request->id)->select('app_detail_id')->first();
        $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($database->app_detail_id,$roleAssignedApplications)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }
        
        AppSettings::where('id',$request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function createFirebaseJsonFormat($request){

        $requestArray = [];
        $requestArray['appAuthKey1'] = $request->appAuthKey1;
        $requestArray['appAuthKey2'] = $request->appAuthKey2;
        $requestArray['appCacheId'] =  (float) number_format($request->appCacheId,1);
        $requestArray['appDetailsDatabaseVersion'] = (float) number_format($request->appDetailsDatabaseVersion,1);
        $requestArray['appSharedPrefId'] =  (float) number_format($request->appSharedPrefId,1);
        $requestArray['leaguesDatabaseVersion'] =  (float) number_format($request->leaguesDatabaseVersion,1);
        $requestArray['schedulesDatabaseVersion'] =  (float) number_format($request->schedulesDatabaseVersion,1);
        $requestArray['serverApiBaseUrl'] = $request->serverApiBaseUrl;
        $requestArray['serversDatabaseVersion'] =  (float) number_format($request->serversDatabaseVersion,1);
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

    public function pushJsonToFirebaseDatabase($JSON,$FIREBASE_URL,$NODE,$REQUEST_METHOD){

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

    public function getApplicationSettingsCardView(Request $request){

        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        $appsList = AppSettings::select('app_settings.id as id','appName','appLogo','packageId','sports.name as sports_name')
            ->join('app_details', function ($join) {
                $join->on('app_details.id', '=', 'app_settings.app_detail_id');
            })
            ->join('sports', function ($join) {
                $join->on('sports.id', '=', 'app_details.sports_id');
            });

        if(isset($request->searchKeywords) && !empty($request->searchKeywords)){
            $appsList = $appsList->where(function($query) {
                $query->orWhere('app_details.appName','like','%'.request()->searchKeywords.'%');
                $query->orWhere('sports.name','like','%'.request()->searchKeywords.'%');
            });
        }

        if(!empty($this->roleAssignedApplications)){
            $appsList = $appsList->whereIn('app_settings.app_detail_id',$this->roleAssignedApplications);
        }

        if(isset($request->sportsId) && !empty($request->sportsId) && $request->sportsId != '-1'){
            $appsList = $appsList->where('app_details.sports_id',$request->sportsId);
            $appsList = $appsList->orderBy('app_settings.id','ASC');
        }

        $totalApps = $appsList->count();
        $appsList = $appsList->paginate(8);

        return view('includes.app_settings_card_view')
            ->with('appsList',$appsList)
            ->with('totalAppsCount',$totalApps);
    }

    public function fetchData(Request $request)
    {
        if($request->ajax())
        {
            $appsList = AppSettings::select('app_settings.id as id','appName','appLogo','packageId','sports.name as sports_name')
                ->join('app_details', function ($join) {
                    $join->on('app_details.id', '=', 'app_settings.app_detail_id');
                })
                ->join('sports', function ($join) {
                    $join->on('sports.id', '=', 'app_details.sports_id');
                });

            if(isset($request->sportsId) && !empty($request->sportsId) && $request->sportsId != '-1'){
                $appsList = $appsList->where('app_details.sports_id',$request->sportsId);
            }

            if(isset($request->searchKeywords) && !empty($request->searchKeywords)){
                $appsList = $appsList->where(function($query) {
                    $query->orWhere('app_details.appName','like','%'.request()->searchKeywords.'%');
                    $query->orWhere('sports.name','like','%'.request()->searchKeywords.'%');
                });
            }

            $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
            if(!empty($this->roleAssignedApplications)){
                $appsList = $appsList->whereIn('app_details.id',$this->roleAssignedApplications);
            }
        

            $totalApps = $appsList->count();
            $appsList = $appsList->paginate(8);

            return view('includes.app_settings_card_view')
                ->with('appsList',$appsList)
                ->with('totalAppsCount',$totalApps);

        }
    }

    public function updateDatabaseVersion(Request $request){

//        if($request->version_app_detail_id == "all"){
//            $listOfApplications = getAppListBySportsId($request->versionSportsId);
//        }
//        else{
//            $listOfApplications = getAppListBySportsId($request->versionSportsId,$request->version_app_detail_id);
//        }

        $sportsId = $request->versionSportsId;
        $sportsDetail = getSportDetailsById($sportsId);

        $versionCategories = $request->version_categories;

        $errors = [];  $response = []; $jsonData = [];

        if(count($request->version_app_detail_ids) > 0) {
            foreach ($request->version_app_detail_ids as $appDetailId) {
                $listOfApplications = getAppListBySportsId($request->versionSportsId, $appDetailId);

                if(!empty($listOfApplications)){
                    foreach ($listOfApplications as $obj) {

                        $firebaseStatus = "";
                        $message = "";
                        $node = "";

                        $appSettings = AppSettings::where('app_detail_id',$obj->application_id);
                        if($appSettings->exists()){
                            $appSettings = $appSettings->first()->toArray();
                            $finalValue = prepareNewVersionNumbers($versionCategories,$appSettings[$versionCategories]);
                            if($finalValue){

                                DB::table('app_settings')->where('app_detail_id',$obj->application_id)->update([$versionCategories => $finalValue]);

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
                                            'app_detail' => $obj->sportsName . ' - ' . $obj->packageId,
                                            'firebase_status' => "success",
                                            'message' => "Credentials Successfully Pushed!",
                                            'firebaseData' => $jsonData,
                                            'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
                                            'firebaseConfigJson' => $firebaseConfigJson,
                                            'node' => $node,
                                            'appPackageId' => $obj->packageId
                                        ];
                                    }
                                    else{
                                        $errors[] = ['app_detail' =>  $obj->sportsName . ' - ' . $obj->packageId , 'message' => 'Firebase Config JSON is missing!' ];
                                    }
                                }
                                else{
                                    $errors[] = ['app_detail' =>  $obj->sportsName . ' - ' . $obj->packageId , 'message' => 'Firebase Credentials not found!' ];
                                }
                            }
                            else{
                                $errors[] = ['app_detail' =>  $obj->sportsName . ' - ' . $obj->packageId , 'message' => 'Failed due to incorrect decimal value!' ];
                            }
                        }
                        else{
                            $errors[] = ['app_detail' =>  $obj->sportsName . ' - ' . $obj->packageId , 'message' => 'App Setting not found!' ];
                        }
                    }
                }
                else{
                    $errors[] = ['app_detail' => $sportsDetail->name , 'message' => 'Application not found!' ];
                }

            }
        }

        return response()->json([
            'data' => [
                'success' => $response,
                'failed' => $errors,
            ]
        ]);
    }

    public function removeAllAppSettings(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids);

        foreach($idsArray as $id) {
            AppSettings::where('id',$id)->delete();
        }

        return response()->json(['success' => "App Setting removed successfully" , "message" => "App Setting removed successfully"]);
    }


}

