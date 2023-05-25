  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; @php echo date('Y') @endphp <a href="javascript:void(0)">{{ config('app.name', 'Laravel') }}</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.0.5
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->

  <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Bootstrap 4 -->

<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->


  {{--<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
  <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>--}}

  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

<script>

    function getLeaguesOptionBySports(sports_id,league_filter_id){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/leagueslistbysport') }}",
            data: { sports_id: sports_id},
            success: function(response){
                $("#"+league_filter_id).html(response);
            }
        });

    }

    function getApplicationListOptionBySports(sports_id,app_filter_id,otherElementId="-1",disableFirstOption = false){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/apps-list-options') }}",
            data: { sports_id: sports_id  , disable_first_option : disableFirstOption},
            success: function(response){
                $("#"+app_filter_id).html(response);
                if(otherElementId != "-1"){
                    $("#"+otherElementId).empty();
                    $("#"+otherElementId).html(response);
                }
            }
        });

    }


    function getApplicationListOptionBySportsNoPermission(sports_id,app_filter_id,otherElementId="-1",disableFirstOption = false){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/apps-list-options-no-permissions') }}",
            data: { sports_id: sports_id  , disable_first_option : disableFirstOption},
            success: function(response){
                $("#"+app_filter_id).html(response);
                if(otherElementId != "-1"){
                    $("#"+otherElementId).empty();
                    $("#"+otherElementId).html(response);
                }
            }
        });

    }

    function getAllApplicationListOptionBySports(sports_id,app_filter_id,otherElementId="-1",disableFirstOption = false){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/apps-list-options/all') }}",
            data: { sports_id: sports_id  , disable_first_option : disableFirstOption},
            success: function(response){
                $("#"+app_filter_id).html(response);
                if(otherElementId != "-1"){
                    $("#"+otherElementId).empty();
                    $("#"+otherElementId).html(response);
                }
            }
        });

    }

    function getAllAppListOptionBySportsNoPermission(sports_id,app_filter_id,otherElementId="-1",disableFirstOption = false){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/apps-list-options-no-permissions/all') }}",
            data: { sports_id: sports_id  , disable_first_option : disableFirstOption},
            success: function(response){
                $("#"+app_filter_id).html(response);
                if(otherElementId != "-1"){
                    $("#"+otherElementId).empty();
                    $("#"+otherElementId).html(response);
                }
            }
        });

    }


    function getRemainingAppsOptionBySports(sports_id,app_filter_id,otherElementId="-1"){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/remaining-apps-options') }}",
            data: { sports_id: sports_id},
            success: function(response){
                $("#"+app_filter_id).html(response);
                if(otherElementId != "-1"){
                    $("#"+otherElementId).empty();
                    $("#"+otherElementId).html(response);
                }
            }
        });

    }


    // const convertTime12to24 = (dateTime12h) => {
    //     const [date, time , modifier] = dateTime12h.split(' ');
    //
    //     let [hours, minutes] = time.split(':');
    //
    //     const [day , month , year] = date.split('/')
    //
    //     if (hours === '12') {
    //         hours = '00';
    //     }
    //
    //     if (modifier === 'PM') {
    //         hours = parseInt(hours, 10) + 12;
    //     }
    //
    //     return `${year}-${month}-${day} ${hours}:${minutes}`;
    // }
    //
    // const convertTime24to12 = (dateTime24h) => {
    //     const [date,time] = dateTime24h.split(' ');
    //
    //     const [year , month , day] = date.split('-');
    //
    //     let [hours, minutes] = time.split(':');
    //
    //     let modifiers = "AM"
    //     if(hours > 12){
    //             hours = parseInt(hours, 10) - 12;
    //             modifiers = "PM"
    //     }
    //
    //     return `${day}/${month}/${year} ${hours}:${minutes} ${modifiers}`;
    //
    // }


    const convertTime12to24 = (dateTime12h) => {
        if(dateTime12h){
            const [date, time , modifier] = dateTime12h.split(' ');

            let [hours, minutes] = time.split(':');

            const [day , month , year] = date.split('/')

            if (hours === '12') {
                hours = '00';
            }

            if (modifier === 'PM') {
                hours = parseInt(hours, 10) + 12;
            }

            return `${year}-${month}-${day} ${hours}:${minutes}`;
        }
        else{
            return 'N/A';
        }
    }

    const convertTime24to12 = (dateTime24h) => {
        console.log(dateTime24h);
        if(dateTime24h){

            const [date,time] = dateTime24h.split(' ');

            const [year , month , day] = date.split('-');

            let [hours, minutes] = time.split(':');

            let modifiers = "AM"
            if(hours > 12){
                hours = parseInt(hours, 10) - 12;
                modifiers = "PM"
            }

            return `${day}/${month}/${year} ${hours}:${minutes} ${modifiers}`;

        }
        else{
            return 'N/A';
        }

    }

    function isConfirmSweelAlert(postID,route){

        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });


        // alert("isConfirmSweelAlert" + postID +  " --- " + route );
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })

        let urls = "{{ url('admin/') }}"+'/'+route+"";

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
                var id = postID;
                $.ajax({
                    type:"POST",
                    url: urls,
                    data: { id: id },
                    dataType: 'json',
                    success: function(res){
                        location.reload();
                    },
                    error: function(response){
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
    }


    $(document).ready(function() {

        $('.notAllowedAlphabets').on('keypress',function (e) {

            var charCode = (e.which) ? e.which : event.keyCode
            if (String.fromCharCode(charCode).match(/[^0-9+.]/g))
                return false;

        });

        $('.notAllowedAlphabets').on("cut copy paste",function(e) {
            e.preventDefault();
        });



        $(".EnableDisableFileUpload").click(function(){
             if($(this).val() === '1'){
                $(".EnableDisableFileUpload-File").removeAttr('disabled')
            }
            else{
                $(".EnableDisableFileUpload-File").attr('disabled','disabled');
            }
        });

        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });

        $(document).delegate('#headerMenuIconId.open', 'click', function(){
            $(this).removeClass('open');
			$(this).removeClass('fa-arrow-left');
			$(this).addClass('closed');
			$(this).addClass('fa-bars');
		});

        $(document).delegate('#headerMenuIconId.closed', 'click', function(){
            $(this).removeClass('fa-bars');
            $(this).addClass('fa-arrow-left');
            $(this).addClass('open');
            $(this).removeClass('closed');

        });



        $("#closeSideMenuId").click(function(){

            $(this).hide(); // hide the div
            $(".brand-image").hide();
            $(".brand-image-mini").show();

            $("#openSideMenuId").show();

        });


        $("#openSideMenuId").click(function(){

            $(this).hide();

            $(".brand-image-mini").hide();
            $(".brand-image").show();

            $("#closeSideMenuId").show();

        });

		$('#start_time,.custom-datetimepicker').datetimepicker({
                setDate: new Date(),
                minDate: new Date(),
                format: 'd/m/Y g:i A',
				formatTime: 'g:i A',
                validateOnBlur: false,

		});

		$('.custom-datetimepicker_with_minutes').datetimepicker({
                setDate: new Date(),
                minDate: new Date(),
                step: 1,
                format: 'd/m/Y g:i A',
				formatTime: 'g:i A',
                validateOnBlur: false,

		});


        $('.js-example-basic-multiple').select2({
            placeholder: "Please Select an Option ",
        });
        $(document).ready(function() {
            $('.js-example-basic-single').select2({
                placeholder: "Please Select an Option ",
            });
        });

    });
  $(function () {
    $('.dataTables').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      "responsive": true,
    });
  });


