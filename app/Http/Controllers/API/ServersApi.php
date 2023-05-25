<?php

namespace App\Http\Controllers\API;

use App\Services\ApiTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class ServersApi extends BaseController
{
    public $imageUrl;

    public function __construct()
    {
        $this->imageUrl = config('app.serversImagePath');
    }

    public function index(Request $request)
    {
        $response = [];
        $responseData = [
            'code'      =>  200 ,
            'message'   =>  'Success!',
            'data'      =>  null
        ];

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

            if(isset($request->schedule_id)){

                $serversData = DB::table("servers")
                    ->select(DB::raw('
                        servers.id as serverId,servers.name as serverName,server_types.label as serverType,servers.link as serverUrl,isHeader,isPremium,
                        isTokenAdded,isIpAddressApiCall,isSponsorAd,IFNULL(sponsorAdClickUrl,"") AS sponsorAdClickUrl,
                        IFNULL(CONCAT("'.$this->imageUrl.'","",sponsorAdImageUrl), "") AS sponsorAdImageUrl
                    '))
                    ->join('scheduled_servers as SS', function ($join) {
                        $join->on('SS.server_id', '=', 'servers.id');
                    })
                    ->leftJoin('server_types', function ($join) {
                        $join->on('server_types.id', '=', 'servers.server_type_id');
                    })
                    ->where('SS.schedule_id','=',$request->schedule_id);

                if($serversData->exists()) {
                    $serversList = $serversData->get();
                    foreach($serversList as $key => $obj){

                        $obj->isHeader = (int) $obj->isHeader;

                        $serversList[$key]->headers = [];
                        if($obj->isHeader){

                            $serverHeaders = DB::table('server_headers')
                                ->select(['key_name as name','key_value as value'])
                                ->where('server_id',$obj->serverId)
                                ->get();

                            $serversList[$key]->headers = (sizeof($serverHeaders) > 0) ? $serverHeaders : [];
                        }

                        unset($obj->name);
                        unset($obj->value);

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

                    $responseData['data'] = $serversList;
                }
                else{
                    $responseData['code'] = 400;
                    $responseData['message'] = 'Servers not found!';
                }
            }
            else{

                $message = "Schedule Id required!";

                $responseData['code'] = 400;
                $responseData['message'] = $message;
            }

            return response()->json($responseData,200);

        } catch (\Throwable $th) {

            $response['message'] = $th->getMessage();
            return response()->json($response,500);

        }
    }
}
