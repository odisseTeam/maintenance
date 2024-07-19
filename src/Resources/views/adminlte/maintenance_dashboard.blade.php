@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title').' '.__('maintenance::dashboard.maintenance_dashboard'))


@section('css')

    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <script src="{{ asset('resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('resources/select2/select2.min.css') }}" />

    <style>
        .select2-selection--multiple {
            border: 0px;
        }

        .select2-container--default {
            border: 0px;
        }

        .select2-container{
            min-width: 345px!important;
        }


    </style>


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
                                                    <select class="form-control pull-right" multiple="multiple" id="search_status" name="search_status" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">

                                                        @foreach($statuses as $status)
                                                            <option value="{{$status->id_maintenance_job_status_ref}}" @if($status->id_maintenance_job_status_ref != \Odisse\Maintenance\App\SLP\Enum\MaintenanceStatusConstants::CLOS){{'selected'}}@endif>
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
                                        <form method="post" action="/maintenance/csv_open_jobs">
                                            @csrf
                                            <input type="hidden" name="from" id="from_open_jobs_date" />
                                            <input type="hidden" name="to" id="to_open_jobs_date" />
                                            <button type="button" style="min-width:150px;margin-top:1px;" id="dl_open_jobs" class="btn btn-primary">
                                                <i class="fa-solid fa-download"></i> {{__('maintenance::dashboard.print_open_jobs')}}
                                            </button>
                                        </form>
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
                                        <th>{{__('maintenance::dashboard.property')}}</th>
                                        <th>{{__('maintenance::dashboard.room')}}</th>
                                        <th>{{__('maintenance::dashboard.status')}}</th>
                                        <th>{{__('maintenance::dashboard.priority')}}</th>
                                        <th>{{__('maintenance::dashboard.category')}}</th>
                                        <th>{{__('maintenance::dashboard.sla_remain_time')}}</th>
                                        <th>{{__('maintenance::dashboard.description')}}</th>
                                        <th>{{__('maintenance::dashboard.logged_by')}}</th>
                                        <th>{{__('maintenance::dashboard.report_date')}}</th>
                                        <th>{{__('maintenance::dashboard.task_start_date')}}</th>
                                        <th>{{__('maintenance::dashboard.task_end_date')}}</th>
                                        <th>{{__('maintenance::dashboard.assignee')}}</th>
                                        <th>{{__('maintenance::dashboard.operation')}}</th>


                                    </tr>
                                    </thead>


                                    <tbody id="maintenance_tbl_body"
                                        class="table table-bordered table-hover dataTable text-center">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('maintenance::dashboard.property')}}</th>
                                        <th>{{__('maintenance::dashboard.room')}}</th>
                                        <th>{{__('maintenance::dashboard.status')}}</th>
                                        <th>{{__('maintenance::dashboard.priority')}}</th>
                                        <th>{{__('maintenance::dashboard.category')}}</th>
                                        <th>{{__('maintenance::dashboard.sla_remain_time')}}</th>
                                        <th>{{__('maintenance::dashboard.description')}}</th>
                                        <th>{{__('maintenance::dashboard.logged_by')}}</th>
                                        <th>{{__('maintenance::dashboard.report_date')}}</th>
                                        <th>{{__('maintenance::dashboard.task_start_date')}}</th>
                                        <th>{{__('maintenance::dashboard.task_end_date')}}</th>
                                        <th>{{__('maintenance::dashboard.assignee')}}</th>
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



                        <!-- contractor skill-->

                        <div class="form-group row ">

                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::maintenance.contractor_skill') }}:</label>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <select name="contractor_skill[]" id="contractor_skill"  class="form-control select2" multiple="multiple">
                                    <option value="">{{ trans('maintenance::maintenance.select_contractor_skill') }}</option>
                                    @foreach ($skills as $skill)
                                        <option value="{{ $skill->id_contractor_skill_ref}}">{{ $skill->skill_name }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary" onclick="search_contractors()">{{trans('maintenance::maintenance.search')}}</button>
                            </div>
                        </div>


                        <hr style="border-top:3px solid #d2d6de;">





                        <!-- Business/contractor -->
                        <div class="form-group row">
                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::dashboard.business_contractor')}}:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">


                                <select name="business_contractor" id="business_contractor" onchange="loadUserAgents()" class="form-control select ">
                                    <option value="">{{trans('maintenance::dashboard.select_business_contractor')}}</option>

                                </select>
                            </div>
                        </div>




                        <!-- short name-->

                        <div class="form-group row ">

                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.short_name') }}:</label>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control" id="contractor_short_name" readonly value="@if(isset($selected_contractor)){{$selected_contractor->short_name}}@endif" >
                            </div>

                        </div>




                        <!-- tel number1-->

                        <div class="form-group row ">

                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.tel_number1') }}:</label>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control" id="contractor_tel_number1" readonly value="@if(isset($selected_contractor)){{$selected_contractor->tel_number1}}@endif" >
                            </div>

                        </div>



                        <!-- address line1-->

                        <div class="form-group row ">

                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.address') }}:</label>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <textarea class="form-control" rows="1" id="contractor_address_line1" readonly column="40">@if(isset($selected_contractor)){{$selected_contractor->address_line1}}@endif</textarea>
                            </div>

                        </div>

                        <!-- contractor_skills-->
                        <div class="form-group row ">

                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.skills') }}:</label>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" id="skill_place">

                            </div>

                        </div>



                        <!-- coverage_areas-->
                        <div class="form-group row ">

                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.coverage_area') }}:</label>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" id="coverage_area_place">

                            </div>

                        </div>




                        <!-- note-->

                        <div class="form-group row ">

                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::maintenance.contractor_note') }}:</label>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <textarea class="form-control" rows="4" id="contractor_note" readonly column="40">@if(isset($selected_contractor)){{$selected_contractor->note}}@endif</textarea>
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




                        <div id="user_place_1" style="display: none;">

                            <!-- Business/contractor -->
                            <div class="form-group row">
                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::dashboard.business_contractor')}}:</label>
                                <div class="col-sm-5 col-md-5 col-lg-5">


                                    <span name="business_contractor_readonly" id="business_contractor_readonly">

                                    </span>
                                </div>
                            </div>




                            <!-- User / agent -->
                            <div class="form-group row">
                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::dashboard.user_agent')}}:</label>
                                <div class="col-sm-5 col-md-5 col-lg-5">


                                    <select name="user_agent_start" id="user_agent_start" class="form-control select ">
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




                        <div id="user_place_2" style="display:none;">




                            <!-- contractor skill-->

                            <div class="form-group row ">

                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::maintenance.contractor_skill') }}:</label>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                    <select name="contractor_skill[]" id="start_contractor_skill"  class="form-control select2" multiple="multiple">
                                        <option value="">{{ trans('maintenance::maintenance.select_contractor_skill') }}</option>
                                        @foreach ($skills as $skill)
                                            <option value="{{ $skill->id_contractor_skill_ref}}">{{ $skill->skill_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary" onclick="search_contractors(2)">{{trans('maintenance::maintenance.search')}}</button>
                                </div>
                            </div>


                            <hr style="border-top:3px solid #d2d6de;">





                            <!-- Business/contractor -->
                            <div class="form-group row">
                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::dashboard.business_contractor')}}:</label>
                                <div class="col-sm-5 col-md-5 col-lg-5">


                                    <select name="business_contractor" id="start_business_contractor" onchange="loadUserAgents(2)" class="form-control select ">
                                        <option value="">{{trans('maintenance::dashboard.select_business_contractor')}}</option>

                                    </select>
                                </div>
                            </div>




                            <!-- short name-->

                            <div class="form-group row ">

                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.short_name') }}:</label>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                    <input class="form-control" id="start_contractor_short_name" readonly value="@if(isset($selected_contractor)){{$selected_contractor->short_name}}@endif" >
                                </div>

                            </div>




                            <!-- tel number1-->

                            <div class="form-group row ">

                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.tel_number1') }}:</label>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                    <input class="form-control" id="start_contractor_tel_number1" readonly value="@if(isset($selected_contractor)){{$selected_contractor->tel_number1}}@endif" >
                                </div>

                            </div>



                            <!-- address line1-->

                            <div class="form-group row ">

                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.address') }}:</label>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                    <textarea class="form-control" rows="1" id="start_contractor_address_line1" readonly column="40">@if(isset($selected_contractor)){{$selected_contractor->address_line1}}@endif</textarea>
                                </div>

                            </div>

                            <!-- contractor_skills-->
                            <div class="form-group row ">

                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.skills') }}:</label>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" id="start_skill_place">

                                </div>

                            </div>



                            <!-- coverage_areas-->
                            <div class="form-group row ">

                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.coverage_area') }}:</label>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" id="start_coverage_area_place">

                                </div>

                            </div>




                            <!-- note-->

                            <div class="form-group row ">

                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::maintenance.contractor_note') }}:</label>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                    <textarea class="form-control" rows="4" id="start_contractor_note" readonly column="40">@if(isset($selected_contractor)){{$selected_contractor->note}}@endif</textarea>
                                </div>

                            </div>





                            <!-- User / agent -->
                            <div class="form-group row">
                                <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::dashboard.user_agent')}}:</label>
                                <div class="col-sm-5 col-md-5 col-lg-5">


                                    <select name="user_agent" id="start_user_agent" class="form-control select ">
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
                    <h4 class="modal-title" id="endMaintenanceModalLabel">{{trans('maintenance::dashboard.complete_maintenance_job')}}</h4>
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

                        <div class="form-group row">
                            <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::dashboard.note') }}:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">

                                <div class="form-group">
                                    <div id="end_note_place">
                                        <textarea class="form-control" id="end_note"></textarea>
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

    <!-- Modal -->
              <!-- send email to contractor  -->
            <div class="modal fade" id="sendContractorEmailModal" tabindex="-1" role="dialog"
                                                aria-labelledby="sendContractorEmailModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">

                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                                                    class="sr-only">{{ __('general.close') }}</span></button>

                                                            <h3 class="modal-title" id="sendContractorEmailModallabel">
                                                                {{ __('maintenance::contractor.send_contractor_email_modal') }}</h3>



                                                        </div>


                                                            <div class="modal-body">
                                                                <div class="alert alert-danger alert-dismissible" id="send_contractor_email_err_msg_box"
                                                                        style="display: none;">
                                                                        <div id="send_contractor_email_ajx_err_msg"></div>
                                                                    </div>
                                                                    <div class="alert alert-success alert-dismissible" id="send_contractor_email_success_msg_box"
                                                                        style="display: none;">
                                                                        <div id="send_contractor_email_ajx_success_msg"></div>
                                                                    </div>



                                                                    <form method="POST" action="/maintenance/contractor/send/email">

                                                                       @csrf
                                                                        <div class="box">
                                                                            <div class="box-body">

                                                                                    <div class="col-md-11">

                                                                                        <h4>{{ __('maintenance::contractor.select_fields_to_attach') }}</h4>

                                                                                        <input type="hidden" name="id_contractor" id="hidden_id_contractor" >
                                                                                        <div id="contractor_job_attachments"></div>

                                                                                        <div class="form-group ">
                                                                                            <label for="exampleFormControlTextarea1">{{ __('maintenance::contractor.additional_comments') }} :</label>
                                                                                            <textarea class="form-control" id="contractor_job_attachment_text" name="contractor_job_attachment_text" rows="4" cols="2"></textarea>
                                                                                        </div>
                                                                                    </div>


                                                                            </div>
                                                                            <div class="box-footer">
                                                                                <button type="submit" class="btn btn-primary"
                                                                                    style="float:right;min-width:55px" >{{ __('general.yes') }}</button>
                                                                                <button style="float:right;margin-right:5px" type="button" id="close_btn" class="btn btn-warning"
                                                                                    data-dismiss="modal">{{ __('general.close') }}</button>


                                                                            </div>
                                                                        </div>

                                                                    </form>


                                                            </div>
                                                            <div class="modal-footer">

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
    {{-- <script src="{{ asset('resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script> --}}
    <script src="{{ asset('resources/select2/select2.full.min.js') }}"></script>


    <link rel="stylesheet" href="{{ asset('resources/multiselect/multiselect.css') }}" type="text/css">
    <script type="text/javascript" src="{{ asset('resources/multiselect/multiselect.js') }}"></script>




    <script>

        $('.select2').select2();


    $('#search_status').multiselect({
        enableFiltering: false,
        includeSelectAllOption: false,
        maxHeight: 400,
        buttonWidth: '100%',
        dropLeft: true,
        selectAllText: 'Select All',
        selectAllValue: 0,
        enableFullValueFiltering: false,
        // onDeselectAll: function() {
        //  //alert('onDeselectAll triggered!');
        //  buttonText: function(options, select) {
        //  if (options.length === 0) {
        //  return 'No option selected ...';
        //  }
        //  }
        //  },
        //nonSelectedText: 'Check an option!',
        //dropUp: true

        buttonText: function (options, select) {
            if (options.length === 0) {
                return 'Not selected';
            }
            else if (options.length > 9) {
                return 'More than 9 options selected!';
            }
            else {
                var labels = [];
                options.each(function () {
                    if ($(this).attr('label') !== undefined) {
                        labels.push($(this).attr('label'));
                    }
                    else {
                        labels.push($(this).html());
                    }
                });
                return labels.join(', ') + '';
            }
        }
    });


        $(function () {

            $('.date_place').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "DD-MM-YYYY HH:mm",
            });
         });


        $(document).ready(function () {

            prepareMaintenanceStatusChartData();
            prepareMaintenanceSlaChartData();

            setTimeout(function() {
                loadMaintenances();
            }, 1000);


            $('#dl_open_jobs').on('click', function (e) {
                $('#from_open_jobs_date').val( $( '#search_start_date' ).val() );
                $('#to_open_jobs_date').val( $( '#search_end_date' ).val() );

                $(this).closest('form').submit();
            });


        });
        ////////////////////////////////////////////////
        function search_contractors(type) {

            var spinHandle = loadingOverlay.activate();
            let maintenance_id;
            let contractor_skill;


            if(type == 2){

                maintenance_id = $('#started_maintenance').val();
                contractor_skill = $('#start_contractor_skill').val();

            }
            else{

                maintenance_id = $('#assigned_maintenance').val();
                contractor_skill = $('#contractor_skill').val();


            }



            send('/maintenance/contractor_skill/contractors', {
                maintenance_id: maintenance_id,
                contractor_skill: contractor_skill,

            }, 'handleSearchContractors', [type]);

        }
        ///////////////////////////////////////////////////////
        function handleSearchContractors(type)
        {
            let message = return_value.message;
            let res = return_value.code;
            let contractor_list = return_value.contractors;
            let business_list = return_value.businesses;


            $("#start_contractor_note").html('');
            $("#start_contractor_address_line1").html('');
            $("#start_contractor_tel_number1").val('');
            $("#start_contractor_short_name").val('');



            $("#contractor_note").html('');
            $("#contractor_address_line1").html('');
            $("#contractor_tel_number1").val('');
            $("#contractor_short_name").val('');

            if(res == "failure"){
                var textmessage = message;
                alert(textmessage);

            }

            else{

                if(type == 2){


                $('#start_business_contractor').find('option').not(':first').remove();
                $('#start_user_agent').find('option').not(':first').remove();
                if(business_list){
                    business_list.forEach(item => {
                    var item_name = item.business_name;
                    $('#start_business_contractor').append(new Option(item_name ,'B'+item.id_saas_client_business));
                    });
                }

                if(contractor_list){
                    contractor_list.forEach(item => {
                    var item_name = item['name'];
                    $('#start_business_contractor').append(new Option(item_name ,'C'+item['id_contractor']));
                    });
                }



                $("#start_contractor_note").html("");



                }
                else{


                $('#business_contractor').find('option').not(':first').remove();
                $('#user_agent').find('option').not(':first').remove();
                if(business_list){
                    business_list.forEach(item => {
                    var item_name = item.business_name;
                    $('#business_contractor').append(new Option(item_name ,'B'+item.id_saas_client_business));
                    });
                }

                if(contractor_list){
                    contractor_list.forEach(item => {
                    var item_name = item['name'];
                    $('#business_contractor').append(new Option(item_name ,'C'+item['id_contractor']));
                    });
                }



                $("#contractor_note").html("");



                }

            }

            loadingOverlay.cancelAll();

        }

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


                    var room = maintenance_list[k]["room"]?maintenance_list[k]["room"].room_number_short:'-';
                    var property = maintenance_list[k]["property"]?maintenance_list[k]["property"].property_short_name:'-';
                    var id_maintenance_job = maintenance_list[k]["id_maintenance_job"];
                    var category = maintenance_list[k]["job_category_name"];
                    var title = maintenance_list[k]["maintenance_job_title"];
                    var sla = maintenance_list[k]["remain_time"];
                    var priority = maintenance_list[k]["priority_name"];
                    var status = maintenance_list[k]["job_status_name"];
                    var job_report_date_time = maintenance_list[k]["job_report_date_time"];
                    var job_start_date_time = maintenance_list[k]["job_start_date_time"]?maintenance_list[k]["job_start_date_time"]:'-';
                    var job_finished_date_time = maintenance_list[k]["job_finish_date_time"]?maintenance_list[k]["job_finish_date_time"]:'-';
                    var staff_reporter = maintenance_list[k]["staff_first_name"]+' '+maintenance_list[k]["staff_last_name"];
                    var assignee = maintenance_list[k]["assignee_first_name"]?(maintenance_list[k]["assignee_first_name"]+' '+maintenance_list[k]["assignee_last_name"]):(maintenance_list[k]['contractor_name']?maintenance_list[k]['contractor_name']:'-');
                    // var resident_reporter = maintenance_list[k]["resident_reporter"]? maintenance_list[k]["resident_reporter"]:'-';
                    var resident_reporter = maintenance_list[k]["resident_first_name"] ?  maintenance_list[k]["resident_first_name"] + ' ' + maintenance_list[k]["resident_surname"] : "N/A";

                    if(assignee == '-'){
                        var email_btn = '<a style="opacity: .4;cursor: default !important;pointer-events: none;" href="/maintenance/create/email_temp/' + id_maintenance_job + '"  target="_blank" data-toggle="tooltip"  title="Send Email To Contractor" >'+
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" >'+
                        '<i class="fa-solid fa-envelope"></i>'+
                        '</button></a>';

                        // var start_btn = '<a style="opacity: .4;cursor: default !important;pointer-events: none;" href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Start Maintenance" onclick="getNowForStartDateTimes('+id_maintenance_job+')">'+
                        //     '<i class="fa-solid fa-play"></i>'+
                        //     '</button></a>';
                    }
                    else{
                        var email_btn = '<a href="/maintenance/create/email_temp/' + id_maintenance_job + '"  target="_blank" data-toggle="tooltip"  title="Send Email To Contractor" >'+
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" >'+
                        '<i class="fa-solid fa-envelope"></i>'+
                        '</button></a>';

                        // if(job_start_date_time == '-'){

                        //     var start_btn = '<a href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Start Maintenance" onclick="getNowForStartDateTimes('+id_maintenance_job+')">'+
                        //     '<i class="fa-solid fa-play"></i>'+
                        //     '</button></a>';

                        // }
                        // else{
                        //     var start_btn = '<a style="opacity: .4;cursor: default !important;pointer-events: none;" href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Start Maintenance" onclick="getNowForStartDateTimes('+id_maintenance_job+')">'+
                        //     '<i class="fa-solid fa-play"></i>'+
                        //     '</button></a>';
                        // }
                    }

                    var start_btn = '<a style="margin-right: 1px;" href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Start Maintenance" onclick="getNowForStartDateTimes('+id_maintenance_job+')">'+
                    '<i class="fa-solid fa-play"></i>'+
                    '</button></a>';

                    if(job_finished_date_time == '-'){

                        var stop_btn ='<a href="#"><button style="margin-right: 1px;" type="button" class="btn btn-success allign-btn" title="Complete maintenance job" onclick="getNowForDateTimes('+id_maintenance_job+')">'+
                        '<i class="fa-solid fa-check"></i>'+
                        '</button></a>';
                    }
                    else{
                        var stop_btn = '<a style="opacity: .4;cursor: default !important;pointer-events: none;" href="#"><button style="margin-right: 1px;" type="button" class="btn btn-success allign-btn" title="Complete maintenance job" onclick="getNowForDateTimes('+id_maintenance_job+')">'+
                        '<i class="fa-solid fa-check"></i>'+
                        '</button></a>';
                    }

                    var operation = '<a href="/maintenance/detail/' + id_maintenance_job + '" target="_blank" data-toggle="tooltip" title="Maintenance Detail" data-original-title="Maintenance Detail">' +
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn"  >' +
                        '<i class="fa-solid fa-info" aria-hidden="true"></i></button>' +
                        '</a>' +
                        '<a href="#"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Assign Maintenance" onclick="showAssignMaintenanceModal('+id_maintenance_job+')">'+
                        '<i class="fa-solid fa-user"></i>'+
                        '</button></a>'+
                        start_btn+
                        stop_btn+
                        email_btn+
                        '<button style="margin-right: 1px;" type="button" class="btn btn-danger allign-btn" title="Delete Maintenance" onclick="showDeleteMaintenanceModal('+id_maintenance_job+')">'+
                        '<i class="fa-solid fa-trash"></i>'+
                        '</button>';


                    htmlValue= htmlValue +"<tr><td>"+(counter)+"</td><td>"+property+"</td><td>"+room+"</td><td>"+status+"</td><td>"+priority+"</td><td>"+category+"</td><td>"
                        +sla+"</td><td>"+title+"</td><td>"+staff_reporter+"</td><td>"+job_report_date_time+"</td><td>"+job_start_date_time+"</td><td>"+job_finished_date_time+"</td><td>"+assignee+"</td><td>"+operation+"</td></tr>";


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
                    "iDisplayLength": 25,
                    "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    //'lengthChange': true,
                    'searching'   : true,
                    'ordering'    : true,
                    'info'        : true,
                    'autoWidth'   : true,
                    'stateSave'   : true,
                    "aoColumnDefs": [

                        { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3,4,5,6,7,8,9,10,11,12,13] },{ "width": "25%", "targets": 13 },{ "width": "17%", "targets": 7 }
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
       /////////////////////////////////////////////////////
        function sendEmailToContractorModal(id_maintenance_job){

            send( '/maintenance/contractor/job_document/'+id_maintenance_job,  {
            }, 'handleShowContractorJobDocumentsModal', [id_maintenance_job]);
        }
        //////////////////////////////////////////////////////
        function handleShowContractorJobDocumentsModal(){


                let contractor_job_documents = return_value.contractor_job_documents;
                let res = return_value.code;
                let message = return_value.message;


                if(res == "failure"){

                    var textmessage = message;

                    alert(textmessage);

                    //     $("#send_contractor_email_ajx_err_msg").html(textmessage);
                    //    $("#send_contractor_email_err_msg_box").css('display' , 'block');
                    //    $('#sendContractorEmailModal').modal('show');

                }else{


                    if((contractor_job_documents != null) && (contractor_job_documents !="undefined")){


                        var htmlValue = "";
                        Object.keys(contractor_job_documents).forEach(function(k){

                            var counter = 1+parseInt(k);


                            var doc_name = contractor_job_documents[k]["document_name"];
                            var doc_id = contractor_job_documents[k]["id_maintenance_job_document"];
                            var id_contractor = contractor_job_documents[k]["id_contractor"];





                            htmlValue= htmlValue +' <input type="checkbox" id="'+ doc_id +'" name="'+ doc_name +'" ><label for="'+doc_name+'">'+doc_name+'</label></br>';

                            $('#hidden_id_contractor').val(id_contractor);
                            $('#hidden_id_contractor').attr('value',id_contractor);


                            $('#contractor_job_attachments').html('');
                            $('#contractor_job_attachments').append(htmlValue);

                        });


                            $('#sendContractorEmailModal').modal('show');


                    }

                }
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
            $('#search_assignee').val('');

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
            $('#user_agent').find('option').remove();
            $("#business_contractor").prop("selectedIndex", 0);

            $("li.select2-selection__choice").remove();
            $(".select2").each(function() { $(this).val([]); });


            $("#contractor_note").html('');
            $("#contractor_address_line1").html('');
            $("#contractor_tel_number1").val('');
            $("#contractor_short_name").val('');

            $("#skill_place").html('');
            $("#coverage_area_place").html('');


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
            let users = return_value.users;
            let agents = return_value.agents;
            let selected_contractor = return_value.selected_contractor;
            let selected_business = return_value.selected_business;
            let selected_user_agent = return_value.selected_user_agent;
            let contractor_skills = return_value.contractor_skills;
            let coverage_areas = return_value.coverage_areas;

            if(res == "failure"){
                var textmessage = message;

                $("#ajx_err_msg_assign_maintenance").html(textmessage);
                $("#err_msg_box_assign_maintenance").css('display' , 'block');

            }

            else{

                $('#business_contractor').find('option').not(':first').remove();
                $('#user_agent').find('option').not(':first').remove();


                if(businesses){
                    businesses.forEach(item => {
                        var item_name = item.business_name ;
                        var item_id = 'B'+item.id_saas_client_business ;
                        $('#business_contractor').append(new Option(item_name ,item_id));
                    });
                }
                if(contractors){

                    Object.keys(contractors).forEach(function(key) {

						console.log(key);
						var item_name = contractors[key]['name'] ;
                        var item_id = 'C'+contractors[key]['id_contractor'] ;
						$('#business_contractor').append(new Option(item_name ,item_id));
					});

                }


                if(selected_contractor){

                    if(agents){
                        agents.forEach(item => {
                            var item_name = item.email;
                            var item_id = item.id ;
                            $('#user_agent').append(new Option(item_name ,item_id));
                        });
                    }

                    $('#business_contractor').val('C'+selected_contractor.id_contractor);

                    $("#contractor_note").html(selected_contractor.note);
                    $("#contractor_address_line1").html(selected_contractor.address_line1);
                    $("#contractor_tel_number1").val(selected_contractor.tel_number1);
                    $("#contractor_short_name").val(selected_contractor.short_name);

                    var contractor_skill = "";

                    if(contractor_skills){
                        contractor_skills.forEach(item => {
                            var item_name = item.skill_name;
                            contractor_skill = contractor_skill +"<button type='button' class='btn btn-primary'>"+item_name+"</button>";
                        });
                    }

                    $("#skill_place").html(contractor_skill);

                    var coverage_area = "";

                    if(coverage_areas){
                        coverage_areas.forEach(item => {
                            var item_name = item.location;
                            coverage_area = coverage_area +"<button type='button' class='btn btn-primary'>"+item_name+"</button>";
                        });
                    }

                    $("#coverage_area_place").html(coverage_area);
                }
                else if(selected_business){


                    if(users){
                        users.forEach(item => {
                            var item_name = item.first_name +' '+item.last_name;
                            var item_id = item.user_id ;
                            $('#user_agent').append(new Option(item_name ,item_id));
                        });
                    }

                    $('#business_contractor').val('B'+selected_business.id_saas_client_business);
                    $("#contractor_note").html('');
                    $("#contractor_address_line1").html('');
                    $("#contractor_tel_number1").val('');
                    $("#contractor_short_name").val('');

                    $("#skill_place").html('');
                    $("#coverage_area_place").html('');
                }
                if(selected_user_agent){
                    $('#user_agent').val(selected_user_agent);
                }




            }
            $('#assignMaintenanceModal').modal('show');


            loadingOverlay.cancelAll();

        }
        ///////////////////////////////////////////////////////
        function loadUserAgents(type){

            var spinHandle = loadingOverlay.activate();

            if(type == 2){
                business_contractor = $('#start_business_contractor').val();
                $("#ajx_err_msg_start").html('');
                $("#err_msg_box_start").css('display' , 'none');
            }
            else{
                business_contractor = $('#business_contractor').val();
                $("#ajx_err_msg_assign_maintenance").html('');
                $("#err_msg_box_assign_maintenance").css('display' , 'none');

            }

            send( '/maintenance/business_contractor/user_agents',  {
                business_contractor :business_contractor,
            }, 'handleLoadUserAgents', [type]);

        }
        ///////////////////////////////////////////////////////
        function handleLoadUserAgents(type){
            let message = return_value.message;
            let res = return_value.code;
            let user_list = return_value.result;
            let contractor = return_value.contractor;
            let contractor_skills = return_value.contractor_skills;
            let coverage_areas = return_value.coverage_areas;
            let user_type = return_value.user_type;

            if(res == "failure"){
                var textmessage = message;

                if(type == 2){

                    $("#ajx_err_msg_assign_maintenance").html(textmessage);
                    $("#err_msg_box_assign_maintenance").css('display' , 'block');
                    $("#start_contractor_note").html('');
                    $("#start_contractor_address_line1").html('');
                    $("#start_contractor_tel_number1").val('');
                    $("#start_contractor_short_name").val('');

                    $('#start_user_agent').find('option').remove();
                    $('#start_user_agent').append(new Option('Select User/Agent' ,''));

                }
                else{

                    $("#ajx_err_msg_assign_maintenance").html(textmessage);
                    $("#err_msg_box_assign_maintenance").css('display' , 'block');
                    $("#contractor_note").html('');
                    $("#contractor_address_line1").html('');
                    $("#contractor_tel_number1").val('');
                    $("#contractor_short_name").val('');

                    $('#user_agent').find('option').remove();
                    $('#user_agent').append(new Option('Select User/Agent' ,''));

                }

            }

            else{

                if(type == 2){

                    business_contractor = $('#start_business_contractor').val();

                    if(business_contractor.charAt(0) == 'C'){
                        $('#start_user_agent').find('option').remove();

                        $("#start_contractor_note").html(contractor.note);
                        $("#start_contractor_address_line1").html(contractor.address_line1);
                        $("#start_contractor_tel_number1").val(contractor.tel_number1);
                        $("#start_contractor_short_name").val(contractor.short_name);
                        console.log(contractor.short_name);

                        var contractor_skill = "";

                        if(contractor_skills){
                            contractor_skills.forEach(item => {
                                var item_name = item.skill_name;
                                contractor_skill = contractor_skill +"<button type='button' class='btn btn-primary'>"+item_name+"</button>";
                            });
                        }

                        $("#start_skill_place").html(contractor_skill);

                        var coverage_area = "";

                        if(coverage_areas){
                            coverage_areas.forEach(item => {
                                var item_name = item.location;
                                coverage_area = coverage_area +"<button type='button' class='btn btn-primary'>"+item_name+"</button>";
                            });
                        }

                        $("#start_coverage_area_place").html(coverage_area);
                    }
                    else{
                        $("#start_contractor_note").html('');
                        $("#start_contractor_address_line1").html('');
                        $("#start_contractor_tel_number1").val('');
                        $("#start_contractor_short_name").val('');

                        $("#start_skill_place").html('');
                        $("#start_coverage_area_place").html('');

                        $('#start_user_agent').find('option').remove();
                        //$('#user_agent').append(new Option('Select User/Agent' ,''));
                    }

                    if(user_type == 'user'){
                        user_list.forEach(item => {
                            var item_name = item.first_name || item.last_name ? item.first_name + " "+ item.last_name : (item.login_name?item.login_name:item.email);
                            $('#start_user_agent').append(new Option(item_name ,item.user_id));
                        });

                    }
                    else{
                        user_list.forEach(item => {
                            var item_name = item.first_name || item.last_name ? item.first_name + " "+ item.last_name : (item.login_name?item.login_name:item.email);
                            $('#start_user_agent').append(new Option(item_name ,item.id));
                        });

                    }


                }
                else{

                    business_contractor = $('#business_contractor').val();

                    if(business_contractor.charAt(0) == 'C'){
                        $('#user_agent').find('option').remove();

                        $("#contractor_note").html(contractor.note);
                        $("#contractor_address_line1").html(contractor.address_line1);
                        $("#contractor_tel_number1").val(contractor.tel_number1);
                        $("#contractor_short_name").val(contractor.short_name);

                        var contractor_skill = "";

                        if(contractor_skills){
                            contractor_skills.forEach(item => {
                            var item_name = item.skill_name;
                            contractor_skill = contractor_skill +"<button type='button' class='btn btn-primary'>"+item_name+"</button>";
                            });
                        }

                        $("#skill_place").html(contractor_skill);

                        var coverage_area = "";

                        if(coverage_areas){
                            coverage_areas.forEach(item => {
                            var item_name = item.location;
                            coverage_area = coverage_area +"<button type='button' class='btn btn-primary'>"+item_name+"</button>";
                            });
                        }

                        $("#coverage_area_place").html(coverage_area);
                    }
                    else{
                        $("#contractor_note").html('');
                        $("#contractor_address_line1").html('');
                        $("#contractor_tel_number1").val('');
                        $("#contractor_short_name").val('');

                        $("#skill_place").html('');
                        $("#coverage_area_place").html('');

                        $('#user_agent').find('option').remove();
                        //$('#user_agent').append(new Option('Select User/Agent' ,''));
                    }

                    if(user_type == 'user'){
                        user_list.forEach(item => {
                            var item_name = item.first_name || item.last_name ? item.first_name + " "+ item.last_name : (item.login_name?item.login_name:item.email);
                            $('#user_agent').append(new Option(item_name ,item.user_id));
                        });

                    }
                    else{
                        user_list.forEach(item => {
                            var item_name = item.first_name || item.last_name ? item.first_name + " "+ item.last_name : (item.login_name?item.login_name:item.email);
                            $('#user_agent').append(new Option(item_name ,item.id));
                        });

                    }


                }



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
        function handleAssignMaintenance(){
            let message = return_value.message;
            let res = return_value.code;
            let user_list = return_value.result;
            loadingOverlay.cancelAll();
            var textmessage = message;


            if(res == "failure"){

                if(typeof message === 'object'){

                    textmessage = "";
                    Object.keys(message).forEach(function(k) {
                        textmessage+= message[k];
                    });
                }

                $("#ajx_err_msg_assign_maintenance").html(textmessage);
                $("#err_msg_box_assign_maintenance").css('display' , 'block');
                $("#assign_maintenance_btn").removeAttr('disabled');


            }

            else{

                $("#ajx_suc_msg_assign_maintenance").html(message);
                $("#suc_msg_box_assign_maintenance").css('display' , 'block');
                $("#err_msg_box_assign_maintenance").css('display' , 'none');
                setTimeout(function() {
                    loadMaintenances();
                    $('#assignMaintenanceModal').modal('hide');
                }, 3000);



            }



        }
        ///////////////////////////////////////////////////////
        function getNowForStartDateTimes(id_maintenance){

            send( '/utility/get_now_from_server',  {
            }, 'showStartMaintenanceModal', [ id_maintenance]);
        }


        //////////////////////////////////////////////////////
        function showStartMaintenanceModal(id_maintenance){


            let now = return_value.now;
            console.log(now);

            if(now){
                $( '#start_datetimepicker input' ).val(now);
            }
            else{
                $( '#start_datetimepicker input' ).val('');
            }



            send( '/maintenance/contractors_for_assignment',  {
                maintenance:id_maintenance
            }, 'handelShowStartMaintenanceModal', [id_maintenance]);


        }
        ///////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////
        function handelShowStartMaintenanceModal(id_maintenance){

            let message = return_value.message;
            let res = return_value.code;
            let contractors = return_value.contractors;
            let businesses = return_value.businesses;
            let users = return_value.users;
            let agents = return_value.agents;
            let selected_contractor = return_value.selected_contractor;
            let selected_business = return_value.selected_business;
            let selected_user_agent = return_value.selected_user_agent;

            if(res == "failure"){
                var textmessage = message;

                $("#ajx_err_msg_start").html(textmessage);
                $("#err_msg_box_start").css('display' , 'block');

            }

            else{

                $('#user_agent_start').find('option').remove();
                if(selected_business){

                    // console.log(selected_business);

                    var item_name = selected_business.business_name ;
                    $('#business_contractor_readonly').html(item_name);

                    if(users){
                        users.forEach(item => {
                            var item_name = item.first_name +' '+item.last_name;
                            var item_id = item.user_id ;
                            $('#user_agent_start').append(new Option(item_name ,item_id));
                        });
                    }


                    $("#user_place_1").css('display' , 'block');
                    $("#user_place_2").css('display' , 'none');

                }
                else if(selected_contractor ){


                    var item_name = selected_contractor.name ;
                    $('#business_contractor_readonly').html(item_name);

                    if(agents){
                        agents.forEach(item => {
                            var item_name = item.email;
                            var item_id = item.id ;
                            $('#user_agent_start').append(new Option(item_name ,item_id));
                        });
                    }


                    $("#user_place_1").css('display' , 'block');
                    $("#user_place_2").css('display' , 'none');
                }
                else{
                    $("#user_place_1").css('display' , 'none');
                    $("#user_place_2").css('display' , 'block');
                }

                if(selected_user_agent){

                    $('#user_agent_start').val(selected_user_agent);

                    $("#user_place_1").css('display' , 'block');
                    $("#user_place_2").css('display' , 'none');
                }
                else{
                    $("#user_place_1").css('display' , 'none');
                    $("#user_place_2").css('display' , 'block');

                }


            }

            $("#start_maintenance_btn").removeAttr('disabled');

            $('#started_maintenance').val(id_maintenance);
            $('#err_msg_box_start').css('display' , 'none');
            $('#suc_msg_box_start').css('display' , 'none');
            $('#startMaintenanceModal').modal('show');

            loadingOverlay.cancelAll();

        }

        ///////////////////////////////////////////////////////
        function startMaintenance(){
            var spinHandle = loadingOverlay.activate();
            $("#start_maintenance_btn").attr('disabled','disabled');
            $("#err_msg_box_start").css('display' , 'none');

            let started_maintenance = $( '#started_maintenance' ).val();
            let start_date_time = $( '#start_datetimepicker input' ).val();
            old_assign_user = $('#user_agent_start').val();
            new_assign_user = $('#start_user_agent').val();


            send( '/maintenance/start/'+started_maintenance,  {
                start_date_time:start_date_time,
                business_user:old_assign_user,
                contractor_user:new_assign_user,
            }, 'handleStartMaintenance', []);
        }
        ////////////////////////////////////////////////////////
        function handleStartMaintenance(){

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
        function getNowForDateTimes(id_maintenance){

            send( '/utility/get_now_from_server',  {
            }, 'showEndMaintenanceModal', [ id_maintenance]);
        }
        ///////////////////////////////////////////////////////

        function showEndMaintenanceModal(id_maintenance){

            let now = return_value.now;

            if(now){
                $( '#end_datetimepicker input' ).val(now);
            }
            else{
                $( '#end_datetimepicker input' ).val('');
            }


            $("#end_maintenance_btn").removeAttr('disabled');

            $('#ended_maintenance').val(id_maintenance);
            $('#end_note').html('');
            $('#err_msg_box_end').css('display' , 'none');
            $('#suc_msg_box_end').css('display' , 'none');
            $('#endMaintenanceModal').modal('show');

        }
        ///////////////////////////////////////////////////////
        function endMaintenance(){
            var spinHandle = loadingOverlay.activate();

            let ended_maintenance = $( '#ended_maintenance' ).val();
            let end_date_time = $( '#end_datetimepicker input' ).val();
            let end_note = $('textarea#end_note').val();
            $("#end_maintenance_btn").attr('disabled','disabled');
            $("#err_msg_box_end").css('display' , 'none');

            send( '/maintenance/end/'+ended_maintenance,  {
                end_date_time:end_date_time,
                end_note:end_note,
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