function checkFileFormat(e){

	var id = e.id;
	if(id == '')
		return false;

	var files = $('#'+id)[0].files[0];
	if(files)
	{
		var filename = files.name;
		var fileNameExt = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();
		var validExtensions = ['jpeg','jpg','png','tiff','bmp','mp4','3gp','wmv','avi','mov']; //array of valid extensions
		if ($.inArray(fileNameExt, validExtensions) == -1)
		{
		   alert("Invalid file type");
		   $("#"+id).val('');
		   return false;
		}
	}
}


    function enableDisableSponsorAdFields(bool){
        if(bool == "1"){
            $("#sponsorAdClickUrl,#sponsorAdImageUrl").removeAttr('disabled');
            $("#sponsorAdClickUrl").attr('required','required');
        }
        else{
            $("#sponsorAdClickUrl").removeAttr('required');
            $("#sponsorAdClickUrl,#sponsorAdImageUrl").attr('disabled','disabled');
        }
    }


function allowonlyImg(e){
	var id = e.id;
	if(id == '')
		return false;

	var files = $('#'+id)[0].files[0];
	if(files)
	{
		var filename = files.name;
		var filesize = files.size;
		if( filesize <= 1048576 ){
			var fileNameExt = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();
			// var validExtensions = ['jpeg','jpg','png','tiff','bmp']; //array of valid extensions
			var validExtensions = ['jpeg','jpg','png']; //array of valid extensions
			if ($.inArray(fileNameExt, validExtensions) == -1)
			{
			   alert("Invalid file type");
			   $("#"+id).val('');
			   return false;
			}
		}else{
			alert("Your file size should not be greater than 1 MB");
		   $("#"+id).val('');
		   return false;
		}
	}
}

