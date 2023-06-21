<?php

namespace App\Http\Controllers;

use App\Models\AppDetails;
use App\Models\Country;
use App\Models\BlockedApplication;
use App\Models\Accounts;
use Illuminate\Http\Request;
use Response;
use DB;

class CountryController extends Controller
{
    protected $roleAssignedAccounts;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-block-app-countries', ['only' => ['index','showBlockedAppsView','fetchCountryData','getRemainingAppsForBlockedCountriesOptions','fetchBlockedAppsList']]);
        $this->middleware('role_or_permission:super-admin|view-accounts', ['only' => ['index','showBlockedAppsView','fetchCountryData','getRemainingAppsForBlockedCountriesOptions','fetchBlockedAppsList']]);
        $this->middleware('role_or_permission:super-admin|manage-block-app-countries',['only' => ['edit','storeBlockedApplications','syncBlockedApplications','destroy','deleteAll']]);
        $this->middleware('role_or_permission:super-admin|manage-accounts',['only' => ['index','storeBlockedApplications','syncBlockedApplications','destroy','deleteAll']]);
    }

    public function index()
    {
        $countries = Country::orderBy('country_name','ASC')->get();
        return view('countries')
            ->with('countries',$countries);
    }

    public function fetchCountryData()
    {
        if(request()->ajax()) {

            $response = array();

            $filterData = Country::where('status','1')->orderBy('id','ASC')->get();

            if(!empty($filterData))
            {
                $i = 0;
                foreach($filterData as $index => $obj)
                {

                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['country_code'] = $obj->country_code;
                    $response[$i]['country_name'] = $obj->country_name;

                    $i++;
                }
            }

            return datatables()->of($response)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getRemainingAppsForBlockedCountriesOptions(Request $request){ // for edit case

        $this->roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);

        $appIdClause = "";
        $accountsIdClause = "";
        $permissionAppIdClause = "";

        DB::enableQueryLog();

        if(!empty($request->id) && $request->id != "-1"){
            $appIdClause = " AND app.id != ". $request->id;
        }

        if(!empty($request->account_id) && $request->account_id != "-1"){
            $accountsIdClause = " AND app.account_id = ". $request->account_id;
        }

        if(!empty($this->roleAssignedAccounts)){
            $permissionAppIdClause .= " AND app.account_id IN (".implode(",",$this->roleAssignedAccounts).")";
        }

        $remainingApplications = DB::select(DB::raw('SELECT * FROM app_details app WHERE NOT EXISTS (SELECT * FROM blocked_applications bap WHERE bap.application_id = app.id '.$appIdClause.' ) '.$permissionAppIdClause.' '.$accountsIdClause));

        $options = '<option value="">Select App </option>';
        if(!empty($remainingApplications)){
            foreach($remainingApplications as $obj){

                $selected = ($obj->id == $request->id) ?  "selected": "";
                $options .= '<option value="'.$obj->id.'" '. $selected . '>   '  .   $obj->appName   .  ' - ' . $obj->packageId . '    </option>';
            }
        }

        // dd(DB::getQueryLog());

        return $options;

    }
    public function showBlockedAppsView()
    {
        $remainingApplications = DB::select(DB::raw('SELECT * FROM app_details app WHERE NOT EXISTS (SELECT * FROM blocked_applications bap WHERE bap.application_id = app.id ) '));

        $countries = Country::all();

        $this->roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);
        if(!empty($this->roleAssignedAccounts)){
            $accountsList = Accounts::whereIn('id',$this->roleAssignedAccounts)->orderBy('id','DESC')->get();
        }
        else{
            $accountsList = Accounts::orderBy('id','DESC')->get();
        }
        
        return view('application.blocked_applications')
            ->with('applications',$remainingApplications)
            ->with('accountsList',$accountsList)
            ->with('countries',$countries);
    }

    public function fetchBlockedAppsList(Request $request)
    {
        if(request()->ajax()) {

            $this->roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);

            $response = array();
            $FilterData = AppDetails::select(['app_details.id as app_id','app_details.packageId','app_details.appName','app_details.isProxyEnable']);

            if(!empty($this->roleAssignedAccounts)){
                $FilterData = $FilterData->whereIn('account_id',$this->roleAssignedAccounts);
            }

            if(isset($request->filter_accounts_id) && !empty($request->filter_accounts_id) && $request->filter_accounts_id != '-1'){
                $FilterData = $FilterData->where('app_details.account_id',$request->filter_accounts_id);
            }

            $FilterData = $FilterData->get();

            if(!empty($FilterData))
            {
                $i = 0;
                foreach($FilterData as $index => $obj)
                {
                    $blockedApplications = DB::table('blocked_applications')->select(DB::raw("GROUP_CONCAT(country_id) as country_ids"))->where('application_id',$obj->app_id)->first();

                    if(!empty($blockedApplications) && ($blockedApplications->country_ids != null)){

                        $countryIdsCollection = explode(',',$blockedApplications->country_ids);
                        $countries = DB::table('countries')->select(DB::raw("GROUP_CONCAT(country_name) as country_names"))->whereIn('id',$countryIdsCollection)->first();

                        $countryNamesCollection = explode(',',$countries->country_names);

                        $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->app_id.'">';
                        $response[$i]['srno'] = $i + 1;
                        $response[$i]['appName'] = $obj->appName  . ' - ' . $obj->packageId;
                        $response[$i]['countries'] = $countryNamesCollection;

                        if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-block-app-countries'))
                        {
                            $liveSwitch = ($obj->isProxyEnable) ? 'active focus' : '';
                            $switchBool = ($obj->isProxyEnable) ? 'true' : 'false';

                            $response[$i]['is_proxy_enable'] = '<a type="button" class="btn btn-sm btn-toggle SwitchScheduleStatus isLiveStatusSwitch '.$liveSwitch.' "  data-id="is_live_status-'.$obj->app_id.'" data-app-id="'.$obj->app_id.'"  data-toggle="button" aria-pressed="'.$switchBool.'" autocomplete="off"><div class="handle"></div></a>';

                            $response[$i]['action'] = '

                            <a href="javascript:void(0)" class="btn edit" data-id="'. $obj->app_id .'"><i class="fa fa-edit  text-info"></i></a>
							<a href="javascript:void(0)" class="btn delete " data-id="'. $obj->app_id .'"><i class="fa fa-trash-alt text-danger"></i></a>';



                        }
                        else
                        {
                            $response[$i]['action'] = "-";
                            $response[$i]['is_proxy_enable'] = "-";
                        }

                        $i++;
                    }
                }
            }

            return datatables()->of($response)
                ->addIndexColumn()
                ->rawColumns(['srno','checkbox','is_proxy_enable','action'])
                ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $blockedAppsCountries = DB::table('blocked_applications')->select('country_id')->where('application_id',$request->id)->get();
        $blockedAppsData = [];
        $blockedAppsData['application_id'] = $request->id;
        $blockedAppsData['countries'] = $blockedAppsCountries;
        return response()->json($blockedAppsData);
    }

    public function destroy(Request $request)
    {
        $account_id = getAccountIdByAppId($request->id);
        $roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($account_id,$roleAssignedAccounts)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }
        
        AppDetails::where('id',$request->id)->update(['isProxyEnable'=>'0']);
        BlockedApplication::where('application_id',$request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function storeBlockedApplications(Request $request)
    {
        $account_id = getAccountIdByAppId($request->application_id);
        $roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($account_id,$roleAssignedAccounts)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }

        $this->syncBlockedApplications($request->application_id,$request->country_ids,$request->id);
        return response()->json(['success' => true]);
    }

    public function syncBlockedApplications($applicationsId,$countryIds,$editApplicationId = ""){

        if($editApplicationId){
            DB::table('blocked_applications')->where('application_id',$editApplicationId)->update(['application_id' => $applicationsId]);
        }

        $blockedAppsCountriesFromDB = DB::table('blocked_applications')->select('application_id','country_id')->where('application_id',$applicationsId)->get()->toArray();

        $array1 = [];
        foreach($blockedAppsCountriesFromDB as $obj){
            $array1[] = $obj->country_id;
        }

        $removableAppsCountries = array_diff($array1,$countryIds); // these country ids will remove form the table
        $newAppsCountries = array_diff($countryIds,$array1); // these ids must be new and will in the table as well

        if(!empty($removableAppsCountries)){
            BlockedApplication::whereIn('country_id',$removableAppsCountries)->where('application_id',$applicationsId)->delete();
        }

        foreach($newAppsCountries as $countryId){
            $blockedApps = [];
            $blockedApps['application_id'] = $applicationsId;
            $blockedApps['country_id'] = $countryId;

            BlockedApplication::create($blockedApps);
        }

        return true;
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;

        AppDetails::whereIn('id',explode(",",$ids))->update(['isProxyEnable'=>'0']);
        DB::table("blocked_applications")->whereIn('application_id',explode(",",$ids))->delete();

        return response()->json(['success'=>"Data has been removed successfully."]);
    }




}
