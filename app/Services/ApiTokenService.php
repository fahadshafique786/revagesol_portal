<?php

namespace App\Services;

use DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Stevebauman\Location\Facades\Location;

class ApiTokenService {

    protected  $proxyApiUrl;
    protected  $proxyApiResponseType;
    protected  $proxyApiKey;
    protected  $proxyApiFields;

    public function __construct()
    {
        $this->proxyApiUrl = config('app.proxy_api_url');
        $this->proxyApiResponseType = config('app.proxy_api_response_type');
        $this->proxyApiKey = config('app.proxy_api_key');
        $this->proxyApiFields = config('app.proxy_api_fields');
    }

    public function detectProxyIpRequest(){

        try {
            $application = getAppByPackageId(request()->header('PackageId'));

            if(!empty($application) && $application->isProxyEnable){
                if(request()->header('ipAddress')){

                    $userIpAddress = request()->header('ipAddress');

                    $apiRequestURL = $this->proxyApiUrl.$this->proxyApiResponseType.$userIpAddress.'?key='.$this->proxyApiKey.'&fields='.$this->proxyApiFields;

                    $client = new Client(
                        [
                            'verify' => false
                        ]);
                    $request = new Request('GET', $apiRequestURL);
                    $res = $client->sendAsync($request)->wait();

                    $apiResponse = json_decode($res->getBody());

                    if(isset($apiResponse->status) && !empty($apiResponse->status ==  "success")){

                        if((isset($apiResponse->proxy) && !empty($apiResponse->proxy == true)) || (isset($apiResponse->hosting) && !empty($apiResponse->hosting == true))){
                            $response['code'] = 205;
                            $response['message'] = 'Bad Request.The server can’t return a response due to an error on the client’s end';
                            $response['data'] = null;
                        }
                        else{
                            $response['code'] = 200;
                            $response['message'] = "Success!";
                            $response['data'] = null;
                        }
                    }
                    else{
                        $response['code'] = 200;
                        $response['message'] = "Success!";
                        $response['data'] = null;
                    }
                }
            }
            else{
                $response['code'] = 200;
                $response['message'] = "Success!";
                $response['data'] = null;
            }

            return $response;

        } catch (\Exception $e) {

            $response['code'] = 401;
            $response['message'] = $e->getMessage();
            $response['data'] = null;

            response()->json($response,200)->send();
            exit();
        }
    }

    public function blockedAppCountryVerification()
    {
        $application = getAppByPackageId(request()->header('PackageId'));

        if(!empty($application) && $application->isProxyEnable){

            $bool = checkAppBlockOnAllCountries(request()->header('PackageId'));

            if ($bool == false) {
                $response['code'] = 204;
                $response['message'] = "Service Not Available!";
                $response['data'] = null;

                return $response;

            }
            else if ($bool == null) {

                $response['code'] = 400;
                $response['message'] = "BAD Request!";
                $response['data'] = null;

                return $response;

            }
            else {

                $userIpAddress = request()->header('ipAddress');

                $apiRequestURL = $this->proxyApiUrl . $this->proxyApiResponseType . $userIpAddress . '?key=' . $this->proxyApiKey . '&fields=' . $this->proxyApiFields;

                $client = new Client(
                    [
                        'verify' => false
                    ]);

                $request = new Request('GET', $apiRequestURL);
                $res = $client->sendAsync($request)->wait();

                $apiResponse = json_decode($res->getBody());

                if (isset($apiResponse->status) && !empty($apiResponse->status == "success")) {

                    if ((isset($apiResponse->countryCode) && !empty($apiResponse->countryCode))) {

                        $countryCode = $apiResponse->countryCode;

                        $application = getAppByPackageId(request()->header('PackageId'));

                        $blockedAppCountries = DB::table('blocked_applications')
                            ->select(DB::raw("GROUP_CONCAT(countries.country_code) as country_codes"))
                            ->join('countries', function ($join) {
                                $join->on('countries.id', '=', 'blocked_applications.country_id');
                            })
                            ->where('application_id', $application->application_id)
                            ->first();

                        $countryCodesCollection = explode(',', $blockedAppCountries->country_codes);

                        $validityCheck = in_array($countryCode, $countryCodesCollection);

                        if (!$validityCheck) {
                            $response['code'] = 200;
                            $response['message'] = "Success!";
                            $response['data'] = null;
                        } else {
                            $response['code'] = 204;
                            $response['message'] = "Service Not Available!";
                            $response['data'] = null;
                        }

                    } else {
                        $response['code'] = 200;
                        $response['message'] = "County code not found!";
                        $response['data'] = null;
                    }
                }
                else {
                    $response['code'] = 200;
                    $response['message'] = "3P-API Response not found!";
                    $response['data'] = null;
                }
            }
        }
        else{

            $response['code'] = 200;
            $response['message'] = "Proxy Check Disable";
            $response['data'] = null;

        }


        return $response;
    }

