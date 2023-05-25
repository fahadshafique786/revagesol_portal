<?php

namespace App\Http\Controllers\Firebase;

use App\Models\Sports;
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
        $sportsList = Sports::orderBy('id','DESC')->get();

        return view('firebase.credentials')
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
            $request->validate([
                'app_detail_id' => 'required|unique:firebase_credentials,app_detail_id,'.$request->id,
                'apps_url' => 'required',
                'leagues_url' => 'required',
                'schedules_url' => 'required',
                'servers_url' => 'required',
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

            $leagueUrl = $request->leagues_url;
            $validation = FirebaseCredentials::where('leagues_url',$request->leagues_url)
                ->where('id','!=',$request->id);

            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['leagues_url'] = "This league url already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = FirebaseCredentials::where('schedules_url',$request->schedules_url)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['schedules_url'] = "This league url already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = FirebaseCredentials::where('servers_url',$request->servers_url)
                ->where('id','!=',$request->id);


            if($validation->exists()){
                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['servers_url'] = "This servers url already exists!";

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
                'app_detail_id' => 'required|unique:firebase_credentials,app_detail_id,'.$request->id,
                'apps_url' => 'required',
                'leagues_url' => 'required',
                'schedules_url' => 'required',
                'servers_url' => 'required',
                'app_setting_url' => 'required',
                'reCaptchaKeyId' => 'required|string',
               'notificationKey' => 'nullable|string',
//                'firebaseConfigJson' => 'required',
            ]);

            $validationResponse = [];

            $validation = FirebaseCredentials::where('apps_url',$request->apps_url)
                ->orWhere('leagues_url',$request->apps_url)
                ->orWhere('schedules_url',$request->apps_url)
                ->orWhere('servers_url',$request->apps_url)
                ->orWhere('app_setting_url',$request->apps_url);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['apps_url'] = "This app url  already exists!";

                return Response::json($validationResponse,422);

            }

            $validation = FirebaseCredentials::where('leagues_url',$request->leagues_url)
                ->orWhere('apps_url',$request->leagues_url)
                ->orWhere('schedules_url',$request->leagues_url)
                ->orWhere('servers_url',$request->leagues_url)
                ->orWhere('app_setting_url',$request->leagues_url);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['leagues_url'] = "This league url already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = FirebaseCredentials::where('schedules_url',$request->schedules_url)
                ->orWhere('apps_url',$request->schedules_url)
                ->orWhere('leagues_url',$request->schedules_url)
                ->orWhere('servers_url',$request->schedules_url)
                ->orWhere('app_setting_url',$request->schedules_url);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['schedules_url'] = "This league url already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = FirebaseCredentials::where('servers_url',$request->servers_url)
                ->orWhere('apps_url',$request->servers_url)
                ->orWhere('leagues_url',$request->servers_url)
                ->orWhere('schedules_url',$request->servers_url)
                ->orWhere('app_setting_url',$request->servers_url);

            if($validation->exists()){

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['servers_url'] = "This server url already exists!";

                return Response::json($validationResponse,422);
            }

            $validation = FirebaseCredentials::where('app_setting_url',$request->app_setting_url)
                ->orWhere('apps_url',$request->app_setting_url)
                ->orWhere('leagues_url',$request->app_setting_url)
                ->orWhere('schedules_url',$request->app_setting_url)
                ->orWhere('servers_url',$request->app_setting_url);

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

        $input['apps_url'] = $request->apps_url;
        $input['leagues_url'] = $request->leagues_url;
        $input['schedules_url'] = $request->schedules_url;
        $input['servers_url'] = $request->servers_url;
        $input['app_setting_url'] = $request->app_setting_url;
        $input['reCaptchaKeyId'] = $request->reCaptchaKeyId;
        $input['notificationKey'] = $request->notificationKey;
        $input['firebaseConfigJson'] = $firebaseConfigDataObject;

        $input['app_detail_id'] = $request->app_detail_id;

        $appData = DB::table('app_details')->select('packageId')->where('id',$request->app_detail_id)->first();

        $input['package_id'] = $appData->packageId;

        $user   =   FirebaseCredentials::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

