<?php

namespace App\Http\Controllers\Firebase;

use App\Models\AppDetails;
use App\Models\NotificationAdditionalInfo;
use App\Models\PushNotification;
use App\Models\Accounts;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use DB;

class PushNotificationsController extends  BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-manage-push_notifications', ['only' => ['index','getNotificationsList']]);
        $this->middleware('role_or_permission:super-admin|view-manage-push_notifications',['only' => ['saveNotifications','removeNotifications']]);
    }

    public function index()
    {
        $appsList = AppDetails::all();

        $this->roleAssignedAccounts = getAccountsByRoleId(auth()->user()->roles()->first()->id);
        
        if(!empty($this->roleAssignedAccounts)){
            $accountsList = Accounts::whereIn('id',$this->roleAssignedAccounts)->orderBy('id','DESC')->get();
        }
        else{
            $accountsList = Accounts::orderBy('id','DESC')->get();
        }

        return view('firebase.push_notifications')
            ->with('accountsList',$accountsList)
            ->with('appsList',$appsList);
    }

    public function getNotificationsList(Request  $request)
    {
        if(request()->ajax()) {

            $response = [];

            $FilterData = PushNotification::select('push_notifications.*','accounts.name as accountsName','app_details.appName','app_details.packageId as packageId');

            if(isset($request->filter_app_id) && !empty($request->filter_app_id) && ($request->filter_app_id != '-1')){
                $FilterData = $FilterData->where('push_notifications.app_detail_id',$request->filter_app_id);
            }

            $FilterData = $FilterData->leftJoin('accounts', function ($join) {
                $join->on('accounts.id', '=', 'push_notifications.account_id');
            });

            $FilterData = $FilterData->leftJoin('app_details', function ($join) {
                $join->on('app_details.id', '=', 'push_notifications.app_detail_id');
            });

            if($request->filter_app_id == '-1' && isset($request->filter_accounts_id) && !empty($request->filter_accounts_id) && ($request->filter_accounts_id != '-1') ){
                $FilterData = $FilterData->where('app_details.account_id',$request->filter_accounts_id);
            }

            $FilterData = $FilterData->orderBy('push_notifications.id','DESC')->get();

            if(!empty($FilterData))
            {
                $i = 0;
                foreach($FilterData as $index => $obj)
                {

                    $icon =  '<a href="javascript:void(0)" class="" ><i class="fa fa-image text-xl"></i></a>';
                    if(!empty($obj->image)){
                        $file = public_path('uploads/push_notifications'.'/'.$obj->image);
                        if(file_exists($file)){
                            $icon = '<img class="dataTable-image" src="'.url("/uploads/push_notifications/").'/'.$obj->image.'" />';
                        }
                    }

                    $appName = ($obj->app_detail_id) ? $obj->appName . ' - ' . $obj->packageId : $obj->accountsName. " - " . "All Apps";

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['appName'] = $appName;
                    $response[$i]['title'] = $obj->title;
                    $response[$i]['message'] = $obj->message;
//                    $response[$i]['schedule_datetime'] = ($obj->schedule_datetime) ? $obj->schedule_datetime :  "";
                    $response[$i]['image'] = $icon;

                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-firebase_configuration'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-id="'. $obj->id .'"><i class="fa fa-clone  text-info"></i></a>
											<a href="javascript:void(0)" class="btn delete " data-id="'. $obj->id .'"><i class="fa fa-trash-alt text-danger"></i></a>';
                    }
                    else
                    {
                        $response[$i]['action'] = "N/A";
                    }
                    $i++;
                }
            }

            return datatables()->of($response)
                ->addIndexColumn()
                ->rawColumns(['image','checkbox','action'])
                ->make(true);
        }
    }

    public function saveNotifications(Request $request){

        $errors = [];
        $success = [];

        if(!empty($request->id))
        {

            $request->validate([
                'account_id' => 'required',
                'app_detail_id' => 'required',
                'title' => 'required',
                'message' => 'required',
                'image' => 'max:300',
            ]);
        }
        else
        {
            $request->validate([
                'title' => 'required',
                'account_id' => 'required',
                'app_detail_id' => 'required',
                'message' => 'required',
                'image' => 'max:300',
            ]);

        }

        $input = [];
        $input['title'] = $request->title;
        $input['message'] = $request->message;
        $input['account_id'] = $request->account_id;
        $input['app_detail_id'] = ($request->app_detail_id == 'all') ? 0  : $request->app_detail_id;
        $input['schedule_datetime'] = ($request->schedule_datetime && ($request->schedule_datetime != "N/A")) ? $request->schedule_datetime : NULL;

        $fileUniqueName = "";
        $getIcon = DB::table('push_notifications')->where('id',$request->id)->select('image')->first();
        if(isset($getIcon->image) && !empty($getIcon->image)) {
            $fileUniqueName = $getIcon->image;
        }

        /****     Upload Image     ****/

        if($request->hasFile('image'))
        {
            if(!empty($request->id)){

                if(!empty($getIcon->image)){
                    $serverImagePath = 'uploads/push_notifications/'.$getIcon->image;
                    removeServerImages($serverImagePath);
                }
            }

            $fileObj				= $request->file('image');
            $fileExtensionName 	= $fileObj->getClientOriginalExtension('image');
            $fileUniqueName 		= str_replace(' ','-',strtolower($request->title).'_'.time().rand(1000,9999).'.'.$fileExtensionName);
            $destinationPath		= public_path('/uploads/push_notifications/');
            $fileObj->move($destinationPath,$fileUniqueName);

            $input['image'] = $fileUniqueName;
        }
        else{
            $input['image'] = $fileUniqueName;
        }

        $appDetailId = $request->app_detail_id;

        $pushNotification = PushNotification::create($input);

        if(isset($request->key_name) && sizeof($request->key_name) > 0)
        {
            $pushNotificationId = $pushNotification->id;
            $keyNamesList = implode(',', $request->key_name);

            $output = DB::table('notification_additional_infos')
                ->select(['key_name'])
                ->where('push_notification_id',$pushNotificationId)
                ->where(function($query) use ($request){
                    $query->whereNotIn('key_name',$request->key_name);
                    $query->orWhereNotIn('key_value',$request->key_value);
                })
                ->delete();

            $prepareCustomData = [];
            foreach($request->key_name as $index => $keyName){

                $prepareCustomData[$keyName] = $request->key_value[$index];

                $additionalInfoData = [];
                if(empty($keyName)){
                    continue;
                }

                $keyValue = $request->key_value[$index];
                $additionalInfoData['key_name'] = $keyName;
                $additionalInfoData['key_value'] = $keyValue;

                $additionalInfoExistence = NotificationAdditionalInfo::where('push_notification_id',$pushNotificationId)
                    ->where('key_name',$keyName);

                if(!$additionalInfoExistence->exists()){

                    $additionalInfoData['push_notification_id'] = $pushNotificationId;

                    NotificationAdditionalInfo::create($additionalInfoData);

                }
                else{

                    unset($additionalInfoData['key_name']);

                    NotificationAdditionalInfo::where('push_notification_id',$pushNotificationId)
                        ->where('key_name',$keyName)
                        ->update($additionalInfoData);

                }

            }
        }

        if($request->app_detail_id == 'all'){

            $applicationList = getAppListByAccountsId($request->account_id);
            if(!empty($applicationList)){

                $errors = [];
                foreach ($applicationList as $index => $obj){

                    $firebaseCredentials = getFirebaseCredentialKeysByAccountId($request->account_id);

                    if($firebaseCredentials){

                        $notificationKey = (!empty($firebaseCredentials->notificationKey)) ? $firebaseCredentials->notificationKey : NULL;

                        $notificationImage = "";
                        if(!empty($fileUniqueName)){
                            $notificationImage = url("/uploads/push_notifications/").'/'.$fileUniqueName;
                        }

                        if($notificationKey){
                            $this->sendNotification($request->title , $request->message , $obj->packageId , $notificationKey , $prepareCustomData , $notificationImage);
                        }
                        else{
                            $errors[] = $obj->accountsName . ' - ' . $obj->packageId . ' : Notification Key Not Found!';
                        }
                    }
                    else{

                        $errors[] = $obj->accountsName . ' - ' . $obj->packageId . ' : Credentials xxx not found!';

                    }

                }

            }

        }
        else{

            $packageId = getPackageIdByAppId($request->app_detail_id);
            $firebaseCredentials = getFirebaseCredentialKeysByAccountId($request->account_id);
            $accountsDetail = getAccountDetailsById($request->account_id);

            if($firebaseCredentials){

                $notificationKey = (!empty($firebaseCredentials->notificationKey)) ? $firebaseCredentials->notificationKey : NULL;

                /***
                 * CALL TO SEND NOTIFICATION
                 */

                $notificationImage = "";
                if(!empty($fileUniqueName)){
                    $notificationImage = url("/uploads/push_notifications/").'/'.$fileUniqueName;
                }

                if($notificationKey){
                    $this->sendNotification($request->title , $request->message , $packageId , $notificationKey , $prepareCustomData , $notificationImage);
                }
                else{
                    $errors[] = $accountsDetail->name . ' - ' . $packageId . ' : Notification Key Not Found!';
                }
            }
            else{
                $errors[] = $accountsDetail->name . ' - ' . $packageId . ' : Credentials not found!';
            }
        }

        $responseData = [];
        $responseData['notification'] = $pushNotification;
        $responseData['errors'] = $errors;

        return response()->json(['success' => true , 'data' => $responseData]);

    }

    public function sendNotification($title = "" , $body = "" , $topicId = "" , $serverKey = "",  $additionalInformation = [] , $image=""){

        if($serverKey != ""){

            $url = 'https://fcm.googleapis.com/fcm/send';

            $data = [
                'to' => '/topics/'. $topicId,
//                "notification" => [
//                    "body" => $body,
//                    "title" => $title,
//                    "image" => $image,
//                ]
            ];

            $additionalInformation['title'] = $title;
            $additionalInformation['image'] = $image;
            $additionalInformation['message'] = $body;

            if(!empty($additionalInformation)){
                $data['data']  = $additionalInformation;
            }

//            dd($additionalInformation);
            $encodedData = json_encode($data);

            $headers = [
                'Authorization:key=' . $serverKey,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
            // Execute post
            $result = curl_exec($ch);

            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }
            else{
               $result = json_decode($result);
            }

//            dd($result);
            // Close connection
            curl_close($ch);

            return $result;
            exit();


//
//            ini_set("allow_url_fopen", "On");
//            $data =
//                [
//                'to' => '/topics/'. $topicId,
//                "notification" => [
//                    "body" => $body,
//                    "title" => $title,
//                    "image" => $image,
//                ],
//            ];
//
//            if(!empty($additionalInformation)){
//                $data['data']  = $additionalInformation;
//            }
//
//
//            $options = array(
//                'http' => array(
//                    'method'  => 'POST',
//                    'content' => json_encode( $data ),
//                    'header'=>  "Content-Type: application/json\r\n" .
//                        "Accept: application/json\r\n" .
//                        "Authorization:key=".$serverKey
//                )
//            );
//
//            $context  = stream_context_create( $options );
//
//            $result = file_get_contents( "https://fcm.googleapis.com/fcm/send", false, $context );
//            return json_decode( $result );
        }
        return false;
    }

    public function showNotificationDetails(Request $request)
    {
        $where = array('push_notifications.id' => $request->id);

        $PushNotification  = PushNotification::select('push_notifications.*')->where($where)->first();

        $additionalInfo = DB::table('notification_additional_infos')->select(['key_name','key_value'])->where('push_notification_id',$request->id)->get();

        $PushNotification->additional_info = (sizeof($additionalInfo) > 0) ? $additionalInfo : [];

        return response()->json($PushNotification);

    }

    public function removeNotifications(Request $request){

        $getIcon = DB::table('push_notifications')->where('id',$request->id)->select('image')->first();

        if(!empty($getIcon->image)){
            $serverImagePath = 'uploads/push_notifications/'.$getIcon->image;
//            removeServerImages($serverImagePath);
        }

        NotificationAdditionalInfo::where('push_notification_id',$request->id)
            ->delete();

        PushNotification::where('id',$request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function removeAllNotifications(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids); // server Ids

        foreach($idsArray as $id){

            NotificationAdditionalInfo::where('push_notification_id',$id)->delete();

            $getIcon = DB::table('push_notifications')->where('id',$id)->select('image')->first();

            if(!empty($getIcon->image)){
                $imagePath = 'uploads/servers/'.$getIcon->image;
//                removeServerImages($imagePath);
            }

        }

        DB::table("push_notifications")->whereIn('id',explode(",",$ids))->delete();

        return response()->json(['success'=>"Notifications deleted successfully."]);
    }


}

