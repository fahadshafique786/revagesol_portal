<?php

namespace App\Http\Controllers;

use App\Models\ScheduledServers;
use Illuminate\Http\Request;
use App\Models\Leagues;
use App\Models\Sports;
use App\Models\Teams;
use App\Models\Schedules;
use Response;
use DB;

class TeamsController extends Controller
{
    public function __construct()
    {
        $this->sports_id = null;
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-teams', ['only' => ['index','fetchteamsdata']]);
        $this->middleware('role_or_permission:super-admin|manage-teams',['only' => ['edit','store','destroy','deleteAll']]);
    }


    public function index(Request $request, $sports_id)
    {
        $sportData = Sports::where('id',$sports_id)->first();
        if(!empty($sportData)){
            $leaguesList = Leagues::where('sports_id',$sports_id)->orderBy('start_datetime','ASC')->get();

            return view('teams')
                ->with('sports_id',$sports_id)
                ->with('sportData',$sportData)
                ->with('leagues_list',$leaguesList);
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
                'name' => 'required',
                'leagues_id' => 'required',
            ]);

            $validation = Teams::where('name',$request->name)
                ->where('leagues_id',$request->leagues_id)
                ->where('id','!=',$request->id);

            $validationResponse = [];

            if($validation->exists()){
                $validationResponse = [];
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['name'] = "The name has already been taken for selected League!";

                return Response::json($validationResponse,422);
            }
        }
        else
        {
            $this->validate($request, [
                'name' => 'required',
                'leagues_id' => 'required',
            ]);

            $validation = Teams::where('name',$request->name)
                ->where('leagues_id',$request->leagues_id);

            $validationResponse = [];

            if($validation->exists()){
                $validationResponse = [];
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['name'] = "The name has already been taken  for this League!";

                return Response::json($validationResponse,422);
            }
        }

        $input = array();
        $input['name'] = $request->name;
        $input['leagues_id'] = $request->leagues_id;
        $input['points'] = $request->points;
        $input['sports_id'] = $sports_id;


        if($request->hasFile('team_icon'))
        {
            if(!empty($request->id)){

                $getIcon = DB::table('teams')->where('id',$request->id)->select('icon')->first();

                if(!empty($getIcon->icon)){
                    $serverImagePath = 'uploads/teams/'.$getIcon->icon;
                    removeServerImages($serverImagePath);
                }

            }

            $fileobj				= $request->file('team_icon');
            $file_original_name 	= $fileobj->getClientOriginalName('team_icon');
            $file_extension_name 	= $fileobj->getClientOriginalExtension('team_icon');
            $file_unique_name 		= str_replace(' ','-',strtolower($request->name).'_'.time().rand(1000,9999).'.'.$file_extension_name);
            $destinationPath		= public_path('/uploads/teams');
            $fileobj->move($destinationPath,$file_unique_name);

            $input['icon'] = $file_unique_name;
        }

        $teams   =   Teams::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $teams  = Teams::where($where)->first();
        return response()->json($teams);
    }

    public function destroy(Request $request)
    {
        Schedules::where('home_team_id',$request->id)
            ->delete();


        Schedules::where('away_team_id',$request->id)
            ->delete();


        $getIcon = DB::table('teams')->where('id',$request->id)->select('icon')->first();

        if(!empty($getIcon->icon)){
            $serverImagePath = 'uploads/teams/'.$getIcon->icon;
            removeServerImages($serverImagePath);
        }

        $teams = Teams::where('id',$request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function fetchteamsdata(Request $request,$sports_id)
    {
        if(request()->ajax()) {

            $response = array();
            $Filterdata = Teams::select('teams.*','leagues.name as league_name')
                ->where('teams.sports_id',$sports_id);

            if(isset($request->filter_league) && !empty($request->filter_league) && ($request->filter_league != '-1')){
                $Filterdata = $Filterdata->where('teams.leagues_id',$request->filter_league);
            }

            $Filterdata = $Filterdata->join('leagues', function ($join) {
                $join->on('leagues.id', '=', 'teams.leagues_id');
            })->orderBy('teams.id','DESC')->get();

            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $team)
                {
                    $icon =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';
                    if(!empty($team->icon)){
                        $file = public_path('uploads/teams'.'/'.$team->icon);
                        if(file_exists($file)){
                            $icon = '<img class="dataTable-image" src="'.url("/uploads/teams/").'/'.$team->icon.'" />';
                        }
                    }

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$team->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['icon'] = $icon;
                    $response[$i]['name'] = $team->name;
                    $response[$i]['league_name'] = $team->league_name;
                    $response[$i]['points'] = $team->points;
                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-teams'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-id="'. $team->id .'"><i class="fa fa-edit  text-info"></i></a>
											<a href="javascript:void(0)" class="btn delete " data-id="'. $team->id .'"><i class="fa fa-trash-alt text-danger"></i></a>';
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
                ->rawColumns(['icon','checkbox','action'])
                ->make(true);
        }
    }


    public function getTeamsByLeagueId(Request $request)
    {
        $teamsList = Teams::where('leagues_id',$request->leagues_id)->orderBy('id','DESC')->get();
        $options = '<option value="">Select Team </option>';
        if(!empty($teamsList)){
            foreach($teamsList as $obj){
                $options .= '<option value="'.$obj->id.'">   '  .   $obj->name    .   '    </option>';
            }
        }

        return $options;
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids);

        foreach($idsArray as $id){

            $getSchedules = Schedules::where('home_team_id',$id)
                ->orWhere('away_team_id',$id)
                ->get();
            foreach($getSchedules as $obj){
                ScheduledServers::where('schedule_id',$obj->id)->delete();
            }

            Schedules::where('home_team_id',$id)
                ->delete();


            Schedules::where('away_team_id',$id)
                ->delete();

                $getIcon = DB::table('teams')->where('id',$id)->select('icon')->first();

                if(!empty($getIcon->icon)){
                    $serverImagePath = 'uploads/teams/'.$getIcon->icon;
                    removeServerImages($serverImagePath);
                }


        }


        DB::table("teams")->whereIn('id',explode(",",$ids))->delete();

        return response()->json(['success'=>"Teams deleted successfully."]);
    }




}
