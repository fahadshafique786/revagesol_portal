<?php

namespace App\Http\Controllers;

use App\Models\AdmobAds;
use App\Models\AppCredentials;
use App\Models\AppSettings;
use App\Models\BlockedApplication;
use App\Models\FirebaseCredentials;
use Illuminate\Http\Request;
use App\Models\AppDetails;
use App\Models\Sports;
use App\Models\SponsorAds;
use App\Models\RoleHasApplication;
use Illuminate\Support\Facades\DB;
use Response;
use Auth;

class AppDetailsController extends Controller
{
    protected $roleAssignedApplications;
    public $imageUrl;
    public $sponsorImageUrl;

    public function __construct()
    {
        $this->imageUrl = config('app.appsImagePath');
        $this->sponsorImageUrl = config('app.sponsoradsImagePath');

        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-applications', ['only' => ['index','getApplicationCardView']]);
        $this->middleware('role_or_permission:super-admin|manage-applications',['only' => ['create','edit','store','destroy','deleteAll']]);

    }


    public function index(Request $request)
    {
        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

//        DB::enableQueryLog();

        $appsList = AppDetails::select('app_details.id as id','appName','appLogo','packageId','sports.name as sports_name')
            ->join('sports', function ($join) {
                $join->on('sports.id', '=', 'app_details.sports_id');
            });

        $appsList = $appsList->get();

//        dd(DB::getQueryLog());

        $sportsList = Sports::orderBy('id','DESC')->get();

        return view('application.index')
            ->with('sportsList',$sportsList)
            ->with('appslist',$appsList);
    }


    public function create()
    {
        $sportsList = Sports::orderBy('id','DESC')->get();
        return view('application.create')
        ->with('sportsList',$sportsList);
    }


    public function edit($application_id)
    {
        $appData = AppDetails::where('id',$application_id)->first();

        $sportsList = Sports::orderBy('id','DESC')->get();


        return view('application.edit')
            ->with('sportsList',$sportsList)
            ->with('appData',$appData)
            ->with('application_id',$application_id);
    }


    public function store(Request $request,$application_id = false)
    {
        if(!empty($application_id))
        {
            $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
            if(!in_array($application_id,$roleAssignedApplications)){
                return Response::json(["message"=>"You are not allowed to perform this action!"],403);
            }

            $this->validate($request, [
                'appName' => 'required',
                'packageId' => 'required|unique:app_details,packageId,'.$application_id,
                'sports_id' => 'required',
                'admobAppId' => 'required',
                'adsIntervalTime' => 'required',
                'adsIntervalCount' => 'required',
//                'checkIpAddressApiUrl' => 'required',
                'startAppId' => 'required',
                'newAppPackage' => 'required',
                'ourAppPackage' => 'required',
            ]);
        }
        else
        {
            $this->validate($request, [
                'appName' => 'required',
                'packageId' => 'required|unique:app_details',
                'appName' => 'required',
                'sports_id' => 'required',
                'admobAppId' => 'required',
                'adsIntervalTime' => 'required',
                'adsIntervalCount' => 'required',
//                'checkIpAddressApiUrl' => 'required',
                'startAppId' => 'required',
                'newAppPackage' => 'required',
                'ourAppPackage' => 'required',
            ]);
        }

        $input = $request->all();

        if($request->hasFile('appLogo'))
        {

            if(!empty($request->id)){

                $getIcon = DB::table('app_details')->where('id',$request->id)->select('appLogo')->first();

                if(!empty($getIcon->appLogo)){
                    $serverImagePath = 'uploads/apps/'.$getIcon->appLogo;
                    removeServerImages($serverImagePath);
                }

            }

            $fileobj				= $request->file('appLogo');
            $file_extension_name 	= $fileobj->getClientOriginalExtension('appLogo');
            $file_unique_name 		= strtolower(str_replace(' ','-',$request->appName)).'_'.time().rand(1000,9999).'.'.$file_extension_name;
            $destinationPath		= public_path('/uploads/apps/');
            $fileobj->move($destinationPath,$file_unique_name);

            $input['appLogo'] = $file_unique_name;
        }

        $appDetailResponse   =   AppDetails::updateOrCreate(
            [
                'id' => $application_id
            ],
            $input);

        /******************
         *
         * PUSH DATA TO FIREBASE REAL TIME DATABASE
         *
         */

        $appDetailId = ($application_id) ? $application_id : $appDetailResponse->id;

        $firebaseStatus = "";
        $status = "";
        $message = "";
        $node = "";


        if(!empty($appDetailResponse)){
            $status = "success";
        }

        $jsonData = [];

        $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$appDetailId)->select('apps_url','reCaptchaKeyId','firebaseConfigJson')->first();

