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
                                    <h4 class="">Edit App Setting</h4>
                                </div>

                            </div>

                        </div>
                        <div class="card-body">
                            <form autocomplete="off" action="javascript:void(0)" id="addEditForm" name="addEditForm" class="form-horizontal" method="POST" enctype="multipart/form-data">

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Application</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="app_detail_id" id="app_detail_id" required>
                                            <option value="">   Select </option>
                                            @foreach($appsList as $obj)
                                                <option value="{{$obj->id}}" {{($obj->id == $appData->app_detail_id) ? 'selected' : '' }}>   {{   $obj->appName . ' - '. $obj->packageId }}    </option>
                                            @endforeach
                                        </select>

                                        <span class="text-danger" id="app_detail_idError"></span>

                                     </div>


                                    <label for="staticEmail" class="col-sm-2 col-form-label">Stream Key</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="streamKey" id=streamKey" value="{{$appData->streamKey}}"  required>
                                    </div>



                                </div>

                                <div class="form-group row">

                                    <label for="appAuthKey1" class="col-sm-2 col-form-label" id="">Auth Key 1</label>
                                    <div class="col-sm-4">
                                        <input type="text"  class="form-control" name="appAuthKey1" id="appAuthKey1" value="{{$appData->appAuthKey1}}" onkeyup="$('#appAuthKey1Error').text('')"  required />
                                        <span class="text-danger" id="appAuthKey1Error"></span>
                                    </div>


                                    <label for="appAuthKey2" class="col-sm-2 col-form-label" id="">Auth Key 2</label>
                                    <div class="col-sm-4">
                                        <input type="text"  class="form-control" name="appAuthKey2" id="appAuthKey2" value="{{$appData->appAuthKey2}}" onkeyup="$('#appAuthKey2Error').text('')"  required />
                                        <span class="text-danger" id="appAuthKey2Error"></span>
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">App Detail Database Version</label>
                                    <div class="col-sm-4">
                                        <input type="text" maxlength="3" class="form-control notAllowedAlphabets" name="appDetailsDatabaseVersion" id="appDetailsDatabaseVersion"
                                               value="{{$appData->appDetailsDatabaseVersion}}" required>
                                    </div>


                                    <label for="staticEmail" class="col-sm-2 col-form-label">Leagues Database Version</label>
                                    <div class="col-sm-4">
                                        <input type="text" value="{{$appData->leaguesDatabaseVersion}}"  maxlength="3" class="form-control notAllowedAlphabets" name="leaguesDatabaseVersion" id="leaguesDatabaseVersion" required>
                                    </div>



                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Schedules Database Version</label>
                                    <div class="col-sm-4">
                                        <input type="text" value="{{$appData->schedulesDatabaseVersion}}"  maxlength="3"  class="form-control notAllowedAlphabets" name="schedulesDatabaseVersion" id="schedulesDatabaseVersion" required>
                                    </div>



                                    <label for="staticEmail" class="col-sm-2 col-form-label">Servers Database Version</label>
                                    <div class="col-sm-4">
                                        <input type="text"  value="{{$appData->serversDatabaseVersion}}"   maxlength="3" class="form-control notAllowedAlphabets" name="serversDatabaseVersion" id="serversDatabaseVersion"  required>
                                    </div>



                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Server Api Base Url</label>
                                    <div class="col-sm-4">
                                        <input type="text"  value="{{$appData->serverApiBaseUrl}}"   class="form-control" name="serverApiBaseUrl" id="serverApiBaseUrl" required>
                                    </div>



                                </div>


                                <div class="form-group row">


                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is App Clear Cache</label>
                                    <div class="col-sm-4 pt-2">
                                        <label for="isAppClearCache1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppClearCache1" name="isAppClearCache" value="1"   {{($appData->isAppClearCache) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppClearCache0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppClearCache0" name="isAppClearCache" value="0"   {{(!$appData->isAppClearCache) ? 'checked' : ''}}  />
                                            <span class="">No</span>
                                        </label>

                                    </div>


                                    <label for="appCacheId" class="col-sm-2 col-form-label">Cache Id</label>
                                    <div class="col-sm-4">

                                        <input type="text" class="form-control notAllowedAlphabets" name="appCacheId" id="appCacheId" value="{{$appData->appCacheId}}"  required >

                                    </div>



                                </div>

                                <div class="form-group row">



                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is App Clear Shared Pref</label>
                                    <div class="col-sm-4 pt-2">

                                        <label for="isAppClearSharedPref1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppClearSharedPref1" name="isAppClearSharedPref" value="1" {{($appData->isAppClearSharedPref) ? 'checked' : ''}}   />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppClearSharedPref0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppClearSharedPref0" name="isAppClearSharedPref" value="0"  {{(!$appData->isAppClearSharedPref) ? 'checked' : ''}} />
                                            <span class="">No</span>
                                        </label>

                                    </div>



                                    <label for="staticEmail" class="col-sm-2 col-form-label">App Shared Pref. Id</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control notAllowedAlphabets" name="appSharedPrefId" id="appSharedPrefId" value="{{$appData->appSharedPrefId}}" required>
                                    </div>


                                </div>

                                <div class="form-group row">

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is App Details Database Save</label>
                                    <div class="col-sm-4 pt-2">

                                        <label for="isAppDetailsDatabaseSave1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppDetailsDatabaseSave1" name="isAppDetailsDatabaseSave" value="1" {{($appData->isAppDetailsDatabaseSave) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppDetailsDatabaseSave1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppDetailsDatabaseSave0" name="isAppDetailsDatabaseSave" value="0" {{(!$appData->isAppDetailsDatabaseSave) ? 'checked' : ''}}  />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is App Details Database Clear</label>
                                    <div class="col-sm-4 pt-2">

                                        <label for="isAppDetailsDatabaseClear1" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppDetailsDatabaseClear1" name="isAppDetailsDatabaseClear" value="1"  {{($appData->isAppDetailsDatabaseClear) ? 'checked' : ''}} />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isAppDetailsDatabaseClear0" class="cursor-pointer">
                                            <input type="radio" class="" id="isAppDetailsDatabaseClear0" name="isAppDetailsDatabaseClear" value="0"  {{(!$appData->isAppDetailsDatabaseClear) ? 'checked' : ''}} />
                                            <span class="">No</span>
                                        </label>

                                    </div>

                                </div>


                                <div class="form-group row">


                                    <label for="staticEmail" class="col-sm-2 col-form-label">Is Firebase Database Access</label>
                                    <div class="col-sm-4 pt-2">
                                        <label for="isFirebaseDatabaseAccess1" class="cursor-pointer">
                                            <input type="radio" class="" id="isFirebaseDatabaseAccess1" name="isFirebaseDatabaseAccess" value="1"  {{($appData->isFirebaseDatabaseAccess) ? 'checked' : ''}}  />
                                            <span class="">Yes</span>
                                        </label>

                                        <label for="isFirebaseDatabaseAccess0" class="cursor-pointer">
                                            <input type="radio" class="" id="isFirebaseDatabaseAccess0" name="isFirebaseDatabaseAccess" value="0" {{(!$appData->isFirebaseDatabaseAccess) ? 'checked' : ''}}  />
                                            <span class="">No</span>
                                        </label>
                                    </div>
                                </div>


                                <div class="form-group row">

                                    <div class="col-sm-12 text-right">
                                        <input type="submit" class="btn bg-dark vertical-bottom" name="submit" id=submitApp"  value="Update" />
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

                $("input[type=submit]").html('Please Wait...');
                $("input[type=submit]").attr("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('admin/add-update-app_settings/'.$app_setting_id) }}",
                    data: Form_Data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {

                        $('#ajax-model').modal('hide');
                        $("input[type=submit]").html('Save');
                        $("input[type=submit]").attr("disabled", false);

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

                        Toast.fire({
                            icon: iconToast,
                            title: message
                        })

						if(res.status == 'success' || res.firebase_status == 'success'){
							setTimeout(function(){
								 window.location.href = "{{ url('admin/app_settings')}}";
							},850);
						}

                    },
                    error: function (response) {

                        $("input[type=submit]").html('Save');

                        $("input[type=submit]").attr("disabled", false);

                        var resp = response.responseJSON;

                        if(response.status == 422){

                            $('html, body').animate({
                                scrollTop: eval($("#PackageIdError").offset().top - 170)
                            }, 1000);

                            $("#app_detail_idError").text(resp.errors.app_detail_id);

                            Toast.fire({
                                icon: 'error',
                                title: resp.message
                            })

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
                                title: resp?.message
                            })

                        }



                    }
                });

            }));


        });



    </script>

@endpush
