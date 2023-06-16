<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\AppDetails;
use App\Models\AdmobAds;
use Response;
use Illuminate\Support\Facades\DB;

class AdmobAdsController extends Controller
{
    protected $roleAssignedApplications;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-admob_ads', ['only' => ['index','fetchAdmobAdsList']]);
        $this->middleware('role_or_permission:super-admin|view-applications', ['only' => ['index','fetchAdmobAdsList']]);
        $this->middleware('role_or_permission:super-admin|manage-admob_ads',['only' => ['edit','store','destroy']]);
        $this->middleware('role_or_permission:super-admin|manage-applications',['only' => ['edit','store','destroy']]);
    }

    public function index()
    {
        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!empty($this->roleAssignedApplications)){
            $appsList = AppDetails::whereIn('id',$this->roleAssignedApplications)->get();
        }
        else{
            $appsList = AppDetails::get();
        }

        $accountsList = Accounts::orderBy('id','DESC')->get();

        return view('admob_ads')
            ->with('accountsList',$accountsList)
            ->with('appsList',$appsList);
    }

    public function store(Request $request)
    {
        // $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        // if(!in_array($request->app_detail_id,$roleAssignedApplications)){
        //     return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        // }

        
        if(!empty($request->id))
        {
            $validation = AdmobAds::where('adName',$request->adName)
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
            $validation = AdmobAds::where('adName',$request->adName)
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

        $account_id = getAccountIdByAppId($request->app_detail_id);

        $input['adName'] = $request->adName;
        $input['account_id'] = $account_id;
        $input['app_detail_id'] = $request->app_detail_id;
        $input['adUId'] = $request->adUId;
        $input['isAdShow'] = $request->isAdShow;


        $user   =   AdmobAds::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $data  = AdmobAds::where($where)->first();
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        // $database = AdmobAds::where('id',$request->id)->select('app_detail_id')->first();
        // $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        // if(!in_array($database->app_detail_id,$roleAssignedApplications)){
        //     return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        // }
        
        AdmobAds::where('id',$request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function fetchAdmobAdsList(Request  $request)
    {
        if(request()->ajax()) {

            $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

            $response = array();
            $Filterdata = AdmobAds::select('admob_ads.*','app_details.appName','app_details.packageId as packageId');

            if(isset($request->filter_app_id) && !empty($request->filter_app_id) && ($request->filter_app_id != '-1')){
                $Filterdata = $Filterdata->where('admob_ads.app_detail_id',$request->filter_app_id);
            }

            $Filterdata = $Filterdata->join('app_details', function ($join) {
                $join->on('app_details.id', '=', 'admob_ads.app_detail_id');
            });

            if(!empty($this->roleAssignedApplications)){
                $Filterdata = $Filterdata->whereIn('admob_ads.app_detail_id',$this->roleAssignedApplications);
            }

            if($request->filter_app_id == '-1' && isset($request->filter_accounts_id) && !empty($request->filter_accounts_id) && ($request->filter_accounts_id != '-1') ){
                $Filterdata = $Filterdata->where('app_details.account_id',$request->filter_accounts_id);
            }


            $Filterdata = $Filterdata->orderBy('admob_ads.id','asc')->get();

            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $obj)
                {

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['appName'] = $obj->appName . ' - ' . $obj->packageId;
                    $response[$i]['name'] = $obj->adName;
                    $response[$i]['adUId'] = $obj->adUId;
                    $response[$i]['isAdShow'] = getBooleanStr($obj->isAdShow,true);
                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-admob_ads'))
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
            AdmobAds::where('id',$id)->delete();
        }

        return response()->json(['success'=>"Admob Ads deleted successfully."]);
    }

}
