@extends('layouts.master')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row">

                                <div class="col-sm-3">
                                    <select class="form-control" id="sports_filter" name="sports_filter" onchange="setFilterDefaultValue();loadApplicationsCardView()" >
                                        <option value="-1" selected>   Select Sports </option>
                                        @foreach ($sportsList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-sm-3 visiblilty-hidden">
                                    <button type="button" onclick="loadApplicationsCardView()" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                </div>

                                <div class="col-6 text-right">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-app_settings'))
                                        <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                            <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                        </a>
                                    @endif

                                </div>

                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row">

                                <div class="col-6 text-left">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-app_settings'))
                                        <a class="btn btn-info d-inline-block" href="{{route('app_setting.create')}}">
                                            Add New App Setting
                                        </a>

                                        @if(sizeof($appsList) > 0)
                                            <button id="deleteAllButton" class="btn btn-danger  delete_all" data-module="AppDetails" data-table_id="DataTbl" data-url="{{ url('admin/app-setting/remove/all') }}">Delete All Selected</button>
                                        @endif

                                    @endif


                                </div>

                                <div class="col-6 pull-right text-right">
                                    <label>
                                        <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Search" >
                                    </label>
                                </div>

                                <div class="col-12 pull-left text-left">
                                    <label for="master" class="form-label">
                                        <input type="checkbox" name=""  class="bigSizeCheckbox mt-0" id="master" />
                                        <span class="vertical-super pt-2 d-inline-block"> Select All </span>
                                    </label>
                                </div>

                            </div>

                            <div class="row mt-4" id="app_settings_container">

                            </div>



                        @if(!empty(count($appsList) > 0))

                                @foreach($appsList as $obj)

                                    <div class="col-xl-3 col-md-3 d-none" id="application{{$obj->id}}">
                                        <div class="card custom text-center">
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
                                                        <img style="height: 72px; width: 72px" src="{{ ($obj->appLogo) ? asset('uploads/apps/'.$obj->appLogo) : asset('uploads/apps/appLogo.png') }}" alt="App Logo">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-app_settings'))
                                                            <a class="ik ik-settings f-18 text-green" href="{{url('/admin/app_settings/'.$obj->id)}}">
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
                                        </div>
                                    </div>
                                @endforeach

                            @else
                                <div class="col-xl-3 col-md-3">
                                    <p> No data available</p>
                                </div>

                            @endif

                        </div>
                    </div>
                </div>

            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->


    </section>
    <!-- /.content -->


@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function($){

            $("#searchBox").on('keyup',function(e){
                    loadApplicationsCardView();
            })

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            loadApplicationsCardView();

            $(document).on('click', '.pagination a', function(event){
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchData(page);
                return false;
            });


        });

        var isFilterChange = false;
        function setFilterDefaultValue(filterId){
            isFilterChange = true;
        }

        function loadApplicationsCardView(page = ""){
            if(isFilterChange){
                $("#searchBox").val('');
                isFilterChange = false;
            }

            $('#cover-spin').show();

            $.ajax({

                type:"POST",
                url: "{{ url('admin/app-settings-card-view') }}",
                data: { sportsId: $("#sports_filter").val() , searchKeywords : $("#searchBox").val()},
                success: function(response){
                    $('#cover-spin').hide();
                    $("#app_settings_container").html(response);

                    if($("#totalAppsCount").val() > 0){
                        $("#deleteAllButton").show();
                    }
                    else{
                        $("#deleteAllButton").hide();
                    }
                }
            });

        }

        function fetchData(page)
        {
            $.ajax({
                url:"pagination/fetch_data?page="+page+"&sportsId="+$("#sports_filter").val()+"&searchKeywords="+$("#searchBox").val(),
                success:function(data)
                {
                    $("#app_settings_container").html(data);                }
            });
        }

        $(document).on('click','.deleteApplication', function () {

            let applicaiton_id = $(this).attr('data-id');
            let routes = $(this).attr('data-route');

            isConfirmSweelAlert(applicaiton_id,routes,true);

        });

    </script>

@endpush
