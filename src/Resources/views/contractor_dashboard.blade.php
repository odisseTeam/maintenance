@extends('layouts.blank_js')
{{-- @extends('adminlte.layouts.sdr') --}}


@section('css')
<!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="../files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/pages/data-table/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="../files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" href="../files/bower_components/select2/css/select2.min.css">



    <style>
        .select2-selection--multiple {
            border: 0px;
        }

        .select2-container--default {
            border: 0px;
        }

        .select2-container{
            min-width: 202px!important;
        }


    </style>

@endsection

@section('content')






    <!-- [ navigation menu ] end -->
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-home bg-c-blue sdr-primary"></i>
                        <div class="d-inline">
                            <h5>{{__('maintenance::contractor.contractor_dashboard')}}</h5>
                            <span>{{__('maintenance::contractor.contractor_dashboard')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="page-header-breadcrumb">
                        <ul class=" breadcrumb breadcrumb-title breadcrumb-padding">
                            <li class="breadcrumb-item">
                                <a href="index.html"><i class="feather icon-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="#!">{{__('maintenance::contractor.contractor_dashboard')}}</a> </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-body">
                        <!-- [ page content ] start -->
                        <div class="row">

                            <section>
                                <div class="box card">
                                    <div class="box-header card-header">
                                        <h3>{{__('maintenance::contractor.contractor_dashboard')}}</h3>

                                    </div>
                                    <div class="box-body card-block">



                                        <div class="col-xs-12">
                                            <div class="box col-lg-12 col-md-12 col-sm-12 col-xs-12 card">



                                                <div class="box-body card-block">

                                                    <div class="row table-responsive no-padding">
                                                        <table id="maintenances_table" class="table table-bordered table-hover dataTable text-center">
                                                            <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{__('maintenance::maintenance_mgt.business')}}</th>
                                                                <th>{{__('maintenance::dashboard.category')}}</th>
                                                                <th>{{__('maintenance::dashboard.title')}}</th>
                                                                <th>{{__('maintenance::dashboard.sla_remain_time')}}</th>
                                                                <th>{{__('maintenance::dashboard.priority')}}</th>
                                                                <th>{{__('maintenance::dashboard.status')}}</th>
                                                                <th>{{__('maintenance::dashboard.task_report_date')}}</th>
                                                                <th>{{__('maintenance::dashboard.task_start_date')}}</th>
                                                                <th>{{__('maintenance::dashboard.task_end_date')}}</th>
                                                                <th>{{__('maintenance::dashboard.staff_reporter')}}</th>
                                                                <th>{{__('maintenance::dashboard.resident_reporter')}}</th>
                                                                <th>{{__('maintenance::dashboard.assigned_user_contractor')}}</th>


                                                            </tr>
                                                            </thead>


                                                            <tbody id="maintenance_tbl_body"
                                                                class="table table-bordered table-hover dataTable text-center">

                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{__('maintenance::maintenance_mgt.business')}}</th>
                                                                <th>{{__('maintenance::dashboard.category')}}</th>
                                                                <th>{{__('maintenance::dashboard.title')}}</th>
                                                                <th>{{__('maintenance::dashboard.sla_remain_time')}}</th>
                                                                <th>{{__('maintenance::dashboard.priority')}}</th>
                                                                <th>{{__('maintenance::dashboard.status')}}</th>
                                                                <th>{{__('maintenance::dashboard.task_report_date')}}</th>
                                                                <th>{{__('maintenance::dashboard.task_start_date')}}</th>
                                                                <th>{{__('maintenance::dashboard.task_end_date')}}</th>
                                                                <th>{{__('maintenance::dashboard.staff_reporter')}}</th>
                                                                <th>{{__('maintenance::dashboard.resident_reporter')}}</th>
                                                                <th>{{__('maintenance::dashboard.assigned_user_contractor')}}</th>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>





                            </section>

                        </div>




</div>
</div>
</div>
</div>
</div>




@endsection

@section('script')
    <script src="{{ asset('resources/Chart.js/Chart.bundle.min.js') }}"></script>


    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>

    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/select2.full.min.js') }}"></script>

