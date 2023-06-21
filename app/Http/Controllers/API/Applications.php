<?php

namespace App\Http\Controllers\API;

use App\Services\ApiTokenService;
use http\Env\Response;
use Illuminate\Http\Request;
use DB;
use Illuminate\Routing\Controller as BaseController;

class Applications extends BaseController
{
    public $imageUrl;
    public $sponsorImageUrl;
    public $apiTokenService;

    public function __construct()
    {
        $this->imageUrl = config('app.appsImagePath');
        $this->sponsorImageUrl = config('app.sponsoradsImagePath');
    }

    public function index(Request $request)
    {

        $response = [];
        $responseData = ['code'=>200,'message'=>'Success!'];

        try {

            $apiTokenService = new ApiTokenService;

            $proxyDetectionResponse = $apiTokenService->detectProxyIpRequest();

            if($proxyDetectionResponse['code'] != 200){
                return response()->json($proxyDetectionResponse,200);
            }

            $ipCheckResponse = $apiTokenService->blockedAppCountryVerification();

            if($ipCheckResponse['code'] != 200){
                return response()->json($ipCheckResponse,200);
            }

            $serviceResponse = $apiTokenService->authenticateToken();

            if($serviceResponse['code'] != 200){
                return response()->json($serviceResponse,200);
            }

            if(isset($request->package_id)){

                $dataObject = DB::table('app_details')
                    ->where('packageId',$request->package_id)
                    ->orderBy('id','asc');

                if($dataObject->exists()){
                    $dataObject = $dataObject->get();


                    $app_detail_id = $dataObject[0]->id;


                    $adsListObject = DB::table('admob_ads')
                        ->select(['id AS admobAdId','app_detail_id AS appDetailId','adName','adUId','isAdShow'])
                        ->where('app_detail_id',$app_detail_id)->get();


                    $sponsorListObject = DB::table('sponsor_ads')
                        ->select(['id AS sponsorAdId','app_detail_id AS appDetailId','adName','adUrlImage','clickAdToGo','isAdShow'])
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

                        dd($sponsorList);

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
                        $obj->pagesCounter = (int) ($obj->pagesCounter);

                        $obj->isAdmobOnline = getBoolean($obj->isAdmobOnline);
                        $obj->isAdsInterval = getBoolean($obj->isAdsInterval);
                        $obj->isBannerPlayer = getBoolean($obj->isBannerPlayer);

                        $obj->isMessageDialogDismiss = getBoolean($obj->isMessageDialogDismiss);
                        $obj->isOnlineCode = getBoolean($obj->isOnlineCode);
                        $obj->isPagesAlgo = getBoolean($obj->isPagesAlgo);

                        $obj->isStartAppAdsShow = getBoolean($obj->isStartAppAdsShow);
                        $obj->isStartAppOnline = getBoolean($obj->isStartAppOnline);
                        $obj->isScreenAdsLimit = getBoolean($obj->isScreenAdsLimit);
                        $obj->isSuspendApp = getBoolean($obj->isSuspendApp);
                        $obj->appLogo = $this->imageUrl.$obj->appLogo;

                    }

                    $responseData['data'] = $dataObject[0];

                    $responseData['data']->admobAds = ($obj->isAdmobAdsShow) ? $adsList : [];

                    $obj->isAdmobAdsShow = getBoolean($obj->isAdmobAdsShow);

                    $responseData['data']->sponsorAds = ($obj->isSponsorAdsShow) ? $sponsorList : [];

                    $obj->isSponsorAdsShow = getBoolean($obj->isSponsorAdsShow);

                }
                else{
                    $responseData['code'] = 400;
                    $responseData['message'] = 'Application not found!';
                    $responseData['data'] = null;
                }


            }
            else{
                $responseData['code'] = 400;
                $responseData['message'] = 'Package Id required!';
                $responseData['data'] = null;
            }

            return response()->json($responseData,200);

        } catch (\Throwable $th) {

            $response['message'] = $th->getMessage();
            return response()->json($response,500);

        }
    }


    public function getStreamToken(Request $request)
    {

        $response = [];
        $responseData = ['code'=>200,'message'=>'Success!'];

        try {

            $apiTokenService = new ApiTokenService;

            $proxyDetectionResponse = $apiTokenService->detectProxyIpRequest();

            if($proxyDetectionResponse['code'] != 200){
                return response()->json($proxyDetectionResponse,200);
            }

            $ipCheckResponse = $apiTokenService->blockedAppCountryVerification();

            if($ipCheckResponse['code'] != 200){
                return response()->json($ipCheckResponse,200);
            }

            $serviceResponse = $apiTokenService->authenticateToken();

            if($serviceResponse['code'] != 200){
                return response()->json($serviceResponse,200);
            }

            if($request->header('PackageId') &&
                isset($request->server_id) &&  !empty($request->server_id)  &&
                isset($request->stream_name) &&  !empty($request->stream_name) ) {

                $serverType = DB::table("servers")->select(['server_types.label as label'])
                    ->join('server_types', function ($join) {
                        $join->on('server_types.id', '=', 'servers.server_type_id');
                    })
                    ->where('servers.id',$request->server_id);

                if($serverType->exists()) {

                    $response = [];

                    $packageId = $request->header('PackageId');
                    $streamName = $request->stream_name;
                    $ipAddress = $request->header('IpAddress');

                    /*** get start & end time ***/

                    $lifeTime = 3600 * 3;
                    $milliSeconds = floor(microtime(true) * 1000);
                    $userStartTime = (int) ($milliSeconds / 1000) - 300;
                    $userEndTime =  $userStartTime + $lifeTime;

                    $userSalt = $this->generateSalt(16);

                    $appDetails = DB::table('app_details')
                        ->where('packageId', $packageId)
                        ->select('id','account_id');

                    /*** Get Key From Database By using Package ID ***/

                    if ($appDetails->exists()) {
                        $appId = $appDetails->first()->id;
                        $accountId = $appDetails->first()->account_id;
                        $appCredentials = DB::table('app_credentials')
                            ->select('token_key')
                            ->where('account_id', $accountId);

                        if ($appCredentials->exists()) {
                            $appCredentials = $appCredentials->first();
                            $tokenKey = $appCredentials->token_key;;
                        }
                    }

                    $serverLabel = $serverType->first()->label;

                    if($serverLabel == "flussonic"){
                        $userHashString = $streamName . $ipAddress . $userStartTime . $userEndTime . $tokenKey . $userSalt;
                    }
                    else{
                        $userHashString = "/" . $streamName  . "/" . "playlist.m3u8" . $tokenKey . $userEndTime . $ipAddress;
                    }

                    $hashSha256Generated = hash('sha256', $userHashString);

                    $saltTimeCombo = $userSalt . '-' . $userEndTime . '-'. $userStartTime;
                    $responseData['data'] = ['ip' => $ipAddress, 'token' => $hashSha256Generated , 'salt' => $saltTimeCombo];

                    return response()->json($responseData, 200);
                }
            }

            $responseData['code'] = 400;
            $responseData['message'] = 'Invalid Request';
            $responseData['data'] = null;

            return response()->json($responseData,200);

        } catch (\Throwable $th) {

            $response['message'] = $th->getMessage();
            return response()->json($response,500);

        }
    }


    public function generateSalt($stringLength){

        $alphaNumeric = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $pass = [];
        $alphaLength = strlen($alphaNumeric) - 1;
        for ($i = 0; $i < $stringLength; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphaNumeric[$n];
        }
        return implode($pass);
    }

}
