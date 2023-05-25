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
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-server-type'))
                                            <a class="btn btn-info d-inline-block" href="javascript:void(0)" id="addNew">
                                                Add Server Type
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-server-type'))
                                            <button class="btn btn-danger d-inline-block delete_all" data-table_id="DataTbl" data-url="{{ url('admin/server-types/remove-all') }}"> Delete All Selected </button>
                                        @endif

                                </div>

                                <div class="col-6 text-right">
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-server-type'))
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
                                    <th scope="col">Server Type Name</th>
                                    <th scope="col">Slug</th>
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
                                    <label for="name" class="control-label d-block">  Server Type Name </label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Server Type Name" value="" maxlength="50" required="">

                                    <span class="text-danger" id="nameError"></span>

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
                ajax: "{{ url('admin/fetch-server-types-data') }}",
                columns: [
                    { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
                    { data: 'srno', name: 'srno' , searchable:false},
                    { data: 'name', name: 'name' },
                    { data: 'slug', name: 'slug' },
                    {data: 'action', name: 'action', orderable: false , searchable:false},
                ],
                order: [[1, 'asc']],
            });

        }

        $(document).ready(function($){

            callDataTableWithFilters();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            $('#addNew').click(function () {

                $('#id').val("");
                $('#nameError').text('');
                $('#addEditForm').trigger("reset");
                $('#ajaxheadingModel').html("Add Sports");
                $('#ajax-model').modal('show');
            });

            $('body').on('click', '.edit', function () {
                var id = $(this).data('id');
                $('#nameError').text('');

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/edit-server-type') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){
                        $('#id').val("");
                        $('#addEditForm').trigger("reset");
                        $('#ajaxheadingModel').html("Edit Sports");
                        $('#ajax-model').modal('show');
                        $('#id').val(res.id);
                        $('#name').val(res.name);
                    }
                });
            });

            $('body').on('click', '.delete', function () {

                if (confirm("Are you sure you want to delete?") == true) {

                    var id = $(this).data('id');

                    $.ajax({
                        type:"POST",
                        url: "{{ url('admin/delete-server-type') }}",
                        data: { id: id },
                        dataType: 'json',
                        success: function(res){
                            callDataTableWithFilters();
                        }
                    });
                }
            });

            /****** Add or Update Form Submit ::  Function **********/

            $("#addEditForm").on('submit',(function(e) {

                e.preventDefault();

                var Form_Data = new FormData(this);

                $("#btn-save").html('Please Wait...');

                $("#btn-save"). attr("disabled", true);

                $('#nameError').text('');

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/add-update-server-type') }}",
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
                    }
                });
            }));


        }); // end document ready!

    </script>

@endpush
