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
                                    <select class="form-control" id="league_filter" name="league_filter"  onchange="$('button#filter').trigger('click');" >
                                        <option value="-1" selected>   Select League </option>
                                        @foreach ($leagues_list as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-sm-2 visiblilty-hidden">
                                    <button type="button" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                </div>

                                <div class="col-8 text-right">
                                    <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                        <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                    </a>
                                </div>



                            </div>

                            <div class="row">
                                <div class="col-12 text-left">
                                    <div class="pull-left">

                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-teams'))
                                            <a class="btn btn-info" href="javascript:void(0)" id="addNew">
                                                Add Team
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-teams'))
                                            <button class="btn btn-danger delete_all" data-table_id="DataTbl" data-url="{{ url('admin/teamsDeleteAll') }}">Delete All Selected</button>
                                        @endif


                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">

                            <table class="table table-bordered table-hover" id="DataTbl">
                                <thead>
                                <tr>
                                    <th scope="col" width="10px">
                                        <input type="checkbox" name="" id="master" />
                                    </th>
                                    <th scope="col" width="10px">#</th>
                                    <th scope="col">Icon</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">League</th>
                                    <th scope="col">Points</th>
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
                    <div class="modal-body">
                        <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">



                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Leagues</label>
                                    <select class="form-control" id="leagues_id" name="leagues_id" required>
                                        <option value="">   Select Leagues </option>
                                        @foreach ($leagues_list as $league)
                                            <option value="{{ $league->id }}"  {{ (isset($league->id) && old('id')) ? "selected":"" }}>{{ $league->name }}</option>
                                        @endforeach
                                    </select>

                                    <span class="text-danger" id="leagues_idError"></span>

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
                                    <label for="name" class="control-label">Points</label>
                                    <input type="number" class="form-control" id="points" name="points" placeholder="0" value="" required />

                                    <span class="text-danger" id="pointsError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="league_icon" class="control-label d-block">Team Icon</label>

                                    <input type="file" class="" id="team_icon" name="team_icon" onchange="allowonlyImg(this); if(this.value !=''){ $('#team_iconError').text('')}">
                                    <span class="text-danger" id="team_iconError"></span>

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
                $("#master").prop('checked',false);
            }
            else
            {
                alert('Select Filter Option');
            }
        });




        var Table_obj = "";

        function fetchData(filter_league="-1")
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if(Table_obj != '' && Table_obj != null)
            {
                $('#DataTbl').dataTable().fnDestroy();
                $('#DataTbl tbody').empty();
                Table_obj = '';
            }


            Table_obj = $('#DataTbl').DataTable({
                processing: true,
                columnDefs: [{
                    "defaultContent": "-",
                    "targets": "_all"
                }],
                serverSide: true,
                "ajax" : {
                    url:"{{ url('admin/fetch-teams-data/'.$sports_id) }}",
                    type:"POST",
                    data:{
                        filter_league:filter_league
                    }
                },                columns: [
                    { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                    { data: 'srno', name: 'srno' ,searchable:false},
                    { data: 'icon', name: 'icon' ,searchable:false},
                    { data: 'name', name: 'name' },
                    { data: 'league_name', name: 'league_name' },
                    { data: 'points', name: 'points'  ,searchable:false},
                    {data: 'action', name: 'action', orderable: false ,searchable:false},
                ],
                order: [[1, 'asc']]
            });

        }

        function callDataTableWithFilters(){
            $("#master").prop('checked',false);
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
                $("#password").prop("required",true);
                $('#ajaxheadingModel').html("Add Team");

                if($("#league_filter").val() > 0){

                    $("#leagues_id").val($("#league_filter").val());

                }

                setTimeout(function(){
                    getTeamsByLeagues($("#leagues_id").val());
                },800);

                $('#ajax-model').modal('show');

            });

            $('body').on('click', '.edit', function () {
                var id = $(this).data('id');
                $('#nameError').text('');
                $('#pointsError').text('');
                $('#team_iconError').text('');
                $('#leagues_idError').text('');
                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/edit-team') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){
                        console.log(res);
                        $("#password").prop("required",false);
                        $('#id').val("");
                        $('#addEditForm').trigger("reset");
                        $('#ajaxheadingModel').html("Edit Team");
                        $('#ajax-model').modal('show');
                        $('#id').val(res.id);
                        $('#name').val(res.name);
                        $('#leagues_id').val(res.leagues_id);
                        $('#points').val(res.points);

                    }
                });
            });
            $('body').on('click', '.delete', function () {
                if (confirm("Are you sure you want to delete?") == true) {
                    var id = $(this).data('id');

                    $.ajax({
                        type:"POST",
                        url: "{{ url('admin/delete-team') }}",
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
                $("#btn-save").html('Please Wait...');
                $("#btn-save"). attr("disabled", true);
                $('#nameError').text('');
                $('#leagues_idError').text('');
                $('#pointsError').text('');

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/add-update-teams/'.$sports_id) }}",
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
                        $('#leagues_idError').text(response.responseJSON.errors.leagues_id);
                        $('#pointsError').text(response.responseJSON.errors.points);
                    }
                });
            }));
        });


        function getTeamsByLeagues(leagues_id){

            $.ajax({
                type:"POST",
                url: "<?php echo e(url('admin/getTeamsByLeagueId')); ?>",
                data: { leagues_id: leagues_id},
                success: function(response){
                    $("#home_team_id").html(response);
                    $("#away_team_id").html(response);
                }
            });

        }

    </script>

@endpush
