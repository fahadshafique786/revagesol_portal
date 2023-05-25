<?php

namespace App\Http\Controllers;

use App\Models\ServerHeaders;
use App\Models\Servers;
use App\Models\ServerTypes;
use App\Models\Sports;
use App\Models\ScheduledServers;
use App\Models\Schedules;
use Illuminate\Http\Request;
use DB;

class ServersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-servers', ['only' => ['index','fetchserversdata']]);
        $this->middleware('role_or_permission:super-admin|manage-servers',['only' => ['edit','store','destroy','deleteAll']]);
    }

    public function index(Request $request)
    {
        $sports_list = Sports::orderBy('id','DESC')->get();
        $serverTypeList = ServerTypes::orderBy('id','DESC')->get();
        return view('servers')
            ->with('serverTypes',$serverTypeList)
            ->with('sports_list',$sports_list);
    }

    public function store(Request $request,$schedule_id = null)
    {
        if($schedule_id){
            $scheduleSports = Schedules::where('id',$schedule_id)->select('sports_id','leagues_id')->first();
            $sports_id  = $scheduleSports->sports_id;
            $leagues_id  = $scheduleSports->leagues_id;
            $request->merge(['sports_id' => $sports_id]);
            $request->merge(['leagues_id' => $leagues_id]);
        }

        if(!empty($request->id)) // edit case
        {
            $this->validate($request, [
                'name' => 'required|unique:servers,name,'.$request->id,
                'sports_id' => 'required',
                'leagues_id' => 'required',
                'server_type_id' => 'required',
            ]);
        }
        else
        {
            $this->validate($request, [
                'name' => 'required|unique:servers,name,'.$request->id,
                'sports_id' => 'required',
                'leagues_id' => 'required',
                'server_type_id' => 'required',
            ]);
        }

        $input = array();

        $input['name'] = $request->name;
        $input['sports_id'] = $request->sports_id;
        $input['leagues_id'] = $request->leagues_id;
        $input['server_type_id'] = $request->server_type_id;
        $input['link'] = $request->link;
        $input['isHeader'] = $request->isHeader;
        $input['isPremium'] = $request->isPremium;
        $input['isTokenAdded'] = $request->isTokenAdded;
        $input['isIpAddressApiCall'] = $request->isIpAddressApiCall;


        if($request->isSponsorAd){
            $input['sponsorAdClickUrl'] = $request->sponsorAdClickUrl;

            if($request->hasFile('sponsorAdImageUrl'))
            {
                if(!empty($request->id)){

                    $getIcon = DB::table('servers')->where('id',$request->id)->select('sponsorAdImageUrl')->first();

                    if(!empty($getIcon->sponsorAdImageUrl)){
                        $serverImagePath = 'uploads/servers/'.$getIcon->sponsorAdImageUrl;
                        removeServerImages($serverImagePath);
                    }

                }

                $fileobj				= $request->file('sponsorAdImageUrl');
                $file_extension_name 	= $fileobj->getClientOriginalExtension('sponsorAdImageUrl');
                $file_unique_name 		= str_replace(' ','-',strtolower($request->name).'_'.time().rand(1000,9999).'.'.$file_extension_name);
                $destinationPath		= public_path('/uploads/servers/');
                $fileobj->move($destinationPath,$file_unique_name);

                $input['sponsorAdImageUrl'] = $file_unique_name;
            }
        }

        $input['isSponsorAd'] = $request->isSponsorAd;

        $servers   =   Servers::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        if($request->isHeader){

            $serverId = ($request->id)  ? $request->id : $servers->id;

            if(isset($request->key_name) && sizeof($request->key_name) > 0)
            {
                $keyNamesList = implode(',', $request->key_name);

                $output = DB::table('server_headers')
                    ->select(['key_name'])
                    ->where('server_id',$serverId)
                    ->whereNotIn('key_name',$request->key_name)->delete();

                foreach($request->key_name as $index => $keyName){

                    $headersData = [];
                    if(empty($keyName)){
                        continue;
                    }


                    $keyValue = $request->key_value[$index];

                    $headersData['key_name'] = $keyName;

                    $headersData['key_value'] = $keyValue;

                    $hederExistance = ServerHeaders::where('server_id',$serverId)
                    ->where('key_name',$keyName);

                    if(!$hederExistance->exists()){

                        $headersData['server_id'] = $serverId;

                        ServerHeaders::create($headersData);

                    }
                    else{

                        unset($headersData['key_name']);

                        ServerHeaders::where('server_id',$serverId)
                            ->where('key_name',$keyName)
                            ->update($headersData);

                    }

                }
            }

        }

        if($schedule_id){

            $data['schedule_id'] = $schedule_id;
            $data['server_id'] = $servers->id;

            $checkExistance = ScheduledServers::where('schedule_id',$schedule_id)
                ->where('server_id',$servers->id);

            if(!$checkExistance->exists()){
                ScheduledServers::create($data);
            }

        }

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
    {
        $where = array('servers.id' => $request->id);
        $servers  = Servers::select('servers.*')->where($where)->first();

        $serverHeaders = DB::table('server_headers')->select(['key_name','key_value'])->where('server_id',$request->id)->get();

        $servers->headers = (sizeof($serverHeaders) > 0) ? $serverHeaders : [];

        return response()->json($servers);

    }

    public function destroy(Request $request,$schedule_id = null)
    {

        $getIcon = DB::table('servers')->where('id',$request->id)->select('sponsorAdImageUrl')->first();

        if(!empty($getIcon->sponsorAdImageUrl)){
            $serverImagePath = 'uploads/servers/'.$getIcon->sponsorAdImageUrl;
            removeServerImages($serverImagePath);
        }

        if($schedule_id){
            ScheduledServers::where('server_id',$request->id)
                ->where('schedule_id',$schedule_id)
                ->delete();
        }
        else{
            ScheduledServers::where('server_id',$request->id)
                ->delete();
            Servers::where('id',$request->id)->delete();
        }
        return response()->json(['success' => true]);
    }

    public function fetchserversdata(Request $request , $schedule_id = null)
    {
        if(request()->ajax()) {

            $response = array();

            $Filterdata = Servers::select('servers.*','sports.name as sport_name','leagues.name as league_name','server_types.name as server_type')
                ->join('sports', function ($join) {
                    $join->on('servers.sports_id', '=', 'sports.id');
                })
                ->leftJoin('leagues', function ($join) {
                    $join->on('servers.leagues_id', '=', 'leagues.id');
                })
                ->leftJoin('server_types', function ($join) {
                    $join->on('servers.server_type_id', '=', 'server_types.id');
                });

            if($schedule_id){
                $Filterdata = $Filterdata->join('scheduled_servers', function ($join) {
                        $join->on('scheduled_servers.server_id', '=', 'servers.id');
                    })
                    ->where('scheduled_servers.schedule_id',$schedule_id);
            }


            if(isset($request->filter_sports) && !empty($request->filter_sports) && ($request->filter_sports != '-1')){
                $Filterdata = $Filterdata->where('servers.sports_id',$request->filter_sports);
            }

            if(isset($request->filter_leagues) && !empty($request->filter_leagues) && ($request->filter_leagues != '-1')){
                $Filterdata = $Filterdata->where('servers.leagues_id',$request->filter_leagues);
            }


            if($schedule_id) {
                $Filterdata = $Filterdata->orderBy('servers.id', 'ASC')->get();
            }
            else{
                $Filterdata = $Filterdata->orderBy('servers.id', 'DESC')->get();
            }

            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $obj)
                {

                    $sponsorAdImageUrl =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';
                    if(!empty($obj->sponsorAdImageUrl)){
                        $file = public_path('uploads/servers'.'/'.$obj->sponsorAdImageUrl);
                        if(file_exists($file)){
                            $sponsorAdImageUrl = '<img class="dataTable-image" src="'.url("/uploads/servers/").'/'.$obj->sponsorAdImageUrl.'" />';
                        }
                    }


                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['server_type'] = $obj->server_type;
                    $response[$i]['name'] = $obj->name;
                    $response[$i]['sport_name'] = $obj->sport_name;
                    $response[$i]['league_name'] = $obj->league_name;
                    $response[$i]['link'] = $obj->link;
                    $response[$i]['isTokenAdded'] = getBooleanStr($obj->isTokenAdded,true);
                    $response[$i]['isIpAddressApiCall'] = getBooleanStr($obj->isIpAddressApiCall,true);
                    $response[$i]['isHeader'] = getBooleanStr($obj->isHeader,true);
                    $response[$i]['isPremium'] = getBooleanStr($obj->isPremium,true);
                    $response[$i]['isSponsorAd'] = getBooleanStr($obj->isSponsorAd,true);
                    $response[$i]['sponsorAdClickUrl'] = $obj->sponsorAdClickUrl;
                    $response[$i]['sponsorAdImageUrl'] = $sponsorAdImageUrl;

                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-servers'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-id="'. $obj->id .'"><i class="fa fa-edit  text-info"></i></a>
											<a href="javascript:void(0)" class="btn delete hide " data-id="'. $obj->id .'"><i class="fa fa-trash-alt text-danger"></i></a>';
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
                ->rawColumns(['sponsorAdImageUrl','checkbox','action'])
                ->make(true);
        }
    }

    public function fetchScheduleServersView($schedule_id)
    {
        $scheduleData = Schedules::select('schedules.id as schedule_id','schedules.scheduleName','homeTeam.name as home_team_name','awayTeam.name as away_team_name')
            ->where('schedules.id',$schedule_id)
            ->join('teams as homeTeam', function ($join) {
                $join->on('schedules.home_team_id', '=', 'homeTeam.id');
            })
            ->join('teams as awayTeam', function ($join) {
                $join->on('schedules.away_team_id', '=', 'awayTeam.id');
            })
            ->orderBy('schedules.id','DESC')->first();


        $scheduleSports = Schedules::where('id',$schedule_id)->select('sports_id','leagues_id')->first();
        $sports_id  = $scheduleSports->sports_id;

        $servers_list = Servers::where('sports_id',$sports_id)
            ->where('leagues_id',$scheduleSports->leagues_id)
            ->orderBy('id','DESC')
            ->get();

        $serverTypeList = ServerTypes::orderBy('id','DESC')->get();

        if(!empty($scheduleData)){

            return view('schedule_servers')
                ->with('scheduleData',$scheduleData)
                ->with('serverTypes',$serverTypeList)
                ->with('servers_list',$servers_list)
                ->with('schedule_id',$schedule_id);
        }
        else{
            abort(404);
        }

    }

    public function attachServers(Request $request,$schedule_id){
        if($schedule_id){
            $request->merge(['schedule_id' => $schedule_id]);

             $checkExistance = ScheduledServers::where('schedule_id',$request->schedule_id)
                    ->where('server_id',$request->server_id);

             if($checkExistance->exists()){
                 return response()->json([
                     'errors' =>
                     [
                         'message'=> 'This server is already linked with the same schedule',
                         'status_code' => 400
                     ]
                 ], 400);

                 exit();
             }


            $data['schedule_id'] = $schedule_id;
            $data['server_id'] = $request->server_id;
            $scheduledServers   =   ScheduledServers::create($data);
        }

        return response()->json(['success' => true]);


    }


    public function getServersList(Request $request){

        if($request->schedule_id){
            $scheduleSports = Schedules::where('id',$request->schedule_id)->select('sports_id','leagues_id')->first();
            $sports_id  = $scheduleSports->sports_id;
            $leagues_id  = $scheduleSports->leagues_id;
            $request->merge(['sports_id' => $sports_id]);
            $request->merge(['leagues_id' => $leagues_id]);
        }


        $serversList = Servers::where('sports_id',$request->sports_id)
        ->where('leagues_id',$request->leagues_id)
        ->orderBy('id','DESC')
        ->get();

//        dd($serversList);

        $options = '<option value="">Select Server </option>';

        if(!empty($serversList)){
            foreach($serversList as $obj){
                $options .= '<option value="'.$obj->id.'">   '  .   $obj->name    .   '    </option>';
            }
        }

        return $options;
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids); // server Ids

        foreach($idsArray as $id){

                ScheduledServers::where('server_id',$id)->delete();

            $getIcon = DB::table('servers')->where('id',$id)->select('sponsorAdImageUrl')->first();

            if(!empty($getIcon->sponsorAdImageUrl)){
                $serverImagePath = 'uploads/servers/'.$getIcon->sponsorAdImageUrl;
                removeServerImages($serverImagePath);
            }

        }

        DB::table("servers")->whereIn('id',explode(",",$ids))->delete();

        return response()->json(['success'=>"Servers deleted successfully."]);
    }



}
