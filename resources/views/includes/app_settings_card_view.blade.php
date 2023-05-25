@if(!empty(count($appsList) > 0))

    @foreach($appsList as $index => $obj)

        <div class="col-xl-3 col-md-3" id="application-{{$obj->id}}">
            <div class="card custom text-center">
                <label class="form-label text-left" for="allappscheckbox-{{$obj->id}}">
                    <input value="{{$obj->id}}" type="checkbox" data-id="{{$obj->id}}" class="sub_chk" id="allappscheckbox-{{$obj->id}}" name="removeAllAppSettingsCheckbox" />
                </label>
                <a class="ik d-block ik-settings f-18 text-dark" href="{{url('/admin/app_settings/'.$obj->id)}}">
                <div class="card-header">

                    <div class="col-md-12">
                        <div class="text-center">
                            <h5><strong>{{$obj->sports_name}}</strong></h5>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-center">
                            <h6>{{$obj->appName . ' - ' . $obj->packageId}}</h6>
                        </div>
                    </div>

                </div>
                </a>

                <a class="ik d-block ik-settings f-18 text-dark" href="{{url('/admin/app_settings/'.$obj->id)}}">

                    <div class="card-block text-center">

                    <div style="height: 92px; " class="col-md-12">
                        <div class="text-center">
                            <img style="height: 72px; width: 72px" src="{{ (file_exists(public_path('uploads/apps/'.$obj->appLogo))) ? asset('uploads/apps/'.$obj->appLogo) : asset('uploads/apps/app.png') }}" alt="App Logo">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-6">
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-app_settings'))
                                <a class="ik d-block ik-settings f-18 text-green" href="{{url('/admin/app_settings/'.$obj->id)}}">
                                    <i class="fa fa-cog"></i>
                                </a>
                            @endif
                        </div>
                        <div class="col-6 border-left">
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-app_settings'))
                                <a class="ik ik-trash-2 f-18 text-red deleteApplication" data-route="delete-app_settings" data-id="{{$obj->id}}" id="delID-{{$obj->id}}" href="javascript:void(0)" data-toggle="modal" data-target="#applicationDelete">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                </a>
            </div>
        </div>

    @endforeach

    <div class="col-md-12 pagination-container">
        {{ $appsList->links('vendor.pagination.custom')  }}
    </div>


@else
    <div class="col-xl-3 col-md-3">
        <p> No App Settings Found</p>
    </div>

@endif

<input type="hidden" disabled id="totalAppsCount" value="{{ $totalAppsCount  }}"/>
