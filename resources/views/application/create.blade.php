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
                                <div class="col-12 text-center">
                                        <h4 class="">Register New Application</h4>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <form action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">


                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Accounts</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="account_id" id="account_id" required>
                                            <option value="">   Select </option>
                                            @foreach($accountsList as $obj)
                                                <option value="{{$obj->id}}" >   {{   $obj->name }}    </option>
                                            @endforeach
                                        </select>


                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label" id="">packageId</label>
                                    <div class="col-sm-4">
                                        <input type="text"  class="form-control" name="packageId" id="packageId" value="" onkeyup="$('#PackageIdError').text('')"  required />
                                        <span class="text-danger" id="PackageIdError"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">App Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="appName" id="appName" value="" required>
                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">App Logo</label>
                                    <div class="col-sm-4">
                                        <input type="file" class="" name="appLogo" id="appLogo" value=""  required onchange="allowonlyImg(this);if(this.value !='');" >

                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label for="pagesUrl" class="col-sm-2 col-form-label">Pages URL</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="pagesUrl" id="pagesUrl" value="" required >
                                    </div>

                                    <label for="pagesCounter" class="col-sm-2 col-form-label">Pages Counter</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="pagesCounter" id="pagesCounter" value="" required>
                                    </div>


                                </div>


                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">adsIntervalTime</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="adsIntervalTime" id="adsIntervalTime" value="" required>
                                    </div>

                                    <label for="adsIntervalCount" class="col-sm-2 col-form-label">adsIntervalCount</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="adsIntervalCount" id="adsIntervalCount" value="" required>
                                    </div>

{{--                                    <label for="staticEmail" class="col-sm-2 col-form-label d-none">checkIpAddressApiUrl</label>--}}
{{--                                    <div class="col-sm-4 d-none">--}}
{{--                                        <input type="text" class="form-control" name="checkIpAddressApiUrl" id="checkIpAddressApiUrl" value="" required>--}}
{{--                                    </div>--}}
                                </div>

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">newAppPackage</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="newAppPackage" id="newAppPackage" value="" required>
                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">ourAppPackage</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="ourAppPackage" id="ourAppPackage" value="" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">startAppId</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="startAppId" id="startAppId" value="" required>
                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">admobAppId</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="admobAppId" id="admobAppId" value="" required>
                                    </div>


                                </div>


                                <div class="form-group row">

                                    <label for="appOpenIntervalHour" class="col-sm-2 col-form-label">appOpenIntervalHour</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="appOpenIntervalHour" id="appOpenIntervalHour" value="3" required>
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="pagesExtension" class="col-sm-2 col-form-label">Pages Extension</label>
                                    <div class="col-sm-4">
                                        <label for="pagesExtension1" class="cursor-pointer">
                                            <input type="radio" class="" id="pagesExtension1" name="pagesExtension" value="jpg"  checked />
                                            <span class="">JPG</span>
                                        </label>

                                        <label for="pagesExtension0" class="cursor-pointer">
                                            <input type="radio" class="" id="pagesExtension0" name="pagesExtension" value="png"  />
                                            <span class="">PNG</span>
                                        </label>

                                    </div>


                                    <label for="onlineCode" class="col-sm-2 col-form-label">isOnlineCode</label>
                                    <div class="col-sm-4">
                                        <label for="onlineCode1" class="cursor-pointer">
                                            <input type="radio" class="" id="onlineCode1" name="onlineCode" value="1"   />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="onlineCode0" class="cursor-pointer">
                                            <input type="radio" class="" id="onlineCode0" name="onlineCode" value="0" checked />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="pagesAlgo" class="col-sm-2 col-form-label">isPagesAlgo</label>
                                    <div class="col-sm-4">
                                        <label for="pagesAlgo1" class="cursor-pointer">
                                            <input type="radio" class="" id="pagesAlgo1" name="pagesAlgo" value="1"  checked />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="pagesAlgo0" class="cursor-pointer">
                                            <input type="radio" class="" id="pagesAlgo0" name="pagesAlgo" value="0"  />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isAdmobAdsShow</label>
                                    <div class="col-sm-4">
                                        <label for="isAdmobAdsShow1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAdmobAdsShow1" name="isAdmobAdsShow" value="1"  checked />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAdmobAdsShow0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAdmobAdsShow0" name="isAdmobAdsShow" value="0"  />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="isScreenAdsLimit" class="col-sm-2 col-form-label">isScreenAdsLimit</label>
                                    <div class="col-sm-4">
                                        <label for="isScreenAdsLimit1" class="cursor-pointer">
                                            <input type="radio" class="" id="isScreenAdsLimit1" name="isScreenAdsLimit" value="1" checked />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isScreenAdsLimit0" class="cursor-pointer">
                                            <input type="radio" class="" id="isScreenAdsLimit0" name="isScreenAdsLimit" value="0"  />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isAdsInterval</label>
                                    <div class="col-sm-4">

                                        <label for="isAdsInterval1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAdsInterval1" name="isAdsInterval" value="1"  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAdsInterval0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAdsInterval0" name="isAdsInterval" value="0" checked />
                                            <span class="">No</span>
                                        </label>

                                    </div>


                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isMessageDialogDismiss</label>
                                    <div class="col-sm-4">



                                        <label for="isMessageDialogDismiss1" class="cursor-pointer">
                                            <input type="radio" class="" id="isMessageDialogDismiss1" name="isMessageDialogDismiss" value="1"  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isMessageDialogDismiss1" class="cursor-pointer">
                                            <input type="radio" class="" id="isMessageDialogDismiss0" name="isMessageDialogDismiss" value="0" checked />
                                            <span class="">No</span>
                                        </label>

                                    </div>




                                    <label for="staticEmail" class="col-sm-2 col-form-label">isStartAppAdsShow</label>
                                    <div class="col-sm-4">

                                        <label for="isStartAppAdsShow1" class="cursor-pointer">
                                            <input type="radio" class="" id="isStartAppAdsShow1" name="isStartAppAdsShow" value="1"  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isStartAppAdsShow0" class="cursor-pointer">
                                            <input type="radio" class="" id="isStartAppAdsShow0" name="isStartAppAdsShow" value="0" checked />
                                            <span class="">No</span>
                                        </label>

                                    </div>
                                    


                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isSponsorAdsShow</label>
                                    <div class="col-sm-4">

                                        <label for="isSponsorAdsShow1" class="cursor-pointer">
                                            <input type="radio" class="" id="isSponsorAdsShow1" name="isSponsorAdsShow" value="1"  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isSponsorAdsShow0" class="cursor-pointer">
                                            <input type="radio" class="" id="isSponsorAdsShow0" name="isSponsorAdsShow" value="0" checked />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isSuspendApp</label>

                                    <div class="col-sm-4">

                                        <label for="suspendApp1" class="cursor-pointer">
                                            <input type="radio" class="" id="suspendApp1" name="isSuspendApp" value="1"  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="suspendApp0" class="cursor-pointer">
                                            <input type="radio" class="" id="suspendApp0" name="isSuspendApp" value="0" checked />
                                            <span class="">No</span>
                                        </label>
                                    </div>


                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">suspendAppMessage</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" name="suspendAppMessage" id="suspendAppMessage"></textarea>
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <div class="col-sm-12 text-right">
                                        <button class="btn btn-info vertical-bottom" name="submit" id="submitApp"> <i class="fa fa-save"></i> &nbsp; <span> SUBMIT </span> </button>
                                    </div>

                                </div>

                            </form>



                        </div>
                    </div>
                </div>

            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->


    </section>
    <!-- /.content -->


