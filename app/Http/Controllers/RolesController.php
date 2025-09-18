<?php

namespace App\Http\Controllers;

use App\Models\AppDetails;
use App\Models\RoleHasAccount;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Accounts;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-roles', ['only' => ['index','fetchRolesdata']]);
        $this->middleware('role_or_permission:super-admin|manage-roles',['only' => ['edit','store','editProfile','updateRole','destroy','syncApplications']]);
    }

    public function index(Request $request)
    {
        $permissions = Permission::all();
        $applicationList = AppDetails::all();
        $accountsList = Accounts::orderBy('id','DESC')->get();

        return view('roles.index')
            ->with('accountsList',$accountsList)
            ->with('permissions',$permissions)
            ->with('applications',$applicationList);
    }

    public function store(Request $request)
    {
        $customMessages = [
            'unique' => ':attribute already exist.'
        ];

        if(!empty($request->id))
        {
            $this->validate($request, [
                'name' => 'required|string',
                 Rule::unique('roles', 'name')->ignore($request->id),
                'permissions' => 'required|array',

            ], $customMessages);
        }
        else
        {
            $this->validate($request, [
                'name' => 'required|unique:roles,name',
                'permissions' => 'required|array',
            ], $customMessages);
        }

        $input = array();
        $input['name'] = $request->name;

        $role  =  Role::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        $role->syncPermissions($request->permissions);

        if((in_array("9",$request->permissions)) && (in_array("10",$request->permissions))){
            $this->syncApplications($request->account_id,$role);
        }
        else{
            RoleHasAccount::where('role_id',$role->id)->delete();
        }

        return response()->json(['success' => true]);
    }

    public function syncApplications($accountIds,$role){

        $roleHasAccountsFromDB = DB::table('role_has_accounts')->select('account_id')->where('role_id',$role->id)->get()->toArray();

        $array1 = [];
        foreach($roleHasAccountsFromDB as $obj){
            $array1[] = $obj->account_id;
        }

        $removableAccounts = array_diff($array1,$accountIds); // these acccounts ids will remove form the table
        $newAccounts = array_diff($accountIds,$array1); // these ids must be new and will in the table as well

        if(!empty($removableAccounts)){
            RoleHasAccount::whereIn('account_id',$removableAccounts)->where('role_id',$role->id)->delete();
        }

        foreach($newAccounts as $accountId){
            $roleAssignedAccounts = [];
            $roleAssignedAccounts['account_id'] = $accountId;
            $roleAssignedAccounts['role_id'] = $role->id;

            RoleHasAccount::create($roleAssignedAccounts);
        }

        return true;
    }

    public function edit(Request $request)
    {
        $with = [
            'permissions',
        ];
        $where = array('id' => $request->id);
        $rolesData  = Role::with($with)->where($where)->first();
        $roleHasAccount = RoleHasAccount::where('role_id', $request->id)->get();
        // $roleHasAccounts =  DB::table('role_has_applications')
        // ->where('role_id',$request->id)
        // ->select('account_id')
        // ->distinct()
        // ->get();

        if(sizeOf($roleHasAccount) <= 0){
            $roleHasAccount = null;
        }
        else{
            $rolesData['account_id'] = $roleHasAccount[0]->account_id;
        }

        $rolesData['role_has_accounts'] = $roleHasAccount;
        // $rolesData['role_has_accounts_id'] = $roleHasAccounts;

        return response()->json($rolesData);
        exit();
    }

    public function destroy(Request $request)
    {
        $user = RoleHasAccount::where('role_id',$request->id)->delete();
        $user = Role::where('id',$request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function fetchRolesdata()
    {
        if(request()->ajax()) {

            $response = array();
            $Filterdata = Role::select('*')->orderBy('id','asc')->get();
            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $role)
                {
                    if(!auth()->user()->hasRole("super-admin")){
                        if($role->name == 'super-admin' || $role->id == '1'){
                            continue;
                        }
                    }


                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['id'] = $role->id;
                    $response[$i]['name'] = $role->name;
                    $response[$i]['permissions'] = $role->permissions;;

                    if(auth()->user()->hasRole("super-admin") OR auth()->user()->can("manage-roles") )
                    {
                        if(auth()->user()->roles[0]->id == $role->id){
                            $response[$i]['action'] = '';
                        }
                        else{

                            $response[$i]['action'] = '<a href="javascript:void(0)" class="btn  editRole" data-id="'. $role->id .'"><i class="fa fa-edit  text-dark"></i></a>
											<a href="javascript:void(0)" class="btn  delete" data-id="'. $role->id .'"><i class="fa fa-trash text-danger"></i></a>';

                        }


                    }
                    else
                    {
                        /*if(!auth()->user()->hasRole('superadmin'))
                            $response[$i]['action'] = '<a href="javascript:void(0)" class="btn editRole text-info" data-id="'. $role->id .'"><i class="fa fa-edit"></i></a>';
                        else*/
                            $response[$i]['action'] = "-";
                    }
                    $i++;
                }
            }

            return datatables()->of($response)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function editProfile(Request $request)
    {
        $id = auth()->user()->id;
        $where = array('id' => $id);
        $user  = User::where($where)->first();
        return response()->json($user);
    }

    public function updateRole(Request $request)
    {
        $customMessages = [
            'unique' => ':attribute already registered.'
        ];

        $id = auth()->user()->id;

        $this->validate($request, [
            'name' => 'required|string',
            Rule::unique('roles', 'name')->ignore($id),
            'permissions' => 'required|array',
        ], $customMessages);

        $input = array();
        $input['name'] = $request->name;

        $where = array('id' => $id);
        $role  =   Role::where($where)->update($input);
        $role -> syncPermissions($request->permissions);

        return response()->json(['success' => true]);
    }
}
