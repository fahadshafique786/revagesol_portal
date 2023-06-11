<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Accounts;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-permissions', ['only' => ['index','fetchpermissionsdata']]);
        $this->middleware('role_or_permission:super-admin|manage-permissions',['only' => ['edit','store','editProfile','updateProfile','destroy']]);
    }
    public function index(Request $request)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('permissions.index',compact('roles'));
    }

    public function store(Request $request)
    {
        $request->merge(['role_id' => 1]);
        $customMessages = [
            'unique' => ':attribute already exist.'
        ];

        if(!empty($request->id))
        {
            $this->validate($request, [
                'name' => 'required|unique:roles,name,\'.$request->id',
                'role_id' => 'required|integer',
            ], $customMessages);
        }
        else
        {
            $this->validate($request, [
                'name' => 'required|unique:permissions,name',
                'role_id' => 'required|integer',
            ], $customMessages);
        }

        $input = array();
        $input['name'] = $request->name;

        $permission =   \Spatie\Permission\Models\Permission::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);


        $permission->syncRoles($request->role_id);

        return response()->json(['success' => true]);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function edit(Request $request)
    {
        $with = [
            'roles',
        ];
        $where = array('id' => $request->id);
        $user =Permission::with($with)->where($where)->first();
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $permisssion = Permission::where('id',$request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function fetchpermissionsdata()
    {
        if(request()->ajax()) {

            $response = array();
            $Filterdata = Permission::select('*')->orderBy('id','asc')->get();
            if(!empty($Filterdata))
            {
                $i = 0;
                foreach($Filterdata as $index => $permission)
                {


                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['id'] = $permission->id;
                    $response[$i]['name'] = $permission->name;
                    $response[$i]['roles'] = $permission->roles;;
                    //  $response[$i]['email'] = $user->email;
//					$response[$i]['phone'] = $user->phone;
//					$response[$i]['status'] = $status;

                    if(auth()->user()->hasRole("super-admin") OR auth()->user()->can("manage-permissions") )
                    {
                        // if(auth()->user()->hasRole('superadmin'))

//                        $response[$i]['action'] = "";
//                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn editPermission" data-id="'. $permission->id .'"><i class="fa fa-edit  text-info"></i></a>';
                    }
                    else
                    {
                        /*if(!auth()->user()->hasRole('superadmin'))
                            $response[$i]['action'] = '<a href="javascript:void(0)" class="btn editPermission text-info" data-id="'. $permission->id .'"><i class="fa fa-edit"></i></a>';
                        else*/
//                            $response[$i]['action'] = "";
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

    public function updateProfile(Request $request)
    {
        $customMessages = [
            'unique' => ':attribute already registered.'
        ];

        $id = auth()->user()->id;

        $this->validate($request, [
            'name' => 'required',
            'user_name' => 'required|unique:users,user_name,'.$id,
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'required',
        ], $customMessages);

        $input = array();
        $input['name'] = $request->name;
        $input['user_name'] = $request->user_name;
        $input['email'] = $request->email;
        $input['updated_by'] = $id;

        if(!empty($request->password))
            $input['password'] = Hash::make($request->password);

        $input['phone'] = $request->phone;

        $where = array('id' => $id);
        $user   =   User::where($where)->update($input);

        return response()->json(['success' => true]);


    }
}
