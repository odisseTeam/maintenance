@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title').' '.__('maintenance::dashboard.maintenance_dashboard'))


@section('css')

    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <style>

        * {
            box-sizing: border-box;
        }

        /* Create three unequal columns that floats next to each other */
        .column {
            float: left;
            padding: 10px;
            height: 300px; /* Should be removed. Only for demonstration */
        }

        .left, .middle {
            width: 35%;
        }

        .right {
            width: 20%;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
@endsection

@section('content')

    @if(session('error'))
        @component('components.alert')

        @endcomponent
    @endif
    @if(session('success'))
        <div class="box-body">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa-solid fa-check"></i>{{session('success')}}</p>
            </div>
        </div>
    @endif

    @if( isset($errors) && $errors->any() )
        <div class="box-body">

            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                @foreach( $errors->all() as $error )
                    <p><i class="icon fa-solid fa-ban"></i>{{$error}}</p>
                @endforeach
            </div>
        </div>

    @endif

    <div class="box-body" id="msg_box" hidden>
        <div class="alert alert-success alert-dismissible" id="msg_box_inner">
            {{--                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>--}}
            <div id="ajx_msg"></div>
        </div>
    </div>



    {{--    <section class="content-header">--}}
    <section>
        <div class="box">
            <div class="box-header">
                <h1>
                    {{__('maintenance::dashboard.maintenance_dashboard')}}
                </h1>
            </div>
            <div class="box-body">


                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3>{{__('incident.recently_incidents_per_priority')}}</h3>

                                <div>
                                    <div class="box-body" id="div_priority_pieChart">
                                        <canvas id="priorityPieChart" style="height:250px"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3>{{__('incident.recently_incidents_of_sites')}}</h3>

                                <div>
                                    <div class="box-body" id="div_pieChart">
                                        <canvas id="pieChart" style="height:250px"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3>{{__('incident.incidents_per_emergency_services')}}</h3>

                            </div>
                            <div>
                                <div class="box-body" id="div_barChart">
                                    <canvas id="barChart" style="height:250px"></canvas>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3>{{__('incident.incidents_count_per_status')}}</h3>

                            </div>
                            <div>
                                <div class="box-body" id="div_incidentCountBarChart">
                                    <canvas id="incidentCountBarChart" style="height:250px"></canvas>
                                </div>
                            </div>


                        </div>

                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3>{{__('incident.shortcuts')}}</h3>

                            </div>
                            <div class="box-body">
                                <div style="margin-bottom: 30px"><a href="/incident_management/search">
                                        <button class="btn btn-primary"
                                                style="width:150px">{{__('incident.search_all_incidents')}}</button>
                                    </a></div>
                                <div style="margin-bottom: 30px"><a href="/incident_management/notes/search">
                                        <button class="btn btn-primary"
                                                style="width: 150px">{{__('incident.search_notes')}}</button>
                                    </a></div>
                                <div style="margin-bottom: 30px"><a href="/incident_management/files/search">
                                        <button class="btn btn-primary"
                                                style="width: 150px">{{__('incident.search_uploaded_files')}}</button>
                                    </a></div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-xs-12">
                    <div class="box col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="box-header">
                            <h3>{{__('maintenance::dashboard.maintenances_list')}}</h3>
                        </div>
                        <div class="box-body table-responsive no-padding">
                            <table id="maintenances_table" class="table table-bordered table-hover dataTable text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('maintenance::dashboard.category')}}</th>
                                    <th>{{__('maintenance::dashboard.title')}}</th>
                                    <th>{{__('maintenance::dashboard.priority')}}</th>
                                    <th>{{__('maintenance::dashboard.status')}}</th>
                                    <th>{{__('maintenance::dashboard.task_report_date')}}</th>
                                    <th>{{__('maintenance::dashboard.task_start_date')}}</th>
                                    <th>{{__('maintenance::dashboard.task_end_date')}}</th>
                                    <th>{{__('maintenance::dashboard.staff_reporter')}}</th>
                                    <th>{{__('maintenance::dashboard.resident_reporter')}}</th>
                                    <th>{{__('maintenance::dashboard.operation')}}</th>


                                </tr>
                                </thead>


                                <tbody id="maintenance_tbl_body"
                                       class="table table-bordered table-hover dataTable text-center">

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('maintenance::dashboard.category')}}</th>
                                    <th>{{__('maintenance::dashboard.title')}}</th>
                                    <th>{{__('maintenance::dashboard.priority')}}</th>
                                    <th>{{__('maintenance::dashboard.status')}}</th>
                                    <th>{{__('maintenance::dashboard.task_report_date')}}</th>
                                    <th>{{__('maintenance::dashboard.task_start_date')}}</th>
                                    <th>{{__('maintenance::dashboard.task_end_date')}}</th>
                                    <th>{{__('maintenance::dashboard.staff_reporter')}}</th>
                                    <th>{{__('maintenance::dashboard.resident_reporter')}}</th>
                                    <th>{{__('maintenance::dashboard.operation')}}</th>
                                </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>
                </div>

            </div>

        </div>


    </section>