@endsection

@push('scripts')
    <script type="text/javascript">

        $(document).ready(function($){

            $('#adsIntervalTime,#minimumVersionSupport,#adsIntervalCount,#appOpenIntervalTime,#pagesCounter').on('keypress',function (e) {

                var charCode = (e.which) ? e.which : event.keyCode
                if (String.fromCharCode(charCode).match(/[^0-9+.]/g))
                    return false;

            });

            $('#adsIntervalTime,#minimumVersionSupport,#adsIntervalCount,#appOpenIntervalTime,#pagesCounter').on("cut copy paste",function(e) {
                e.preventDefault();
            });


            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            $("#addEditForm").on('submit',(function(e) {

                // $('#contentContainer').html('<div id=\'cover-spin\'></div>');

                e.preventDefault();

                var Form_Data = new FormData(this);

                if($("#adsIntervalCount").val() <= 0){
                    alert("Ads Interval Count must be greater than zero");
                    return false;
                }

                var opt = [];
                opt['btnId']  = "submitApp";
                opt['btnText']  = "SUBMIT";
                opt['btnAddClass']  = "fa fa-save";
                opt['btnRemoveClass']  = "spinner-border spinner-border-sm";

                showHideBtnSpinner("submitApp","Loading...","spinner-border spinner-border-sm","fa fa-save",true);

                $("#PackageIdError").text(' ');

                $.ajax({
                    type: "POST",
                    url: "{{ url('admin/add-update-apps') }}",
                    data: Form_Data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {

                        $('#ajax-model').modal('hide');

                        Toast.fire({
                            icon: 'success',
                            title: 'Application has been registered successfully!'
                        })

                        $("form#addEditForm")[0].reset();

                        setTimeout(function(){
                            window.location.href = "{{ url('admin/app/')}}";
                        },550);

                        // $('#cover-spin').hide();
                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                    },
                    error: function (response) {

                        // $('#cover-spin').hide();

                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                        // $("input[type=submit]").html('Save');
                        // $("input[type=submit]").attr("disabled", false);

                        var resp = response.responseJSON;

                        if(response.status == 422){

                            $('html, body').animate({
                                scrollTop: eval($("#PackageIdError").offset().top - 170)
                            }, 1000);


                            $("#PackageIdError").text(resp.errors.packageId);
                        }
                        else{
                            Toast.fire({
                                icon: 'error',
                                title: 'Network Error!'
                            })

                        }
                    }
                });

            }));
        });

    </script>

@endpush
