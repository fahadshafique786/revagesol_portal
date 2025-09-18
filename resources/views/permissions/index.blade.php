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
                          <div class="card-header d-none">
                              <div class="row d-none">
                                  <div class="col-12 text-left">
                                      <div class="pull-left d-none">
                                          @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-permissions'))
                                              <a class="btn btn-dark " href="javascript:void(0)" id="addNew">
                                                  Add Permission
                                              </a>
                                          @endif
                                      </div>
                                  </div>
                              </div>
                          </div>

						<div class="card-body">
							<table class="table table-bordered table-hover" id="DataTbl">
								<thead>
									<tr>
										<th scope="col">#</th>
										<th scope="col">Name</th>
										<th scope="col">Roles</th>
{{--										<th scope="col">Action</th>--}}
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
      <div class="modal-dialog modal-sm">
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

              <div class="col-sm-offset-2 col-sm-12 text-right">
                <button type="submit" class="btn btn-dark" id="btn-save" >
                    <i class="fa fa-save"></i>&nbsp; Save
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

function fetchData()
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
		serverSide: true,
		ajax: "{{ url('admin/fetchpermissionsdata') }}",
		columns: [
		{ data: 'srno', name: 'srno' },
		{ data: 'name', name: 'name' },
		{ data: 'roles', name: 'roles',
            render: function( data, type, full, meta,rowData ) {
            let value = "";
		    for (let i = 0; i < data.length; i++) {
                    value +=  "<a href='javascript:void(0)' class='badge badge-system  text-xs'>"+data[i].name +"</a>" +" ";
                }
                return value;
             //   return  "<a href='javascript:void(0)' class='badge badge-success'>"+data + "</a>" ;
            },

        },
	//	{ data: 'email', name: 'email' },
		// { data: 'phone', name: 'phone' },
		// { data: 'status', name: 'status' },
		// {data: 'action', name: 'action', orderable: false},
		],
		order: [[0, 'asc']]
	});

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
        $('#roles').val("");
        $('#addEditForm').trigger("reset");
		$("#password").prop("required",true);
        $('#ajaxheadingModel').html("Add Permission");
        $('#ajax-model').modal('show');
    });

    $('body').on('click', '.editPermission', function () {
        var id = $(this).data('id');

        $('#nameError').text('');

        $.ajax({
            type:"POST",
            url: "{{ url('admin/edit-permission') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){
			  $('#id').val("");
			  $('#addEditForm').trigger("reset");
              $('#ajaxheadingModel').html("Edit Permission");
              $('#ajax-model').modal('show');
              $('#id').val(res.id);
              $('#name').val(res.name);
                // $('#permissionss').select2('data', res.permissions);
           }
        });
    });
    $('body').on('click', '.delete', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {


                var id = $(this).data('id');

                $.ajax({
                    type: "POST",
                    url: "{{ url('admin/delete-permission') }}",
                    data: {id: id},
                    dataType: 'json',
                    success: function (res) {
                        fetchData();
                    }
                });
            }
        })
    });
    $("#addEditForm").on('submit',(function(e) {
		e.preventDefault();
		var Form_Data = new FormData(this);
        $("#btn-save").html('Please Wait...');
        $("#btn-save"). attr("disabled", true);
        $('nameError').text('');

        $.ajax({
            type:"POST",
            url: "{{ url('admin/update-permission') }}",
            data: Form_Data,
			mimeType: "multipart/form-data",
		    contentType: false,
		    cache: false,
		    processData: false,
            dataType: 'json',
            success: function(res){
				fetchData();
				$('#ajax-model').modal('hide');
				$("#btn-save").html('<i class="fa fa-save"></i> Save');
				$("#btn-save"). attr("disabled", false);
           },
		   error:function (response) {
               if(response.responseJSON.message=='User does not have any of the necessary access rights.'){
                   Swal.fire({
                       icon: 'error',
                       title: 'Oops...',
                       text: 'User does not have any of the necessary access rights.',
                   })
               }
				$("#btn-save").html('<i class="fa fa-save"></i> Save');
				$("#btn-save"). attr("disabled", false);
				$('#nameError').text(response.responseJSON.errors.name);
			}
        });
    }));
});


</script>

@endpush
