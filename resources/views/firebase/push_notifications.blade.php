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

                                    <select class="form-control" id="account_filter" name="account_filter" onchange="getAllAppListOptionByAccountsNoPermission(this.value,'filter_app_id','app_detail_id');$('button#filter').trigger('click');"  >
                                        <option value="-1" selected>   Select Accounts </option>
                                        @foreach ($accountsList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-sm-4">
                                    <select class="form-control" id="filter_app_id" name="filter_app_id" onchange="$('button#filter').trigger('click');">
                                        <option value="" selected>   Select App </option>
                                    </select>

                                </div>

                                <div class="col-sm-2 visiblilty-hidden">
                                    <button type="button" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                </div>

                                <div class="col-2 text-right">

                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-push_notifications'))
                                        <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                            <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                        </a>
                                    @endif

                                </div>


                            </div>

                            <div class="row ">
                                <div class="col-6 text-left">

                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-push_notifications'))
                                        <a class="btn btn-info d-inline-block" href="javascript:void(0)" id="addNew">
                                            Add Push Notification
                                        </a>
                                    @endif

                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-push_notifications'))
                                        <button class="btn btn-danger delete_all" data-table_id="DataTbl" data-url="{{ url('admin/firebase/notifications/remove-all') }}">Delete All Selected</button>
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
                                    <th scope="col">Application</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Message</th>
{{--                                    <th scope="col">Scheduling (Datetime)</th>--}}
                                    <th scope="col">Image</th>
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
        <div class="modal fade" id="ajax-modal" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="ajaxHeadingModal"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">

                            <div class="form-group row">

                                <div class="col-sm-6">
                                    <label for="account_id" class="control-label">Accounts</label>
                                    <select required class="form-control" id="account_id" name="account_id" onchange="getAllApplicationListOptionByAccounts(this.value,'app_detail_id')"  >
                                        <option value="" selected>   Select Accounts </option>
                                        @foreach ($accountsList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>


                                    <span class="text-danger" id="accounts_idError"></span>

                                </div>

                                <div class="col-sm-6">
                                    <label for="app_detail_id" class="control-label">Application</label>
                                    <select class="form-control" id="app_detail_id" name="app_detail_id" required>
                                        <option value="">   Select App </option>
                                    </select>

                                    <span class="text-danger" id="app_detail_idError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-6">
                                    <label for="title" class="control-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" value="" maxlength="50" required="">

                                    <span class="text-danger" id="titleError"></span>

                                </div>

                                <div class="col-sm-6">
                                    <label for="image" class="control-label">Image</label>
                                    <input type="file" class="form-control pl-0 border-0" id="image" name="image" placeholder="Enter image" accept="image/*"/>

                                    <span class="text-danger" id="imageError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-6 d-none">

                                    <label for="name" class="control-label d-block">Is Scheduling</label>

                                    <label for="isScheduling1" class="cursor-pointer">
                                        <input type="radio" onclick="$('#schedulingBlock').show();"  id="isScheduling1" name="isScheduling" value="1" />
                                        <span class="">Yes</span>
                                    </label>

{{--                                    onchange="enableDisableScheduling(this.value)"--}}
                                    <label for="isScheduling0" class="cursor-pointer">

                                        <input type="radio" class="EnableDisableFileUpload" id="isScheduling0"   onclick="$('#schedulingBlock').hide();" name="isScheduling" value="0"  checked />
                                        <span class="">No</span>
                                    </label>
                                    <span class="text-danger" id="isSchedulingError"></span>


                                </div>

                                <div class="col-sm-6 d-none" id="schedulingBlock">

                                    <label for="start_time">Scheduling (DateTime)</label>
                                    <div class="input-group date" id="" data-target-input="nearest">
                                        <input type="text" autocomplete="off" class="form-control custom-datetimepicker_with_minutes"  id="schedule_datetime" name="schedule_datetime"  />
                                        <div class="input-group-append" data-target="" data-toggle="datetimepicker">
                                            <div class="input-group-text calendarIcon"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>

                                </div>


                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="message" class="control-label d-block"> Message </label>
                                    <textarea rows="3" class="form-control" id="message" name="message" placeholder="Enter Message" required=""></textarea>
                                    <span class="text-danger" id="messageError"></span>

                                </div>


                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12 mb-3 pt-2 pb-2 bg-gray-light">
                                    <h5 class="card-title text-bold"> Additional Information (Optional)</h5>
                                </div>

                                <div class="col-sm-12 additionalInfoParamsBlock">
                                    <div class="row">
                                        <div class="col-sm-12 text-right  d-none">
                                            <button id="additionalInfoRow" type="button" class="btn btn-warning mb-3 ">Add New Addition Info</button>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="key_name" class="control-label">Key Name</label>
                                            <input type="text" class="form-control mb-2 key_name" id="key_name0" name="key_name[]" placeholder="Enter New Key Name" value="">
                                        </div>


                                        <div class="col-sm-6">
                                            <label for="key_value" class="control-label">Key Value</label>
                                            <input type="text" class="form-control mb-2 key_value" id="key_value0" name="key_value[]" placeholder="Enter New Key Value" value="">
                                        </div>


                                        <div class="col-sm-6">
                                            <label for="key_name" class="control-label">Key Name</label>
                                            <input type="text" class="form-control mb-2 key_name" id="key_name1" name="key_name[]" placeholder="Enter New Key Name" value="">
                                        </div>


                                        <div class="col-sm-6">
                                            <label for="key_value" class="control-label">Key Value</label>
                                            <input type="text" class="form-control mb-2 key_value" id="key_value1" name="key_value[]" placeholder="Enter New Key Value" value="">
                                        </div>

                                    </div>

                                    <div id="newAdditionalInfoRow"></div>

                                </div>

                            </div>


                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-info full-width-button" id="btn-save" >
                                    <i class="fa fa-bell mr-2"> </i> Send Notification
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
                // "bStateSave": true,

                columnDefs: [{
                    "defaultContent": "-",
                    "targets": "_all"
                }],
                serverSide: true,
                "ajax" : {
                    url:"{{ url('admin/firebase/notifications/data') }}",
                    type:"POST",
                    data:{
                        filter_app_id:filter_app_id,filter_accounts_id:filter_accounts_id
                    }
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                    { data: 'srno', name: 'srno' , searchable:false},
                    { data: 'appName', name: 'appName' },
                    { data: 'title', name: 'title' },
                    { data: 'message', name: 'message' },
                    // { data: 'schedule_datetime', name: 'schedule_datetime', render: function( data, type, full, meta,rowData ) {
                    //
                    //         return convertTime24to12(data)
                    //
                    //     }
                    //
                    // },

                    { data: 'image', name: 'image' },
                    {data: 'action', name: 'action', orderable: false , searchable:false},
                ],
                order: [[1, 'asc']]
            });

        }


        function callDataTableWithFilters(){
            $("#master").prop('checked',false);
            fetchData($('#filter_app_id').val());
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
                $('#titleError,#messageError,#iconError,#accounts_idError,#app_detail_idError,#imageError').text('');
                $('#addEditForm').trigger("reset");
                $('#ajaxHeadingModal').html("Compose Push Notification");
                $("form#addEditForm")[0].reset();

                $("#isScheduling0").prop('checked',true);
                $("#schedulingBlock").hide();



                if($("#filter_app_id").val() > 0 || $("#filter_app_id").val() == 'all'){
                    $("#app_detail_id").val($("#filter_app_id").val());
                }

                if($("#account_filter").val() > 0) {
                    $("#account_id").val($("#account_filter").val());
                }

                $('#ajax-modal').modal('show');

            });


            $('#image').on('change', function() {
                $("#imageError").text('');

                const size = this.files[0].size;

                if (size > 300000) {
                    $("#imageError").text('File size is greater than 300KB');
                    $("#image").val('');
                    return false;

                }
            });

            $("#addEditForm").on('submit',(function(e) {

                e.preventDefault();

                var Form_Data = new FormData(this);

                if($("#isScheduling1").is(':checked'))
                {
                    let schedule_datetime = convertTime12to24($("#schedule_datetime").val());
                    Form_Data.set('schedule_datetime', schedule_datetime);
                }

                if($("#app_detail_id").val() == '-1'){
                    Form_Data.set('app_detail_id', '');
                }

                $("#btn-save").html('Please Wait...');

                $("#btn-save"). attr("disabled", true);

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/firebase/notifications/save') }}",
                    data: Form_Data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(res){

                        var dataTablePageInfo = Table_obj.page.info();
                        Table_obj.page(dataTablePageInfo.page).draw('page');

                        $('#ajax-modal').modal('hide');

                        $("#btn-save").html('Save');
                        $("#btn-save"). attr("disabled", false);


                        if(res?.data?.errors.length  > 0){

                            var customHtml = '<table id="responseTable" border="1" width="100%">' +
                                '        <thead>' +
                                '            <tr>' +
                                '                <th class="text-uppercase bg-gray-light">Response</th>' +
                                '            </tr>' +
                                '        </thead>' +
                                '        <tbody>';
                                ;
                            for(var i = 0 ; i < res?.data?.errors.length;i++){
                                customHtml += '<tr>';
                                customHtml += '<td class="text-danger text-md pb-2 pt-2 text-left fs pr-3 pl-3">' +res?.data?.errors[i] +  '</td> </tr>';
                            }
                            customHtml += '</tbody></table>';


                            Swal.fire({
                                title: '<strong>Failed Notifications </strong>',
                                icon: 'warning',
                                html: customHtml,
                                showConfirmButton: true,
                                confirmButtonText: 'OK'

                            })
                            .then((result) => {

                                if (result.value) {

                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Push Notification has been saved successfully!'
                                    });
                                }
                            })
                        }
                        else{
                            Toast.fire({
                                icon: 'success',
                                title: 'Push Notification has been saved successfully!'
                            });
                        }

                        $("form#addEditForm")[0].reset();

                    },
                    error:function (response) {
                        if(response.status == 422){
                            Toast.fire({
                                icon: 'error',
                                title: response.responseJSON.message
                            });
                        }
                        else{
                            Toast.fire({
                                icon: 'error',
                                title: 'Network Error Occurred !'
                            });
                        }


                        $("#btn-save").html(' Save');
                        $("#btn-save"). attr("disabled", false);
                        $('#titleError').text(response.responseJSON.errors?.title);
                        $('#messageError').text(response.responseJSON.errors?.message);
                        $('#imageError').text(response.responseJSON.errors?.image);
                        $('#accounts_idError').text(response.responseJSON.errors?.account_id);
                        $('#app_detail_idError').text(response.responseJSON.errors?.app_detail_id);

                    }
                });
            }));

            $('body').on('click', '.edit', function () {

                var id = $(this).data('id');

                $('#titleError,#messageError,#iconError,#accounts_idError,#app_detail_idError,#imageError').text('');

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/firebase/notifications/show') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){

                        $('#id').val("");
                        $('#addEditForm').trigger("reset");
                        $('#ajaxheadingModel').html("Compose Push Notification");
                        $('#id').val(res.id);

                        getAllApplicationListOptionByAccounts(res.account_id,'app_detail_id');

                        if(res.app_detail_id == 0 ){
                            res.app_detail_id = "all";
                        }

                        $('#app_detail_id').val(res.app_detail_id);

                        $('#title').val(res.title);


                        $('#schedule_datetime').val(convertTime24to12(res.schedule_datetime));

                        if(res.schedule_datetime != null){
                            $("#isScheduling1").prop('checked',true);
                            $("#schedulingBlock").show();
                        }
                        else{
                            $("#schedulingBlock").hide();
                        }

                        $('#message').val(res.message);
                        $('#account_id').val(res.account_id);

                        if(res.additional_info.length > 0){
                            for(var i=0; i<res.additional_info.length;i++){
                                // if(i == 0){
                                    $('#key_name'+i).val(res.additional_info[i].key_name);
                                    $('#key_value'+i).val(res.additional_info[i].key_value);
                                // }
                                // else{
                                // generateHeaderInputs(res.headers[i].key_name,res.headers[i].key_value,i)
                                // }
                            }
                        }


                        setTimeout(function(){
                            $('#ajax-modal').modal('show');
                            $('#app_detail_id').val(res.app_detail_id);
                        },1600);

                    }
                });
            });

            $('body').on('click', '.delete', function () {

                if (confirm("Are you sure you want to delete?") == true) {
                    var id = $(this).data('id');

                    $.ajax({
                        type:"POST",
                        url: "{{ url('admin/firebase/notifications/remove') }}",
                        data: { id: id },
                        dataType: 'json',
                        success: function(res){
                            Toast.fire({
                                icon: 'success',
                                title: 'Notification has been removed!'
                            });

                            var dataTablePageInfo = Table_obj.page.info();
                            Table_obj.page(dataTablePageInfo.page).draw('page');

                        },
                        error:function (response) {

                            Toast.fire({
                                icon: 'error',
                                title: 'Network Error Occurred!'
                            });
                        }
                    });
                }
            });

        });

    </script>

@endpush
