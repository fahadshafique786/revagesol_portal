<?php

use Illuminate\Http\Request;
use App\Models\Accounts;
use App\Models\AppDetails;
use Illuminate\Support\Facades\DB;
use App\Models\RoleHasApplication;
use App\Models\Country;
use App\Models\BlockedApplication;
use App\Models\FirebaseCredentials;
use App\Models\AppSettings;
use Illuminate\Support\Facades\Response;

if ( ! function_exists('getAppByPackageId')) {
    function getAppByPackageId($packageId)
    {
        $application = AppDetails::where('packageId',$packageId)->select(['id as application_id','isProxyEnable'])->first();

        return (!empty($application)) ? $application : null;
    }
}

if ( ! function_exists('prepareNewVersionNumbers')) {
    function prepareNewVersionNumbers($versionCategories,$versionNumber){

        $finalValue = null;
        $explodedArray = explode('.',$versionNumber);
        $integerValue = $explodedArray[0];
        $decimalValue = $explodedArray[1];

        if($decimalValue == 9){
            $decimalValue = 0;
            $integerValue++;
            $finalValue =  $integerValue . '.' . $decimalValue;
        }
        else{
            $decimalValue++;
            $finalValue =  $integerValue . '.' . $decimalValue;
        }

        return $finalValue;

    }
}

if ( ! function_exists('getAccountDetailsById')) {
    function getAccountDetailsById($accountId)
    {
        $accounts = Accounts::where('id',$accountId)->first();

        return (!empty($accounts)) ? $accounts : null;
    }
}

if ( ! function_exists('getAppSettingDataByAppId')) {
    function getAppSettingDataByAppId($appID)
    {
        $appSettings = AppSettings::where('app_detail_id',$appID)->first();

        return (!empty($appSettings)) ? $appSettings : null;
    }
}

if ( ! function_exists('getAppListByAccountId')) {
    function getAppListByAccountId($accountId,$applicationId = "")
    {
        $applicationList = AppDetails::where('account_id',$accountId)->select(['app_details.id as application_id','packageId','appName','accounts.name as accountsName'])
            ->join('accounts', function($join){
                $join->on('accounts.id', '=', 'app_details.account_id');
            });

        if($applicationId){
            $applicationList = $applicationList->where('app_details.id',$applicationId);
        }

        $applicationList = $applicationList->get();

        return (!empty($applicationList)) ? $applicationList : null;
    }
}

if ( ! function_exists('getPackageIdByAppId')) {
    function getPackageIdByAppId($appDetailId)
    {
        $application = AppDetails::where('id',$appDetailId)->select(['packageId'])->first();

        return (!empty($application)) ? $application->packageId : null;
    }
}

if ( ! function_exists('getAccountIdByAppId')) {
    function getAccountIdByAppId($appDetailId)
    {
        $application = AppDetails::where('id',$appDetailId)->select(['account_id'])->first();

        return (!empty($application)) ? $application->account_id : null;
    }
}

if ( ! function_exists('getFirebaseCredentialKeysByAppId')) {
    function getFirebaseCredentialKeysByAppId($appDetailId)
    {
        $firebaseCredentials = FirebaseCredentials::where('app_detail_id',$appDetailId)->select(['notificationKey'])->first();

        return (!empty($firebaseCredentials)) ? $firebaseCredentials : null;
    }
}

if ( ! function_exists('checkAppBlockOnAllCountries')) {
    function checkAppBlockOnAllCountries($packageId)
    {
        $application = AppDetails::where('packageId',$packageId)->select(['id as application_id'])->first();

        if(!empty($application)){
            $allCountryBlockCheck = BlockedApplication::where('application_id',$application->application_id)->where('country_id','1');
            if($allCountryBlockCheck->exists()){
                return false;
            }
            else{
                return true;
            }
        }

        return (!empty($application)) ? $application : null;
    }
}

if ( ! function_exists('getBoolean')) {
    function getBoolean($val, $StringResponse = false)
    {
        $bool = "";
        if (is_string($val)) {
            $val = (int)$val;
        }

        if ($StringResponse) {
            $bool = ($val === 1) ? "true" : "false";
        } else {
            $bool = ($val === 1) ? true : false;
        }
        return $bool;
    }
}

if ( ! function_exists('getBooleanStr'))
    {
    function getBooleanStr($val,$StringResponse = false){
        $bool = "";
        if(is_string($val)){
            $val = (int) $val;
        }

        if($StringResponse){
            $bool = ($val === 1) ? "Yes" : "No";
        }
        else{
            $bool = ($val === 1) ? true : false;
        }
        return $bool;
    }

}

