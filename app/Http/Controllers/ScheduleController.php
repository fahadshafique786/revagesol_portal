<?php

namespace App\Http\Controllers;

use App\Models\AppDetails;
use App\Models\SchedulesApps;
use Illuminate\Http\Request;
use App\Models\Sports;
use App\Models\Leagues;
use App\Models\Teams;
use App\Models\Schedules;
use App\Models\ScheduledServers;
use Response;
use DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->sports_id = null;
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-schedules', ['only' => ['index','fetchschedulesdata']]);
        $this->middleware('role_or_permission:super-admin|manage-schedules',['only' => ['edit','store','destroy','deleteAll']]);
    }

    public function index(Request $request, $sports_id)
    {
        $sportData = Sports::where('id',$sports_id)->first();
        $applicationList = AppDetails::all()->where('sports_id',$sports_id);
        if(!empty($sportData)){
            $leaguesList = Leagues::where('sports_id',$sports_id)->orderBy('start_datetime','ASC')->get();
            $teamsList = Teams::where('sports_id',$sports_id)->orderBy('id','DESC')->get();

            return view('schedules')
                ->with('applications',$applicationList)
                ->with('sports_id',$sports_id)
                ->with('sportData',$sportData)
                ->with('leaguesList',$leaguesList)
                ->with('teamsList',$teamsList);
        }
        else{
            abort(404);
        }

    }

    public function store(Request $request , $sports_id)
    {
        if(!empty($request->id))
        {
            $this->validate($request, [
//                'scheduleName' => 'required|unique:schedules,scheduleName,'.$request->id,
                'scheduleName' => 'required',
                'home_team_id' => 'required',
                'away_team_id' => 'required',
                'start_time' => 'required',
                'application_ids' => 'required|array',
            ]);

            $validation = Schedules::where('scheduleName',$request->scheduleName)
                ->where('sports_id',$sports_id)
                ->where('id','!=',$request->id);

            $validationResponse = [];

            if($validation->exists()){
                $validationResponse = [];
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['scheduleName'] = "The name has already been taken for selected sport!";

                return Response::json($validationResponse,422);
            }


        }
        else
        {
            /***
             * Create Action
             **/

            $this->validate($request, [
                'scheduleName' => 'required',
//                'scheduleName' => 'required',
                'home_team_id' => 'required',
                'away_team_id' => 'required',
                'start_time' => 'required',
                'application_ids' => 'required|array',
            ]);

            $validation = Schedules::where('scheduleName',$request->scheduleName)
                ->where('sports_id',$sports_id);


//            dd($validation->exists(),$sports_id,$request->scheduleName);
            $validationResponse = [];

            if($validation->exists()){
                $validationResponse = [];
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['scheduleName'] = "The name has already been taken";

                return Response::json($validationResponse,422);
            }

        }


        $input = array();
        $input['scheduleName'] = $request->scheduleName;
        $input['leagues_id'] = $request->leagues_id;
        $input['home_team_id'] = $request->home_team_id;
        $input['away_team_id'] = $request->away_team_id;
        $input['start_time'] = $date_string = $request->start_time;
        $input['sports_id'] = $sports_id;


        if($request->isSponsorAd){
            $input['sponsorAdClickUrl'] = $request->sponsorAdClickUrl;

            if($request->hasFile('sponsorAdImageUrl'))
            {
                if(!empty($request->id)){

                    $getIcon = DB::table('schedules')->where('id',$request->id)->select('sponsorAdImageUrl')->first();

                    if(!empty($getIcon->sponsorAdImageUrl)){
                        $serverImagePath = 'uploads/schedules/'.$getIcon->sponsorAdImageUrl;
                        removeServerImages($serverImagePath);
                    }

                }


                $fileobj				= $request->file('sponsorAdImageUrl');
                $file_extension_name 	= $fileobj->getClientOriginalExtension('sponsorAdImageUrl');
                $file_unique_name 		= str_replace(' ','-',strtolower($request->scheduleName).'_'.time().rand(1000,9999).'.'.$file_extension_name);
                $destinationPath		= public_path('/uploads/schedules/');
                $fileobj->move($destinationPath,$file_unique_name);

                $input['sponsorAdImageUrl'] = $file_unique_name;
            }
        }

        $input['isSponsorAd'] = $request->isSponsorAd;

        $schduledData   =   Schedules::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        $this->syncScheduleApplications($schduledData,$request->application_ids);

        return response()->json(['success' => true]);
    }

    public function syncScheduleApplications($scheduledData,$applicationsIds){

        $schedulesAppsFromDB = DB::table('schedules_apps')->select('application_id')->where('schedule_id',$scheduledData->id)->get()->toArray();

        $array1 = [];
        foreach($schedulesAppsFromDB as $obj){
            $array1[] = $obj->application_id;
        }

        $removableApps = array_diff($array1,$applicationsIds); // these application ids will remove form the table
        $newApps = array_diff($applicationsIds,$array1); // these ids must be new and will in the table as well

        if(!empty($removableApps)){
            SchedulesApps::whereIn('application_id',$removableApps)->where('schedule_id',$scheduledData->id)->delete();
        }

        foreach($newApps as $appId){
            $schedulesApps = [];
            $schedulesApps['application_id'] = $appId;
            $schedulesApps['schedule_id'] =$scheduledData->id;

            SchedulesApps::create($schedulesApps);
        }

        return true;
    }

    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $scheduleData  = Schedules::where($where)->first();
        $schedulesApps = DB::table('schedules_apps')->select('application_id')->where('schedule_id',$request->id)->get();

        $scheduleData->apps = $schedulesApps;
        return response()->json($scheduleData);
    }

    public function destroy(Request $request)
    {
        ScheduledServers::where('schedule_id',$request->id)->delete();

        $getIcon = DB::table('schedules')->where('id',$request->id)->select('sponsorAdImageUrl')->first();

        if(!empty($getIcon->sponsorAdImageUrl)){
            $serverImagePath = 'uploads/schedules/'.$getIcon->sponsorAdImageUrl;
            removeServerImages($serverImagePath);
        }

        Schedules::where('id',$request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function fetchschedulesdata(Request $request , $sports_id)
    {
        if(request()->ajax()) {

            $response = array();
            $Filterdata = Schedules::select('schedules.*','leagues.name as league_name','homeTeam.name as home_team_name','homeTeam.points as home_points','awayTeam.name as away_team_name','awayTeam.points as away_points')
                ->where('schedules.sports_id',$sports_id);


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
                            $serverLink =  '<a target="" href="'.url("admin/servers/".$obj->id).'" class=""> <i class="fa fa-server text-md text-success"></i> <span class="text-dark text-bold">'.$linkedServer->count().'</span>  </a>';
                        }
                        else{
                            $serverLink = '<a target="" href="'.url("admin/servers/".$obj->id).'" class=""> <i class="fa fa-server text-md text-danger"></i>   </a>';
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

                    $sponsorAdImageUrl =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';
                    if(!empty($obj->sponsorAdImageUrl)){
                        $file = public_path('uploads/schedules'.'/'.$obj->sponsorAdImageUrl);
                        if(file_exists($file)){
                            $sponsorAdImageUrl = '<img class="dataTable-image" src="'.url("/uploads/schedules/").'/'.$obj->sponsorAdImageUrl.'" />';
                        }
                    }

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $serverLink;
                    $response[$i]['appData'] = $dataArray;
                    $response[$i]['scheduleName'] = $obj->scheduleName;
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
            return datatables()->of($response)
                ->addIndexColumn()
                ->rawColumns(['srno','checkbox','sponsorAdImageUrl','action'])
                ->make(true);
        }
    }

    public function updateScheduleLiveStatus(Request $request){

        $input['is_live'] = $request->is_live;
        Schedules::updateOrCreate(
        [
            'id' => $request->schedule_id
        ],
        $input);

        return response()->json(['success' => true]);

    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;

        $idsArray = explode(",",$ids);

        foreach($idsArray as $id){

            ScheduledServers::where('schedule_id',$id)->delete();

            $getIcon = DB::table('schedules')->where('id',$id)->select('sponsorAdImageUrl')->first();

            if(!empty($getIcon->sponsorAdImageUrl)){
                $serverImagePath = 'uploads/schedules/'.$getIcon->sponsorAdImageUrl;
                removeServerImages($serverImagePath);
            }

        }

        DB::table("schedules")->whereIn('id',explode(",",$ids))->delete();

        return response()->json(['success'=>"Schedules deleted successfully."]);
    }

    public function getSchedulesAppByScheduleId(Request $request){
        $schedulesAppsList = SchedulesApps::where('schedule_id',$request->scheduleId)
            ->select(['app_details.*'])
            ->join("app_details",function($join){
                $join->on('app_details.id','=','schedules_apps.application_id');
            })
            ->join("schedules",function($join){
                $join->on('schedules.id','=','schedules_apps.schedule_id');
            })
            ->get();

        return view('includes.schedules_apps_card_view')
            ->with('schedulesAppsList',$schedulesAppsList);
    }


}
