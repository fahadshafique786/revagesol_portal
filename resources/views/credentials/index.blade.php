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

                                <div class="col-sm-4">
                                    <select class="form-control" id="account_filter" name="account_filter" onchange="$('button#filter').trigger('click');"  >
                                        <option value="-1" selected>   Select Accounts </option>
                                        @foreach ($accountsList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-sm-2 visiblilty-hidden">
                                    <button type="button" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                </div>

                                <div class="col-6 text-right">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-credentials'))
                                        <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                            <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                        </a>
                                    @endif

                                </div>


                            </div>

                            <div class="row">
                                <div class="col-6 text-left">

                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-credentials'))
                                        <a class="btn btn-info d-inline-block " href="javascript:void(0)" id="addNew">
                                            Add New Credential
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-credentials'))
                                        <button class="btn btn-danger d-inline-block delete_all" data-table_id="DataTbl" data-url="{{ url('admin/credentialsDeleteAll') }}">Delete All Selected</button>
                                    @endif

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
                                    <th scope="col">Accounts</th>
                                    <th scope="col">Server Auth Key</th>
                                    <th scope="col">App Signing key</th>
                                    <th scope="col">Minimum Version Code</th>
                                    <th scope="col">Stream Key</th>
                                    <th scope="col">Token Key</th>
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
                    <div class="modal-body" style="overflow-y: scroll">
                        <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">



                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Accounts</label>
                                    <select class="form-control" id="account_id" name="account_id" required>
                                        <option value="">   Select App </option>
                                        @foreach ($remainingAppsList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name}}</option>
                                        @endforeach
                                    </select>

                                    <span class="text-danger" id="account_idError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="server_auth_key" class="control-label">Server Auth key</label>
                                    <input type="text" class="form-control" id="server_auth_key" name="server_auth_key" placeholder="" required="">

                                    <span class="text-danger" id="server_auth_keyError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="appSigningKey" class="control-label">App Signing key</label>
                                    <input type="text" class="form-control" id="appSigningKey" name="appSigningKey" placeholder="" required="">

                                    <span class="text-danger" id="appSigningKeyError"></span>

                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">

                                    <label for="versionCode" class="control-label">Minimum Version Code</label>

                                    <input type="text"
                                           readonly="readonly"
                                           maxlength="3"
                                           class="form-control w-70 d-inline-block notAllowedAlphabets versionControlInput"
                                           name="versionCode" id="versionCode"
                                           value="0"
                                    />

                                    <button type="button" class="plus digit_numbers" >+</button>
                                    <button type="button" class="minus digit_numbers" >-</button>


                                    <span class="text-danger" id="versionCodeError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="stream_key" class="control-label d-block"> Stream Key </label>
                                    <input type="text" class="form-control" id="stream_key" name="stream_key" value="" required="">
                                    <span class="text-danger" id="stream_keyError"></span>

                                </div>


                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="token_key" class="control-label d-block"> Token key </label>
                                    <input type="text" class="form-control" id="token_key" name="token_key" value="" required="">
                                    <span class="text-danger" id="token_keyError"></span>

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
            var filter_app_id = $('#filter_app_id').val();
            if(filter_app_id != '')
            {
                $('#DataTbl').DataTable().destroy();
                if(filter_app_id != '-1'){ // for all...
                    fetchData(filter_app_id);
                }
                else{
                    fetchData();
                }
                $("#master").prop('checked',false);
            }
            else
            {
                $('#DataTbl').DataTable().destroy();
                fetchData();
            }
        });



        var Table_obj = "";

        function fetchData(filter_app_id= '-1')
        {
            var filter_accounts_id = $("#account_filter").val();

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
                    url:"{{ url('admin/fetch-credentials-data/') }}",
                    type:"POST",
                    data:{
                        filter_app_id:filter_app_id,filter_accounts_id:filter_accounts_id
                    }
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                    { data: 'srno', name: 'srno' , searchable:false},
                    { data: 'account_id', name: 'account_id' },
                    { data: 'server_auth_key', name: 'server_auth_key' },
                    { data: 'appSigningKey', name: 'appSigningKey' },
                    { data: 'versionCode', name: 'versionCode' },
                    { data: 'stream_key', name: 'stream_key' },
                    { data: 'token_key', name: 'token_key' },

                    {data: 'action', name: 'action', orderable: false , searchable:false},
                ],
                order: [[1, 'asc']]
            });

        }


        function callDataTableWithFilters(){
            $("#master").prop('checked',false);
            fetchData($('#filter_app_id').val());
            reloadAppsList();
        }

        function reloadAppsList(account_id = ""){
            $.ajax({
                type:"POST",
                url: "{{ url('admin/get-applist-options') }}",
                data: { account_id: account_id , accountsId : $("#account_filter").val()},
                success: function(response){
                    $("#account_id").html(response);
                }


            });
        }



        $(document).ready(function($){

            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });



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
                $('#ajaxheadingModel').html("Add Credential ");
                $("form#addEditForm")[0].reset();

                // reloadAppsList();

                setTimeout(function(){
                    // if($("#filter_app_id").val() > 0){
                    //     $("#app_detail_id").val($("#filter_app_id").val());
                    // }

                    $('#ajax-model').modal('show');

                },1500);



            });

            $('body').on('click', '.edit', function () {

                var id = $(this).data('id');
                $('#server_auth_keyError,#stream_keyError,#token_keyError,#appSigningKeyError,#versionCodeError,#account_idError').text('');

                var account_id =  $(this).data('account_id');

                reloadAppsList(account_id)


                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/edit-credentials') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){
                        console.log(res);
                        $("#password").prop("required",false);
                        $('#id').val("");
                        $('#addEditForm').trigger("reset");
                        $('#ajaxheadingModel').html("Edit Credential");
                        $('#id').val(res.id);


                        // $('#app_detail_id').val(res.app_detail_id);
                        $('#account_id').val(res.account_id);
                        $('#server_auth_key').val(res.server_auth_key);
                        $('#stream_key').val(res.stream_key);
                        $('#token_key').val(res.token_key);
                        $('#appSigningKey').val(res.appSigningKey);
                        $('#versionCode').val(res.versionCode);
                        $('#ajax-model').modal('show');

                    }
                });
            });


            $('body').on('click', '.delete', function () {
                if (confirm("Are you sure you want to delete?") == true) {
                    var id = $(this).data('id');

                    $.ajax({
                        type:"POST",
                        url: "{{ url('admin/delete-credentials') }}",
                        data: { id: id },
                        dataType: 'json',
                        success: function(res){
                            Toast.fire({
                                icon: 'success',
                                title: 'App Credential has been removed!'
                            });

                            var dataTablePageInfo = Table_obj.page.info();
                            Table_obj.page(dataTablePageInfo.page).draw('page');
                            // callDataTableWithFilters();

                        },
                        error:function (response) {
                            if(response?.status == 403){
                                Toast.fire({
                                    icon: 'error',
                                    title: response.responseJSON.message
                                });
                            }
                            else{
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Network Error Occured!'
                                });
                                
                            }
                        }
                    });
                }
            });

            $("#addEditForm").on('submit',(function(e) {
                e.preventDefault();

                var Form_Data = new FormData(this);
                $("#btn-save").html('Please Wait...');
                $("#btn-save"). attr("disabled", true);


                $('#server_auth_keyError,#stream_keyError,#token_keyError,#appSigningKeyError,#versionCodeError').text('');


                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/add-update-credentials') }}",
                    data: Form_Data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(res){

                        // callDataTableWithFilters();
                        var dataTablePageInfo = Table_obj.page.info();
                        Table_obj.page(dataTablePageInfo.page).draw('page');


                        $('#ajax-model').modal('hide');
                        $("#btn-save").html('Save');
                        $("#btn-save"). attr("disabled", false);

                        Toast.fire({
                            icon: 'success',
                            title: 'App Credential has been saved successfully!'
                        });

                        $("form#addEditForm")[0].reset();

                    },
                    error:function (response) {
                        console.log(response.responseJSON);
                        if(response.status == 422){
                            Toast.fire({
                                icon: 'error',
                                title: response.responseJSON.message
                            });
                        }
                        else if(response.status == 403){
                            Toast.fire({
                                icon: 'error',
                                title: response.responseJSON.message
                            });
                        }
                        else{
                            Toast.fire({
                                icon: 'error',
                                title: 'Network Error Occured!'
                            });
                        }

                        $("#btn-save").html(' Save');
                        $("#btn-save"). attr("disabled", false);
                        $('#server_auth_keyError').text(response.responseJSON.errors.server_auth_key);
                        $('#versionCodeError').text(response.responseJSON.errors.versionCode);
                        $('#stream_keyError').text(response.responseJSON.errors.stream_key);
                        $('#token_keyError').text(response.responseJSON.errors.token_key);
                        $('#appSigningKeyError').text(response.responseJSON.errors.appSigningKey);

                    }
                });
            }));
        });

    </script>

@endpush
