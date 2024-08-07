@extends('robust.layouts.main')

@section('page_title', session('saas_title') . ' ' . __('maintenance::maintenance.maintenance'))


@section('body_class', 'login-page')

@section('css')


    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.css') }}" /> --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('resources/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}"/>

    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/iCheck/all.css') }}" />
    <link rel="stylesheet" href="{{ asset('resources/select2/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('resources/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />

    <style>
        .select2-selection--multiple {
            border: 0px;
        }

        .select2-container--default {
            border: 0px;
        }
    </style>

    <style>
        .input-group-addon {
            min-width: 35px;
        }

        .tab-pane{
            padding: 42px 0!important;
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

        <div class="row">
            <div class="col-xs-12">
                <div class="card box box-primary">
                    <div class="card-header box-header">

                        <form action="/maintenance/new/save" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body box-body">

                                <div class="form-group row">
                                    <div class="col-sm-12 col-xs-12 col-md-12">





                                        <!-- maintenance title-->

                                        <div class="form-group row">
                                               <label
                                                     class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.description') }}:</label>

                                                    <div class="col-xs-10 col-sm-10 col-md-10">
                                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">


                                                    <input class="form-control" name="description"
                                                        id="maintenance_title"
                                                        value="@if (old('description')) {{ old('description') }} @endif" />
                                                </div>



                                             </div>
                                        </div>


                                        <!-- description-->

                                        <div class="form-group row">
                                            <label
                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.comments') }}:</label>
                                            <div class="col-xs-10 col-sm-10 col-md-10">
                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                    <textarea class="form-control" rows="4" name="comment" id="description" column="40">{{ old('comment') }}</textarea>
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



                                        <div class="row">
                                            <div class="col-md-8">


                                                <!-- datetime -->
                                                <div class="form-group row">
                                                    <label
                                                        class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.date_time_reported') }}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">

                                                    <div class="">
                                                            <div class="form-group">
                                                                <div class="input-group date" id="datetimepicker1" >
                                                                <input type="text" class="form-control"
                                                                value="@if (isset($maintenance)) {{ $maintenance->maintenance_date_time }} @elseif (old('maintenance_date')) {{ old('maintenance_date') }} @else{{$now}}@endif"
                                                                placeholder="{{ trans('maintenance::maintenance.date_time') }}"
                                                                id="maintenance_date" name="maintenance_date"   >
                                                                <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>



                                                <!-- commencement_date -->
                                                <div class="form-group row">
                                                    <label
                                                        class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.commencement_date') }}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">

                                                    <div class="">
                                                            <div class="form-group">
                                                                <div class="input-group date" id="datepicker2" >
                                                                <input type="text" class="form-control"
                                                                value="@if (isset($maintenance)) {{ $maintenance->commencement_date }} @elseif (old('commencement_date')) {{ old('commencement_date') }} @endif"
                                                                placeholder="{{ trans('maintenance::maintenance.commencement_date') }}"
                                                                id="commencement_date" name="commencement_date"   >
                                                                <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>



                                                <!-- complete_date -->
                                                <div class="form-group row">
                                                    <label
                                                        class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.complete_date') }}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">

                                                    <div class="">
                                                            <div class="form-group">
                                                                <div class="input-group date" id="datepicker3" >
                                                                <input type="text" class="form-control"
                                                                value="@if (isset($maintenance)) {{ $maintenance->complete_date }} @elseif (old('complete_date')) {{ old('complete_date') }} @endif"
                                                                placeholder="{{ trans('maintenance::maintenance.complete_date') }}"
                                                                id="complete_date" name="complete_date"   >
                                                                <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>


                                                <!-- category-->
                                                <div class="form-group row">
                                                    <label
                                                        class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.category') }}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">


                                                        <select name="maintenance_category" id="maintenance_category"
                                                            class="form-control select ">
                                                            <option value="">
                                                                {{ trans('maintenance::maintenance.select_category') }}</option>
                                                            @if (isset($maintenance_categories))
                                                                @foreach ($maintenance_categories as $maintenance_category)
                                                                    <option
                                                                        value="{{ $maintenance_category->id_maintenance_job_category_ref }}"
                                                                        @if (
                                                                            $maintenance_category->id_maintenance_job_category_ref == $maintenance_category->id_credit_note_category ||
                                                                                old('maintenance_category') == $maintenance_category->id_maintenance_job_category_ref) {{ 'selected' }} @endif>
                                                                        {{ $maintenance_category->job_category_name }}
                                                                    </option>
                                                                @endforeach

                                                            @else
                                                            @endif


                                                        </select>


                                                    </div>
                                                </div>


                                                <!-- saas client budiness-->
                                                <div class="form-group row">
                                                    <label
                                                        class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.saas_client_business') }}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">


                                                        <select name="saas_client_business" id="saas_client_business" readonly
                                                            class="form-control select ">
                                                            @if (isset($saas_client_businesses))
                                                                @foreach ($saas_client_businesses as $saas_client_business)
                                                                    <option
                                                                        value="{{ $saas_client_business->id_saas_client_business }}"
                                                                        @if (old('saas_client_business') == $saas_client_business->id_saas_client_business) {{ 'selected' }}@elseif($current_business == $saas_client_business->id_saas_client_business){{'selected'}} @endif>
                                                                        {{ $saas_client_business->business_name }}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                            @endif


                                                        </select>


                                                    </div>
                                                </div>

                                                <!-- locations-->
                                                <div class="form-group row">



                                                    <label class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{trans('maintenance::maintenance.locations')}}:</label>
                                                    <div class="col-sm-7 col-md-7 col-lg-7 col-xs-7">


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
                                                        class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.priority') }}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">


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
                                                        class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.resident_reporter') }}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">


                                                        <select name="resident_reporter" id="resident_reporter"
                                                            class="form-control select ">

                                                        </select>


                                                    </div>
                                                </div>


                                                <input type="hidden" id="previous_resident_value" name="previous_resident_value" />



                                                <!-- staff reporter-->

                                                <div class="form-group row">
                                                    <label
                                                        class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.staff_reporter') }}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">


                                                        <select name="staff_reporter" id="staff_reporter" class="form-control select select2 ">
                                                            @if (isset($staffs))
                                                            @foreach ($staffs as $staff)
                                                                <option
                                                                    value="{{ $staff->id }}"
                                                                    @if ( (isset($maintenance) && ($staff->id == $maintenance->id_saas_staff_reporter ))||
                                                                            old('staff_reporter') == $staff->id) {{ 'selected' }} @elseif($staff->id == $logged_in_user){{'selected'}} @endif>
                                                                    @if($staff->first_name){{ $staff->first_name }} {{ $staff->last_name }}
                                                                    @else{{$staff->email}}
                                                                    @endif
                                                                </option>
                                                            @endforeach

                                                        @else
                                                        @endif
                                                        </select>


                                                    </div>
                                                </div>


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





                                            </div>
                                            <div class="col-md-4">

                                            <!-- people-->


                                            <div class="box">
                                                <div class="box-header">
                                                    <h4><strong>{{ trans('maintenance::maintenance.assignee') }}</strong></h4>

                                                </div>
                                                <div class="box-body">





                                                    <!-- contractor skill-->

                                                    <div class="form-group row ">

                                                        <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::maintenance.contractor_skill') }}:</label>
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







                                                    <!-- contractor-->

                                                    <div class="form-group row ">

                                                        <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::maintenance.business_contractor') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                            <select name="business_contractor" id="business_contractor" onchange="loadUserAgents()" class="form-control select ">
                                                                {{-- maintenance_assignee --}}
                                                                <option value="">{{ trans('maintenance::maintenance.select_business_contractor') }}</option>
                                                                @foreach ($businesses as $business)
                                                                    <option value="B{{ $business->id_saas_client_business}}"
                                                                        @if (isset($selected_business) && $business->id_saas_client_business == $selected_business->id_saas_client_business) {{ 'selected' }} @endif>
                                                                        {{ $business->business_name }}
                                                                    </option>
                                                                @endforeach
                                                                @foreach ($contactors as $contractor)
                                                                    <option value="C{{ $contractor['id_contractor'] }}"
                                                                        @if (isset($selected_contractor) && $contractor['id_contractor'] == $selected_contractor->id_contractor) {{ 'selected' }} @endif>
                                                                        {{ $contractor['name'] }}
                                                                    </option>
                                                                @endforeach

                                                            </select>
                                                        </div>

                                                    </div>



                                                    <!-- short name-->

                                                    <div class="form-group row ">

                                                        <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::contractor.short_name') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                            <input class="form-control" id="contractor_short_name" readonly value="@if(isset($selected_contractor)){{$selected_contractor->short_name}}@endif" >
                                                        </div>

                                                    </div>


                                                    <!-- tel number1-->

                                                    <div class="form-group row ">

                                                        <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::contractor.tel_number1') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                            <input class="form-control" id="contractor_tel_number1" readonly value="@if(isset($selected_contractor)){{$selected_contractor->tel_number1}}@endif" >
                                                        </div>

                                                    </div>



                                                    <!-- address line1-->

                                                    <div class="form-group row ">

                                                        <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::contractor.address') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                            <textarea class="form-control" rows="1" id="contractor_address_line1" readonly column="40">@if(isset($selected_contractor)){{$selected_contractor->address_line1}}@endif</textarea>
                                                        </div>

                                                    </div>


                                                    <!-- contractor_skills-->
                                                    <div class="form-group row ">

                                                        <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::contractor.skills') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" id="skill_place">

                                                        </div>

                                                    </div>



                                                    <!-- coverage_areas-->
                                                    <div class="form-group row ">

                                                        <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::contractor.coverage_area') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" id="coverage_area_place">

                                                        </div>

                                                    </div>



                                                    <!-- note-->

                                                    <div class="form-group row ">

                                                        <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::maintenance.contractor_note') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                            <textarea class="form-control" rows="4" id="contractor_note" readonly column="40">@if(isset($selected_contractor)){{$selected_contractor->note}}@endif</textarea>
                                                        </div>

                                                    </div>




                                                    <!-- assignee-->

                                                    <div class="form-group row ">

                                                        <label
                                                            class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::maintenance.user_agent') }}:</label>
                                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                            <select name="user_agent" id="user_agent" class="form-control select ">
                                                                <option value="">{{trans('maintenance::maintenance.select_user_agent')}}</option>
                                                                @if($users)
                                                                    @foreach ($users as $user)
                                                                        <option value="{{ $user->user_id}}"
                                                                            @if (isset($selected_user_agent) && $user->user_id == $selected_user_agent) {{ 'selected' }} @endif>
                                                                            @if(isset($user->first_name) || isset($user->last_name)){{ $user->first_name  }} {{$user->last_name}}
                                                                            @else{{$user->email}}@endif
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                                @if($agents)
                                                                    @foreach ($agents as $agent)
                                                                        <option value="{{ $agent->id}}"
                                                                            @if (isset($selected_user_agent) && $agent->id == $selected_user_agent) {{ 'selected' }} @endif>
                                                                            @if(isset($agent->first_name) || isset($agent->last_name)){{ $agent->first_name  }} {{$agent->last_name}}
                                                                            @else{{$agent->email}}@endif
                                                                        </option>
                                                                    @endforeach
                                                                @endif

                                                            </select>
                                                        </div>
                                                    </div>






                                                </div>
                                            </div>



                                            </div>
                                        </div>
                                        <div class="box-footer text-right">


                                            <a href="/maintenance/dashboard"><button type="button"
                                                class="btn btn-warning">{{ trans('maintenance::maintenance.cancel') }}</button></a>



                                            <button type="submit" id="save_maintenance"
                                                class="btn btn-primary">{{ trans('maintenance::maintenance.save') }}</button>



                                        </div>





                        </form>

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
    <!-- <script src="{{ asset('resources/bootstrap-timepicker/js/moment.min.js') }}"></script> -->
    <!-- <script src="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.js') }}"></script> -->

    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>

    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>

    <script src="{{ asset('resources/iCheck/icheck.min.js') }}"></script>

    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('resources/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('resources/moment/min/moment-with-locales.min.js') }}"></script>

    <script src="{{ asset('resources/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>

    <!-- <script src="{{ asset('js/maintenance.js') }}"></script> -->

    <script>
        $('.select2').select2();

        $(document).ready(function() {

            var OldValue = '{{ old('resident_reporter') }}';

            document.getElementById("previous_resident_value").value = OldValue;


            console.log(OldValue);



            loadResidentReporters();


             $('#datetimepicker1').datetimepicker({
                 format: window._date_time_format,
                 useCurrent: true
             });

             $('#datepicker2 , #datepicker3').datepicker({
                autoclose: true,
                weekStart: 1,
                todayBtn: "linked",
                todayHighlight: true,
                orientation: "left",
                //format: 'dd/mm/yy',
                format: window._date_format,
            });

             $('#maintenance_date').datetimepicker({
                 format: window._date_time_format,
                 useCurrent: true
             });



        });

        ///////////////////////////////////////////////////

        $("select[name=resident_reporter]").change(function() {

            var res_id = $('#resident_reporter').val();

        });
        ////////////////////////////////////////////////
        function search_contractors() {

            var spinHandle = loadingOverlay.activate();


            let maintenance_id = $('#id_maintenance').val();
            let contractor_skill = $('#contractor_skill').val();
            let maintainable = $('#locations').val();
            var place = 0;
            if(maintainable.length > 0){
                place = maintainable[0];
            }


            send('/maintenance/contractor_skill/contractors', {
                maintenance_id: maintenance_id,
                contractor_skill: contractor_skill,
                place: place,

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
                    contractor_list.forEach(item => {
                    var item_name = item['name'];
                    $('#business_contractor').append(new Option(item_name ,'C'+item['id_contractor']));
                    });
                }



                $("#contractor_note").html("");


            }

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
            let contractor = return_value.contractor;
            let contractor_skills = return_value.contractor_skills;
            let coverage_areas = return_value.coverage_areas;
            let user_type = return_value.user_type;

            if(res == "failure"){
                var textmessage = message;

                $("#ajx_err_msg_assign_maintenance").html(textmessage);
                $("#err_msg_box_assign_maintenance").css('display' , 'block');

                $("#contractor_note").html('');
                $("#contractor_address_line1").html('');
                $("#contractor_tel_number1").val('');
                $("#contractor_short_name").val('');

                $("#skill_place").html('');
                $("#coverage_area_place").html('');

                $('#user_agent').find('option').remove();
                $('#user_agent').append(new Option('Select User/Agent' ,''));

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
            // $('#assignMaintenanceModal').modal('show');


            loadingOverlay.cancelAll();

        }


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
