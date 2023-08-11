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
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-roles'))
										<a class="btn btn-info" href="javascript:void(0)" id="addNew">
                                            Add Role
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
										<th scope="col">Permissions</th>
										<th scope="col" width="100px">Action</th>
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
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="ajaxheadingModel"></h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          </div>
          <div class="modal-body">
            <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id" id="id">
              <div class="form-group row">
                <div class="col-sm-12 mb-3">
					<label for="name" class="control-label">Name</label>
					<input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                    <span class="text-danger" id="user_nameError"></span>
                </div>
				<div class="col-sm-12">
					<label for="name" class="control-label">Permissions</label><br/>
                    <select class="js-example-basic-multiple" id="permissionss" name="permissions[]" multiple="multiple" required>
                        @foreach($permissions as $permission)
                        <option value="{{$permission->id}}" selected>{{$permission->name}}</option>
                        @endforeach
                    </select><br/>
					<span class="text-danger" id="permissionError"></span>
                </div>
              </div>


                <div class="form-group row">

                    <div class="col-sm-12 isShowAccountListOptions d-none">
                        <label for="name" class="control-label">Is Show Applications List </label><br/>
                        <label for="isShowAccountList1" class="cursor-pointer">
                            <input type="radio" class="" checked
                                   id="isShowAccountList1" name="isShowAccountList" value="1"   />
                            <span class="">Yes</span>
                        </label>

                        <label for="isShowAccountList0" class="cursor-pointer">
                            <input type="radio" class="" 
                             id="isShowAccountList0" name="isShowAccountList" value="0"  />
                            <span class="">No</span>
                        </label>
                        <span class="text-danger" id="isShowAccountListError"></span>
                    </div>

                    <div class="col-sm-10 accountListRow">
                        <label for="name" class="control-label">Accounts</label><br/>
                        <!-- getRolesAppsListByAccounts('account_filter','account_ids',false); -->
                        <select required   multiple="multiple" class="form-control js-example-basic-multiple" id="account_ids" name="account_id[]" onchange="$('#selectAllAccountsData').prop('checked',false);"  >
                            @foreach ($accountsList as $obj)
                                <option value="{{ $obj->id }}" >{{ $obj->name }}</option>
                            @endforeach
                        </select>

                        <br/>
                        <span class="text-danger" id="account_idsError"></span>
                    </div>

                    <div class="col-sm-2 accountListRow mt-4">
                        <label for="selectAllAccountsData" class="col-form-label">
                            <input type="checkbox" id="selectAllAccountsData" > &nbsp; Select All Accounts
                        </label>
                    </div>                    

                    
                </div>



                <div class="col-sm-offset-2 col-sm-12 text-right">
                <button type="submit" class="btn btn-dark" id="btn-save" >
                    <i class="fa fa-save"></i>&nbsp; Save
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
            ajax: "{{ url('admin/fetchrolesdata') }}",
            columns: [
            { data: 'srno', name: 'srno' },
            { data: 'name', name: 'name' },
            { data: 'permissions', name: 'permissions',
                render: function( data, type, full, meta,rowData ) {
                let value = "";
                for (let i = 0; i < data.length; i++) {
                        value +=  "<a href='javascript:void(0)' class='badge badge-info text-xs mb-1' style='letter-spacing:1px'>"+data[i].name +"</a>" +" ";
                    }
                    return value;
                 //   return  "<a href='javascript:void(0)' class='badge badge-success'>"+data + "</a>" ;
                },

            },
        //	{ data: 'email', name: 'email' },
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


        $("#selectAllAccountsData").click(function(){

            if($("#selectAllAccountsData").is(':checked') ){
                $("#account_ids > option").prop("selected","selected");
                $("#account_ids").trigger("change");
                $("#selectAllAccountsData").prop('checked','true')
            }else{

                // $("#account_ids").val(null).trigger("change");
                getRolesAccountsList("account_ids",true);                

            }
        });

        $('#addNew').click(function () {

            $('#id').val("");

            $('#account_ids').val(null).trigger('change');

            $("#permissionss option").attr('selected','selected');

            $("#permissionss").select2();

            $('#addEditForm').trigger("reset");

            $('#ajaxheadingModel').html("Add Role");

            $("#permissionss").select2();

            // $("#account_ids > option").prop("selected","selected");// Select All Options
            // $("#account_ids").trigger("change");
            

            $('#account_ids option:eq(0)').attr('selected', 'selected');
            $("#account_ids").trigger("change");


            // getApplicationListOptionByAccountsNoPermission($("#account_filter").val(),'account_ids','-1');            

            // enableDisableAccountInput(1);

            setTimeout(function(){
                // $('#account_ids').val(null).trigger('change');
                // if($("#account_ids option").length > 1){
                //     $("#selectAllAccountsData").prop("checked",true);
                // }
                $('#ajax-model').modal('show');
            },2500);

        });

        $(document).on('change', '#permissionss', function (e) {
            // enableDisableAccountInput(0);

            $("#isShowAccountList0").prop('checked','checked');

            permissionArr = [];
            $("#permissionss :selected").map(function(i, el) {

                if($(el).text() == 'view-accounts'  || $(el).text() == 'manage-accounts' ){
                    permissionArr.push($(el).text());
                }

                if($.inArray("view-accounts",permissionArr) >= 0) {
                    if ($.inArray("manage-accounts", permissionArr) >= 0) {

                        enableDisableAccountInput(1);
                        $("#isShowAccountList1").prop('checked', 'checked');
                        return false;
                    }
                }
            }).get();


        });

        $('#account_ids').on('select2:unselect', function (e) {
            var data = e.params.data;

            if($("#account_ids :selected").length == 0){
                $("#permissionss option[value=9]").prop("selected",false);
                $("#permissionss option[value=10]").prop("selected",false);
                $("#permissionss").select2();
                enableDisableAccountInput(0);
            }

        });

        $("#permissionss").on("select2:unselect", function (e) {
            if(e.params.data.text == 'view-accounts'  || e.params.data.text == 'manage-accounts' ){
                    enableDisableAccountInput(0);
                    return false;
            }
        });

        $("#permissionss").on("select2:select", function (e) {
            permissionArr = [];
            $("#permissionss :selected").map(function(i, el) {

                if($(el).text() == 'view-accounts'  || $(el).text() == 'manage-accounts' ){
                    permissionArr.push($(el).text());
                }

                if($.inArray("view-accounts",permissionArr) >= 0){
                    if($.inArray("manage-accounts",permissionArr) >= 0){
                        enableDisableAccountInput(1);
                        $("#isShowAccountList1").prop('checked','checked');
                        return false;
                    }
                }

            }).get();
        });


        $('body').on('click', '.editRole', function () {
            
            $('#account_ids').val(null).trigger('change');

            $("#selectAllAccountsData").prop("checked",false);

            var id = $(this).data('id');

            $('#user_nameError').text('');

            $('#permissionError').text('');

            $("#permissionss option").removeAttr('selected');

            $("#permissionss").select2();

            $("#account_ids").select2();
            
            $('#account_ids option:eq(0)').removeAttr('selected');
            $("#account_ids").trigger("change");

            $.ajax({
                type:"POST",
                url: "{{ url('admin/edit-role') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){

                    $('#id').val("");

                    $('#addEditForm').trigger("reset");

                    $('#ajaxheadingModel').html("Edit Role");

                    $('#id').val(res.id);

                    $('#name').val(res.name);

                    // if(res?.account_id){
                    //     $('#account_filter').val(res?.account_id);
                    //     $("#account_filter").select2("val", $("#account_filter").select2("val").concat(res?.account_id));
                    //     getApplicationListOptionByAccountsNoPermission(res?.account_id,'account_ids','-1');
                    // }

                    setTimeout(() => {
                        
                    
                        $.each(res.permissions,function(key,obj){
                            $("#permissionss").select2("val", $("#permissionss").select2("val").concat(obj.id));
                        });


                        $("#account_ids").select2("val","");

                        if(res.role_has_accounts?.length > 0){
                            $.each(res.role_has_accounts,function(key,obj){

                                $("#account_ids").select2("val", $("#account_ids").select2("val").concat(obj.account_id));
                            });

                            $("#isShowAccountList1").prop('checked','checked');

                        }
                        else{
                            $("#isShowAccountList0").prop('checked','checked');
                        }

                        if($.inArray("view-accounts",res.permissions) >= 0){
                            if($.inArray("manage-accounts",res.permissions) >= 0){
                                enableDisableAccountInput(1);
                                $("#isShowAccountList1").prop('checked','checked');
                                return false;
                            }
                        }
                        
                        $('#ajax-model').modal('show');

                    }, 2000);

                    setTimeout(() => {
                        var total_apps = $('#account_ids option').length - 1;
                        if(total_apps == $('#account_ids option:selected').length){
                            $("#selectAllAccountsData").prop("checked",false);
                        }

                    }, 3000);



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
                        url: "{{ url('admin/delete-role') }}",
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
            $('#user_nameError').text('');
            $('#permissionError').text('');

            $.ajax({
                type:"POST",
                url: "{{ url('admin/add-update-role') }}",
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
                    // $('#user_nameError').text(response.responseJSON.errors.name);
                    // $('#permissionError').text(response.responseJSON.errors.permissions);
                }
            });
        }));

    });

    function getRolesAccountsList(account_id_field_id,unSelectAllOption,disableFirstOption = false){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/roles/accounts/options') }}",
            data: { un_select_all_option : unSelectAllOption  , role_id : $("#id").val() , accounts : $("#account_ids").val()  , disable_first_option : disableFirstOption},
            success: function(response){
                $("#"+account_id_field_id).html(response);
                $("#"+account_id_field_id).trigger("change");
            }
        });

    }

    function getRolesAppsListByAccounts(account_id,application_id,unSelectAllOption,disableFirstOption = false){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/roles/accounts/apps-options') }}",
            data: { account_id : $("#"+account_id).val() , un_select_all_option : unSelectAllOption  , role_id : $("#id").val() , applications : $("#account_ids").val()  , disable_first_option : disableFirstOption},
            success: function(response){
                $('#account_ids').val(null).trigger('change');
                $("#"+application_id).html(response);
                // $("#"+application_id + " option").prop("selected",true);
                $("#"+application_id).trigger("change");
            }
        });

    }

    function enableDisableAccountInput(bool){
        if(bool == "1"){
            $(".accountListRow").show();
            $("select[name=account_id]").prop("disabled",false);
            $("#account_ids").select2("enable",true);
            $("input[name=isShowAccountList]").prop("disabled",false);
        }
        else{
            $(".accountListRow").hide();
            $("select[name=account_id]").prop("disabled",true);
            $("#account_ids").select2("enable",false);
            $("input[name=isShowAccountList]").prop("disabled",true);
            $("#account_ids").val(null).trigger("change");
        }
    }

</script>

@endpush
