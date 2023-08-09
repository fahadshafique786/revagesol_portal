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
                                        <h4 class="">Add New App Setting</h4>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">

                            <form autocomplete="off" action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Accounts</label>
                                    <div class="col-sm-4">

                                        <select class="form-control" id="account_id" name="account_id" {{ (isset($appSettingId) && ($appSettingId)) ? 'disabled' : '' }}
                                                required onchange="getRemainingAppsOptionByAccounts(this.value,'app_detail_id')"  >
                                            <option value="" selected>   Select Accounts </option>
                                            @foreach ($accountsList as $obj)
                                                <option value="{{ $obj->id }}"  {{ (isset($obj->id) && (isset($accountsId)) && ($obj->id == $accountsId)) ? "selected":"" }}>{{ $obj->name }}</option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <label for="sslSha256Key" class="col-sm-2 col-form-label">sslSha256Key</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="sslSha256Key" id="sslSha256Key" value="{{(isset($appData->sslSha256Key) && ($appData->sslSha256Key)) ? $appData->sslSha256Key : "" }}" required>
                                        <span class="text-danger" id="sslSha256KeyError"></span>
                                    </div>


                                </div>

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Application</label>
                                    <div class="col-sm-4">
{{--                                        <select class="form-control" name="app_detail_id" id="app_detail_id" required {{ (!empty($appsList)) ? '' : 'disabled'  }}>--}}
                                        <select class="form-control"  {{ (isset($appSettingId) && ($appSettingId)) ? 'disabled' : '' }} name="app_detail_id" id="app_detail_id" required >
                                            <option value="">   Select App </option>
                                            @php
                                                $appSettingAppId = 0;

                                                $appSettingId = (isset($appSettingId) && ($appSettingId)) ? $appSettingId : 0;

                                                if(isset($appData->app_detail_id))
                                                    $appSettingAppId = $appData->app_detail_id;

                                            @endphp

                                            @if(count($appsList) > 0)
                                            @foreach($appsList as $obj)

                                                <option value="{{$obj->id}}" {{($obj->id == $appSettingAppId) ? 'selected' : '' }} >   {{  $obj->appName . ' - '. $obj->packageId  }}    </option>
                                            @endforeach
                                            @else
                                            @endif
                                        </select>

                                        <span class="text-danger" id="app_detail_idError"></span>
                                        <input type="hidden" readonly id="appSettingId" name="appSettingId"  value="{{$appSettingId}}"/>
                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Auth Helper Key</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="authHelperKey" id="authHelperKey" value="{{(isset($appData->authHelperKey) && ($appData->authHelperKey)) ? $appData->authHelperKey : '' }}" required>
                                    </div>

                                </div>

                                <div class="form-group row">


                                    <label for="serverAuthKey1" class="col-sm-2 col-form-label">Server Auth Key 1</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="serverAuthKey1" id="serverAuthKey1" value="{{(isset($appData->serverAuthKey1) && ($appData->serverAuthKey1)) ? $appData->serverAuthKey1 : '' }}" required />
                                    </div>


                                    <label for="serverAuthKey2" class="col-sm-2 col-form-label">Server Auth Key 2</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="serverAuthKey2" id="serverAuthKey2" value="{{(isset($appData->serverAuthKey2) && ($appData->serverAuthKey2)) ? $appData->serverAuthKey2 : '' }}" required/>
                                    </div>

                                </div>


                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Server Api Base Url</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="serverApiBaseUrl" id="serverApiBaseUrl" value="{{(isset($appData->serverApiBaseUrl) && ($appData->serverApiBaseUrl)) ? $appData->serverApiBaseUrl : '' }}" required>
                                    </div>

                                    <label for="checkIpAddressApiUrl" class="col-sm-2 col-form-label mt-1">Check Ip Address Api Url</label>
                                    <div class="col-sm-4 mt-2">
                                        <input type="text" class="form-control" name="checkIpAddressApiUrl" id="checkIpAddressApiUrl" value="{{(isset($appData->checkIpAddressApiUrl) && ($appData->checkIpAddressApiUrl)) ? $appData->checkIpAddressApiUrl : '' }}" required>
                                    </div>


                                </div>

                                <div class="form-group row dbversion">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is App Details Database Save</label>
                                    <div class="col-sm-4 pt-2">

                                        <label for="isAppDetailsDatabaseSave1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppDetailsDatabaseSave1" name="isAppDetailsDatabaseSave" value="1"  {{((isset($appData->isAppDetailsDatabaseSave) && $appData->isAppDetailsDatabaseSave) || ($appSettingId || !$appSettingId)) ? 'checked' : ""  }}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppDetailsDatabaseSave0" class=" cursor-pointer">
                                            <input type="radio" class="" id="isAppDetailsDatabaseSave0" name="isAppDetailsDatabaseSave" value="0"  {{(isset($appData->isAppDetailsDatabaseSave) && !$appData->isAppDetailsDatabaseSave) ? 'checked' : "" }}  />
                                            <span class="">No</span>
                                        </label>

                                    </div>


                                    <label for="staticEmail" class="col-sm-2 col-form-label">App Details Database Save Version</label>
                                    <div class="col-sm-4">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <input type="text"
                                                       readonly="readonly"
                                                       maxlength="3"
                                                       data-currentValue="{{(isset($appData->appDetailsDatabaseVersion) && ($appData->appDetailsDatabaseVersion)) ? $appData->appDetailsDatabaseVersion : '1.1' }}"
                                                       class="form-control w-50 d-inline-block notAllowedAlphabets versionControlInput"
                                                       name="appDetailsDatabaseVersion" id="appDetailsDatabaseVersion"
                                                       value="{{(isset($appData->appDetailsDatabaseVersion) && ($appData->appDetailsDatabaseVersion)) ? $appData->appDetailsDatabaseVersion : '1.1' }}"
                                                       required />

                                                <button type="button" class="plus numbers" >+</button>
                                                <button type="button" class="minus numbers" >-</button>

                                            </div>



                                        </div>

                                    </div>

                                </div>


                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is App Clear Cache</label>
                                    <div class="col-sm-4 pt-2">
                                        <label for="isAppClearCache1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppClearCache1" name="isAppClearCache" value="1" {{(isset($appData->isAppClearCache) && ($appData->isAppClearCache)) ? 'checked' : "" }}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppClearCache0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppClearCache0" name="isAppClearCache" value="0"  {{((isset($appData->isAppClearCache) && !$appData->isAppClearCache) || (!$appSettingId)) ? 'checked' : "" }}   />
                                            <span class="">No</span>
                                        </label>

                                    </div>



                                    <label for="appCacheId" class="col-sm-2 col-form-label">App Cache Id</label>
                                    <div class="col-sm-4">
                                        <input type="text"
                                               readonly="readonly"
                                               maxlength="3"
                                               data-currentValue="{{(isset($appData->appCacheId) && ($appData->appCacheId)) ? $appData->appCacheId : '1.1' }}"
                                               class="form-control w-50 d-inline-block notAllowedAlphabets versionControlInput"
                                               name="appCacheId" id="appCacheId"
                                               value="{{(isset($appData->appCacheId) && ($appData->appCacheId)) ? $appData->appCacheId : "1.1" }}"
                                               required />

                                        <button type="button" class="plus numbers" >+</button>
                                        <button type="button" class="minus numbers" >-</button>

                                    </div>


                                </div>


                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is App Clear Shared Pref</label>
                                    <div class="col-sm-4 pt-2">

                                        <label for="isAppClearSharedPref1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppClearSharedPref1" name="isAppClearSharedPref" value="1" {{(isset($appData->isAppClearSharedPref) && ($appData->isAppClearSharedPref)) ? 'checked' : "" }}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppClearSharedPref0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppClearSharedPref0" name="isAppClearSharedPref" value="0"  {{((isset($appData->isAppClearSharedPref)  && !$appData->isAppClearSharedPref) || (!$appSettingId)) ? 'checked' : "" }} />
                                            <span class="">No</span>
                                        </label>

                                    </div>


                                    <label for="staticEmail" class="col-sm-2 col-form-label">App Shared Pref. Id</label>
                                    <div class="col-sm-4">
                                        <input type="text"
                                               readonly="readonly"
                                               maxlength="3"
                                               data-currentValue="{{(isset($appData->appSharedPrefId) && ($appData->appSharedPrefId)) ? $appData->appSharedPrefId : '1.1' }}"
                                               class="form-control w-50 d-inline-block notAllowedAlphabets versionControlInput"
                                               name="appSharedPrefId" id="appSharedPrefId"
                                               value="{{(isset($appData->appSharedPrefId) && ($appData->appSharedPrefId)) ? $appData->appSharedPrefId : "1.1" }}"
                                               required />

                                        <button type="button" class="plus numbers" >+</button>
                                        <button type="button" class="minus numbers" >-</button>

                                    </div>



                                </div>



                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is App Details Database Clear</label>
                                    <div class="col-sm-4 pt-2">

                                        <label for="isAppDetailsDatabaseClear1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppDetailsDatabaseClear1" name="isAppDetailsDatabaseClear" value="1" {{(isset($appData->isAppDetailsDatabaseClear) && ($appData->isAppDetailsDatabaseClear)) ? 'checked' : "" }} />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppDetailsDatabaseClear0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppDetailsDatabaseClear0" name="isAppDetailsDatabaseClear" value="0" {{((isset($appData->isAppDetailsDatabaseClear) && !$appData->isAppDetailsDatabaseClear) || (!$appSettingId)) ? 'checked' : "" }} />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label mt-2">App Details Database Clear Version</label>
                                    <div class="col-sm-4 pt-2">

                                        <input type="text"
                                               readonly="readonly"
                                               maxlength="3"
                                               data-currentValue="{{(isset($appData->appDetailsDatabaseClearVersion) && ($appData->appDetailsDatabaseClearVersion)) ? $appData->appDetailsDatabaseClearVersion : '1.1' }}"
                                               class="form-control w-50 d-inline-block notAllowedAlphabets versionControlInput"
                                               name="appDetailsDatabaseClearVersion" id="appDetailsDatabaseClearVersion"
                                               value="{{(isset($appData->appDetailsDatabaseClearVersion) && ($appData->appDetailsDatabaseClearVersion)) ? $appData->appDetailsDatabaseClearVersion : "1.1" }}"
                                               required />

                                        <button type="button" class="plus numbers" >+</button>
                                        <button type="button" class="minus numbers" >-</button>

                                    </div>
                                </div>


                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is Server Local Auth Keys Used</label>
                                    <div class="col-sm-4 pt-2">

                                        <label for="isServerLocalAuthKeyUsed1" class="cursor-pointer">
                                            <input type="radio" class="" id="isServerLocalAuthKeyUsed1" name="isServerLocalAuthKeyUsed" value="1" {{(isset($appData->isServerLocalAuthKeyUsed) && ($appData->isServerLocalAuthKeyUsed)) ? 'checked' : "" }} />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isServerLocalAuthKeyUsed0" class="cursor-pointer">
                                            <input type="radio" class="" id="isServerLocalAuthKeyUsed0" name="isServerLocalAuthKeyUsed" value="0" {{((isset($appData->isServerLocalAuthKeyUsed) && !$appData->isServerLocalAuthKeyUsed) || (!$appSettingId)) ? 'checked' : "" }} />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                    <label for="isAppSigningKeyUsed" class="col-sm-2 col-form-label">Is App Signing Key Used</label>

                                    <div class="col-sm-4 mt-2">
                                        <label for="isAppSigningKeyUsed1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppSigningKeyUsed1" name="isAppSigningKeyUsed" value="1"  {{(isset($appData->isAppSigningKeyUsed) && ($appData->isAppSigningKeyUsed)) ? 'checked' : "" }}   />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppSigningKeyUsed0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppSigningKeyUsed0" name="isAppSigningKeyUsed" value="0"   {{((isset($appData->isAppSigningKeyUsed) && !$appData->isAppSigningKeyUsed) || (!$appSettingId)) ? 'checked' : "" }}  />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                </div>

                                <div class="form-group row d-none">
                                    <label for="staticEmail" class="col-sm-2 col-form-label mt-2">Is Suspend App</label>
                                    <div class="col-sm-4 mt-3">
                                        <label for="suspendApp1" class="cursor-pointer">
                                            <input type="radio" class="" id="suspendApp1" name="" value="1" {{(isset($appData->isSuspendApp) && ($appData->isSuspendApp)) ? 'checked' : "" }}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="suspendApp0" class="cursor-pointer">
                                            <input type="radio" class="" id="suspendApp0" name="" value="0" {{((isset($appData->isSuspendApp) && !$appData->isSuspendApp) || (!$appSettingId)) ? 'checked' : "" }} />
                                            <span class="">No</span>
                                        </label>
                                    </div>


                                    <label for="staticEmail" class="col-sm-2 col-form-label mt-2">Suspend App Message</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" name="suspendAppMessage" id="suspendAppMessage">{{(isset($appData->suspendAppMessage) && ($appData->suspendAppMessage)) ? $appData->suspendAppMessage : "" }}</textarea>
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is Message Dialog Dismiss</label>
                                    <div class="col-sm-4 mt-2">
                                        <label for="isMessageDialogDismiss1" class="cursor-pointer">
                                            <input type="radio" class="" id="isMessageDialogDismiss1" name="isMessageDialogDismiss" value="1" {{(isset($appData->isMessageDialogDismiss) && ($appData->isMessageDialogDismiss)) ? 'checked' : "" }}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isMessageDialogDismiss0" class="cursor-pointer">
                                            <input type="radio" class="" id="isMessageDialogDismiss0" name="isMessageDialogDismiss" value="0" {{((isset($appData->isMessageDialogDismiss) && !$appData->isMessageDialogDismiss) || (!$appSettingId)) ? 'checked' : "" }} />
                                            <span class="">No</span>
                                        </label>
                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is Firebase Database Access</label>
                                    <div class="col-sm-4 pt-2">
                                        <label for="isFirebaseDatabaseAccess1" class="cursor-pointer">
                                            <input type="radio" class="" id="isFirebaseDatabaseAccess1" name="isFirebaseDatabaseAccess" value="1" {{(isset($appData->isFirebaseDatabaseAccess) && ($appData->isFirebaseDatabaseAccess)) ? 'checked' : "" }}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isFirebaseDatabaseAccess0" class="cursor-pointer">
                                            <input type="radio" class="" id="isFirebaseDatabaseAccess0" name="isFirebaseDatabaseAccess" value="0" {{((isset($appData->isFirebaseDatabaseAccess) && !$appData->isFirebaseDatabaseAccess) || (!$appSettingId)) ? 'checked' : "" }} />
                                            <span class="">No</span>
                                        </label>
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Minimum Version Support</label>
                                    <div class="col-sm-4">

                                        <input type="text"
                                               readonly="readonly"
                                               maxlength="3"
                                               data-currentValue="{{(isset($appData->minimumVersionSupport) && ($appData->minimumVersionSupport)) ? $appData->minimumVersionSupport : '1' }}"
                                               class="form-control w-50 d-inline-block notAllowedAlphabets versionControlInput"
                                               name="minimumVersionSupport" id="minimumVersionSupport"
                                               value="{{(isset($appData->minimumVersionSupport) && ($appData->minimumVersionSupport)) ? $appData->minimumVersionSupport : '1' }}"
                                               required />

                                        <button type="button" class="plus digit_numbers" >+</button>
                                        <button type="button" class="minus digit_numbers" >-</button>


                                    </div>

                                    <div class="col-sm-6 text-right">
                                        <button type="submit" class="btn btn-info" id="submitApp"> <i class="fa fa-save"></i> <span class=""> SUBMIT </span> </button>
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

        $("button.numbers").on("click", function() {

            var $button = $(this);
            var OldValue = $button.parent().find("input").attr('data-currentValue');
            var value = $button.parent().find("input").val()

                if ($button.text() == "+") {
                var floatValue = value.split('.');
                var decimalValue = floatValue[1];

                var oldFloatValue = OldValue.split('.');
                var oldDecimalValue = oldFloatValue[1];

                var newVal = parseFloat(decimalValue) + 1;


                let initials = floatValue[0];

                let difference = Math.abs(newVal - oldDecimalValue);
                if(newVal > 9){
                    newVal = 0;
                    initials = parseInt(floatValue[0])+1;
                }

                if(difference > 1){
                    alert("New value must be equal or one decimal greater than current value!");
                    return false;
                }
                else{
                    var finalValue = initials + '.' + newVal;
                }

            }
            else {

                var finalValue = OldValue;

                var floatValue = value.split('.');
                var decimalValue = floatValue[1];

                var oldFloatValue = OldValue.split('.');
                var oldDecimalValue = oldFloatValue[1];


                var newVal = parseFloat(decimalValue) - 1;

                let difference = Math.abs(oldDecimalValue - newVal);

                if(OldValue == value){
                    if(difference){
                        alert("New value must be equal or one decimal greater minus than current value!");
                    }
                }
            }

            $button.parent().find("input").val(finalValue);

        });

        $(document).ready(function($){

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

                let appSettidIdDatabase = {{$appSettingId}};

                let url = "{{ url('admin/add-update-app_settings') }}";
                let appSettingId = "";
                if($("#appSettingId").val() > 0){
                    let appSettingId = '/' + $("#appSettingId").val();
                    url = url + appSettingId;

                }

                var Form_Data = new FormData(this);

                var opt = [];
                opt['btnId']  = "submitApp";
                opt['btnText']  = "SUBMIT";
                opt['btnAddClass']  = "fa fa-save";
                opt['btnRemoveClass']  = "spinner-border spinner-border-sm";

                showHideBtnSpinner("submitApp","Loading...","spinner-border spinner-border-sm","fa fa-save",true);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: Form_Data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {

                        $('#ajax-model').modal('hide');

                        if(!res.message){
                            var message = "App Setting has been updated successfully!";
                        }
                        else{
                            var message = res.message;
                        }

                        if(res.status == 'failed'){
                            var iconToast = "error";
                        }
                        else if(res.firebase_status == 'failed'){
                            var iconToast = "warning";
                        }
						else{
                            var iconToast = "success";
						}

						if(res?.firebaseConfigJson != "null" && res?.firebaseConfigJson.length > 0){

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

                            pushDataToRealTimeDatabase(db,res.node,jsonData,opt,package_id);

                        }
						else{

                            Toast.fire({
                                icon: 'error',
                                title: res?.message
                            });
                            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                        }

						setTimeout(function(){
                            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                        },4500);


                        if(res.status == 'success' || res.firebase_status == 'success'){


                            if(!appSettidIdDatabase){
                                setTimeout(function(){
                                    	 window.location.href = "{{ url('admin/app_settings')}}";
                                },1500);
                            }
                            else{

                                $(".versionControlInput").each(function(index,element){
                                    $("#"+element.id).attr('data-currentValue',element.value);
                                })

                            }
						}

                    },
                    error: function (response) {

                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                        var resp = response.responseJSON;

                        if(response.status == 422){

                            $('html, body').animate({
                                scrollTop: eval($("#PackageIdError").offset().top - 170)
                            }, 1000);

                            $("#app_detail_idError").text(resp.errors.app_detail_id);

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

        function pushDataToRealTimeDatabase(db,node,JsonData,opt,package_id) {

            console.log(db);
            // return false;

            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            set(ref(db,'/'+package_id+'/'),JsonData)
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
        }

    </script>

@endpush
