<?php

namespace App\Http\Controllers\API;

use App\Services\ApiTokenService;
use App\Models\Leagues;
use App\Models\ScheduledServers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class LeaguesApi extends BaseController
{
    public $imageUrl;

    public function __construct()
    {
        $this->imageUrl = config('app.leaguesImagePath');
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

            if(isset($request->sport_id)){

                $data = DB::table('leagues')
                    ->where('leagues.sports_id',$request->sport_id)
                    ->select(DB::raw('
                        leagues.id AS leagueId,name AS leagueName,leagues.sports_id AS sportsId,IFNULL(CONCAT("'.$this->imageUrl.'","",icon),"") AS leagueIcon ,
                        leagues.isSponsorAd AS isSponsorAd, IFNULL(leagues.sponsorAdClickUrl,"") AS sponsorAdClickUrl,
                        IFNULL(CONCAT("'.$this->imageUrl.'","",leagues.sponsorAdImageUrl),"") AS sponsorAdImageUrl'
                    ));

                $appDetail = DB::table("app_details")->select(['id as application_id'])->where('packageId',$request->header('PackageId'))->first();

                $leaguesArray = [];
                if($data->exists()){
                    $data = $data->orderBy('leagues.start_datetime','ASC')->get();
                    foreach($data as $index => $arr) {
                        $totalSchedules = 0;
                        $totalSchedules = DB::table('schedules')
                            ->join('schedules_apps' , function ($join) {
                                $join->on('schedules_apps.schedule_id','=','schedules.id');
                            })
                            ->where('schedules_apps.application_id',$appDetail->application_id)
                            ->where('leagues_id',$arr->leagueId)
                            ->where('is_live','1')->count();

                        if($totalSchedules > 0){
                            $arr->totalSchedules = $totalSchedules;
                            $arr->isSponsorAd = (int) $arr->isSponsorAd;
                            $arr->isSponsorAd = getBoolean($arr->isSponsorAd);
                            $leaguesArray[] = $arr;
                        }
                    }

                    if(sizeof($leaguesArray) > 0){
                        $response['data'] = $leaguesArray;
                    }
                    else{
                        $response['code'] = 400;
                        $response['message'] = 'Leagues data not found!';
                        $response['data'] = null;
                    }
                }
                else{
                    $response['code'] = 400;
                    $response['message'] = 'Leagues data not found!';
                    $response['data'] = null;
                }
            }
            else{
                $response['code'] = 400;
                $response['message'] = 'Sport Id required!';
                $response['data'] = null;
            }

            return response()->json($response,200);


        } catch (\Throwable $th) {

            $response['message'] = $th->getMessage();
            return response()->json($response,500);

        }
    }
}
