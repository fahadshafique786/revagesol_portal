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

                            <div class="col-sm-4 ">
                                <select class="form-control" id="account_filter" name="account_filter" onchange="getApplicationListOptionByAccounts(this.value,'filter_app_id');$('button#filter').trigger('click');"  >
                                    <option value="-1" selected>   Select Accounts </option>
                                    @foreach ($accountsList as $obj)
                                        <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <select class="form-control" id="filter_app_id" name="filter_app_id" onchange="$('button#filter').trigger('click');" >
                                    <option value="" selected>   Select App </option>
                                </select>
                            </div>

                            <div class="col-sm-2  visiblilty-hidden">
                                <button type="button" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                            </div>

                            <div class="col-2 text-right">
                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-firebase_configuration'))
                                    <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                        <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                    </a>
                                @endif
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-6 text-left">
                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-firebase_configuration'))
                                <a class="btn btn-info d-inline-block " href="javascript:void(0)" id="addNew">
                                    Add New Firebase Credential
                                </a>
                                @endif

                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-firebase_configuration'))
                                    <button class="btn btn-danger d-inline-block delete_all" data-table_id="DataTbl" data-url="{{ url('admin/firebase/credentialsDeleteAll') }}">Delete All Selected</button>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover customResponsiveDatatable" id="DataTbl">
                            <thead>
                            <tr>
                                <th scope="col" width="10px">
                                    <input type="checkbox" name="" id="master" />
                                </th>
                                <th scope="col" width="10px">#</th>
                                <th scope="col">Application</th>
                                <th scope="col">App Setting URL</th>
                                <th scope="col">Apps Detail URL</th>
                                <th scope="col">Leagues URL</th>
                                <th scope="col">Schedules URL</th>
                                <th scope="col">Servers URL</th>
                                <th scope="col">Re Captcha Key Id</th>
                                <th scope="col">Notification Key</th>
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
        <div class="modal-dialog modal-lg custom-fixed-popups rightx">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="ajaxheadingModel"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" style="overflow-y: scroll">
                    <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id">

                        <div class="form-group row">

                            <div class="col-sm-6">
                                <label for="name" class="control-label">Application</label>
                                <select class="form-control" id="app_detail_id" name="app_detail_id" required>
                                    <option value="">   Select App </option>
                                    @foreach ($appsList as $obj)
                                    <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->appName . ' - ' . $obj->packageId}}</option>
                                    @endforeach
                                </select>

                                <span class="text-danger" id="accounts_idError"></span>

                            </div>

                            <div class="col-sm-6">
                                <label for="stream_key" class="control-label d-block"> ReCaptcha Key ID </label>
                                <input type="text" class="form-control" id="reCaptchaKeyId" name="reCaptchaKeyId" value="" required />
                                <span class="text-danger" id="reCaptchaKeyIdError"></span>

                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-6">
                                <label for="stream_key" class="control-label d-block"> App Setting URL </label>
                                <input type="text" class="form-control" id="app_setting_url" name="app_setting_url" value="" >
                                <span class="text-danger" id="app_setting_urlError"></span>
                            </div>

                            <div class="col-sm-6">
                                <label for="secret_key" class="control-label">App Detail URL</label>
                                <input type="text" class="form-control" id="apps_url" name="apps_url" placeholder="" >
                                <span class="text-danger" id="apps_urlError"></span>
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-sm-6">
                                <label for="stream_key" class="control-label d-block"> League URL </label>
                                <input type="text" class="form-control" id="leagues_url" name="leagues_url" value="" >
                                <span class="text-danger" id="leagues_urlError"></span>
                            </div>

                            <div class="col-sm-6">
                                <label for="stream_key" class="control-label d-block"> Schedule URL </label>
                                <input type="text" class="form-control" id="schedules_url" name="schedules_url" value="" >
                                <span class="text-danger" id="schedules_urlError"></span>

                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="stream_key" class="control-label d-block"> Server URL </label>
                                <input type="text" class="form-control" id="servers_url" name="servers_url" value="" >
                                <span class="text-danger" id="servers_urlError"></span>
                            </div>

                            <div class="col-sm-6">
                                <label for="notificationKey" class="control-label d-block"> Notification Key </label>
                                <input type="text" class="form-control" id="notificationKey" name="notificationKey" value="" >
                                <span class="text-danger" id="notificationKeyError"></span>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="stream_key" class="control-label d-block"> Firebase Config Params </label>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="" value="apiKey" readonly>
                                    </div>


                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_value" id="apiKey" name="key_value[]" placeholder="Enter Key Value" value=""  required />
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="" value="authDomain" readonly>
                                    </div>


                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_value" id="authDomain" name="key_value[]" placeholder="Enter Key Value" value=""  required />
                                    </div>

                                </div>

                                <div class="row" >
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_name" id="key_name"  name="key_name[]" placeholder="" value="databaseURL" readonly>
                                    </div>

                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_value" id="databaseURL" name="key_value[]" placeholder="Enter Key Value" value=""  required />
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="" value="projectId" readonly>
                                    </div>

                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_value" id="projectId" name="key_value[]" placeholder="Enter Key Value" value=""  required />
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="" value="storageBucket" readonly>
                                    </div>

                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_value" id="storageBucket" name="key_value[]" placeholder="Enter Key Value" value=""  required />
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="" value="messagingSenderId" readonly>
                                    </div>

                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_value" id="messagingSenderId" name="key_value[]" placeholder="Enter Key Value" value=""  required />
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="" value="appId" readonly>
                                    </div>


                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_value" id="appId" name="key_value[]" placeholder="Enter Key Value" value=""  required />
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="" value="measurementId" readonly>
                                    </div>


                                    <div class="col-sm-6">
                                        <input type="text" class="form-control mb-2 key_value" id="measurementId" name="key_value[]" placeholder="Enter Key Value" value=""  required />
                                    </div>

                                </div>




                                <span  class="text-danger" id="firebaseConfigJsonError"></span>
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
                url:"{{ url('admin/fetch-firebase-credentials/') }}",
                type:"POST",
                data:{
                    filter_app_id:filter_app_id,filter_accounts_id:filter_accounts_id
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                { data: 'srno', name: 'srno' , searchable:false},
                { data: 'appName', name: 'appName' },
                { data: 'app_setting_url', name: 'app_setting_url' },
                { data: 'apps_url', name: 'apps_url' },
                { data: 'leagues_url', name: 'leagues_url' },
                { data: 'schedules_url', name: 'schedules_url' },
                { data: 'servers_url', name: 'servers_url' },
                { data: 'reCaptchaKeyId', name: 'reCaptchaKeyId' },
                { data: 'notificationKey', name: 'notificationKey' },

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

    function reloadAppsList(application_id = ""){
        $.ajax({
            type:"POST",
            url: "{{ url('admin/firebase/get-applist-options') }}",
            data: { appId: application_id , accountsId : $("#account_filter").val()},
            success: function(response){
                $("#app_detail_id").html(response);
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

            $('#leagues_urlError,#schedules_urlError,#notificationKeyError,#apps_urlError,#servers_urlError,#app_setting_urlError,#reCaptchaKeyIdError,#firebaseConfigJsonError').text('');

            $('#addEditForm').trigger("reset");

            $('#ajaxheadingModel').html("Add Firebase Credential ");

            $("form#addEditForm")[0].reset();

            reloadAppsList();

            setTimeout(function(){

                if($("#filter_app_id").val() > 0){
                    $("#app_detail_id").val($("#filter_app_id").val());
                }

                $('#ajax-model').modal('show');

            },600)

        });

        $('body').on('click', '.edit', function () {

            var id = $(this).data('id');

            $('#leagues_urlError,#schedules_urlError,#apps_urlError,#servers_urlError,#app_setting_urlError').text('');

            var application_id =  $(this).data('application_id');

            reloadAppsList(application_id)


            $.ajax({
                type:"POST",
                url: "{{ url('admin/firebase/edit-credentials') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){

                    $("#password").prop("required",false);
                    $('#id').val("");
                    $('#addEditForm').trigger("reset");
                    $('#ajaxheadingModel').html("Edit Firebase Credential");
                    $('#id').val(res.id);


                    $('#apps_url').val(res.apps_url);
                    $('#leagues_url').val(res.leagues_url);
                    $('#schedules_url').val(res.schedules_url);
                    $('#servers_url').val(res.servers_url);
                    $('#app_setting_url').val(res.app_setting_url);
                    $('#reCaptchaKeyId').val(res.reCaptchaKeyId);
                    $('#notificationKey').val(res.notificationKey);


                    if(res.firebaseConfigJson != null){

                        let firebaseConfigJson =  JSON.parse(res.firebaseConfigJson);

                        $("#apiKey").val(firebaseConfigJson.apiKey);
                        $("#appId").val(firebaseConfigJson.appId);
                        $("#authDomain").val(firebaseConfigJson.authDomain);
                        $("#databaseURL").val(firebaseConfigJson.databaseURL);
                        $("#projectId").val(firebaseConfigJson.projectId);
                        $("#measurementId").val(firebaseConfigJson.measurementId);
                        $("#storageBucket").val(firebaseConfigJson.storageBucket);
                        $("#messagingSenderId").val(firebaseConfigJson.messagingSenderId);

                    }

                    setTimeout(function(){
                        $('#app_detail_id').val(res.app_detail_id);
                        $('#ajax-model').modal('show');
                    },1000);

                    // reloadAppsList(res.app_detail_id);



                }
            });
        });


        $('body').on('click', '.delete', function () {
            if (confirm("Are you sure you want to delete?") == true) {
                var id = $(this).data('id');

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/firebase/delete-credentials') }}",
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


            $('#leagues_urlError,#schedules_urlError,#apps_urlError,#servers_urlError,#app_setting_urlError,#reCaptchaKeyIdError,#firebaseConfigJsonError').text('');


            $.ajax({
                type:"POST",
                url: "{{ url('admin/firebase/add-update-credentials') }}",
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
                    else{
                        Toast.fire({
                            icon: 'error',
                            title: 'Network Error Occured!'
                        });
                    }


                    $("#btn-save").html(' Save');
                    $("#btn-save"). attr("disabled", false);
                    $('#apps_urlError').text(response.responseJSON.errors.apps_url);
                    $('#leagues_urlError').text(response.responseJSON.errors.leagues_url);
                    $('#schedules_urlError').text(response.responseJSON.errors.schedules_url);
                    $('#servers_urlError').text(response.responseJSON.errors.servers_url);
                    $('#app_setting_urlError').text(response.responseJSON.errors.app_setting_url);
                    $('#reCaptchaKeyIdError').text(response.responseJSON.errors.reCaptchaKeyId);
                    $('#firebaseConfigJsonError').text(response.responseJSON.errors.firebaseConfigJson);
                    $('#notificationKeyError').text(response.responseJSON.errors.notificationKey);

                }
            });
        }));
    });

</script>

@endpush
