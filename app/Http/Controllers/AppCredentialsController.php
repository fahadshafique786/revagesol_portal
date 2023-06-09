<?php

namespace App\Http\Controllers;

use App\Models\AppCredentials;
use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\AppDetails;
use Response;
use Illuminate\Support\Facades\DB;

class AppCredentialsController extends Controller
{
    protected $roleAssignedApplications;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-credentials', ['only' => ['index','fetchAppCredentialsList']]);
        $this->middleware('role_or_permission:super-admin|view-applications', ['only' => ['index','fetchAppCredentialsList']]);
        $this->middleware('role_or_permission:super-admin|manage-credentials',['only' => ['edit','store','destroy','deleteAll']]);
        $this->middleware('role_or_permission:super-admin|manage-applications',['only' => ['index','store','destroy','deleteAll']]);
    }

    public function index()
    {
        $appsList = AppDetails::all();
        $accountsList = Accounts::orderBy('id','DESC')->get();

        $appListWithoutCredentials = DB::select(DB::raw('
        SELECT *
       FROM app_details app
       WHERE NOT EXISTS (SELECT *
                                FROM app_credentials ac
                                WHERE ac.app_detail_id = app.id
                                      );
        '));

        return view('credentials.index')
            ->with('accountsList',$accountsList)
            ->with('appsList',$appsList)
            ->with('remainingAppsList',$appListWithoutCredentials);
    }


    public function store(Request $request)
    {
        $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($request->app_detail_id,$roleAssignedApplications)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }

        if(!empty($request->id))
        {
            $validationResponse = [];

            $validation = AppCredentials::where('appSigningKey',$request->appSigningKey)
                ->where('app_detail_id',$request->app_detail_id)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['appSigningKey'] = "The app Signing auth key already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = AppCredentials::where('server_auth_key',$request->server_auth_key)
                ->where('app_detail_id',$request->app_detail_id)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['server_auth_key'] = "The server auth key already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = AppCredentials::where('stream_key',$request->stream_key)
                ->where('app_detail_id',$request->app_detail_id)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['server_auth_key'] = "The stream key already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = AppCredentials::where('token_key',$request->token_key)
                ->where('app_detail_id',$request->app_detail_id)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['token_key'] = "The Token key already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = AppCredentials::where('versionCode',$request->versionCode)
                ->where('app_detail_id',$request->app_detail_id)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['versionCode'] = "The Version Code already exists!";

                return Response::json($validationResponse,422);
            }

        }
        else
        {

            $validationResponse = [];

            $validation = AppCredentials::where('appSigningKey',$request->appSigningKey)
                ->where('app_detail_id',$request->app_detail_id);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['appSigningKey'] = "This app signing auth key already exists!";

                return Response::json($validationResponse,422);

            }

            $validation = AppCredentials::where('server_auth_key',$request->server_auth_key)
                ->where('app_detail_id',$request->app_detail_id);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['server_auth_key'] = "This server auth key already exists!";

                return Response::json($validationResponse,422);

            }

            $validation = AppCredentials::where('stream_key',$request->stream_key)
                ->where('app_detail_id',$request->app_detail_id);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['stream_key'] = "This stream key already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = AppCredentials::where('token_key',$request->token_key)
                ->where('app_detail_id',$request->app_detail_id);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['token_key'] = "This token key already exists!";

                return Response::json($validationResponse,422);
            }


            $validation = AppCredentials::where('versionCode',$request->versionCode)
                ->where('app_detail_id',$request->app_detail_id);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['versionCode'] = "This Version Code already exists!";

                return Response::json($validationResponse,422);
            }

        }

        $input = array();
        $input['server_auth_key'] = $request->server_auth_key;
        $input['stream_key'] = $request->stream_key;
        $input['token_key'] = $request->token_key;
        $input['app_detail_id'] = $request->app_detail_id;
        $input['appSigningKey'] = $request->appSigningKey;
        $input['versionCode'] = $request->versionCode;

        $user   =   AppCredentials::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        return response()->json(['success' => true]);
    }


    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $data  = AppCredentials::where($where)->first();
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        $database = AppCredentials::where('id',$request->id)->select('app_detail_id')->first();
        $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($database->app_detail_id,$roleAssignedApplications)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }
        
        AppCredentials::where('id',$request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function fetchAppCredentialsList(Request  $request)
    {

        if(request()->ajax()) {

            $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

            $response = array();
            $Filterdata = AppCredentials::select('app_credentials.*','app_details.appName','app_details.packageId as packageId');


            if(isset($request->filter_app_id) && !empty($request->filter_app_id) && ($request->filter_app_id != '-1')){
                $Filterdata = $Filterdata->where('app_credentials.app_detail_id',$request->filter_app_id);
            }

            $Filterdata = $Filterdata->join('app_details', function ($join) {
                $join->on('app_details.id', '=', 'app_credentials.app_detail_id');
            });

            if(!empty($this->roleAssignedApplications)){
                $Filterdata = $Filterdata->whereIn('app_credentials.app_detail_id',$this->roleAssignedApplications);
            }

            if($request->filter_app_id == '-1' && isset($request->filter_accounts_id) && !empty($request->filter_accounts_id) && ($request->filter_accounts_id != '-1') ){
                $Filterdata = $Filterdata->where('app_details.account_id',$request->filter_accounts_id);
            }

            $Filterdata = $Filterdata->orderBy('app_credentials.id','asc')->get();


            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $obj)
                {


                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['appName'] = $obj->appName . ' - ' . $obj->packageId;
                    $response[$i]['server_auth_key'] = $obj->server_auth_key;
                    $response[$i]['appSigningKey'] = $obj->appSigningKey;
                    $response[$i]['versionCode'] = $obj->versionCode;
                    $response[$i]['stream_key'] = $obj->stream_key;
                    $response[$i]['token_key'] = $obj->token_key;
                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-credentials'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-application_id="'.$obj->app_detail_id.'" data-id="'. $obj->id .'"><i class="fa fa-edit  text-info"></i></a>
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
                ->rawColumns(['checkbox','action'])
                ->make(true);
        }
    }


    /****** Get Apps List not saved in App Credentials ***********/


    public function getAppsOptions(Request $request){

        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        DB::enableQueryLog();
        $appIdClause = "";
        if(isset($request->appId) && !empty($request->appId)){
            $appIdClause = " OR app.id = ". $request->appId;
        }

        if(isset($request->accountsId) && !empty($request->accountsId) && ($request->accountsId != "-1")){
            $appIdClause .= " AND app.account_id = ". $request->accountsId;
        }

        if(!empty($this->roleAssignedApplications)){
            $appIdClause .= " AND app.id IN (".implode(",",$this->roleAssignedApplications).")";
        }

        $appListWithoutCredentials = DB::select(DB::raw('
        SELECT *
       FROM app_details app
       WHERE NOT EXISTS (SELECT *
                                FROM app_credentials ac
                                WHERE ac.app_detail_id = app.id
                        )
        '.$appIdClause.'
        '));

        $options = '<option value="">Select App </option>';
        if(!empty($appListWithoutCredentials)){
            foreach($appListWithoutCredentials as $obj){
                $options .= '<option value="'.$obj->id.'">   '  .   $obj->appName  . ' - '  . $obj->packageId   .   '    </option>';
            }
        }

        // dd(DB::getQueryLog());

        return $options;
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids); // server Ids

        foreach($idsArray as $id){
            AppCredentials::where('id',$id)->delete();
        }

        return response()->json(['success'=>"App Credentials deleted successfully."]);
    }

}

