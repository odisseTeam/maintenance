@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title').' '.__('maintenance::contractor.contractor'))


@section('body_class', 'login-page')

@section('css')


    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.css') }}"/>
    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/iCheck/all.css')}}" />
    <link rel="stylesheet" href="{{ asset('resources/select2/select2.min.css') }}" />

    <style>
  .select2-selection--multiple {
  border: 0px  ;
}
.select2-container--default {
  border: 0px  ;
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

    @if( $errors->any() )
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
            @if(! isset($contractor))
        {{trans('maintenance::contractor.create_contractor')}}
            @else
            {{trans('maintenance::contractor.edit_contractor')}}
            @endif
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header">

                            <form action="@if(! isset($contractor)){{'/maintenance/contractor'}}@else{{'/maintenance/contractor/'.$contractor->id_contractor}}@endif" method="post">
                                @csrf
                                <div class="box-body">


                                    <!-- Name -->
                                    <div class="form-group row">
                                        <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.name')}}:</label>

                                        <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                            <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                    <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.name_placeholder')}}" autocomplete="off" id="name" name="name" value="@if (old('name')){{old('name')}}@elseif(null==old('_token') && isset($contractor)) {{$contractor->name}} @endif ">
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Short Name -->
                                    <div class="form-group row">
                                        <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.short_name')}}:</label>

                                        <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                            <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.short_name_placeholder')}}" autocomplete="off" id="short_name" name="short_name" value="@if (old('short_name')){{old('short_name')}}@elseif(null==old('_token') && isset($contractor)) {{$contractor->short_name}} @endif ">
                                            </div>

                                        </div>
                                    </div>

                                    @if(! isset($contractor))

                                        <!-- Email -->
                                        <div class="form-group row">
                                            <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.email')}}:</label>

                                            <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                    <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.email_placeholder')}}" autocomplete="off" id="email" name="email" value="@if (old('email')){{old('email')}} @endif ">
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Password -->
                                        <div class="form-group row">
                                            <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.password')}}:</label>

                                            <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                    <input type="password" class="form-control pull-right" autocomplete="off" id="password" name="password" value="@if (old('password')){{old('password')}} @endif">
                                                </div>

                                            </div>
                                        </div>
                                    @endif

                                    <!-- VAT Number -->
                                    <div class="form-group row">
                                        <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.vat_number')}}:</label>

                                        <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                            <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.vat_number_placeholder')}}" autocomplete="off" id="vat_number" name="vat_number" value="@if (old('vat_number')){{old('vat_number')}}@elseif(null==old('_token') && isset($contractor) ){{$contractor->vat_number}} @endif ">
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Tel Number1 -->
                                    <div class="form-group row">
                                        <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.tel_number1')}}:</label>

                                        <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                            <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.tel_number1_placeholder')}}" autocomplete="off" id="tel_number1" name="tel_number1" value="@if (old('tel_number1')){{old('tel_number1')}}@elseif(null==old('_token') && isset($contractor) ){{$contractor->tel_number1}} @endif ">
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Tel Number2 -->
                                    <div class="form-group row">
                                        <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.tel_number2')}}:</label>

                                        <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                            <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.tel_number2_placeholder')}}" autocomplete="off" id="tel_number2" name="tel_number2" value="@if (old('tel_number2')){{old('tel_number2')}}@elseif(null==old('_token') && isset($contractor)) {{$contractor->tel_number2}} @endif ">
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Address Line1-->
                                    <div class="form-group row">
                                        <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::contractor.address_line1')}}:</label>
                                        <div class="col-xs-10 col-sm-10 col-md-10">
                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                            <textarea class="form-control" rows="4" name="address_line1" id="address_line1" column="40" >@if (old('address_line1')){{old('address_line1')}}@elseif(null==old('_token') && isset($contractor) ){{$contractor->address_line1}} @endif</textarea>
                                        </div>

                                        </div>
                                    </div>

                                    <!-- Address Line2-->
                                    <div class="form-group row">
                                        <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::contractor.address_line2')}}:</label>
                                        <div class="col-xs-10 col-sm-10 col-md-10">
                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                            <textarea class="form-control" rows="4" name="address_line2" id="address_line2" column="40" >@if (old('address_line2')){{old('address_line2')}}@elseif(null==old('_token') && isset($contractor) ){{$contractor->address_line2}} @endif</textarea>
                                        </div>

                                        </div>
                                    </div>


                                    <!-- Address Line3-->
                                    <div class="form-group row">
                                        <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::contractor.address_line3')}}:</label>
                                        <div class="col-xs-10 col-sm-10 col-md-10">
                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                            <textarea class="form-control" rows="4" name="address_line3" id="address_line3" column="40" >@if (old('address_line3')){{old('address_line3')}}@elseif(null==old('_token') && isset($contractor) ){{strip_tags($contractor->address_line3)}} @endif</textarea>
                                        </div>

                                        </div>
                                    </div>

                                </div>




                                <div class="box-footer text-right">


                                    <a href="/maintenance/contractors"><button type="button" class="btn btn-warning">{{trans('maintenance::maintenance.cancel')}}</button></a>



                                    <button type="submit" id="save_maintenance"
                                        class="btn btn-primary">{{trans('maintenance::maintenance.save')}}</button>



                                </div>
                            </form>

                        </div>
                    </div>
                </div>
        </div>

    </section>
    <!-- /.content -->




@endsection



@section('script')
<script src="{{ asset('resources/bootstrap-timepicker/js/moment.min.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>

    <script src="{{ asset('resources/iCheck/icheck.min.js') }}"></script>

    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('resources/select2/select2.full.min.js') }}"></script>

    <script src="{{ asset('js/maintenance.js') }}"></script>

    <script>

      $('.select2').select2();



       //////////////////////////////////////////////////////////

       $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

        });


/////////////////////////////////////////////////////////

    </script>

@endsection