if ( ! function_exists('getServerLoad'))
    {
        function getServerLoad(){

            if(file_exists('/proc/stat')){
                $cont = file('/proc/stat');
                $cpuloadtmp = explode(' ',$cont[0]);
                $cpuload0[0] = $cpuloadtmp[2] + $cpuloadtmp[4];
                $cpuload0[1] = $cpuloadtmp[2] + $cpuloadtmp[4]+ $cpuloadtmp[5];
                sleep(1);
                $cont = file('/proc/stat');
                $cpuloadtmp = explode(' ',$cont[0]);
                $cpuload1[0] = $cpuloadtmp[2] + $cpuloadtmp[4];
                $cpuload1[1] = $cpuloadtmp[2] + $cpuloadtmp[4]+ $cpuloadtmp[5];
                return round(($cpuload1[0] - $cpuload0[0])*100/($cpuload1[1] - $cpuload0[1]),3);

            }
            else{

                return 0;

            }

        }

        function getServerLoadX1(){



            if(function_exists('sys_getloadavg')){
                $exec_loads = sys_getloadavg();
                $exec_cores = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
                $cpu = $exec_loads[1]/($exec_cores + 1)*100;

//                $loads=sys_getloadavg();
//                $core_nums=trim(shell_exec("grep -P '^physical id' /proc/cpuinfo|wc -l"));
//                $load=$loads[0]/$core_nums;

                return $cpu ; //round($load,3);
            }
            else{
                return 0;
            }


        }

        function getServerLoad1($windows = false){
            $os=strtolower(PHP_OS);
            if(strpos($os, 'win') === false){
                if(file_exists('/proc/loadavg')){
                    $load = file_get_contents('/proc/loadavg');
                    $load = explode(' ', $load, 1);
                    $load = $load[0];
                }elseif(function_exists('shell_exec')){
                    $load = explode(' ', `uptime`);
                    $load = $load[count($load)-1];
                }else{
                    return false;
                }

                if(function_exists('shell_exec'))
                    $cpu_count = shell_exec('cat /proc/cpuinfo | grep processor | wc -l');

                return array('load'=>$load, 'procs'=>$cpu_count);
            }elseif($windows){
                if(class_exists('COM')){
                    $wmi=new COM('WinMgmts:\\\\.');
                    $cpus=$wmi->InstancesOf('Win32_Processor');
                    $load=0;
                    $cpu_count=0;
                    if(version_compare('4.50.0', PHP_VERSION) == 1){
                        while($cpu = $cpus->Next()){
                            $load += $cpu->LoadPercentage;
                            $cpu_count++;
                        }
                    }else{
                        foreach($cpus as $cpu){
                            $load += $cpu->LoadPercentage;
                            $cpu_count++;
                        }
                    }
                    return array('load'=>$load, 'procs'=>$cpu_count);
                }
                return false;
            }
            return false;
        }

}

if (! function_exists('directoryList')){
    function directoryList ($url) {
        $outp = 0;
        if(dir($url))
        {
            $d = dir($url);
            while($entry = $d->read()) {
                $size = round(filesize($url.$entry)/1024);
                if ($size > 999) $sizestring = ((round($size/100))/10)." mb";
                else $sizestring = $size." kb";
                $outp .= "<a href='".$url.$entry."'>".$entry."</a> [ ".$sizestring." ]<br>\n";
            }
            $outp = "Path: ".$d->path."<br>\n".$outp;
            $d->close();
        }

    }
}

if ( ! function_exists('get_server_memory_usage'))
    {

    //  RAM Consumption function
    function get_server_memory_usage()
    {
        $free = shell_exec('free');
        $free = (string)trim($free);
        $memory_usage = 0;
        if(!empty($free)){
            $free_arr = explode("\n", $free);
            $mem = explode(" ", $free_arr[1]);
            $mem = array_filter($mem);
            $mem = array_merge($mem);
            $memory_usage = round($mem[2]/$mem[1]*100,2);
        }

        return $memory_usage;
    }



}

if ( ! function_exists('getTotalAccounts'))
    {
    function getTotalAccounts(){
        $count = Accounts::all()->count();
        return $count;
    }

}

if ( ! function_exists('getTotalApp'))
    {
    function getTotalApp($count = 0){
        $count = AppDetails::all()->count();
        return $count;
    }

}

