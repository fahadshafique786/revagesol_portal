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
                            <div class="col-12 text-left">
                                <div class="pull-left">    <!--- test --->

                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-servers'))
                                    <a class="btn btn-info" href="javascript:void(0)" id="addNew">
                                        New Server
                                    </a>
                                    <a class="btn btn-success" href="javascript:void(0)" id="linkServer">
                                        Link Server
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover customResponsiveDatatable" id="serverDataTable">
                            <thead>
                            <tr>
                                <th scope="col" width="10px">#</th>
                                <th scope="col">Type</th>
                                <th scope="col">Name</th>
                                <th scope="col">Sport</th>
                                <th scope="col">Link</th>
                                <th scope="col">Headers</th>
                                <th scope="col">Premium</th>
                                <th scope="col">isTokenAdded</th>
                                <th scope="col">isIpAddressApiCall</th>
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
        <!-- /.row -->
    </div><!-- /.container-fluid -->

    <!-- boostrap model -->
    <div class="modal fade" id="ajax-model" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="ajaxheadingModel"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" style="overflow-y: scroll">
                    <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id" readonly>

                        <div class="form-group row">

                            <div class="col-sm-6">
                                <label for="name" class="control-label"> Server Type </label>
                                <select class="form-control" id="server_type_id" name="server_type_id" required >
                                    <option value="">   Select Sever Type </option>
                                    @foreach ($serverTypes as $serverType)
                                        <option value="{{ $serverType->id }}"  {{ (isset($serverType->id) && old('id')) ? "selected":"" }}>{{ $serverType->name }}</option>
                                    @endforeach
                                </select>

                                <span class="text-danger" id="serverTypeIdError"></span>

                            </div>

                            <div class="col-sm-6">
                                <label for="name" class="control-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required>

                                <span class="text-danger" id="nameError"></span>

                            </div>

                            <div class="col-sm-6">
                                <label for="name" class="control-label">Link</label>
                                <input type="text" class="form-control" id="link" name="link" placeholder="Enter Link" value="" required>

                                <span class="text-danger" id="linkError"></span>

                            </div>


                        </div>



                        <div class="form-group row">

                            <div class="col-sm-4">
                                <label for="isIpAddressApiCallLab0" class="control-label d-block">Is Ip Address Api Call</label>

                                <label for="isIpAddressApiCall1" class="cursor-pointer">
                                    <input type="radio" class=""id="isIpAddressApiCall1" checked name="isIpAddressApiCall" value="1" />
                                    <span class="">Yes</span>
                                </label>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <label for="isIpAddressApiCall0" class="cursor-pointer">
                                    <input type="radio" class="" id="isIpAddressApiCall0" name="isIpAddressApiCall" value="0"  />
                                    <span class="">No</span>
                                </label>
                                <span class="text-danger" id="isIpAddressApiCallError"></span>

                            </div>

                            <div class="col-sm-4">
                                <label for="name" class="control-label d-block">Token Added</label>
                                <label for="isTokenAdded0" class="cursor-pointer">
                                    <input type="radio" class="" id="isTokenAdded1" name="isTokenAdded" value="1" checked />
                                    <span class="">Yes</span>
                                </label>

                                &nbsp;&nbsp;&nbsp;&nbsp;

                                <label for="isTokenAdded1" class="cursor-pointer">
                                    <input type="radio" class="" id="isTokenAdded0" name="isTokenAdded"  value="0" />
                                    <span class="">No</span>
                                </label>

                                <span class="text-danger" id="isHeaderError"></span>
                            </div>

                            <div class="col-sm-4"></div>

                            <div class="col-sm-4">
                                <label for="name" class="control-label d-block">Premium</label>
                                <label for="isPremium0" class="cursor-pointer">
                                    <input type="radio" class="" id="isPremium1" name="isPremium" value="1" />
                                    <span class="">Yes</span>
                                </label>

                                &nbsp;&nbsp;&nbsp;&nbsp;

                                <label for="isPremium1" class="cursor-pointer">
                                    <input type="radio" class="" id="isPremium0" name="isPremium"  value="0" checked/>
                                    <span class="">No</span>
                                </label>

                                <span class="text-danger" id="isPremiumYesError"></span>
                            </div>

                            <div class="col-sm-4">
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

                            <div class="col-sm-6">
                                <label for="sponsorAdClickUrl" class="control-label">Sponsor Ad Click Url</label>
                                <input type="text" class="form-control" id="sponsorAdClickUrl" name="sponsorAdClickUrl" placeholder="" value="" disabled />

                                <span class="text-danger" id="sponsorAdClickUrlError"></span>

                            </div>

                            <div class="col-sm-6">
                                <label for="sponsorAdImageUrl" class="control-label d-block">sponsor Ad Image</label>
                                <input type="file" class="" id="sponsorAdImageUrl" name="sponsorAdImageUrl" onchange="allowonlyImg(this);if(this.value !=''){ $('#sponsorAdImageUrlError').text(''); }" disabled />
                                <input type="hidden" readonly class="" id="sponsor_ad_image_hidden" name="sponsor_ad_image_hidden" >

                                <span class="text-danger block d-block" id="sponsorAdImageUrlError"></span>

                            </div>



                        </div>

                        <div class="form-group row">

                            <div class="col-sm-12">
                                <label for="name" class="control-label d-block">Header</label>
                                <label for="isHeader1" class="cursor-pointer">
                                    <input type="radio" class="" id="isHeader1" onchange="showHideHeaderParams(this.value)" name="isHeader" value="1" />
                                    <span class="">Yes</span>
                                </label>

                                &nbsp;&nbsp;&nbsp;&nbsp;

                                <label for="isHeader0" class="cursor-pointer">
                                    <input type="radio" class="" id="isHeader0" name="isHeader" onchange="showHideHeaderParams(this.value)"   value="0" checked />
                                    <span class=""> No</span>
                                </label>

                                <span class="text-danger" id="isHeaderError"></span>
                            </div>

                            <div class="col-sm-12 headerParamsBlock">
                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <button id="addHeaderRow" type="button" class="btn btn-warning mb-3 ">Add Headers</button>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="name" class="control-label">Header Key Name</label>
                                        <input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="Enter Header Key Name" value="">
                                    </div>


                                    <div class="col-sm-6">
                                        <label for="name" class="control-label">Header Key Value</label>
                                        <input type="text" class="form-control mb-2 key_value" id="key_value" name="key_value[]" placeholder="Enter Link Key Value" value="">
                                    </div>
                                </div>

                                <div id="newHeaderRow"></div>

                            </div>

                        </div>


                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-info" id="btn-save" >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end bootstrap model -->

    <!-- boostrap model -->
    <div class="modal fade" id="attachServerModal" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-sm custom-fixed-popup right">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="ajaxheadingModal-LinkServer"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" id="addEditForm-LinkServer" name="addEditForm-LinkServer" class="form-horizontal" method="POST" enctype="multipart/form-data">

                        <div class="form-group row">

                            <div class="col-sm-12">
                                <label for="name" class="control-label">Name</label>
                                <select class="form-control" id="server_id" name="server_id" required="">

                                    <option value="">  Select Server     </option>

                                    @foreach ($servers_list as $obj)
                                        <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                    @endforeach

                                </select>

                                <span class="text-danger" id="linked_serverError"></span>

                            </div>

                        </div>

                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-info full-width-button" id="btn-save-LinkServer" >
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

    // add rowc
    $("#addHeaderRow").click(function () {

        var html = '';
        html += '<div id="inputFormRow" class="row">';
        html += '<div class="col-sm-6">';
        html += '<input type="text" class="form-control mb-2 key_name" id="key_name" name="key_name[]" placeholder="Enter Header Key Name" value="">';
        html += '</div>';


        html += '<div class="col-sm-6">';
        html += '<input type="text" class="form-control mb-2 key_value d-inline-block w-75 " id="key_value" name="key_value[]" placeholder="Enter Link Key Value" value="">';
        html += '<button id="removeRow" type="button" class="btn btn-danger removeHeadersButton">Remove</button>';
        html += '</div>';
        html += '</div>';

        $('#newHeaderRow').append(html);
    });

    function generateHeaderInputs(keyName,keyValue,iteration){

        var html = '';
        html += '<div id="inputFormRow" class="row">';
        html += '<div class="col-sm-6">';
        html += '<input type="text" readonly="readonly" class="form-control mb-2 key_name" id="key_name'+iteration+'" name="key_name[]" placeholder="Enter Header Key Name" value="'+keyName+'">';
        html += '</div>';


        html += '<div class="col-sm-6">';
        html += '<input type="text" class="form-control mb-2 key_value d-inline-block w-75 " id="key_value'+iteration+'" name="key_value[]" value="'+keyValue+'">';
        html += '<button id="removeRow" type="button" class="btn btn-danger removeHeadersButton">Remove</button>';
        html += '</div>';
        html += '</div>';

        $('#newHeaderRow').append(html);

    }

    // remove row
    $(document).on('click', '#removeRow', function () {
        $(this).closest('#inputFormRow').remove();
    });



    var Table_obj = "";

    function fetchData()
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if(Table_obj != '' && Table_obj != null)
        {
            $('#serverDataTable').dataTable().fnDestroy();
            $('#serverDataTable tbody').empty();
            Table_obj = '';
        }


        Table_obj = $('#serverDataTable').DataTable({
            processing: true,
            columnDefs: [
                { targets: '_all',
                    orderable: true
                },
            ],
            serverSide: true,
            ajax: "{{ url('admin/fetch-servers-data/'.$schedule_id) }}",
            columns: [
                { data: 'srno', name: 'srno' },
                { data: 'server_type', name: 'server_type' },
                { data: 'name', name: 'name' },
                { data: 'sport_name', name: 'sport_name' },
                { data: 'link', name: 'sport_name' },
                {
                    data: 'isHeader', name: 'isHeader', render: function (data, type, full, meta, rowData) {

                        if (data == 'Yes') {
                            return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>" + data + "</a>" + " ";
                        } else {
                            return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>" + data + "</a>" + " ";
                        }
                    },
                },
                {
                    data: 'isPremium', name: 'isPremium', render: function (data, type, full, meta, rowData) {

                        if (data == 'Yes') {
                            return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>" + data + "</a>" + " ";
                        } else {
                            return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>" + data + "</a>" + " ";
                        }
                    },
                },

                { data: 'isTokenAdded', name: 'isTokenAdded' , render: function( data, type, full, meta,rowData ) {

                        if(data=='Yes'){
                            return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                        }
                        else{
                            return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                        }
                    },

                },
                { data: 'isIpAddressApiCall', name: 'isIpAddressApiCall' , render: function( data, type, full, meta,rowData ) {

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


                {data: 'action', name: 'action', orderable: false},
            ],
            order: [[0, 'asc']]
        });

    }


    function loadServersOption(){

        $.ajax({
            type:"POST",
            url: "<?php echo e(url('admin/getServersList')); ?>",
            data: { schedule_id: {{ $schedule_id }} },
            success: function(response){
                $("#server_id").html(response);
            }
        });

    }


    function showHideHeaderParams(bool){

        if(bool == "1"){
            $(".headerParamsBlock").show();
            $("#key_name,#key_value").removeAttr('disabled');
            $("#key_name,#key_value").attr('required','required');

        }
        else{
            $(".headerParamsBlock").hide();
            $("#key_name,#key_value").removeAttr('required');
            $("#key_name,#key_value").attr('disabled','disabled');
        }

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
            $("#nameError").text('');
            $('#addEditForm').trigger("reset");
            $('#ajaxheadingModel').html("New Server");

            $("#newHeaderRow").html('');

            $('#isHeader0,#isPremium0,#isTokenAdded1,#isSponsorAd0').prop('checked',true);

            showHideHeaderParams(0);




            $('#ajax-model').modal('show');
        });

        $('#linkServer').click(function () {
            $('#id').val("");
            $("#linked_serverError").text('');
            $('#addEditForm-LinkServer').trigger("reset");
            $('#ajaxheadingModal-LinkServer').html("Attach Server");
            $('#attachServerModal').modal('show');
        });



        $('body').on('click', '.edit', function () {

            $("#newHeaderRow").html('');

            var id = $(this).data('id');

            $('input[name=isSponsorAd],input[name=isHeader],input[name=isPremium],input[name=isTokenAdded]').removeAttr('checked');
            $('#nameError,#sponsorAdImageUrlError,#link,#isHeaderError,#isPremiumYesError').text('');

            $.ajax({
                type:"POST",
                url: "{{ url('admin/edit-server') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){
                    console.log(res);
                    $('#id').val("");
                    $('#addEditForm').trigger("reset");

                    $('#ajaxheadingModel').html("Edit Server");


                    $('#id').val(res.id);

                    $('#name').val(res.name);

                    $('#sports_id').val(res.sports_id);

                    $('#link').val(res.link);

                    $('#sponsorAdClickUrl').val(res.sponsorAdClickUrl);

                    $('#sponsor_ad_image_hidden').val(res.sponsorAdImageUrl);

                    if(res.server_type_id){
                        $('#server_type_id').val(res.server_type_id);
                    }

                    $('#isHeader'+res.isHeader).prop('checked',true);

                    if(res.isHeader){
                        if(res.headers.length > 0){
                            for(var i=0; i<res.headers.length;i++){
                                if(i == 0){
                                    $('#key_name').val(res.headers[i].key_name);
                                    $('#key_value').val(res.headers[i].key_value);
                                }
                                else{
                                    generateHeaderInputs(res.headers[i].key_name,res.headers[i].key_value,i)
                                }
                            }
                        }

                    }

                    $('#isPremium'+res.isPremium).prop('checked',true);

                    $('#isTokenAdded'+res.isTokenAdded).prop('checked',true);
                    $('#isIpAddressApiCall'+res.isIpAddressApiCall).prop('checked',true);

                    setTimeout(function(){
                        $('#ajax-model').modal('show');
                    },500);

                    enableDisableSponsorAdFields(res.isSponsorAd);
                    $('#isSponsorAd'+res.isSponsorAd).prop('checked',true);

                    showHideHeaderParams(res.isHeader)


                }
            });
        });


        $('body').on('click', '.delete', function () {
            if (confirm("Are you sure you want to delete?") == true) {
                var id = $(this).data('id');

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/delete-server/' . $schedule_id) }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){
                        var dataTablePageInfo = Table_obj.page.info();
                        Table_obj.page(dataTablePageInfo.page).draw('page');
                        // fetchData();
                    }
                });
            }
        });



        $("#addEditForm").on('submit',(function(e) {
            e.preventDefault();
            var Form_Data = new FormData(this);


            $("#btn-save").html('Please Wait...');
            $("#btn-save"). attr("disabled", true);

            $('#nameError,#sports_idError,#linkError,#isHeaderError,#isPremiumError').text('');

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
                url: "{{ url('admin/add-update-servers/'.$schedule_id) }}",
                data: Form_Data,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                success: function(res){
                    // fetchData();

                    var dataTablePageInfo = Table_obj.page.info();
                    Table_obj.page(dataTablePageInfo.page).draw('page');

                    $('#ajax-model').modal('hide');
                    $("#btn-save").html('Save');
                    $("#btn-save"). attr("disabled", false);

                    loadServersOption();
                },
                error:function (response) {
                    $("#btn-save").html(' Save');
                    $("#btn-save"). attr("disabled", false);

                    $('#nameError').text(response.responseJSON.errors.name);
                    $('#sports_idError').text(response.responseJSON.errors.sports_id);
                    $('#linkError').text(response.responseJSON.errors.link);
                    $('#isHeaderError').text(response.responseJSON.errors.isHeader);
                    $('#isPremiumError').text(response.responseJSON.errors.isPremium);
                }
            });
        }));


        $("#addEditForm-LinkServer").on('submit',(function(e) {
            e.preventDefault();
            var Form_Data = new FormData(this);

            $("#btn-save-LinkServer").html('Please Wait...');
            $("#btn-save-LinkServer").attr("disabled", true);

            $.ajax({
                type:"POST",
                url: "{{ url('admin/attach-servers/'.$schedule_id) }}",
                data: Form_Data,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                success: function(res){
                    // fetchData();

                    var dataTablePageInfo = Table_obj.page.info();
                    Table_obj.page(dataTablePageInfo.page).draw('page');

                    $('#attachServerModal').modal('hide');
                    $("#btn-save-LinkServer").html('Save');
                    $("#btn-save-LinkServer"). attr("disabled", false);
                },
                error:function (response) {
                    $("#btn-save-LinkServer").html(' Save');
                    $("#btn-save-LinkServer"). attr("disabled", false);
                    $('#linked_serverError').text(response.responseJSON.errors.message);
                }
            });
        }));

        loadServersOption();

    });

</script>

@endpush
