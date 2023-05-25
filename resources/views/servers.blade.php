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
                                    <select class="form-control" id="sports_filter" name="sports_filter" onchange="getLeaguesOptionBySports(this.value,'leagues_filter');$('button#filter').trigger('click');">
                                        <option value="-1">   Select Sports </option>
                                        @foreach ($sports_list as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control" id="leagues_filter" name="leagues_filter" onchange="$('button#filter').trigger('click');">
                                        <option value="-1">   Select League </option>
                                    </select>

                                </div>

                                <div class="col-sm-2 visiblilty-hidden">
                                    <button type="button" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                </div>

                                <div class="col-6 pull-right text-right">
                                    <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                        <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                    </a>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-6 text-left">

                                    <div class="pull-left">
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-servers'))
                                            <a class="btn btn-info d-inline-block" href="javascript:void(0)" id="addNew">
                                                Add Server
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-servers'))
                                            <button class="btn btn-danger d-inline-block delete_all" data-table_id="DataTbl" data-url="{{ url('admin/serversDeleteAll') }}">Delete All Selected</button>
                                        @endif


                                    </div>
                                </div>

                            </div>


                        </div>
                        <div class="card-body">

                            <table class="table table-bordered table-hover serverDataTable customResponsiveDatatable" id="serverDataTable">
                                <thead>
                                <tr>
                                    <th scope="col" width="10px">
                                        <input type="checkbox" name="" id="master" />
                                    </th>
                                    <th scope="col" width="10px">#</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Sport</th>
                                    <th scope="col">League</th>
                                    <th scope="col">Link</th>
                                    <th scope="col">isTokenAdded</th>
                                    <th scope="col">Headers</th>
                                    <th scope="col">Premium</th>
                                    <th scope="col">isApi</th>
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
            <div class="modal-dialog modal-lg ">
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
                                    <label for="name" class="control-label">Sport</label>
                                    <select class="form-control" id="sports_id" name="sports_id" required onchange="getLeaguesOptionBySports(this.value,'leagues_id')">
                                        <option value="">   Select Sport </option>
                                        @foreach ($sports_list as $sport)
                                            <option value="{{ $sport->id }}"  {{ (isset($sport->id) && old('id')) ? "selected":"" }}>{{ $sport->name }}</option>
                                        @endforeach
                                    </select>

                                    <span class="text-danger" id="sports_idError"></span>

                                </div>

                                <div class="col-sm-6">
                                    <label for="name" class="control-label">Leagues</label>
                                    <select class="form-control" id="leagues_id" name="leagues_id" required>
                                        <option value="">   Select League </option>
                                    </select>

                                    <span class="text-danger" id="leagues_idError"></span>

                                </div>


                            </div>

                            <div class="form-group row">

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

                                <div class="col-sm-4">
                                </div>

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


        $('#filter').click(function(){
            var sports_filter = $('#sports_filter').val();
            var leagues_filter = $('#leagues_filter').val();

            if(sports_filter != '' || leagues_filter != '')
            {
                $('#serverDataTable').DataTable().destroy();
                if(sports_filter != '-1'){ // for all...
                    fetchData(sports_filter,leagues_filter);
                }
                else{
                    fetchData();
                }
                $("#master").prop('checked',false);
            }
            else
            {
                $('#serverDataTable').DataTable().destroy();
                fetchData();
            }
        });




        var Table_obj = "";
        function fetchData(filter_sports = "",filter_leagues = "")
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
                "ajax" : {
                    url:"{{ url('admin/fetch-servers-data') }}",
                    type:"POST",
                    data:{
                        filter_sports:filter_sports,filter_leagues:filter_leagues
                    }
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                    { data: 'srno', name: 'srno' },
                    { data: 'server_type', name: 'server_type' },
                    { data: 'name', name: 'name' },
                    { data: 'sport_name', name: 'sport_name' },
                    { data: 'league_name', name: 'league_name' },
                    { data: 'link', name: 'sport_name' },
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
                    { data: 'isHeader', name: 'isHeader' , render: function( data, type, full, meta,rowData ) {

                            if(data=='Yes'){
                                return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                            else{
                                return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                        },

                    },

                    { data: 'isPremium', name: 'isPremium' , render: function( data, type, full, meta,rowData ) {

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
                order: [[1, 'asc']]
            });

        }

        function callDataTableWithFilters(){
            $("#master").prop('checked',false);
            fetchData($('#sports_filter').val(),$('#leagues_filter').val());
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
                $('#addEditForm').trigger("reset");

                $('#ajaxheadingModel').html("Add Server");

                $('#nameError,#leagues_idError,#sport_logoError').text('');

                $("#newHeaderRow").html('');

                if($("#sports_filter").val() > 0){
                    $("#sports_id").val($("#sports_filter").val());
                }

                setTimeout(function(){
                    getLeaguesOptionBySports($("#sports_id").val(),'leagues_id');
                },500);

                setTimeout(function(){
                    $("#leagues_id").val($("#leagues_filter").val());
                    $('#ajax-model').modal('show');
                },1000);

                $('#isSponsorAd0,#isPremium0,#isHeader0').prop('checked',true);
                $('#isTokenAdded1').prop('checked',true);

                enableDisableSponsorAdFields(0);

                showHideHeaderParams(0);

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

                        getLeaguesOptionBySports(res.sports_id,'leagues_id');

                        $('#id').val("");
                        $('#addEditForm').trigger("reset");
                        $('#ajaxheadingModel').html("Edit Server");
                        $('#sports_id').val(res.sports_id);

                        $('#id').val(res.id);
                        $('#name').val(res.name);
                        $('#link').val(res.link);

                        $('#sponsorAdClickUrl').val(res.sponsorAdClickUrl);
                        if(res.server_type_id){
                            $('#server_type_id').val(res.server_type_id);
                        }
                        $('#sponsor_ad_image_hidden').val(res.sponsorAdImageUrl);


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
                            $('#leagues_id').val(res.leagues_id);
                            $('#ajax-model').modal('show');
                        },1000);

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
                        url: "{{ url('admin/delete-server') }}",
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

                $('#nameError,#sports_idError,#leagues_idError,#serverTypeIdError').text('');


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
                    url: "{{ url('admin/add-update-servers') }}",
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
                    },
                    error:function (response) {
                        $("#btn-save").html(' Save');
                        $("#btn-save"). attr("disabled", false);

                        $('#nameError').text(response.responseJSON.errors.name);
                        $('#sports_idError').text(response.responseJSON.errors.sports_id);
                        $('#leagues_idError').text(response.responseJSON.errors.leagues_id);
                        $('#serverTypeIdError').text(response.responseJSON.errors.server_type_id);


                    }
                });
            }));
        });

    </script>

@endpush
