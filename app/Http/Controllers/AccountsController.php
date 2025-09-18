<?php

namespace App\Http\Controllers;

use App\Models\AdmobAds;
use App\Models\AppCredentials;
use App\Models\FirebaseCredentials;
use App\Models\SponsorAds;
use Illuminate\Http\Request;
use App\Models\Accounts;
use App\Models\Schedules;
use App\Models\Leagues;
use App\Models\Teams;
use App\Models\Servers;
use App\Models\AppDetails;
use App\Models\RoleHasAccount;
use Response;
use DB;

class AccountsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-accounts', ['only' => ['index','fetchaccountsdata']]);
        $this->middleware('role_or_permission:super-admin|manage-accounts',['only' => ['edit','store','editProfile','updateRole','destroy','deleteAll']]);
    }

    public function index(Request $request)
    {
        $accounts_list = Accounts::all();
        return view('accounts')
            ->with('accounts_list',$accounts_list);
    }

    public function store(Request $request)
    {
        if(!empty($request->id))
        {
            $roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);
            if(!in_array($request->id,$roleAssignedAccounts)){
                return Response::json(["message"=>"You are not allowed to perform this action!"],403);
            }

            $this->validate($request, [
                'name' => 'required|unique:accounts,name,'.$request->id,
            ]);
        }
        else
        {
            $this->validate($request, [
                'name' => 'required|unique:accounts,name,'.$request->id,
            ]);
        }

        $input = array();
        $input['name'] = $request->name;
        $input['image_required'] = $request->image_required;


        if($request->hasFile('sport_logo'))
        {
            if(!empty($request->id)){

                $getIcon = DB::table('accounts')->where('id',$request->id)->select('icon')->first();
                if(!empty($getIcon->icon)){
                    $serverImagePath = 'uploads/accounts/'.$getIcon->icon;
                    removeServerImages($serverImagePath);
                }
            }

            $fileobj				= $request->file('sport_logo');
            $file_extension_name 	= $fileobj->getClientOriginalExtension('sport_logo');
            $file_unique_name 		= str_replace(' ','-',strtolower($request->name).'_'.time().rand(1000,9999).'.'.$file_extension_name);
            $destinationPath		= public_path('/uploads/accounts/');
            $fileobj->move($destinationPath,$file_unique_name);

            $input['icon'] = $file_unique_name;
        }

        $accounts   =   Accounts::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        if(empty($request->id)){
            $roleId = auth()->user()->roles()->first()->id;
            RoleHasAccount::create(["role_id"=> $roleId , "account_id" => $accounts->id ]);
        }

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $accounts  = Accounts::where($where)->first();
        return response()->json($accounts);
    }



    public function destroy(Request $request)
    {
        $roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($request->id,$roleAssignedAccounts)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }

        $getApplications = AppDetails::where('account_id',$request->id)->get();
        foreach($getApplications as $obj){

            AdmobAds::where('app_detail_id',$obj->id)->delete();
            SponsorAds::where('app_detail_id',$obj->id)->delete();
            AppCredentials::where('app_detail_id',$obj->id)->delete();
        }

        AppDetails::where('account_id',$request->id)->delete();

        if(!empty($request->id)){

            $getIcon = DB::table('accounts')->where('id',$request->id)->select('icon')->first();
            if(!empty($getIcon->icon)){
                $serverImagePath = 'uploads/accounts/'.$getIcon->icon;
                removeServerImages($serverImagePath);
            }
        }


        Accounts::where('id',$request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function fetchaccountsdata(Request $request)
    {
        if(request()->ajax()) {

            $response = array();
            $Filterdata = Accounts::select('*');

            if(isset($request->filter_accounts) && !empty($request->filter_accounts)){
                $Filterdata = $Filterdata->where('id',$request->filter_accounts);
            }

            $this->roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);
            if(!empty($this->roleAssignedAccounts)){
                $Filterdata = $Filterdata->whereIn('id',$this->roleAssignedAccounts);
            }

            $Filterdata =  $Filterdata->orderBy('id','DESC')->get();

            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $accounts)
                {
                    $sport_logo =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl text-dark "></i></a>';
                    if(!empty($accounts->icon)){
                        $file = public_path('uploads/accounts'.'/'.$accounts->icon);
                        if(file_exists($file)){
                            $sport_logo = '<img class="dataTable-image" src="'.url("/uploads/accounts/").'/'.$accounts->icon.'" />';
                        }
                    }

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$accounts->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['icon'] = $sport_logo;
                    $response[$i]['name'] = $accounts->name;
                    $response[$i]['image_required'] = getBooleanStr($accounts->image_required,true);
                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-accounts'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-id="'. $accounts->id .'"><i class="fa fa-edit  text-dark"></i></a>
											<a href="javascript:void(0)" class="btn delete " data-id="'. $accounts->id .'"><i class="fa fa-trash-alt text-danger"></i></a>';
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

            $getApplications = AppDetails::where('account_id',$id)->get();
            foreach($getApplications as $obj){

                AdmobAds::where('app_detail_id',$obj->id)->delete();
                SponsorAds::where('app_detail_id',$obj->id)->delete();
                AppCredentials::where('app_detail_id',$obj->id)->delete();
                FirebaseCredentials::where('app_detail_id',$obj->id)->delete();
            }

            AppDetails::where('account_id',$id)->delete();

            $getIcon = DB::table('accounts')->where('id',$id)->select('icon')->first();
            if(!empty($getIcon->icon)){
                $serverImagePath = 'uploads/accounts/'.$getIcon->icon;
                removeServerImages($serverImagePath);
            }

        }

        DB::table("accounts")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>"Accounts deleted successfully."]);
    }


}
