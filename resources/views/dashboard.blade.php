@extends('layouts.master')

@section('content')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{get_server_memory_usage()}}
                    <sup style="font-size: 20px">%</sup></h3>
                  </h3>

                <p>RAM Usage</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
                <a href="#" class="small-box-footer"> &nbsp; &nbsp; &nbsp; </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>
                    {{ getServerLoad() }}
                    <sup style="font-size: 20px">%</sup></h3>

                <p>CPU Load</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer"> &nbsp; &nbsp; &nbsp; </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                  <h3>{{getTotalSports()}}</h3>

                <p>Total Sports</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{url('/admin/sports')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{getTotalApp()}}</h3>

                <p>Total Apps</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="{{url('/admin/app')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->

          <div class="row">
              <div class="col-md-12">

                  <div class="card">
                      <div class="card-header" style="padding-top: 0.75em;padding-bottom: 0.75em;">
                          <div class="row">
                              <div class="col-sm-12 text-left">
                                  <h2 class="card-title text-bold"> Schedules </h2>
                              </div>
                          </div>
                      </div>
                      <div class="card-body">

                          <div class="row">
                              <div class="col-sm-12">

                                  <div class="row">

                                      <div class="col-sm-3 mb-3">
                                          <select class="form-control" id="sports_filter" name="sports_filter" onchange="getLeaguesOptionBySports(this.value,'leagues_filter');$('button#filter').trigger('click');">
                                              <option value="">   Select Sports </option>
                                              <option value="-1">   All </option>
                                              @if(!empty($sportsList))
                                                  @foreach ($sportsList as $obj)
                                                      <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                                  @endforeach
                                              @endif
                                          </select>

                                      </div>
                                      <div class="col-sm-3 mb-3">
                                          <select class="form-control" id="leagues_filter" name="leagues_filter" onchange="$('button#filter').trigger('click');" >
                                              <option value="">   Select League </option>
                                              <option value="-1">   All </option>
                                              @if(!empty($leaguesList))
                                                  @foreach ($leaguesList as $obj)
                                                      <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                                  @endforeach
                                              @endif
                                          </select>

                                      </div>

                                      <div class="col-sm-2 visiblilty-hidden">

                                          <button type="button" class="btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>
                                      </div>

                                  </div>

                              </div>

                              <div class="col-sm-12">

                                  <ul class="nav nav-tabs schedule-nav-tabs" id="custom-content-above-tab" role="tablist">

                                      <li class="nav-item">
                                          <a class="nav-link active" id="live-schedules-tab" data-toggle="pill" href="#live-schedules" role="tab" aria-controls="live-schedules" aria-selected="false">  Live  Schedules </a>
                                      </li>
                                      <li class="nav-item">
                                          <a class="nav-link" id="upcoming-schedules-tab" data-toggle="pill" href="#upcoming-schedules" role="tab" aria-controls="upcoming-schedules" aria-selected="false">Upcoming Schedules</a>
                                      </li>

                                      <li class="nav-item">
                                          <a class="nav-link" id="previous-schedules-tab" data-toggle="pill" href="#previous-schedules" role="tab" aria-controls="previous-schedules"   aria-selected="true">Previous Schedules</a>
                                      </li>
                                  </ul>

                                  <div class="tab-content" id="custom-content-above-tabContent">
                                      <div class="tab-pane fade show pt-2 active" id="live-schedules" role="tabpanel" aria-labelledby="live-schedules-tab">

                                          <!-------------- LIVE SCHDEULES TABLE ------------->
                                          <table class="table table-bordered table-hover mt-3" id="live-DataTbl">
                                              <thead>
                                              <tr>
                                                  <th scope="col" width="5px">#</th>
                                                  <th scope="col">Total Apps</th>
                                                  <th scope="col">Name</th>
                                                  <th scope="col">Sports</th>
                                                  <th scope="col">League</th>
                                                  <th scope="col">Home</th>
                                                  <th scope="col">Away</th>
                                                  <th scope="col">Score</th>
                                                  <th scope="col">Start(DateTime)</th>
                                                  <th scope="col">Live</th>
                                                  <th scope="col">isSponsorAd</th>
                                                  <th scope="col">sponsorAdClickUrl</th>
                                                  <th scope="col">sponsorAdImageUrl</th>
                                              </tr>
                                              </thead>
                                              <tbody>

                                              </tbody>
                                          </table>

                                      </div>
                                      <div class="tab-pane fade show pt-2" id="upcoming-schedules" role="tabpanel" aria-labelledby="upcoming-schedules-tab">

                                          <!-------------- UPCOMING SCHDEULES TABLE ------------->
                                          <table class="table table-bordered table-hover mt-3" id="DataTbl">
                                              <thead>
                                              <tr>
                                                  <th scope="col" width="5px">#</th>
                                                  <th scope="col">Total Apps</th>
                                                  <th scope="col">Name</th>
                                                  <th scope="col">Sports</th>
                                                  <th scope="col">League</th>
                                                  <th scope="col">Home</th>
                                                  <th scope="col">Away</th>
                                                  <th scope="col">Score</th>
                                                  <th scope="col">Start(DateTime)</th>
                                                  <th scope="col">Live</th>
                                                  <th scope="col">isSponsorAd</th>
                                                  <th scope="col">sponsorAdClickUrl</th>
                                                  <th scope="col">sponsorAdImageUrl</th>
                                              </tr>
                                              </thead>
                                              <tbody>

                                              </tbody>
                                          </table>

                                      </div>


                                      <div class="tab-pane fade show pt-2 " id="previous-schedules" role="tabpanel" aria-labelledby="previous-schedules-tab">

                                          <!-------------- PREVIOUS SCHDEULES TABLE ------------->
                                          <table class="table table-bordered table-hover mt-3" id="previous-DataTbl">
                                              <thead>
                                              <tr>
                                                  <th scope="col" width="5px">#</th>
                                                  <th scope="col">Total Apps</th>
                                                  <th scope="col">Name</th>
                                                  <th scope="col">Sports</th>
                                                  <th scope="col">League</th>
                                                  <th scope="col">Home</th>
                                                  <th scope="col">Away</th>
                                                  <th scope="col">Score</th>
                                                  <th scope="col">Start(DateTime)</th>
                                                  <th scope="col">Live</th>
                                                  <th scope="col">isSponsorAd</th>
                                                  <th scope="col">sponsorAdClickUrl</th>
                                                  <th scope="col">sponsorAdImageUrl</th>
                                              </tr>
                                              </thead>
                                              <tbody>

                                              </tbody>
                                          </table>

                                      </div>
                                  </div>


                              </div>

                          </div>

                      </div>

                      </div>

              </div>

          </div>

        <!-- /.row -->

          <!-- boostrap model -->
          <div class="modal fade" id="scheduledAppsModal" aria-hidden="true" data-backdrop="static">
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h4 class="modal-title">Schedules Applications</h4>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                      </div>
                      <div class="modal-body" >
                          <div class="row" id="schedules_applications_container" style="margin: 0 3% !important;">
                              Fahad
                          </div>

                      </div>
                  </div>
              </div>
          </div>
          <!-- end bootstrap model -->


     </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection

@push('scripts')
    <script>




        $('#filter').click(function(){
            var sports_filter = $('#sports_filter').val();
            var leagues_filter = $('#leagues_filter').val();

            if(sports_filter != '' || leagues_filter != '')
            {
                $('#DataTbl').DataTable().destroy();
                if(sports_filter != '-1'){ // for all...
                    getDashboardSchedules(sports_filter,leagues_filter);
                }
                else{
                    getDashboardSchedules();
                }
            }
            else
            {
                // alert('Select Filter Option');
                $('#DataTbl').DataTable().destroy();
                getDashboardSchedules();
            }
        });


        $(".nav.schedule-nav-tabs li a.nav-link").on('click',function () {

            setTimeout(function(){
                callDataTableWithFilters();
            },500);
        });

        var Table_obj = "";
        function getDashboardSchedules(filter_sports = "" , filter_leagues = "")
        {

            var activeTabId = $("#custom-content-above-tabContent .tab-pane.active").attr("id");


            if(activeTabId == "upcoming-schedules"){

                var table_id = "DataTbl";
            }
            else if(activeTabId == "previous-schedules"){
                var table_id = "previous-DataTbl";
            }
            else {
                var table_id = "live-DataTbl";
            }

            if(Table_obj != '' && Table_obj != null)
            {
                $('#'+table_id).dataTable().fnDestroy();
                $('#'+table_id+' tbody').empty();
                Table_obj = '';
            }

            Table_obj = $('#'+table_id).DataTable({
                processing: true,
                columnDefs: [
                    { targets: '_all',
                        orderable: true
                    },
                ],
                serverSide: true,
                "ajax" : {
                    url:"{{ url('admin/get-schedule-list') }}",
                    type:"POST",
                    data:{
                        filter_sports : filter_sports, filter_league : filter_leagues, active_tab : activeTabId
                    },
                    dataSrc: function ( json ) {

                        //Make your callback here.
                        setTimeout(function () {
                            $("input[data-bootstrap-switch]").each(function(){
                                $(this).bootstrapSwitch('state', $(this).prop('checked'));
                            });
                        },600);


                        return json.data;
                    }
                },
                columns: [
                    { data: 'srno', name: 'srno' },
                    { data: 'appData', name: 'appData', searchable:false , render: function( data, type, full, meta,rowData ) {
                            if(full.appData.totalApps > 0 && full.appData.isShowApps){
                                var className = "viewScheduledApps";
                            }
                            return "<a href='javascript:void(0)' data-id='"+ full.appData.schedule_id +"' class='"+className+" badge badge-info text-white text-xs text-capitalize'>"+ full.appData.totalApps + "</a>" +" ";
                        }},
                    { data: 'scheduleName', name: 'scheduleName'},
                    { data: 'sportsName', name: 'sportsName'},
                    { data: 'league', name: 'league'},
                    { data: 'home_team_id', name: 'home_team_id'},
                    { data: 'away_team_id', name: 'away_team_id' },
                    { data: 'score', name: 'score' },
                    { data: 'start_time', name: 'start_time', render: function( data, type, full, meta,rowData ) {

                            return convertTime24to12(data)

                        }

                    },

                    { data: 'is_live', name: 'is_live', searchable:false , render: function( data, type, full, meta,rowData ) {

                            if(data=='Yes'){
                                return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                            else{
                                return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                        },


                    },

                    { data: 'isSponsorAd', name: 'isSponsorAd', searchable:false , render: function( data, type, full, meta,rowData ) {

                            if(data=='Yes'){
                                return "<a href='javascript:void(0)' class='badge badge-success text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                            else{
                                return "<a href='javascript:void(0)' class='badge badge-danger text-xs text-capitalize'>"+data+"</a>" +" ";
                            }
                        },


                    },

                    { data: 'sponsorAdClickUrl', name: 'sponsorAdClickUrl' },

                    { data: 'sponsorAdImageUrl', name: 'sponsorAdImageUrl' },

                ],
                order: [[6, 'asc']]
            });


            setTimeout(function(){


            },1500);
        }

        function callDataTableWithFilters(){
            getDashboardSchedules($('#sports_filter').val(),$('#leagues_filter').val());
        }

        $(document).ready(function($) {
            getDashboardSchedules();
        });

        $(document).on('click','.viewScheduledApps',function() {
            let scheduleId = $(this).attr('data-id');
            loadScheduledAppsByScheduleId(scheduleId);
        });

        function loadScheduledAppsByScheduleId(scheduleId){
            $.ajax({
                type:"POST",
                url: "{{ url('admin/schedules-apps-card-view') }}",
                data: { scheduleId: scheduleId},
                success: function(response){
                    $("#schedules_applications_container").html(response);
                    $("#scheduledAppsModal").modal('show');

                }
            });
        }


    </script>
    @endpush

