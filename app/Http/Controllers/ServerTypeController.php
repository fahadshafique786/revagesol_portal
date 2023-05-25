<?php

namespace App\Http\Controllers;

use App\Models\ServerHeaders;
use App\Models\Servers;
use App\Models\ServerTypes;
use App\Models\Sports;
use App\Models\ScheduledServers;
use App\Models\Schedules;
use Illuminate\Http\Request;
use DB;
use MongoDB\Driver\Server;

class ServerTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:super-admin|view-server-types', ['only' => ['index','fetchServerTypesData']]);
        $this->middleware('role_or_permission:super-admin|manage-server-types',['only' => ['edit','store','destroy','deleteAll']]);
    }

    public function index(Request $request)
    {
        $serverTypeList = ServerTypes::orderBy('id','DESC')->get();
        return view('server_types')
            ->with('serverTypes',$serverTypeList);
    }

    public function store(Request $request)
    {
        if(!empty($request->id)) // edit case
        {
            $this->validate($request, [
                'name' => 'required|unique:server_types,name,'.$request->id,
            ]);
        }
        else
        {
            $this->validate($request, [
                'name' => 'required|unique:server_types,name',
            ]);
        }

        $input = array();

        $input['name'] = $request->name;
        $input['label'] = slugify($request->name);


        $servers   =   ServerTypes::updateOrCreate(
            [
                'id' => $request->id
            ],
            $input);

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $servers  = ServerTypes::where($where)->first();
        return response()->json($servers);
    }

    public function destroy(Request $request)
    {
        Servers::where('server_type_id',$request->id)->delete();

        ServerTypes::where('id',$request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function fetchServerTypesData(Request $request , $schedule_id = null)
    {
        if(request()->ajax()) {

            $response = array();

            $filterData = ServerTypes::where('status','1')->orderBy('id','DESC')->get();

            if(!empty($filterData))
            {
                $i = 0;
                foreach($filterData as $index => $obj)
                {

                    $response[$i]['checkbox'] = '<input type="checkbox" class="sub_chk" data-id="'.$obj->id.'">';
                    $response[$i]['srno'] = $i + 1;
                    $response[$i]['name'] = $obj->name;
                    $response[$i]['slug'] = $obj->label;

                    if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-servers'))
                    {
                        $response[$i]['action'] = '<a href="javascript:void(0)" class="btn edit" data-id="'. $obj->id .'"><i class="fa fa-edit  text-info"></i></a>
											<a href="javascript:void(0)" class="btn delete hide " data-id="'. $obj->id .'"><i class="fa fa-trash-alt text-danger"></i></a>';
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
                ->rawColumns(['sponsorAdImageUrl','checkbox','action'])
                ->make(true);
        }
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $idsArray = explode(",",$ids); // server Ids

        foreach($idsArray as $id){

            Servers::where('server_type_id',$id)->delete();
        }

        DB::table("server_types")->whereIn('id',explode(",",$ids))->delete();

        return response()->json(['success'=>"Server Type deleted successfully."]);
    }



}
