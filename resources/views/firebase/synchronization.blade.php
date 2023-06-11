@extends('layouts.master')

@section('content')
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-sync_accounts_data'))
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">

                        <form id="firebaseSynchronizationForm" action="javascript:void(0)"  class="form-horizontal" method="POST" >
                            <div class="row">

                                <div class="col-sm-12">
                                    <h3 class=""> Sync Accounts </h3>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row form-group">

                                        <label for="account_filter" class="col-sm-2 col-form-label mt-4">Accounts</label>

                                        <div class="col-sm-4 pt-4">

                                            <select class="form-control" id="account_filter" name="account_id" onchange="getApplicationListOptionByAccountsNoPermission(this.value,'app_detail_id','-1',true);$('#app_detail_id').select2('val','');$('#selectAllAccountsData').prop('checked',false);"  >
                                                <option value="-1" selected>   Select Accounts </option>
                                                @foreach ($accountsList as $obj)
                                                    <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>

                                        <div class="col-sm-3 pt-4 SyncrhonizationType-Block">
                                            <select class="form-control" id="synchronization_type" name="synchronization_type"  >
                                                <option value="">   Select Synchronization Type </option>
                                                @php
                                                    $syncTypes = config('app.synchronizationTypes');
                                                @endphp

                                                @foreach ($syncTypes as $key => $obj)
                                                    <option value="{{ $key }}">{{ $obj}}</option>
                                                @endforeach
                                            </select>

                                        </div>

                                        <div class="col-sm-3 pt-4 SyncrhonizationType-Block">
                                            <select class="form-control" id="sync_accounts_timer" name="sync_accounts_timer">
                                                <option value="">   Select Sync Timer </option>
                                                @php
                                                    $syncTypes = config('app.synchronizationTimer');
                                                @endphp


                                                @foreach ($syncTypes as $key => $obj)
                                                    <option value="{{ $key }}">{{ $obj}}</option>
                                                @endforeach
                                            </select>

                                        </div>


                                    </div>

                                    <div class="row form-group">
                                        <label for="app_details_application_id" class="col-sm-2 col-form-label">Application</label>
                                        <div class="col-sm-6">

                                            <select  class="form-control js-example-basic-multiple"  multiple="multiple" id="app_detail_id" name="app_detail_id[]">
                                                <option value="-1" disabled>   Select App </option>
                                                @foreach ($appsList as $obj)
                                                    <option value="{{ $obj->id }}" data-account_id="{{ $obj->account_id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->appName . ' - ' . $obj->packageId }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-4">
                                            <label for="selectAllAccountsData" class="col-form-label">
                                                <input type="checkbox" id="selectAllAccountsData" > &nbsp; Select All Apps
                                            </label>
                                        </div>


                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6 pt-4">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-sync_accounts_data'))
                                        <button id="pushAccountsDataSubmit" type="submit" class="btn btn-info"> <i class="fa fa-paper-plane"></i> <span class=""> Push Data to Firebase </span> </button>
                                    @endif
                                </div>


                            </div>

                        </form>

                    </div>

                    <div class="card-body">
                        <div class="pull-right text-right closeButtonDiv" style="display:none" >
                            <button type="submit" class="btn btn-danger" onclick="$('#syncAccountsResultList,.closeButtonDiv').hide();$('#syncAccountsResultList tbody').html('<tr></tr>');">
                                <i class="fa fa-times"></i>
                                <span class=""> &nbsp; Close </span>
                            </button>
                        </div>

                        <table class="table table-bordered" style="display:none" id="syncAccountsResultList">
                            <thead>
                            <tr>
                                <th scope="col" colspan="3" class="text-dark text-white custom-siderbar-dark">Sync Accounts Data Result</th>
                            </tr>
                            <tr>
                                <th class="text-dark" scope="col">Application</th>
                                <th class="text-dark" scope="col">Message</th>
                                <th class="text-dark" scope="col">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr style="display:none;">
                                </tr>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>

        </div>
        <!-- /.row 1 -->


            <!-- Small boxes (Stat box2) -->
            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-header">
                            <form id="versionManagementForm" action="javascript:void(0)"  class="form-horizontal" method="POST" >

                                <div class="row">

                                    <div class="col-sm-12">
                                        <h3 class=""> Sync Versions </h3>
                                    </div>


                                    <div class="col-sm-12">
                                        <div class="row form-group">

                                            <label for="versionAccountsId" class="col-sm-2 col-form-label mt-4">Accounts</label>

                                            <div class="col-sm-4 pt-4">

                                                <select class="form-control" id="versionAccountsId" name="versionAccountsId" onchange="getApplicationListOptionByAccountsNoPermission(this.value,'version_app_detail_id','-1',true);$('#version_app_detail_id').select2('val','');$('#selectAllVersionAppData').prop('checked',false);"  >
                                                    <option value="-1" selected>   Select Accounts </option>
                                                    @foreach ($accountsList as $obj)
                                                        <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                                    @endforeach
                                                </select>

                                            </div>

                                            <div class="col-sm-3 pt-4">

                                                <select class="form-control" id="version_categories" name="version_categories">
                                                    <option value="">   Select Version Categories </option>
                                                    @php
                                                        $syncTypes = config('app.versionCategories');
                                                    @endphp


                                                    @foreach ($syncTypes as $key => $obj)
                                                        <option value="{{ $key }}">{{ $obj}}</option>
                                                    @endforeach
                                                </select>

                                            </div>

                                            <div class="col-sm-3 pt-4">

                                                <select class="form-control" id="sync_version_update_timer" name="sync_version_update_timer">
                                                    <option value="">   Select Sync Timer </option>
                                                    @php
                                                        $syncTypes = config('app.synchronizationTimer');
                                                    @endphp


                                                    @foreach ($syncTypes as $key => $obj)
                                                        <option value="{{ $key }}">{{ $obj}}</option>
                                                    @endforeach
                                                </select>

                                            </div>

                                        </div>

                                        <div class="row form-group">
                                            <label for="version_app_detail_id" class="col-sm-2 col-form-label">Application</label>
                                            <div class="col-sm-6">

                                                <select  class="form-control js-example-basic-multiple"  multiple="multiple" id="version_app_detail_id" name="version_app_detail_id[]">
                                                    <option value="-1" disabled>   Select App </option>
                                                    @foreach ($appsList as $obj)
                                                        <option value="{{ $obj->id }}" data-account_id="{{ $obj->account_id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->appName . ' - ' . $obj->packageId }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <label for="selectAllVersionAppData" class="col-form-label">
                                                    <input type="checkbox" id="selectAllVersionAppData" > &nbsp; Select All Apps
                                                </label>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6 pt-4">
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-sync_accounts_data'))
                                            <button type="submit" class="btn btn-info" id="updateAppSettingVersions"> <i class="fa fa-paper-plane"></i> <span class="">Push Updated Version to Firebase </span> </button>
                                        @endif
                                    </div>


                                </div>

                            </form>

                        </div>


                        <div class="card-body">
                            <div class="pull-right text-right closeVersionUpdateButtonDiv" style="display:none" >
                                <button type="submit" class="btn btn-danger" onclick="$('#syncVersionUpdateResultList,.closeVersionUpdateButtonDiv').hide();$('#syncVersionUpdateResultList tbody').html('<tr></tr>');">
                                    <i class="fa fa-times"></i>
                                    <span class=""> &nbsp; Close </span>
                                </button>
                            </div>

                            <table class="table table-bordered" style="display:none" id="syncVersionUpdateResultList">
                                <thead>
                                <tr>
                                    <th scope="col" colspan="3" class="text-dark text-white custom-siderbar-dark">Sync Versions Data Result</th>
                                </tr>
                                <tr>
                                    <th class="text-dark" scope="col">Application</th>
                                    <th class="text-dark" scope="col">Message</th>
                                    <th class="text-dark" scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr style="display:none;">
                                </tr>
                                </tbody>
                            </table>

                        </div>



                    </div>


                </div>

            </div>
            <!-- /.row 2-->


        @endif

        @if(auth()->user()->hasRole('super-admin') || (auth()->user()->can('view-manage-sync_apps_data') && auth()->user()->can('view-applications')  && auth()->user()->can('manage-applications')))

        <!-- Small boxes (Stat box3) -->
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <form id="appKeysManagementForm" action="javascript:void(0)"  class="form-horizontal" method="POST"     >

                            <div class="row">

                                <div class="col-sm-12">
                                    <h3 class=""> Sync App Settings</h3>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row form-group">

                                        <label for="appKeyAccountsId" class="col-sm-2 col-form-label mt-4">Accounts</label>
                                        <div class="col-sm-4 pt-4">
                                            <select class="form-control" id="appKeyAccountsId" name="appKeyAccountsId" onchange="getApplicationListOptionByAccounts(this.value,'app_key_app_detail_id','-1',true);$('#app_key_app_detail_id').select2('val','');$('#selectAllApp').prop('checked',false);"  >
                                                <option value="-1" selected>   Select Accounts </option>
                                                @foreach ($accountsList as $obj)
                                                    <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>

                                        <div class="col-sm-3 pt-4">

                                            <select class="form-control" id="syncAppSettingTimer" name="syncAppSettingTimer">
                                                <option value="">   Select Sync Timer </option>
                                                @php
                                                    $syncTypes = config('app.synchronizationTimer');
                                                @endphp


                                                @foreach ($syncTypes as $key => $obj)
                                                    <option value="{{ $key }}">{{ $obj}}</option>
                                                @endforeach
                                            </select>

                                        </div>


                                    </div>

                                    <div class="row form-group">
                                        <label for="appKeyAccountsId" class="col-sm-2 col-form-label">Application</label>
                                        <div class="col-sm-6">

                                            <select  class="form-control js-example-basic-multiple"  multiple="multiple" id="app_key_app_detail_id" name="app_key_app_detail_id[]">
                                                <option value="-1" disabled >   Select App </option>
                                                @foreach ($assignedAppsList as $obj)
                                                    <option value="{{ $obj->id }}" data-account_id="{{ $obj->account_id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->appName . ' - ' . $obj->packageId }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-4">
                                            <label for="selectAllApp" class="col-form-label">
                                                <input type="checkbox" id="selectAllApp" > &nbsp; Select All Apps
                                            </label>
                                        </div>


                                    </div>

                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="appAuthKey1" class="col-sm-2 col-form-label" id="">App Auth Key 1</label>
                                        <div class="col-sm-4">
                                            <input type="text"  class="form-control" name="appAuthKey1" id="appAuthKey1" value="{{(isset($appData->appAuthKey1) && ($appData->appAuthKey1)) ? $appData->appAuthKey1 : "" }}"  />
                                            <span class="text-danger" id="appAuthKey1Error"></span>
                                        </div>

                                        <label for="appAuthKey2" class="col-sm-2 col-form-label">App Auth Key 2</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="appAuthKey2" id="appAuthKey2" value="{{(isset($appData->appAuthKey2) && ($appData->appAuthKey2)) ? $appData->appAuthKey2 : "" }}" >
                                            <span class="text-danger" id="appAuthKey2Error"></span>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="serverAuthKey1" class="col-sm-2 col-form-label">Server Auth Key 1</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="serverAuthKey1" id="serverAuthKey1" value="{{(isset($appData->serverAuthKey1) && ($appData->serverAuthKey1)) ? $appData->serverAuthKey1 : "" }}"  />
                                        </div>


                                        <label for="serverAuthKey2" class="col-sm-2 col-form-label">Server Auth Key 2</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="serverAuthKey2" id="serverAuthKey2" value="{{(isset($appData->serverAuthKey2) && ($appData->serverAuthKey2)) ? $appData->serverAuthKey2 : "" }}" />
                                        </div>

                                    </div>
                                </div>


                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="isAppSigningKeyUsed" class="col-sm-2 col-form-label">Is App Signing Key Used</label>
                                        <div class="col-sm-4  pt-2">

                                            <label for="isAppSigningKeyUsed1" class="cursor-pointer">
                                                <input type="radio" class="" id="isAppSigningKeyUsed1" name="isAppSigningKeyUsed" value="1"  />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isAppSigningKeyUsed0" class="cursor-pointer">
                                                <input type="radio" class="" id="isAppSigningKeyUsed0" name="isAppSigningKeyUsed" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>

                                        <label for="isServerTokenFetch" class="col-sm-2 col-form-label">Is Server Token Fetch</label>
                                        <div class="col-sm-4  pt-2">

                                            <label for="isServerTokenFetch1" class="cursor-pointer">
                                                <input type="radio" class="" id="isServerTokenFetch1" name="isServerTokenFetch" value="1"  />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isServerTokenFetch0" class="cursor-pointer">
                                                <input type="radio" class="" id="isServerTokenFetch0" name="isServerTokenFetch" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>



                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="isAppAuthKeysUsed" class="col-sm-2 col-form-label">Is App Auth Keys Used</label>
                                        <div class="col-sm-4  pt-2">

                                            <label for="isAppAuthKeysUsed1" class="cursor-pointer">
                                                <input type="radio" class="" id="isAppAuthKeysUsed1" name="isAppAuthKeysUsed" value="1"  />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isAppAuthKeysUsed0" class="cursor-pointer">
                                                <input type="radio" class="" id="isAppAuthKeysUsed0" name="isAppAuthKeysUsed" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>

                                        <label for="isServerLocalAuthKeyUsed" class="col-sm-2 col-form-label">Is Server Local Auth Keys Used</label>
                                        <div class="col-sm-4  pt-2">
                                            <label for="isServerLocalAuthKeyUsed1" class="cursor-pointer">
                                                <input type="radio" class="" id="isServerLocalAuthKeyUsed1" name="isServerLocalAuthKeyUsed" value="1"  />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isServerLocalAuthKeyUsed0" class="cursor-pointer">
                                                <input type="radio" class="" id="isServerLocalAuthKeyUsed0" name="isServerLocalAuthKeyUsed" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">


                                        <label for="isFirebaseDatabaseAccess" class="col-sm-2 col-form-label">Is Firebase Database Access</label>
                                        <div class="col-sm-4  pt-2">

                                            <label for="isFirebaseDatabaseAccess1" class="cursor-pointer">
                                                <input type="radio" class="" id="isFirebaseDatabaseAccess1" name="isFirebaseDatabaseAccess" value="1"  />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isFirebaseDatabaseAccess0" class="cursor-pointer">
                                                <input type="radio" class="" id="isFirebaseDatabaseAccess0" name="isFirebaseDatabaseAccess" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>



                                    </div>
                                </div>




                                <div class="col-sm-6 col-md-6 pt-4">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-sync_apps_data'))
                                        <button type="submit" class="btn btn-info" id="updateAppKeys"> <i class="fa fa-paper-plane"></i> Push App Settings to Firebase  </button>
                                    @endif
                                </div>


                            </div>

                        </form>

                    </div>

                    <div class="card-body">
                        <div class="pull-right text-right closeAppSettingButtonDiv" style="display:none" >
                            <button type="submit" class="btn btn-danger" onclick="$('#syncAppSettingResultList, .closeAppSettingButtonDiv').hide();$('#syncAppSettingResultList tbody').html('<tr></tr>');">
                                <i class="fa fa-times"></i>
                                <span class="">  Close </span>
                            </button>
                        </div>

                        <table class="table table-bordered" style="display:none" id="syncAppSettingResultList">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="3" class="text-dark text-white custom-siderbar-dark">Sync App Settings Result</th>
                                </tr>
                                <tr>
                                    <th class="text-dark" scope="col">Application</th>
                                    <th class="text-dark" scope="col">Message</th>
                                    <th class="text-dark" scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="display:none;">
                                </tr>
                            </tbody>
                        </table>

                    </div>


                </div>
            </div>

        </div>
        <!-- /.row 3-->


        <!-- Small boxes (Stat box4) -->
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <form id="appCredentialsManagementForm" action="javascript:void(0)"  class="form-horizontal" method="POST" >

                            <div class="row">

                                <div class="col-sm-12">
                                    <h3 class=""> Sync App Credentials </h3>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row form-group">
                                        <label for="appCredentialsAccountsId" class="col-sm-2 col-form-label mt-4">Accounts</label>
                                        <div class="col-sm-4 pt-4">
                                            <select class="form-control" id="appCredentialsAccountsId" name="appCredentialsAccountsId" onchange="getApplicationListOptionByAccounts(this.value,'app_credentials_app_detail_id','-1',true);$('#app_credentials_app_detail_id').select2('val','');$('#selectAllAppCredentials').prop('checked',false);"  >
                                                <option value="-1" selected>   Select Accounts </option>
                                                @foreach ($accountsList as $obj)
                                                    <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="app_credentials" class="col-sm-2 col-form-label">Application</label>
                                        <div class="col-sm-6">

                                            <select  class="form-control js-example-basic-multiple"  multiple="multiple" id="app_credentials_app_detail_id" name="app_credentials_app_detail_id[]">
                                                <option value="-1" disabled>   Select App </option>
                                                @foreach ($assignedAppsList as $obj)
                                                    <option value="{{ $obj->id }}" data-account_id="{{ $obj->account_id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->appName . ' - ' . $obj->packageId }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-4">
                                            <label for="selectAllAppCredentials" class="col-form-label">
                                                <input type="checkbox" id="selectAllAppCredentials" > &nbsp; Select All Apps
                                            </label>
                                        </div>


                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="server_auth_key" class="col-sm-2 col-form-label" id="">Server Auth key</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="server_auth_key" name="server_auth_key" placeholder="" >
                                            <span class="text-danger" id="server_auth_keyError"></span>
                                        </div>

                                        <label for="stream_key" class="col-sm-2 col-form-label">Stream Key</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="stream_key" name="stream_key" value="" >
                                            <span class="text-danger" id="stream_keyError"></span>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="serverAuthKey1" class="col-sm-2 col-form-label">Token key </label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="token_key" name="token_key" value="" >
                                            <span class="text-danger" id="token_keyError"></span>
                                        </div>

                                        <label for="appSigningKey" class="col-sm-2 col-form-label">App Signing Key </label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="appSigningKey" name="appSigningKey" value="" >
                                            <span class="text-danger" id="appSigningKeyError"></span>
                                        </div>



                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6 pt-4">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-sync_apps_data'))
                                        <button type="submit" class="btn btn-info" id="updateAppCredentials"> <i class="fa fa-save"></i> Update  </button>
                                    @endif
                                </div>


                            </div>

                        </form>

                    </div>

                </div>
            </div>

        </div>
        <!-- /.row 4-->

        <!-- Small boxes (Stat box5) -->
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <form id="appDetailsManagementForm" action="javascript:void(0)"  class="form-horizontal" method="POST" >

                            <div class="row">

                                <div class="col-sm-12">
                                    <h3 class=""> Sync App Details </h3>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row form-group">
                                        <label for="appDetailsAccountsId" class="col-sm-2 col-form-label mt-4">Accounts</label>
                                        <div class="col-sm-4 pt-4">
                                            <select class="form-control" id="appDetailsAccountsId" name="appDetailsAccountsId" onchange="getApplicationListOptionByAccounts(this.value,'app_details_application_id','-1',true);$('#app_details_application_id').select2('val','');$('#selectAllAppDetails').prop('checked',false);"  >
                                                <option value="-1" selected>   Select Accounts </option>
                                                @foreach ($accountsList as $obj)
                                                    <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>


                                        <div class="col-sm-3 pt-4">

                                            <select class="form-control" id="syncAppDetailTimer" name="syncAppDetailTimer">
                                                <option value="">   Select Sync Timer </option>
                                                @php
                                                    $syncTypes = config('app.synchronizationTimer');
                                                @endphp


                                                @foreach ($syncTypes as $key => $obj)
                                                    <option value="{{ $key }}">{{ $obj}}</option>
                                                @endforeach
                                            </select>


                                        </div>

                                    </div>
                                    <div class="row form-group">
                                        <label for="app_details_application_id" class="col-sm-2 col-form-label">Application</label>
                                        <div class="col-sm-6">

                                            <select  class="form-control js-example-basic-multiple"  multiple="multiple" id="app_details_application_id" name="app_details_application_id[]">
                                                <option value="-1" disabled>   Select App </option>
                                                @foreach ($assignedAppsList as $obj)
                                                    <option value="{{ $obj->id }}" data-account_id="{{ $obj->account_id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->appName . ' - ' . $obj->packageId }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-4">
                                            <label for="selectAllAppDetails" class="col-form-label">
                                                <input type="checkbox" id="selectAllAppDetails" > &nbsp; Select All Apps
                                            </label>
                                        </div>


                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="adsIntervalTime" class="col-sm-2 col-form-label">adsIntervalTime</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="adsIntervalTime" id="adsIntervalTime" value="" />
                                        </div>

                                        <label for="adsIntervalCount" class="col-sm-2 col-form-label">adsIntervalCount</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="adsIntervalCount" id="adsIntervalCount" value="" />
                                        </div>

                                    </div>
                                </div>


                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="startAppId" class="col-sm-2 col-form-label">startAppId</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="startAppId" id="startAppId" value="" />
                                        </div>


                                        <label for="isScreenAdsLimit" class="col-sm-2 col-form-label">isScreenAdsLimit</label>
                                        <div class="col-sm-4  pt-2">
                                            <label for="isScreenAdsLimit1" class="cursor-pointer">
                                                <input type="radio" class="" id="isScreenAdsLimit1" name="isScreenAdsLimit" value="1" />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isScreenAdsLimit0" class="cursor-pointer">
                                                <input type="radio" class="" id="isScreenAdsLimit0" name="isScreenAdsLimit" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>






                                    </div>




                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="isAdmobAdsShow" class="col-sm-2 col-form-label">isAdmobAdsShow</label>
                                        <div class="col-sm-4  pt-2">
                                            <label for="isAdmobAdsShow1" class="cursor-pointer">
                                                <input type="radio" class="" id="isAdmobAdsShow1" name="isAdmobAdsShow" value="1" />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isAdmobAdsShow0" class="cursor-pointer">
                                                <input type="radio" class="" id="isAdmobAdsShow0" name="isAdmobAdsShow" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>

                                        <label for="isAdsInterval" class="col-sm-2 col-form-label">isAdsInterval</label>
                                        <div class="col-sm-4  pt-2">

                                            <label for="isAdsInterval1" class="cursor-pointer">
                                                <input type="radio" class="" id="isAdsInterval1" name="isAdsInterval" value="1"  />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isAdsInterval0" class="cursor-pointer">
                                                <input type="radio" class="" id="isAdsInterval0" name="isAdsInterval" value="0" />
                                                <span class="">No</span>
                                            </label>

                                        </div>

                                    </div>
                                </div>


                                <div class="col-sm-12">
                                    <div class="form-group row">

                                        <label for="isStartAppAdsShow" class="col-sm-2 col-form-label">isStartAppAdsShow</label>
                                        <div class="col-sm-4  pt-2">

                                            <label for="isStartAppAdsShow1" class="cursor-pointer">
                                                <input type="radio" class="" id="isStartAppAdsShow1" name="isStartAppAdsShow" value="1"  />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isStartAppAdsShow0" class="cursor-pointer">
                                                <input type="radio" class="" id="isStartAppAdsShow0" name="isStartAppAdsShow" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>


                                        <label for="isSponsorAdsShow" class="col-sm-2 col-form-label">isSponsorAdsShow</label>
                                        <div class="col-sm-4  pt-2">

                                            <label for="isSponsorAdsShow1" class="cursor-pointer">
                                                <input type="radio" class="" id="isSponsorAdsShow1" name="isSponsorAdsShow" value="1"  />
                                                <span class="">Yes</span>
                                            </label>

                                            <label for="isSponsorAdsShow0" class="cursor-pointer">
                                                <input type="radio" class="" id="isSponsorAdsShow0" name="isSponsorAdsShow" value="0"  />
                                                <span class="">No</span>
                                            </label>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6 pt-4">
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->can('view-manage-sync_apps_data'))
                                        <button type="submit" class="btn btn-info" id="updateAppDetails"> <i class="fa fa-paper-plane"></i> <span class="">Push App Details to Firebase </span> </button>
                                    @endif
                                </div>

                            </div>

                        </form>

                    </div>


                    <div class="card-body">
                        <div class="pull-right text-right closeAppDetailButtonDiv" style="display:none" >
                            <button type="submit" class="btn btn-danger" onclick="$('#syncAppDetailResultList, .closeAppDetailButtonDiv').hide();$('#syncAppDetailResultList tbody').html('<tr></tr>');">
                                <i class="fa fa-times"></i>
                                <span class="">  Close </span>
                            </button>
                        </div>

                        <table class="table table-bordered" style="display:none" id="syncAppDetailResultList">
                            <thead>
                            <tr>
                                <th scope="col" colspan="3" class="text-dark text-white custom-siderbar-dark">Sync App Details Result</th>
                            </tr>
                            <tr>
                                <th class="text-dark" scope="col">Application</th>
                                <th class="text-dark" scope="col">Message</th>
                                <th class="text-dark" scope="col">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr style="display:none;">
                            </tr>
                            </tbody>
                        </table>

                    </div>


                </div>
            </div>

        </div>
        <!-- /.row 5-->

        @endif




    </div><!-- /.container-fluid -->

