<?php

namespace App\Http\Controllers\API;

use App\Services\ApiTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class TeamsApi extends BaseController
{
    public $imageUrl;

    public function __construct()
    {
        $this->imageUrl = config('app.teamsImagePath');
    }

    public function index(Request $request)
    {
        $response = [
            'code'      =>  200 ,
            'message'   =>  'Success!',
            'data'      =>  null
        ];

        $responseData = null;

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

            if(isset($request->league_id)){

                $data = Teams::where('leagues_id',$request->league_id)
                    ->select(DB::raw('id,name AS teamName,sports_id AS sportsId,leagues_id AS leagueId,
                    points AS teamScore, IFNULL(CONCAT("'.$this->imageUrl.'","",icon),"") AS icon'));

                if($data->exists()){
                    $data = $data->get();
                    $responseData['TeamsList'] = $data;
                }
                else{
                    $response['code'] = 400;
                    $response['message'] = 'Teams data not found!';
                }
            }
            else{
                $response['code'] = 400;
                $response['message'] = 'League Id required!';
            }

            $response['data'] = $responseData;
            return response()->json($response,200);

        } catch (\Throwable $th) {

            $response['message'] = $th->getMessage();
            return response()->json($response,500);

        }
    }
}
