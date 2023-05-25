<?php

namespace App\Http\Controllers\API;

use App\Services\ApiTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;

class SchedulesApi extends BaseController
{
    public $imageUrl;
    public $teamsImageUrl;

    public function __construct()
    {
        $this->imageUrl = config('app.schedulesImagePath');
        $this->teamsImageUrl = config('app.teamsImagePath');
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

            if(isset($request->sport_id) && isset($request->league_id)  && isset($request->type) ) {

                $appDetail = DB::table("app_details")->select(['id as application_id'])->where('packageId',$request->header('PackageId'))->first();

                $schedulesData = DB::table("schedules")
                    ->select(DB::raw('
                        schedules.id as scheduleId,
                        schedules.scheduleName,
                        homeTeam.name as homeTeamName,homeTeam.points as homeTeamScore,
                        IFNULL(CONCAT("'.$this->teamsImageUrl.'","",homeTeam.icon),"") as homeTeamImage,
                        IFNULL(CONCAT("'.$this->teamsImageUrl.'","",awayTeam.icon),"") as awayTeamImage,
                        awayTeam.name as awayTeamName,awayTeam.points as awayTeamScore,
                        schedules.isSponsorAd , IFNULL(schedules.sponsorAdClickUrl,"") AS sponsorAdClickUrl,
                        IFNULL(CONCAT("'.$this->imageUrl.'","",schedules.sponsorAdImageUrl),"") AS sponsorAdImageUrl
                        '))
                    ->join('teams as homeTeam', function ($join) {
                        $join->on('schedules.home_team_id', '=', 'homeTeam.id');
                    })
                    ->join('teams as awayTeam', function ($join) {
                        $join->on('schedules.away_team_id', '=', 'awayTeam.id');
                    })
                    ->join('schedules_apps' , function ($join) {
                        $join->on('schedules_apps.schedule_id','=','schedules.id');
                    })
                    ->where('schedules_apps.application_id',$appDetail->application_id)
                    ->where('schedules.sports_id',$request->sport_id)
                    ->where('schedules.leagues_id',$request->league_id);

                if(isset($request->type) && $request->type == 'live'){
                    $schedulesData = $schedulesData->where('schedules.is_live','1');
                }

                else if(isset($request->type) && $request->type == 'upcoming'){
                    $schedulesData = $schedulesData->where('start_time', '>', Carbon::Now()); // to get those servers having start time greater than current datetime
                }

                else if(isset($request->type) && $request->type == 'previous'){
                    $schedulesData = $schedulesData->where('schedules.start_time', '<', NOW());     // to get those servers having start time less than current datetime
                    $schedulesData = $schedulesData->where('schedules.is_live','=','0');            // to get those servers having start time less than current datetime
                }

                $schedulesData = $schedulesData->orderBy('schedules.start_time','ASC');


                if($schedulesData->exists()) {
                    $schedulesList = $schedulesData->get();
                    foreach($schedulesList as $key => $obj){
                        $obj->isSponsorAd = (int) $obj->isSponsorAd;
                        $obj->isSponsorAd = getBoolean($obj->isSponsorAd);
                    }
                    $responseData['application'] = $appDetail;
                    $responseData['data'] = $schedulesList;
                }
                else{
                    $responseData['code'] = 400;
                    $responseData['message'] = 'Schedules not found!';
                    $responseData['data'] = null;
                }


            }
            else{

                if(isset($request->sport_id)){

                    if(!isset($request->league_id)){
                        $message = "League Id required!";
                    }
                    else{
                        if(!isset($request->type)){
                            $message = "Schedule Type required!";
                        }
                    }
                }
                else{
                    $message = "Sport Id required!";
                }

                $responseData['code'] = 400;
                $responseData['message'] = $message;

                $responseData['data'] = null;

            }

            return response()->json($responseData,200);


        } catch (\Throwable $th) {

            $response['message'] = $th->getMessage();
            return response()->json($response,500);

        }
    }
}
