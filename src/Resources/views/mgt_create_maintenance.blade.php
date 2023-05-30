@extends('layouts.blank_js')

@section('page_title', session('saas_title') . ' ' . __('maintenance::maintenance.maintenance'))


@section('body_class', 'login-page')

@section('css')


    <link rel="stylesheet" type="text/css" href="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/iCheck/all.css') }}" />
    <link rel="stylesheet" href="{{ asset('resources/select2/select2.min.css') }}" />

    <style>
        .select2-selection--multiple {
            border: 0px;
        }

        .select2-container--default {
            border: 0px;
        }
    </style>

@endsection


@section('content')


    @if ($message = Session::get('message'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>{{ $message }}</strong>
        </div>
    @endif
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="box-body">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa-solid fa-ban"></i>{{ session('error') }}</p>
            </div>
        </div>
    @endif
    @if (session('success'))
        <div class="box-body">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa-solid fa-check"></i>{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="box-body">

            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                @foreach ($errors->all() as $error)
                    <p><i class="icon fa-solid fa-ban"></i>{{ $error }}</p>
                @endforeach
            </div>
        </div>

    @endif

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{ trans('maintenance::maintenance.page-title') }}
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">





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
                            <span>{{__('maintenance::maintenance_mgt.create_maintenance')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="page-header-breadcrumb">
                        <ul class=" breadcrumb breadcrumb-title breadcrumb-padding">
                            <li class="breadcrumb-item">
                                <a href="index.html"><i class="feather icon-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="/maintenance/management">{{__('maintenance::maintenance_mgt.maintenance_management')}}</a> </li>
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
                            <div class="col-xs-12">
                                <div class="box box-primary card">
                                    <div class="box-header card-header">

                                        <form action="/maintenance/mgt/new/save" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="box-body card-block">

                                                <div class="form-group row">
                                                    <div class="col-sm-12 col-xs-12 col-md-12">


                                                        <!-- saas client budiness-->
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.saas_client_business') }}:</label>
                                                            <div class="col-sm-5 col-md-5 col-lg-5">


                                                                <select name="saas_client_business" id="saas_client_business"
                                                                    class="form-control select ">
                                                                    <option value="">
                                                                        {{ __('maintenance::maintenance.select_saas_client_business') }}
                                                                    </option>
                                                                    @if (isset($businesses))
                                                                        @foreach ($businesses as $business)
                                                                            <option
                                                                                value="{{ $business['id_saas_client_business'] }}"
                                                                                @if (old('saas_client_business') == $business['id_saas_client_business']) {{ 'selected' }} @endif>
                                                                                {{ $business['business_name'] }}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                    @endif


                                                                </select>


                                                            </div>
                                                        </div>



                                                        <!-- maintenance title-->

                                                        <div class="form-group row">
                                                            <label
                                                                    class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.maintenance_title') }}:</label>

                                                                    <div class="col-xs-10 col-sm-10 col-md-10">
                                                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                                            <input class="form-control" name="maintenance_title"
                                                                                id="maintenance_title"
                                                                                value="@if (old('maintenance_title')) {{ old('maintenance_title') }} @endif" />


                                                                        </div>


                                                                    </div>



                                                        </div>
                                                        <!-- </div> -->


                                                        <!-- description-->

                                                        <div class="form-group row">
                                                            <label
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.description') }}:</label>
                                                            <div class="col-xs-10 col-sm-10 col-md-10">
                                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                                    <textarea class="form-control" rows="4" name="description" id="description" column="40">{{ null !== old('description') ? old('description'): "" }}</textarea>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        <!-- attachments-->

                                                        <div class="form-group row">
                                                            <label  style="margin-top:10px;"
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.attachments') }}:</label>
                                                            <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                                <!-- <div class=" input-group" id="contaner"> -->


                                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                                    <!-- <label class="control-label col-sm-3 col-md-3 col-lg-3 col-md-offset-1">{{ trans('maintenance::maintenance.file') }}:</label> -->
                                                                    <div>
                                                                        <input type="file" id="file" name="files[]" class=""
                                                                            multiple style="margin-top:10px;">

                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.file_description') }}:</label>
                                                            <div class="col-xs-10 col-sm-10 col-md-10">
                                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                                    <textarea class="form-control" rows="4" name="file_description" id="file_description" column="40">{{ old('file_description') }}</textarea>
                                                                </div>

                                                            </div>
                                                        </div>




                                                        <!-- datetime -->
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.date_time') }}:</label>
                                                            <div class="col-xs-5 col-sm-5 col-md-5">

                                                                <div class="input-group date date_place" id="id_2">
                                                                    <input type="text" id="maintenance_date" name="maintenance_date" placeholder="{{ trans('maintenance::maintenance.date_time') }}" value="@if (isset($maintenance)) {{ $maintenance->maintenance_date_time }} @elseif (old('maintenance_date')) {{ old('maintenance_date') }} @endif"  class="form-control" name="start_datetimepicker" id="start_datetimepicker">
                                                                    <div class="input-group-addon input-group-append">
                                                                        <div class="input-group-text">
                                                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{-- <div class="input-group">
                                                                    <span class="input-group-addon"><i class="fa-solid fa-calendar"
                                                                            onclick="setNow('maintenance_date')"></i>
                                                                    </span>

                                                                    <input type="text" class="form-control"
                                                                        onkeydown="event.preventDefault()"
                                                                        placeholder="{{ trans('maintenance::maintenance.date_time') }}"
                                                                        autocomplete="off"
                                                                        value="@if (isset($maintenance)) {{ $maintenance->maintenance_date_time }} @elseif (old('maintenance_date')) {{ old('maintenance_date') }} @endif"
                                                                        id="maintenance_date" name="maintenance_date">
                                                                </div> --}}
                                                            </div>
                                                        </div>


                                                        <!-- category-->
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.category') }}:</label>
                                                            <div class="col-sm-5 col-md-5 col-lg-5">



                                                                <select name="maintenance_category" id="maintenance_category" class="form-control select ">
                                                                    <option value="" >{{trans('maintenance::maintenance.select_category')}}</option>
                                                                    @if(isset($maintenance_categories))
                                                                    @foreach ($maintenance_categories as $maintenance_category)
                                                                    <option value="{{ $maintenance_category->id_maintenance_job_category_ref }}">
                                                                        {{ $maintenance_category->job_category_name }}
                                                                    </option>
                                                                    @endforeach
                                                                    @else
                                                                    @endif


                                                                </select>


                                                            </div>
                                                        </div>



                                                        <!-- locations-->
                                                        <div class="form-group row">

                                                            <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::maintenance.locations')}}:</label>
                                                            <div class="col-sm-5 col-md-5 col-lg-5 col-xs-5">


                                                                <select name="locations[]" id="locations" class="form-control select2"
                                                                    placeholder="{{ __('maintenance.select_locations') }}"
                                                                    multiple="multiple" onchange="loadResidentReporters()">
                                                                    <!-- <option value="">{{ __('maintenance.select_locations') }}</option> -->
                                                                    @if (isset($locations))
                                                                        @foreach ($locations as $location)
                                                                            <option value="{{ $location->id }}"
                                                                            {{in_array($location->id, old("locations") ?: []) ? "selected": ""}}
                                                                            >
                                                                                {{ $location->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                    @endif


                                                                </select>


                                                            </div>
                                                        </div>


                                                        <!-- priority-->

                                                        <div class="form-group row">
                                                            <label
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.priority') }}:</label>
                                                            <div class="col-sm-5 col-md-5 col-lg-5">


                                                                <select name="priority" id="priority" class="form-control select ">
                                                                    <option value="">
                                                                        {{ trans('maintenance::maintenance.select_priority') }}</option>
                                                                    @if (isset($priorities))
                                                                        @foreach ($priorities as $priority)
                                                                            <option
                                                                                value="{{ $priority->id_maintenance_job_priority_ref }}"
                                                                                @if (old('priority') == $priority->id_maintenance_job_priority_ref) {{ 'selected' }} @endif>
                                                                                {{ $priority->priority_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                    @endif


                                                                </select>


                                                            </div>
                                                        </div>


                                                        <!-- resident reporter-->

                                                        <div class="form-group row">
                                                            <label
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.resident_reporter') }}:</label>
                                                            <div class="col-sm-5 col-md-5 col-lg-5">


                                                                <select name="resident_reporter" id="resident_reporter"
                                                                    class="form-control select ">

                                                                </select>


                                                            </div>
                                                        </div>


                                                        <input type="hidden" id="previous_resident_value" name="previous_resident_value" />

                                                        <!-- parent job-->

                                                        <!-- <div class="form-group row">
                                                                    <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.parent_job') }}:</label>
                                                                    <div class="col-sm-5 col-md-5 col-lg-5">

                                                                        <div class="input-group ">

                                                                            <input class="form-control"  name="parent_job" id="parent_job"  style="display: inline-block;">

                                                                                <span class="input-group-btn" >
                                                                                    <button class="btn btn-default" type="button" style="display: inline-block;" onclick="showAddParentModal()">{{ trans('maintenance::maintenance.select_parent_job') }}</button>
                                                                                </span>
                                                                        </div>

                                                                    </div>
                                                        </div> -->




                                                        <div class="box-footer text-right">


                                                            <a href="/maintenance/management"><button type="button"
                                                                class="btn btn-warning">{{ trans('maintenance::maintenance.cancel') }}</button></a>



                                                            <button type="submit" id="save_maintenance"
                                                                class="btn btn-primary">{{ trans('maintenance::maintenance.save') }}</button>



                                                        </div>





                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    </section>
    <!-- /.content -->


    <!-- Modal -->
    <div class="modal fade" id="attachFileModal" tabindex="-1" role="dialog" aria-labelledby="attachFileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="attachFileModalLabel">{{ trans('maintenance::maintenance.attach_file') }}
                    </h4>
                </div>
                <div class="box">
                    <div class="box-header">
                        <h4 style="margin-left:40px;">{{ trans('maintenance::maintenance.attach_new_file') }}</h4>

                    </div>

                    <div class="box-body">
                        <!-- <div class="form-horizontal" id="attach_file_form"> -->
                        <form id="attach_file_form" method="post" enctype="multipart/form-data" novalidate
                            action="/maintenance/upload/file">
                            @csrf

                            <div class="modal-body">
                                <div class="alert alert-danger alert-dismissible" id="err_msg_box" style="display: none">
                                    <div id="ajx_err_msg"></div>
                                </div>
                                <div class="alert alert-success alert-dismissible" id="suc_msg_box"
                                    style="display: none">
                                    <div id="ajx_suc_msg"></div>
                                </div>
                                <div class="row">

                                    <!-- file -->
                                    <div class="row form-group col-sm-6 col-md-6 col-lg-6">
                                        <label
                                            class="control-label col-sm-3 col-md-3 col-lg-3 col-md-offset-1">{{ trans('maintenance::maintenance.file') }}:</label>
                                        <div>
                                            <input type="file" id="file" name="files[]"
                                                class="col-sm-8 col-md-8 col-lg-8" multiple style="margin-top:10px;">

                                        </div>
                                    </div>

                                    <!-- description -->
                                    <div class="row form-group col-sm-6 col-md-6 col-lg-6">
                                        <label
                                            class="control-label col-sm-5 col-md-5 col-lg-5">{{ trans('maintenance::maintenance.description') }}:</label>

                                        <textarea id="description" name="description" class="col-sm-7 col-md-7 col-lg-7"></textarea>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="form-label col-sm-2 col-form-label">Textarea</label>
                                        <div class="col-sm-10">
                                            <textarea rows="5" cols="5" class="form-control"
                                            placeholder="Default textarea"></textarea>
                                        </div>
                                    </div>


                                </div>


                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning"
                                    data-dismiss="modal">{{ trans('maintenance::maintenance.cancel') }}</button>
                                <button type="button" class="btn btn-primary" id="final_add_file"
                                    onclick="submitAttachMaintenanceDocument()">{{ trans('maintenance::maintenance.save') }}</button>
                            </div>
                        </form>

                        <!-- </div> -->

                    </div>

                </div>


            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addParentJobModal" tabindex="-1" role="dialog"
        aria-labelledby="addParentJobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="addParentJobModalLabel">
                        {{ trans('maintenance::maintenance.add_parent_job') }}</h4>
                </div>
                <div class="box">
                    <div class="box-header">
                        <h4 style="margin-left:40px;">{{ trans('maintenance::maintenance.select_parent_job') }}</h4>

                    </div>
                    <div class="box-body">
                        <div class="form-horizontal" id="parent_job_form">

                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-danger alert-dismissible" id="err_msg_box_parent_job"
                                    style="display: none">
                                    <div id="ajx_err_msg_parent_job"></div>
                                </div>
                                <div class="alert alert-success alert-dismissible" id="suc_msg_box_parent_job"
                                    style="display: none">
                                    <div id="ajx_suc_msg_parent_job"></div>
                                </div>
                                <div class="row">

                                    <!-- parent_job -->
                                    <div class="row form-group col-sm-12 col-md-12 col-lg-12">
                                        <label
                                            class="control-label col-sm-3 col-md-3 col-lg-3">{{ trans('maintenance::maintenance.parent_job') }}:</label>

                                        <select name="parent_job_modal[]" id="parent_job_modal"
                                            class="form-control  col-sm-8 col-md-8 col-lg8"
                                            placeholder="{{ __('maintenance.select_parent_jobs') }}" style="width:40%">
                                            <option value="0">
                                                {{ trans('maintenance::maintenance.select_parent_job') }}</option>
                                            @if (isset($jobs))
                                                @foreach ($jobs as $job)
                                                    <option value="{{ $job->id_maintenance_job }}">
                                                        {{ $job->maintenance_job_title }}_{{ $job->job_report_date_time }}
                                                    </option>
                                                @endforeach
                                            @else
                                            @endif


                                        </select>
                                    </div>

                                </div>


                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning"
                                    data-dismiss="modal">{{ trans('maintenance::maintenance.cancel') }}</button>
                                <button type="button" class="btn btn-primary"
                                    id="add_parent_job">{{ trans('maintenance::maintenance.save') }}</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection



@section('script')
    <script src="{{ asset('resources/bootstrap-timepicker/js/moment.min.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>

    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>

    <script src="{{ asset('resources/iCheck/icheck.min.js') }}"></script>

    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('resources/select2/select2.full.min.js') }}"></script>

    <script src="{{ asset('js/maintenance.js') }}"></script>

    <script>
        $('.select2').select2();

        $(document).ready(function() {

            var OldValue = '{{ old('resident_reporter') }}';

            document.getElementById("previous_resident_value").value = OldValue;


            //console.log(OldValue);

            loadResidentReporters();


            $('.date_place').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "DD-MM-YYYY hh:mm",
            });

        });
        ///////////////////////////////////////////////////

        $("select[name=resident_reporter]").change(function() {

        var res_id = $('#resident_reporter').val();

    });

        ///////////////////////////////////////////////////////
        function showAttachFileModal() {

            $("#ajx_err_msg").html('');
            $("#err_msg_box").css('display', 'none');

            $("#ajx_suc_msg").html('');
            $("#suc_msg_box").css('display', 'none');

            $("#final_add_file").prop('onclick', null);

            $('#final_add_file').attr('onClick', 'submitAttachMaintenanceDocument()');


            $("#attachFileModal").modal('show');

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


        ////////////////////////////////////////////////////////////
        function submitAttachMaintenanceDocument() {
            console.log('fgui');

            let attached_files = $('#file').val();

            console.log(attached_files);

        }
        ///////////////////////////////////////////////////////////

        function loadResidentReporters() {

            console.log('are')

            var locations = $('#locations').val();

            console.log(locations);
            send('/maintenance/mgt/resident_reporter', {
                'locations': locations,
            }, 'handleLoadResidentReporter', []);
        }
        /////////////////////////////////////////////////////////////
        function handleLoadResidentReporter() {

            console.log('tt');

            let res = return_value.code;
            let message = return_value.message;
            let residents = return_value.residents;

            console.log(return_value.residents);

            if (res == "failure") {

            } else if (residents == undefined || residents == null || residents == "undefined") {
                console.log('ppppp');

                let htmlValue = '<option value="">Select Resident Reporter</option>';
                $('#resident_reporter').html(htmlValue);

            } else if (residents != null && residents != "undefined") {

                // $('#template_sub_category').prop('disabled', false);


                let htmlValue = '<option value="">Select Resident Reporter</option>';
                Object.keys(residents).forEach(function(k) {

                    var counter = 1 + parseInt(k);


                    var resident_id = residents[k]["id_resident"];
                    var resident_name = residents[k]["resident_title"] + " " + residents[k]["resident_first_name"] +
                        " " + residents[k]["resident_surname"];

                        var previous_res = $('#previous_resident_value').val();

                        console.log('are');
                        console.log(previous_res);



                        if ( previous_res == resident_id ){

                            htmlValue = htmlValue + "<option value='" + resident_id + "' selected >" + resident_name + "</option>";

                        }else{

                            htmlValue = htmlValue + "<option value='" + resident_id + "' >" + resident_name + "</option>";

                        }

                });
                $('#resident_reporter').html(htmlValue);

                // if(jQuery("#resident_reporter").data('oldid')!='' && typeof(jQuery("#resident_reporter").data('oldid'))!="undefined"){
                //     jQuery('#resident_reporter').val(jQuery("#resident_reporter").data('oldid')); //select the old doctor id here
                // }

            }
        }

        //////////////////////////////////////////////////////


        $.ajaxSetup({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            }

        });



        $('#attach_file_form').submit(function(e) {



            e.preventDefault();

            console.log('are');
            let formData = new FormData(this);
            console.log('na');



            $.ajax({

                method: 'POST',

                url: "/maintenance/upload/file",

                data: formData,

                contentType: false,

                processData: false,

                success: function(response) {

                    console.log()
                    let description = response.description;
                    let text1 = "+";
                    let result = text1.concat(description);
                    // console.log(result);

                    let doc_name = response.fileName;
                    let doc = doc_name + "/";
                    let seperated_doc = text1.concat(doc);
                    let doc_result = description.concat(seperated_doc);

                    console.log(doc_result);

                    document.getElementById('attach_file_btn').style.display = 'none';
                    $('#attach_file_btn').attr('type', 'hidden');

                    let p = document.getElementById('selected_file');
                    let nnn = document.getElementById('nahayat');


                    let previous_name = p.getAttribute('name');

                    let previous_doc = nnn.value;
                    console.log(previous_doc);


                    // console.log(previous_name);

                    let update_name = result.concat(previous_name);


                    htmlVal = response.fileName + ' ';
                    $('#selected_file').append(htmlVal);
                    $('#selected_file').attr('name', update_name);
                    let bb = +response.fileName + '/' + description + ",";

                    // console.log(bb);
                    let update_doc = doc_result.concat(previous_doc);

                    $('#nahayat').val(update_doc);

                    // let aa = "<option>"+response.fileName+"/"+description+"<option>";

                    let aa = "<option value=" + bb + " selected>" + response.fileName + "/" +
                        description + "<option>";


                    let cc = "<input value=" + bb + " >" + response.fileName + "/" + description +
                        "<option>";

                    $('#all_attached_file').append(aa);

                    // htmlVal = "<p >"+ response.fileName +"</p>";
                    // $('#contaner').append( htmlVal);

                    $("#attachFileModal").modal('hide');


                    // $('#contaner').append(
                    //     $('<input/>').attr('type', 'file').attr('value', response.uploaded_file));

                    // let a = document.getElementById("file");
                    // e.preventDefault();
                    //    $('#show_attached_file').val(response.uploaded_file);
                    // document.getElementById("file").type="file";
                    // $('#show_attached_file').attr('value',response.uploaded_file);
                    // const dataTransfer = new DataTransfer();
                    // dataTransfer.items.add(response.uploaded_file,'response.fileName');
                    // a.files = dataTransfer.files;


                    // document.getElementById("show_attached_file").value = response.file;
                },

                error: function(response) {

                    alert(response.responseJSON.message);


                }

            });

        });

        /////////////////////////////////////////////////////////////
        $('#add_parent_job').on('click', function(e) {

            console.log('are');

            let id_maintenance_job = $('#parent_job_modal').val();

            send('/maintenance/find/maintenance_title', {
                    id_maintenance_job: id_maintenance_job
                },
                'handleShowMaintenanceJobTitle', []);


        })
        //////////////////////////////////////////////////////
        function handleShowMaintenanceJobTitle() {

            let message = return_value.message;
            let res = return_value.maintenace_job_title;
            $('#parent_job').val(res);
            $('#parent_job').value = res;

            $("#addParentJobModal").modal('hide');


        }
        ///////////////////////////////////////////////////////////////////
        function showAttachFileModal() {

            $("#ajx_err_msg").html('');
            $("#err_msg_box").css('display', 'none');

            $("#ajx_suc_msg").html('');
            $("#suc_msg_box").css('display', 'none');



            $("#attachFileModal").modal('show');

        }
        ///////////////////////////////////////////////////////

        function showAddParentModal() {

            $("#ajx_err_msg").html('');
            $("#err_msg_box").css('display', 'none');

            $("#ajx_suc_msg").html('');
            $("#suc_msg_box").css('display', 'none');

            $('#parent_job_modal').val(0);


            $("#addParentJobModal").modal('show');

        }


        /////////////////////////////////////////////////////////
    </script>

@endsection
