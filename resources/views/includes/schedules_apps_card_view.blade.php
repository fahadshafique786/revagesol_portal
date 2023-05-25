@if(!empty(count($schedulesAppsList) > 0))

    @foreach($schedulesAppsList as $index => $obj)

            <div class="card custom text-center" style="float: left;width: 30%; margin-left: 2% !important;">
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
                <div class="card-block text-center">
                    <div style="height: 92px; " class="col-md-12">
                        <div class="text-center">
                            <img style="height: 72px; width: 72px" src="{{ (file_exists(public_path('uploads/apps/'.$obj->appLogo))) ? asset('uploads/apps/'.$obj->appLogo) : asset('uploads/apps/app.png') }}" alt="App Logo">
                        </div>
                    </div>
                    <div class="row d-none">
                        <div class="col-6">
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-applications'))
                                <a class="ik ik-settings f-18 text-green" href="{{url('/admin/app/'.$obj->id)}}">
                                    <i class="fa fa-cog"></i>
                                </a>
                            @endif
                        </div>
                        <div class="col-6 border-left">
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-applications'))
                                <a class="ik ik-trash-2 f-18 text-red deleteApplication" data-route="delete-app" data-id="{{$obj->id}}" id="delID-{{$obj->id}}" href="javascript:void(0)" data-toggle="modal" data-target="#applicationDelete">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


    @endforeach

@else
    <div class="col-xl-3 col-md-3">
        <p> No data available</p>
    </div>

@endif
