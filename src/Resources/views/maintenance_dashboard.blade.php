@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title').' '.__('maintenance::dashboard.maintenance_dashboard'))


@section('css')

    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <script src="{{ asset('resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

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
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="box box-primary">


                            <div class="col-lg-3 col-md-3 col-sm-3">
                                <div class="">
                                    <div class="box-header">
                                        <h3>{{__('maintenance::dashboard.maintenance_per_status')}}</h3>

                                    </div>
                                    <div>
                                        <div class="box-body" id="div_barChart">
                                            <canvas id="barChart" style="height:250px"></canvas>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3">
                                <div class="">
                                    <div class="box-header">
                                        <h3>{{__('maintenance::dashboard.expired_maintenance_count')}}</h3>

                                    </div>
                                    <div>
                                        <div class="box-body" id="div_barChart2">
                                            <canvas id="barChart2" style="height:250px"></canvas>
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3>{{__('maintenance::dashboard.search_options')}}</h3>

                            </div>
                            <div class="box-body">

                                <div class="col-md-10 col-xs-12">


                                    <div class="row">
                                        <!-- Start Date -->
                                        <div class="col-md-3">


                                            <div class="input-group date date_place" id="id_0">
                                                <input type="text" value="" placeholder="{{__('maintenance::maintenance_mgt.start_date')}}" class="form-control" name="search_start_date" id="search_start_date" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>





                                            {{-- <div class="form-group" style="">
                                                <div class="input-group col-xs-10 col-sm-10 col-md-10" style="float:left;padding-right: 15px;padding-left: 15px;">
                                                    <div class="input-group-addon">
                                                        <i class="fa-solid fa-calendar"></i>
                                                    </div>
                                                    <input name="search_start_date" placeholder="{{__('booking.start_date')}}" type="text" class="form-control date active" id="search_start_date" value="" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                </div>

                                            </div> --}}

                                        </div>


                                        <!-- saas_client_business -->
                                        <div class="col-md-3">
                                            <div class="form-group">

                                                <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                    <select class="form-control pull-right" id="search_business" name="search_business" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                        {{-- <option value="">
                                                            {{__('maintenance::dashboard.business')}}
                                                        </option> --}}
                                                        @foreach($businesses as $business)
                                                            <option value="{{$business->id_saas_client_business}}">
                                                                {{$business->business_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>

                                            </div>
                                        </div>



                                        <!-- priority -->
                                        <div class="col-md-3">
                                            <div class="form-group">

                                                <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                    <select class="form-control pull-right" id="search_priority" name="search_priority" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                        <option value="">
                                                            {{__('maintenance::dashboard.priority')}}
                                                        </option>
                                                        @foreach($priorities as $priority)
                                                            <option value="{{$priority->id_maintenance_job_priority_ref}}">
                                                                {{$priority->priority_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>

                                            </div>
                                        </div>



                                        <!-- title -->
                                        <div class="col-md-3">
                                            <div class="form-group">

                                                <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                    <input name="search_room_no" placeholder="{{__('maintenance::dashboard.title')}}" type="text" class="form-control active" id="search_title" value="" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">

                                                </div>

                                            </div>
                                        </div>

                                    </div>


                                    <div style="margin-top: 2px;" class="row">


                                        <!-- End Date -->
                                        <div class="col-md-3">


                                            <div class="input-group date date_place" id="id_1">
                                                <input type="text" value="" placeholder="{{__('maintenance::maintenance_mgt.end_date')}}" class="form-control" name="search_end_date" id="search_end_date" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>




                                            {{-- <div class="form-group" style="">
                                                <div class="input-group col-xs-10 col-sm-10 col-md-10" style="float:left;padding-right: 15px;padding-left: 15px;">
                                                    <div class="input-group-addon">
                                                        <i class="fa-solid fa-calendar"></i>
                                                    </div>
                                                    <input name="search_end_date" type="text" placeholder="{{__('booking.end_date')}}" class="form-control date active" id="search_end_date" value="" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                </div>

                                            </div> --}}
                                        </div>



                                        <!-- category -->
                                        <div class="col-md-3">
                                            <div class="form-group">

                                                <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                    <select class="form-control pull-right" id="search_category" name="search_category" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                        <option value="">
                                                            {{__('maintenance::dashboard.category')}}
                                                        </option>
                                                        @foreach($categories as $category)
                                                            <option value="{{$category->id_maintenance_job_category_ref}}">
                                                                {{$category->job_category_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>

                                            </div>
                                        </div>



                                        <!-- status -->
                                        <div class="col-md-3">
                                            <div class="form-group">

                                                <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                    <select class="form-control pull-right" id="search_status" name="search_status" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                        <option value="">
                                                            {{__('maintenance::dashboard.status')}}
                                                        </option>
                                                        @foreach($statuses as $status)
                                                            <option value="{{$status->id_maintenance_job_status_ref}}">
                                                                {{$status->job_status_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>

                                            </div>
                                        </div>





                                        <!-- assignee -->
                                        <div class="col-md-3">
                                            <div class="form-group">

                                                <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                    <input name="search_assignee" placeholder="{{__('maintenance::dashboard.assignee_contractor')}}" type="text" class="form-control active" id="search_assignee" value="" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">

                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                </div>


                                <div class="col-md-2 col-xs-12 col-sm-12 col-lg-2">
                                    <div class="row">
                                        <button style="min-width:150px;margin-top:1px;" id="searchbtn" onclick="searchAgain()" type="button" class="btn btn-primary">{{__('booking.search')}}</button>
                                    </div>
                                    <div class="row">
                                        <button style="min-width:150px;margin-top:1px;" type="button" onclick="resetSearchbox()" class="btn btn-primary">{{__('booking.reset')}}</button>
                                    </div>


                                </div>


                            </div>

                        </div>




                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="box col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="box-header">
                            <h3>{{__('maintenance::dashboard.maintenances_list')}}</h3>
                        </div>

                        <div class="box-body">
                            <div class="row" style="float: right;">
                                <a style="min-width:150px;margin-top:1px;" href="/maintenance/create/page" class="btn btn-primary">{{trans('maintenance::dashboard.create_job')}}</a>
                                <br>
                            </div>
                        </div>
                        <div class="box-body ">


                            <div class="row table-responsive no-padding">

                                <table id="maintenances_table" class="table table-bordered table-hover dataTable text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
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
                                        <th>{{__('maintenance::dashboard.sla_remain_time')}}</th>
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

        </div>



















    <!-- Modal -->
    <div class="modal fade" id="deleteMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="deleteMaintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="deleteMaintenanceModalLabel">{{trans('maintenance::dashboard.delete_maintenance')}}</h4>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_delete_maintenance" style="display: none">
                            <div id="ajx_err_msg_delete_maintenance"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_delete_maintenance" style="display: none">
                            <div id="ajx_suc_msg_delete_maintenance"></div>
                        </div>

                        <p>{{trans('maintenance::dashboard.do_you_want_to_delete_maintenance')}}</p>
                        <input type="hidden" id="deleted_maintenance">
                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-warning"
                        data-dismiss="modal">{{trans('maintenance::contractor.cancel')}}</button>
                    <button type="button" class="btn btn-danger"
                        id="delete_maintenance" onclick="deleteMaintenance()">{{trans('maintenance::contractor.delete')}}</button>
                </div>


            </div>
        </div>
    </div>






    <!-- Modal -->
    <div class="modal fade" id="assignMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="assignMaintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="assignMaintenanceModalLabel">{{trans('maintenance::dashboard.assign_maintenance')}}</h4>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_assign_maintenance" style="display: none">
                            <div id="ajx_err_msg_assign_maintenance"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_assign_maintenance" style="display: none">
                            <div id="ajx_suc_msg_assign_maintenance"></div>
                        </div>

                        <input type="hidden" id="assigned_maintenance">


                        <!-- Business/contractor -->
                        <div class="form-group row">
                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::dashboard.business_contractor')}}:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">


                                <select name="business_contractor" id="business_contractor" onchange="loadUserAgents()" class="form-control select ">
                                    <option value="">{{trans('maintenance::dashboard.select_business_contractor')}}</option>
                                     {{-- @foreach ($businesses as $business)
                                     <option value="B{{ $business->id_saas_client_business }}">
                                        {{ $business->business_name }}
                                     </option>
                                    @endforeach --}}
                                    {{-- @foreach ($contractors as $contractor)
                                     <option value="C{{ $contractor->id_contractor }}">
                                        {{ $contractor->name }}
                                     </option>
                                    @endforeach --}}



                                </select>
                            </div>
                        </div>



                        <!-- User / agent -->
                        <div class="form-group row">
                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::dashboard.user_agent')}}:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">


                                <select name="user_agent" id="user_agent" class="form-control select ">
                                    <option value="">{{trans('maintenance::dashboard.select_user_agent')}}</option>
                                    @foreach ($maintenance_users as $maintenance_user)
                                     <option value="{{ $maintenance_user->id }}">
                                        {{ $maintenance_user->first_name }} {{ $maintenance_user->last_name }}
                                     </option>
                                    @endforeach
                                    @foreach ($contractor_agents as $contractor_agent)
                                     <option value="{{ $contractor_agent->id }}">
                                        @if(isset($contractor_agent->first_name) || isset($contractor_agent->last_name))){{ $contractor_agent->first_name }} {{ $contractor_agent->last_name }}@else{{$contractor_agent->login_name}}@endif
                                     </option>
                                    @endforeach



                                </select>
                            </div>
                        </div>




                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-warning"
                        data-dismiss="modal">{{trans('maintenance::dashboard.cancel')}}</button>
                    <button type="button" class="btn btn-danger" id="assign_maintenance_btn"
                         onclick="assignMaintenance()">{{trans('maintenance::dashboard.save')}}</button>
                </div>


            </div>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade" id="startMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="startMaintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="startMaintenanceModalLabel">{{trans('maintenance::dashboard.start_maintenance')}}</h4>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_start" style="display: none">
                            <div id="ajx_err_msg_start"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_start" style="display: none">
                            <div id="ajx_suc_msg_start"></div>
                        </div>

                        <input type="hidden" id="started_maintenance">

                        <div class="form-group row">
                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::dashboard.select_start_date_of_job') }}:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">

                                <div class="form-group">
                                    <div class="input-group date date_place" id="start_datetimepicker">
                                        <input type="text" class="form-control">
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>


                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-warning"
                        data-dismiss="modal">{{trans('maintenance::dashboard.cancel')}}</button>
                    <button type="button" class="btn btn-danger" id="start_maintenance_btn"
                         onclick="startMaintenance()">{{trans('maintenance::dashboard.save')}}</button>
                </div>


            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="endMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="endMaintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="endMaintenanceModalLabel">{{trans('maintenance::dashboard.end_maintenance')}}</h4>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_end" style="display: none">
                            <div id="ajx_err_msg_end"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_end" style="display: none">
                            <div id="ajx_suc_msg_end"></div>
                        </div>

                        <input type="hidden" id="ended_maintenance">

                        <div class="form-group row">
                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::dashboard.select_end_date_of_job') }}:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">

                                <div class="form-group">
                                    <div class="input-group date date_place" id="end_datetimepicker">
                                        <input type="text" class="form-control">
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>


                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-warning"
                        data-dismiss="modal">{{trans('maintenance::dashboard.cancel')}}</button>
                    <button type="button" class="btn btn-danger" id="end_maintenance_btn"
                         onclick="endMaintenance()">{{trans('maintenance::dashboard.save')}}</button>
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
    <script src="{{ asset('resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>


    <script>

        $(function () {

            $('.date_place').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "DD-MM-YYYY hh:mm",
            });
         });


        $(document).ready(function () {

            prepareMaintenanceStatusChartData();
            prepareMaintenanceSlaChartData();
            loadMaintenances();

        });
        ///////////////////////////////////////////////////////////////////////////
        let prepareMaintenanceStatusChartData = function () {
            let spinHandle = loadingOverlay.activate();

            send( '/maintenance/statuses/charts',  {}, 'handleMaintenanceStatusChart', []);

        };
        ///////////////////////////////////////////////////////////////////////////
        let prepareMaintenanceSlaChartData = function () {
            let spinHandle = loadingOverlay.activate();

            send( '/maintenance/sla/charts',  {}, 'handleMaintenanceSlaChart', []);

        };
        ///////////////////////////////////////////////////////////////////////////

        function handleMaintenanceStatusChart(){

            let res = return_value.code;
            if(res == "failure"){

            }
            else{
                let status_report = return_value.result;
                generateChart2( status_report, "div_barChart" , "barChart");

            }


        }
        ///////////////////////////////////////////////////////////////////////////

        function handleMaintenanceSlaChart(){

            let res = return_value.code;
            if(res == "failure"){

            }
            else{
                let sla_report = return_value.result;
                generateChart2( sla_report, "div_barChart2" ,'barChart2' );

            }


        }
        //////////////////////////////////////////////////////////////////////
        let generateChart2 = function(report, place_chart_id , canvas_id){

            document.getElementById(place_chart_id).innerHTML='<canvas id="'+canvas_id+'" style="height:250px"></canvas>';


            //end of edit to fix bug in charts - by ahmadian


            var labels4 = [];
            var dataset4 = [];
            var colours4 = [];

            var ref_colors = ['#EE3322','#1A5276' , '#F7DC6F' , '#A9DFBF' , '#BB8FCE' , '#196F3D' , '#994C40' , '#BD1525' , '#24BD15' , '#2215BD' , '#FA033F'];

            var counter = 0;
            Object.keys(report).forEach(function(k){
                labels4.push(k);
                dataset4.push(report[k]);
                colours4.push(ref_colors[counter++]);
            });



            var data4 = {
                labels: labels4,
                datasets: [{
                    data: dataset4,
                    backgroundColor: colours4,
                    hoverBackgroundColor: colours4,
                    borderWidth: 1
                }]
            };


            var bar_options = {
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true,
                    }]
                },
            };

            var bar_ctx4 = $('#'+canvas_id);
            var bar_Chart4 = new Chart(bar_ctx4, {
                type: 'bar',
                data: data4,
                options: bar_options
            });

        }

        ///////////////////////////////////////////////////////
        function loadMaintenances(){

            var spinHandle = loadingOverlay.activate();
            business = $('#search_business').val();
            category = $('#search_category').val();
            priority = $('#search_priority').val();
            status = $('#search_status').val();
            title = $('#search_title').val();
            start_date = $('#search_start_date').val();
            end_date = $('#search_end_date').val();
            assignee = $('#search_assignee').val();

            send( '/maintenance/maintenances_list',  {
                business :business,
                category :category,
                priority :priority,
                status :status,
                title :title,
                start_date :start_date,
                end_date :end_date,
                assignee :assignee,
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
                    var category = maintenance_list[k]["job_category_name"];
                    var title = maintenance_list[k]["maintenance_job_title"];
                    var sla = maintenance_list[k]["remain_time"];
                    var priority = maintenance_list[k]["priority_name"];
                    var status = maintenance_list[k]["job_status_name"];
                    var job_report_date_time = maintenance_list[k]["job_report_date_time"];
                    var job_start_date_time = maintenance_list[k]["job_start_date_time"]?maintenance_list[k]["job_start_date_time"]:'-';
                    var job_finished_date_time = maintenance_list[k]["job_finish_date_time"]?maintenance_list[k]["job_finish_date_time"]:'-';
                    var staff_reporter = maintenance_list[k]["first_name"]+' '+maintenance_list[k]["last_name"];
                    // var resident_reporter = maintenance_list[k]["resident_reporter"]? maintenance_list[k]["resident_reporter"]:'-';
                    var resident_reporter = maintenance_list[k]["resident_first_name"] ?  maintenance_list[k]["resident_first_name"] + ' ' + maintenance_list[k]["resident_surname"] : "N/A";


                    var operation = '<a href="/maintenance/detail/' + id_maintenance_job + '" target="_blank" data-toggle="tooltip" title="Maintenance Detail" data-original-title="Maintenance Detail">' +
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn"  >' +
                        '<i class="fa-solid fa-info" aria-hidden="true"></i></button>' +
                        '</a>' +
                        '<a href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Assign Maintenance" onclick="showAssignMaintenanceModal('+id_maintenance_job+')">'+
                        '<i class="fa-solid fa-user"></i>'+
                        '</button></a>'+
                        '<a href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Start Maintenance" onclick="showStartMaintenanceModal('+id_maintenance_job+')">'+
                        '<i class="fa-solid fa-play"></i>'+
                        '</button></a>'+
                        '<a href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Stop Maintenance" onclick="showEndMaintenanceModal('+id_maintenance_job+')">'+
                        '<i class="fa-solid fa-stop"></i>'+
                        '</button></a>'+

                        '<button style="margin-right: 1px;" type="button" class="btn btn-danger allign-btn" title="Delete Maintenance" onclick="showDeleteMaintenanceModal('+id_maintenance_job+')">'+
                        '<i class="fa-solid fa-trash"></i>'+
                        '</button>';


                    htmlValue= htmlValue +"<tr><td>"+(counter)+"</td><td>"+category+"</td><td>"+title+"</td><td>"+sla+"</td><td>"
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

                    { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3,4,5,6,7,8,9,10,11] },{ "width": "20%", "targets": 11 }
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


        ///////////////////////////////////////////////////////

        function showDeleteMaintenanceModal(id_maintenance){

            $('#deleted_maintenance').val(id_maintenance);
            $('#err_msg_box_delete_maintenance').css('display' , 'none');
            $('#suc_msg_box_delete_maintenance').css('display' , 'none');
            $('#deleteMaintenanceModal').modal('show');

        }

        ///////////////////////////////////////////////////////
        function deleteMaintenance(){
            var spinHandle = loadingOverlay.activate();

            let deleted_maintenance = $( '#deleted_maintenance' ).val();

            send( '/maintenance/delete/'+deleted_maintenance,  {
            }, 'handleDeleteMaintenance', []);
        }
        ////////////////////////////////////////////////////////
        function handleDeleteMaintenance()
        {
            let message = return_value.message;
            let res = return_value.code;

            if(res == "failure"){
                var textmessage = message;

                $("#ajx_err_msg_delete_maintenance").html(textmessage);
                $("#err_msg_box_delete_maintenance").css('display' , 'block');

            }

            else{

                $('#deleteMaintenanceModal').modal('hide');
                loadMaintenances();

            }


            loadingOverlay.cancelAll();

        }
        ////////////////////////////////////////////////////////
        function resetSearchbox() {
            //drop downs
            $("#search_business").val($("#search_business option:first").val());
            $("#search_category").val($("#search_category option:first").val());
            $("#search_priority").val($("#search_priority option:first").val());
            $("#search_status").val($("#search_status option:first").val());



            //inputs
            $('#search_title').val('');
            $('#search_start_date').val('');
            $('#search_end_date').val('');

            searchAgain();

        }
        ///////////////////////////////////////////////////////
        function searchAgain(){

            loadMaintenances();

        }
        ///////////////////////////////////////////////////////

        function showAssignMaintenanceModal(id_maintenance){

            $("#assign_maintenance_btn").removeAttr('disabled');

            $('#assigned_maintenance').val(id_maintenance);
            $('#err_msg_box_assign_maintenance').css('display' , 'none');
            $('#suc_msg_box_assign_maintenance').css('display' , 'none');
            // $('#assignMaintenanceModal').modal('show');
            $('#user_agent').find('option').not(':first').remove();
            $("#business_contractor").prop("selectedIndex", 0);


            send( '/maintenance/contractors_for_assignment',  {
                maintenance:id_maintenance
            }, 'handelShowAssignMaintenanceModal', []);


        }
        /////////////////////////////////////////////////////////
        function handelShowAssignMaintenanceModal(){
            let message = return_value.message;
            let res = return_value.code;
            let contractors = return_value.contractors;
            let businesses = return_value.businesses;

            if(res == "failure"){
                var textmessage = message;

                $("#ajx_err_msg_assign_maintenance").html(textmessage);
                $("#err_msg_box_assign_maintenance").css('display' , 'block');

            }

            else{

                $('#business_contractor').find('option').not(':first').remove();
                businesses.forEach(item => {
                    var item_name = item.business_name ;
                    var item_id = 'B'+item.id_saas_client_business ;
                    $('#business_contractor').append(new Option(item_name ,item_id));
                });
                contractors.forEach(item => {
                    var item_name = item.name ;
                    var item_id = 'C'+item.id_contractor ;
                    $('#business_contractor').append(new Option(item_name ,item_id));
                });


            }
            $('#assignMaintenanceModal').modal('show');


            loadingOverlay.cancelAll();

        }
        ///////////////////////////////////////////////////////
        function loadUserAgents(){

            var spinHandle = loadingOverlay.activate();
            business_contractor = $('#business_contractor').val();

            send( '/maintenance/business_contractor/user_agents',  {
                business_contractor :business_contractor,
            }, 'handleLoadUserAgents', []);

        }
        ///////////////////////////////////////////////////////
        function handleLoadUserAgents()
        {
            let message = return_value.message;
            let res = return_value.code;
            let user_list = return_value.result;

            if(res == "failure"){
                var textmessage = message;

                $("#ajx_err_msg_assign_maintenance").html(textmessage);
                $("#err_msg_box_assign_maintenance").css('display' , 'block');

            }

            else{

                $('#user_agent').find('option').not(':first').remove();
                user_list.forEach(item => {
                    var item_name = item.first_name || item.last_name ? item.first_name + " "+ item.last_name : (item.login_name?item.login_name:item.email);
                    $('#user_agent').append(new Option(item_name ,item.id));
                });


            }
            // $('#assignMaintenanceModal').modal('show');


            loadingOverlay.cancelAll();

        }
        ///////////////////////////////////////////////////////
        function assignMaintenance(){

            $("#assign_maintenance_btn").attr('disabled','disabled');

            var spinHandle = loadingOverlay.activate();
            maintenance = $('#assigned_maintenance').val();
            user = $('#user_agent').val();

            send( '/maintenance/assign_user',  {
                maintenance :maintenance,
                user :user,
            }, 'handleAssignMaintenance', []);

        }
        ///////////////////////////////////////////////////////
        function handleAssignMaintenance()
        {
            let message = return_value.message;
            let res = return_value.code;
            let user_list = return_value.result;
            loadingOverlay.cancelAll();


            if(res == "failure"){

                $("#ajx_err_msg_assign_maintenance").html(message);
                $("#err_msg_box_assign_maintenance").css('display' , 'block');
                $("#assign_maintenance_btn").removeAttr('disabled');


            }

            else{

                $("#ajx_suc_msg_assign_maintenance").html(message);
                $("#suc_msg_box_assign_maintenance").css('display' , 'block');
                $("#err_msg_box_assign_maintenance").css('display' , 'none');
                setTimeout(function() {$('#assignMaintenanceModal').modal('hide');}, 3000);



            }



        }
        ///////////////////////////////////////////////////////

        function showStartMaintenanceModal(id_maintenance){

            $("#start_maintenance_btn").removeAttr('disabled');

            $('#started_maintenance').val(id_maintenance);
            $('#err_msg_box_start').css('display' , 'none');
            $('#suc_msg_box_start').css('display' , 'none');
            $('#startMaintenanceModal').modal('show');

        }
        ///////////////////////////////////////////////////////
        function startMaintenance(){
            var spinHandle = loadingOverlay.activate();
            $("#start_maintenance_btn").attr('disabled','disabled');


            let started_maintenance = $( '#started_maintenance' ).val();
            let start_date_time = $( '#start_datetimepicker input' ).val();

            send( '/maintenance/start/'+started_maintenance,  {
                start_date_time:start_date_time,
            }, 'handleStartMaintenance', []);
        }
        ////////////////////////////////////////////////////////
        function handleStartMaintenance()
        {
            let message = return_value.message;
            let res = return_value.code;
            var textmessage = message;


            if(res == "failure"){

                if(typeof message === 'object'){

                    textmessage = "";
                    Object.keys(message).forEach(function(k) {
                        textmessage+= message[k];
                    });
                }

                $("#ajx_err_msg_start").html(textmessage);
                $("#err_msg_box_start").css('display' , 'block');
                $("#start_maintenance_btn").removeAttr('disabled');


            }

            else{
                $("#ajx_suc_msg_start").html(message);
                $("#suc_msg_box_start").css('display' , 'block');

                setTimeout(function() {
                    $('#startMaintenanceModal').modal('hide');
                    prepareMaintenanceStatusChartData();
                    prepareMaintenanceSlaChartData();
                    loadMaintenances();

            }, 3000);

            }


            loadingOverlay.cancelAll();

        }
        ///////////////////////////////////////////////////////

        function showEndMaintenanceModal(id_maintenance){

            $("#end_maintenance_btn").removeAttr('disabled');

            $('#ended_maintenance').val(id_maintenance);
            $('#err_msg_box_end').css('display' , 'none');
            $('#suc_msg_box_end').css('display' , 'none');
            $('#endMaintenanceModal').modal('show');

        }
        ///////////////////////////////////////////////////////
        function endMaintenance(){
            var spinHandle = loadingOverlay.activate();

            let ended_maintenance = $( '#ended_maintenance' ).val();
            let end_date_time = $( '#end_datetimepicker input' ).val();
            $("#end_maintenance_btn").attr('disabled','disabled');


            send( '/maintenance/end/'+ended_maintenance,  {
                end_date_time:end_date_time,
            }, 'handleEndMaintenance', []);
        }
        ////////////////////////////////////////////////////////
        function handleEndMaintenance()
        {
            let message = return_value.message;
            let res = return_value.code;
            var textmessage = message;


            if(res == "failure"){


                if(typeof message === 'object'){

                    textmessage = "";


                    Object.keys(message).forEach(function(k) {
                        textmessage+= message[k];
                    });
                }

                $("#ajx_err_msg_end").html(textmessage);
                $("#err_msg_box_end").css('display' , 'block');
                $("#end_maintenance_btn").removeAttr('disabled');


            }

            else{
                $('#err_msg_box_end').css('display' , 'none');

                $("#ajx_suc_msg_end").html(message);
                $("#suc_msg_box_end").css('display' , 'block');

                setTimeout(function() {
                    $('#endMaintenanceModal').modal('hide');
                    prepareMaintenanceStatusChartData();
                    prepareMaintenanceSlaChartData();
                    loadMaintenances();
                }, 3000);

            }


            loadingOverlay.cancelAll();

        }






    </script>


@endsection
