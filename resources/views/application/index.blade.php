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
                                    <select class="form-control" id="account_filter" name="account_filter" onchange="setFilterDefaultValue();loadApplicationsCardView()" >
                                        <option value="-1" selected>   Select Accounts </option>
                                        @foreach ($accountsList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-sm-3 visiblilty-hidden">
                                    <button type="button" onclick="loadApplicationsCardView()" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                </div>


                                <div class="col-6 text-right">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-applications'))
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
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-applications'))
                                        <a class="btn btn-dark " href="{{route('app.create')}}">
                                            Add New Application
                                        </a>

                                        @if(sizeof($appslist) > 0)
                                            <button id="deleteAllButton" class="btn btn-danger delete_all" data-module="AppDetails" data-table_id="DataTbl" data-url="{{ url('admin/app/remove/all') }}">Delete All Selected</button>
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

                            <div class="row mt-4" id="applications_container">

                            </div>

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

            $(document).on('click','.deleteApplication', function () {
                let applicaiton_id = $(this).attr('data-id');
                let routes = $(this).attr('data-route');

                isConfirmSweelAlert(applicaiton_id,routes,true)
            });

            $(document).on('click', '.pagination a', function(event){
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchData(page);
                return false;
            });

        });

        var isFilterChange = false;
        function setFilterDefaultValue(){
            isFilterChange = true;
        }

        function fetchData(page)
        {
            $.ajax({
                url:"pagination/applications/fetch_data?page="+page+"&accountsId="+$("#account_filter").val()+"&searchKeywords="+$("#searchBox").val(),
                success:function(data)
                {
                    $("#applications_container").html(data);                }
            });
        }

        function loadApplicationsCardView(){
            if(isFilterChange){
                $("#searchBox").val('');
                isFilterChange = false;
            }

            $('#cover-spin').show();

            $.ajax({

                type:"POST",
                url: "{{ url('admin/apps-card-view') }}",
                data: { accountsId: $("#account_filter").val() , searchKeywords : $("#searchBox").val()},
                success: function(response){
                    $('#cover-spin').hide();
                    $("#applications_container").html(response);

                    if($("#totalAppsCount").val() > 0){
                        $("#deleteAllButton").show();
                    }
                    else{
                        $("#deleteAllButton").hide();
                    }
                }
            });

        }

    </script>

@endpush
