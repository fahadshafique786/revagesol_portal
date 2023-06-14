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
                                                <option value="{{$obj->id}}" {{($obj->id == $appData->account_id) ? 'selected' : '' }}>   {{   $obj->name }}    </option>
                                            @endforeach
                                        </select>


                                     </div>


                                    <label for="staticEmail" class="col-sm-2 col-form-label" id="PackageIdLabel">packageId</label>

                                    <div class="col-sm-4">
                                        <input type="text"  class="form-control" name="packageId" id="packageId" value="{{$appData->packageId}}" onkeyup="$('#PackageIdError').text('')"   required />
                                        <span class="text-danger" id="PackageIdError"></span>
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">App Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="appName" id="appName" value="{{$appData->appName}}" required>
                                    </div>



                                    <label for="staticEmail" class="col-sm-2 col-form-label">App Logo</label>
                                    <div class="col-sm-4">
                                        <input type="file" class="" name="appLogo" id="appLogo" value="{{$appData->appLogo}}"  {{ (!$appData->appLogo) ? 'required' : '' }} onchange="allowonlyImg(this);if(this.value !='');" >

                                    </div>
                                </div>


                                <div class="form-group row">

                                    <label for="pagesUrl" class="col-sm-2 col-form-label">Pages URL</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="pagesUrl" id="pagesUrl" value="{{$appData->pagesUrl}}" required >
                                    </div>

                                    <label for="pagesCounter" class="col-sm-2 col-form-label">Pages Counter</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="pagesCounter" id="pagesCounter" value="{{$appData->pagesCounter}}" required>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">adsIntervalTime</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="adsIntervalTime" id="adsIntervalTime" value="{{$appData->adsIntervalTime}}" required>
                                    </div>


                                    <label for="adsIntervalCount" class="col-sm-2 col-form-label">adsIntervalCount</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="adsIntervalCount" id="adsIntervalCount" value="{{$appData->adsIntervalCount}}" required>
                                    </div>

