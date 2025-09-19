@extends('layouts.app')

@section('content')

@php if(isset($_COOKIE['login_email']) || isset($_COOKIE['login_pass']))
{
  $login_email = $_COOKIE['login_email'];
  $login_pass  = (isset($_COOKIE['login_pass'])) ? $_COOKIE['login_pass'] : "";
}
else{
  $login_email ='';
  $login_pass = '';
}
if(isset($_COOKIE['login_email']) && isset($_COOKIE['login_pass'])){
  $is_remember = "checked='checked'";
}
else{
  $is_remember = "";
}

//dd(config('app.teamsImagePath'));


@endphp

    <div class="login-box m-auto">
        <!-- /.login-logo -->
        <div class="card card-outline card-">
            <div class="card-header text-center primary-bg-dark">
                <h2 class="heading mb-0 text-white">    Revage Solutions </h2>
<!--                <img src="{{ asset('images/logo.png') }}" class="login-logo" width="200"/>-->
            </div>
            <div class="card-body custom-card-body">
                <h2 class="login-box-msg text-bold">Sign <span class="site-color">In</span></h2>

                @if(Session::has('message'))
                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                @endif


                <form method="POST" action="{{ route('post.login') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input id="email" type="text" placeholder="Email Address or Username" onfocusout="convertLowerCase(this.value)"  class="disableSpecialCharacters form-control @error('email') is-invalid @enderror" name="email" value="{{$login_email}}" required autocomplete="email" autofocus>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input id="password" type="password" placeholder="Password"  class="form-control @error('password') is-invalid @enderror" value="{{$login_pass}}" name="password" required autocomplete="current-password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
{{--                                <input class="form-check-input-o" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>--}}
                                <input type="checkbox" name="rememberme" id="remember" {{$is_remember}}   />
                                <label class="form-check-label" for="remember">

                                    {{ ('Remember me') }}
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" id="LoginSubmitBtn" class="btn btn-dark text-uppercase btn-block">Login</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mb-1">
                @if (Route::has('password.request'))
                    <!--<a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>-->
                    @endif


                </p>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <script>

        window.addEventListener("pageshow", (event) => {

            @if(\Illuminate\Support\Facades\Auth::check())
                window.location.href = {{route('dashboard')}};
                @endif


            const historyTraversal =
                event.persisted ||
                (typeof window.performance != "undefined" &&
                    window.performance.navigation.type === 2);

            if (historyTraversal) {
                window.location.reload();
            }
        });

    </script>
@endsection
