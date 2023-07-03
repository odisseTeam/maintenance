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
                            <h5>{{__('maintenance::maintenance_mgt.maintenance_management')}}</h5>
                            <span>{{__('maintenance::maintenance_mgt.maintenance_management')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="page-header-breadcrumb">
                        <ul class=" breadcrumb breadcrumb-title breadcrumb-padding">
                            <li class="breadcrumb-item">
                                <a href="index.html"><i class="feather icon-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="#!">{{__('maintenance::maintenance_mgt.maintenance_management')}}</a> </li>
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
                                        <h1>
                                            {{__('maintenance::maintenance_mgt.maintenance_management')}}
                                        </h1>
                                    </div>
                                    <div class="box-body card-block">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="box box-primary card">
                                                    <div class="row">

                                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                                        <div class="">
                                                            <div class="box-header card-header">
                                                                <h3>{{__('maintenance::dashboard.maintenance_per_status')}}</h3>

                                                            </div>
                                                            <div>
                                                                <div class="box-body card-block" id="div_barChart">
                                                                    <canvas id="barChart" style="height:250px"></canvas>
                                                                </div>
                                                                <div class="box-body card-block">
                                                                    <div class="row">
                                                                        @foreach($businesses as $business)
                                                                            <div class="border-checkbox-group border-checkbox-group-danger col-md-3">
                                                                                <input  class="border-checkbox selected_business" type="checkbox"  value="{{$business['id_saas_client_business']}}" @if($business['id_saas_client_business'] == 1){{'checked'}}@endif>

                                                                                <label class="form-label border-checkbox-label">{{$business['business_name']}}</label>
                                                                            </div>

                                                                        @endforeach
                                                                    </div>
                                                                    <div class="card-block">
                                                                        <button type="button" onclick="prepareMaintenanceStatusChartData()" class="btn waves-effect waves-light hor-grd btn-grd-primary sdr-primary">Update</button>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>

                                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                                        <div class="">
                                                            <div class="box-header card-header">
                                                                <h3>{{__('maintenance::dashboard.expired_maintenance_count')}}</h3>

                                                            </div>
                                                            <div>
                                                                <div class="box-body card-block" id="div_barChart2">
                                                                    <canvas id="barChart2" style="height:250px"></canvas>
                                                                </div>
                                                                <div class="box-body card-block">
                                                                    <div class="row">
                                                                        @foreach($businesses as $business)
                                                                            <div class="border-checkbox-group border-checkbox-group-danger col-md-3">
                                                                                <input  class="border-checkbox sla_selected_business" type="checkbox"  value="{{$business['id_saas_client_business']}}" @if($business['id_saas_client_business'] == 1){{'checked'}}@endif>

                                                                                <label class="form-label border-checkbox-label">{{$business['business_name']}}</label>
                                                                            </div>

                                                                        @endforeach
                                                                    </div>
                                                                    <div class="card-block">
                                                                        <button type="button" onclick="prepareMaintenanceSlaChartData()" class="btn waves-effect waves-light hor-grd btn-grd-primary sdr-primary">Update</button>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                    </div>



                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="box box-primary card">
                                                    <div class="box-header card-header">
                                                        <h3>{{__('maintenance::dashboard.search_options')}}</h3>

                                                    </div>
                                                    <div class="box-body card-block">

                                                        <div class="row">

                                                        <div class="col-md-10 col-xs-12">


                                                            <div class="row">
                                                                <!-- Start Date -->
                                                                <div class="col-md-3">

                                                                    <div class="input-group date date_place" id="id_0">
                                                                        <input type="text" value="" placeholder="{{__('maintenance::maintenance_mgt.start_date')}}" class="form-control" name="search_start_date" id="search_start_date" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                                        <div class="input-group-addon input-group-append">
                                                                            <div class="input-group-text">
                                                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    {{-- <div class="form-group" style="">
                                                                        <div class="input-group col-xs-10 col-sm-10 col-md-10" style="float:left;padding-right: 15px;padding-left: 15px;">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa-solid fa-calendar"></i>
                                                                            </div>
                                                                            <input name="search_start_date" placeholder="{{__('maintenance::maintenance_mgt.start_date')}}" type="text" class="form-control date active" id="search_start_date" value="" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                                        </div>

                                                                    </div> --}}

                                                                </div>


                                                                <!-- saas_client_business -->
                                                                <div class="col-md-3">
                                                                    <div class="form-group">

                                                                        <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                                            <select class="form-control pull-right select2" id="search_business" name="search_business[]" multiple="multiple"  onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
                                                                                {{-- <option value="">
                                                                                    {{__('maintenance::dashboard.business')}}
                                                                                </option> --}}
                                                                                @foreach($businesses as $business)
                                                                                    <option value="{{$business['id_saas_client_business']}}">
                                                                                        {{$business['business_name']}}
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
                                                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    {{-- <div class="form-group" style="">
                                                                        <div class="input-group col-xs-10 col-sm-10 col-md-10" style="float:left;padding-right: 15px;padding-left: 15px;">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa-solid fa-calendar"></i>
                                                                            </div>
                                                                            <input name="search_end_date" type="text" placeholder="{{__('maintenance::maintenance_mgt.end_date')}}" class="form-control date active" id="search_end_date" value="" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">
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
                                                                <button style="min-width:150px;margin-top:1px;" id="searchbtn" onclick="searchAgain()" type="button" class="btn btn-primary">{{__('maintenance::maintenance_mgt.search')}}</button>
                                                            </div>
                                                            <div class="row">
                                                                <button style="min-width:150px;margin-top:1px;" type="button" onclick="resetSearchbox()" class="btn btn-primary">{{__('maintenance::maintenance_mgt.reset')}}</button>
                                                            </div>

                                                        </div>

                                                    </div>
                                                    </div>

                                                </div>




                                            </div>
                                        </div>

                                        <div class="col-xs-12">
                                            <div class="box col-lg-12 col-md-12 col-sm-12 col-xs-12 card">
                                                <div class="box-header card-header">
                                                    <h3>{{__('maintenance::dashboard.maintenances_list')}}</h3>
                                                </div>

                                                <div class="box-body card-block">
                                                    <div class="row" style="float: right;">
                                                        <a style="min-width:150px;margin-top:1px;" href="/maintenance/mgt/create" class="btn btn-primary">{{trans('maintenance::dashboard.create_job')}}</a>
                                                        <br>
                                                    </div>
                                                </div>
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
                                                                <th>{{__('maintenance::dashboard.operation')}}</th>


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
                                                <h4 class="modal-title" id="deleteMaintenanceModalLabel">{{trans('maintenance::dashboard.delete_maintenance')}}</h4>
                                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                                                        aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            </div>


                                            <div class="modal-body">
                                                    <div class="alert alert-danger alert-dismissible" id="err_msg_box_delete_maintenance" style="display: none">
                                                        <div id="ajx_err_msg_delete_maintenance"></div>
                                                    </div>
                                                    <div class="alert alert-success alert-dismissible" id="suc_msg_box_delete_maintenance" style="display: none">
                                                        <div id="ajx_suc_msg_delete_maintenance"></div>
                                                    </div>

                                                    <p>{{trans('maintenance::dashboard.do_you_want_to_delete_maintenance')}}</p>
                                                    <input type="hidden" id="deleted_business">
                                                    <input type="hidden" id="deleted_maintenance">
                                            </div>



                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-warning mobtn"
                                                    data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::contractor.cancel')}}</button>
                                                <button type="button" class="btn btn-danger sdr-danger mobtn"
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
                                                <h4 class="modal-title" id="assignMaintenanceModalLabel">{{trans('maintenance::dashboard.assign_maintenance')}}</h4>
                                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                                                        aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            </div>


                                            <div class="modal-body">
                                                    <div class="alert alert-danger alert-dismissible" id="err_msg_box_assign_maintenance" style="display: none">
                                                        <div id="ajx_err_msg_assign_maintenance"></div>
                                                    </div>
                                                    <div class="alert alert-success alert-dismissible" id="suc_msg_box_assign_maintenance" style="display: none">
                                                        <div id="ajx_suc_msg_assign_maintenance"></div>
                                                    </div>

                                                    <input type="hidden" id="assigned_maintenance">
                                                    <input type="hidden" id="assigned_business">




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
                                                            <button type="button" class="btn btn-primary waves-effect waves-light hor-grd btn-grd-primary sdr-primary" onclick="search_contractors()">{{trans('maintenance::maintenance.search')}}</button>
                                                        </div>
                                                    </div>


                                                    <hr style="border-top:3px solid #d2d6de;">




                                                    <!-- Business/contractor -->
                                                    <div class="form-group row">
                                                        <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::dashboard.business_contractor')}}:</label>
                                                        <div class="col-sm-5 col-md-5 col-lg-5">


                                                            <select name="business_contractor" id="business_contractor" onchange="loadUserAgents()" class="form-control select ">
                                                                <option value="">{{trans('maintenance::dashboard.select_business_contractor')}}</option>
                                                                @foreach ($businesses as $business)
                                                                <option value="B{{$business['id_saas_client_business']}}">
                                                                    {{ $business['business_name']}}
                                                                </option>
                                                                @endforeach
                                                                @foreach ($contractors as $contractor)
                                                                <option value="C{{ $contractor->id_contractor }}">
                                                                    {{ $contractor->name }}
                                                                </option>
                                                                @endforeach



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


                                                    <!-- vat number-->

                                                    <div class="form-group row ">

                                                        <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.vat_number') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                            <input class="form-control" id="contractor_vat_number" readonly value="@if(isset($selected_contractor)){{$selected_contractor->vat_number}}@endif" >
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

                                                        <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::contractor.address_line1') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                            <textarea class="form-control" rows="4" id="contractor_address_line1" readonly column="40">@if(isset($selected_contractor)){{$selected_contractor->address_line1}}@endif</textarea>
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
                                                <button type="button" class="btn btn-warning mobtn"
                                                    data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::dashboard.cancel')}}</button>
                                                <button type="button" class="btn btn-danger sdr-danger mobtn" id="assign_maintenance_btn"
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
                                                <h4 class="modal-title" id="startMaintenanceModalLabel">{{trans('maintenance::dashboard.start_maintenance')}}</h4>
                                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                                                        aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            </div>


                                            <div class="modal-body">
                                                    <div class="alert alert-danger alert-dismissible" id="err_msg_box_start" style="display: none">
                                                        <div id="ajx_err_msg_start"></div>
                                                    </div>
                                                    <div class="alert alert-success alert-dismissible" id="suc_msg_box_start" style="display: none">
                                                        <div id="ajx_suc_msg_start"></div>
                                                    </div>
                                                    <input type="hidden" id="started_business">
                                                    <input type="hidden" id="started_maintenance">

                                                    <div class="form-group row">
                                                        <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::dashboard.select_start_date_of_job') }}:</label>
                                                        <div class="col-sm-5 col-md-5 col-lg-5">

                                                            <div class="input-group date date_place" id="id_2">
                                                                <input type="text" value="" placeholder="{{__('maintenance::maintenance_mgt.start_date')}}" class="form-control" name="start_datetimepicker" id="start_datetimepicker">
                                                                <div class="input-group-addon input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- <div class="form-group">
                                                                <div class="input-group date" id="start_datetimepicker">
                                                                    <input type="text" class="form-control">
                                                                    <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                                    </span>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>


                                            </div>



                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-warning mobtn" data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::dashboard.cancel')}}</button>
                                                <button type="button" class="btn btn-danger sdr-danger mobtn" id="start_maintenance_btn" onclick="startMaintenance()">{{trans('maintenance::dashboard.save')}}</button>
                                            </div>


                                        </div>
                                    </div>
                                </div>



                                <!-- Modal -->
                                <div class="modal fade" id="endMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="endMaintenanceModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" style="max-width: 60%;">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="endMaintenanceModalLabel">{{trans('maintenance::dashboard.end_maintenance')}}</h4>

                                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                                                        aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            </div>


                                            <div class="modal-body">
                                                    <div class="alert alert-danger alert-dismissible" id="err_msg_box_end" style="display: none">
                                                        <div id="ajx_err_msg_end"></div>
                                                    </div>
                                                    <div class="alert alert-success alert-dismissible" id="suc_msg_box_end" style="display: none">
                                                        <div id="ajx_suc_msg_end"></div>
                                                    </div>

                                                    <input type="hidden" id="ended_maintenance">
                                                    <input type="hidden" id="ended_business">

                                                    <div class="form-group row">
                                                        <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::dashboard.select_end_date_of_job') }}:</label>
                                                        <div class="col-sm-5 col-md-5 col-lg-5">


                                                            <div class="input-group date date_place" id="id_3">
                                                                <input type="text" value="" placeholder="{{__('maintenance::maintenance_mgt.end_date')}}" class="form-control" name="end_datetimepicker" id="end_datetimepicker">
                                                                <div class="input-group-addon input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                                    </div>
                                                                </div>
                                                            </div>



                                                        {{--
                                                            <div class="form-group">
                                                                <div class="input-group date" id="end_datetimepicker">
                                                                    <input type="text" class="form-control">
                                                                    <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                                    </span>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>


                                            </div>



                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-warning mobtn" data-dismiss="modal"
                                                data-bs-dismiss="modal">{{trans('maintenance::dashboard.cancel')}}</button>
                                                <button type="button" class="btn btn-danger sdr-danger mobtn" id="end_maintenance_btn"
                                                    onclick="endMaintenance()">{{trans('maintenance::dashboard.save')}}</button>
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


    <script>

        $(document).ready(function () {


        $('.select2').select2();

            $('.date_place').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "DD-MM-YYYY hh:mm",
            });

            //loadMaintenances();
            prepareMaintenanceStatusChartData();
            prepareMaintenanceSlaChartData();

        });
        ////////////////////////////////////////////////
        function search_contractors() {

            // var spinHandle = loadingOverlay.activate();


            let maintenance_id = $('#assigned_maintenance').val();
            let business = $('#assigned_business').val();
            let contractor_skill = $('#contractor_skill').val();


            send('/maintenance/mgt/contractor_skill/contractors', {
                business: business,
                maintenance: maintenance_id,
                contractor_skill: contractor_skill,

            }, 'handleSearchContractors', []);

        }

        ///////////////////////////////////////////////////////
        function handleSearchContractors()
        {
            let message = return_value.message;
            let res = return_value.code;
            let contractor_list = return_value.contractors;
            let business_list = return_value.businesses;



            $("#contractor_note").html('');
            $("#contractor_address_line1").html('');
            $("#contractor_vat_number").val('');
            $("#contractor_tel_number1").val('');
            $("#contractor_short_name").val('');

            if(res == "failure"){
                var textmessage = message;
                alert(textmessage);

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

                    Object.keys(contractor_list).forEach(function(key) {

                        console.log(key);
                        var item_name = contractor_list[key]['name'] ;
                        var item_id = 'C'+contractor_list[key]['id_contractor'] ;
                        $('#business_contractor').append(new Option(item_name ,item_id));
                    });

                }

                // if(contractor_list){



                //     contractor_list.forEach(item => {
                //     var item_name = item['name'];
                //     $('#business_contractor').append(new Option(item_name ,'C'+item['id_contractor']));
                //     });
                // }



                $("#contractor_note").html("");


            }

            // loadingOverlay.cancelAll();

        }



        ///////////////////////////////////////////////////////
        let prepareMaintenanceStatusChartData = function () {
            var businesses = $('.selected_business:checkbox:checked').map(function() {
                return this.value;
            }).get();

            send( '/maintenance/mgt/statuses/charts',  {
                businesses:businesses,
            }, 'handleMaintenanceStatusChart', []);

        };
        ///////////////////////////////////////////////////////////////////////////
        let prepareMaintenanceSlaChartData = function () {
            var businesses = $('.sla_selected_business:checkbox:checked').map(function() {
                return this.value;
            }).get();
            send( '/maintenance/mgt/sla/charts',  {
                businesses:businesses,
            }, 'handleMaintenanceSlaChart', []);

        };

        ///////////////////////////////////////////////////////
        function handleMaintenanceStatusChart(){

            let res = return_value.code;
            if(res == "failure"){

            }
            else{
                let widget_data = return_value.widget_data;
                ShowChart( widget_data);

            }


        }
        ///////////////////////////////////////////////////////
        function handleMaintenanceSlaChart(){

            let res = return_value.code;
            if(res == "failure"){

            }
            else{
                let widget_data = return_value.widget_data;
                ShowChart2( widget_data);

            }


        }
        ///////////////////////////////////////////////////////
        function ShowChart(widget_data){


            var place = document.getElementById("div_barChart").innerHTML = '<canvas id="barChart" width="400" height="200"></canvas>';
            var bar = document.getElementById("barChart").getContext('2d');
            var myBarChart = new Chart(bar, {
            type: 'bar',
            data: widget_data,
            options: {
                barValueSpacing: 20
            }
            });

        }
        ///////////////////////////////////////////////////////
        function ShowChart2(widget_data){


            var place = document.getElementById("div_barChart2").innerHTML = '<canvas id="barChart2" width="400" height="200"></canvas>';
            var bar = document.getElementById("barChart2").getContext('2d');
            var myBarChart = new Chart(bar, {
            type: 'bar',
            data: widget_data,
            options: {
                barValueSpacing: 20
            }
            });

        }
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

            // var spinHandle = loadingOverlay.activate();
            business = $('#search_business').val();
            category = $('#search_category').val();
            priority = $('#search_priority').val();
            status = $('#search_status').val();
            title = $('#search_title').val();
            start_date = $('#search_start_date').val();
            end_date = $('#search_end_date').val();
            assignee = $('#search_assignee').val();


            send( '/maintenance/mgt_maintenances_list',  {
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
                    var m_url = maintenance_list[k]["m_url"];
                    var id_business = maintenance_list[k]["id_business"];
                    var business_name = maintenance_list[k]["business_name"];
                    var title = maintenance_list[k]["maintenance_job_title"];
                    var sla = maintenance_list[k]["remain_time"];
                    var priority = maintenance_list[k]["priority_name"];
                    var status = maintenance_list[k]["job_status_name"];
                    var job_report_date_time = maintenance_list[k]["job_report_date_time"];
                    var job_start_date_time = maintenance_list[k]["job_start_date_time"]?maintenance_list[k]["job_start_date_time"]:'-';
                    var job_finished_date_time = maintenance_list[k]["job_finish_date_time"]?maintenance_list[k]["job_finish_date_time"]:'-';
                    var staff_reporter = maintenance_list[k]["first_name"]+' '+maintenance_list[k]["last_name"];
                    var resident_reporter = maintenance_list[k]["resident_reporter"]? maintenance_list[k]["resident_reporter"]:'-';

                    var operation = '<a href="' + m_url + '" target="_blank" class="btn btn-primary allign-btn sdr-primary" data-toggle="tooltip" title="Maintenance Detail" data-original-title="Maintenance Detail">' +
                        '<i class="fa-solid fa-info fa fa-info" aria-hidden="true"></i>' +
                        '</a>' +

                        '<a href="#" class="btn btn-primary allign-btn sdr-primary" title="Assign Maintenance" onclick="showAssignMaintenanceModal('+id_business+','+id_maintenance_job+')">'+
                        '<i class="fa fa-solid fa-user"></i>'+
                        '</a>'+

                        '<a href="#" class="btn btn-primary allign-btn sdr-primary" title="Start Maintenance" onclick="showStartMaintenanceModal('+id_business +','+ id_maintenance_job+')"> '+
                        '<i class="fa fa-solid fa-play"></i>'+
                        '</a>'+
                        '<a href="#" class="btn btn-primary allign-btn sdr-primary" title="Stop Maintenance" onclick="showEndMaintenanceModal('+id_business + ',' + id_maintenance_job+')"> '+
                        '<i class="fa fa-solid fa-stop"></i>'+
                        '</a>'+



                        '<button style="margin-right: 1px;" type="button" class="btn btn-danger allign-btn sdr-danger alert-confirm m-b-10" title="Delete Maintenance" onclick="showDeleteMaintenanceModal('+id_business +','+ id_maintenance_job+')">'+
                        '<i class="fa fa-solid fa-trash"></i>'+
                        '</button>';


                    htmlValue= htmlValue +"<tr><td>"+(counter)+"</td><td>"+business_name+"</td><td>"+category+"</td><td>"+title+"</td><td>"+sla+"</td><td>"
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

                    { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3,4,5,6,7,8,9,10,11,12] },{ "width": "20%", "targets": 12 }
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


        ///////////////////////////////////////////////////////

        function showDeleteMaintenanceModal(id_business , id_maintenance){

            $('#deleted_business').val(id_business);
            $('#deleted_maintenance').val(id_maintenance);
            $('#err_msg_box_delete_maintenance').css('display' , 'none');
            $('#suc_msg_box_delete_maintenance').css('display' , 'none');
            $('#deleteMaintenanceModal').modal('show');

        }

        ///////////////////////////////////////////////////////
        function deleteMaintenance(){
            // var spinHandle = loadingOverlay.activate();

            let deleted_business = $( '#deleted_business' ).val();
            let deleted_maintenance = $( '#deleted_maintenance' ).val();

            send( '/maintenance/mgt/delete/'+deleted_maintenance,  {
                business:deleted_business,
                maintenance:deleted_maintenance
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


            // loadingOverlay.cancelAll();

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

        function showAssignMaintenanceModal(id_business , id_maintenance){

            $("#assign_maintenance_btn").removeAttr('disabled');

            $('#assigned_business').val(id_business);
            $('#assigned_maintenance').val(id_maintenance);

            $('#business_contractor').find('option').not(':first').remove();
            $('#user_agent').find('option').not(':first').remove();


            // var spinHandle = loadingOverlay.activate();

            send( '/maintenance/mgt/business_contractors',  {
                business :id_business,
                maintenance :id_maintenance,
            }, 'handleLoadBusinessContractor', []);



        }
        ////////////////////////////////////////////////////////
        function handleLoadBusinessContractor()
        {
            let message = return_value.message;
            let res = return_value.code;
            let business_list = return_value.businesses;
            let contractor_list = return_value.contractors;
            let users = return_value.users;
            let agents = return_value.agents;
            let selected_contractor = return_value.selected_contractor;
            let selected_business = return_value.selected_business;
            let selected_user_agent = return_value.selected_user_agent;

            if(res == "failure"){
                var textmessage = message;

                $("#ajx_err_msg_assign_maintenance").html(textmessage);
                $("#err_msg_box_assign_maintenance").css('display' , 'block');

            }

            else{

                business_list.forEach(item => {
                    var item_name = item.business_name;
                    $('#business_contractor').append(new Option(item_name ,'B'+item.id_saas_client_business));
                });

                if(contractor_list){

                    Object.keys(contractor_list).forEach(function(key) {

                        console.log(key);
                        var item_name = contractor_list[key]['name'] ;
                        var item_id = 'C'+contractor_list[key]['id_contractor'] ;
                        $('#business_contractor').append(new Option(item_name ,item_id));
                    });
                }
                // contractor_list.forEach(item => {
                //     $('#business_contractor').append(new Option(item.name ,'C'+item.id_contractor));
                // });







                if(users){
                    users.forEach(item => {
                    var item_name = item.first_name +' '+item.last_name;
                    var item_id = item.user_id ;
                    $('#user_agent').append(new Option(item_name ,item_id));
                });
                }




                if(agents){
                    agents.forEach(item => {
                    var item_name = item.email;
                    var item_id = item.id ;
                    $('#user_agent').append(new Option(item_name ,item_id));
                });
                }



                if(selected_contractor){
                    $('#business_contractor').val('C'+selected_contractor.id_contractor);
                }
                else if(selected_business){
                    $('#business_contractor').val('B'+selected_business.id_saas_client_business);
                }
                if(selected_user_agent){
                    $('#user_agent').val(selected_user_agent);
                }



            }


            // loadingOverlay.cancelAll();
            $('#err_msg_box_assign_maintenance').css('display' , 'none');
            $('#suc_msg_box_assign_maintenance').css('display' , 'none');
            $('#assignMaintenanceModal').modal('show');

        }

        ///////////////////////////////////////////////////////
        function loadUserAgents(){

            // var spinHandle = loadingOverlay.activate();
            business = $('#assigned_business').val();
            business_contractor = $('#business_contractor').val();

            send( '/maintenance/mgt/business_contractor/user_agents',  {
                business :business,
                business_contractor :business_contractor,
            }, 'handleLoadUserAgents', []);

        }
        ///////////////////////////////////////////////////////
        function handleLoadUserAgents()
        {
            let message = return_value.message;
            let res = return_value.code;
            let user_list = return_value.agents;
            let contractor = return_value.contractor;
            let user_type = return_value.user_type;


            if(res == "failure"){
                var textmessage = message;

                $("#ajx_err_msg_assign_maintenance").html(textmessage);
                $("#err_msg_box_assign_maintenance").css('display' , 'block');

                $("#contractor_note").html('');
                $("#contractor_address_line1").html('');
                $("#contractor_vat_number").val('');
                $("#contractor_tel_number1").val('');
                $("#contractor_short_name").val('');

                $('#user_agent').find('option').remove();
                $('#user_agent').append(new Option('Select User/Agent' ,''));

            }

            else{
                business_contractor = $('#business_contractor').val();


                if(business_contractor.charAt(0) == 'C'){
                    $('#user_agent').find('option').remove();

                    $("#contractor_note").html(contractor.note);
                    $("#contractor_address_line1").html(contractor.address_line1);
                    $("#contractor_vat_number").val(contractor.vat_number);
                    $("#contractor_tel_number1").val(contractor.tel_number1);
                    $("#contractor_short_name").val(contractor.short_name);
                }
                else{
                    $("#contractor_note").html('');
                    $("#contractor_address_line1").html('');
                    $("#contractor_vat_number").val('');
                    $("#contractor_tel_number1").val('');
                    $("#contractor_short_name").val('');


                    $('#user_agent').find('option').remove();
                    $('#user_agent').append(new Option('Select User/Agent' ,''));
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


            // loadingOverlay.cancelAll();

        }
        ///////////////////////////////////////////////////////
        function assignMaintenance(){

            $("#assign_maintenance_btn").attr('disabled','disabled');

            // var spinHandle = loadingOverlay.activate();
            business = $('#assigned_business').val();
            maintenance = $('#assigned_maintenance').val();
            user = $('#user_agent').val();

            send( '/maintenance/mgt/assign_user',  {
                business :business,
                maintenance :maintenance,
                user :user,
            }, 'handleAssignMaintenance', []);

        }
        ///////////////////////////////////////////////////////
        function handleAssignMaintenance()
        {
            let message = return_value.message;
            let res = return_value.code;
            // loadingOverlay.cancelAll();
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
                setTimeout(function() {$('#assignMaintenanceModal').modal('hide');}, 3000);



            }



        }
        ///////////////////////////////////////////////////////

        function showStartMaintenanceModal(id_business , id_maintenance){

            $("#start_maintenance_btn").removeAttr('disabled');

            $('#start_datetimepicker').val('');
            $('#started_maintenance').val(id_maintenance);
            $('#started_business').val(id_business);

            $('#err_msg_box_start').css('display' , 'none');
            $('#suc_msg_box_start').css('display' , 'none');
            $('#startMaintenanceModal').modal('show');

        }
        ///////////////////////////////////////////////////////
        function startMaintenance(){
            // var spinHandle = loadingOverlay.activate();

            $("#start_maintenance_btn").attr('disabled','disabled');
            $('#err_msg_box_start').css('display' , 'none');


            let started_maintenance = $( '#started_maintenance' ).val();
            let started_business = $( '#started_business' ).val();

            let start_date_time = $( '#start_datetimepicker' ).val();

            send( '/maintenance/mgt/start/'+started_maintenance,  {
                business:started_business,
                start_date_time:start_date_time,
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


            // loadingOverlay.cancelAll();

        }
        ///////////////////////////////////////////////////////

        function showEndMaintenanceModal(id_business , id_maintenance){

            $("#end_maintenance_btn").removeAttr('disabled');

            $('#end_datetimepicker').val('');
            $('#ended_maintenance').val(id_maintenance);
            $('#ended_business').val(id_business);

            $('#err_msg_box_end').css('display' , 'none');
            $('#suc_msg_box_end').css('display' , 'none');
            $('#endMaintenanceModal').modal('show');

        }
        ///////////////////////////////////////////////////////
        function endMaintenance(){
            // var spinHandle = loadingOverlay.activate();

            $("#end_maintenance_btn").attr('disabled','disabled');
            $('#err_msg_box_end').css('display' , 'none');



            let ended_maintenance = $( '#ended_maintenance' ).val();
            let ended_business = $( '#ended_business' ).val();

            let end_date_time = $( '#end_datetimepicker' ).val();

            send( '/maintenance/mgt/end/'+ended_maintenance,  {
                business:ended_business,
                end_date_time:end_date_time,
            }, 'handleEndMaintenance', []);
        }
        ////////////////////////////////////////////////////////
        function handleEndMaintenance(){
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
                $("#ajx_suc_msg_end").html(message);
                $("#suc_msg_box_end").css('display' , 'block');

                setTimeout(function() {
                    $('#endMaintenanceModal').modal('hide');
                    prepareMaintenanceStatusChartData();
                    prepareMaintenanceSlaChartData();
                    loadMaintenances();
                }, 3000);

            }


            // loadingOverlay.cancelAll();

        }




    </script>


@endsection