//        dd($input);

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
        $database = FirebaseCredentials::where('id',$request->id)->select('app_detail_id')->first();
        $roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);
        if(!in_array($database->app_detail_id,$roleAssignedApplications)){
            return Response::json(["message"=>"You are not allowed to perform this action!"],403);
        }
        
        FirebaseCredentials::where('id',$request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function getDatabaseCredentialsList(Request  $request)
    {

        if(request()->ajax()) {

            $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

            $response = array();
            $Filterdata = FirebaseCredentials::select('firebase_credentials.*','app_details.appName','app_details.packageId as packageId');


            if(isset($request->filter_app_id) && !empty($request->filter_app_id) && ($request->filter_app_id != '-1')){
                $Filterdata = $Filterdata->where('firebase_credentials.app_detail_id',$request->filter_app_id);
            }

            $Filterdata = $Filterdata->join('app_details', function ($join) {
                $join->on('app_details.id', '=', 'firebase_credentials.app_detail_id');
            });

            if(!empty($this->roleAssignedApplications)){
                $Filterdata = $Filterdata->whereIn('firebase_credentials.app_detail_id',$this->roleAssignedApplications);
            }

            if($request->filter_app_id == '-1' && isset($request->filter_sports_id) && !empty($request->filter_sports_id) && ($request->filter_sports_id != '-1') ){
                $Filterdata = $Filterdata->where('app_details.sports_id',$request->filter_sports_id);
            }

            $Filterdata = $Filterdata->orderBy('firebase_credentials.id','asc')->get();


            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $obj)
                {

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['appName'] = $obj->appName . ' - ' . $obj->packageId;
                    $response[$i]['apps_url'] = $obj->apps_url;
                    $response[$i]['leagues_url'] = $obj->leagues_url;
                    $response[$i]['schedules_url'] = $obj->schedules_url;
                    $response[$i]['servers_url'] = $obj->servers_url;
                    $response[$i]['app_setting_url'] = $obj->app_setting_url;
                    $response[$i]['reCaptchaKeyId'] = $obj->reCaptchaKeyId;
                    $response[$i]['notificationKey'] = $obj->notificationKey;
                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-firebase_configuration'))
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
                ->rawColumns(['action','checkbox'])
                ->make(true);
        }
    }

    public function getAppsOptions(Request $request){

        $this->roleAssignedApplications = getApplicationsByRoleId(auth()->user()->roles()->first()->id);

        $appIdClause = "";
        $sportsIdClause = "";
        $permissionAppIdClause = "";

        if(isset($request->appId) && !empty($request->appId)){
            $appIdClause = " AND app.id != ". $request->appId;
        }
        
        if(!empty($this->roleAssignedApplications)){
            
            $permissionAppIdClause .= " AND app.id IN (".implode(",",$this->roleAssignedApplications).")";
        }


        if(isset($request->sportsId) && !empty($request->sportsId) && ($request->sportsId != "-1")){
            $sportsIdClause .= " AND app.sports_id = ". $request->sportsId;
        }


        DB::enableQueryLog();
        $appListWithoutCredentials = DB::select(DB::raw('
        SELECT *
           FROM app_details app
           WHERE NOT EXISTS (SELECT *
                                FROM firebase_credentials fc
                                WHERE fc.app_detail_id = app.id
                '.$appIdClause.'
            ) 
            '.$permissionAppIdClause.'
            '.$sportsIdClause.'
        '));

        // dd(DB::getQueryLog());

        $options = '<option value="-1">Select App </option>';
        if(!empty($appListWithoutCredentials)){
            foreach($appListWithoutCredentials as $obj){
                $options .= '<option value="'.$obj->id.'">   '  .   $obj->appName  . ' - '  . $obj->packageId   .   '    </option>';
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
