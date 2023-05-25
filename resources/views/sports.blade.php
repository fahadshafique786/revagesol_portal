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
                                <div class="col-6 text-left">
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-sports'))
                                            <a class="btn btn-info d-inline-block" href="javascript:void(0)" id="addNew">
                                                Add Sport
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-sports'))
                                            <button class="btn btn-danger d-inline-block delete_all" data-table_id="DataTbl" data-url="{{ url('admin/sportsDeleteAll') }}">Delete All Selected</button>
                                        @endif


                                </div>

                                <div class="col-6 text-right">
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-sports'))
                                            <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                                <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                            </a>
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
                                    <th scope="col">Icon</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Multi League</th>
                                    <th scope="col">Image Required</th>
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
                                    <label for="name" class="control-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">

                                    <span class="text-danger" id="nameError"></span>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label d-block">  Sports Type </label>
                                    <label for="sports_type_single" class="cursor-pointer">
                                        <input type="radio" class="" id="sports_type_single" name="sports_type" value="single"  />
                                        Single
                                    </label>

                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="sports_type_double" class="cursor-pointer">
                                        <input type="radio" class="" id="sports_type_double" name="sports_type"  value="double" checked />
                                        Double
                                    </label>

                                    <span class="text-danger" id="sports_typeError"></span>
                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label d-block">Multi League</label>
                                    <label for="multi_league1" class="cursor-pointer">
                                        <input type="radio" class="" id="multi_league1" name="multi_league" value="1" checked/>
                                        <span class="">Yes</span>
                                    </label>

                                    &nbsp;&nbsp;&nbsp;&nbsp;

                                    <label for="multi_league0" class="cursor-pointer">
                                        <input type="radio" class="" id="multi_league0" name="multi_league"  value="0" />
                                        <span class="">No</span>
                                    </label>

                                    <span class="text-danger" id="multi_leagueError"></span>
                                </div>

                            </div>



                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label d-block">Image Required</label>

                                    <label for="image_required1" class="cursor-pointer">
                                        <input type="radio" class="EnableDisableFileUpload" onchange="enableDisableImageRequired(this.value)" id="image_required1" name="image_required" value="1" checked/>
                                        <span class="">Yes</span>
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="image_required0" class="cursor-pointer">
                                        <input type="radio" class="EnableDisableFileUpload" id="image_required0"  onchange="enableDisableImageRequired(this.value)"  onclick="$('#sport_logoError').text('');" name="image_required" value="0"  />
                                        <span class="">No</span>
                                    </label>
                                    <span class="text-danger" id="image_requiredError"></span>

                                </div>


                            </div>


                            <div class="form-group row">

                                <div class="col-sm-12">
                                    <label for="name" class="control-label d-block">Sport Logo</label>

                                        <input type="file" class="EnableDisableFileUpload-File" id="sport_logo" name="sport_logo" onchange="allowonlyImg(this); if(this.value !=''){ $('#sport_logoError').text('');}">
                                        <input type="hidden" readonly class="" id="sport_logo_hidden" name="sport_logo_hidden" >
                                        <span class="text-danger" id="sport_logoError"></span>

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

        var Table_obj = "";

        function callDataTableWithFilters()
        {
            $("#master").prop('checked',false);

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
                columnDefs: [
                    { targets: '_all',
                        orderable: true
                    },
                ],
                serverSide: true,
                ajax: "{{ url('admin/fetchsportsdata') }}",
                columns: [
                    { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                    { data: 'srno', name: 'srno' , searchable:false},
                    { data: 'icon', name: 'icon', searchable:false},
                    { data: 'name', name: 'name' },
                    { data: 'sports_type', name: 'sports_type' },
                    { data: 'multi_league', name: 'multi_league' , searchable:false , render: function( data, type, full, meta,rowData ) {
                            if(data=='Yes'){
                                return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                            else{
                                return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                        },


                    },
                    { data: 'image_required', name: 'image_required' , searchable:false , render: function( data, type, full, meta,rowData ) {

                            if(data=='Yes'){
                                return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                            else{
                                return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                        },
                    },
                    {data: 'action', name: 'action', orderable: false , searchable:false},
                ],
                order: [[1, 'asc']],
            });

        }


        function enableDisableImageRequired(bool){

            if(bool == "1"){
                $("#sport_logo").removeAttr('disabled');
                // $("#sport_logo").attr('required','required');
            }
            else{
                // $("#sport_logo").removeAttr('required');
                $("#sport_logo").attr('disabled','disabled');
            }
        }

        $(document).ready(function($){

            callDataTableWithFilters();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            $('#addNew').click(function () {

                enableDisableImageRequired($("#image_required1").val());

                $('#id').val("");

                $('#nameError,#sport_logoError').text('');

                $('#addEditForm').trigger("reset");

                $('#ajaxheadingModel').html("Add Sports");

                $("#sports_type_double").attr('checked','checked');

                $("#sport_logo_hidden").val('');

                $('#ajax-model').modal('show');
            });

            $('body').on('click', '.edit', function () {

                var id = $(this).data('id');

                $('#nameError').text('');

                $('#sport_logoError').text('');

                $("input[name=sports_type]").removeAttr('checked');

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/edit-Sport') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){
                        $("#password").prop("required",false);
                        $('#id').val("");
                        $('#addEditForm').trigger("reset");
                        $('#ajaxheadingModel').html("Edit Sports");
                        $('#ajax-model').modal('show');
                        $('#id').val(res.id);
                        $('#name').val(res.name);
                        $('#sport_logo_hidden').val(res.icon);

                        $("#multi_league"+res.multi_league).prop("checked",true);

                        $("#image_required"+res.image_required).prop("checked",true);

                        if(res.image_required == "1"){
                            $("#sport_logo").removeAttr('disabled');
                        }
                        else{
                            $("#sport_logo").attr('disabled','disabled');
                        }

                        $("#sports_type_"+res.sports_type).prop("checked",true);


                    }
                });
            });


            $('body').on('click', '.delete', function () {
                if (confirm("Are you sure you want to delete?") == true) {
                    var id = $(this).data('id');

                    $.ajax({
                        type:"POST",
                        url: "{{ url('admin/delete-sport') }}",
                        data: { id: id },
                        dataType: 'json',
                        success: function(res){
                            var dataTablePageInfo = Table_obj.page.info();
                            Table_obj.page(dataTablePageInfo.page).draw('page');
                            // callDataTableWithFilters();
                            // location.reload();
                        }
                    });
                }
            });


            $("#image_required_no").on('click',function(){
                $('#sport_logoError').text('');
            })

            /****** Add or Update Form Submit ::  Function **********/

            $("#addEditForm").on('submit',(function(e) {

                e.preventDefault();

                var Form_Data = new FormData(this);

                $("#btn-save").html('Please Wait...');

                $("#btn-save"). attr("disabled", true);

                $('#nameError').text('');

                $('#sports_typeError').text('');

                $('#multi_leagueError').text('');

                $('#sport_logoError').text('');


                    if($("#image_required1").prop('checked') && !$("#sport_logo_hidden").val()){

                        if(!$("#sport_logo").val()){
                            alert("Please select sports logo!")
                            $("#btn-save").html('Save');
                            $("#btn-save"). attr("disabled", false);
                            $('#sport_logoError').text('Please select logo!');
                            return false;
                        }
                    }


                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/add-update-Sport') }}",
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
                        $('#sports_typeError').text(response.responseJSON.errors.sports_type);
                        $('#multi_leagueError').text(response.responseJSON.errors.multi_league);
                        $('#image_requiredError').text(response.responseJSON.errors.image_required);
                    }
                });
            }));


        }); // end document ready!

    </script>

@endpush
