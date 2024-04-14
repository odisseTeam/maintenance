@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title') . ' ' . __('maintenance::maintenance.maintenance_detaill'))


@section('body_class', 'login-page')

@section('css')


    <link rel="stylesheet" type="text/css" href="{{ asset('resources/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}"/>

    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/iCheck/all.css') }}" />
    <link rel="stylesheet" href="{{ asset('resources/select2/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('resources/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />


    <style>
        .select2-selection--multiple {
            border: 0px;
        }
        .select2-container{
            min-width: 345px!important;
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

                                    <input type="hidden" id="id_maintenance" name="id_maintenance" value="{{ $maintenance->id_maintenance_job }}">


                                    <div class="row">
                                        <div class="col-md-6">


                                            <!-- maintenance title-->
                                            <div class="form-group row ">
                                                <label class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.maintenance_title') }}:</label>

                                                <div class="col-xs-9 col-sm-9 col-md-9">
                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                        <input class="form-control" name="maintenance_title" id="maintenance_title" value="{{ $maintenance->maintenance_job_title }}" />

                                                    </div>


                                                </div>



                                            </div>


                                            <!-- comment-->
                                            <div class="form-group row">
                                                <label class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.add_comment') }}:</label>
                                                <div class="col-xs-9 col-sm-9 col-md-9">
                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                        <textarea class="form-control" rows="4" name="coment" id="coment" column="40">{{ old('coment') }}</textarea>


                                                    </div>

                                                </div>
                                            </div>




                                            <!-- saas client budiness-->
                                            <div class="form-group row " style="margin-top:15px;">
                                                <label class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.saas_client_business') }}:</label>

                                                <div class="col-md-9 col-sm-9 col-lg-9 col-xs-9">
                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                    <select name="saas_client_business" id="saas_client_business" class="form-control select" disabled="disabled">
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
                                            </div>



                                            <!-- category-->
                                            <div class="form-group row">
                                                <label class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.category') }}:</label>
                                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                    <select name="maintenance_category" id="maintenance_category" class="form-control select ">
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
                                                        @endif
                                                    </select>
                                                </div>

                                                </div>
                                            </div>



                                            <!-- locations-->
                                            <div class="form-group row">
                                                <label class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.locations') }}:</label>
                                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                        <select name="locations[]" id="locations" class="form-control select2"
                                                            placeholder="{{ __('maintenance::maintenance.select_locations') }}" multiple="multiple" onchange="loadResidentReporters()">
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
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- priority-->
                                            <div class="form-group row">
                                                <label class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.priority') }}:</label>
                                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
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
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>




                                        <!-- commencement_date -->
                                        <div class="form-group row">
                                            <label class="col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right">{{ trans('maintenance::maintenance.commencement_date') }}:</label>
                                            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">

                                               <div class="input-group col-xs-10 col-sm-10 col-md-10">
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
                                            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">

                                               <div class="input-group col-xs-10 col-sm-10 col-md-10">
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




                                                <!-- staff reporter-->

                                                <div class="form-group row">
                                                    <label
                                                        class="col-xs-3 col-sm-3 col-md-3 control-label text-right">{{ trans('maintenance::maintenance.staff_reporter') }}:</label>
                                                    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">

                                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">


                                                        <select name="staff_reporter" id="staff_reporter" class="form-control select select2 ">
                                                            @if (isset($staffs))
                                                            @foreach ($staffs as $staff)
                                                                <option
                                                                    value="{{ $staff->id }}"
                                                                    @if ( (isset($maintenance) && ($staff->id == $maintenance->id_saas_staff_reporter ))||
                                                                            old('staff_reporter') == $staff->id) {{ 'selected' }} @endif>
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
                                                </div>





                                        </div>
                                        <div class="col-md-6">

                                            <!-- maintenance status-->
                                            <div class="form-group row ">
                                                <label class="col-xs-5 col-sm-5 col-md-5 control-label text-right">{{ trans('maintenance::maintenance.status') }}:</label>
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






                                        <div class="col-xs-0 col-md-1 col-sm-1 col-lg-1">

                                        </div>
                                    <div class="col-xs-12 col-md-3 col-sm-3 col-lg-3">

                                    </div>





                                    <div class="col-xs-0 col-md-1 col-sm-1 col-lg-1">
                                    </div>




                                </div>
                                <div class="box-footer">
                                    <div class="" style="text-align: right;">

                                        <a href="/maintenance/dashboard"><button type="button" class="btn btn-warning"  style="min-width: 60px;">{{ __('general.close') }}</button></a>
                                        <a href="/maintenance/create/email_temp/{{ $maintenance->id_maintenance_job }}" target="_blank"><button type="button" class="btn btn-primary"  style="min-width: 60px;">{{ __('maintenance::maintenance.email_to_contractor') }}</button></a>
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

                                                                        <textarea class="form-control" rows="4" name="file_description" id="file_description" >{{ old('file_description') }}</textarea>
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

                <div class="{{ (null == session('active_tab') or session('active_tab') == 'maintenance_documents') ? 'active' : '' }} tab-pane" id="maintenance_documents">
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
    {{-- <script src="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.js') }}"></script> --}}
    <script src="{{ asset('resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

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

            // //Date picker
            // $('#maintenance_date').daterangepicker({
            //     singleDatePicker: true,
            //     timePicker: true,
            //     timePickerSeconds: true,
            //     //timePicker12Hour: false,
            //     showDropdowns: true,
            //     // minYear: 2000,
            //     format: window._date_time_format,

            //     timePickerIncrement: 5,
            //     locale: {
            //         format: window._date_time_format,
            //         separator: " - ",
            //         applyLabel: "Apply",
            //         cancelLabel: "Cancel",
            //         fromLabel: "From",
            //         toLabel: "To",
            //         customRangeLabel: "Custom",
            //         weekLabel: "W",
            //         daysOfWeek: [
            //             "Su",
            //             "Mo",
            //             "Tu",
            //             "We",
            //             "Th",
            //             "Fr",
            //             "Sa"
            //         ],
            //         monthNames: [
            //             "January",
            //             "February",
            //             "March",
            //             "April",
            //             "May",
            //             "June",
            //             "July",
            //             "August",
            //             "September",
            //             "October",
            //             "November",
            //             "December"
            //         ],
            //         firstDay: 1

            //     }
            // });


            $('#commencement_date , #complete_date').datepicker({
                autoclose: true,
                weekStart: 1,
                todayBtn: "linked",
                todayHighlight: true,
                orientation: "left",
                //format: 'dd/mm/yy',
                format: window._date_format,
            });


            prepareMaintenanceDocumentsTable();
            prepareMaintenanceTimelineTable();

            console.log(window._date_format);



        });
        ////////////////////////////////////////////////
        function search_contractors() {

            var spinHandle = loadingOverlay.activate();


            let maintenance_id = $('#id_maintenance').val();
            let contractor_skill = $('#contractor_skill').val();


            send('/maintenance/contractor_skill/contractors', {
                maintenance_id: maintenance_id,
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

    </script>

@endsection
