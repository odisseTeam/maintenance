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





    <section>
        <div class="box">
            <div class="box-header">
                <h1>
                    {{__('maintenance::dashboard.create_email_template')}}
                </h1>
            </div>
            <div class="box-body">



                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">


                        <form method="POST" action="/maintenance/contractor/send/email">

                            @csrf
                            <div class="box">
                                <div class="box-body" id="main_text">

                                        <div class="col-md-11">

                                            <h4>{{ __('maintenance::contractor.maintenance_template') }}</h4>

                                            <input type="hidden" name="email_content_for_download" id="email_content_for_download" >
                                            <input type="hidden" name="html_maintenance_temp" id="html_maintenance_temp" value="{{$template_message_body}}" >

                                            <div>
                                              {!! $template_message_body !!}
                                            </div>

                                            <hr>

                                                 <!-- commencement_date -->
                                                <div class="form-group row ">
                                                    <div class="col-sm-10 col-md-10 col-xs-10 col-lg-10">
                                                        <label
                                                            class="col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label text-right">{{ trans('maintenance::maintenance.commencement_date') }}:</label>
                                                            <div class="col-xs-10 col-sm-6 col-md-6 col-lg-6">

                                                            <div class="">
                                                                    <div class="form-group">
                                                                        <div class="input-group date" id="datepicker2" >
                                                                        <input type="text" class="form-control"  value="{{ $maintenance->commencement_date }}"
                                                                        value="{{ $commencement_date }}"
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
                                                </div>



                                            <!-- complete_date -->
                                            <div class="form-group row ">
                                              <div class="col-sm-10 col-md-10 col-xs-10 col-lg-10">

                                                <label
                                                    class="col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label text-right">{{ trans('maintenance::maintenance.complete_date') }}:</label>
                                                    <div class="col-xs-10 col-sm-6 col-md-6 col-lg-6">

                                                    <div class="">
                                                            <div class="form-group">
                                                                <div class="input-group date" id="datepicker3" >
                                                                <input type="text" class="form-control"  value="{{ $maintenance->complete_date }}"
                                                                value="{{ $complete_date }}"
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
                                            </div>
                                            <hr>


                                        
                                            <h4>{{ __('maintenance::contractor.selected_notes') }}:</h4>

                                            <div>

                                              {!! $selected_notes !!}

                                            </div>
                                            
                                              <hr>

                                            <h4>{{ __('maintenance::contractor.attached_files') }}</h4>

                                            <input type="hidden" name="id_contractor" id="hidden_id_contractor"  value="@if(isset($contractor)){{$contractor->id_contractor}}@endif">
                                            <input type="hidden" name="id_maintenance_job" id="hidden_id_maintenance_job" value="@if(isset($contractor)){{$contractor->id_maintenance_job}}@endif" >
                                          
                                            <div>
                                              {!! $selected_document !!}

                                            </div>

                                          

                                            <hr>


                                            <div class="form-group row">
                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-left" for="exampleFormControlTextarea1">{{ __('maintenance::contractor.additional_comments') }} :</label>

                                                <div class="input-group col-xs-8 col-sm-8 col-md-8">

                                                    <textarea  class="form-control" id="contractor_job_attachment_text" name="contractor_job_attachment_text" rows="4" cols="2"  style="border: none;" >{{$additional_comment}}</textarea>

                                                </div>
                                            </div>
                                        </div>

                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary" style="float:right;min-width:55px"  >{{ __('maintenance::contractor.send_email') }}</button>
                                    <button type="button" onclick="downloadPdf()" class="btn btn-primary" style="float:right;min-width:55px;margin-right:2px"  >{{ __('maintenance::maintenance.download_pdf') }}</button>
                                    <button type="button" class="btn btn-primary" style="float:right;min-width:55px;margin-right:2px" onclick="previewEmailContent()" >{{ __('general.preview') }}</button>
                                    <a href="/maintenance/dashboard" ><button style="float:right;margin-right:2px" type="button" id="close_btn" class="btn btn-warning"
                                        >{{ __('maintenance::contractor.back') }}</button></a>


                                </div>
                            </div>

                        </form>




                    </div>
                </div>



            </div>

        </div>

              
    </section>



@endsection