function allowonlyVideo(e){

	var id = e.id;
	if(id == '')
		return false;

	var files = $('#'+id)[0].files[0];
	if(files)
	{
		var filename = files.name;
		var fileNameExt = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();
		var validExtensions = ['mp4','3gp','wmv','avi','mov','flv']; //array of valid extensions
		if ($.inArray(fileNameExt, validExtensions) == -1)
		{
		   alert("Invalid file type");
		   $("#"+id).val('');
		   return false;
		}
	}
}

function allowonlyImportCodeFiles(e){

	var id = e.id;
	if(id == '')
		return false;

	var files = $('#'+id)[0].files[0];
	if(files)
	{
		var filename = files.name;
		var fileNameExt = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();
		var validExtensions = ['xlsx']; //array of valid extensions
		if ($.inArray(fileNameExt, validExtensions) == -1)
		{
		   alert("Invalid file type");
		   $("#"+id).val('');
		   return false;
		}
	}
}

$(function () {


	$('.decimal_only').keypress(function(event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});

	$('.number_only').keyup(function () {
	   this.value = this.value.replace(/[^0-9]/g,'');
	});

	$('.phone_number').keyup(function () {
	   this.value = this.value.replace(/[^0-9+-]/g,'');
	});

});




$(document).ready(function($){


    document.addEventListener('fullscreenchange', exitHandler);
    document.addEventListener('webkitfullscreenchange', exitHandler);
    document.addEventListener('mozfullscreenchange', exitHandler);
    document.addEventListener('MSFullscreenChange', exitHandler);

    function exitHandler() {
        if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {

            $("#defaultScreenId").hide();
            $("#fullScreenId").show();

        }
    }


	$.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	$('body').on('click', '.profile', function () {
        $("form#changePasswordForm")[0].reset();
        $('#profile_user_nameError,#current_passwordError,#confirm_passwordError,#passwordError,#changePasswodSuccessAlert,#profile_emailError').text('');
        $("#changePasswodSuccessAlert").hide();


        $.ajax({
            type:"POST",
            url: "{{ url('admin/edit-profile') }}",
            dataType: 'json',
            success: function(res){

                $("#profile_password").prop("required",false);

                $('#profile_addEditForm').trigger("reset");

                $('#profile_ajaxheadingModel').html("Update Profile");

                $('#profile_name').val(res.name);

                $('#profile_user_name').val(res.user_name);

                $('#profile_email').val(res.email);

                $('#profile_ajax-model').modal('show');


           }
        });
    });

	$("#profile_addEditForm").on('submit',(function(e) {

		e.preventDefault();

		var Form_Data = new FormData(this);

        $("#profile_btn-save").html('Please Wait...');

        $("#profile_btn-save"). attr("disabled", true);

        $('#profile_user_nameError,#changePasswodSuccessAlert,#profile_emailError').text('');
        $("#changePasswodSuccessAlert").hide();


        $.ajax({
            type:"POST",
            url: "{{ url('admin/update-profile') }}",
            data: Form_Data,
			mimeType: "multipart/form-data",
		    contentType: false,
		    cache: false,
		    processData: false,
            dataType: 'json',
            success: function(res){

				$("#profile_btn-save").html('<i class="fa fa-save"></i> Update');
				$("#profile_btn-save"). attr("disabled", false);

                $('#profile_ajax-model').modal('hide');
                if($("#profile_image").val()){
                    $("#UserProfileImg").attr('src',res.profile_image)
                }
                if($("#profile_name").val()){
                    $("#UserProfileName").text($("#profile_name").val());
                }



           },
		   error:function (response) {

				$("#profile_btn-save").html('<i class="fa fa-save"></i> Update');
				$("#profile_btn-save"). attr("disabled", false);
				$('#profile_user_nameError').text(response.responseJSON.errors.user_name);
				$('#profile_emailError').text(response.responseJSON.errors.email);


			}
        });
    }));

	$("#changePasswordForm").on('submit',(function(e) {
        e.preventDefault();

        $("#passwordError,#confirm_passwordError,#changePasswodSuccessAlert,#current_passwordError").text('');
        $("#changePasswodSuccessAlert").hide();

		var Form_Data = new FormData(this);


        var current_password = $("#current_password").val();
        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();

        if(password != confirm_password ){

            $("#confirm_passwordError").text('Password & Confirm password does not match!')
            return false;
        }

        if(password == current_password ){

            $("#passwordError").text('New password & current password must be different!')
            return false;
        }


        $("#changePasswordSubmit").html('Please Wait...');
        $("#changePasswordSubmit"). attr("disabled", true);

        $.ajax({
            type:"POST",
            url: "{{ url('admin/update-password') }}",
            data: Form_Data,
			mimeType: "multipart/form-data",
		    contentType: false,
		    cache: false,
		    processData: false,
            dataType: 'json',
            success: function(res){

				$("#changePasswordSubmit").html('<i class="fa fa-save"></i> Update');
				$("#changePasswordSubmit"). attr("disabled", false);

                $("#changePasswodSuccessAlert").show();
                $("#changePasswodSuccessAlert").text('Password has been changed successfully!');

                $("#changePasswordForm")[0].reset();
                $('#profile_ajax-model').modal('hide');

                window.location = "{{url('/login')}}";x
            },
		   error:function (response) {

				$("#changePasswordSubmit").html('<i class="fa fa-save"></i> Update');
				$("#changePasswordSubmit"). attr("disabled", false);

				$('#current_passwordError').text(response.responseJSON.errors.current_password);


			}
        });
    }));

    $("#fullScreenId").click(function(){

        $(this).hide();
        $("#defaultScreenId").show();

        var elem = document.getElementById("customBody");

        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) { /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE11 */
            elem.msRequestFullscreen();
        }

    });

    $("#defaultScreenId").click(function(){

        document.exitFullscreen();

        $(this).hide();
        $("#fullScreenId").show();

    });

    $('.disableSpecialCharacters').keypress(function(e) {
        e = e || event;
        var s = String.fromCharCode(e.charCode);
        if(!s.match(/^[a-zA-Z0-9@.]+$/)) return false;

    });

    /*********** DELET ALL FUNCTIONALITY ***************/

    $('#master').on('click', function(e) {


        if($(this).is(':checked',true))
        {
            $(".sub_chk").prop('checked', true);
        } else {
            $(".sub_chk").prop('checked',false);
        }
    });


    $('.delete_all').on('click', function(e) {

        var allVals = [];

        var module = $(this).attr('data-module');

        if(module == 'schedules'){

            var activeTabId = $("#custom-content-above-tabContent .tab-pane.active").attr("id");

            if(activeTabId == "upcoming-schedules"){

                var tableId = "DataTbl";
            }
            else if(activeTabId == "previous-schedules"){
                var tableId = "previous-DataTbl";
            }
            else {
                var tableId = "live-DataTbl";
            }

            $("#"+tableId+" tbody td .sub_chk:checked").each(function() {
                allVals.push($(this).attr('data-id'));
            });
        }
        else{

            $(".sub_chk:checked").each(function() {
                allVals.push($(this).attr('data-id'));
            });
        }

        if(allVals.length <=0)
        {
            alert("Please select row.");
        }
        else {

            var check = confirm("Are you sure you want to delete this row?");
            if(check == true){

                var join_selected_values = allVals.join(",");

                $.ajax({
                    url: $(this).data('url'),
                    type: 'DELETE',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: 'ids='+join_selected_values,
                    success: function (data) {
                        if (data['success']) {
                            $(".sub_chk:checked").each(function() {
                                $(this).parents("tr").remove();
                            });

                            alert(data['success']);

                            $(".master,#master").prop('checked',false);

                            if(module == "AppDetails" || module == "AppSettings" ){
                                loadApplicationsCardView();
                            }
                            else{
                                // callDataTableWithFilters();

                                var dataTablePageInfo = Table_obj.page.info();
                                Table_obj.page(dataTablePageInfo.page).draw('page');
                            }

                        } else if (data['error']) {
                            alert(data['error']);
                        } else {
                            alert('Whoops Something went wrong!!');
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });


                $.each(allVals, function( index, value ) {
                    $('table tr').filter("[data-row-id='" + value + "']").remove();
                });
            }
        }
    });




    $("button.digit_numbers").on("click", function() {

        var $button = $(this);
        var OldValue = $button.parent().find("input").attr('data-currentValue');
        var value = $button.parent().find("input").val()

        var finalValue = 0;
        var newVal = 0;

        if ($button.text() === "+") {

            newVal = parseInt(value) + 1;

            finalValue = newVal;
        }
        else {

            if(value > 0){
                newVal = parseInt(value) - 1;
                finalValue = newVal;
            }
            else{
                finalValue = value;
            }

        }

        $button.parent().find("input").val(finalValue);

    });



});

    function convertLowerCase(str,id){
        let loginUserName = str.toLowerCase();
        $("#"+id).val(loginUserName);
    }


    function TriggerSyncTypeDropdown(value){
        if(value){
            $("#app_detail_id").removeAttr('disabled');
            TriggerApplicationDropdown($("#app_detail_id").val());
        }
        else{
            $("#app_detail_id").val('');
            $("#app_detail_id").attr('disabled','disabled')
        }
    }

    function TriggerApplicationDropdown(applicationID){

        $(".Sports-Block,.Leagues-Block,.Schedules-Block").hide();
        $("#leagues_id,#schedules_id").attr('disabled','disabled');
        $("#sports_id").val('');


        if(applicationID){
            var sports_id = $("#app_detail_id").find(':selected').attr('data-sports_id')
            $("#sports_id").val(sports_id);

        }
    }

    function showHideBtnSpinner(btnId,btnText,btnAddClass,btnRemoveClass,btnEnabledDisable){
        $("#"+btnId+" > span").text(btnText);
        $("#"+btnId+" > i").removeClass(btnRemoveClass);
        $("#"+btnId+" > i").addClass(btnAddClass);
        $("#"+btnId).attr("disabled",btnEnabledDisable);
    }

    function getApplicationListOptionsBySports(sports_id,app_filter_id){

        $.ajax({
            type:"POST",
            url: "{{ url('admin/sync/app-list-options') }}",
            data: { sports_id: sports_id},
            success: function(response){
                $("#"+app_filter_id).html(response);
            }
        });

    }



</script>


	@stack('scripts')

	 <!-- boostrap model -->
    <div class="modal fade" id="profile_ajax-model" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="profile_ajaxheadingModel"></h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          </div>
          <div class="modal-body">

              <ul class="nav nav-tabs profile-nav-tabs" id="custom-content-above-tab" role="tablist">
                  <li class="nav-item">
                      <a class="nav-link active" id="custom-content-above-home-tab" data-toggle="pill" href="#custom-content-above-profile" role="tab" aria-controls="custom-content-above-profile" aria-selected="true">Profile</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" id="custom-content-above-changepassword-tab" data-toggle="pill" href="#custom-content-above-changepassword" role="tab" aria-controls="custom-content-above-changepassword" aria-selected="false">Change Password</a>
                  </li>
              </ul>


              <div class="tab-content" id="custom-content-above-tabContent">
                  <div class="tab-pane fade show active" id="custom-content-above-profile" role="tabpanel" aria-labelledby="custom-content-above-home-tab">

                      <!----==== BEGIN ::  Update Profile Form ====---->

                      <form action="javascript:void(0)" id="profile_addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                          <div class="form-group row">
                              <div class="col-sm-12 mb-3 mt-3">
                                  <label for="name" class="control-label">Name</label>
                                  <input type="text" class="form-control" id="profile_name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                              </div>
                              <div class="col-sm-12">
                                  <label for="name" class="control-label">User Name</label>
                                  <input type="text" class="form-control" id="profile_user_name" disabled name="user_name" placeholder="Enter User Name" value="" maxlength="50" required="">
                                  <span class="text-danger" id="profile_user_nameError"></span>
                              </div>
                          </div>
                          <div class="form-group row">
                              <div class="col-sm-12 mb-3">
                                  <label for="name" class="control-label">Email</label>
                                  <input type="email" disabled class="form-control" id="profile_email" name="email" placeholder="Enter Email" value="" maxlength="50" required="">
                                  <span class="text-danger" id="profile_emailError"></span>
                              </div>
                          </div>

                          <div class="form-group row">
                              <div class="col-sm-12">
                                  <label for="name" class="control-label">Upload Image</label>
                              </div>
                              <div class="col-sm-12">
                                  <input type="file" class="" id="profile_image" name="profile_image"  onchange="allowonlyImg(this)" />
                              </div>
                          </div>

                          <div class="col-sm-offset-2 col-sm-12 text-right">
                              <button type="submit" class="btn btn-dark" id="profile_btn-save" >
                                  <i class="fa fa-save"></i>&nbsp; Update
                              </button>
                          </div>
                      </form>

                      <!----==== END ::   Update Profile Form ====---->

                  </div>
                  <div class="tab-pane fade" id="custom-content-above-changepassword" role="tabpanel" aria-labelledby="custom-content-above-changepassword-tab">

                      <!----==== BEGIN ::  Update Profile Form ====---->

                      <form action="javascript:void(0)" id="changePasswordForm" name="changePasswordForm" class="form-horizontal" method="POST">

                          <p class="alert alert-success mt-2 mb-0" id="changePasswodSuccessAlert"></p>


                          <div class="form-group row">
                              <div class="col-sm-12 mb-3 mt-3">
                                  <label for="current_password" class="control-label">Current Password</label>
                                  <input type="password" class="form-control" autocomplete="off"  id="current_password" name="current_password" placeholder="Enter Password"  required="">
                                  <span class="text-danger" id="current_passwordError"></span>
                              </div>
                              <div class="col-sm-12">
                                  <label for="password" class="control-label">New Password</label>
                                  <input type="password" class="form-control" autocomplete="off" id="password" name="password" placeholder="Enter New Password"  required="">
                                  <span class="text-danger" id="passwordError"></span>
                              </div>
                          </div>
                          <div class="form-group row">
                              <div class="col-sm-12 mb-3">
                                  <label for="confirm_password" class="control-label">New Password</label>
                                  <input type="password" class="form-control"  autocomplete="off" id="confirm_password" name="" placeholder="Enter Confirm Password"  required="">
                                  <span class="text-danger" id="confirm_passwordError"></span>
                              </div>
                          </div>

                          <div class="col-sm-offset-2 col-sm-12 text-right">
                              <input type="hidden" name="user_id" value="{{\Illuminate\Support\Facades\Auth::user()->id}}" readonly>
                              <button type="submit" class="btn btn-dark" id="changePasswordSubmit">
                                  <i class="fa fa-save"></i>&nbsp; Update
                              </button>
                          </div>
                      </form>

                      <!----==== END ::   Update Profile Form ====---->




                  </div>
              </div>




          </div>

        </div>
      </div>
    </div>
<!-- end bootstrap model -->
