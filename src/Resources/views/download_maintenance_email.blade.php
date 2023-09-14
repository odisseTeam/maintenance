@extends('maintenance::sdr')

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
     
            <div class="">



                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">


                        <form method="POST" action="/maintenance/contractor/send/email">

                            @csrf
                            <div class="">
                                <div class="" id="main_text">

                                        <div class="col-md-11">


                                            <input type="hidden" name="email_content_for_download" id="email_content_for_download" >
                                            <input type="hidden" name="html_maintenance_temp" id="html_maintenance_temp" value="{{$template_message_body}}" >

                                            <div>
                                              {!! $template_message_body !!}
                                            </div>

                                            <hr>




                                        
                                            <h4>{{ __('maintenance::contractor.selected_notes') }}:</h4>

                                            <div>

                                              {!! $selected_notes !!}

                                            </div>
                                            

                                            <h4>{{ __('maintenance::contractor.attached_files') }}</h4>

                                              <div>
                                               {!! $selected_document !!}

                                              </div>

                                            <input type="hidden" name="id_contractor" id="hidden_id_contractor"  value="@if(isset($contractor)){{$contractor->id_contractor}}@endif">
                                            <input type="hidden" name="id_maintenance_job" id="hidden_id_maintenance_job" value="@if(isset($contractor)){{$contractor->id_maintenance_job}}@endif" >
                                          
                                   
                                          
                                                <h4>{{ __('maintenance::contractor.additional_comments') }}:</h4>

                                                <div class="input-group col-xs-8 col-sm-8 col-md-8">

                                                   {!! $additional_comment !!}


                                                </div>
                                        </div>

                                </div>

                            </div>

                        </form>




                    </div>
                </div>



            </div>

        <!-- </div> -->

              
    </section>



@endsection


