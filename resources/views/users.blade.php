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
									<div class="pull-left">
										@if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-users'))
                                        <a class="btn btn-dark " href="javascript:void(0)" id="addNew">
                                            Add User
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
										<th scope="col">User Name</th>
										<th scope="col">Email</th>
										<th scope="col">Role</th>
{{--										<th scope="col">Phone</th>--}}
{{--										<th scope="col">Status</th>--}}
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
      <div class="modal-dialog modal-md">
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
					<label for="name" class="control-label">User Name</label>
					<input type="text" class="form-control disableSpecialCharacters" id="user_name"  onfocusout="convertLowerCase(this.value,this.id)"  name="user_name" placeholder="Enter User Name" value="" maxlength="50" required="">
					<span class="text-danger" id="user_nameError"></span>
                </div>

              </div>

			  <div class="form-group row">

                <div class="col-sm-12">
					<label for="name" class="control-label">Email</label>
					<input type="email" class="form-control" id="email" name="email" placeholder="Enter Email"   required="">
					<span class="text-danger" id="emailError"></span>
                </div>

              </div>

               @if(Auth::user()->hasRole("super-admin") || auth()->user()->can("manage-users"))
                  <div class="form-group row passwordRow">

                    <div class="col-sm-12">
                        <label for="name" class="control-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password Here">
                        <span class="text-danger" id="passwordError"></span>
                    </div>

                  </div>
                @endif

                <div class="form-group row">

                    <div class="col-sm-12">
                        <label for="name" class="control-label">Role</label>
                        <select class="js-example-basic-single" id="user_role" name="role_id"  required >
                            @foreach($roles as $role)
                                <?php
                                if(!auth()->user()->hasRole("super-admin")){
                                    if($role->name == 'super-admin' || $role->id == '1'){
                                        continue;
                                    }
                                }
                                ?>
                                <option data-fahad value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select><br/>
                        <span class="text-danger" id="roleError"></span>
                    </div>

                </div>


              <div class="col-sm-offset-2 col-sm-12 text-right">
                <button type="submit" class=" mb-3 btn btn-dark" id="btn-save" >
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
		ajax: "{{ url('admin/fetchusersdata') }}",
		columns: [
		{ data: 'srno', name: 'srno' },
		{ data: 'name', name: 'name' },
		{ data: 'user_name', name: 'user_name' },
		{ data: 'email', name: 'email' },
		{ data: 'role', name: 'role',
            render: function( data, type, full, meta,rowData ) {

               if(data.length) {
                   let value = "";
                   for (let i = 0; i < data.length; i++) {
                       value += "<a href='javascript:void(0)' class='badge badge-info text-xs'>" + data[i].name + "</a>" + " ";
                   }
                   return value;
               }
                else{
                   return  "<a href='javascript:void(0)' class='badge badge-danger  text-xs'>"+"No Role Assigned Yet"+ "</a>" ;
                }
                //
            },
        },
		// { data: 'phone', name: 'phone' },
		// { data: 'status', name: 'status' },
		{data: 'action', name: 'action', orderable: false},
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
        $('#addEditForm').trigger("reset");

		$("#password").prop("required",true);

        $('#ajaxheadingModel').html("Add User");

        $('#user_name').removeAttr('disabled');
        $('#email').removeAttr('disabled');

        $('#user_nameError,#emailError,#roleError,#passwordError,#roleError').text('');

        $('#ajax-model').modal('show');
    });

    $('body').on('click', '.edit', function () {

        var id = $(this).data('id');

        $('#user_nameError,#emailError,#roleError,#passwordError').text('');

        $('#user_name').attr('disabled','disabled');
        $('#email').attr('disabled','disabled');

        // $(".passwordRow").hide();
        // $("#password").removeAttr('required');

        $.ajax({
            type:"POST",
            url: "{{ url('admin/edit-user') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){
			  $("#password").prop("required",false);
			  $('#id').val("");
			  $('#addEditForm').trigger("reset");
              $('#ajaxheadingModel').html("Edit User");

              $('#id').val(res.id);
              $('#name').val(res.name);
			  $('#user_name').val(res.user_name);
			  $('#email').val(res.email);


                setTimeout(function(){

                    $('#user_role').select2('val', res.role_id);

                    $('#ajax-model').modal('show');

                },800);



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
                    type:"POST",
                    url: "{{ url('admin/delete-user') }}",
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){
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
        $('#user_nameError,#emailError').text('');

        $.ajax({
            type:"POST",
            url: "{{ url('admin/add-update-user') }}",
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
				$('#user_nameError').text(response.responseJSON.errors.user_name);
				$('#emailError').text(response.responseJSON.errors.email);
			}
        });
    }));
});

</script>

@endpush
