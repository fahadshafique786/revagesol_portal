<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> {{ config('app.name', 'Laravel') }}</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- overlayScrollbars -->

  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- overlayScrollbars -->

  <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">



  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css">

    <style>
        #cover-spinner {
            position:fixed;
            width:100%;
            left:0;right:0;top:0;bottom:0;
            background-color: rgba(255,255,255,0.7);
            z-index:9999;
            display:none;
        }

        @-webkit-keyframes spin {
            from {-webkit-transform:rotate(0deg);}
            to {-webkit-transform:rotate(360deg);}
        }

        @keyframes spin {
            from {transform:rotate(0deg);}
            to {transform:rotate(360deg);}
        }

        #cover-spinner::after {
            content:'';
            display:block;
            position:absolute;
            left:48%;top:40%;
            width:40px;height:40px;
            border-style:solid;
            border-color:black;
            border-top-color:transparent;
            border-width: 4px;
            border-radius:50%;
            -webkit-animation: spin .8s linear infinite;
            animation: spin .8s linear infinite;
        }

    </style>

</head>
<body id="customBody" class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed text-sm">
<div id="cover-spinner"></div>

<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark custom-nav">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a  id="closeSideMenuId" class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-arrow-left close-sidebar"></i></a>
        <a  id="openSideMenuId" class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars  open-sidebar"></i></a>
      </li>

		</ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item">
            <a class="nav-link"  href="javascript:void(0)" role="button">
                <i id="fullScreenId" class="fas fa-expand-arrows-alt maximize-screen"></i>
                <i id="defaultScreenId" class="fas fa-compress-arrows-alt minimize-screen"></i>
            </a>
        </li>

		<li class="nav-item dropdown">
			<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
				<span id="UserProfileName"> {{ Auth::user()->name }}     </span><span class="caret"></span>
                <img id="UserProfileImg" src="{{ (Auth::user()->profile_image && (file_exists(public_path('uploads/users'.'/'.Auth::user()->profile_image)))) ? asset('uploads/users'). '/' .Auth::user()->profile_image  : asset('dist/img/avatar-custom.png')   }}" class="img-circle elevation-2 image-width-20 " alt="User Image"/>
			</a>


			<div class="dropdown-menu custom" aria-labelledby="navbarDropdown">

				<a class="dropdown-item profile" href="javascript:void(0)">
					Profile
				</a>

				<a class="dropdown-item" href="{{ route('logout') }}"
				   onclick="event.preventDefault();
								 document.getElementById('logout-form').submit();">
					{{ __('Logout') }}
				</a>


				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			</div>
		</li>
    </ul>
  </nav>
  <!-- /.navbar -->

@extends('layouts.sidebar')

@php
	$link_label_array['dashboard'] = "Dashboard";
	$link_label_array['users'] = "User Administrator";
	$link_label_array['sports'] = "Sports";
	$link_label_array['teams'] = "Teams";
	$link_label_array['schedules'] = "Schedules";
	$link_label_array['leagues'] = "Leagues";
	$link_label_array['servers'] = "Servers";
	$link_label_array['roles'] = "Roles";
	$link_label_array['permissions'] = "Permissions";
	$link_label_array['app'] = "Applications";
	$link_label_array['sponsors'] = "Sponsor Ads";
	$link_label_array['admob_ads'] = "Admob Ads";
	$link_label_array['credentials'] = "App Credentials";
	$link_label_array['firebase-credentials'] = "Firebase Credentials";
	$link_label_array['sync-data'] = "Firebase Synchronization";
	$link_label_array['server-types'] = "Server Types";
	$link_label_array['country'] = "Countries";
	$link_label_array['app_settings'] = "App Settings";

	$link_label_array1['sports']['icon'] = "sports.png";
	$link_label_array1['teams']['icon']  = "team.png";
	$link_label_array1['schedules']['icon'] = "schedule.png";
	$link_label_array1['leagues']['icon'] = "league.png";
	$link_label_array1['servers']['icon'] = "serverss.png";
	$link_label_array1['server-types']['icon'] = "servers.png";
	$link_label_array1['app']['icon'] = "app.png";