if ( ! function_exists('getServerBandwith'))
    {
    function getServerBandwith($val,$StringResponse = false){
        $bool = "";
        if(is_string($val)){
            $val = (int) $val;
        }

        if($StringResponse){
            $bool = ($val === 1) ? "Yes" : "No";
        }
        else{
            $bool = ($val === 1) ? true : false;
        }
        return $bool;
    }

}

if ( ! function_exists('verifyToken')) {
    function verifyToken()
    {

        $token = null;
        $headers = apache_request_headers();

        $response = [];

        // dd($headers);
        if (
            isset($headers['Authorization']) && !empty($headers['Authorization']) &&
            isset($headers['Packageid']) && !empty($headers['Packageid']) &&
            isset($headers['Ipaddress']) && !empty($headers['Ipaddress'])
        ) {

            $streamKey = "";
            $secretKey = "";

            $authToken = $headers['Authorization'];
            $packageId = $headers['Packageid'];
            $ipAddress = $headers['Ipaddress'];

            /*** Split Header Token into the Array ***/

            $authTokenSplitedArray = explode("-", $authToken);
            $userStartTime = (isset($authTokenSplitedArray[3])) ? $authTokenSplitedArray[3] : "";
            $userEndTime = (isset($authTokenSplitedArray[2])) ? $authTokenSplitedArray[2] : "";
            $userSalt = (isset($authTokenSplitedArray[1])) ? $authTokenSplitedArray[1] : "";


            $appDetails = DB::table('app_details')
                ->where('packageId', $packageId)
                ->select('id');

            /*** Get Key From Database By using Package ID ***/

            if ($appDetails->exists()) {
                $appId = $appDetails->first()->id;
                $appCredentials = DB::table('app_credentials')
                    ->select('stream_key', 'server_auth_key')
                    ->where('app_detail_id', $appId);

                if ($appCredentials->exists()) {
                    $appCredentials = $appCredentials->first();
                    $streamKey = $appCredentials->stream_key;
                    $secretKey = $appCredentials->server_auth_key;;
                } else {
                    $appCredentials = 0;
                }

            }


            $userHashString = $streamKey . $ipAddress . $userStartTime . $userEndTime . $secretKey . $userSalt;
            $hashSha1Generated = sha1($userHashString);


            $ourGeneratedToken = $hashSha1Generated . '-' . $userSalt . '-' . $userEndTime . '-' . $userStartTime;


            if ($ourGeneratedToken != $authToken) {
                $response['code'] = 403;
                $response['message'] = "Invalid Token!";
                $response['data'] = null;

                echo json_encode($response);
                // http_response_code(207);
                exit();
            }
        } else {

            $response['code'] = 401;
            $response['message'] = "Unauthorized Request!";
            $response['data'] = null;

            echo json_encode($response);
            // http_response_code(401);
            exit();
        }

        return true;
    }

}

if (!function_exists('removeServerImages')) {
    function removeServerImages($image)
    {
        if (file_exists(public_path($image))) {
            if (unlink(public_path($image))) {
                return true;
            }
        }
    }
}

if (!function_exists('slugify')) {
    function slugify($text, $divider = '-')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
}

if (!function_exists('ip_info')) {
    function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {

            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }

        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));

        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");

        $countries = Country::where('status','1')->get();
        $continents = [];
        foreach($countries as $obj){
            $continents[$obj->country_code] = $obj->country_name;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {


                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }
}

if ( ! function_exists('getApplicationsByRoleId'))
{
    function getApplicationsByRoleId($roleId){

        $applicationsList = RoleHasApplication::select('application_id')->where('role_id',$roleId)->get()->toArray();
        if(!empty($applicationsList)){

            $applicationArray = [];
            foreach($applicationsList as $obj){
                $applicationArray [] = $obj['application_id'];
            }

            return $applicationArray;
//                return rtrim($commaSeparatedIds,',');
        }
        return false;
    }
}

if ( ! function_exists('getAccountssByRoleId'))
{
    function getAccountssByRoleId($roleId){

        $applicationsList = RoleHasApplication::select('application_id')->where('role_id',$roleId)->get()->toArray();
        if(!empty($applicationsList)){

            $applicationArray = [];
            foreach($applicationsList as $obj){
                $applicationArray [] = $obj['application_id'];
            }

            return $applicationArray;
//                return rtrim($commaSeparatedIds,',');
        }
        return false;
    }
}
