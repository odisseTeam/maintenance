{{-- @extends('adminlte.layouts.sdr') --}}
@extends('layouts.blank_js')


@section('page_title', session('saas_title').' '.__('maintenance::contractor.contractor_mgt'))


@section('body_class', 'login-page')

@section('css')

    <link rel="stylesheet" href="{{ asset('resources/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.css') }}"/>
    <!-- <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/bootstrap-timepicker/css/timepicker.less') }}"/> -->


    <link rel="stylesheet" type="text/css"
    href="{{ asset('css/bootstrap-multiselect.css') }}"/>

    <link rel="stylesheet" href="{{ asset('resources/iCheck/all.css')}}" />
    <link rel="stylesheet" href="{{ asset('resources/select2/select2.min.css') }}" />

    <style>
        .leftSide{
            text-align: left!important;
        }
    </style>

@endsection


@section('content')
    @if(session('error'))
        <div class="box-body">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa-solid fa-ban"></i>{{session('error')}}</p>
            </div>
        </div>
    @endif
    @if(session('success'))
        <div class="box-body">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa-solid fa-check"></i>{{session('success')}}</p>
            </div>
        </div>
    @endif

    @if(session('errors'))
        <div class="box-body">

            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                @foreach( $errors->all() as $error )
                    <p><i class="icon fa-solid fa-ban"></i>{{$error}}</p>
                @endforeach
            </div>
        </div>

    @endif
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{-- {{__('contractor.contractor_mgt')}} --}}
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
                            <h5>{{__('maintenance::contractor.contractor_management')}}</h5>
                            <span>{{__('maintenance::contractor.contractor_management')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="page-header-breadcrumb">
                        <ul class=" breadcrumb breadcrumb-title breadcrumb-padding">
                            <li class="breadcrumb-item">
                                <a href="index.html"><i class="feather icon-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="#!">{{__('maintenance::contractor.contractor_management')}}</a> </li>
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

                            <div class="box card">
                                    <div class="box-header card-header">
                                        <h1>
                                            {{__('maintenance::contractor.contractor_management')}}
                                        </h1>
                                    </div>
                                    <div class="box-body card-block">

                                        <div class="row">
                                            <div class="col-md-12">

                                                <div class="box box-primary card">
                                                    <div class="box-header card-header">
                                                            <h3>{{__('maintenance::dashboard.search_options')}}</h3>
                                                    </div>

                                                    <div class="box-body card-block">

                                                        <div class="row">

                                                            <div class="col-md-10 col-xs-12">


                                                                <div class="row">
                                                                    <!-- saas_client_business -->
                                                                    <div class="col-md-5">
                                                                        <div class="form-group">

                                                                            <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                                                <select class="form-control select2"  placeholder="{{__('maintenance::dashboard.contractor_name')}}" id="search_business" multiple="multiple" name="search_business[]" >
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

                                                                    <!-- skills -->
                                                                    <div class="col-md-5">
                                                                        <div class="form-group">

                                                                            <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                                                <select class="form-control select2" id="skills" name="skills[]" multiple="multiple"  >                                                                                <option value="">
                                                                                        <!-- {{__('maintenance::dashboard.skills')}} -->
                                                                                    </option>
                                                                                    @foreach($skills as $skill)
                                                                                        <option value="{{$skill->id_contractor_skill_ref}}">
                                                                                            {{$skill->skill_name}}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>

                                                                            </div>

                                                                        </div>
                                                                    </div>

                                                                </div>


                                                                <div style="margin-top: 2px;" class="row">

                                                                    <!-- locations -->
                                                                    <div class="col-md-5">
                                                                        <div class="form-group">

                                                                            <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                                                <select class="form-control select2" id="locations"  name="locations[]" multiple="multiple" >
                                                                                    <option value="">
                                                                                        <!-- {{__('maintenance::dashboard.locations')}} -->
                                                                                    </option>
                                                                                    @foreach($locations as $location)
                                                                                        <option value="{{$location->id_contractor_location_ref}}">
                                                                                            {{$location->location}}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>

                                                                            </div>

                                                                        </div>
                                                                    </div>

                                                                    <!-- contractor name -->
                                                                    <div class="col-md-5">
                                                                        <div class="form-group">

                                                                            <div class="col-xs-10 col-sm-10 col-md-10 ">
                                                                                <input name="contractor_name" placeholder="{{__('maintenance::dashboard.contractor_name')}}" type="text" class="form-control active" id="contractor_name" value="" onkeydown = "if (event.keyCode == 13)document.getElementById('searchbtn').click()">

                                                                            </div>

                                                                        </div>
                                                                    </div>

                                                                </div>

                                                            </div>


                                                            <div class="col-md-2 col-xs-12 col-sm-12 col-lg-2">
                                                                <div class="row">
                                                                    <button style="min-width:150px;margin-top:1px;float:right" id="searchbtn" onclick="searchAgain()" type="button" class="btn btn-primary">{{__('maintenance::maintenance_mgt.search')}}</button>
                                                                </div>
                                                                <div class="row">
                                                                    <button style="min-width:150px;margin-top:1px;float:right" type="button" onclick="resetSearchbox()" class="btn btn-primary">{{__('maintenance::maintenance_mgt.reset')}}</button>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="">
                                                        <!-- <ul class="nav nav-tabs">
                                                            <li class="active" id="contractor_mgt_tab"><a href="#contractor_mgt" data-toggle="tab">{{trans('maintenance::contractor.contractor_mgt')}}</a></li>

                                                        </ul> -->
                                                        <div class="">
                                                        <!-- start of tab report -->
                                                            <div class="active tab-pane" id="contractor_mgt">

                                                                <div class="row">
                                                                    <div class="col-xs-12">
                                                                        <div class="box box-primary">
                                                                            <div class="box-header">
                                                                                <h4 class="text-danger" id="room_type_text"> </h4>
                                                                            </div>
                                                                            <!-- /.box-header -->
                                                                            <div class="box-body table-responsive no-padding" style="text-align: left;">




                                                                                <div class="row"><a href="/maintenance/new_contractor"><button type="button"  style="float: right; margin: 10px 20px; min-width:135px;" id="create_contractor_btn" class="btn btn-primary" >{{trans('maintenance::contractor.create_new_contractor')}}</button></a></div>


                                                                                <div class="">

                                                                                <table id="contractor_list_table" class="table table-bordered table-hover dataTable text-center">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>#</th>
                                                                                        <th>{{trans('maintenance::contractor.name')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.short_name')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.vat_number')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.tel_number1')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.tel_number2')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.address_line1')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.operation')}}</th>
                                                                                    </tr>
                                                                                    </thead>


                                                                                    <tbody id="contractor_list_body_tbl">




                                                                                    </tbody>
                                                                                    <tfoot>
                                                                                    <tr>
                                                                                        <th>#</th>
                                                                                        <th>{{trans('maintenance::contractor.name')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.short_name')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.vat_number')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.tel_number1')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.tel_number2')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.address_line1')}}</th>
                                                                                        <th>{{trans('maintenance::contractor.operation')}}</th>
                                                                                    </tr>
                                                                                    </tfoot>
                                                                                </table>


                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>

                                                        <!-- /.tab-content -->
                                                    </div>

                                                </div>
                                            </div>
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
    <div class="modal fade" id="deleteContractorModal" tabindex="-1" role="dialog" aria-labelledby="deleteContractorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteContractorModalLabel">{{trans('maintenance::contractor.delete_contractor')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_delete_contractor" style="display: none">
                            <div id="ajx_err_msg_delete_contractor"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_delete_contractor" style="display: none">
                            <div id="ajx_suc_msg_delete_contractor"></div>
                        </div>

                        <p>{{trans('maintenance::contractor.do_you_want_to_delete_contractor')}}</p>
                        <input type="hidden" id="deleted_contractor">
                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-warning"
                        data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::contractor.cancel')}}</button>
                    <button type="button" class="btn btn-danger"
                        id="delete_contractor" onclick="deleteContractor()">{{trans('maintenance::contractor.delete')}}</button>
                </div>


            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="showLoginSettingsModal" tabindex="-1" role="dialog" aria-labelledby="showLoginSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="showLoginSettingsModalLabel">{{trans('maintenance::contractor.login_agent_settings')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_login_setting" style="display: none">
                            <div id="ajx_err_msg_login_setting"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_login_setting" style="display: none">
                            <div id="ajx_suc_msg_login_setting"></div>
                        </div>

                        <div class="box">
                            <div class="box-body">

                                <input type="hidden" id="changed_contractor" value="">



                                <!-- Email -->
                                <div class="form-group row">
                                    <label class="col-xs-3 col-sm-3 col-md-3  text-right">{{trans('maintenance::contractor.email')}}:</label>

                                    <div class="col-xs-9 col-sm-9 col-md-9 text-left">
                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                            <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.email_placeholder')}}" autocomplete="off" id="email" name="email"> {{-- value="@if (old('email')){{old('email')}}@elseif(null==old('_token') && isset($contractor)) {{$contractor->email}} @endif "> --}}
                                        </div>

                                    </div>
                                </div>




                                <!-- Password -->
                                <div class="form-group row">
                                    <label class="col-xs-3 col-sm-3 col-md-3  text-right">{{trans('maintenance::contractor.password')}}:</label>

                                    <div class="col-xs-9 col-sm-9 col-md-9 text-left">
                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                            <input type="password" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.password_placeholder')}}" autocomplete="off" id="password" name="password"> {{-- value="@if (old('password')){{old('password')}}@elseif(null==old('_token') && isset($contractor)) {{$contractor->password}} @endif "> --}}
                                        </div>

                                    </div>
                                </div>



                                <!-- Confirm Password -->
                                <div class="form-group row">
                                    <label class="col-xs-3 col-sm-3 col-md-3  text-right">{{trans('maintenance::contractor.confirm_password')}}:</label>

                                    <div class="col-xs-9 col-sm-9 col-md-9 text-left">
                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                            <input type="password" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.confirm_password_placeholder')}}" autocomplete="off" id="confirm_password" name="confirm_password"> {{-- value="@if (old('password')){{old('password')}}@elseif(null==old('_token') && isset($contractor)) {{$contractor->password}} @endif "> --}}
                                        </div>

                                    </div>
                                </div>


                            </div>
                        </div>

                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-warning"
                        data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::contractor.cancel')}}</button>
                    <button type="button" class="btn btn-primary"
                        id="save_contractor" onclick="changeContractorLoginSettings()">{{trans('maintenance::contractor.save')}}</button>
                </div>


            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="skillModal" tabindex="-1" role="dialog" aria-labelledby="skillModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="skillModalLabel">{{trans('maintenance::contractor.contractor_skills')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_skill" style="display: none">
                            <div id="ajx_err_msg_skill"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_skill" style="display: none">
                            <div id="ajx_suc_msg_skill"></div>
                        </div>

                        <div class="box">
                            <div class="box-body">
                                <input type="hidden" id="change_skill_contractor" value="">


                                <!-- skill-->
                                <div class="form-group row">
                                    <label
                                        class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::contractor.skill') }}:</label>
                                    <div class="col-sm-5 col-md-5 col-lg-5">

                                        <select name="skill[]" id="skill" multiple="multiple" class="form-control select ">
                                                @if (isset($skills))
                                                    @foreach ($skills as $skill)
                                                        <option value="{{ $skill->id_contractor_skill_ref }}">{{$skill->skill_name}}</option>
                                                    @endforeach
                                                @endif
                                        </select>

                                    </div>
                                </div>

                            </div>
                        </div>

                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-warning"
                        data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::contractor.cancel')}}</button>
                    <button type="button" class="btn btn-primary" id="change_skill_btn"
                         onclick="changeContractorSkill()">{{trans('maintenance::contractor.save')}}</button>
                </div>


            </div>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="locationModalLabel">{{trans('maintenance::contractor.contractor_locations')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_location" style="display: none">
                            <div id="ajx_err_msg_location"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_location" style="display: none">
                            <div id="ajx_suc_msg_location"></div>
                        </div>

                        <div class="box">
                            <div class="box-body">
                                <input type="hidden" id="change_location_contractor" value="">


                                <!-- location-->
                                <div class="form-group row">
                                    <label
                                        class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::contractor.location') }}:</label>
                                    <div class="col-sm-5 col-md-5 col-lg-5">

                                        <select name="location[]" id="location" multiple="multiple" class="form-control select ">
                                                @if (isset($locations))
                                                    @foreach ($locations as $location)
                                                        <option value="{{ $location->id_contractor_location_ref }}">{{$location->location}}</option>
                                                    @endforeach
                                                @endif
                                        </select>


                                        {{-- <select id='public-methods' multiple='multiple'>
                                            <option value='elem_1'>elem 1</option>
                                            <option value='elem_2' disabled>elem 2</option>
                                            <option value='elem_3'>elem 3</option>
                                            <option value='elem_4'>elem 4</option>
                                            <option value='elem_1000'>elem 100</option>
                                          </select> --}}

                                    </div>
                                </div>

                            </div>
                        </div>

                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-warning"
                        data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::contractor.cancel')}}</button>
                    <button type="button" class="btn btn-primary"  id="change_location_btn"
                         onclick="changeContractorLocation()">{{trans('maintenance::contractor.save')}}</button>
                </div>


            </div>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade" id="listModal" tabindex="-1" role="dialog" aria-labelledby="listModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; width: 80%">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="listModalLabel">{{trans('maintenance::contractor.contractor_task_list')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_list" style="display: none">
                            <div id="ajx_err_msg_list"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_list" style="display: none">
                            <div id="ajx_suc_msg_list"></div>
                        </div>

                        <div class="box">
                            <div class="box-body table-responsive">

                                <div>


                                    <!-- list-->

                                    <table id="task_list_table" class="table table-bordered table-hover dataTable text-center" style="width:100%!important;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{trans('maintenance::contractor.title')}}</th>
                                                <th>{{trans('maintenance::contractor.sla_expire_time')}}</th>
                                                <th>{{trans('maintenance::contractor.priority')}}</th>
                                                <th>{{trans('maintenance::contractor.status')}}</th>
                                                <th>{{trans('maintenance::contractor.task_report_date')}}</th>
                                                <th>{{trans('maintenance::contractor.task_start_date')}}</th>
                                                <th>{{trans('maintenance::contractor.task_end_date')}}</th>
                                                <th>{{trans('maintenance::contractor.operation')}}</th>
                                            </tr>
                                        </thead>


                                        <tbody id="task_list_body_tbl">


                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>{{trans('maintenance::contractor.title')}}</th>
                                                <th>{{trans('maintenance::contractor.sla_expire_time')}}</th>
                                                <th>{{trans('maintenance::contractor.priority')}}</th>
                                                <th>{{trans('maintenance::contractor.status')}}</th>
                                                <th>{{trans('maintenance::contractor.task_report_date')}}</th>
                                                <th>{{trans('maintenance::contractor.task_start_date')}}</th>
                                                <th>{{trans('maintenance::contractor.task_end_date')}}</th>
                                                <th>{{trans('maintenance::contractor.operation')}}</th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>



                            </div>
                        </div>

                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"  data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::contractor.close')}}</button>
                </div>


            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="contractorAttachmentModal" tabindex="-1" role="dialog" aria-labelledby="contractorAttachmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; width: 80%">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="contractorAttachmentModalLabel">{{trans('maintenance::contractor.contractor_attachment_list')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>


                <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="err_msg_box_list" style="display: none">
                            <div id="ajx_err_msg_list"></div>
                        </div>
                        <div class="alert alert-success alert-dismissible" id="suc_msg_box_list" style="display: none">
                            <div id="ajx_suc_msg_list"></div>
                        </div>

                        <div class="box">
                            <div class="box-body table-responsive">

                                <div>


                                    <!-- list-->

                                    <table id="contractor_attachment_table" class="table table-bordered table-hover dataTable text-center" style="width:100%!important;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('maintenance::maintenance.document_name') }}</th>
                                                <th>{{ __('maintenance::maintenance.document_extention') }}</th>
                                                <th>{{ __('maintenance::maintenance.description') }}</th>
                                                <!-- <th>{{ __('maintenance::maintenance.operation') }}</th> -->
                                            </tr>
                                        </thead>


                                        <tbody id="contractor_attachment_body_tbl">


                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('maintenance::maintenance.document_name') }}</th>
                                                <th>{{ __('maintenance::maintenance.document_extention') }}</th>
                                                <th>{{ __('maintenance::maintenance.description') }}</th>
                                                <!-- <th>{{ __('maintenance::maintenance.operation') }}</th> -->
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>



                            </div>
                        </div>

                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"  data-dismiss="modal" data-bs-dismiss="modal">{{trans('maintenance::contractor.close')}}</button>
                </div>


            </div>
        </div>
    </div>

    <!-- delete contractor document Modal -->
    <div class="modal fade" id="deleteContractorDocumentModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteContractorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteContractorDocumentModallabel">
                        {{ __('maintenance::contractor.delete_contractor_document_modal') }}</h4>
                    <button type="button" class="close" data-dismiss="modal"  data-bs-dismiss="modal"><span aria-hidden="true">×</span><span
                            class="sr-only">{{ __('general.close') }}</span></button>



                </div>
                <div class="form-horizontal" id="note_mgt_form" novalidate="novalidate">

                    <div class="alert alert-danger alert-dismissible" id="delete_contractor_document_err_msg_box"
                        style="display: none;">
                        <div id="delete_contractor_document_ajx_err_msg"></div>
                    </div>
                    <div class="alert alert-success alert-dismissible" id="delete_contractor_document_success_msg_box"
                        style="display: none;">
                        <div id="delete_contractor_document_ajx_success_msg"></div>
                    </div>
                    <div class="modal-body">

                        <h4>{{ __('maintenance::contractor.are_you_sure_to_delete_contractor_document') }}</h4>

                        <input type="hidden" id="deleted_business">

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="close_btn" class="btn btn-warning"
                            data-dismiss="modal" data-bs-dismiss="modal">{{ __('general.close') }}</button>
                        <button type="button" class="btn btn-danger"
                            id="btn_operate_delete">{{ __('general.yes') }}</button>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection



@section('script')
    <!-- <script src="{{ asset('resources/bootstrap-timepicker/js/moment.min.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>
 -->

    <!-- Laravel Javascript Validation -->
    {{-- <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>


    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script src="{{ asset('resources/bootstrap-multiselect/js/bootstrap-multiselect.js') }}"></script>


    <script src="{{ asset('resources/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('resources/select2/select2.full.min.js') }}"></script> --}}








    <script src="{{ asset('resources/Chart.js/Chart.bundle.min.js') }}"></script>


    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>

    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/select2.full.min.js') }}"></script>

    <script src="{{ asset('js/jquery.multi-select.js') }}"></script>
    {{-- <script src="{{ asset('js/bootstrap-multiselect.js') }}"></script> --}}



    <script>

        $('.select2#search_business').select2({
            placeholder: {
            id: '-1', // the value of the option
            text: 'Select a business'
            }
        });
        $('.select2#skills').select2({
            placeholder: {
                id: '-1', // the value of the option
                text: 'Select a skill'
            }
        });
        $('.select2#locations').select2({
            placeholder: {
                id: '-1', // the value of the option
                text: 'Select a location'
            }
        });

        $(document).ready(function () {

            // $("#search_business").val($("#search_business option:first").val());

            loadAllContractors();

        });
        ////////////////////////////////
        function searchAgain(){

            loadAllContractors();

          }
          ///////////////////////////////////
          function resetSearchbox() {
            //drop downs
            $("#search_business").val($("#search_business option:first").val());



            //inputs
            $('#skills').val('');
            $('#locations').val('');
            $('#contractor_name').val('');

            $('.select2#search_business').select2({
                placeholder: {
                id: '-1', // the value of the option
                text: 'Select a business'
                }
            });
            $('.select2#skills').select2({
                placeholder: {
                    id: '-1', // the value of the option
                    text: 'Select a skill'
                }
            });
            $('.select2#locations').select2({
                placeholder: {
                    id: '-1', // the value of the option
                    text: 'Select a location'
                }
            });

            searchAgain();

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
        function loadAllContractors(){

            // var spinHandle = loadingOverlay.activate();

            business = $('#search_business').val();
            skills = $('#skills').val();
            locations = $('#locations').val();
            contractor_name = $('#contractor_name').val();



            if(business.length != null){
                console.log('are nulle');
                 $("#search_business").val($("#search_business option:first").val());
                 business = $('#search_business').val();

            }

            send( '/maintenance/mgt_contractors_list',  {
                business :business,
                skills :skills,
                locations :locations,
                contractor_name :contractor_name,
            }, 'handleContractorTableBody', []);

        }
        ///////////////////////////////////////////////////////

        function handleContractorTableBody(){

            let contractor_list = return_value.contractors;
            let res = return_value.code;
            let message = return_value.message;
            if(res == "failure"){

                $('#contractor_list_table').DataTable().clear().destroy();

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
            else if(contractor_list != null && contractor_list !="undefined"){

                var htmlValue = "";
                Object.keys(contractor_list).forEach(function(k){

                    var counter = 1+parseInt(k);

                    console.log(contractor_list);

                    var id_contractor = contractor_list[k]["first_contractor"];
                    var name = contractor_list[k]["name"];
                    var id_business = contractor_list[k]["id_saas_client_business"];

                    var c_url = contractor_list[k]["c_url"];
                    var short_name = contractor_list[k]["short_name"];
                    var vat_number = contractor_list[k]["vat_number"]?contractor_list[k]["vat_number"]:'-';
                    var tel_number1 = contractor_list[k]["tel_number1"]?contractor_list[k]["tel_number1"]:'-';
                    var tel_number2 = contractor_list[k]["tel_number2"]?contractor_list[k]["tel_number2"]:'-';
                    var address_line1 = contractor_list[k]["address_line1"]?contractor_list[k]["address_line1"]:'-';

                    var operation = '<a href="'+c_url+'" target="_blank" data-toggle="tooltip" title="Edit Contractor" data-original-title="EDit Contractor">' +
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" id="edit' + counter + '" >' +
                        '<i class="fa fa-solid fa-edit" aria-hidden="true"></i></button>' +
                        '</a>' +
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Login Settings" onclick="showLoginSettingsModal('+id_business +','+id_contractor+')">'+
                        '<i class="fa fa-solid fa-cogs"></i>'+
                        '</button>'+

                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Set Skills" onclick="showSkillModal('+id_business +','+id_contractor+')">'+
                        '<i class="fa fa-star"></i>'+
                        '</button>'+

                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Set Location" onclick="showLocationModal('+id_business +','+id_contractor+')">'+
                        '<i class="fa fa-map-marker"></i>'+
                        '</button>'+


                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Task List" onclick="showListModal('+id_business +','+id_contractor+')">'+
                        '<i class="fa fa-solid fa-list"></i>'+
                        '</button>'+

                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Document List" onclick="showContractorAttachmentModal('+id_business +','+id_contractor+')">'+
                        '<i class="fa fa-solid fa-list"></i>'+
                        '</button>'+

                        '<button style="margin-right: 1px;" type="button" class="btn btn-danger allign-btn" title="Delete Contractor" onclick="showDeleteContractorModal('+id_business +','+id_contractor+')">'+
                        '<i class="fa fa-solid fa-trash"></i>'+
                        '</button>'

                        ;


                    htmlValue= htmlValue +"<tr><td>"+(counter)+"</td><td>"+name+"</td><td>"+short_name+"</td><td>"
                        +vat_number+"</td><td>"+tel_number1+"</td><td>"+tel_number2+"</td><td>"+address_line1+"</td><td>"+operation+"</td></tr>";


                });



                $('#contractor_list_table').DataTable().clear().destroy();
                $('#contractor_list_table #contractor_list_body_tbl').html('');
                $('#contractor_list_table #contractor_list_body_tbl').append(htmlValue);
                $('#contractor_list_table tfoot th').each( function () {
                    //var title = $(this).text();
                    //$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                } );

            //datatable
            var table = $('#contractor_list_table').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true,
                "aoColumnDefs": [

                    { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3,4,5,6,7] },{ "width": "20%", "targets": 7 }
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

        //////////////////////////////////////////////////////
        function showDeleteContractorModal(id_business,id_contractor){

            $('#deleted_business').val(id_business);
            $('#deleted_contractor').val(id_contractor);

            $("#err_msg_box_delete_contractor").css('display' , 'none');
            $("#suc_msg_box_delete_contractor").css('display' , 'none');
            $('#deleteContractorModal').modal('show');

        }
        ///////////////////////////////////////////////////////
        function deleteContractor(){
            //var spinHandle = loadingOverlay.activate();

            let deleted_contractor = $( '#deleted_contractor' ).val();

            let deleted_business = $( '#deleted_business' ).val();

            send( '/maintenance/contractor/mgt/delete/'+deleted_contractor,  {
                deleted_contractor:deleted_contractor,
                deleted_business:deleted_business,

            }, 'handleDeleteContractor', []);
        }

        /////////////////////////////////////////////////////////
        function handleDeleteContractor()
        {
            let message = return_value.message;
            let res = return_value.code;

            if(res == "failure"){
                var textmessage = message;

                // Object.keys(message).forEach(function(k) {
                //     textmessage+= message[k];
                // });

                $("#ajx_err_msg_delete_contractor").html(textmessage);
                $("#err_msg_box_delete_contractor").css('display' , 'block');

            }

            else{

                $('#deleteContractorModal').modal('hide');
                loadAllContractors();

            }


            //loadingOverlay.cancelAll();

        }
        /////////////////////////////////////////////////////////
        function showLoginSettingsModal(id_business,id_contractor){

            console.log('omad');
            $('#password').val("");
            $('#confirm_password').val("");
            $('#email').val("");

            $('#deleted_business').val(id_business);
            $('#deleted_contractor').val(id_contractor);

            let deleted_business = $( '#deleted_business' ).val();

            let deleted_contractor = $( '#deleted_contractor' ).val();

            //var spinHandle = loadingOverlay.activate();



            send( '/maintenance/mgt_contractor/email/'+id_contractor,  {

                id_business : deleted_business,
                id_contractor : deleted_contractor,

            }, 'handleShowLoginSettingsModal', [id_contractor]);


        }
        /////////////////////////////////////////////////////////
        function handleShowLoginSettingsModal(id_contractor){

            let user_info = return_value.user_info;
            let res = return_value.code;
            let message = return_value.message;
            if(res == "failure"){
                x=message;
                if(typeof message == "object"){
                    x="";
                    //var messages = get_object_vars(message);
                    var messages2 = Object.values(message);
                    for(var i=0;i<messages2.length;i++){
                        x=x+messages2[i];
                    }

                }


                $("#ajx_err_msg_login_setting").html(x);
                $("#err_msg_box_login_setting").css('display' , 'block');

            }
            else if(user_info != null && user_info !="undefined"){
                $('#email').val(user_info.email);
            }



            $('#changed_contractor').val(id_contractor);

            $("#err_msg_box_login_setting").css('display' , 'none');
            $("#suc_msg_box_login_setting").css('display' , 'none');

            $('#showLoginSettingsModal').modal('show');
            //loadingOverlay.cancelAll();

        }
        /////////////////////////////////////////////////////////
        function changeContractorLoginSettings(){
            //var spinHandle = loadingOverlay.activate();
            $("#err_msg_box_login_setting").css('display' , 'none');

            let deleted_business = $( '#deleted_business' ).val();
            let changed_contractor = $('#changed_contractor' ).val();
            let email = $('#email' ).val();
            let password = $('#password' ).val();
            let password_confirmation = $('#confirm_password' ).val();
            $("#save_contractor").attr('disabled','disabled');



            send( '/maintenance/mgt_contractor/login_settings/change',  {
                contractor:changed_contractor,
                id_business : deleted_business,
                email:email,
                password:password,
                password_confirmation:password_confirmation,
            }, 'handleChangeContractorLoginSettings', []);
        }
        ///////////////////////////////////////////////////////////
        function handleChangeContractorLoginSettings(){
            //loadingOverlay.cancelAll();

            let res = return_value.code;
            let message = return_value.message;

            if(res == "failure"){
                x=message;
                if(typeof message == "object"){
                    x="";
                    //var messages = get_object_vars(message);
                    var messages2 = Object.values(message);
                    for(var i=0;i<messages2.length;i++){
                        x=x+messages2[i];
                    }

                }
                $("#ajx_err_msg_login_setting").html(x);
                $("#err_msg_box_login_setting").css('display' , 'block');
                $("#save_contractor").removeAttr('disabled');

            }
            else{
                $("#ajx_suc_msg_login_setting").html(message);
                $("#suc_msg_box_login_setting").css('display' , 'block');
                setTimeout(function() {$('#showLoginSettingsModal').modal('hide');}, 3000);

            }





        }
        /////////////////////////////////////////////////////////
        function showSkillModal(id_business,id_contractor){



            //var spinHandle = loadingOverlay.activate();

            // $('#skill').multiselect({
            //     enableFiltering: false,
            //     includeSelectAllOption: true,
            //     maxHeight: 400,
            //     buttonWidth: '100%',
            //     dropLeft: true,
            //     selectAllText: 'Select All',
            //     selectAllValue: 0,
            //     enableFullValueFiltering: false,
            //     onDeselectAll: function() {
            //         // prepareClientRates();
            //         // buttonText: function(options, select) {
            //         //     if (options.length === 0) {
            //         //         return 'No option selected ...';
            //         //     }
            //         // }
            //     },
            //     onSelectAll: function() {
            //         // prepareClientRates();
            //         // buttonText: function(options, select) {
            //         //     if (options.length === 0) {
            //         //         return 'No option selected ...';
            //         //     }
            //         // }
            //     },
            //     onChange: function() {
            //         // prepareClientRates();
            //     },
            //     //nonSelectedText: 'Check an option!',
            //     //dropUp: true

            //     buttonText: function (options, select) {
            //         if (options.length === 0) {
            //             return 'Not selected';
            //         }
            //         else if (options.length > 9) {
            //             return 'More than 3 options selected!';
            //         }
            //         else {
            //             var labels = [];
            //             options.each(function () {
            //                 if ($(this).attr('label') !== undefined) {
            //                     labels.push($(this).attr('label'));
            //                 }
            //                 else {
            //                     labels.push($(this).html());
            //                 }
            //             });
            //             return labels.join(', ') + '';
            //         }
            //     }
            // });

            // $('#skill').multiselect("clearSelection");



            $('#skill').multiSelect({});
            $('#skill').multiSelect('deselect_all');


            $('#deleted_business').val(id_business);
            $('#deleted_contractor').val(id_contractor);

            let deleted_business = $( '#deleted_business' ).val();

            let deleted_contractor = $( '#deleted_contractor' ).val();



            send( '/maintenance/mgt_contractor/skill/'+id_contractor,  {
                id_business : deleted_business,
                id_contractor : deleted_contractor,

            }, 'handleShowSkillModal', [id_contractor]);


        }
        /////////////////////////////////////////////////////////
        function handleShowSkillModal(id_contractor){

            let contractor_skills = return_value.contractor_skills;
            let res = return_value.code;
            let message = return_value.message;
            if(res == "failure"){
                x=message;
                if(typeof message == "object"){
                    x="";
                    //var messages = get_object_vars(message);
                    var messages2 = Object.values(message);
                    for(var i=0;i<messages2.length;i++){
                        x=x+messages2[i];
                    }

                }


                $("#ajx_err_msg_skill").html(x);
                $("#err_msg_box_skill").css('display' , 'block');

            }
            else if(contractor_skills != null && contractor_skills !="undefined"){
                var arr = [];

                var htmlValue = "";
                Object.keys(contractor_skills).forEach(function(k){

                    var counter = 1+parseInt(k);

                    var skill_name = contractor_skills[k]["skill_name"];
                    var id_contractor_skill_ref = contractor_skills[k]["id_contractor_skill_ref"];

                    arr.push(id_contractor_skill_ref.toString());
                    //$("#skill option[value="+id_contractor_skill_ref+"]").attr('selected', 'selected');



                });

                $('#skill').multiSelect('select', arr);
                $('#skill').multiSelect('refresh');



            }

            $("#change_skill_contractor").val(id_contractor);
            $("#change_skill_btn").removeAttr('disabled');


            $("#err_msg_box_skill").css('display' , 'none');
            $("#suc_msg_box_skill").css('display' , 'none');



            $('#skillModal').modal('show');
            //loadingOverlay.cancelAll();

        }
        /////////////////////////////////////////////////////////
        function changeContractorSkill(){
            //var spinHandle = loadingOverlay.activate();
            $("#err_msg_box_skill").css('display' , 'none');

            let change_skill_contractor = $('#change_skill_contractor' ).val();
            let skills = $('select#skill').val();
            $("#change_skill_btn").attr('disabled','disabled');


            let deleted_business = $( '#deleted_business' ).val();


            send( '/maintenance/mgt_contractor/skills/change',  {
                contractor:change_skill_contractor,
                skills:skills,
                id_business : deleted_business
            }, 'handleChangeContractorSkill', []);
        }
        ///////////////////////////////////////////////////////////
        function handleChangeContractorSkill(){
            //loadingOverlay.cancelAll();

            let res = return_value.code;
            let message = return_value.message;
            if(res == "failure"){
                x=message;
                if(typeof message == "object"){
                    x="";
                    //var messages = get_object_vars(message);
                    var messages2 = Object.values(message);
                    for(var i=0;i<messages2.length;i++){
                        x=x+messages2[i];
                    }

                }
                $("#ajx_err_msg_skill").html(x);
                $("#err_msg_box_skill").css('display' , 'block');
                $("#change_skill_btn").removeAttr('disabled');

            }
            else{
                $("#ajx_suc_msg_skill").html(message);
                $("#suc_msg_box_skill").css('display' , 'block');
                setTimeout(function() {$('#skillModal').modal('hide');}, 3000);

            }





        }
        /////////////////////////////////////////////////////////
        function showLocationModal(id_business,id_contractor){

        //     $('.select2#location').select2({
        //     placeholder: {
        //         id: '-1', // the value of the option
        //         text: 'Select a location'
        //     }
        // });



            //var spinHandle = loadingOverlay.activate();

            // $('#location').multiselect({
            //     enableFiltering: false,
            //     includeSelectAllOption: true,
            //     maxHeight: 400,
            //     buttonWidth: '100%',
            //     dropLeft: true,
            //     selectAllText: 'Select All',
            //     selectAllValue: 0,
            //     enableFullValueFiltering: false,
            //     onDeselectAll: function() {
            //         // prepareClientRates();
            //         // buttonText: function(options, select) {
            //         //     if (options.length === 0) {
            //         //         return 'No option selected ...';
            //         //     }
            //         // }
            //     },
            //     onSelectAll: function() {
            //         // prepareClientRates();
            //         // buttonText: function(options, select) {
            //         //     if (options.length === 0) {
            //         //         return 'No option selected ...';
            //         //     }
            //         // }
            //     },
            //     onChange: function() {
            //         // prepareClientRates();
            //     },
            //     //nonSelectedText: 'Check an option!',
            //     //dropUp: true

            //     buttonText: function (options, select) {
            //         if (options.length === 0) {
            //             return 'Not selected';
            //         }
            //         else if (options.length > 9) {
            //             return 'More than 3 options selected!';
            //         }
            //         else {
            //             var labels = [];
            //             options.each(function () {
            //                 if ($(this).attr('label') !== undefined) {
            //                     labels.push($(this).attr('label'));
            //                 }
            //                 else {
            //                     labels.push($(this).html());
            //                 }
            //             });
            //             return labels.join(', ') + '';
            //         }
            //     }
            // });

            $('#location').multiSelect({});
            $('#location').multiSelect('deselect_all');

            // $('#location').multiselect("clearSelection");
            $("#change_location_btn").removeAttr('disabled');

            $('#deleted_business').val(id_business);
            $('#deleted_contractor').val(id_contractor);

            let deleted_business = $( '#deleted_business' ).val();

            let deleted_contractor = $( '#deleted_contractor' ).val();



            send( '/maintenance/mgt_contractor/location/'+id_contractor,  {

                id_business : deleted_business,
                id_contractor : deleted_contractor,

            }, 'handleShowMgtLocationModal', [id_contractor]);


        }
        /////////////////////////////////////////////////////////
        function handleShowMgtLocationModal(id_contractor){

            let contractor_locations = return_value.locations;
            let res = return_value.code;
            let message = return_value.message;
            if(res == "failure"){
                x=message;
                if(typeof message == "object"){
                    x="";
                    //var messages = get_object_vars(message);
                    var messages2 = Object.values(message);
                    for(var i=0;i<messages2.length;i++){
                        x=x+messages2[i];
                    }

                }


                $("#ajx_err_msg_location").html(x);
                $("#err_msg_box_location").css('display' , 'block');

            }
            else if(contractor_locations != null && contractor_locations !="undefined"){
                var arr = [];

                var htmlValue = "";
                Object.keys(contractor_locations).forEach(function(k){

                    var counter = 1+parseInt(k);

                    var id_contractor_location_ref = contractor_locations[k]["id_contractor_location_ref"];

                    arr.push(id_contractor_location_ref.toString());

                });
                console.log(arr);

                $('#location').multiSelect('select', arr);
                $('#location').multiSelect('refresh');



            }

            $("#change_location_contractor").val(id_contractor);

            $("#err_msg_box_location").css('display' , 'none');
            $("#suc_msg_box_location").css('display' , 'none');



            $('#locationModal').modal('show');
            //loadingOverlay.cancelAll();

        }
        /////////////////////////////////////////////////////////
        function showListModal(id_business,id_contractor){

            $('#deleted_business').val(id_business);
            $('#deleted_contractor').val(id_contractor);

            let deleted_business = $( '#deleted_business' ).val();

            let deleted_contractor = $( '#deleted_contractor' ).val();

            //var spinHandle = loadingOverlay.activate();

            send( '/maintenance/mgt_contractor/tasks/'+id_contractor,  {

                id_business : deleted_business,
                id_contractor : deleted_contractor,
            }, 'handleShowListModal', [id_contractor]);


        }
        /////////////////////////////////////////////////////////
        function handleShowListModal(id_contractor){


            let tasks = return_value.tasks;
            let res = return_value.code;
            let message = return_value.message;
            if(res == "failure"){

                $('#task_list_table').DataTable().clear().destroy();

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
            else if(tasks != null && tasks !="undefined"){

                var htmlValue = "";
                Object.keys(tasks).forEach(function(k){

                    var counter = 1+parseInt(k);


                    var m_url = tasks[k]["m_url"];
                    var maintenance_job_title = tasks[k]["maintenance_job_title"];
                    var sla_expire_time = tasks[k]["remain_time"];
                    var priority = tasks[k]["priority_name"];
                    var status = tasks[k]["job_status_name"];
                    var job_report_date_time = tasks[k]["job_report_date_time"];
                    var job_start_date_time = tasks[k]["job_start_date_time"]?tasks[k]["job_start_date_time"]:'-';
                    var job_finish_date_time = tasks[k]["job_finish_date_time"]?tasks[k]["job_finish_date_time"]:'-';

                    var operation = '<a href="'+m_url+'" target="_blank" data-toggle="tooltip" title="Edit Maintenance Job" >' +
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" id="edit' + counter + '" >' +
                        '<i class="fa fa-solid fa-edit" aria-hidden="true"></i></button>' +
                        '</a>';


                    htmlValue= htmlValue +"<tr><td>"+(counter)+"</td><td>"+maintenance_job_title+"</td><td>"+sla_expire_time+"</td><td>"
                        +priority+"</td><td>"+status+"</td><td>"+job_report_date_time+"</td><td>"+job_start_date_time+"</td><td>"+job_finish_date_time+"</td><td>"+operation+"</td></tr>";


                });



                $('#task_list_table').DataTable().clear().destroy();
                $('#task_list_table #task_list_body_tbl').html('');
                $('#task_list_table #task_list_body_tbl').append(htmlValue);

            //datatable
            var table = $('#task_list_table').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true,
                "aoColumnDefs": [

                    { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3,4,5,6,7 ,8] },{ "width": "20%", "targets":8 }
                ]
            });


            }




            $('#listModal').modal('show');
            //loadingOverlay.cancelAll();

        }
        ////////////////////////////////////////////////////
        function showContractorAttachmentModal(id_business,id_contractor){

            $('#deleted_business').val(id_business);
            $('#deleted_contractor').val(id_contractor);

            let deleted_business = $( '#deleted_business' ).val();

            let deleted_contractor = $( '#deleted_contractor' ).val();

            //var spinHandle = loadingOverlay.activate();

            send( '/maintenance/mgt_contractor/attachments/'+id_contractor,  {
                id_business : deleted_business,
                id_contractor : deleted_contractor,

            }, 'handleShowContractorAttachmentModal', [id_contractor]);


        }
        /////////////////////////////////////////////////////
        function handleShowContractorAttachmentModal(id_contractor){


            let attachments = return_value.attachments;
            let res = return_value.code;
            let message = return_value.message;
            if(res == "failure"){

                $('#contractor_attachment_table').DataTable().clear().destroy();

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
            else if(attachments != null && attachments !="undefined"){

                var htmlValue = "";
                Object.keys(attachments).forEach(function(k){

                    var counter = 1+parseInt(k);


                    counter = 1 + parseInt(k);

                    var id_contractor_document = attachments[k]["id_contractor_document"];
                    var document_name = attachments[k]["document_name"];
                    var document_extention = attachments[k]["document_extention"];
                    var description = attachments[k]["description"];

                //     var operation =
                //     '<a data-toggle="tooltip" title="Delete Contractor Document" class="btn btn-danger allign-btn"  data-original-title="Delete Contractor Document" onclick="deleteContractorDocument(' +
                //     id_contractor_document + ')" >' +
                //     '<i class="fa-solid fa-trash" ></i> </a>';

                //     operation += '<a href="/maintenance/contractor_attachment/' + id_contractor_document +
                //     '/download" style="margin-left:10px" class="btn btn-primary allign-btn" target="blank" ><i class="fa-solid fa-download"></i></a>';

                //  operation += '<a href="/contractor/files/' + document_name +'" style="margin-left:10px" class="btn btn-primary allign-btn" target="blank" title="Show Document" ><i class="fa-solid fa-eye "></i></a>';




                    htmlValue += "<tr><td>" + counter + "</td><td>" + document_name + "</td><td>" +
                    document_extention +
                    "</td><td>" + description + "</td></tr>";


                });



                $('#contractor_attachment_table').DataTable().clear().destroy();
                $('#contractor_attachment_table #contractor_attachment_body_tbl').html('');
                $('#contractor_attachment_table #contractor_attachment_body_tbl').append(htmlValue);

            //datatable
            var table = $('#contractor_attachment_table').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true,
                "aoColumnDefs": [

                    { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3] },{ "width": "20%", "targets":3 }
                ]
            });


            }


            $('#contractorAttachmentModal').modal('show');
            //Overlay.cancelAll();

        }
        ////////////////////////////////////////////////////////////
        function deleteContractorDocument(id_contractor_document) {

            $("#delete_contractor_document_ajx_err_msg").html('');
            $("#delete_contractor_document_err_msg_box").css('display', 'none');

            $("#delete_contractor_document_ajx_success_msg").html('');
            $("#delete_contractor_document_success_msg_box").css('display', 'none');
            //action
            $("#btn_operate_delete").prop('onclick', null);
            $('#btn_operate_delete').attr('onClick', 'submitDeleteContractorDocument(' + id_contractor_document +
                ');');
            //showModal
            $('#deleteContractorDocumentModal').modal('show');
        }
/////////////////////////////////////////////////////////////////

        function submitDeleteContractorDocument(id_contractor_document){
            // let maintenance_id = $('#id_maintenance').val();

            send('/maintenance/contractor_document/delete', {

                id_contractor_document: id_contractor_document,
                // maintenance_id: maintenance_id,
            }, 'handleDisableContractorDocument', []);
        }
/////////////////////////////////////////////////


        function handleDisableContractorDocument() {

            let message = return_value.message;
            let res = return_value.code;
            let id_contractor = return_value.id_contractor;


            if (res == "failure") {

                $("#delete_contractor_document_ajx_err_msg").html(message);
                $("#delete_contractor_document_err_msg_box").css('display', 'block');
            } else {
                $("#delete_contractor_document_ajx_success_msg").html(message);
                $("#delete_contractor_document_success_msg_box").css('display', 'block');

                setTimeout(function() {
                    $('#deleteContractorDocumentModal').modal('hide')
                }, 4000);

                showContractorAttachmentModal(id_contractor);

            }


}
        /////////////////////////////////////////////////////////
        function changeContractorLocation(){
            //var spinHandle = loadingOverlay.activate();
            $("#err_msg_box_location").css('display' , 'none');

            let change_location_contractor = $('#change_location_contractor' ).val();
            let locations = $('select#location').val();
            $("#change_location_btn").attr('disabled','disabled');


            let deleted_business = $( '#deleted_business' ).val();


            send( '/maintenance/mgt_contractor/locations/change',  {
                contractor:change_location_contractor,
                locations:locations,
                id_business : deleted_business
            }, 'handleChangeMgtContractorLocation', []);
        }
        ///////////////////////////////////////////////////////////
        function handleChangeMgtContractorLocation(){
            //loadingOverlay.cancelAll();

            let res = return_value.code;
            let message = return_value.message;
            if(res == "failure"){
                x=message;
                if(typeof message == "object"){
                    x="";
                    //var messages = get_object_vars(message);
                    var messages2 = Object.values(message);
                    for(var i=0;i<messages2.length;i++){
                        x=x+messages2[i];
                    }

                }
                $("#ajx_err_msg_location").html(x);
                $("#err_msg_box_location").css('display' , 'block');
                $("#change_location_btn").removeAttr('disabled');

            }
            else{
                $("#ajx_suc_msg_location").html(message);
                $("#suc_msg_box_location").css('display' , 'block');
                setTimeout(function() {$('#locationModal').modal('hide');}, 3000);

            }





        }





    </script>




@endsection