<!-- /.content -->


@endsection

@push('scripts')
<script type="module">

    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-app.js";
    import { initializeAppCheck , ReCaptchaEnterpriseProvider } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-app-check.js";
    import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-analytics.js";
    import { getDatabase , set , ref } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-database.js";


    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });


    $("#selectAllApp").click(function(){
        if($("#selectAllApp").is(':checked') ){
            $("#app_key_app_detail_id > option:not(:first)").prop("selected","selected");
            $("#app_key_app_detail_id").select2();
        }else{
            $("#app_key_app_detail_id > option").removeAttr("selected");
            $("#app_key_app_detail_id").select2("val", "");
            $("#app_key_app_detail_id").trigger("change");
        }
    });

    $("#selectAllAccountsData").click(function(){
        if($("#selectAllAccountsData").is(':checked') ){
            $("#app_detail_id > option:not(:first)").prop("selected","selected");
            $("#app_detail_id").trigger("change");
        }else{
            $("#app_detail_id > option").removeAttr("selected");
            $("#app_detail_id").select2("val", "");
            $("#app_detail_id").trigger("change");
        }
    });


    $("#selectAllVersionAppData").click(function(){
        if($("#selectAllVersionAppData").is(':checked') ){
            $("#version_app_detail_id > option:not(:first)").prop("selected","selected");
            $("#version_app_detail_id").trigger("change");
        }else{
            $("#version_app_detail_id > option").removeAttr("selected");
            $("#version_app_detail_id").select2("val", "");
            $("#version_app_detail_id").trigger("change");
        }
    });

    $("#selectAllAppCredentials").click(function(){
        if($("#selectAllAppCredentials").is(':checked') ){
            $("#app_credentials_app_detail_id > option:not(:first)").prop("selected","selected");
            $("#app_credentials_app_detail_id").trigger("change");
        }else{
            $("#app_credentials_app_detail_id > option").removeAttr("selected");
            $("#app_credentials_app_detail_id").select2("val", "");
            $("#app_credentials_app_detail_id").trigger("change");
        }
    });


    $("#selectAllAppDetails").click(function(){
        if($("#selectAllAppDetails").is(':checked') ){
            $("#app_details_application_id > option:not(:first)").prop("selected","selected");
            $("#app_details_application_id").trigger("change");
        }else{
            $("#app_details_application_id > option").removeAttr("selected");
            $("#app_details_application_id").select2("val", "");
            $("#app_details_application_id").trigger("change");
        }
    });

    $('#adsIntervalTime,#adsIntervalCount').on('keypress',function (e) {

        var charCode = (e.which) ? e.which : event.keyCode
        if (String.fromCharCode(charCode).match(/[^0-9+.]/g))
            return false;

    });

    $('#adsIntervalTime,#adsIntervalCount').on("cut copy paste",function(e) {
        e.preventDefault();
    });

    /****** Add or Update Version Management Form  ::  Function **********/
    $("#appDetailsManagementForm").on('submit',(function(e) {

        $('#syncAppDetailResultList tbody').html('<tr></tr>');
        $('#syncAppDetailResultList, .closeAppDetailButtonDiv').hide();

        e.preventDefault();
        var Form_Data = new FormData(this);
        var validation = true;

        if(!$("#appDetailsAccountsId").val() || ($("#appDetailsAccountsId").val() == "-1")){
            alert("Please Select Accounts");
            validation = false;
            return false;
        }

        if(($("#app_details_application_id").val() == "") || ($("#app_details_application_id").val() == "-1")){
            alert("Please Select Application");
            validation = false;
            return false;
        }

        if($("#adsIntervalCount").val() === "0"){
            alert("Ads Interval Count must be greater than zero");
            return false;
        }

        var adsIntervalTime = $("#adsIntervalTime").val().trim();
        var startAppId = $("#startAppId").val().trim();
        var adsIntervalCount = $("#adsIntervalCount").val().trim();
        var isScreenAdsLimit = $("input[name='isScreenAdsLimit']").is(":checked");
        var isAdmobAdsShow = $("input[name='isAdmobAdsShow']").is(":checked");
        var isAdsInterval = $("input[name='isAdsInterval']").is(":checked");
        var isStartAppAdsShow = $("input[name='isStartAppAdsShow']").is(":checked");
        var isSponsorAdsShow = $("input[name='isSponsorAdsShow']").is(":checked");

        if( adsIntervalTime || startAppId || isAdmobAdsShow || isAdsInterval || isStartAppAdsShow || isSponsorAdsShow || isScreenAdsLimit || (adsIntervalCount > 0)){
            validation = true;
        }
        else{

            if(adsIntervalCount === "0"){
                alert("Ads Interval Count must be greater than zero");
                validation =  false;
                return false;
            }

            if(!adsIntervalTime || !startAppId || !isAdmobAdsShow || !isAdsInterval || !isStartAppAdsShow || !isSponsorAdsShow || !isScreenAdsLimit){
                alert("Please fill at least one field!");
                validation =  false;
                return false;
            }
        }

        var opt = [];
        opt['btnId']  = "updateAppDetails";
        opt['btnText']  = "Push App Details to Firebase";
        opt['btnAddClass']  = "fa fa-paper-plane";
        opt['btnRemoveClass']  = "spinner-border spinner-border-sm";
        opt['resultTableId']  = "syncAppDetailResultList";


        var applications_list = $("#app_details_application_id").val();

        if(!$("#syncAppDetailTimer").val() && applications_list.length > 1){
            alert("Please Select Synchronization Timer");
            validation = false;
            return false;
        }

        showHideBtnSpinner("updateAppDetails","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

        if(validation){

            var timer = $("#syncAppDetailTimer").val();

            for (let i=0; i < applications_list.length; i++) {
                SyncAppDetailLoop(i,this,opt,applications_list.length,timer);
            }
        }
        else{
            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
        }

        return false;

    }));

    async function SyncAppDetailLoop(iteration,htmlForm,opt,appSize,timer){

        showHideBtnSpinner("updateAppDetails","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

        var finalTimer = 1000;

        if(iteration == 0){

            finalTimer  = 1000;
        }
        else{
            finalTimer = timer *  iteration;
        }

        setTimeout(function(){

            showHideBtnSpinner("updateAppDetails","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

            let totalAppSize = parseInt(appSize) - 1;

            let  applications_list = [];
            var Form_Data = new FormData(htmlForm);
            applications_list = $("#app_details_application_id").val();
            Form_Data.set("app_details_application_ids[]",applications_list[iteration]);

            $.ajax({
                type:"POST",
                url: "{{ url('admin/sync/app-details') }}",
                data: Form_Data,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response){

                    var customHtml = "";
                    var successHtml = "";
                    var errorHtml = "";

                    if(response?.data?.failed.length > 0){

                        for(var i = 0 ; i < response?.data?.failed.length;i++) {
                            $('table#syncAppDetailResultList tbody tr:last').after('<tr><td>'+response?.data?.failed[i]?.app_detail+'</td> <td>'+response?.data?.failed[i]?.message+'</td>  <td><span class="badge badge-danger text-sm">Failed</span></td> </tr>');
                            $("table#syncAppDetailResultList").show();
                        }
                    }

                    customHtml = errorHtml;

                    if(response?.data?.success.length > 0) {

                        for (var i = 0; i < response?.data?.success.length; i++) {

                            if (response?.data?.success[i]?.firebaseConfigJson != "null") {

                                $('table#syncAppDetailResultList tbody tr:last').after('<tr><td>'+response?.data?.success[i]?.app_detail+'</td> <td>'+response?.data?.success[i]?.message+'</td>  <td><span class="badge badge-success text-sm">Success</span></td> </tr>');
                                $("table#syncAppDetailResultList").show();

                                showHideBtnSpinner("updateAppDetails","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

                                var firebaseConfig = JSON.parse(response?.data?.success[i]?.firebaseConfigJson);
                                const app = initializeApp(firebaseConfig, response?.data?.success[i]?.appPackageId);
                                const db = getDatabase(app);
                                const appCheck = initializeAppCheck(app, {
                                    provider: new ReCaptchaEnterpriseProvider(response?.data?.success[i]?.reCaptchaKeyId),
                                    isTokenAutoRefreshEnabled: true // Set to true to allow auto-refresh.
                                });

                                const jsonData = JSON.parse(response?.data?.success[i]?.firebaseData);

                                if(response?.data?.success[i]?.node == "AppDetails"){
                                    syncDataToRealTimeDatabase(db, response?.data?.success[i]?.node, jsonData,opt);
                                }
                                else{
                                    pushDataToRealTimeDatabase(db, response?.data?.success[i]?.node, jsonData,opt)
                                }

                            }

                        }
                    }
                },
                dataType: 'json',
                error:function (ex) {

                    Toast.fire({
                        icon: 'error',
                        title: "Something Went Wrong!",
                    })

                    showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                }
            });

            if(iteration == totalAppSize){

                setTimeout(function(){
                    showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                    $(".closeAppDetailButtonDiv").show();
                    return false;
                },5000);
            }
        }, finalTimer);


    }

    /****** Add or Update Version Management Form  ::  Function **********/
    $("#appCredentialsManagementForm").on('submit',(function(e) {

        e.preventDefault();
        var Form_Data = new FormData(this);
        var validation = true;

        if(!$("#appCredentialsAccountsId").val() || ($("#appCredentialsAccountsId").val() == "-1")){
            alert("Please Select Accounts");
            validation = false;
            return false;
        }
        if(($("#app_credentials_app_detail_id").val() == "") || ($("#app_credentials_app_detail_id").val() == "-1")){
            alert("Please Select Application");
            validation = false;
            return false;
        }
        else{
        }

        var server_auth_key = $("#server_auth_key").val().trim();
        var stream_key = $("#stream_key").val().trim();
        var token_key = $("#token_key").val().trim();
        var appSigningKey = $("#appSigningKey").val().trim();

        if( server_auth_key || stream_key || token_key  || appSigningKey ){
            validation = true;
        }
        else{
            alert("Please fill at least on key field!");
            validation =  false;
        }

        if(validation) {

            $.ajax({
                type:"POST",
                url: "{{ url('admin/sync/app-credentials') }}",
                data: Form_Data,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response){

                    var customHtml = "";
                    var successHtml = "";
                    var errorHtml = "";

                    if(response?.data?.failed.length > 0){
                        errorHtml = '<table id="responseTable" border="1" width="100%">' +
                            '        <thead>' +
                            '            <tr>' +
                            '                <th class="text-uppercase bg-gray-light">App Name</th>' +
                            '                <th class="text-uppercase bg-gray-light"> Reason </th>' +
                            '            </tr>' +
                            '        </thead>' +
                            '        <tbody>';
                        ;

                        for(var i = 0 ; i < response?.data?.failed.length;i++){
                            errorHtml += '<tr>';
                            errorHtml += '<td class="text-danger text-md pb-2 pt-2 text-left fs pr-3 pl-3">' + response?.data?.failed[i].app_detail +  '</td>';
                            errorHtml += '<td class="text-danger text-md pb-2 pt-2 text-left fs pr-3 pl-3">' + response?.data?.failed[i].message +  '</td> </tr>';
                        }

                        errorHtml += '</tbody></table>';

                    }

                    customHtml = errorHtml;

                    if(customHtml){

                        Swal.fire({
                            title: '<strong>Sync App Credentials ( Response ) </strong>',
                            icon: 'warning',
                            html: customHtml,
                            showConfirmButton: true,
                            confirmButtonText: 'OK'

                        })
                            .then((result) => {

                                if (result.value) {

                                    if(response?.data?.success.length > 0) {

                                        Swal.fire('Success','App Credentials Successfully Updated','success')

                                    }
                                }
                            });
                    }
                    else{
                        if(response?.data?.success.length > 0) {
                            Swal.fire('Success','App Credentials Successfully Updated','success')
                        }
                    }




                    if(response?.data?.success.length > 0) {
                    }

                },
                dataType: 'json',
                error:function (response) {


                    var message = "Something Went Wrong!";
                    if(response?.responseJSON?.error){
                        message = response?.responseJSON?.error;
                    }
                    else if(response?.responseJSON?.message){
                        message = response?.responseJSON?.message
                    }

                    Toast.fire({
                        icon: 'error',
                        title: message
                    });


                    $("#push_data").html('Update');
                    $("#push_data"). attr("disabled", false);


                }
            });



        }


    }));

    /****** Add or Update Version Management Form  ::  Function **********/
    $("#appKeysManagementForm").on('submit',(function(e) {

        $('#syncAppSettingResultList tbody').html('<tr></tr>');
        $('#syncAppSettingResultList, .closeAppSettingButtonDiv').hide();

        e.preventDefault();
        var Form_Data = new FormData(this);
        var validation = true;

        if(!$("#appKeyAccountsId").val() || ($("#appKeyAccountsId").val() == "-1")){
            alert("Please Select Accounts");
            validation = false;
            return false;
        }
        if(($("#app_key_app_detail_id").val() == "") || ($("#app_key_app_detail_id").val() == "-1")){
            alert("Please Select Application");
            validation = false;
            return false;
        }

        var appAuthKey1 =$("#appAuthKey1").val().trim();
        var appAuthKey2 =$("#appAuthKey2").val().trim();
        var serverAuthKey1 =$("#serverAuthKey1").val().trim();
        var serverAuthKey2 =$("#serverAuthKey2").val().trim();
        var isAppSigningKeyUsed = $("input[name='isAppSigningKeyUsed']").is(":checked");
        var isFirebaseDatabaseAccess = $("input[name='isFirebaseDatabaseAccess']").is(":checked");
        var isServerTokenFetch = $("input[name='isServerTokenFetch']").is(":checked");
        var isAppAuthKeysUsed = $("input[name='isAppAuthKeysUsed']").is(":checked");
        var isServerLocalAuthKeyUsed = $("input[name='isServerLocalAuthKeyUsed']").is(":checked");

        if(appAuthKey1 || appAuthKey2 || serverAuthKey1 || serverAuthKey2 || isAppSigningKeyUsed
            || isFirebaseDatabaseAccess || isServerTokenFetch || isAppAuthKeysUsed || isServerLocalAuthKeyUsed ){
            validation = true;
        }
        else{
            alert("Please fill at least on key field!");
            validation =  false;
            return false;
        }

        var applications_list = $("#app_key_app_detail_id").val();

        if(!$("#syncAppSettingTimer").val() && applications_list.length > 1){
            alert("Please Select Synchronization Timer");
            validation = false;
            return false;
        }

        var opt = [];

        opt['btnId']  = "updateAppKeys";
        opt['btnText']  = "Push App Settings to Firebase";
        opt['btnAddClass']  = "fa fa-paper-plane";
        opt['btnRemoveClass']  = "spinner-border spinner-border-sm";
        opt['resultTableId']  = "syncAppSettingResultList";

        showHideBtnSpinner("updateAppKeys","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

        if(validation){

            var timer = $("#syncAppSettingTimer").val();

            for (let i=0; i < applications_list.length; i++) {
                SyncAppSettingKeysLoop(i,this,opt,applications_list.length,timer);
            }
        }
        else{
            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
        }

    }));

    async function SyncAppSettingKeysLoop(iteration,htmlForm,opt,appSize,timer){

        showHideBtnSpinner("updateAppKeys","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

        var finalTimer = 1000;

        if(iteration == 0){

            finalTimer  = 1000;
        }
        else{
            finalTimer = timer *  iteration;
        }

        setTimeout(function(){

            showHideBtnSpinner("updateAppKeys","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

            let totalAppSize = parseInt(appSize) - 1;

            let  applications_list = [];

            var Form_Data = new FormData(htmlForm);

            applications_list = $("#app_key_app_detail_id").val();

            Form_Data.set("app_key_app_detail_ids[]",applications_list[iteration]);

            /***********
             *
             * Paste AJAX FUNCTION HERE
             *
             * ***************/


            $.ajax({
                type:"POST",
                url: "{{ url('admin/sync/app-keys') }}",
                data: Form_Data,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response){

                    var customHtml = "";
                    var successHtml = "";
                    var errorHtml = "";

                    if(response?.data?.failed.length > 0){

                        for(var i = 0 ; i < response?.data?.failed.length;i++) {
                            $('table#syncAppSettingResultList tbody tr:last').after('<tr><td>'+response?.data?.failed[i]?.app_detail+'</td> <td>'+response?.data?.failed[i]?.message+'</td>  <td><span class="badge badge-danger text-sm">Failed</span></td> </tr>');
                            $("table#syncAppSettingResultList").show();
                        }
                    }

                    if(response?.data?.success.length > 0) {

                        for (var i = 0; i < response?.data?.success.length; i++) {

                            $('table#syncAppSettingResultList tbody tr:last').after('<tr><td>'+response?.data?.success[i]?.app_detail+'</td> <td>'+response?.data?.success[i]?.message+'</td>  <td><span class="badge badge-success text-sm">Success</span></td> </tr>');
                            $("table#syncAppSettingResultList").show();

                            showHideBtnSpinner("updateAppKeys","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

                            if (response?.data?.success[i]?.firebaseConfigJson != "null") {
                                var firebaseConfig = JSON.parse(response?.data?.success[i]?.firebaseConfigJson);
                                const app = initializeApp(firebaseConfig, response?.data?.success[i]?.appPackageId);
                                const db = getDatabase(app);
                                const appCheck = initializeAppCheck(app, {
                                    provider: new ReCaptchaEnterpriseProvider(response?.data?.success[i]?.reCaptchaKeyId),
                                    isTokenAutoRefreshEnabled: true // Set to true to allow auto-refresh.
                                });

                                const jsonData = JSON.parse(response?.data?.success[i]?.firebaseData);

                                pushDataToRealTimeDatabase(db, response?.data?.success[i]?.node, jsonData,opt)

                            }
                        }
                    }
                    else{
                        // showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                    }

                },
                dataType: 'json',
                error:function (response) {

                    var message = "Something Went Wrong!";
                    if(response?.responseJSON?.error){
                        message = response?.responseJSON?.error;
                    }
                    else if(response?.responseJSON?.message){
                        message = response?.responseJSON?.message
                    }

                    Toast.fire({
                        icon: 'error',
                        title: message
                    })

                    showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                }
            });

            if(iteration == totalAppSize){

                setTimeout(function(){
                    showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                    $(".closeAppSettingButtonDiv").show();
                    return false;
                },3500);
            }
        }, finalTimer);


    }


    /****** Add or Update Version Management Form  ::  Function **********/

    $("#versionManagementForm").on('submit',(function(e) {

        $('#syncVersionUpdateResultList tbody').html('<tr></tr>');
        $('#syncVersionUpdateResultList, .closeVersionUpdateButtonDiv').hide();

        e.preventDefault();
        var Form_Data = new FormData(this);
        var validation = true;

        if(!$("#versionAccountsId").val() || ($("#versionAccountsId").val() == "-1")){
            alert("Please Select Accounts");
            validation = false;
            return false;
        }

        if(!$("#version_categories").val()){
            alert("Please Select Version Category");
            validation = false;
            return false;
        }

        if(($("#version_app_detail_id").val() == "") || ($("#version_app_detail_id").val() == "-1")){
            alert("Please Select Application");
            validation = false;
            return false;

        }

        var applications_list = $("#version_app_detail_id").val();

        if(!$("#sync_version_update_timer").val() && applications_list.length > 1){
            alert("Please Select Synchronization Timer");
            validation = false;
            return false;
        }

        var opt = [];
        opt['btnId']  = "updateAppSettingVersions";
        opt['btnText']  = "Push Updated Version to Firebase";
        opt['btnAddClass']  = "fa fa-paper-plane";
        opt['btnRemoveClass']  = "spinner-border spinner-border-sm";
        opt['resultTableId']  = "syncVersionUpdateResultList";

        showHideBtnSpinner("updateAppSettingVersions","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

        if(validation){

            var timer = $("#sync_version_update_timer").val();

            for (let i=0; i < applications_list.length; i++) {
                SyncVersionUpdateLoop(i,this,opt,applications_list.length,timer);
            }
        }
        else{
            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
        }


    }));

    /****** End of Version Management Form  **********/

    async function SyncVersionUpdateLoop(iteration,htmlForm,opt,appSize,timer){

        showHideBtnSpinner("updateAppSettingVersions","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

        var finalTimer = 1000;

        if(iteration == 0){

            finalTimer  = 1000;
        }
        else{
            finalTimer = timer *  iteration;
        }

        setTimeout(function(){

            showHideBtnSpinner("updateAppSettingVersions","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

            let totalAppSize = parseInt(appSize) - 1;

            let  applications_list = [];

            var Form_Data = new FormData(htmlForm);

            applications_list = $("#version_app_detail_id").val();

            Form_Data.set("version_app_detail_ids[]",applications_list[iteration]);

            /***********
             *
             * Paste AJAX FUNCTION HERE
             *
             * ***************/


            $.ajax({
                type:"POST",
                url: "{{ url('admin/app-settings/update-database-version') }}",
                data: Form_Data,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response){

                    var customHtml = "";
                    var successHtml = "";
                    var errorHtml = "";

                    if(response?.data?.failed.length > 0){

                        for(var i = 0 ; i < response?.data?.failed.length;i++) {
                            $('table#syncVersionUpdateResultList tbody tr:last').after('<tr><td>'+response?.data?.failed[i]?.app_detail+'</td> <td>'+response?.data?.failed[i]?.message+'</td>  <td><span class="badge badge-danger text-sm">Failed</span></td> </tr>');
                            $("table#syncVersionUpdateResultList").show();
                        }
                    }

                    if(response?.data?.success.length > 0) {

                            for (var i = 0; i < response?.data?.success.length; i++) {

                                $('table#syncVersionUpdateResultList tbody tr:last').after('<tr><td>'+response?.data?.success[i]?.app_detail+'</td> <td>'+response?.data?.success[i]?.message+'</td>  <td><span class="badge badge-success text-sm">Success</span></td> </tr>');
                                $("table#syncVersionUpdateResultList").show();

                                showHideBtnSpinner("updateAppSettingVersions","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

                                if (response?.data?.success[i]?.firebaseConfigJson != "null") {
                                    var firebaseConfig = JSON.parse(response?.data?.success[i]?.firebaseConfigJson);
                                    const app = initializeApp(firebaseConfig, response?.data?.success[i]?.appPackageId);
                                    const db = getDatabase(app);
                                    const appCheck = initializeAppCheck(app, {
                                        provider: new ReCaptchaEnterpriseProvider(response?.data?.success[i]?.reCaptchaKeyId),
                                        isTokenAutoRefreshEnabled: true // Set to true to allow auto-refresh.
                                    });

                                    const jsonData = JSON.parse(response?.data?.success[i]?.firebaseData);

                                    pushDataToRealTimeDatabase(db, response?.data?.success[i]?.node, jsonData,opt)

                                }
                            }
                        }
                        else{
//                            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                        }

                },
                dataType: 'json',
                error:function (response) {

                    var message = "Something Went Wrong!";
                    if(response?.responseJSON?.error){
                        message = response?.responseJSON?.error;
                    }
                    else if(response?.responseJSON?.message){
                        message = response?.responseJSON?.message
                    }

                    Toast.fire({
                        icon: 'error',
                        title: message
                    })

                    showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                }
            });

            if(iteration == totalAppSize){

                setTimeout(function(){
                    showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                    $(".closeVersionUpdateButtonDiv").show();
                    return false;
                },3500);
            }
        }, finalTimer);


    }

    async function SyncAccountsLoop(iteration,htmlForm,opt,appSize,timer){

            showHideBtnSpinner("pushAccountsDataSubmit","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

            var finalTimer = 1000;

            if(iteration == 0){

                finalTimer  = 1000;
            }
            else{
                finalTimer = timer *  iteration;
            }

            setTimeout(function(){

                showHideBtnSpinner("pushAccountsDataSubmit","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

                let totalAppSize = parseInt(appSize) - 1;

                let  applications_list = [];
                var Form_Data = new FormData(htmlForm);
                applications_list = $("#app_detail_id").val();
                Form_Data.set("app_detail_ids[]",applications_list[iteration]);

                $.ajax({
                    type:"POST",
                    url: "{{ url('admin/firebase/push-data') }}",
                    data: Form_Data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response){

                        var customHtml = "";
                        var successHtml = "";
                        var errorHtml = "";

                        if(response?.data?.failed.length > 0){

                            for(var i = 0 ; i < response?.data?.failed.length;i++) {
                                $('table#syncAccountsResultList tbody tr:last').after('<tr><td>'+response?.data?.failed[i]?.app_detail+'</td> <td>'+response?.data?.failed[i]?.message+'</td>  <td><span class="badge badge-danger text-sm">Failed</span></td> </tr>');
                                $("table#syncAccountsResultList").show();
                            }
                        }

                        customHtml = errorHtml;

                            if(response?.data?.success.length > 0) {

                                for (var i = 0; i < response?.data?.success.length; i++) {

                                    if (response?.data?.success[i]?.firebaseConfigJson != "null") {

                                        $('table#syncAccountsResultList tbody tr:last').after('<tr><td>'+response?.data?.success[i]?.app_detail+'</td> <td>'+response?.data?.success[i]?.message+'</td>  <td><span class="badge badge-success text-sm">Success</span></td> </tr>');
                                        $("table#syncAccountsResultList").show();

                                        showHideBtnSpinner("pushAccountsDataSubmit","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

                                        var firebaseConfig = JSON.parse(response?.data?.success[i]?.firebaseConfigJson);
                                        const app = initializeApp(firebaseConfig, response?.data?.success[i]?.appPackageId);
                                        const db = getDatabase(app);
                                        const appCheck = initializeAppCheck(app, {
                                            provider: new ReCaptchaEnterpriseProvider(response?.data?.success[i]?.reCaptchaKeyId),
                                            isTokenAutoRefreshEnabled: true // Set to true to allow auto-refresh.
                                        });

                                        const jsonData = JSON.parse(response?.data?.success[i]?.firebaseData);

                                        syncDataToRealTimeDatabase(db, response?.data?.success[i]?.node, jsonData,opt);

                                    }

                                }
                            }
                    },
                    dataType: 'json',
                    error:function (ex) {

                        Toast.fire({
                            icon: 'error',
                            title: "Something Went Wrong!",
                        })

                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                    }
                });

                if(iteration == totalAppSize){

                    setTimeout(function(){
                        console.log("RAJA G");
                        console.log(opt);
                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                        $(".closeButtonDiv").show();
                        return false;
                    },5000);
                }
            }, finalTimer);


    }

    /****** Add or Update Sync Data Form  ::  Function **********/
    $("#firebaseSynchronizationForm").on('submit',(function(e) {

        $('#syncAccountsResultList tbody').html('<tr></tr>');
        $('#syncAccountsResultList, .closeButtonDiv').hide();

        var applications_list = $("#app_detail_id").val();


        e.preventDefault();
        var Form_Data = new FormData(this);
        var validation = true;

        if(!$("#account_filter").val() || ($("#account_filter").val() == "-1")){
            alert("Please Select Accounts");
            validation = false;
            return false;
        }

        if(!$("#synchronization_type").val()){
            alert("Please Select Synchronization Type");
            validation = false;
            return false;
        }

        if(!$("#sync_accounts_timer").val() && applications_list.length > 1){
            alert("Please Select Synchronization Timer");
            validation = false;
            return false;
        }

        if(($("#app_detail_id").val() == "") || ($("#app_detail_id").val() == "-1")){
            alert("Please Select Application");
            validation = false;
            return false;
        }

        var opt = [];
        opt['btnId']  = "pushAccountsDataSubmit";
        opt['btnText']  = "Push Data to Firebase";
        opt['btnAddClass']  = "fa fa-paper-plane";
        opt['btnRemoveClass']  = "spinner-border spinner-border-sm";
        opt['resultTableId']  = "syncAccountsResultList";

        showHideBtnSpinner("pushAccountsDataSubmit","Loading...","spinner-border spinner-border-sm","fa fa-paper-plane",true);

        if(validation){

            var timer = $("#sync_accounts_timer").val();

            for (let i=0; i < applications_list.length; i++) {
                SyncAccountsLoop(i,this,opt,applications_list.length,timer);
            }
        }
        else{
            showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
        }

    }));

    async function pushDataToRealTimeDatabase(db,node,AppSettings,opt) {
        
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        set(ref(db), {
            AppSettings
        })
            .then(() => {

                Toast.fire({
                    icon:  "success",
                    title: 'Data has been pushed successfully!'
                })

                console.log("Success!");
                // showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
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

    async function syncDataToRealTimeDatabase(db,node,jsonData,opt) {

        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        switch(node) {
            case 'AppDetails':
                const AppDetails = jsonData;
                set(ref(db), {
                    AppDetails
                })
                .then(() => {

                    Toast.fire({
                        icon: "success",
                        title: "Data has been pushed successfully"
                    });

                })
                .catch((error) => {

                    Toast.fire({
                        icon:  "error",
                        title: 'Synchronization Failed due to ' + error
                    });

                    showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                });

                break;

            case 'Leagues':

                let Leagues = jsonData;

                set(ref(db), {
                    Leagues
                })
                    .then(() => {

                        Toast.fire({
                            icon: "success",
                            title: "Data has been pushed successfully"
                        })

                    })
                    .catch((error) => {

                        Toast.fire({
                            icon:  "error",
                            title: 'Synchronization Failed due to ' + error
                        });

                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);
                    });

                break;
            case "Schedules":

                const Schedules = jsonData;

                set(ref(db), {
                    Schedules
                })
                    .then(() => {

                        Toast.fire({
                            icon: "success",
                            title: "Data has been pushed successfully"
                        })

                        console.log("Success!");

                    })
                    .catch((error) => {

                        Toast.fire({
                            icon:  "error",
                            title: 'Synchronization Failed due to ' + error
                        });

                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                    });
                // code block
                break;

            case "Servers":

                const Servers = jsonData;
                set(ref(db), {
                    Servers
                })
                    .then(() => {

                        Toast.fire({
                            icon: "success",
                            title: "Data has been pushed successfully"
                        })

                        console.log("Success!");

                    })
                    .catch((error) => {

                        Toast.fire({
                            icon:  "error",
                            title: 'Synchronization Failed due to ' + error
                        });

                        showHideBtnSpinner(opt.btnId,opt.btnText,opt.btnAddClass,opt.btnRemoveClass,false);

                    });
                break;
            default:
            // code block



        }
    }


</script>



@endpush
