  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-info elevation-4 custom-siderbar-dark">

	<a class="brand-link" href="{{ route('dashboard') }}">
		<img src="{{ asset('images/logo-mini.png') }}" alt="AdminLTE Logo" class="brand-image-mini"/>
		<img src="{{ asset('images/logo.png') }}" alt="AdminLTE Logo" class="brand-image"
           style="opacity: .8">
		<span class="visiblilty-hidden db"> {{ config('app.name', 'Revage Solution') }} </span>

	</a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel d-none mt-3 pb-3 mb-3 d-flexd">
        <div class="image">
          <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
{{--            {{ dd(auth()->user())}}--}}


{{--            @if(auth()->user()->can('view-dashboard')  || auth()->user()->hasRole('super-admin'))--}}
          <li class="nav-item has-treeview  {{ ( Request::segment(2) == 'dashboard' || Request::segment(2) == '' ) ? 'menu-open' : '' }} ">
            <a href="{{ route('dashboard') }}" class="nav-link  {{ ( Request::segment(2) == 'dashboard' || Request::segment(2) == '' ) ? 'active' : '' }} ">
                <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
{{--        @endif--}}


            @if(auth()->user()->can('view-users') OR auth()->user()->can('view-roles') OR auth()->user()->can('view-permissions') || auth()->user()->hasRole('super-admin'))
            <li class="nav-item  custom {{(Request::segment(2) == 'users' || Request::segment(2) == 'roles' || Request::segment(2) == 'permissions')  ? 'menu-open' : 'hello'}}">

                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-users "></i>
                    <p>
                        User Administrator
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview {{ ( Request::segment(2) == 'users' || Request::segment(2) == 'roles' ) ? 'menu-open' : '' }}">

                    @if(auth()->user()->can('view-users') || auth()->user()->hasRole('super-admin'))
                    <li class="nav-item">
                        <a class="nav-link custom  {{ (Request::segment(2) == 'users') ? 'active' : '' }}" href="{{ url('admin/users') }}">
                            <i class="nav-icon fa fa-minus"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    </li>
                    @endif
                        @if(auth()->user()->can('view-roles') || auth()->user()->hasRole('super-admin'))
                    <li class="nav-item">
                        <a class="nav-link custom  {{ (Request::segment(2) == 'roles') ? 'active' : '' }}" href="{{ url('admin/roles') }}">
                            <i class="nav-icon fa fa-minus"></i>
                            <p>
                                Roles
                            </p>
                        </a>
                    </li>
                        @endif
                            @if(auth()->user()->can('view-permissions') || auth()->user()->hasRole('super-admin'))
                    <li class="nav-item">
                        <a class="nav-link custom  {{ (Request::segment(2) == 'permissions') ? 'active' : '' }}" href="{{ url('admin/permissions') }}">
                            <i class="nav-icon fa fa-minus"></i>
                            <p>
                                Permissions
                            </p>
                        </a>
                    </li>
                        @endif

                </ul>
            </li>
            @endif

			@if(auth()->user()->can('view-sports') || auth()->user()->can('view-leagues') || auth()->user()->can('view-teams') || auth()->user()->can('view-schedules') || auth()->user()->can('view-servers') || auth()->user()->hasRole('super-admin'))
            <li class="nav-header py-3">SPORTS MANAGEMENT </li>
                @if(auth()->user()->can('view-sports')  || auth()->user()->hasRole('super-admin'))
                    <li class="nav-item">
                        <a href="{{ url('admin/sports') }}" class="nav-link {{ (Request::segment(2) == 'sports') ? 'active' : '' }}">
                            <!-- <i class="far fa fa-life-ring nav-icon"></i> -->
							<img src="{{ asset('dist/img/sidebar-icons/sports.png') }}" class="elevation-2 "/>
                            <p>Sports</p>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->can('view-leagues')  || auth()->user()->hasRole('super-admin'))
                    <li class="nav-item">
                        <a href="{{ url('admin/leagues') }}" class="nav-link {{ (Request::segment(2) == 'leagues') ? 'active' : '' }}">
							<img src="{{ asset('dist/img/sidebar-icons/league.png') }}" class="elevation-2 "/>
                            <p>Leagues</p>
                        </a>
                    </li>
                @endif

                @php $sportsList = \App\Models\Sports::orderBy('id','DESC')->get(); @endphp

                @if(auth()->user()->can('view-teams') ||  auth()->user()->hasRole('super-admin'))
                   <li class="nav-item custom {{(Request::segment(2) == 'teams' && count($sportsList) > 0)  ? 'menu-open' : '' }}">
                       @if(count($sportsList) > 0)
                           <a href="javascript:void(0)" class="nav-link">
                               @else
                                   <span class="nav-link" disabled="disabled">
                       @endif
                        <img src="{{ asset('dist/img/sidebar-icons/team.png') }}" class="elevation-2 "/>
                            <p>
                                Teams
                                @if(count($sportsList) > 0)
                                    <i class="fas fa-angle-left right"></i>
                                @endif
                            </p>
                           @if(count($sportsList) > 0)
                                </a>
                           @else
                                </span>
                           @endif
                    <ul class="nav nav-treeview">
                    @foreach($sportsList as $sport)
                        <li class="nav-item">
                            <a href="{{ url('admin/teams/'.$sport->id) }}" class="nav-link custom {{(Request::segment(2) == 'teams' && Request::segment(3) == $sport->id) ? 'active' : ''}}">
                                <i class="far fa fa-minus nav-icon text-sm"></i>
                                <p class="text-capitalize">{{$sport->name}}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
                @endif

				@if(auth()->user()->can('view-schedules')  || auth()->user()->hasRole('super-admin'))
                    <li class="nav-item custom {{(Request::segment(2) == 'schedules' && count($sportsList) > 0 ) ? 'menu-open' : ''}}">
                        @if(count($sportsList) > 0)
                            <a href="javascript:void(0)" class="nav-link">
                                @else
                                    <span class="nav-link" disabled="disabled">
                       @endif
							<img src="{{ asset('dist/img/sidebar-icons/schedule.png') }}" class="elevation-2 "/>
                            <p>
                                Schedule
                                @if(count($sportsList) > 0)
                                    <i class="fas fa-angle-left right"></i>
                                @endif
                            </p>
                       @if(count($sportsList) > 0)
                            </a>
                       @else
                                </span>
                       @endif
						<ul class="nav nav-treeview">
							@foreach($sportsList as $sport)
								<li class="nav-item">
									<a href="{{ url('admin/schedules/'.$sport->id) }}" class="nav-link  custom {{Request::segment(2) == 'schedules' &&  Request::segment(3) == $sport->id ? 'active' : ''}}">
										<i class="far fa fa-minus nav-icon text-sm"></i>
										<p class="text-capitalize">{{$sport->name}}</p>
									</a>
								</li>
							@endforeach
						</ul>
					</li>
				@endif


            @endif

            @if(auth()->user()->can('view-server-types')  || auth()->user()->can('view-servers')  || auth()->user()->hasRole('super-admin'))
                <li class="nav-item custom {{(Request::segment(2) == 'servers' || Request::segment(2) == 'server-types') ? 'menu-open' : ''}}">
                    @if(count($sportsList) > 0)
                        <a href="javascript:void(0)" class="nav-link">
							<img src="{{ asset('dist/img/sidebar-icons/serverss.png') }}" class="elevation-2 "/>
                            <p>
                                Servers
                                    <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                    @endif
                    <ul class="nav nav-treeview">
                        @if(auth()->user()->can('view-servers')  || auth()->user()->hasRole('super-admin'))
                            <li class="nav-item">
                                <a href="{{ url('admin/servers') }}" class="nav-link  custom {{Request::segment(2) == 'servers' ? 'active' : ''}}">
                                    <i class="far fa fa-minus nav-icon text-sm"></i>
                                    <p class="text-capitalize">Live Servers</p>
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->can('view-server-types')  || auth()->user()->hasRole('super-admin'))
                            <li class="nav-item">
                                <a href="{{ url('admin/server-types') }}" class="nav-link  custom {{Request::segment(2) == 'server-types' ? 'active' : ''}}">
                                    <i class="far fa fa-minus nav-icon text-sm"></i>
                                    <p class="text-capitalize">Server Types</p>
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif

            @if(auth()->user()->can('view-app_settings') || auth()->user()->can('view-applications')  || auth()->user()->hasRole('super-admin'))
            <li class="nav-header py-3"> APP CONFIGURATION </li>
            @endif

        @if((auth()->user()->can('view-app_settings') && auth()->user()->can('view-applications')  && auth()->user()->can('manage-applications')  )   || auth()->user()->hasRole('super-admin'))

                    <li class="nav-item">
                        <a href="{{ url('admin/app_settings') }}" class="nav-link {{ (Request::segment(2) == 'app_settings') ? 'active' : '' }}">
							<img src="{{ asset('dist/img/sidebar-icons/settings.png') }}" class="elevation-2 "/>
                            <p>App Setting</p>
                        </a>
                    </li>

                @endif

				@if(auth()->user()->can('view-applications')  || auth()->user()->hasRole('super-admin'))

                <li class="nav-item">
                    <a href="{{ url('admin/app') }}" class="nav-link {{ (Request::segment(2) == 'app') ? 'active' : '' }}">
                        <img src="{{ asset('dist/img/sidebar-icons/application.png') }}" class="elevation-2 "/>
                        <p>Applications</p>
                    </a>
                </li>

				@endif



				@if((auth()->user()->can('view-admob_ads') && auth()->user()->can('view-applications')  && auth()->user()->can('manage-applications')  )   || auth()->user()->hasRole('super-admin'))
                <li class="nav-item">
                    <a href="{{ url('admin/admob_ads') }}" class="nav-link {{ (Request::segment(2) == 'admob_ads') ? 'active' : '' }}">
                        <img src="{{ asset('dist/img/sidebar-icons/amobs.png') }}" class="elevation-2 "/>
                        <p>Admob Ads</p>
                    </a>
                </li>

				@endif

				@if((auth()->user()->can('view-sponsors')   && auth()->user()->can('view-applications')  && auth()->user()->can('manage-applications')  ) || auth()->user()->hasRole('super-admin'))

                <li class="nav-item">
                    <a href="{{ url('admin/sponsors') }}" class="nav-link {{ (Request::segment(2) == 'sponsors') ? 'active' : '' }}">
                        <img src="{{ asset('dist/img/sidebar-icons/sponsor.png') }}" class="elevation-2 "/>
                        <p>Sponsor Ads</p>
                    </a>
                </li>

				@endif

                @if((auth()->user()->can('view-credentials')  && auth()->user()->can('view-applications')  && auth()->user()->can('manage-applications')  )  || auth()->user()->hasRole('super-admin'))

                <li class="nav-item ">
                    <a href="{{ url('admin/credentials') }}" class="nav-link {{ (Request::segment(2) == 'credentials') ? 'active' : '' }}">
							<img src="{{ asset('dist/img/sidebar-icons/app_credential.png') }}" class="elevation-2 "/>
                        <p>App Credentials</p>
                    </a>
                </li>

                @endif

            @if((auth()->user()->can('view-block-app-countries')  && auth()->user()->can('view-applications')  && auth()->user()->can('manage-applications'))  || auth()->user()->hasRole('super-admin'))
            <li class="nav-item d-none">
                <a href="{{ url('admin/country') }}" class="nav-link {{ (Request::segment(2) == 'country') ? 'active' : '' }}">
                    <i class="fa fa-flag elevation-2"></i>
                    <p>Countries</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('admin/block-applications') }}" class="nav-link {{ (Request::segment(2) == 'block-applications') ? 'active' : '' }}">
                    <i class="fa fa-lock elevation-2"></i>
                    <p>Blocked Applications</p>
                </a>
            </li>
            @endif


                @if(auth()->user()->can('view-firebase_configuration') || auth()->user()->can('view-manage-push_notifications') || auth()->user()->can('view-manage-sync_apps_data') || auth()->user()->can('view-manage-sync_sports_data') || auth()->user()->hasRole('super-admin'))

                <li class="nav-header py-3">FIREBASE CONFIGURATION </li>

                @if((auth()->user()->can('view-firebase_configuration') && auth()->user()->can('view-applications')  && auth()->user()->can('manage-applications')  ) || auth()->user()->hasRole('super-admin') )

                <li class="nav-item ">
                    <a href="{{ url('admin/firebase-credentials') }}" class="nav-link {{ (Request::segment(2) == 'firebase-credentials') ? 'active' : '' }}">
							<img src="{{ asset('dist/img/sidebar-icons/credentials.png') }}" class="elevation-2 "/>
                        <p> Firebase Credentials</p>
                    </a>
                </li>

                @endif

                @if(auth()->user()->can('view-manage-push_notifications') || auth()->user()->hasRole('super-admin'))

                <li class="nav-item ">
                    <a href="{{ url('admin/firebase/notifications') }}" class="nav-link {{ (Request::segment(3) == 'notifications') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell " style="margin-right:-4px ; margin-left: -5px !important;"></i>
                        <p>Push Notifications </p>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('view-manage-sync_apps_data') || auth()->user()->can('view-manage-sync_sports_data') ||  auth()->user()->hasRole('super-admin'))
                <li class="nav-item ">
                    <a href="{{ url('admin/sync-data') }}" class="nav-link {{ (Request::segment(2) == 'sync-data') ? 'active' : '' }}">
                        <img src="{{ asset('dist/img/sidebar-icons/sync.png') }}" class="elevation-2 "/>
                        <p> Sync Data</p>
                    </a>
                </li>
                @endif

            @endif


            <!-- Authentication Links -->
		@guest
			<li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
			<li><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
		@else

		@endguest


          </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

