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
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="DataTbl">
                                <thead>
                                <tr>
                                    <th scope="col" width="10px">#</th>
                                    <th scope="col">Country Code</th>
                                    <th scope="col">Country Name</th>
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

    </section>
    <!-- /.content -->


@endsection

@push('scripts')
    <script type="text/javascript">

        var Table_obj = "";

        function callDataTableWithFilters()
        {
            $("#master").prop('checked',false);

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
                columnDefs: [
                    { targets: '_all',
                        orderable: true
                    },
                ],
                serverSide: true,
                ajax: "{{ url('admin/fetch-country-data') }}",
                columns: [
                    { data: 'srno', name: 'srno' , searchable:false},
                    { data: 'country_code', name: 'country_code' },
                    { data: 'country_name', name: 'country_name' },
                ],
                order: [[0, 'ASC']],
            });

        }

        $(document).ready(function($){

            callDataTableWithFilters();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        }); // end document ready!

    </script>

@endpush
