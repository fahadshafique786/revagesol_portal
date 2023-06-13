<?php

namespace App\Http\Controllers;

use App\Models\AppDetails;
use App\Models\Accounts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
//        $this->middleware('role_or_permission:view-dashboard', ['only' => ['index']]);

    }

    public function index()
    {
        $accountsList = Accounts::orderBy('id','DESC')->get();

        $appsList = AppDetails::select('app_details.id as id','appName','appLogo','packageId','accounts.name as accounts_name')
            ->join('accounts', function ($join) {
                $join->on('accounts.id', '=', 'app_details.account_id');
            });

        $appsList = $appsList->get();


        return view('dashboard')
            ->with('accountsList',$accountsList)
            ->with('appslist',$appsList);

    }

}