@endsection

@section('script')
    <script src="{{ asset('resources/Chart.js/Chart.bundle.min.js') }}"></script>


    {{--    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>--}}
    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>

    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script>

        $(document).ready(function () {

            loadAllContractors();

        });


        function loadAllMaintenances(){

            var spinHandle = loadingOverlay.activate();

            send( '/maintenance/maintenances_list',  {
            }, 'handleMaintenanceTableBody', []);

            }
            ///////////////////////////////////////////////////////

            function handleMaintenanceTableBody(){

                let maintenance_list = return_value.maintenances;
                let res = return_value.code;
                let message = return_value.message;
                if(res == "failure"){

                    $('#maintenances_table').DataTable().clear().destroy();

                    x=message;
                    if(typeof message == "object"){
                        x="";
                        //var messages = get_object_vars(message);
                        var messages2 = Object.values(message);
                        for(var i=0;i<messages2.length;i++){
                            x=x+messages2[i];
                        }

                    }


                }
                else if(maintenance_list != null && maintenance_list !="undefined"){

                    var htmlValue = "";
                    Object.keys(maintenance_list).forEach(function(k){

                        var counter = 1+parseInt(k);


                        var id_maintenance_job = maintenance_list[k]["id_maintenance_job"];
                        var category = maintenance_list[k]["category"];
                        var title = maintenance_list[k]["title"];
                        var priority = maintenance_list[k]["priority"];
                        var status = maintenance_list[k]["status"];
                        var job_report_date_time = maintenance_list[k]["job_report_date_time"];
                        var job_start_date_time = maintenance_list[k]["job_start_date_time"];
                        var job_finished_date_time = maintenance_list[k]["job_finished_date_time"];
                        var staff_reporter = maintenance_list[k]["staff_reporter"]?maintenance_list[k]["staff_reporter"]:'-';
                        var resident_reporter = maintenance_list[k]["resident_reporter"]? maintenance_list[k]["resident_reporter"]:'-';

                        var operation = '<a href="/maintenance/contractor/' + id_contractor + '" target="_blank" data-toggle="tooltip" title="Edit Contractor" data-original-title="EDit Contractor">' +
                            '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" id="edit' + counter + '" >' +
                            '<i class="fa-solid fa-edit" aria-hidden="true"></i></button>' +
                            '</a>' +
                            '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Login Settings" data-toggle="modal" data-target="#showLoginSettingsModal">'+
                            '<i class="fa-solid fa-cogs"></i>'+
                            '</button>'+

                            '<button style="margin-right: 1px;" type="button" class="btn btn-danger allign-btn" title="Delete Contractor" onclick="showDeleteContractorModal('+id_contractor+')">'+
                            '<i class="fa-solid fa-trash"></i>'+
                            '</button>'

                            ;


                        htmlValue= htmlValue +"<tr><td>"+(counter)+"</td><td>"+category+"</td><td>"+title+"</td><td>"
                            +priority+"</td><td>"+status+"</td><td>"+job_report_date_time+"</td><td>"+job_start_date_time+"</td><td>"+job_finished_date_time+"</td><td>"+staff_reporter+"</td><td>"+resident_reporter+"</td><td>"+operation+"</td></tr>";


                    });



                    $('#maintenances_table').DataTable().clear().destroy();
                    $('#maintenances_table #maintenance_tbl_body').html('');
                    $('#maintenances_table #maintenance_tbl_body').append(htmlValue);
                    $('#maintenances_table tfoot th').each( function () {
                        //var title = $(this).text();
                        //$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                    } );

                //datatable
                var table = $('#maintenances_table').DataTable({
                    'paging'      : true,
                    'lengthChange': true,
                    'searching'   : true,
                    'ordering'    : true,
                    'info'        : true,
                    'autoWidth'   : true,
                    "aoColumnDefs": [

                        { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3,4,5,6,7] }
                    ]
                });


                // Apply the search
                table.columns().every( function () {
                    var that = this;

                    $( 'input', this.footer() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    } );
                } );


                }



                loadingOverlay.cancelAll();

            }

    </script>


@endsection
