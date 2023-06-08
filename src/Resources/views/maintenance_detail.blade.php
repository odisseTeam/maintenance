@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title') . ' ' . __('maintenance::maintenance.maintenance_detaill'))


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

    <!-- Content Header (Page header) -->
    <section class="content-header">

    </section>

    <!-- Main content -->
    <section class="content">


        <div class="nav-tabs-custom">
            @if (isset($maintenance))

                <ul class="nav nav-tabs">

                    <li
                        class="{{ (null == session('active_tab') or session('active_tab') == 'maintenanceDetail') ? 'active' : '' }}">
                        <a id="maintenanceDetail_tab" href="#maintenanceDetail"
                            data-toggle="tab">{{ __('maintenance::maintenance.maintenance_detail') }}</a>
                    </li>
                    <li class="{{ session('active_tab') == 'maintenance_timeline' ? 'active' : '' }}">
                        <a id="maintenance_timeline_tab" href="#maintenance_timeline"
                            data-toggle="tab">{{ __('maintenance::maintenance.maintenance_timeline') }}</a>
                    </li>
                    <li class="{{ session('active_tab') == 'maintenance_documents' ? 'active' : '' }}">
                        <a id="maintenance_documents_tab" href="#maintenance_documents"
                            data-toggle="tab">{{ __('maintenance::maintenance.maintenance_documents') }}</a>
                    </li>
                </ul>
            @endif

            <div class="tab-content row">
                <div class="{{ (null == session('active_tab') or session('active_tab') == 'maintenanceDetail') ? 'active' : '' }} tab-pane"
                    id="maintenanceDetail">

                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box box-header">
                                <h3>
                                    {{ trans('maintenance::maintenance.maintenance_detail') }}
                                </h3>

                            </div>


                            <form action="/maintenance/detail/edit" method="post">
                                @csrf
                                <div class="box-body">

                                    <input type="hidden" id="id_maintenance" name="id_maintenance"
                                        value="{{ $maintenance->id_maintenance_job }}">
                                    <!-- maintenance title-->

                                    <div class="form-group row col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                               <label
                                                     class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.maintenance_title') }}:</label>

                                                    <div class="col-xs-9 col-sm-9 col-md-9">
                                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                            <input class="form-control" name="maintenance_title"
                                                                id="maintenance_title"
                                                                value="{{ $maintenance->maintenance_job_title }}" />


                                                        </div>


                                                    </div>



                                        </div>




                                        <div class="col-xs-0 col-md-1 col-sm-1 col-lg-1">

                                        </div>
                                    <!-- maintenance status-->
                                    <div class="col-xs-12 col-md-3 col-sm-3 col-lg-3">
                                        <div class="form-group row ">
                                            <label
                                                class="col-xs-3 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::maintenance.status') }}:</label>
                                            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                <select name="maintenance_status" id="maintenance_status"
                                                    class="form-control select ">
                                                    @foreach ($maintenance_status as $maintenance_sts)
                                                        <option
                                                            value="{{ $maintenance_sts->id_maintenance_job_status_ref }}"
                                                            @if (
                                                                $maintenance_sts->id_maintenance_job_status_ref == $maintenance->id_maintenance_job_status ||
                                                                    old('maintenance_status') == $maintenance->id_maintenance_job_status) {{ 'selected' }} @endif>
                                                            {{ $maintenance_sts->job_status_name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- comment-->


                                    <div class="form-group row col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                            <label
                                                class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.add_comment') }}:</label>
                                            <div class="col-xs-9 col-sm-9 col-md-9">
                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                    <textarea class="form-control" rows="4" name="coment" id="coment" column="40">{{ old('coment') }}</textarea>

                                                    
                                                </div>

                                            </div>
                                        </div>




                                    <div class="col-xs-0 col-md-1 col-sm-1 col-lg-1">

                                    </div>

                                    <!-- people-->
                                    <div class="col-xs-12 col-md-3 col-sm-3 col-lg-3">
                                        <!-- assignee-->

                                        <div class="form-group row ">
                                            <h4><strong>{{ trans('maintenance::maintenance.people') }}</strong></h4>

                                            <label
                                                class="col-xs-3 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::maintenance.assignee') }}:</label>
                                            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                <select name="maintenance_assignee" id="maintenance_assignee"
                                                    class="form-control select ">
                                                    <option value="">
                                                        {{ trans('maintenance::maintenance.select_assignee') }}</option>
                                                    @foreach ($contactors as $contractor)
                                                        <option value="{{ $contractor['id_contractor'] }}"
                                                            @if ($contractor['id_contractor'] == $maintenance_job_detail->id_staff) {{ 'selected' }} @endif>



                                                            {{ $contractor['name'] }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <!-- reporter-->

                                        <div class="form-group row ">

                                            <label
                                                class="col-xs-3 col-sm-4 col-md-4 control-label text-right">{{ trans('maintenance::maintenance.reporter') }}:</label>
                                            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                <select name="maintenance_reporter" id="maintenance_reporter"
                                                    class="form-control select ">
                                                    @foreach ($reporters as $reporter)
                                                        <option value="{{ $reporter->id }}"
                                                            @if (
                                                                $reporter->id == $maintenance->id_saas_staff_reporter ||
                                                                    old('maintenance_reporter') == $maintenance->id_saas_staff_reporter) {{ 'selected' }} @endif>
                                                            {{ $reporter->first_name }} {{ $reporter->last_name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

                                    </div>



                                    <!-- saas client budiness-->
                                    <div class="form-group row col-xs-12 col-sm-12 col-md-12 col-lg-12"
                                        style="margin-top:15px;">
                                        <label
                                            class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.saas_client_business') }}:</label>

                                        <div class="col-md-5 col-sm-5 col-lg-5 col-xs-10">
                                            <select name="saas_client_business" id="saas_client_business"
                                                class="form-control select" disabled="disabled">
                                                @if (isset($saas_client_businesses))
                                                    @foreach ($saas_client_businesses as $saas_client_business)
                                                        <option
                                                            value="{{ $saas_client_business->id_saas_client_business }}"
                                                            @if (
                                                                $saas_client_business->id_saas_client_business == $maintenance->id_saas_client_business ||
                                                                    old('saas_client_business') == $maintenance->id_saas_client_business) {{ 'selected' }} @endif>
                                                            {{ $saas_client_business->business_name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                @endif


                                            </select>

                                        </div>
                                    </div>


                                    <!-- category-->
                                    <div class="form-group row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <label
                                            class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.category') }}:</label>
                                        <div class="col-xs-10 col-sm-5 col-md-5 col-lg-5">


                                            <select name="maintenance_category" id="maintenance_category"
                                                class="form-control select ">
                                                @if (isset($maintenance_categories))
                                                    @foreach ($maintenance_categories as $maintenance_category)
                                                        <option
                                                            value="{{ $maintenance_category->id_maintenance_job_category_ref }}"
                                                            @if (
                                                                $maintenance_category->id_maintenance_job_category_ref == $maintenance->id_maintenance_job_category ||
                                                                    old('maintenance_category') == $maintenance->id_maintenance_job_category) {{ 'selected' }} @endif>
                                                            {{ $maintenance_category->job_category_name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                @endif


                                            </select>


                                        </div>
                                    </div>


                                    <!-- locations-->
                                    <div class="form-group row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <label
                                            class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.locations') }}:</label>
                                        <div class="col-xs-10 col-sm-5 col-md-5 col-lg-5">


                                            <select name="locations[]" id="locations" class="form-control select2"
                                                placeholder="{{ __('maintenance.select_locations') }}"
                                                multiple="multiple" onchange="loadResidentReporters()">
                                                <!-- <option value="">{{ __('maintenance.select_locations') }}</option> -->
                                                @if (isset($locations))
                                                    @foreach ($locations as $location)
                                                        <option value="{{ $location->id }}"
                                                            @if (isset($maintainables)) @foreach ($maintainables as $maintainable)
                                                                                    @if ($location->id == $maintainable->id_location || old('locations') == $maintainable->id_location) {{ 'selected' }} @endif
                                                            @endforeach
                                                    @endif
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

                                    <div class="form-group row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <label
                                            class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.priority') }}:</label>
                                        <div class="col-xs-10 col-sm-5 col-md-5 col-lg-5">


                                            <select name="priority" id="priority" class="form-control select ">
                                                @if (isset($priorities))
                                                    @foreach ($priorities as $priority)
                                                        <option value="{{ $priority->id_maintenance_job_priority_ref }}"
                                                            @if (
                                                                $priority->id_maintenance_job_priority_ref == $maintenance->id_maintenance_job_priority ||
                                                                    old('priority') == $maintenance->id_maintenance_job_priority) {{ 'selected' }} @endif>
                                                            {{ $priority->priority_name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                @endif


                                            </select>


                                        </div>
                                    </div>





                                </div>
                                <div class="box-footer">
                                    <div class="" style="text-align: right;">

                                        <a href="/maintenance/dashboard"><button type="button" class="btn btn-warning"  style="min-width: 60px;">{{ __('general.close') }}</button></a>
                                        <button type="submit" class="btn btn-primary" style="margin-left:1px;min-width: 60px;">{{ __('general.save') }}</button>
                                    </div>
                                </div>

                            </form>

                            <form action="/maintenance/attachment/upload" method="post" enctype="multipart/form-data">
                              @csrf

                               <div class="box-body">

                                    <input type="hidden" id="id_maintenance" name="id_maintenance"
                                        value="{{ $maintenance->id_maintenance_job }}">


                                                <div class="box box-header">
                                                    <h4 style="font-weight: bold;" >{{ trans('maintenance::maintenance.add_file') }}</h4>
                                                </div>

                                            <div class="form-group row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <label   style="margin-top:10px;"
                                                    class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.attach_new_file') }}:</label>
                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                    <!-- <div class=" input-group" id="contaner"> -->


                                                    <div class="col-sm-5 col-md-5 col-lg-5">
                                                        <!-- <label class="control-label col-sm-3 col-md-3 col-lg-3 col-md-offset-1">{{ trans('maintenance::maintenance.file') }}:</label> -->
                                                        <div>
                                                            <input type="file" type="file" multiple name="attachments[]"
                                                                style="margin-top:10px;">

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <label
                                                    class="col-xs-4 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.file_description') }}:</label>
                                                        <div class="col-xs-8 col-sm-6 col-md-6 ">

                                                                    <div class="input-group col-xs-12 col-sm-10 col-md-10 col-lg-10">

                                                                        <textarea class="form-control" rows="4" name="file_description" id="file_description" >
                                                                        {{ old('file_description') }}
                                                                        </textarea>
                                                                    </div>

                                                        </div>
                                            </div>
                                                            </div>

                                            <div class="box-footer">
                                                <div class="" style="text-align: right;">
                                                    <button type="submit" class="btn btn-primary">{{ trans('maintenance::maintenance.upload') }}</button>
                                                </div>
                                            </div>



                                <!-- </div> -->
                            </form>


                        </div>
                    </div>
                </div>

                <div class="{{ (null == session('active_tab') or session('active_tab') == 'maintenance_timeline') ? 'active' : '' }} tab-pane"
                    id="maintenance_timeline">
                    <div class="box box-primary">
                        <div class="box box-header">
                            <h3>
                                {{ trans('maintenance::maintenance.maintenance_timeline') }}
                            </h3>

                        </div>

                        <div class="box-body">

                            <div id="timeline_box_body">

                            </div>




                        </div>
                    </div>
                </div>

                <div class="{{ (null == session('active_tab') or session('active_tab') == 'maintenance_documents') ? 'active' : '' }} tab-pane"
                    id="maintenance_documents">
                    <div class="box box-primary">
                        <div class="box box-header">
                            <h3>
                                {{ trans('maintenance::maintenance.maintenance_documents') }}
                            </h3>

                        </div>

                        <div class="box-body">


                            <table id="maintenance_documents_table"
                                class="table table-bordered table-hover dataTable text-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('maintenance::maintenance.document_name') }}</th>
                                        <th>{{ __('maintenance::maintenance.document_extention') }}</th>
                                        <th>{{ __('maintenance::maintenance.description') }}</th>
                                        <th>{{ __('maintenance::maintenance.operation') }}</th>
                                    </tr>
                                </thead>


                                <tbody id="maintenance_documents_body_tbl">


                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('maintenance::maintenance.document_name') }}</th>
                                        <th>{{ __('maintenance::maintenance.document_extention') }}</th>
                                        <th>{{ __('maintenance::maintenance.description') }}</th>
                                        <th>{{ __('maintenance::maintenance.operation') }}</th>


                                    </tr>
                                </tfoot>
                            </table>



                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->


    <div class="modal fade" id="deleteMaintenanceDocumentModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                            class="sr-only">{{ __('general.close') }}</span></button>

                    <h4 class="modal-title" id="deleteMaintenanceDocumentModallabel">
                        {{ __('maintenance::maintenance.delete_maintenance_document_modal') }}</h4>

                </div>
                <div class="form-horizontal" id="note_mgt_form" novalidate="novalidate">

                    <div class="alert alert-danger alert-dismissible" id="delete_maintenance_document_err_msg_box"
                        style="display: none;">
                        <div id="delete_maintenance_document_ajx_err_msg"></div>
                    </div>
                    <div class="alert alert-success alert-dismissible" id="delete_maintenance_document_success_msg_box"
                        style="display: none;">
                        <div id="delete_maintenance_document_ajx_success_msg"></div>
                    </div>
                    <div class="modal-body">

                        <h4>{{ __('maintenance::maintenance.are_you_sure_to_delete_maintenance_document') }}</h4>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="close_btn" class="btn btn-warning"
                            data-dismiss="modal">{{ __('general.close') }}</button>
                        <button type="button" class="btn btn-danger"
                            id="btn_operate_delete">{{ __('general.yes') }}</button>

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

            //Date picker
            $('#maintenance_date').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePickerSeconds: true,
                //timePicker12Hour: false,
                showDropdowns: true,
                // minYear: 2000,
                format: window._date_time_format,

                timePickerIncrement: 5,
                locale: {
                    format: window._date_time_format,
                    separator: " - ",
                    applyLabel: "Apply",
                    cancelLabel: "Cancel",
                    fromLabel: "From",
                    toLabel: "To",
                    customRangeLabel: "Custom",
                    weekLabel: "W",
                    daysOfWeek: [
                        "Su",
                        "Mo",
                        "Tu",
                        "We",
                        "Th",
                        "Fr",
                        "Sa"
                    ],
                    monthNames: [
                        "January",
                        "February",
                        "March",
                        "April",
                        "May",
                        "June",
                        "July",
                        "August",
                        "September",
                        "October",
                        "November",
                        "December"
                    ],
                    firstDay: 1

                }
            });

            prepareMaintenanceDocumentsTable();
            prepareMaintenanceTimelineTable();



        });
        ////////////////////////////////////////////////
        function prepareMaintenanceTimelineTable() {

            let maintenance_id = $('#id_maintenance').val();


            send('/maintenance/timeline/get', {
                maintenance_id: maintenance_id,

            }, 'handleMaintenanceTimelineBody', []);

        }
        ////////////////////////////////////////////////

        function handleMaintenanceTimelineBody() {

            let code = return_value.code;
            let message = return_value.message;
            let maintenance_timeline = return_value.report;

            console.log(maintenance_timeline);
            if (code == "failure") {

            } else if (maintenance_timeline != null && maintenance_timeline != "undefined") {

                let htmlValue = "";
                Object.keys(maintenance_timeline).forEach(function(k) {

                    var log_date_time = maintenance_timeline[k]["log_date_time"];
                    var header = maintenance_timeline[k]["header"];
                    var log_note = maintenance_timeline[k]["log_note"];

                    htmlValue += '<ul class="timeline"><li class="time-label"><span class="">' + log_date_time +
                        '</span></li><li><i class="fa fa-user"></i><div class="timeline-item"><h3 class="timeline-header">' +
                        header +
                        '</h3><div class="timeline-body">' + log_note +
                        '</div><div class="timeline-footer"></div></div></li></ul>';

                });

                $('#timeline_box_body').html('');
                $('#timeline_box_body').append(htmlValue);

            }

        }
        /////////////////////////////////////////////////////
        function prepareMaintenanceDocumentsTable() {

            let maintenance_id = $('#id_maintenance').val();


            send('/maintenance/documents/get', {
                maintenance_id: maintenance_id,

            }, 'handleMaintenanceDocumentBody', []);

        }
        ///////////////////////////////////////////////////
        function handleMaintenanceDocumentBody() {

            // document.getElementById("charge_mgt_report_text").innerHTML="";

            let code = return_value.code;
            let message = return_value.message;
            let maintenance_documents_list = return_value.report;

            console.log(maintenance_documents_list);
            if (code == "failure") {

                // document.getElementById("charge_mgt_report_text").innerHTML=message;
                $('#maintenance_documents_table').DataTable().clear().destroy();


            } else if (maintenance_documents_list != null && maintenance_documents_list != "undefined") {


                let htmlValue = "";
                Object.keys(maintenance_documents_list).forEach(function(k) {

                    counter = 1 + parseInt(k);

                    var id_maintenance_job_document = maintenance_documents_list[k]["id_maintenance_job_document"];
                    var document_name = maintenance_documents_list[k]["document_name"];
                    var document_extention = maintenance_documents_list[k]["document_extention"];
                    var description = maintenance_documents_list[k]["description"];


                    var operation =
                        '<a data-toggle="tooltip" title="Delete Maintenance Document" class="btn btn-danger allign-btn"  data-original-title="Delete Maintenance Document" onclick="deleteMaintenanceDocument(' +
                        id_maintenance_job_document + ')" >' +
                        '<i class="fa-solid fa-trash" ></i> </a>';
                    operation += '<a href="/maintenance/attachment/' + id_maintenance_job_document +
                        '/download" style="margin-left:10px" class="btn btn-primary allign-btn" target="blank" ><i class="fa-solid fa-download"></i></a>';
                        // '/download" style="margin-left:10px" class="btn btn-primary allign-btn" target="blank" ><i class="fa-solid fa-download"></i></a>';
                       
                     operation += '<a href="/maintenance/files/' + document_name +'" style="margin-left:10px" class="btn btn-primary allign-btn" target="blank" title="Show Document" ><i class="fa-solid fa-eye "></i></a>';



                    htmlValue += "<tr><td>" + counter + "</td><td>" + document_name + "</td><td>" +
                        document_extention +
                        "</td><td>" + description + "</td><td>" + operation + "</td></tr>";
                });


                $('#maintenance_documents_table').DataTable().clear().destroy();
                $('#maintenance_documents_table #maintenance_documents_body_tbl').html('');
                $('#maintenance_documents_table #maintenance_documents_body_tbl').append(htmlValue);


                var table = $('#maintenance_documents_table').DataTable({
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': true,
                    'info': true,
                    'autoWidth': true,
                    "aoColumnDefs": [

                        {
                            "sClass": "leftSide",
                            "aTargets": [0, 1, 2, 3, 4]
                        }, {
                            "width": "20%",
                            "targets": 4
                        }
                    ]
                });


            }


        }
        //////////////////////////////////////////////////////
        function deleteMaintenanceDocument(id_maintenance_job_document) {

            $("#delete_maintenance_document_ajx_err_msg").html('');
            $("#delete_maintenance_document_err_msg_box").css('display', 'none');

            $("#delete_maintenance_document_ajx_success_msg").html('');
            $("#delete_maintenance_document_success_msg_box").css('display', 'none');
            //action
            $("#btn_operate_delete").prop('onclick', null);
            $('#btn_operate_delete').attr('onClick', 'submitDeleteMaintenanceDocument(' + id_maintenance_job_document +
                ');');
            //showModal
            console.log('are');
            $('#deleteMaintenanceDocumentModal').modal('show');
        }
        ///////////////////////////////////////////////////
        function submitDeleteMaintenanceDocument(id_maintenance_job_document) {

            let maintenance_id = $('#id_maintenance').val();

            send('/maintenance/maintenance_document/delete', {
                id_maintenance_job_document: id_maintenance_job_document,
                maintenance_id: maintenance_id,
            }, 'handleDisableMaintenanceDocument', []);
        }

        ////////////////////////////////////////////////
        function handleDisableMaintenanceDocument() {

            let message = return_value.message;
            let res = return_value.code;

            console.log(message);
            if (res == "failure") {

                $("#delete_maintenance_document_ajx_err_msg").html(message);
                $("#delete_maintenance_document_err_msg_box").css('display', 'block');
            } else {
                $("#delete_maintenance_document_ajx_success_msg").html(message);
                $("#delete_maintenance_document_success_msg_box").css('display', 'block');

                setTimeout(function() {
                    $('#deleteMaintenanceDocumentModal').modal('hide')
                }, 4000);

                prepareMaintenanceDocumentsTable();
                prepareMaintenanceTimelineTable();

            }


        }

        ///////////////////////////////////////////////////////
        function showAttachFileModal() {

            $("#ajx_err_msg").html('');
            $("#err_msg_box").css('display', 'none');

            $("#ajx_suc_msg").html('');
            $("#suc_msg_box").css('display', 'none');



            $("#attachFileModal").modal('show');

        }
        ///////////////////////////////////////////////////////////

        function loadResidentReporters() {

            console.log('are')

            var locations = $('#locations').val();

            console.log(locations);
            send('/maintenance/get/resident_reporter', {
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
            console.log(return_value.code);

            if (res == "failure") {

            } else if (residents != null && residents != "undefined") {



                let htmlValue = '<option value="">Select Resident Reporter</option>';
                Object.keys(residents).forEach(function(k) {

                    var counter = 1 + parseInt(k);


                    var resident_id = residents[k]["id_resident"];
                    var resident_name = residents[k]["resident_title"] + " " + residents[k]["resident_first_name"] +
                        " " + residents[k]["resident_surname"];

                    htmlValue = htmlValue + "<option value='" + resident_id + "'>" + resident_name + "</option>";


                });
                $('#resident_reporter').html(htmlValue);

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
