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
                                  <div class="col-3  mb-3 text-left">
                                      <div class="pull-left">
                                          <select class="form-control" id="account_filter" name="account_filter" onchange="fetchData()"  >
                                              <option value="-1" selected>   Select Accounts </option>
                                              @foreach ($accountsList as $obj)
                                                  <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                              @endforeach
                                          </select>


                                      </div>
                                  </div>

                                  <div class="col-3">

                                      <button type="button" class="btn btn-primary d-none" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                  </div>

                                  <div class="col-6 pull-right text-right">
                                      <a class="btn btn-warning" href="javascript:window.location.reload()" id="">
                                          <i class="fa fa-spinner"></i> &nbsp; Refresh Screen
                                      </a>
                                  </div>


                              </div>
                              <div class="row">
                                  <div class="col-12 text-left">
                                          @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-block-app-countries'))
                                              <a class="btn btn-dark  d-inline-block" href="javascript:void(0)" id="addNew">
                                                  Block New Application
                                              </a>
                                          @endif
                                          @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('manage-block-app-countries'))
                                              <button  class="btn btn-danger d-inline-block delete_all" data-table_id="DataTbl" data-url="{{ url('admin/remove/all-block-apps') }}">Delete All Selected</button>
                                          @endif
                                  </div>
                              </div>
                          </div>

						<div class="card-body">
							<table class="table table-bordered table-hover" id="DataTbl">
								<thead>
									<tr>
                                        <th scope="col" width="10px">
                                            <input type="checkbox" name="" class="master" id="master" />
                                        </th>
										<th scope="col">#</th>
										<th scope="col">Application</th>
										<th scope="col">Countries</th>
										<th scope="col">Enable Proxy & Country</th>
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
              <input type="hidden"  name="id" id="id"  />
              <div class="form-group row">

                <div class="col-sm-12 mb-2">
                    <label for="app_detail_id" class="control-label">Application</label>
                    <select class="form-control" id="application_id" name="application_id" required>
                        <option value="">   Select App </option>
                        @foreach ($applications as $obj)
                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->appName . ' - ' . $obj->packageId}}</option>
                        @endforeach
                    </select>

                    <span class="text-danger" id="application_idError"></span>

                </div>
                <div class="col-sm-12">

                    <label for="name" class="control-label">Countries</label><br/>
                    <select class="js-example-basic-multiple" id="country_ids" name="country_ids[]" multiple="multiple" required>
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->country_name}}</option>
                        @endforeach
                    </select><br/>
                    <span class="text-danger" id="country_idError"></span>

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

    $('#filter').click(function(){
        var filter_accounts_id = $('#account_filter').val();
        if(filter_accounts_id != '')
        {
            // $('#DataTbl').DataTable().destroy();
            // if(filter_accounts_id != '-1'){ // for all...
            //     fetchData(filter_accounts_id);
            // }
            // else{
                fetchData();
            // }
            $("#master").prop('checked',false);
        }
        else
        {
            $('#DataTbl').DataTable().destroy();
            fetchData();
        }
    });



var Table_obj = "";

function getRemainingAppsForBlockedCountriesOptions(application_id){

    let account_id = $("#account_filter").val();

    $.ajax({
        type:"POST",
        url: "{{ url('admin/remaining-block-apps-option') }}",
        data: { id: application_id , account_id : account_id},
        success: function(response){
            $("#application_id").html(response);
        }
    });

}


