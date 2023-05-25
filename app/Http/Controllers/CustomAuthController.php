<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use Auth;
use Session;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CustomAuthController extends Controller
{

    public function registerform()
    {
        return view('rememberme.signup');
    }

    public function postRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $usersignup = new User;
        $usersignup->name      = $request->name;
        $usersignup->email     = $request->email;
        $usersignup->password  = Hash::make($request->password);
        $usersignup->save();
        return redirect()->route('auth.login');

    }

    public function loginform()
    {
        if(!empty(Auth::user())){
            return redirect()->route('dashboard');
        }
        else{
            return view('auth.login');
        }

    }

    public function checklogin(Request $request)
    {
        $input = $request->all();
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ],[
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',

        ]);

        if($request->rememberme===null){
            setcookie('login_email',$request->email,100);
            setcookie('login_pass',$request->password,100);
        }
        else{
            setcookie('login_email',$request->email,time()+60*60*24*100);
            setcookie('login_pass',$request->password,time()+60*60*24*100);

        }
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']]))
        {
            Session::put('user_session', $input['email']);
            return redirect('/admin/dashboard');
        }
        else if (Auth::attempt(['user_name' => $input['email'], 'password' => $input['password']]))
        {
            Session::put('user_session', $input['email']);
            return redirect('/admin/dashboard');
        }
        else
        {

            $username= User::where('user_name',$input['email'])
                ->orWhere('email',$input['email'])->exists();
            $email = User::where('email',$input['email'])->exists();
            $password = User::where('password',$input['password'])->exists();

            if(!$username) {
                Session::flash('message', 'Invalid username or email address!');
            }
            else if(!$password) {
                Session::flash('message', 'Invalid password!');
            }
            else{
                Session::flash('message', 'Invalid credentials!');
            }

            setcookie('login_email',$request->email,time()+60*60*24*100);
//            setcookie('login_pass',"",time()+60*60*24*100);

            Session::flash('alert-class', 'alert-danger');
            return redirect('/login');



        }
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        Session::forget('user_session');
        return redirect()->route('login');
    }
}