@endphp

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper contentContainer">
      <div id="cover-spin">Processing...</div>

      <!-- Content Header (Page header) -->
    <div class="content-header" style="padding-bottom: 0;">
      <div class="container-fluid">
        <div class="row d-none">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">

				@if(Request::segment(2) == 'dashboard')
					<i class="d-inline fas fa-tachometer-alt  icon-bg nav-icon pl-1"></i>
				@elseif(Request::segment(2) == 'teams')
					<img src="{{ asset('dist/img/sidebar-icons/team.png') }}" class="elevation-2 icon-bg vertical-baseline"/>
				@elseif(Request::segment(2) == 'sports')
					<img src="{{ asset('dist/img/sidebar-icons/sports.png') }}" class="elevation-2 icon-bg vertical-baseline"/>
				@elseif(Request::segment(2) == 'schedules')
					<img src="{{ asset('dist/img/sidebar-icons/schedule.png') }}" class="elevation-2 icon-bg vertical-baseline "/>
				@elseif(Request::segment(2) == 'leagues')
					<img src="{{ asset('dist/img/sidebar-icons/league.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
				@elseif(Request::segment(2) == 'servers' || Request::segment(2) == 'server-types' )
					<img src="{{ asset('dist/img/sidebar-icons/serverss.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
                @elseif(Request::segment(2) == 'app' || Request::segment(2) == 'block-applications')
                    <img src="{{ asset('dist/img/sidebar-icons/application.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
                @elseif(Request::segment(2) == 'users' || Request::segment(2) == 'permissions'  || Request::segment(2) == 'roles' )
                        <i class="fas fa-users icon-bg  vertical-super"></i>
                @elseif(Request::segment(2) == 'sponsors')
                    <img src="{{ asset('dist/img/sidebar-icons/sponsor.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
                @elseif(Request::segment(2) == 'admob_ads')
                    <img src="{{ asset('dist/img/sidebar-icons/amobs.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
                @elseif(Request::segment(2) == 'credentials')
					<img src="{{ asset('dist/img/sidebar-icons/app_credential.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
                @elseif(Request::segment(2) == 'firebase-credentials')
					<img src="{{ asset('dist/img/sidebar-icons/credentials.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
                @elseif(Request::segment(2) == 'app_settings')
					<img src="{{ asset('dist/img/sidebar-icons/settings.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
                @elseif(Request::segment(2) == 'sync-data')
					<img src="{{ asset('dist/img/sidebar-icons/sync.png') }}" class="elevation-2  icon-bg vertical-baseline"/>
                @elseif(Request::segment(2) == 'country')
                    <i class="fa fa-flag elevation-2  icon-bg vertical-super" ></i>
                @else
					<i class="d-inline fas fa-tachometer-alt  icon-bg nav-icon pl-1"></i>
				@endif


                <p class="page-title  {{  Request::segment(2) == 'dashboard' ? 'd-inline' : 'd-inline-block' }}  ml-2">{{ isset($link_label_array[Request::segment(2)]) ? $link_label_array[Request::segment(2)] : 'Dashboard'  }}
                    @if(Request::segment(2) == 'teams' || Request::segment(2) == 'schedules')
                        <small class="d-block text-sm"> Add or update {{$sportData->name}} Teams</small>
                    @elseif(Request::segment(2) == 'servers' || (Request::segment(2) == 'servers'  && Request::segment(3) > 0))
                        <small class="d-block text-sm"> {{ isset($scheduleData->scheduleName) ? $scheduleData->scheduleName : "Add or update servers" }} &nbsp; &nbsp; </small>
                    @elseif(Request::segment(2) == 'app'|| (Request::segment(2) == 'app'  && Request::segment(3) > 0))
                        <small class="d-block text-sm"> Register new apps or update configurations</small>
                    @elseif(Request::segment(2) == 'sync-data')
                        <small class="d-block text-sm"> Push Data to Firebase Database </small>
                    @elseif(Request::segment(2) == 'server-types')
                        <small class="d-block text-sm"> Add or update  {{str_replace('-',' ',str_replace('_',' ',Request::segment(2)))}} </small>
                    @elseif(Request::segment(2) == 'country')
                        <small class="d-block text-sm"> List Of All Countries </small>
                    @elseif(Request::segment(2) != 'dashboard')
                        <small class="d-block text-sm"> Add or update  {{str_replace('_',' ',Request::segment(2))}} </small>
                    @else
                        <small class="d-block text-sm"> &nbsp;&nbsp; </small>
                    @endif
                </p>
            </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> <i class="fa fa-home text-info"> </i> </a></li>
              <li class="breadcrumb-item active">{{ isset($link_label_array[Request::segment(2)]) ? $link_label_array[Request::segment(2)] : 'Dashboard' }}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

		@yield('content')

  </div>
  <!-- /.content-wrapper -->

@extends('layouts.footer')
