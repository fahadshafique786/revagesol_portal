<?php

namespace App\Http\Controllers\Firebase;

use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\AppDetails;
use App\Models\FirebaseCredentials;
use Illuminate\Routing\Controller as BaseController;
use DB;
use Response;

class DatabaseCredentials extends BaseController
{
    protected $roleAssignedApplications;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-firebase_configuration', ['only' => ['index','getDatabaseCredentialsList']]);
        $this->middleware('role_or_permission:super-admin|view-applications', ['only' => ['index','getDatabaseCredentialsList']]);
        $this->middleware('role_or_permission:super-admin|manage-firebase_configuration',['only' => ['edit','store','destroy','deleteAll']]);
        $this->middleware('role_or_permission:super-admin|manage-applications',['only' => ['edit','store','destroy','deleteAll']]);
    }

    public function index()
    {
        $appsList = AppDetails::all();
        $accountsList = Accounts::orderBy('id','DESC')->get();

        $accountListWithoutCredentials = DB::select(DB::raw('
        SELECT *
           FROM accounts acc
           WHERE NOT EXISTS (SELECT *
                                FROM app_credentials ac
                                WHERE ac.account_id = acc.id
                                      );
        '));


        return view('firebase.credentials')
            ->with('accountsList',$accountsList)
            ->with('remainingAccountList',$accountListWithoutCredentials)
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
            $request->validate([
                // 'app_detail_id' => 'required|unique:firebase_credentials,app_detail_id,'.$request->id,
                'account_id' => 'required',
                'apps_url' => 'required',
                'app_setting_url' => 'required',
                'reCaptchaKeyId' => 'required|string',
                'notificationKey' => 'nullable|string',
//                'firebaseConfigJson' => 'required',
            ]);

            $validationResponse = [];

            $validation = FirebaseCredentials::where('apps_url',$request->apps_url)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['apps_url'] = "This app url  already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = FirebaseCredentials::where('app_setting_url',$request->app_setting_url)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['app_setting_url'] = "This app setting url already exists!";

                return Response::json($validationResponse,422);
            }

        }
        else
        {

           $request->validate([
                // 'app_detail_id' => 'required|unique:firebase_credentials,app_detail_id,'.$request->id,
                'account_id' => 'required',
                'apps_url' => 'required',
                'app_setting_url' => 'required',
                'reCaptchaKeyId' => 'required|string',
               'notificationKey' => 'nullable|string',
            ]);

            $validationResponse = [];

            $validation = FirebaseCredentials::where('apps_url',$request->apps_url)
                ->orWhere('app_setting_url',$request->apps_url);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['apps_url'] = "This app url  already exists!";

                return Response::json($validationResponse,422);

            }

            $validation = FirebaseCredentials::where('app_setting_url',$request->app_setting_url)
                ->orWhere('apps_url',$request->app_setting_url);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['app_setting_url'] = "This app setting url already exists!";

                return Response::json($validationResponse,422);
            }

        }

        $input = [];

        /**************    Create Firebase Config Json      ***************/

        $firebaseConfigData = [];
        foreach($request->key_name as $index => $keyName) {

            if (empty($keyName)) {
                continue;
            }


            $keyValue = $request->key_value[$index];
            $firebaseConfigData[$keyName] = $keyValue;
        }

        $firebaseConfigDataObject = (object) $firebaseConfigData;
        $firebaseConfigDataObject = json_encode($firebaseConfigDataObject);

        $input['account_id'] = $request->account_id;
        $input['apps_url'] = $request->apps_url;
        $input['app_setting_url'] = $request->app_setting_url;
        $input['reCaptchaKeyId'] = $request->reCaptchaKeyId;
        $input['notificationKey'] = $request->notificationKey;
        $input['firebaseConfigJson'] = $firebaseConfigDataObject;

        // $input['app_detail_id'] = $request->app_detail_id;

        // $appData = DB::table('app_details')->select('packageId')->where('id',$request->app_detail_id)->first();

        // $input['package_id'] = $appData->packageId;

        $user   =   FirebaseCredentials::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        return response()->json(['success' => true]);
    }


    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $data  = FirebaseCredentials::where($where)->first();
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        // $database = FirebaseCredentials::where('id',$request->id)->select('app_detail_id')->first();
        // $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        // if(!in_array($database->app_detail_id,$roleAssignedApplications)){
        //     return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        // }
        
        FirebaseCredentials::where('id',$request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function getDatabaseCredentialsList(Request  $request)
    {

        if(request()->ajax()) {

            // $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

            $response = array();
            $Filterdata = FirebaseCredentials::select('firebase_credentials.*','accounts.name as accountName');


            // if(isset($request->filter_app_id) && !empty($request->filter_app_id) && ($request->filter_app_id != '-1')){
            //     $Filterdata = $Filterdata->where('firebase_credentials.app_detail_id',$request->filter_app_id);
            // }

            $Filterdata = $Filterdata->join('accounts', function ($join) {
                $join->on('accounts.id', '=', 'firebase_credentials.account_id');
            });

            // if(!empty($this->roleAssignedApplications)){
            //     $Filterdata = $Filterdata->whereIn('firebase_credentials.app_detail_id',$this->roleAssignedApplications);
            // }

            if($request->filter_app_id == '-1' && isset($request->filter_accounts_id) && !empty($request->filter_accounts_id) && ($request->filter_accounts_id != '-1') ){
                $Filterdata = $Filterdata->where('accounts.id',$request->filter_accounts_id);
            }

            $Filterdata = $Filterdata->orderBy('firebase_credentials.id','asc')->get();


            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $obj)
                {

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['account'] = $obj->accountName;
                    $response[$i]['apps_url'] = $obj->apps_url;
                    $response[$i]['app_setting_url'] = $obj->app_setting_url;
                    $response[$i]['reCaptchaKeyId'] = $obj->reCaptchaKeyId;
                    $response[$i]['notificationKey'] = $obj->notificationKey;
                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-firebase_configuration'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-account_id="'.$obj->account_id.'" data-id="'. $obj->id .'"><i class="fa fa-edit  text-info"></i></a>
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
                ->rawColumns(['action','checkbox'])
                ->make(true);
        }
    }

    public function getAppsOptions(Request $request){

        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        $appIdClause = "";
        $accountsIdClause = "";
        $permissionAppIdClause = "";

        if(isset($request->account_id) && !empty($request->account_id)){
            $accountsIdClause = " OR acc.id = ". $request->account_id;
        }
        
        // if(!empty($this->roleAssignedApplications)){            
        //     $permissionAppIdClause .= " AND app.id IN (".implode(",",$this->roleAssignedApplications).")";
        // }


        if(isset($request->accountsId) && !empty($request->accountsId) && ($request->accountsId != "-1")){
            $accountsIdClause .= " AND acc.id = ". $request->accountsId;
        }


        DB::enableQueryLog();
        $accountListWithoutCredentials = DB::select(DB::raw('
        SELECT *
        FROM accounts acc
           WHERE NOT EXISTS (SELECT *
                                FROM firebase_credentials fc
                                WHERE fc.app_detail_id = acc.id
                '.$appIdClause.'
            ) 
            '.$permissionAppIdClause.'
            '.$accountsIdClause.'
        '));

        // dd(DB::getQueryLog());

        $options = '<option value="-1">Select App </option>';
        if(!empty($accountListWithoutCredentials)){
            foreach($accountListWithoutCredentials as $obj){
                $options .= '<option value="'.$obj->id.'">   '  .   $obj->name .   '    </option>';
            }
        }

        return $options;
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids); // server Ids

        foreach($idsArray as $id){
            FirebaseCredentials::where('id',$id)->delete();
        }

        return response()->json(['success'=>"Firebase Credentials deleted successfully."]);
    }


}
