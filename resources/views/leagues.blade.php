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

                                <div class="col-sm-2">
                                    <select class="form-control" id="sports_filter" name="sports_filter" onchange="$('button#filter').trigger('click');" >
                                        <option value="-1" selected>   Select Sports </option>
                                        @foreach ($sports_list as $sport)
                                            <option value="{{ $sport->id }}"  {{ (isset($sport->id) && old('id')) ? "selected":"" }}>{{ $sport->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-sm-2 visiblilty-hidden">
                                    <button type="button" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                </div>

                                <div class="col-8 text-right">

                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-leagues'))
                                        <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                            <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                        </a>
                                    @endif

                                </div>



                            </div>

                            <div class="row">
                                <div class="col-6 text-left">

                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-leagues'))
                                            <a class="btn btn-info d-inline-block" href="javascript:void(0)" id="addNew">
                                                Add League
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-leagues'))
                                            <button  class="btn btn-danger d-inline-block delete_all" data-url="{{ url('admin/leaguesDeleteAll') }}">Delete All Selected</button>
                                        @endif



                                </div>

                            </div>

                        </div>
                        <div class="card-body">

                            <ul class="nav nav-tabs schedule-nav-tabs" id="custom-content-above-tab" role="tablist">

                                <li class="nav-item">
                                    <a class="nav-link active" id="live-schedules-tab" data-toggle="pill" href="#live-leagues" role="tab" aria-controls="live-leagues" aria-selected="false">  Live Leagues </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link " id="all-leagues-tab" data-toggle="pill" href="#all-leagues" role="tab" aria-controls="all-leagues" aria-selected="false">All Leagues</a>
                                </li>

                            </ul>

                            <div class="tab-content" id="custom-content-above-tabContent">

                                <div class="tab-pane fade show pt-4 active" id="live-leagues" role="tabpanel" aria-labelledby="live-leagues-tab">

                                    <table class="table table-bordered table-hover" id="live-DataTbl">
                                        <thead>
                                        <tr>
                                            <th scope="col" width="10px">
                                                <input type="checkbox" name=""  data-tableId="live-DataTbl"   class="master"  id="master" />
                                            </th>
                                            <th scope="col" width="10px">#</th>
                                            <th scope="col">Icon</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Sport</th>
                                            <th scope="col">Start (DateTime)</th>
                                            <th scope="col">Sponsor Ad</th>
                                            <th scope="col">Sponsor Ad Click Url</th>
                                            <th scope="col">Sponsor Ad Image</th>
{{--                                            <th scope="col">Live</th>--}}
                                            <th scope="col">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>



                                </div>

                                <div class="tab-pane fade show pt-4" id="all-leagues" role="tabpanel" aria-labelledby="all-leagues-tab">

                                    <table class="table table-bordered table-hover" id="DataTbl">
                                        <thead>
                                        <tr>
                                            <th scope="col" width="10px">
                                                <input type="checkbox" name="" data-tableId="DataTbl"   class="master" id="master2" />
                                            </th>
                                            <th scope="col" width="10px">#</th>
                                            <th scope="col">Icon</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Sport</th>
                                            <th scope="col">Start (DateTime)</th>
                                            <th scope="col">Sponsor Ad</th>
                                            <th scope="col">Sponsor Ad Click Url</th>
                                            <th scope="col">Sponsor Ad Image</th>
{{--                                            <th scope="col">Live</th>--}}
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
            <div class="modal-dialog modal-sm custom-fixed-popup right">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="ajaxheadingModel"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll">
                        <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">



                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Sport</label>
                                    <select class="form-control" id="sports_id" name="sports_id" required>
                                        <option value="">   Select Sport </option>
                                        @foreach ($sports_list as $sport)
                                            <option value="{{ $sport->id }}"  {{ (isset($sport->id) && old('id')) ? "selected":"" }}>{{ $sport->name }}</option>
                                        @endforeach
                                    </select>

                                    <span class="text-danger" id="sports_idError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">

                                    <span class="text-danger" id="nameError"></span>

                                </div>

                            </div>


                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="league_icon" class="control-label d-block">League Icon</label>

                                    <input type="file" class="" id="league_icon" name="league_icon" onchange="allowonlyImg(this); if(this.value !=''){ $('#sport_logoError').text('')};">
                                    <span class="text-danger" id="league_iconError"></span>

                                </div>


                            </div>


                            <div class="form-group">
                                <label for="start_time">Match Start(DateTime)</label>
                                <div class="input-group date" id="" data-target-input="nearest">
                                    <input type="text" autocomplete="off" class="form-control datetimepicker custom-datetimepicker"  id="start_datetime" name="start_datetime"  required />
                                    <div class="input-group-append" data-target="" data-toggle="datetimepicker">
                                        <div class="input-group-text calendarIcon"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <span class="text-danger" id="start_datetimeError"></span>

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
                                    <label for="sponsorAdImageUrl" class="control-label">Sponsor Ad Image</label>
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

    </section>
    <!-- /.content -->


@endsection

@push('scripts')
    <script type="text/javascript">

        $('#master').on('click', function(e) {
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

        $('#filter').click(function(){

            var sports_filter = $('#sports_filter').val();
            if(sports_filter != '')
            {
                $('#DataTbl').DataTable().destroy();
                if(sports_filter != '-1'){ // for all...
                    fetchData(sports_filter);
                }
                else{
                    fetchData();
                }
                $(".master").prop('checked',false);
            }
            else {
                alert('Select  Filter Option');
            }
        });

        $(".nav.schedule-nav-tabs li a.nav-link").on('click',function () {

            $("#master,#master2").prop('checked',false);

            setTimeout(function(){
                callDataTableWithFilters();
            },500);
        });



        var Table_obj = "";

        function fetchData(filter_sports= '-1')
        {
            // $("input[data-bootstrap-switch]").each(function(){
            //     $(this).bootstrapSwitch('state', $(this).prop('checked'));
            // });
            //

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var activeTabId = $("#custom-content-above-tabContent .tab-pane.active").attr("id");

            if(activeTabId == "all-leagues"){

                var table_id = "DataTbl";
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
                "processing" : true,
                "serverSide" : true,
                "order" : [],
                "searching" : true,
                "paging": true,
                columnDefs: [{
                    "defaultContent": "-",
                    "targets": "_all"
                }],
                serverSide: true,
                "ajax" : {
                    url:"{{ url('admin/fetch-leagues-data') }}",
                    type:"POST",
                    data:{
                        filter_sports:filter_sports , active_tab : activeTabId
                    }
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                    { data: 'srno', name: 'srno' , searchable:false},
                    { data: 'icon', name: 'icon', searchable:false},
                    { data: 'name', name: 'name' },
                    { data: 'sport_name', name: 'sport_name' },
                    { data: 'start_datetime', name: 'start_datetime', render: function( data, type, full, meta,rowData ) {


                            if(data){
                                return convertTime24to12(data)

                            }
                            else{
                                return '-';
                            }

                        }

                    },

                    { data: 'isSponsorAd', name: 'isSponsorAd' , searchable:false , render: function( data, type, full, meta,rowData ) {
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
                    {data: 'action', name: 'action', orderable: false , searchable:false},
                ],
                order: [[5, 'asc']]
            });

        }

        function callDataTableWithFilters(){
            $(".master").prop('checked',false);
            fetchData($('#sports_filter').val());
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

                $('#sponsorAdImageUrlError').text('');


                $('#addEditForm').trigger("reset");

                $('#isSponsorAd0').prop('checked',true);

                enableDisableSponsorAdFields(0);

                $('#ajaxheadingModel').html("Add League");

                $('#ajax-model').modal('show');

                if($("#sports_filter").val() > 0){
                    $("#sports_id").val($("#sports_filter").val());
                }

            });

            $('body').on('click', '.edit', function () {
                var id = $(this).data('id');
                $('input[name=isSponsorAd]').removeAttr('checked');
                $('#nameError,#sponsorAdImageUrlError').text('');

                $('#emailError').text('');
                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/edit-league') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){
                        $('#id').val("");

                        $('#addEditForm').trigger("reset");
                        $('#ajaxheadingModel').html("Edit League");
                        $('#ajax-model').modal('show');

                        $('#id').val(res.id);

                        $('#name').val(res.name);
                        $('#sports_id').val(res.sports_id);
                        $('#sponsorAdClickUrl').val(res.sponsorAdClickUrl);
                        $('#sponsor_ad_image_hidden').val(res.sponsorAdImageUrl);

                        $('#isSponsorAd'+res.isSponsorAd).prop('checked',true);

                        enableDisableSponsorAdFields(res.isSponsorAd);

                        $('#start_datetime').val(convertTime24to12(res.start_datetime));

                        if(res.sponsorAdImageUrl){
                            $("#sponsorAdImageUrl").removeAttr('required');
                        }

                    }
                });
            });



            $('body').on('click', '.delete', function () {
                if (confirm("Are you sure you want to delete?") == true) {
                    var id = $(this).data('id');

                    $.ajax({
                        type:"POST",
                        url: "{{ url('admin/delete-league') }}",
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

            $(document).delegate('.isLiveStatusSwitch', 'click', function(event,state){

                var league_id = $(this).attr('data-league-id');

                let bools = $(this).attr('aria-pressed');

                var is_live  = (bools == "true" ) ? 1 : 0;

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/change-league-status') }}",
                    data: { league_id: league_id , is_live :  is_live},
                    dataType: 'json',
                    success: function(res){

                        var dataTablePageInfo = Table_obj.page.info();
                        Table_obj.page(dataTablePageInfo.page).draw('page');

                        // callDataTableWithFilters();

                    }
                });

            });

            $("#addEditForm").on('submit',(function(e) {
                e.preventDefault();
                var Form_Data = new FormData(this);

                let start_datetime = convertTime12to24($("#start_datetime").val());
                Form_Data.set('start_datetime', start_datetime);


                $("#btn-save").html('Please Wait...');
                $("#btn-save"). attr("disabled", true);

                $('#nameError').text('');
                $('#sports_typeError').text('');
                $('#multi_leagueError').text('');
                $('#image_requiredError').text('');


                if($("#isSponsorAd1").prop('checked') && !$("#sponsor_ad_image_hidden").val()){

                    if(!$("#sponsorAdImageUrl").val()){
                        alert("Please select sponsor ad image!")
                        $("#btn-save").html('Save');
                        $("#btn-save"). attr("disabled", false);
                        $('#sponsorAdImageUrlError').text('Please select sponsor ad image!');
                        return false;
                    }
                }


                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/add-update-leagues') }}",
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
                        $('#nameError').text(response.responseJSON.errors.name);
                        $('#league_iconError').text(response.responseJSON.errors.icon);
                        $('#start_datetimeError').text(response.responseJSON.errors.start_datetime);
                        $('#isSponsorAdError').text(response.responseJSON.errors.isSponsorAd);
                        $('#sponsorAdClickUrlError').text(response.responseJSON.errors.sponsorAdClickUrl);
                    }
                });
            }));
        });

    </script>

@endpush
