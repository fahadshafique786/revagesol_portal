<?php

namespace App\Http\Controllers;

use App\Models\AppDetails;
use App\Models\SponsorAds;
use App\Models\Sports;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;

class SponsorsController extends Controller
{
    protected $roleAssignedApplications;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-sponsors', ['only' => ['index','FetchSponsorsData']]);
        $this->middleware('role_or_permission:super-admin|view-applications', ['only' => ['index','FetchSponsorsData']]);
        $this->middleware('role_or_permission:super-admin|manage-sponsors',['only' => ['edit','store','destroy']]);
        $this->middleware('role_or_permission:super-admin|manage-applications',['only' => ['edit','store','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        if(!empty($this->roleAssignedApplications)){
            $appsList = AppDetails::whereIn('id',$this->roleAssignedApplications)->get();
        }
        else{
            $appsList = AppDetails::get();
        }

        $sportsList = Sports::orderBy('id','DESC')->get();

        return view('sponsors')
            ->with('sportsList',$sportsList)
            ->with('appsList',$appsList);
    }

    public function store(Request $request)
    {
        $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($request->app_detail_id,$roleAssignedApplications)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }

        if(!empty($request->id))
        {
            $validation = SponsorAds::where('adName',$request->adName)
                ->where('app_detail_id',$request->app_detail_id)
                ->where('id','!=',$request->id);

            $validationResponse = [];

            if($validation->exists()){
                $validationResponse = [];
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['adName'] = "The ad name already exists with current App!";

                return Response::json($validationResponse,422);
            }

        }
        else
        {
//            $this->validate($request, [
//                'adName' => 'required|unique:sponsor_ads,adName',
//                'app_detail_id' => 'required',
//            ]);
//
            $validation = SponsorAds::where('adName',$request->adName)
                ->where('app_detail_id',$request->app_detail_id);

            $validationResponse = [];

            if($validation->exists()){
                $validationResponse = [];
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['adName'] = "The ad name already exists with current App!";

                return Response::json($validationResponse,422);
            }

        }

        $input = array();
        $input['adName'] = $request->adName;
        $input['app_detail_id'] = $request->app_detail_id;
        $input['clickAdToGo'] = $request->clickAdToGo;
        $input['isAdShow'] = $request->isAdShow;


        if($request->hasFile('adUrlImage'))
        {

            if(!empty($request->id)){

                $getIcon = DB::table('sponsor_ads')->where('id',$request->id)->select('adUrlImage')->first();

                if(!empty($getIcon->adUrlImage)){
                    $serverImagePath = 'uploads/sponsor_ads/'.$getIcon->adUrlImage;
                    removeServerImages($serverImagePath);
                }

            }


            $fileobj				= $request->file('adUrlImage');
            $file_extension_name 	= $fileobj->getClientOriginalExtension('adUrlImage');
            $file_unique_name 		= str_replace(' ','-',strtolower($request->adName).'_'.time().rand(1000,9999).'.'.$file_extension_name);
            $destinationPath		= public_path('/uploads/sponsor_ads/');
            $fileobj->move($destinationPath,$file_unique_name);

            $input['adUrlImage'] = $file_unique_name;
        }

        $user   =   SponsorAds::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        return response()->json(['success' => true]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\sponsors  $sponsors
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $data  = SponsorAds::where($where)->first();
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
      * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)
    {

        $database = SponsorAds::where('id',$request->id)->select('app_detail_id')->first();
        $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($database->app_detail_id,$roleAssignedApplications)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }
        

        $getIcon = DB::table('sponsor_ads')->where('id',$request->id)->select('adUrlImage')->first();

        if(!empty($getIcon->adUrlImage)){
            $serverImagePath = 'uploads/sponsor_ads/'.$getIcon->adUrlImage;
            removeServerImages($serverImagePath);
        }

        SponsorAds::where('id',$request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function fetchSponsorAdsList(Request  $request)
    {
        if(request()->ajax()) {

            $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

            $response = array();
            $Filterdata = SponsorAds::select('sponsor_ads.*','app_details.appName','app_details.packageId as packageId');


            if(isset($request->filter_app_id) && !empty($request->filter_app_id) && ($request->filter_app_id != '-1')){
                $Filterdata = $Filterdata->where('sponsor_ads.app_detail_id',$request->filter_app_id);
            }

            $Filterdata = $Filterdata->join('app_details', function ($join) {
                $join->on('app_details.id', '=', 'sponsor_ads.app_detail_id');
            });

            if(!empty($this->roleAssignedApplications)){
                $Filterdata = $Filterdata->whereIn('sponsor_ads.app_detail_id',$this->roleAssignedApplications);
            }

            if($request->filter_app_id == '-1' && isset($request->filter_sports_id) && !empty($request->filter_sports_id) && ($request->filter_sports_id != '-1') ){
                $Filterdata = $Filterdata->where('app_details.sports_id',$request->filter_sports_id);
            }

            $Filterdata = $Filterdata->orderBy('sponsor_ads.id','asc')->get();

            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $obj)
                {

                    $images =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';
                    if(!empty($obj->adUrlImage)){
                        $file = public_path('uploads/sponsor_ads'.'/'.$obj->adUrlImage);
                        if(file_exists($file)){
                            $images = '<img class="dataTable-image" src="'.url("/uploads/sponsor_ads/").'/'.$obj->adUrlImage.'" />';
                        }
                    }


                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['appName'] = $obj->appName . ' - ' . $obj->packageId;
                    $response[$i]['name'] = $obj->adName;
                    $response[$i]['adUrlImage'] = $images;
                    $response[$i]['url'] = $obj->clickAdToGo;
                    $response[$i]['isAdShow'] = getBooleanStr($obj->isAdShow,true);
                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-sponsors'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-id="'. $obj->id .'"><i class="fa fa-edit  text-info"></i></a>
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
                ->rawColumns(['adUrlImage','checkbox','action'])
                ->make(true);
        }
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids); // server Ids

        foreach($idsArray as $id){


            $getIcon = DB::table('sponsor_ads')->where('id',$id)->select('adUrlImage')->first();

            if(!empty($getIcon->adUrlImage)){
                $serverImagePath = 'uploads/sponsor_ads/'.$getIcon->adUrlImage;
                removeServerImages($serverImagePath);
            }

            SponsorAds::where('id',$id)->delete();
        }

        return response()->json(['success'=>"Sponsor Ads deleted successfully."]);
    }

}
