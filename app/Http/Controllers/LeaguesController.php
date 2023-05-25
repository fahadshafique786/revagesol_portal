<?php

namespace App\Http\Controllers;

use App\Models\ScheduledServers;
use App\Models\ServerHeaders;
use App\Models\Teams;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use App\Models\Leagues;
use App\Models\Schedules;
use App\Models\Sports;
use App\Models\Servers;
use Response;
use DB;

class LeaguesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-leagues', ['only' => ['index','fetchleaguesdata']]);
        $this->middleware('role_or_permission:super-admin|manage-leagues',['only' => ['edit','store','destroy','deleteAll']]);
    }

    public function index(Request $request)
    {
        $sports_list = Sports::orderBy('id','DESC')->get();
        return view('leagues')
            ->with('sports_list',$sports_list);
    }

    public function store(Request $request)
    {
        if(!empty($request->id))
        {
            $this->validate($request, [
                'name' => 'required',
                'sports_id' => 'required',
                'start_datetime' => 'required',
            ]);

            $validation = Leagues::where('name',$request->name)
                ->where('sports_id',$request->sports_id)
                ->where('id','!=',$request->id);

            $validationResponse = [];

            if($validation->exists()){
                $validationResponse = [];
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['name'] = "The name has already been taken for selected Sport!";

                return Response::json($validationResponse,422);
            }

        }
        else
        {
            $this->validate($request, [
                'name' => 'required',
                'sports_id' => 'required',
                'start_datetime' => 'required',
            ]);


            $validation = Leagues::where('name',$request->name)
                ->where('sports_id',$request->sports_id);

            $validationResponse = [];

            if($validation->exists()){
                $validationResponse = [];
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['name'] = "The name has already been taken  for this Sport!";

                return Response::json($validationResponse,422);
            }

        }


        $input = array();
        $input['name'] = $request->name;
        $input['sports_id'] = $request->sports_id;
        $input['start_datetime'] = $request->start_datetime;


        if($request->isSponsorAd){
            $input['sponsorAdClickUrl'] = $request->sponsorAdClickUrl;

            if($request->hasFile('sponsorAdImageUrl'))
            {
                if(!empty($request->id)){

                    $getIcon = DB::table('leagues')->where('id',$request->id)->select('sponsorAdImageUrl')->first();

                    if(!empty($getIcon->sponsorAdImageUrl)){
                        $serverImagePath = 'uploads/leagues/'.$getIcon->sponsorAdImageUrl;
                        removeServerImages($serverImagePath);
                    }
                }

                $fileobj				= $request->file('sponsorAdImageUrl');
                $file_extension_name 	= $fileobj->getClientOriginalExtension('sponsorAdImageUrl');
                $file_unique_name 		= str_replace(' ','-',strtolower($request->name).'_'.time().rand(1000,9999).'.'.$file_extension_name);
                $destinationPath		= public_path('/uploads/leagues/');
                $fileobj->move($destinationPath,$file_unique_name);

                $input['sponsorAdImageUrl'] = $file_unique_name;
            }
        }

        $input['isSponsorAd'] = $request->isSponsorAd;


        if($request->hasFile('league_icon'))
        {


            if(!empty($request->id)){

                $getIcon = DB::table('leagues')->where('id',$request->id)->select('icon')->first();

                if(!empty($getIcon->icon)){
                    $serverImagePath = 'uploads/leagues/'.$getIcon->icon;
                    removeServerImages($serverImagePath);
                }

            }

            $fileobj				= $request->file('league_icon');
            $file_extension_name 	= $fileobj->getClientOriginalExtension('league_icon');
            $file_unique_name 		= str_replace(' ','-',strtolower($request->name).'_'.time().rand(1000,9999).'.'.$file_extension_name);
            $destinationPath		= public_path('/uploads/leagues/');
            $fileobj->move($destinationPath,$file_unique_name);

            $input['icon'] = $file_unique_name;
        }

        $user   =   Leagues::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $leagues  = Leagues::where($where)->first();
        return response()->json($leagues);
    }

    public function destroy(Request $request)
    {
        // get servers list from schedule server table!
        // get schedules list from schedule server table!

        Servers::where('leagues_id',$request->id)->delete();
        Schedules::where('leagues_id',$request->id)->delete();
        Teams::where('leagues_id',$request->id)->delete();


        if(!empty($request->id)){

            $getIcon = DB::table('leagues')->where('id',$request->id)->select('sponsorAdImageUrl')->first();

            if(!empty($getIcon->sponsorAdImageUrl)){
                $serverImagePath = 'uploads/leagues/'.$getIcon->sponsorAdImageUrl;
                removeServerImages($serverImagePath);
            }

            $getIcon = DB::table('leagues')->where('id',$request->id)->select('icon')->first();

            if(!empty($getIcon->icon)){
                $serverImagePath = 'uploads/leagues/'.$getIcon->icon;
                removeServerImages($serverImagePath);
            }

        }

        Leagues::where('id',$request->id)->delete();

        return response()->json(['success' => true]);

    }

    public function fetchleaguesdata(Request  $request)
    {

        if(request()->ajax()) {

            DB::enableQueryLog();

            $response = array();
            $Filterdata = Leagues::select('leagues.*','sports.name as sport_name');

            $Filterdata = $Filterdata->join('sports', function ($join) {
                $join->on('leagues.sports_id', '=', 'sports.id');
            });



            if(isset($request->filter_sports) && !empty($request->filter_sports)  && ($request->filter_sports != '-1')){
                $Filterdata = $Filterdata->where('leagues.sports_id',$request->filter_sports);
            }

            if(isset($request->active_tab) && ($request->active_tab == 'live-leagues')){
                $Filterdata = $Filterdata->where('leagues.is_live',1);
            }

            $Filterdata = $Filterdata->orderBy('leagues.start_datetime','ASC')->get();

//            dd(DB::getQueryLog(),$Filterdata);

            $leaguesArray = [];
            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $leagues)
                {

                    if(isset($request->active_tab) && ($request->active_tab == 'live-leagues')){

                        $totalSchedules = 0;
                        $totalSchedules = DB::table('schedules')
                            ->join('schedules_apps' , function ($join) {
                                $join->on('schedules_apps.schedule_id','=','schedules.id');
                            })
                            ->where('leagues_id',$leagues->id)
                            ->where('is_live','1')->count();

                            if($totalSchedules == 0) {
                                $leagues->totalSchedules = $totalSchedules;
                                $i++;
                                continue;
                            }
                    }

                    $leagues->isSponsorAd = (int)$leagues->isSponsorAd;
                    $leagues->isSponsorAd = getBoolean($leagues->isSponsorAd);

                    $icon =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';
                    if(!empty($leagues->icon)){
                        $file = public_path('uploads/leagues'.'/'.$leagues->icon);
                        if(file_exists($file)){
                            $icon = '<img class="dataTable-image" src="'.url("/uploads/leagues/").'/'.$leagues->icon.'" />';
                        }
                    }

                    $sponsorAdImageUrl =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';

                    if(!empty($leagues->sponsorAdImageUrl)){
                        $file = public_path('uploads/leagues'.'/'.$leagues->sponsorAdImageUrl);
                        if(file_exists($file)){
                            $sponsorAdImageUrl = '<img class="dataTable-image" src="'.url("/uploads/leagues/").'/'.$leagues->sponsorAdImageUrl.'" />';
                        }
                    }

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$leagues->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['icon'] = $icon;
                    $response[$i]['name'] = $leagues->name;
                    $response[$i]['sport_name'] = $leagues->sport_name;
                    $response[$i]['start_datetime'] = $leagues->start_datetime;
                    $response[$i]['isSponsorAd'] = getBooleanStr($leagues->isSponsorAd,true);
                    $response[$i]['sponsorAdClickUrl'] = $leagues->sponsorAdClickUrl;
                    $response[$i]['sponsorAdImageUrl'] = $sponsorAdImageUrl;

                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-leagues'))
                    {
                        $response[$i]['action'] = '
                            <a href="javascript:void(0)" class="btn edit" data-id="'. $leagues->id .'"><i class="fa fa-edit  text-info"></i></a>
                            <a href="javascript:void(0)" class="btn delete " data-id="'. $leagues->id .'"><i class="fa fa-trash-alt text-danger"></i></a>
                    ';
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
                ->rawColumns(['icon','checkbox','sponsorAdImageUrl','action'])
                ->make(true);
        }
    }

    public function getLeaguesOptionBySports(Request $request){
        $leaguesList = Leagues::where('sports_id',$request->sports_id)->orderBy('leagues.start_datetime','ASC')->get();
        $options = '<option value="">Select League </option>';
        if(!empty($leaguesList)){
            foreach($leaguesList as $obj){
                $options .= '<option value="'.$obj->id.'">   '  .   $obj->name    .   '    </option>';
            }
        }

        return $options;
    }


    public function updateLeagueStatus(Request $request){

        $input['is_live'] = $request->is_live;

        Leagues::updateOrCreate(
            [
                'id' => $request->league_id
            ],
            $input);

        return response()->json(['success' => true]);

    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids);

        foreach($idsArray as $id){

            $getServers = Servers::where('leagues_id',$id)->get();

            if(!empty($getServers)){
                foreach($getServers as $obj) {
                    ScheduledServers::where('server_id',$obj->id)->delete();
                    ServerHeaders::where('server_id',$obj->id)->delete();
                }
            }

            Servers::where('leagues_id',$id)->delete();

            Schedules::where('leagues_id',$id)->delete();

            Teams::where('leagues_id',$id)->delete();


            $getIcon = DB::table('leagues')->where('id',$id)->select('sponsorAdImageUrl')->first();

            if(!empty($getIcon->sponsorAdImageUrl)){
                $serverImagePath = 'uploads/leagues/'.$getIcon->sponsorAdImageUrl;
                removeServerImages($serverImagePath);
            }

            $getIcon = DB::table('leagues')->where('id',$id)->select('icon')->first();

            if(!empty($getIcon->icon)){
                $serverImagePath = 'uploads/leagues/'.$getIcon->icon;
                removeServerImages($serverImagePath);
            }

        }

       DB::table("leagues")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>"Leagues deleted successfully."]);
    }



}
