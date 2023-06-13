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
                  <h3>{{getTotalAccounts()}}</h3>

                <p>Total Accounts</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{url('/admin/accounts')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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


        <!-- APPLICATION LIST  -->

        <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">

                        <div class="card-body">

                            <div class="row">
                                <div class="col-3 pull-right text-right">
                                    <button type="button" onclick="loadApplicationsCardView()" class="d-none btn btn-primary" id="filter"> <i class="fa fa-filter"></i> Apply Filter </button>

                                    <select class="form-control" id="account_filter" name="account_filter" onchange="setFilterDefaultValue();loadApplicationsCardView()" >
                                        <option value="-1" selected>   Select Accounts </option>
                                        @foreach ($accountsList as $obj)
                                            <option value="{{ $obj->id }}"  {{ (isset($obj->id) && old('id')) ? "selected":"" }}>{{ $obj->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-9 pull-right text-right">
                                    <label>
                                        <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Search" >
                                    </label>
                                </div>
                            </div>

                            <div class="row mt-4" id="applications_container">

                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <!-- /.row -->


        <!-- END OF  APPLICATION LIST  -->





     </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
 
@endsection

@push('scripts')

<script type="text/javascript">

    $(document).ready(function($){

        $("#searchBox").on('keyup',function(e){
            loadApplicationsCardView();
        })

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        loadApplicationsCardView();

        $(document).on('click', '.pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetchData(page);
            return false;
        });

    });



    var isFilterChange = false;
    function setFilterDefaultValue(){
        isFilterChange = true;
    }

    function fetchData(page)
    {
        $.ajax({
            url:"pagination/applications/fetch_data?page="+page+"&accountsId="+$("#account_filter").val()+"&searchKeywords="+$("#searchBox").val(),
            success:function(data)
            {
                $("#applications_container").html(data);                }
        });
    }

    function loadApplicationsCardView(){
        if(isFilterChange){
            $("#searchBox").val('');
            isFilterChange = false;
        }

        $('#cover-spin').show();

        $.ajax({

            type:"POST",
            url: "{{ url('admin/apps-card-view') }}",
            data: { accountsId: $("#account_filter").val() , searchKeywords : $("#searchBox").val()},
            success: function(response){
                $('#cover-spin').hide();
                $("#applications_container").html(response);

                if($("#totalAppsCount").val() > 0){
                    $("#deleteAllButton").show();
                }
                else{
                    $("#deleteAllButton").hide();
                }
            }
        });

    }  
</script>

@endpush