function fetchData()
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
		"processing": true,
		"serverSide": true,
        "order" : [],
        "searching" : true,
        "paging": true,
        "ajax" : {
            url:"{{ url('admin/fetch-blocked-apps') }}",
            type:"POST",
            data:{
                filter_accounts_id:filter_accounts_id
            }
        },
		columns: [
        { data: 'checkbox', name: 'checkbox' , orderable:false , searchable:false},
		{ data: 'srno', name: 'srno' },
		{ data: 'appName', name: 'appName' },
		{ data: 'countries', name: 'countries',
            render: function( data, type, full, meta,rowData ) {
            let value = "";
            console.log(data);
		    for (let i = 0; i < data.length; i++) {
                    value +=  "<a href='javascript:void(0)' class='badge badge-info  text-xs'>"+data[i] +"</a>" +" ";
                }
                return value;
            },

        },
        { data: 'is_proxy_enable', name: 'is_proxy_enable', searchable:false , render: function( data, type, full, meta,rowData ) {

                if(data=='Yes'){
                    return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                }
                else{
                    return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                }
            },


        },
        { data: 'action', name: 'action' },
		],
		order: [[1, 'asc']]
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

     $(document).delegate('.isLiveStatusSwitch', 'click', function(event,state){

         var application_id = $(this).attr('data-app-id');
         let bools = $(this).attr('aria-pressed');
         var is_proxy_enable  = (bools == "true" ) ? 1 : 0;

         $.ajax({
             type:"POST",
             url: "{{ url('admin/app-detail/proxy/change-status') }}",
             data: { application_id: application_id , is_proxy_enable :  is_proxy_enable},
             dataType: 'json',
             success: function(res){
                 // fetchData();
                 var dataTablePageInfo = Table_obj.page.info();
                 Table_obj.page(dataTablePageInfo.page).draw('page');

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

				$("#btn-save").html('<i class="fa fa-save"></i> Save');
				$("#btn-save"). attr("disabled", false);

			}
         });

     });

     $("#country_ids").on("select2:unselecting", function (e) {
         $("#country_ids :selected").map(function(i, el) {

             if($(el).text() == 'All Countries'){
                 $('#country_ids option').prop('disabled',false);
             }
         }).get();

         $('#country_ids').select2("destroy").select2();
     });

     $("#country_ids").on("select2:select", function (e) {
         $("#country_ids :selected").map(function(i, el) {

             if($(el).text() == 'All Countries'){

                 $('#country_ids option[value !="1"]').prop('disabled',true);
                 $('#country_ids').val('1'); // Select the option with a value of '1'
                 $('#country_ids').trigger('change');
                 // $("#country_ids option[value !='1']").val(null).trigger('change');
                 // $('#country_ids').select2("destroy").select2();

                 // $("#country_ids").val(null).trigger('change');
                 // $("#country_ids option[value='1']").attr('selected','selected');
                 // $('#country_ids').select2("destroy").select2();
             }
         }).get();
         $('#country_ids').select2("destroy").select2();
     });

    $('#addNew').click(function () {
        $("#application_id").val('');
        $('#country_ids option').prop('disabled',false);
        $('#country_ids').select2();
        getRemainingAppsForBlockedCountriesOptions('-1');
        $("#country_ids").select2("val","");
        $('#addEditForm').trigger("reset");
		$("#password").prop("required",true);
        $('#ajaxheadingModel').html("Block New Application");
        $('#ajax-model').modal('show');
    });

    $('body').on('click', '.edit', function () {

        var id = $(this).data('id');

        // $('#country_ids').select2('destroy').select2();

        $.ajax({
            type:"POST",
            url: "{{ url('admin/edit-blocked-application') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){
                $('#addEditForm').trigger("reset");
                $('#id').val(id);
                getRemainingAppsForBlockedCountriesOptions(id);
                $('#ajaxheadingModel').html("Edit Block Application");
                $('#ajax-model').modal('show');

                $("#application_id").val(res.application_id);

                $.each(res.countries,function(key,obj){
                    if(obj.country_id == 1){
                        $("#country_ids").select2("val", $("#country_ids").select2("val").concat(obj.country_id));
                    }
                    else{
                        $('#country_ids option').prop('disabled',false);
                        $("#country_ids").select2("val", $("#country_ids").select2("val").concat(obj.country_id));
                    }
                });
                $("#country_ids").select2();


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
                    url: "{{ url('admin/delete-blocked-application') }}",
                    data: {id: id},
                    dataType: 'json',
                    success: function (res) {
                        var dataTablePageInfo = Table_obj.page.info();
                        Table_obj.page(dataTablePageInfo.page).draw('page');
                        // fetchData();
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
            url: "{{ url('admin/add-update-blocked-applications') }}",
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
				$("#btn-save").html('<i class="fa fa-save"></i> Save');
				$("#btn-save"). attr("disabled", false);

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

				$("#btn-save").html('<i class="fa fa-save"></i> Save');
				$("#btn-save"). attr("disabled", false);

			}
        });
    }));
});


</script>

@endpush