        if(isset($firebaseCredentials->apps_url)){

            /***   CREATE JSON FORMAT TO PUSH DATA ON FIREBASE DATABASE ***/

            $jsonData  = $this->generateAppDetailJson($appDetailId);
            $node = "AppDetails";

            $firebaseConfigJson = trim(preg_replace('/\s\s+/', ' ', $firebaseCredentials->firebaseConfigJson));
            if(!empty($firebaseConfigJson)){
                $parseFirebaseConfigJson = json_decode($firebaseConfigJson);
                $parseFirebaseConfigJson->databaseURL = $firebaseCredentials->apps_url;
                $firebaseConfigJson = json_encode($parseFirebaseConfigJson);
            }
            else{
                $message = "Firebase Config Json Not Found!";
            }

        }
        else{
            $firebaseConfigJson = [];
            $firebaseStatus = "failed";
            $message  =  "Firebase credentials not found!";
        }

        if(empty($application_id)){
            $roleId = auth()->user()->roles()->first()->id;
            RoleHasApplication::create(["role_id"=> $roleId , "application_id" => $appDetailResponse->id ]);
        }

        return response()->json(['status' => $status,
            'firebase_status' => $firebaseStatus,
            'message' => $message,
            'appSetting' => $appDetailId,
            'firebaseData' => $jsonData,
            'reCaptchaKeyId' => (!empty($firebaseCredentials->reCaptchaKeyId)) ? $firebaseCredentials->reCaptchaKeyId : "",
            'firebaseConfigJson' => $firebaseConfigJson,
            'node' => $node
        ]);

    }


    public function destroy(Request $request)
    {
        $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($request->id,$roleAssignedApplications)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }
        

        AdmobAds::where('app_detail_id',$request->id)->delete();

        SponsorAds::where('app_detail_id',$request->id)->delete();

        AppCredentials::where('app_detail_id',$request->id)->delete();

        FirebaseCredentials::where('app_detail_id',$request->id)->delete();

        BlockedApplication::where('application_id',$request->id)->delete();


        $getIcon = DB::table('app_details')->where('id',$request->id)->select('appLogo')->first();

        if(!empty($getIcon->appLogo)){
            $serverImagePath = 'uploads/apps/'.$getIcon->appLogo;
            removeServerImages($serverImagePath);
        }


        AppDetails::where('id',$request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function getSportsOptionsByApp(Request $request){
        $leaguesList = Leagues::where('sports_id',$request->sports_id)->get();
        $options = '<option value="">Select League </option>';
        if(!empty($leaguesList)){
            foreach($leaguesList as $obj){
                $options .= '<option value="'.$obj->id.'">   '  .   $obj->name    .   '    </option>';
            }
        }
        return $options;
    }

    public function getApplicationCardView(Request $request){


        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        DB::enableQueryLog();

        $appsList = AppDetails::select('app_details.id as id','appName','appLogo','packageId','sports.name as sports_name')
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
            $appsList = $appsList->whereIn('app_details.id',$this->roleAssignedApplications);
        }


        if(isset($request->sportsId) && !empty($request->sportsId) && $request->sportsId != '-1'){
            $appsList = $appsList->where('sports_id',$request->sportsId);
            $appsList = $appsList->orderBy('app_details.id','ASC');
        }


        $totalApps = $appsList->count();
        $appsList = $appsList->paginate(8);

//        dd(DB::getQueryLog());

        return view('includes.apps_card_view')
            ->with('appsList',$appsList)
            ->with('totalAppsCount',$totalApps);
    }

    public function fetchData(Request $request)
    {
        if($request->ajax())
        {
            $appsList = AppDetails::select('app_details.id as id','appName','appLogo','packageId','sports.name as sports_name')
                ->join('sports', function ($join) {
                    $join->on('sports.id', '=', 'app_details.sports_id');
                });

            if(isset($request->sportsId) && !empty($request->sportsId) && $request->sportsId != '-1'){
                $appsList = $appsList->where('sports_id',$request->sportsId);
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

            return view('includes.apps_card_view')
                ->with('appsList',$appsList)
                ->with('totalAppsCount',$totalApps);
        }
    }

    public function getApplicationListOptions(Request $request){

        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        if(!empty($request->sports_id) && $request->sports_id != "-1"){
            $appList = AppDetails::where('sports_id',$request->sports_id);

            if(!empty($this->roleAssignedApplications)){
                $appList = $appList->whereIn('id',$this->roleAssignedApplications);
            }
                
            $appList = $appList->get();
        }
        else{

            if(!empty($this->roleAssignedApplications)){
                $appList = AppDetails->whereIn('id',$this->roleAssignedApplications)->get();
            }
            else{
                $appList = AppDetails::get();
            }
                
        }

        $isDisabled = "";
        if(isset($request->disable_first_option)){
            if($request->disable_first_option == "true"){
                $isDisabled = "disabled";
            }
        }

        $options = '<option value="-1" '. $isDisabled .'> Select App </option>';
        if(!empty($appList)){
            foreach($appList as $obj){
                $options .= '<option value="'.$obj->id.'" >   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
            }
        }

        return $options;
    }

    public function getApplicationListOptionsNoPermission(Request $request){

        if(!empty($request->sports_id) && $request->sports_id != "-1"){

            $appList = AppDetails::where('sports_id',$request->sports_id)->get();
        }
        else{
                $appList = AppDetails::get();                
        }

        $isDisabled = "";
        if(isset($request->disable_first_option)){
            if($request->disable_first_option == "true"){
                $isDisabled = "disabled";
            }
        }

        $options = '<option value="-1" '. $isDisabled .'> Select App </option>';
        if(!empty($appList)){
            foreach($appList as $obj){
                $options .= '<option value="'.$obj->id.'" >   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
            }
        }

        return $options;
    }

    public function getAppsListWithAllOption(Request $request){

        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        if(!empty($request->sports_id) && $request->sports_id != "-1"){

            $appList = AppDetails::where('sports_id',$request->sports_id);

            if(!empty($this->roleAssignedApplications)){
                $appList = $appList->whereIn('id',$this->roleAssignedApplications);
            }
                
            $appList = $appList->get();

        }
        else{

            if(!empty($this->roleAssignedApplications)){
                $appList = AppDetails->whereIn('id',$this->roleAssignedApplications)->get();
            }
            else{
                $appList = AppDetails::get();
            }

        }

        $isDisabled = "";
        if(isset($request->disable_first_option)){
            if($request->disable_first_option == "true"){
                $isDisabled = "disabled";
            }
        }

        $options = '<option value="-1" '.$isDisabled.'> Select App</option>';
        if(!empty($appList) && sizeof($appList) > 0){
            $options .= '<option value="all"> All Apps </option>';
            foreach($appList as $obj){
                $options .= '<option value="'.$obj->id.'" >   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
            }
        }

        return $options;
    }

    public function getRolesAppsListBySports(Request $request){

        $commonApps = [];
        if(!empty($request->sports_id) && $request->sports_id != "-1"){
            $appList = AppDetails::whereIn('sports_id',$request->sports_id);       
            $currentSelectedApps = [];

            $array1 = $appList->pluck('id')->toArray();
            
            if(isset($request->applications) && !empty($request->applications)){
                if($request->un_select_all_option == "false"){
                    $commonApps = array_intersect($array1, $request->applications);
                }
            }
            
            $appList = $appList->get();

        }
        else{

            return ""; // on demand during qa  ( might be possible it can be temporary )

            // $appList = AppDetails::get();
        }

        $rolesApplications = RoleHasApplication::where('role_id',$request->role_id)->where('role_id','!=',1)->pluck('application_id')->toArray();

        // dd($rolesApplications->toArray());
        $options = $this->mergeSportsAppsList($appList,$rolesApplications,$commonApps);

        return $options;
    }

    public function mergeSportsAppsList($appsList , $rolesApplications,$commonApps){

        $options = '';
        if(!empty($appsList) && sizeof($appsList) > 0){
            if(count($rolesApplications) > 0){
                foreach($appsList as $obj){
                        $isSelected = "";
                        if(in_array($obj->id,$rolesApplications) || in_array($obj->id,$commonApps) ){
                            $isSelected = "selected";
                        }
                        $options .= '<option value="'.$obj->id.'" '.$isSelected.' >   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
                }
                return $options;
            }
            else{
                foreach($appsList as $obj){
                    $isSelected = "";
                    if(in_array($obj->id,$rolesApplications) || in_array($obj->id,$commonApps) ){
                        $isSelected = "selected";
                    }
                    $options .= '<option value="'.$obj->id.'" '.$isSelected.' >   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
                }
            }
        }

        return $options;

    }


    public function getAppsListWithAllOptionNoPermissions(Request $request){

        if(!empty($request->sports_id) && $request->sports_id != "-1"){

            $appList = AppDetails::where('sports_id',$request->sports_id);
                
            $appList = $appList->get();

        }
        else{

            $appList = AppDetails::get();
        }

        $isDisabled = "";
        if(isset($request->disable_first_option)){
            if($request->disable_first_option == "true"){
                $isDisabled = "disabled";
            }
        }

        $options = '<option value="-1" '.$isDisabled.'> Select App</option>';
        if(!empty($appList) && sizeof($appList) > 0){
            $options .= '<option value="all"> All Apps </option>';
            foreach($appList as $obj){
                $options .= '<option value="'.$obj->id.'" >   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
            }
        }

        return $options;
    }

    public function getRemainingAppsOptions(Request $request){

        if(!empty($request->sports_id) && $request->sports_id != "-1"){
            $appList = AppDetails::where('sports_id',$request->sports_id)->get();
        }
        else{
            $appList = AppDetails::all();
        }

        $options = '<option value="-1">Select App </option>';
        if(!empty($appList)){
            foreach($appList as $obj){
                $options .= '<option value="'.$obj->id.'" >   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
            }
        }

        return $options;
    }

    public function getRemainingAppsForAppSettingOptions(Request $request){

        $appIdClause = "";
        if(!empty($request->sports_id) && $request->sports_id != "-1"){
            $appIdClause = " AND app.sports_id = ". $request->sports_id;
        }

        $appListHavingNoSetting = DB::select(DB::raw('
            SELECT *
            FROM app_details app
            WHERE NOT EXISTS (SELECT *
                                    FROM app_settings s
                                    WHERE s.app_detail_id = app.id
                            )
            '.$appIdClause.'
            '));


        $options = '<option value="">Select App </option>';
        if(!empty($appListHavingNoSetting)){
            foreach($appListHavingNoSetting as $obj){
                $options .= '<option value="'.$obj->id.'" >   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
            }
        }

        return $options;
    }

    public function generateAppDetailJson($appDetailId){

        $dataObject = AppDetails::select()
            ->where('id',$appDetailId)
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

                $obj->sportsId = $obj->sports_id;

                unset($obj->sports_id);
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

    public function updateProxyStatus(Request $request){
        $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($request->application_id,$roleAssignedApplications)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }

        $input['isProxyEnable'] = $request->is_proxy_enable;
        AppDetails::updateOrCreate(
            [
                'id' => $request->application_id
            ],
            $input);

        return response()->json(['success' => true]);

    }

    public function removeAllAppDetails(Request $request){
        $ids = $request->ids;
        $idsArray = explode(",",$ids);

        foreach($idsArray as $id){

            AdmobAds::where('app_detail_id',$id)->delete();

            SponsorAds::where('app_detail_id',$id)->delete();

            AppCredentials::where('app_detail_id',$id)->delete();

            FirebaseCredentials::where('app_detail_id',$id)->delete();

            BlockedApplication::where('application_id',$id)->delete();

            $getIcon = DB::table('app_details')->where('id',$id)->select('appLogo')->first();

            if(!empty($getIcon->appLogo)){
                $serverImagePath = 'uploads/apps/'.$getIcon->appLogo;
                removeServerImages($serverImagePath);
            }

        }

        DB::table("app_details")->whereIn('id',explode(",",$ids))->delete();

        return response()->json(['success' => "App Detail removed successfully" , "message" => "App Detail removed successfully"]);
    }



}