{{--                                    <label for="staticEmail" class="col-sm-2 col-form-label">checkIpAddressApiUrl</label>--}}
{{--                                    <div class="col-sm-4">--}}
{{--                                        <input type="text" class="form-control" name="checkIpAddressApiUrl" id="checkIpAddressApiUrl" value="{{$appData->checkIpAddressApiUrl}}" required>--}}
{{--                                    </div>--}}

                                </div>

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">newAppPackage</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="newAppPackage" id="newAppPackage" value="{{$appData->newAppPackage}}" required>
                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">ourAppPackage</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="ourAppPackage" id="ourAppPackage" value="{{$appData->ourAppPackage}}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">startAppId</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="startAppId" id="startAppId" value="{{$appData->startAppId}}" required>
                                    </div>




                                    <label for="staticEmail" class="col-sm-2 col-form-label">admobAppId</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="admobAppId" id="admobAppId" value="{{$appData->admobAppId}}" required>
                                    </div>

                                </div>


                                <div class="form-group row">

                                    <label for="appOpenIntervalHour" class="col-sm-2 col-form-label">appOpenIntervalHour</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="appOpenIntervalHour" id="appOpenIntervalHour" value="{{($appData->appOpenIntervalHour) ? $appData->appOpenIntervalHour : '3'}}" required>
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="pagesExtension" class="col-sm-2 col-form-label">isPagesExtension</label>
                                    <div class="col-sm-4">
                                        <label for="pagesExtension1" class="cursor-pointer">
                                            <input type="radio" class="" id="pagesExtension1" name="pagesExtension" value="jpg"   {{($appData->pagesExtension == 'jpg') ? 'checked' : ''}} />
                                            <span class="">JPG</span>
                                        </label>

                                        <label for="pagesExtension0" class="cursor-pointer">
                                            <input type="radio" class="" id="pagesExtension0" name="pagesExtension" value="png"   {{($appData->pagesExtension == 'png') ? 'checked' : ''}} />
                                            <span class="">PNG</span>
                                        </label>

                                    </div>


                                    <label for="isOnlineCode" class="col-sm-2 col-form-label">isOnlineCode</label>
                                    <div class="col-sm-4">
                                        <label for="isOnlineCode1" class="cursor-pointer">
                                            <input type="radio" class="" id="isOnlineCode1" name="isOnlineCode" value="1"  {{($appData->isOnlineCode) ? 'checked' : ''}} />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isOnlineCode0" class="cursor-pointer">
                                            <input type="radio" class="" id="isOnlineCode0" name="isOnlineCode" value="0" {{(!$appData->isOnlineCode) ? 'checked' : ''}} />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="isPagesAlgo" class="col-sm-2 col-form-label">isPagesAlgo</label>
                                    <div class="col-sm-4">
                                        <label for="isPagesAlgo1" class="cursor-pointer">
                                            <input type="radio" class="" id="isPagesAlgo1" name="isPagesAlgo" value="1"   {{($appData->isPagesAlgo) ? 'checked' : ''}} />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isPagesAlgo0" class="cursor-pointer">
                                            <input type="radio" class="" id="isPagesAlgo0" name="isPagesAlgo" value="0" {{(!$appData->isPagesAlgo) ? 'checked' : ''}} />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isAdmobAdsShow</label>
                                    <div class="col-sm-4">
                                        <label for="isAdmobAdsShow1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAdmobAdsShow1" name="isAdmobAdsShow" value="1"  {{($appData->isAdmobAdsShow) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAdmobAdsShow0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAdmobAdsShow0" name="isAdmobAdsShow" value="0" {{(!$appData->isAdmobAdsShow) ? 'checked' : ''}} />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isBannerPlayer</label>
                                    <div class="col-sm-4">

                                        <label for="isBannerPlayer1" class="cursor-pointer">
                                            <input type="radio" class="" id="isBannerPlayer1" name="isBannerPlayer" value="1"   {{($appData->isBannerPlayer) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isBannerPlayer0" class="cursor-pointer">
                                            <input type="radio" class="" id="isBannerPlayer0" name="isBannerPlayer" value="0"  {{(!$appData->isBannerPlayer) ? 'checked' : ''}}  />
                                            <span class="">No</span>
                                        </label>

                                    </div>


                                    <label for="isScreenAdsLimit" class="col-sm-2 col-form-label">isScreenAdsLimit</label>
                                    <div class="col-sm-4">
                                        <label for="isScreenAdsLimit1" class="cursor-pointer">
                                            <input type="radio" class="" id="isScreenAdsLimit1" name="isScreenAdsLimit" value="1"  {{($appData->isScreenAdsLimit) ? 'checked' : ''}} />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isScreenAdsLimit0" class="cursor-pointer">
                                            <input type="radio" class="" id="isScreenAdsLimit0" name="isScreenAdsLimit" value="0" {{(!$appData->isScreenAdsLimit) ? 'checked' : ''}} />
                                            <span class="">No</span>
                                        </label>

                                    </div>


                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isAdsInterval</label>
                                    
                                    <div class="col-sm-4">

                                        <label for="isAdsInterval1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAdsInterval1" name="isAdsInterval" value="1"  {{($appData->isAdsInterval) ? 'checked' : ''}}   />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAdsInterval0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAdsInterval0" name="isAdsInterval" value="0"  {{(!$appData->isAdsInterval) ? 'checked' : ''}}  />
                                            <span class="">No</span>
                                        </label>

                                    </div>


                                    <label for="staticEmail" class="col-sm-2 col-form-label">isMessageDialogDismiss</label>

                                    <div class="col-sm-4">

                                        <label for="isMessageDialogDismiss1" class="cursor-pointer">
                                            <input type="radio" class="" id="isMessageDialogDismiss1" name="isMessageDialogDismiss" value="1"  {{($appData->isMessageDialogDismiss) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isMessageDialogDismiss1" class="cursor-pointer">
                                            <input type="radio" class="" id="isMessageDialogDismiss0" name="isMessageDialogDismiss" value="0"  {{(!$appData->isMessageDialogDismiss) ? 'checked' : ''}}  />
                                            <span class="">No</span>
                                        </label>

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">isSponsorAdsShow</label>
                                    <div class="col-sm-4">


                                        <label for="isSponsorAdsShow1" class="cursor-pointer">
                                            <input type="radio" class="" id="isSponsorAdsShow1" name="isSponsorAdsShow" value="1"   {{($appData->isSponsorAdsShow) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isSponsorAdsShow0" class="cursor-pointer">
                                            <input type="radio" class="" id="isSponsorAdsShow0" name="isSponsorAdsShow" value="0"  {{(!$appData->isSponsorAdsShow) ? 'checked' : ''}}  />
                                            <span class="">No</span>
                                        </label>


                                    </div>


                                    <label for="staticEmail" class="col-sm-2 col-form-label">isStartAppAdsShow</label>
                                    <div class="col-sm-4">

                                        <label for="isStartAppAdsShow1" class="cursor-pointer">
                                            <input type="radio" class="" id="isStartAppAdsShow1" name="isStartAppAdsShow" value="1"   {{($appData->isStartAppAdsShow) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isStartAppAdsShow0" class="cursor-pointer">
                                            <input type="radio" class="" id="isStartAppAdsShow0" name="isStartAppAdsShow" value="0"  {{(!$appData->isStartAppAdsShow) ? 'checked' : ''}}  />
                                            <span class="">No</span>
                                        </label>


                                    </div>
                                </div>

                                <div class="form-group row">

{{--                                    <label for="staticEmail" class="col-sm-2 col-form-label">isStartAppOnline</label>--}}
{{--                                    <div class="col-sm-4">--}}


{{--                                        <label for="isStartAppOnline1" class="cursor-pointer">--}}
{{--                                            <input type="radio" class="" id="isStartAppOnline1" name="isStartAppOnline" value="1"  {{($appData->isStartAppOnline) ? 'checked' : ''}}/>--}}
{{--                                            <span class="">Yes</span>--}}
{{--                                        </label>--}}

{{--                                        <label for="isStartAppOnline0" class="cursor-pointer">--}}
{{--                                            <input type="radio" class="" id="isStartAppOnline0" name="isStartAppOnline" value="0" {{(!$appData->isStartAppOnline) ? 'checked' : ''}} />--}}
{{--                                            <span class="">No</span>--}}
{{--                                        </label>--}}

{{--                                    </div>--}}

                                    <label for="staticEmail" class="col-sm-2 col-form-label">isSuspendApp</label>

                                    <div class="col-sm-4">

                                        <label for="suspendApp1" class="cursor-pointer">
                                            <input type="radio" class="" id="suspendApp1" name="isSuspendApp" value="1" {{($appData->isSuspendApp) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="suspendApp0" class="cursor-pointer">
                                            <input type="radio" class="" id="suspendApp0" name="isSuspendApp" value="0" {{(!$appData->isSuspendApp) ? 'checked' : ''}} />
                                            <span class="">No</span>
                                        </label>
                                    </div>

                                </div>

                                <div class="form-group row ">


                                    <label for="staticEmail" class="col-sm-2 col-form-label">suspendAppMessage</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" name="suspendAppMessage" id="suspendAppMessage">{{$appData->suspendAppMessage}}</textarea>
                                    </div>

{{--                                    <label for="staticEmail" class="col-sm-2 col-form-label">minimumVersionSupport</label>--}}
{{--                                    <div class="col-sm-4">--}}
{{--                                        <input type="text" class="form-control" name="minimumVersionSupport" id="minimumVersionSupport" value="{{$appData->minimumVersionSupport}}" />--}}
{{--                                    </div>--}}

                                </div>

                                <div class="form-group row">

                                    <div class="col-sm-12 text-right">
                                        <input type="submit" class="btn bg-dark vertical-bottom" name="submit" id=submitApp"  value="SUBMIT" />
                                        <!-- <button type="submit" class="btn btn-info" id="submitApp"> <i class="fa fa-save"></i> <span class="">  </span> </button> -->
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

    <script type="module">

        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-app.js";
        import { initializeAppCheck , ReCaptchaEnterpriseProvider } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-app-check.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-analytics.js";
        import { getDatabase , set , ref } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-database.js";

        $(document).ready(function($){

            $('#adsIntervalTime,#minimumVersionSupport,#adsIntervalCount').on('keypress',function (e) {

                var charCode = (e.which) ? e.which : event.keyCode
                if (String.fromCharCode(charCode).match(/[^0-9+.]/g))
                    return false;

            });

            $('#adsIntervalTime,#minimumVersionSupport,#adsIntervalCount').on("cut copy paste",function(e) {
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

                e.preventDefault();

                var Form_Data = new FormData(this);

                if($("#adsIntervalCount").val() <= 0){
                    alert("Ads Interval Count must be greater than zero");
                    return false;
                }

                // $('#cover-spin').show();

                var opt = [];
                opt['btnId']  = "submitApp";
                opt['btnText']  = "SUBMIT";
                opt['btnAddClass']  = "fa fa-save";
                opt['btnRemoveClass']  = "spinner-border spinner-border-sm";

                showHideBtnSpinner("submitApp","Loading...","spinner-border spinner-border-sm","fa fa-save",true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('admin/add-update-apps/'.$application_id) }}",
                    data: Form_Data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {

                        $('#cover-spin').hide();

                        $('#ajax-model').modal('hide');

                        if(res.firebaseConfigJson != "null" && res.firebaseConfigJson != ""  && res.firebaseConfigJson != null) {

                            showHideBtnSpinner("submitApp","Loading...","spinner-border spinner-border-sm","fa fa-save",true);

                            var firebaseConfig = JSON.parse(res.firebaseConfigJson);
                            var app = initializeApp(firebaseConfig);
                            var db = getDatabase(app);

                            var appCheck = initializeAppCheck(app, {
                                provider: new ReCaptchaEnterpriseProvider(res.reCaptchaKeyId),
                                isTokenAutoRefreshEnabled: true // Set to true to allow auto-refresh.
                            });

                            var jsonData = JSON.parse(res.firebaseData);
                            var package_id = res?.packageId;

                            pushDataToRealTimeDatabase(db, res.node, jsonData,opt,package_id);

                        }
                        else{
                            Toast.fire({
                                icon: 'error',
                                title: res?.message
                            });
                            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                        }

                        setTimeout(function(){
                            {{--window.location.href = "{{ url('admin/app')}}";--}}
                        },7000);

                    },
                    error: function (response) {


                        $('#cover-spin').hide();

                        $("input[type=submit]").html('Save');

                        $("input[type=submit]").attr("disabled", false);

                        var resp = response.responseJSON;

                        if(response.status == 422){

                            $('html, body').animate({
                                scrollTop: eval($("#PackageIdError").offset().top - 170)
                            }, 1000);

                            $("#PackageIdError").text(resp.errors.packageId);
                        }
                        else if(response.status == 403){

                            Toast.fire({
                                icon: 'error',
                                title: resp?.message
                            });
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

        function updateAppDetailDBVersion(opt){

            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            var version_accounts_id = $("#account_id").val();
            var versionCategories = "appDetailsDatabaseVersion";
            var versionAppDetailId  = [];
                versionAppDetailId[0] = {{ $application_id }};

            $.ajax({
                type:"POST",
                url: "{{ url('admin/app-settings/update-database-version') }}",
                data: { versionAccountsId : version_accounts_id  , version_categories : versionCategories , version_app_detail_ids : versionAppDetailId},
                success: function (res) {

                    $('#cover-spin').hide();

                    $('#ajax-model').modal('hide');

                    if(res?.data?.success[0].firebaseConfigJson != "null" && res?.data?.success[0].firebaseConfigJson != ""  && res?.data?.success[0].firebaseConfigJson != null) {

                        showHideBtnSpinner("submitApp","Loading...","spinner-border spinner-border-sm","fa fa-save",true);

                        var firebaseConfig = JSON.parse(res?.data?.success[0].firebaseConfigJson);
                        var app = initializeApp(firebaseConfig,res?.data?.success[0]?.appPackageId);
                        var db = getDatabase(app);

                        var appCheck = initializeAppCheck(app, {
                            provider: new ReCaptchaEnterpriseProvider(res?.data?.success[0].reCaptchaKeyId),
                            isTokenAutoRefreshEnabled: true // Set to true to allow auto-refresh.
                        });

                        const jsonData = JSON.parse(res?.data?.success[0].firebaseData);
                        var package_id = res?.packageId;

                        pushDataToRealTimeDatabase(db, res?.data?.success[0].node, jsonData,opt,package_id);

                    }
                    else{
                        Toast.fire({
                            icon: 'error',
                            title: res?.message
                        });
                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                    }

                    setTimeout(function(){
                        {{--window.location.href = "{{ url('admin/app')}}";--}}
                    },7000);

                },
                error: function (response) {


                    $('#cover-spin').hide();

                    $("input[type=submit]").html('Save');

                    $("input[type=submit]").attr("disabled", false);

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
        }

        function pushDataToRealTimeDatabase(db,node,jsonData,opt,package_id) {

            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            switch(node) {
                case 'AppDetails':
                    const AppDetails = jsonData;
                    set(ref(db,'/'+package_id+'/'),AppDetails)
                        .then(() => {

                            Toast.fire({
                                icon:  "success",
                                title: 'Data has been pushed successfully!'
                            })

                            console.log("Success!");
                            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                            updateAppDetailDBVersion(opt)

                        })
                        .catch((error) => {

                            Toast.fire({
                                icon:  "error",
                                title: 'Synchronization Failed due to ' + error
                            })

                            console.log("Failed!");
                            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                            // The write failed...
                        });

                    break;

                case 'AppSettings':
                    const AppSettings = jsonData;
                    set(ref(db,'/'+package_id+'/'),AppSettings)
                        .then(() => {

                            Toast.fire({
                                icon:  "success",
                                title: 'Data has been pushed successfully!'
                            })

                            console.log("Success!");
                            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                        })
                        .catch((error) => {

                            Toast.fire({
                                icon:  "error",
                                title: 'Synchronization Failed due to ' + error
                            })

                            console.log("Failed!");
                            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                            // The write failed...
                        });

                    break;

            }
        }

    </script>

@endpush