    public function authenticateToken(){

        try {

            $token = null;
            $headers = apache_request_headers();

            if(
                isset($headers['Authorization']) && !empty($headers['Authorization'])  &&
                isset($headers['Accountid']) && !empty($headers['Accountid'])  &&
                isset($headers['Packageid']) && !empty($headers['Packageid']) &&
                isset($headers['Ipaddress']) && !empty($headers['Ipaddress'])
            ){

                $response =  [];
                $streamKey = ""; $secretKey = "";

                $authToken = $headers['Authorization'];
                $packageId = $headers['Packageid'];
                $accountId = $headers['Accountid'];
                $ipAddress = $headers['Ipaddress'];
                $headerVersionCode = (isset($headers['Versioncode'])) ? (int) $headers['Versioncode'] : 0;

                /*** Split Header Token into the Array ***/

                $authTokenSplitedArray = explode("-",$authToken);
                $userStartTime = (isset($authTokenSplitedArray[3])) ? $authTokenSplitedArray[3] :  "";
                $userEndTime = (isset($authTokenSplitedArray[2])) ? $authTokenSplitedArray[2]   :  "";
                $userSalt = (isset($authTokenSplitedArray[1])) ?  $authTokenSplitedArray[1]     :  "";


                $appDetails =   DB::table('app_details')
                    // ->where('packageId',$packageId)
                    ->where('account_id',$accountId)
                    ->select('id');

                /*** Get Key From Database By using Package ID ***/

                if($appDetails->exists()){
                    $appId = $appDetails->first()->id;
                    $appCredentials =   DB::table('app_credentials')
                        ->select('stream_key','server_auth_key','appSigningKey','versionCode')
                        ->where('app_detail_id',$appId);

                    if($appCredentials->exists()){
                        $appCredentials = $appCredentials->first();
                        $streamKey = $appCredentials->stream_key;

                        $appSetting = DB::table('app_settings')
                            ->select('isAppSigningKeyUsed')
                            ->where('app_detail_id',$appId)->first();

                        $secretKey = (($appSetting->isAppSigningKeyUsed == "1" || $appSetting->isAppSigningKeyUsed == "1") && ($headerVersionCode >= $appCredentials->versionCode ) ) ? $appCredentials->appSigningKey :  $appCredentials->server_auth_key ;
                    }
                    else{
                        $appCredentials = 0;
                    }

                }


                $userHashString = $streamKey.$ipAddress.$userStartTime.$userEndTime.$secretKey.$userSalt;
                $hashSha1Generated = sha1($userHashString);
                $hashSha256Generated =  hash('sha256',$userHashString);

                $ourSha1GeneratedToken = $hashSha1Generated.'-'.$userSalt.'-'.$userEndTime.'-'.$userStartTime;
                $ourSha256GeneratedToken = $hashSha256Generated.'-'.$userSalt.'-'.$userEndTime.'-'.$userStartTime;

                if($ourSha1GeneratedToken != $authToken && $ourSha256GeneratedToken != $authToken) {
                    $response['code'] = 403;
                    $response['message'] = "Invalid Token!";
                    $response['data'] = null;
                    return $response;
                }
            }
            else{

                $response['code'] = 401;
                $response['message'] = "Unauthorized Request! 786";
                $response['data'] = null;
                dd($response,$headers);

                return $response;
            }

            $response['code'] = 200;
            $response['message'] = "Success";
            $response['data'] = null;
            return $response;

        } catch (\Exception $e) {

            $response['code'] = 401;
            $response['message'] = $e->getMessage();
            $response['data'] = null;
            response()->json($response,200)->send();
            exit();
        }

    }

    public function oldCode()
    {

        /*            $locationDetails = Location::get(request()->header('ipAddress'));

                    if(!empty($locationDetails->countryCode)){

                        $countryCode = $locationDetails->countryCode;

                        $application = getAppByPackageId(request()->header('PackageId'));

                        $blockedAppCountries = DB::table('blocked_applications')
                            ->select(DB::raw("GROUP_CONCAT(countries.country_code) as country_codes"))
                            ->join('countries',function($join){
                                $join->on('countries.id','=','blocked_applications.country_id');
                            })
                            ->where('application_id',$application->application_id)
                            ->first();

                        $countryCodesCollection = explode(',',$blockedAppCountries->country_codes);

                        $validityCheck = in_array($countryCode,$countryCodesCollection);

                        if(!$validityCheck){
                            $response['code'] = 200;
                            $response['message'] = "Success!";
                            $response['data'] = null;
                        }
                        else{
                            $response['code'] = 403;
                            $response['message'] = "Service Not Available!";
                            $response['data'] = null;
                        }
                    }
                    else{
                        $response['code'] = 200;
                        $response['message'] = 'Country Code not found';
                        $response['data'] = null;
                    }

                }


                return $response;

            }*/

    }
}
