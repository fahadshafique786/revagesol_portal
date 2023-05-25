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

                            <div class="row mb-3">

                                <div class="col-sm-3">
                                    <select class="form-control" id="league_filter" name="league_filter"  onchange="$('button#filter').trigger('click');"  >
                                        <option value="-1">   Select League </option>
                                        @foreach ($leaguesList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-sm-2 visiblilty-hidden">

                                    <button type="button" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                </div>

                                <div class="col-7 text-right">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-schedules'))
                                        <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                            <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                        </a>
                                    @endif

                                </div>


                            </div>

                            <div class="row">
                                <div class="col-6 text-left">
                                    <div class="pull-left">
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-schedules'))
                                            <a class="btn btn-info" href="javascript:void(0)" id="addNew">
                                                Add Schedule
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-schedules'))
                                            <button class="btn btn-danger delete_all" data-table_id="DataTbl" data-module="schedules" data-url="{{ url('admin/schedulesDeleteAll') }}">Delete All Selected</button>
                                        @endif


                                    </div>
                                </div>

                            </div>


                        </div>
                        <div class="card-body">

                            <ul class="nav nav-tabs schedule-nav-tabs" id="custom-content-above-tab" role="tablist">

                                <li class="nav-item">
                                    <a class="nav-link active" id="live-schedules-tab" data-toggle="pill" href="#live-schedules" role="tab" aria-controls="live-schedules" aria-selected="false">  Live  Schedules </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="upcoming-schedules-tab" data-toggle="pill" href="#upcoming-schedules" role="tab" aria-controls="upcoming-schedules" aria-selected="false">Upcoming Schedules</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="previous-schedules-tab" data-toggle="pill" href="#previous-schedules" role="tab" aria-controls="previous-schedules"   aria-selected="true">Previous Schedules</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="tab-pane fade show pt-4 active" id="live-schedules" role="tabpanel" aria-labelledby="live-schedules-tab">

                                    <!-------------- LIVE SCHDEULES TABLE ------------->
                                    <table class="table table-bordered table-hover mt-3 serverDataTable customResponsiveDatatable" id="live-DataTbl">
                                        <thead>
                                        <tr>
                                            <th scope="col" width="10px">
                                                <input type="checkbox" name="" data-tableId="live-DataTbl" class="master" id="master1" />
                                            </th>
                                            <th scope="col" width="5px">#</th>
                                            <th scope="col">Total Apps</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">League</th>
                                            <th scope="col">Home</th>
                                            <th scope="col">Away</th>
                                            <th scope="col">Score</th>
                                            <th scope="col">Start(DateTime)</th>
                                            <th scope="col">Live</th>
                                            <th scope="col">isSponsorAd</th>
                                            <th scope="col">sponsorAdClickUrl</th>
                                            <th scope="col">sponsorAdImageUrl</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                                <div class="tab-pane fade show pt-4" id="upcoming-schedules" role="tabpanel" aria-labelledby="upcoming-schedules-tab">

                                    <!-------------- UPCOMING SCHDEULES TABLE ------------->
                                    <table class="table table-bordered table-hover mt-3" id="DataTbl">
                                        <thead>
                                        <tr>
                                            <th scope="col" width="10px">
                                                <input type="checkbox" name="" data-tableId="DataTbl" class="master"   id="master2" />
                                            </th>
                                            <th scope="col" width="5px">#</th>
                                            <th scope="col">Total Apps</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">League</th>
                                            <th scope="col">Home</th>
                                            <th scope="col">Away</th>
                                            <th scope="col">Score</th>
                                            <th scope="col">Start(DateTime)</th>
                                            <th scope="col">Live</th>
                                            <th scope="col">isSponsorAd</th>
                                            <th scope="col">sponsorAdClickUrl</th>
                                            <th scope="col">sponsorAdImageUrl</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>


                                <div class="tab-pane fade show pt-4 " id="previous-schedules" role="tabpanel" aria-labelledby="previous-schedules-tab">

                                    <!-------------- PREVIOUS SCHDEULES TABLE ------------->
                                    <table class="table table-bordered table-hover mt-3" id="previous-DataTbl">
                                        <thead>
                                        <tr>
                                            <th scope="col" width="10px">
                                                <input type="checkbox" name="" data-tableId="previous-DataTbl" class="master"  id="master3" />
                                            </th>
                                            <th scope="col" width="5px">#</th>
                                            <th scope="col">Total Apps</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">League</th>
                                            <th scope="col">Home</th>
                                            <th scope="col">Away</th>
                                            <th scope="col">Score</th>
                                            <th scope="col">Start(DateTime)</th>
                                            <th scope="col">Live</th>
                                            <th scope="col">isSponsorAd</th>
                                            <th scope="col">sponsorAdClickUrl</th>
                                            <th scope="col">sponsorAdImageUrl</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->

        <!-- boostrap model -->
        <div class="modal fade" id="ajax-model" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg custom-fixed-popup right">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="ajaxheadingModel"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll;">
                        <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">


                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Applications</label><br/>
                                    <select class="js-example-basic-multiple" id="application_ids" name="application_ids[]" multiple="multiple" required>
                                        @foreach($applications as $app)
                                            <option value="{{$app->id}}" selected>{{$app->appName . ' - ' . $app->packageId }}</option>
                                        @endforeach
                                    </select><br/>
                                    <span class="text-danger" id="permissionError"></span>
                                </div>


                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Name</label>
                                    <input type="text" class="form-control" id="scheduleName" name="scheduleName"  placeholder="Enter schedule name" required />

                                    <span class="text-danger" id="scheduleNameError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Leagues</label>
                                    <select class="form-control" id="leagues_id" name="leagues_id" required onchange="getTeamsByLeagues(this.value);">
                                        <option value="">  Select League</option>
                                        @foreach ($leaguesList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>

                                    <span class="text-danger" id="leaguesError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Home Team</label>
                                    <select class="form-control" id="home_team_id" name="home_team_id" required
                                            onchange="verifyHomeAwayTeamDuplication('home',this.id,this.value)">
                                        <option value="">   Select Home Team </option>
                                    </select>

                                    <span class="text-danger" id="home_teamError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Away Team</label>
                                    <select class="form-control" id="away_team_id" name="away_team_id" required
                                            onchange="verifyHomeAwayTeamDuplication('away',this.id,this.value)">
                                        <option value="">   Select Away Team </option>
                                    </select>

                                    <span class="text-danger" id="away_teamError"></span>

                                </div>
                            </div>



                            <div class="form-group">
                                <label for="start_time">Match Start(DateTime)</label>
                                <div class="input-group date" id="" data-target-input="nearest">
                                    <input type="text" autocomplete="off" class="form-control datetimepicker"  id="start_time" name="start_time"  required />
                                    <div class="input-group-append" data-target="" data-toggle="datetimepicker">
                                        <div class="input-group-text calendarIcon"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>



                            <div class="form-group row">


                                <div class="col-sm-12">
                                    <label for="name" class="control-label d-block">Sponsor Ad</label>

                                    <label for="isSponsorAd1" class="cursor-pointer">
                                        <input type="radio" class="EnableDisableFileUpload" onchange="enableDisableSponsorAdFields(this.value)" id="isSponsorAd1" name="isSponsorAd" value="1" />
                                        <span class="">Yes</span>
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="isSponsorAd0" class="cursor-pointer">
                                        <input type="radio" class="EnableDisableFileUpload" id="isSponsorAd0" onchange="enableDisableSponsorAdFields(this.value)"  checked name="isSponsorAd" value="0"  />
                                        <span class="">No</span>
                                    </label>
                                    <span class="text-danger" id="isSponsorAdError"></span>

                                </div>


                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="sponsorAdClickUrl" class="control-label">Sponsor Ad Click Url</label>
                                    <input type="text" class="form-control" id="sponsorAdClickUrl" name="sponsorAdClickUrl" placeholder="" value="" disabled />

                                    <span class="text-danger" id="sponsorAdClickUrlError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="sponsorAdImageUrl" class="control-label">sponsor Ad Image</label>
                                    <input type="file" class="" id="sponsorAdImageUrl" name="sponsorAdImageUrl" onchange="allowonlyImg(this);if(this.value !=''){ $('#sponsorAdImageUrlError').text('');}" disabled />
                                    <input type="hidden" readonly class="" id="sponsor_ad_image_hidden" name="sponsor_ad_image_hidden" >

                                    <span class="text-danger block" id="sponsorAdImageUrlError"></span>

                                </div>

                            </div>


                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-info full-width-button" id="btn-save" >
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
        <!-- end bootstrap model -->

        <!-- boostrap model -->
        <div class="modal fade" id="scheduledAppsModal" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Schedules Applications</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" >
                        <div class="row" id="schedules_applications_container">

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end bootstrap model -->

    </section>
    <!-- /.content -->


@endsection

@push('scripts')
    <script type="text/javascript">


        $('#master1').on('click', function(e) {
            var  tableId = $(this).attr('data-tableId');

            if($(this).is(':checked',true))
            {
                $("#"+tableId+" tbody td .sub_chk").prop('checked', true);
            } else {
                $("#"+tableId+" tbody td .sub_chk").prop('checked', false);
            }
        });


        $('#master2').on('click', function(e) {
            var  tableId = $(this).attr('data-tableId');
            if($(this).is(':checked',true))
            {

                $("#"+tableId+" tbody td .sub_chk").prop('checked', true);
            } else {
                $("#"+tableId+" tbody td .sub_chk").prop('checked', false);
            }
        });


        $('#master3').on('click', function(e) {

            var  tableId = $(this).attr('data-tableId');

            if($(this).is(':checked',true))
            {
                $("#"+tableId+" tbody td .sub_chk").prop('checked', true);
            } else {
                $("#"+tableId+" tbody td .sub_chk").prop('checked', false);
            }
        });

        $('#filter').click(function(){
            var league_filter = $('#league_filter').val();
            if(league_filter != '')
            {
                $('#DataTbl').DataTable().destroy();
                if(league_filter != '-1'){ // for all...
                        fetchData(league_filter);
                }
                else{
                    fetchData();
                }
                $(".master").prop('checked',false);
            }
            else
            {
                alert('Select Filter Option');
                $('#DataTbl').DataTable().destroy();
            }
        });

        $(".nav.schedule-nav-tabs li a.nav-link").on('click',function () {

            $("#master1,#master2,#master3").prop('checked',false);

            setTimeout(function(){
                callDataTableWithFilters();
            },500);
        });

        function getTeamsByLeagues(leagues_id){

            $.ajax({
                type:"POST",
                url: "{{ url('admin/getTeamsByLeagueId') }}",
                data: { leagues_id: leagues_id},
                success: function(response){
                    $("#home_team_id").html(response);
                    $("#away_team_id").html(response);
                }
            });

        }

        function verifyHomeAwayTeamDuplication(type,currentId,currentValue){

            if(type == 'home') {
                $("#home_teamError").text('');
                if (currentValue == $("#away_team_id").val()) {
                    $("#home_teamError").text('Please select different team!');
                    $("#home_team_id").val('');
                }
            }

            if(type == 'away'){
                $("#away_teamError").text('');
                if(currentValue == $("#home_team_id").val()){
                    $("#away_teamError").text('Please select different team!');
                    $("#away_team_id").val('');
                }

            }
        }

        $(document).delegate('.isLiveStatusSwitch', 'click', function(event,state){

            var schedule_id = $(this).attr('data-schedule-id');

            let bools = $(this).attr('aria-pressed');

            var is_live  = (bools == "true" ) ? 1 : 0;

            $.ajax({
                type:"POST",
                url: "{{ url('admin/update-schedule-live-status') }}",
                data: { schedule_id: schedule_id , is_live :  is_live},
                dataType: 'json',
                success: function(res){
                    callDataTableWithFilters();
                }
            });

        });


        var Table_obj = "";
        function fetchData(filter_league = "-1")
        {
            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var activeTabId = $("#custom-content-above-tabContent .tab-pane.active").attr("id");

            if(activeTabId == "upcoming-schedules"){

                var table_id = "DataTbl";
            }
            else if(activeTabId == "previous-schedules"){
                var table_id = "previous-DataTbl";
            }
            else {
                var table_id = "live-DataTbl";
            }


            if(Table_obj != '' && Table_obj != null)
            {
                $('#'+table_id).dataTable().fnDestroy();
                $('#'+table_id+' tbody').empty();
                Table_obj = '';
            }


            Table_obj = $('#'+table_id).DataTable({
                processing: true,
                columnDefs: [
                    { targets: '_all',
                        orderable: true
                    },
                ],
                serverSide: true,
                "ajax" : {
                    url:"{{ url('admin/fetch-schedules-data/'.$sports_id) }}",
                    type:"POST",
                    data:{
                        filter_league:filter_league, active_tab : activeTabId
                    },
                    dataSrc: function ( json ) {
                        //Make your callback here.

                        setTimeout(function () {
                             $("input[data-bootstrap-switch]").each(function(){
                                $(this).bootstrapSwitch('state', $(this).prop('checked'));
                            });
                         },600);


                        return json.data;
                    }
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                    { data: 'srno', name: 'srno' },
                    { data: 'appData', name: 'appData', searchable:false , render: function( data, type, full, meta,rowData ) {
                        if(full.appData.totalApps > 0 && full.appData.isShowApps){
                         var className = "viewScheduledApps";
                        }

                        return "<a href='javascript:void(0)' data-id='"+ full.appData.schedule_id +"' class='"+className+" badge badge-info text-white text-xs text-capitalize'>"+ full.appData.totalApps + "</a>" +" ";
                    }},

                    { data: 'scheduleName', name: 'scheduleName'},
                    { data: 'league', name: 'league'},
                    { data: 'home_team_id', name: 'home_team_id'},
                    { data: 'away_team_id', name: 'away_team_id' },
                    { data: 'score', name: 'score' },
                    { data: 'start_time', name: 'start_time', render: function( data, type, full, meta,rowData ) {

                        return convertTime24to12(data)

                        }

                    },

                    { data: 'is_live', name: 'is_live', searchable:false , render: function( data, type, full, meta,rowData ) {

                            if(data=='Yes'){
                                return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                            else{
                                return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                        },


                    },

                    { data: 'isSponsorAd', name: 'isSponsorAd', searchable:false , render: function( data, type, full, meta,rowData ) {

                            if(data=='Yes'){
                                return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                            else{
                                return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                        },


                    },

                    { data: 'sponsorAdClickUrl', name: 'sponsorAdClickUrl' },

                    { data: 'sponsorAdImageUrl', name: 'sponsorAdImageUrl' },

                    {data: 'action', name: 'action', orderable: false , render: function( data, type, full, meta,rowData ) {
                            $("input[data-bootstrap-switch]").each(function(){
                                $(this).bootstrapSwitch('state', $(this).prop('checked'));
                            });
                            return data;


                        }
                    },
                ],
                order: [[8, 'asc']]
            });


            setTimeout(function(){


            },1500);
        }

        function callDataTableWithFilters(){
            $(".master").prop('checked',false);
            fetchData($('#league_filter').val());
        }


        $(document).ready(function($){

            fetchData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#addNew').click(function () {
                $('#id').val("");
                $('#addEditForm').trigger("reset");

                $('#ajaxheadingModel').html("Add Schedule");

                $("#application_ids option").attr('selected','selected');

                if($("#league_filter").val() > 0){

                   $("#leagues_id").val($("#league_filter").val());

                }

                setTimeout(function(){
                    getTeamsByLeagues($("#leagues_id").val());
                },800);



                $('#isSponsorAd0').prop('checked',true);

                enableDisableSponsorAdFields(0);

                $("#application_ids").select2();

                $('#ajax-model').modal('show');


            });

            $('body').on('click', '.edit', function () {
                var id = $(this).data('id');

                $('input[name=isSponsorAd]').removeAttr('checked');
                $('#nameError,#sponsorAdImageUrlError,#pointsError,#team_iconError,#leagues_idError').text('');

                $("#application_ids option").removeAttr('selected');

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/edit-schedule') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){

                        $("#password").prop("required",false);
                        $('#id').val("");
                        $('#addEditForm').trigger("reset");
                        $('#ajaxheadingModel').html("Edit Schedule");

                        $('#leagues_id').val(res.leagues_id);

                        getTeamsByLeagues(res.leagues_id)

                        $('#id').val(res.id);

                        $('#scheduleName').val(res.scheduleName);


                        $('#start_time').val(convertTime24to12(res.start_time));

                        setTimeout(function(){
                            $('#home_team_id').val(res.home_team_id);
                            $('#away_team_id').val(res.away_team_id);
                            $('#sponsorAdClickUrl').val(res.sponsorAdClickUrl);
                            $('#sponsor_ad_image_hidden').val(res.sponsorAdImageUrl);
                            $('#ajax-model').modal('show');
                        },1500);

                        if(res.apps != ""){
                            $("#application_ids").select2();
                            $.each(res.apps,function(key,obj){
                                $("#application_ids").select2("val", $("#application_ids").select2("val").concat(obj.application_id));
                            });
                        }
                        else{
                            $("#application_ids option").attr('selected','selected');
                            $("#application_ids").select2();
                        }


                        $('#isSponsorAd'+res.isSponsorAd).prop('checked',true);

                        enableDisableSponsorAdFields(res.isSponsorAd);

                    }
                });
            });


            $('body').on('click', '.delete', function () {
                if (confirm("Are you sure you want to delete?") == true) {
                    var id = $(this).data('id');

                    $.ajax({
                        type:"POST",
                        url: "{{ url('admin/delete-schedule') }}",
                        data: { id: id },
                        dataType: 'json',
                        success: function(res){
                            var dataTablePageInfo = Table_obj.page.info();
                            Table_obj.page(dataTablePageInfo.page).draw('page');
                            // callDataTableWithFilters();
                        }
                    });
                }
            });


            $("#addEditForm").on('submit',(function(e) {
                e.preventDefault();
                var Form_Data = new FormData(this);

                let start_time = convertTime12to24($("#start_time").val());
                Form_Data.set('start_time', start_time);


                $("#btn-save").html('Please Wait...');
                $("#btn-save"). attr("disabled", true);
                $('#scheduleNameError').text('');
                $('#home_teamError,#away_teamError').text('');




                if($("#isSponsorAd1").prop('checked') && !$("#sponsor_ad_image_hidden").val()){

                    if(!$("#sponsorAdImageUrl").val()){
                        alert("Please select Image!")
                        $("#btn-save").html('Save');
                        $("#btn-save"). attr("disabled", false);
                        $('#sponsorAdImageUrlError').text('Please select Image!');
                        return false;
                    }
                }


                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/add-update-schedules/'.$sports_id) }}",
                    data: Form_Data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(res){


                        $('#ajax-model').modal('hide');
                        $("#btn-save").html('Save');
                        $("#btn-save"). attr("disabled", false);

                        // callDataTableWithFilters();

                        var dataTablePageInfo = Table_obj.page.info();
                        Table_obj.page(dataTablePageInfo.page).draw('page');


                    },
                    error:function (response) {
                        $("#btn-save").html(' Save');
                        $("#btn-save"). attr("disabled", false);
                        $('#scheduleNameError').text(response.responseJSON.errors.scheduleName);
                        $('#home_teamError').text(response.responseJSON.errors.home_team_id);
                        $('#away_teamError').text(response.responseJSON.errors.away_team_id);
                    }
                });
            }));


        });

        $(document).on('click','.viewScheduledApps',function() {
            let scheduleId = $(this).attr('data-id');
            loadScheduledAppsByScheduleId(scheduleId);
        });

        function loadScheduledAppsByScheduleId(scheduleId){

            $.ajax({
                type:"POST",
                url: "{{ url('admin/schedules-apps-card-view') }}",
                data: { scheduleId: scheduleId},
                success: function(response){
                    console.log(response);
                    $("#schedules_applications_container").html(response);
                    $("#scheduledAppsModal").modal('show');

                }
            });

        }
    </script>

@endpush
