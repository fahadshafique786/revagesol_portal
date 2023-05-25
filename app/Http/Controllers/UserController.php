<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Response;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-users', ['only' => ['index','fetchusersdata']]);
        $this->middleware('role_or_permission:super-admin|manage-users', ['only' => ['edit','store','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {$roles = Role::all();
        return view('users',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$customMessages = [
			'unique' => ':attribute already registered.'
		];

        $input = [];

		if(!empty($request->id))
		{
			$this->validate($request, [
				'name' => 'required',
//				'user_name' => 'required|unique:users,user_name,'.$request->id,
//				'email' => 'required|email|unique:users,email,'.$request->id,
			], $customMessages);
		}
		else
		{


			$this->validate($request, [
				'name' => 'required',
				'password' => 'required',
				'user_name' => 'required|unique:users,user_name',
				'email' => 'required|email|unique:users,email',
                'role_id'=>'required|integer'
			], $customMessages);



            $input['user_name'] = $request->user_name;
            $input['email'] = $request->email;

		}

        $input['name'] = $request->name;


		if(!empty($request->password))
			$input['password'] = Hash::make($request->password);


        $user   =   User::updateOrCreate(
                    [
                        'id' => $request->id
                    ],
                    $input);

        $user->syncRoles([$request->role_id]);

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
//        $with = [
//            'roles',
//        ];

		$where = array('id' => $request->id);
        $user  = User::where($where)->first();
        $da  = DB::table('model_has_roles')
            ->select('role_id')
            ->where('model_id',$request->id)
            ->first();


        $user->role_id = (isset($da->role_id)) ? $da->role_id : null;

//        dd($user->id,$user->role_id,$da->role_id);
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

        try {

            $unlikRoleFromUser = DB::table('model_has_roles')->where('model_id',$request->id)->delete();
            $x  = User::where('id',$request->id)->delete();

            return response()->json(['success' => true]);

        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }

        //		    $input['deleted_by'] = auth()->user()->id
        //		    User::where('id',$request->id)->update($input);
        //          $user = User::where('id',$request->id)->delete();
    }

	public function fetchusersdata()
    {
        if(request()->ajax()) {
            $with = [
                'permissions',
                'roles',
                'roles.permissions'
            ];
			$response = array();

			$Filterdata = User::with($with)->select('*');

			$Filterdata = $Filterdata->orderBy('id','asc')->get();


			if(!empty($Filterdata))
			{
				$i = 0;
				foreach($Filterdata as $index => $user)
				{
                    if(!auth()->user()->hasRole("super-admin")){
                        if($user->roles[0]->name == 'super-admin' || $user->roles[0]->id == '1'){
                            continue;
                        }
                    }

                    $status = (!empty($user->is_status)) ? "Active" : "Inactive";

					$response[$i]['srno'] = $i + 1;
					$response[$i]['id'] = $user->id;
					$response[$i]['name'] = $user->name;
					$response[$i]['user_name'] = $user->user_name;
					$response[$i]['email'] = $user->email;
					$response[$i]['role'] = $user->roles;
					$response[$i]['permissions'] = $user->permissions;
//					$response[$i]['phone'] = $user->phone;
//					$response[$i]['status'] = $status;

					if(auth()->user()->hasRole("super-admin") OR auth()->user()->can("manage-users") )
					{
					    if(auth()->user()->id == $user->id){
                            $response[$i]['action'] = '';
                        }
					    else{
                            $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-id="'. $user->id .'"><i class="fa fa-edit  text-info"></i></a>
											<a href="javascript:void(0)" class="btn delete" data-id="'. $user->id .'"><i class="fa fa-trash  text-danger"></i></a>';
                        }
					}
					else
					{
						/*if($user->user_type != "super-admin")
							$response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit text-info" data-id="'. $user->id .'"><i class="fa fa-edit"></i></a>';*/
						//else
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

	public function updateProfile(Request $request)
    {
		$customMessages = [
			'unique' => ':attribute already registered.'
		];

		$id = auth()->user()->id;

		$this->validate($request, [
			'name' => 'required',
		], $customMessages);

		$input = array();
		$input['name'] = $request->name;


        $profile_image = "";
        if($request->hasFile('profile_image'))
        {

            if(!empty($request->id)){

                $getIcon = DB::table('users')->where('id',$request->id)->select('profile_image')->first();
                $serverImagePath = 'uploads/users/'.$getIcon->profile_image;

                removeServerImages($serverImagePath);
            }


            $fileobj				= $request->file('profile_image');
            $file_original_name 	= $fileobj->getClientOriginalName('profile_image');
            $file_extension_name 	= $fileobj->getClientOriginalExtension('profile_image');
            $file_unique_name 		= str_replace(' ','-',strtolower($request->name).'_'.time().rand(1000,9999).'.'.$file_extension_name);
            $destinationPath		= public_path('/uploads/users/');
            $fileobj->move($destinationPath,$file_unique_name);

            $input['profile_image'] = $file_unique_name;
            $profile_image = url('/uploads/users/'). '/' .$file_unique_name;
        }

		if(!empty($request->password))
			$input['password'] = Hash::make($request->password);

		$where = array('id' => $id);
        $user   =   User::where($where)->update($input);

        return response()->json(['success' => true,'profile_image' => $profile_image]);


    }

	public function changePassword(Request $request)
    {

        $getOldPassword = User::select('password')
            ->where('id',$request->user_id)->first();

        $oldPassword = $getOldPassword->password;

		$this->validate($request, [
			'password' => 'required',
		]);

        $validationResponse = [];

		if(!empty($request->current_password)){

            $currentPassword = Hash::make($request->current_password);

            if(Hash::check($request->current_password, $oldPassword)) {

                /******* Update Password ********/

                $input = array();
                $input['password'] = Hash::make($request->password);

                $user   =   User::where('id',$request->user_id)->update($input);

                $request->session()->flush();
                return response()->json(['success' => true]);

            }
		    else{

                $validationResponse['message'] = "The given data was invalid.";
                $validationResponse['errors']['current_password'] = "Current Password not matched!";

                return Response::json($validationResponse,422);
                exit();

            }

		    exit();
        }




    }


}
