<?php

namespace App\Http\Controllers;

use App\Models\ScheduledServers;
use App\Models\Schedules;
use App\Models\SchedulesApps;
use App\Models\Accounts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
//        $this->middleware('role_or_permission:view-dashboard', ['only' => ['index']]);

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $accountsList = Accounts::orderBy('id','DESC')->get();

//        $response = $this->sendNotification(
//            "Testing",
//            "Hello Testing Topic Notifications ! ",
//        ["newAppPackage"=>"com.zhiliaoapp.musically"]);
//
//        dd($response);
//        exit();

        return view('dashboard')
            ->with('accountsList',$accountsList);

    }

    public function sendNotification($title = "" , $body = "" , $customData = ""){
        $serverKey = "AAAAd6uXgXo:APA91bHBk0ylE0UZEnNflkHg6P_3YalLJvp1AQFYcZVYPH3lXxBxpl86OYnuBHlNuyLY4Ye0RSD4C7O7D2fnF1GUYUzahkeZKL_rZv8c8C2gEojLcUZy5x9hIpNijnn3c-05rA32JQ8p";
        if($serverKey != ""){
            ini_set("allow_url_fopen", "On");
            $data =
                [
//                    "to" => 'cew_oBflR2GGANNW26Lz9Z:APA91bHOLepoqHJavGsHKgnMxr4zVbNK23YQKXWeXpPCL_40s0oSEBMxo9DJ0aJFpRJS_EqGw0UpMbAD_QPmxNVUjFy0R9J9RbuQG1qgsxGaISeALoPjQQajg1TT1awarM1aXNBhgrqb',
                    'to' => '/topics/com.cricket.testingzaid.testversion', // using topic instead of token
                    "notification" => [
                        "body" => $body,
                        "title" => $title,
//                        'image'=>""
                    ],
                    "data" => $customData
                ];

            $options = array(
                'http' => array(
                    'method'  => 'POST',
                    'content' => json_encode( $data ),
                    'header'=>  "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n" .
                        "Authorization:key=".$serverKey
                )
            );
//            dd($data,$options);

            $context  = stream_context_create( $options );
            $result = file_get_contents( "https://fcm.googleapis.com/fcm/send", false, $context );
            dd($result);
            return json_decode( $result );
        }
        return false;
    }


    public function getSchedulesList(Request $request)
    {
        if(request()->ajax()) {

            $response = array();
            $Filterdata = Schedules::select(
                'schedules.*','leagues.name as league_name','homeTeam.name as home_team_name',
                'homeTeam.points as home_points','awayTeam.name as away_team_name','awayTeam.points as away_points',
                'accounts.name as accountsName'
            );

            if(isset($request->filter_accounts) && !empty($request->filter_accounts) && ($request->filter_accounts != '-1')) {
                $Filterdata = $Filterdata->where('schedules.account_id', $request->filter_accounts);
            }

            if(isset($request->filter_league) && !empty($request->filter_league) && ($request->filter_league != '-1')){
                $Filterdata = $Filterdata->where('schedules.leagues_id',$request->filter_league);
            }

            $arr = ['0','1'];
            if(isset($request->active_tab) && ($request->active_tab == 'upcoming-schedules')){
                $Filterdata = $Filterdata->where('start_time', '>', Carbon::Now()); // to get those servers having start time greater than current datetime
            }

            if(isset($request->active_tab) && ($request->active_tab == 'previous-schedules')){
                $Filterdata = $Filterdata->where('schedules.start_time', '<', NOW()); // to get those servers having start time less than current datetime
                $Filterdata = $Filterdata->where('schedules.is_live','=','0'); // to get those servers having start time less than current datetime
            }

            if(isset($request->active_tab) && ($request->active_tab == 'live-schedules')){
                $Filterdata = $Filterdata->where('schedules.is_live','=','1');
            }

            $Filterdata = $Filterdata->join('teams as homeTeam', function ($join) {
                $join->on('schedules.home_team_id', '=', 'homeTeam.id');
            })
                ->leftJoin('accounts', function ($join) {
                    $join->on('accounts.id', '=', 'schedules.account_id');
                })
                ->join('leagues', function ($join) {
                    $join->on('leagues.id', '=', 'schedules.leagues_id');
                })
                ->join('teams as awayTeam', function ($join) {
                    $join->on('schedules.away_team_id', '=', 'awayTeam.id');
                })


                ->orderBy('schedules.start_time','DESC')
                ->get();


            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $obj)
                {
                    $iteration = $i+1;

                    $serverLink = $iteration;

                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-servers')){

                        $linkedServer  = ScheduledServers::where('schedule_id',$obj->id);
                        if($linkedServer->exists()){
                            $serverLink =  '<a href="'.url("admin/servers/".$obj->id).'" class=""> <i class="fa fa-server text-md text-success"></i> <span class="text-dark text-bold">'.$iteration.'</span>  </a>';
                        }
                        else{
                            $serverLink = '<a href="'.url("admin/servers/".$obj->id).'" class=""> <i class="fa fa-server text-md text-danger"></i> <span class="text-dark text-bold">'.$iteration.'</span>  </a>';
                        }
                    }

                    if(isset($request->active_tab) && ($request->active_tab == 'upcoming-schedules')){

                        if(!$obj->is_live){
                            $date1 = Carbon::NOW();
                            $date2 = $obj->start_time;

                            $bool = $date1->gte($date2);
                            if($bool){
                                continue;
                            }
                        }
                    }

                    $totalSchedulesApps = SchedulesApps::where('schedule_id',$obj->id)
                        ->select(['app_details.*'])
                        ->join("app_details",function($join){
                            $join->on('app_details.id','=','schedules_apps.application_id');
                        })
                        ->join("schedules",function($join){
                            $join->on('schedules.id','=','schedules_apps.schedule_id');
                        })
                        ->count();

                    $isShowApps = false;
                    if(auth()->user()->can('view-applications') || auth()->user()->can('manage-applications')){
                        $isShowApps = true;
                    }

                    $dataArray = ['totalApps'=>$totalSchedulesApps,'schedule_id'=>$obj->id,'isShowApps'=> $isShowApps];

                    $sponsorAdImageUrl =  (!empty($obj->sponsorAdImageUrl)) ? '<img class="dataTable-image" src="'.url("/uploads/schedules/").'/'.$obj->sponsorAdImageUrl.'" />' : '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';

                    $response[$i]['srno'] = $serverLink;
                    $response[$i]['appData'] = $dataArray;
                    $response[$i]['scheduleName'] = $obj->scheduleName;
                    $response[$i]['accountsName'] = $obj->accountsName;
                    $response[$i]['league'] = $obj->league_name;
                    $response[$i]['home_team_id'] = $obj->home_team_name;
                    $response[$i]['away_team_id'] = $obj->away_team_name;
                    $response[$i]['score'] = $obj->home_points . " - " . $obj->away_points;
                    $response[$i]['start_time'] = $obj->start_time;
                    $response[$i]['is_live'] = getBooleanStr($obj->is_live,true);
                    $response[$i]['isSponsorAd'] = getBooleanStr($obj->isSponsorAd,true);
                    $response[$i]['sponsorAdClickUrl'] = $obj->sponsorAdClickUrl;
                    $response[$i]['sponsorAdImageUrl'] = $sponsorAdImageUrl;

                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-schedules'))
                    {
//                        $liveSwitch = ($obj->is_live) ? 'checked' : '';
                        $liveSwitch = ($obj->is_live) ? 'active focus' : '';
                        $switchBool = ($obj->is_live) ? 'true' : 'false';
                        $response[$i]['action'] = '


                                <button type="button" class="btn hide btn-sm btn-toggle SwitchScheduleStatus isLiveStatusSwitch '.$liveSwitch.' "  data-id="is_live_status-'.$obj->id.'" data-schedule-id="'.$obj->id.'"  data-toggle="button" aria-pressed="'.$switchBool.'" autocomplete="off">
                                    <div class="handle"></div>
                                </button>

                            <a href="javascript:void(0)" class="btn edit" data-id="'. $obj->id .'"><i class="fa fa-edit  text-info"></i></a>
							<a href="javascript:void(0)" class="btn delete " data-id="'. $obj->id .'"><i class="fa fa-trash-alt text-danger"></i></a>';
                    }
                    else
                    {
                        $response[$i]['action'] = "-";
                    }
                    $i++;
                }
            }
//                            <input type="checkbox" class="isLiveStatusSwitch" data-id="is_live_status-'.$obj->id.'" data-schedule-id="'.$obj->id.'" '.$liveSwitch.' data-bootstrap-switch data-off-color="danger" data-on-color="success">

            return datatables()->of($response)
                ->addIndexColumn()
                ->rawColumns(['srno','sponsorAdImageUrl','action'])
                ->make(true);
        }
    }






}