{{--
    <link rel="stylesheet" href="{{ asset('resources/multiselect/multiselect.css') }}" type="text/css">
    <script type="text/javascript" src="{{ asset('resources/multiselect/multiselect.js') }}"></script> --}}





    <script>

        $(document).ready(function () {


        $('.select2').select2();

            $('.date_place').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "DD-MM-YYYY HH:mm",
            });

            loadMaintenances();

        });

        ///////////////////////////////////////////////////////
        function send( url , data , name, parameters ) {
            return_value = null;
            $.ajax({
                'type': "POST",
                'global': false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                'dataType': 'json',
                'url': url,
                'data': data,
                'success': function (data) {
                    callback(data , name , parameters );
                },
                'error': function(xhr, ajaxOptions, thrownError, ) {

                    alert(xhr.responseJSON.message);
                    //todo hanle error
                    callback(data , name , parameters );
                }
            });
        }
        /////////////////////////////////////////////////////////

        function callback(response , name, parameters ) {
            return_value = response;

            var fn = window[name];
            if(typeof fn !== 'function')
                return;

            fn.apply(window, parameters);

            //use return_first variable here
        }

        ///////////////////////////////////////////////////////
        function loadMaintenances(){
            business = [1 ];


            send( '/maintenance/mgt_contractor_maintenances_list',  {
                business :business,

            }, 'handleMaintenanceTableBody', []);

        }
        //////////////////////////////////////////////////////
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
                    var category = maintenance_list[k]["job_category_name"];
                    var m_url = maintenance_list[k]["m_url"];
                    var mail_url = maintenance_list[k]["mail_url"];
                    var id_business = maintenance_list[k]["id_business"];
                    var business_name = maintenance_list[k]["business_name"];
                    var title = maintenance_list[k]["maintenance_job_title"];
                    var sla = maintenance_list[k]["remain_time"];
                    var priority = maintenance_list[k]["priority_name"];
                    var status = maintenance_list[k]["job_status_name"];
                    var job_report_date_time = maintenance_list[k]["job_report_date_time"];
                    var job_start_date_time = maintenance_list[k]["job_start_date_time"]?maintenance_list[k]["job_start_date_time"]:'-';
                    var job_finished_date_time = maintenance_list[k]["job_finish_date_time"]?maintenance_list[k]["job_finish_date_time"]:'-';
                    var staff_reporter = maintenance_list[k]["staff_first_name"]+' '+maintenance_list[k]["staff_last_name"];
                    var resident_reporter = maintenance_list[k]["resident_reporter"]? maintenance_list[k]["resident_reporter"]:'-';
                    var assignee = maintenance_list[k]["assignee_first_name"]?(maintenance_list[k]["assignee_first_name"]+' '+maintenance_list[k]["assignee_last_name"]):(maintenance_list[k]['contractor_name']?maintenance_list[k]['contractor_name']:'-');

                    if(assignee == '-'){
                        var email_btn ='<a style="opacity: .4;cursor: default !important;pointer-events: none;" href="' + mail_url + '" target="_blank" class="btn btn-primary allign-btn sdr-primary" title="Send Email To Contractor" >'+
                        '<i class="fa fa-solid fa-envelope"></i>'+
                        '</a>';

                        var start_btn = '<a style="opacity: .4;cursor: default !important;pointer-events: none;" href="#" class="btn btn-primary allign-btn sdr-primary" title="Start Maintenance" onclick="getNowForStartDateTimes('+id_business +','+ id_maintenance_job+')"> '+
                        '<i class="fa fa-solid fa-play"></i>'+
                        '</a>';
                    }
                    else{
                        var email_btn = '<a href="' + mail_url + '" target="_blank" class="btn btn-primary allign-btn sdr-primary" title="Send Email To Contractor" >'+
                        '<i class="fa fa-solid fa-envelope"></i>'+
                        '</a>';

                        if(job_start_date_time == '-'){

                            var start_btn = '<a href="#" class="btn btn-primary allign-btn sdr-primary" title="Start Maintenance" onclick="getNowForStartDateTimes('+id_business +','+ id_maintenance_job+')"> '+
                            '<i class="fa fa-solid fa-play"></i>'+
                            '</a>';

                        }
                        else{
                            var start_btn = '<a style="opacity: .4;cursor: default !important;pointer-events: none;" href="#" class="btn btn-primary allign-btn sdr-primary" title="Start Maintenance" onclick="getNowForStartDateTimes('+id_business +','+ id_maintenance_job+')"> '+
                            '<i class="fa fa-solid fa-play"></i>'+
                            '</a>';
                        }
                    }

                    if(job_finished_date_time == '-'){

                        var stop_btn ='<a href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn sdr-primary" title="Finish Maintenance" onclick="getNowForDateTimes('+id_business +','+id_maintenance_job+')">'+
                        '<i class="fa fa-solid fa-stop"></i>'+
                        '</button></a>';
                    }
                    else{
                        var stop_btn = '<a style="opacity: .4;cursor: default !important;pointer-events: none;" href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn sdr-primary" title="Finish Maintenance" onclick="getNowForDateTimes('+id_business +','+id_maintenance_job+')">'+
                        '<i class="fa fa-solid fa-stop"></i>'+
                        '</button></a>';
                    }


                    var operation = '<a href="' + m_url + '" target="_blank" class="btn btn-primary allign-btn sdr-primary" data-toggle="tooltip" title="Maintenance Detail" data-original-title="Maintenance Detail">' +
                        '<i class="fa-solid fa-info fa fa-info" aria-hidden="true"></i>' +
                        '</a>' +

                        '<a href="#" class="btn btn-primary allign-btn sdr-primary" title="Assign Maintenance" onclick="showAssignMaintenanceModal('+id_business+','+id_maintenance_job+')">'+
                        '<i class="fa fa-solid fa-user"></i>'+
                        '</a>'+

                        start_btn+
                        stop_btn+
                        email_btn+



                        '<button style="margin-right: 1px;" type="button" class="btn btn-danger allign-btn sdr-danger alert-confirm m-b-10" title="Delete Maintenance" onclick="showDeleteMaintenanceModal('+id_business +','+ id_maintenance_job+')">'+
                        '<i class="fa fa-solid fa-trash"></i>'+
                        '</button>';


                    htmlValue= htmlValue +"<tr><td>"+(counter)+"</td><td>"+business_name+"</td><td>"+category+"</td><td>"+title+"</td><td>"+sla+"</td><td>"
                        +priority+"</td><td>"+status+"</td><td>"+job_report_date_time+"</td><td>"+job_start_date_time+"</td><td>"+job_finished_date_time+"</td><td>"+staff_reporter+"</td><td>"+resident_reporter+"</td><td>"+assignee+"</td></tr>";


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

                    { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3,4,5,6,7,8,9,10,11,12] }
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



            //loadingOverlay.cancelAll();

}




    </script>


@endsection
