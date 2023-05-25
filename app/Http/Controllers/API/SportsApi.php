<?php

namespace App\Http\Controllers\API;

use App\Services\ApiTokenService;
use Illuminate\Http\Request;
use DB;
use Illuminate\Routing\Controller as BaseController;

class SportsApi extends BaseController
{
    public $imageUrl;

    public function __construct(){

        $this->imageUrl = config('app.sportsImagePath');
    }

    public function index()
    {
        $response = [
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

            $responseData = null;

            $data = DB::table('sports')
                ->select(DB::raw('
                    id as sportsId,name as sportsName,
                    IFNULL(CONCAT("'.$this->imageUrl.'","",icon),"") AS icon, sports_type AS sportsType,
                    image_required AS isImageRequired, multi_league AS isMultiLeague
                '))
                 ->orderBy('id','asc');

            if($data->exists()) {
                $data = $data->get();
                foreach($data as $index => $arr){
                    $arr->isImageRequired = (int) $arr->isImageRequired;
                    $arr->isImageRequired = getBoolean($arr->isImageRequired);

                    $arr->isMultiLeague = (int) $arr->isMultiLeague;
                    $arr->isMultiLeague = getBoolean($arr->isMultiLeague);
                }

                $response['data'] = $data;
            }
            else{
                $response['code'] = 400;
                $response['message'] = 'Sports List not found!';
                $response['data'] = null;
            }

            return response()->json($response,200);

        } catch (\Throwable $th) {

            $response['message'] = $th->getMessage();
            return response()->json($response,500);

        }
    }

}
