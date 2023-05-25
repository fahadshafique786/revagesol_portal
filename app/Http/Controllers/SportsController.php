<?php

namespace App\Http\Controllers;

use App\Models\AdmobAds;
use App\Models\AppCredentials;
use App\Models\FirebaseCredentials;
use App\Models\SponsorAds;
use Illuminate\Http\Request;
use App\Models\Sports;
use App\Models\Schedules;
use App\Models\Leagues;
use App\Models\Teams;
use App\Models\Servers;
use App\Models\AppDetails;
use DB;

class SportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-sports', ['only' => ['index','fetchsportsdata']]);
        $this->middleware('role_or_permission:super-admin|manage-sports',['only' => ['edit','store','editProfile','updateRole','destroy','deleteAll']]);
    }

    public function index(Request $request)
    {
        $sports_list = Sports::all();
        return view('sports')
            ->with('sports_list',$sports_list);
    }

    public function store(Request $request)
    {
        if(!empty($request->id))
        {
            $this->validate($request, [
                'name' => 'required|unique:sports,name,'.$request->id,
                'sports_type' => 'required',
            ]);
        }
        else
        {
            $this->validate($request, [
                'name' => 'required|unique:sports,name,'.$request->id,
                'sports_type' => 'required',
            ]);
        }

        $input = array();
        $input['name'] = $request->name;
        $input['multi_league'] = $request->multi_league;
        $input['sports_type'] = $request->sports_type;
        $input['image_required'] = $request->image_required;


        if($request->hasFile('sport_logo'))
        {
            if(!empty($request->id)){

                $getIcon = DB::table('sports')->where('id',$request->id)->select('icon')->first();
                if(!empty($getIcon->icon)){
                    $serverImagePath = 'uploads/sports/'.$getIcon->icon;
                    removeServerImages($serverImagePath);
                }
            }

            $fileobj				= $request->file('sport_logo');
            $file_extension_name 	= $fileobj->getClientOriginalExtension('sport_logo');
            $file_unique_name 		= str_replace(' ','-',strtolower($request->name).'_'.time().rand(1000,9999).'.'.$file_extension_name);
            $destinationPath		= public_path('/uploads/sports/');
            $fileobj->move($destinationPath,$file_unique_name);

            $input['icon'] = $file_unique_name;
        }

        $user   =   Sports::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $sports  = Sports::where($where)->first();
        return response()->json($sports);
    }



    public function destroy(Request $request)
    {
        $getApplications = AppDetails::where('sports_id',$request->id)->get();
        foreach($getApplications as $obj){

            AdmobAds::where('app_detail_id',$obj->id)->delete();
            SponsorAds::where('app_detail_id',$obj->id)->delete();
            AppCredentials::where('app_detail_id',$obj->id)->delete();
        }


        AppDetails::where('sports_id',$request->id)->delete();


        Servers::where('sports_id',$request->id)->delete();
        Schedules::where('sports_id',$request->id)->delete();
        Teams::where('sports_id',$request->id)->delete();
        Leagues::where('sports_id',$request->id)->delete();

        if(!empty($request->id)){

            $getIcon = DB::table('sports')->where('id',$request->id)->select('icon')->first();
            if(!empty($getIcon->icon)){
                $serverImagePath = 'uploads/sports/'.$getIcon->icon;
                removeServerImages($serverImagePath);
            }
        }


        Sports::where('id',$request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function fetchsportsdata(Request $request)
    {
        if(request()->ajax()) {

            $response = array();
            $Filterdata = Sports::select('*');

            if(isset($request->filter_sports) && !empty($request->filter_sports)){
                $Filterdata = $Filterdata->where('id',$request->filter_sports);
            }

            $Filterdata =  $Filterdata->orderBy('id','DESC')->get();

            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $sports)
                {
                    $sport_logo =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';
                    if(!empty($sports->icon)){
                        $file = public_path('uploads/sports'.'/'.$sports->icon);
                        if(file_exists($file)){
                            $sport_logo = '<img class="dataTable-image" src="'.url("/uploads/sports/").'/'.$sports->icon.'" />';
                        }
                    }

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$sports->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['icon'] = $sport_logo;
                    $response[$i]['name'] = $sports->name;
                    $response[$i]['sports_type'] = $sports->sports_type;
                    $response[$i]['multi_league'] = getBooleanStr($sports->multi_league,true);
                    $response[$i]['image_required'] = getBooleanStr($sports->image_required,true);
                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-sports'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-id="'. $sports->id .'"><i class="fa fa-edit  text-info"></i></a>
											<a href="javascript:void(0)" class="btn delete " data-id="'. $sports->id .'"><i class="fa fa-trash-alt text-danger"></i></a>';
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


    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids);

        foreach($idsArray as $id){

            $getApplications = AppDetails::where('sports_id',$id)->get();
            foreach($getApplications as $obj){

                AdmobAds::where('app_detail_id',$obj->id)->delete();
                SponsorAds::where('app_detail_id',$obj->id)->delete();
                AppCredentials::where('app_detail_id',$obj->id)->delete();
                FirebaseCredentials::where('app_detail_id',$obj->id)->delete();
            }

            AppDetails::where('sports_id',$id)->delete();

            Servers::where('sports_id',$id)->delete();

            Schedules::where('sports_id',$id)->delete();

            Teams::where('sports_id',$id)->delete();

            Leagues::where('sports_id',$id)->delete();

            $getIcon = DB::table('sports')->where('id',$id)->select('icon')->first();
            if(!empty($getIcon->icon)){
                $serverImagePath = 'uploads/sports/'.$getIcon->icon;
                removeServerImages($serverImagePath);
            }

        }

        DB::table("sports")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>"Sports deleted successfully."]);
    }


}
