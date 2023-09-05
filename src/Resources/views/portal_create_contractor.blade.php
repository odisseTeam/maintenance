@extends('layouts.blank_js')


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


    <!-- Content Header (Page header) -->
    <section class="content-header">

    </section>

    <!-- Main content -->
    <section class="content">



    <!-- [ navigation menu ] end -->
    <div class="pcoded-content">





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




        <!-- [ breadcrumb ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-home bg-c-blue sdr-primary"></i>
                        <div class="d-inline">
                            <h5>{{__('maintenance::contractor.contractor_management')}}</h5>
                            <span>{{__('maintenance::contractor.create_contractor')}}</span>
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
                            <li class="breadcrumb-item"><a href="#!">{{__('maintenance::contractor.create_contractor')}}</a> </li>
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
                                            {{__('maintenance::contractor.create_contractor')}}
                                        </h1>
                                    </div>
                                    <div class="box-body card-block">

                                        <div class="nav-tabs-custom">

                                                {{-- <ul class="nav nav-tabs">
                                                    @if(! isset($contractor))
                                                    <li
                                                            class="{{ (null == session('active_tab') or session('active_tab') == 'contractorDetail') ? 'active' : '' }}">
                                                            <a id="contractorDetail_tab" href="#contractorDetail"
                                                                data-toggle="tab">{{ __('maintenance::contractor.create_contractor') }}</a>
                                                        </li>
                                                    @else
                                                        <li
                                                            class="{{ (null == session('active_tab') or session('active_tab') == 'contractorDetail') ? 'active' : '' }}">
                                                            <a id="contractorDetail_tab" href="#contractorDetail"
                                                                data-toggle="tab">{{ __('maintenance::contractor.edit_contractor') }}</a>
                                                        </li>
                                                        <li class="{{ session('active_tab') == 'contractor_document' ? 'active' : '' }}">
                                                            <a id="contractor_document_tab" href="#contractor_document"
                                                                data-toggle="tab">{{ __('maintenance::contractor.contractor_document') }}</a>
                                                        </li>
                                                    @endif


                                                </ul> --}}
                                                <div class="tab-content row">
                                                        <div class="{{ (null == session('active_tab') or session('active_tab') == 'maintenanceDetail') ? 'active' : '' }} tab-pane col-xs-12" id="contractorDetail">
                                                            <div class="box box-primary">
                                                                <div class="box-header">

                                                                    <form action="@if(! isset($contractor)){{'/maintenance/mgt_contractor/portal/store'}}@else{{'/maintenance/contractor/'.$contractor->id_contractor}}@endif" method="post"  enctype="multipart/form-data" >
                                                                        @csrf
                                                                        <div class="box-body">


                                                        <!-- saas client budiness-->
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.saas_client_business') }}:</label>
                                                            <div class="col-sm-10 col-md-10 col-lg-10">


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




                                                                            <!-- Name -->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.name')}}:</label>

                                                                                <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                                                            <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.name_placeholder')}}" autocomplete="off" id="name" name="name" value="@if(old('name')){{old('name')}}@elseif(null==old('_token') && isset($contractor)){{$contractor->name}}@endif">
                                                                                    </div>

                                                                                </div>
                                                                            </div>

                                                                            <!-- Short Name -->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.short_name')}}:</label>

                                                                                <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                                                        <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.short_name_placeholder')}}" autocomplete="off" id="short_name" name="short_name" value="@if(old('short_name')){{old('short_name')}}@elseif(null==old('_token') && isset($contractor)){{$contractor->short_name}}@endif">
                                                                                    </div>

                                                                                </div>
                                                                            </div>

                                                                            @if(! isset($contractor))

                                                                                <!-- Email -->
                                                                                <div class="form-group row">
                                                                                    <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.email')}}:</label>

                                                                                    <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                                                            <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.email_placeholder')}}" autocomplete="off" id="email" name="email" value="@if(old('email')){{old('email')}}@endif">
                                                                                        </div>

                                                                                    </div>
                                                                                </div>

                                                                                <!-- Password -->
                                                                                <div class="form-group row">
                                                                                    <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.password')}}:</label>

                                                                                    <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                                                        <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                                                            <input type="password" class="form-control pull-right" autocomplete="off" id="password" name="password" value="@if(old('password')){{old('password')}}@endif">
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            @endif

                                                                            <!-- VAT Number -->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.vat_number')}}:</label>

                                                                                <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                                                        <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.vat_number_placeholder')}}" autocomplete="off" id="vat_number" name="vat_number" value="@if(old('vat_number')){{old('vat_number')}}@elseif(null==old('_token') && isset($contractor)){{$contractor->vat_number}}@endif">
                                                                                    </div>

                                                                                </div>
                                                                            </div>

                                                                            <!-- Tel Number1 -->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.tel_number1')}}:</label>

                                                                                <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                                                        <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.tel_number1_placeholder')}}" autocomplete="off" id="tel_number1" name="tel_number1" value="@if(old('tel_number1')){{old('tel_number1')}}@elseif(null==old('_token') && isset($contractor)){{$contractor->tel_number1}}@endif">
                                                                                    </div>

                                                                                </div>
                                                                            </div>

                                                                            <!-- Tel Number2 -->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2  text-right">{{trans('maintenance::contractor.tel_number2')}}:</label>

                                                                                <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">
                                                                                        <input type="text" class="form-control pull-right" placeholder="{{trans('maintenance::contractor.tel_number2_placeholder')}}" autocomplete="off" id="tel_number2" name="tel_number2" value="@if(old('tel_number2')){{old('tel_number2')}}@elseif(null==old('_token') && isset($contractor)){{$contractor->tel_number2}}@endif">
                                                                                    </div>

                                                                                </div>
                                                                            </div>

                                                                            <!-- Address Line1-->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::contractor.address_line1')}}:</label>
                                                                                <div class="col-xs-10 col-sm-10 col-md-10">
                                                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                                                    <textarea class="form-control" rows="4" name="address_line1" id="address_line1" column="40" >@if(old('address_line1')){{old('address_line1')}}@elseif(null==old('_token') && isset($contractor) ){{$contractor->address_line1}}@endif</textarea>
                                                                                </div>

                                                                                </div>
                                                                            </div>

                                                                            <!-- Address Line2-->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::contractor.address_line2')}}:</label>
                                                                                <div class="col-xs-10 col-sm-10 col-md-10">
                                                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                                                    <textarea class="form-control" rows="4" name="address_line2" id="address_line2" column="40" >@if(old('address_line2')){{old('address_line2')}}@elseif(null==old('_token') && isset($contractor) ){{$contractor->address_line2}}@endif</textarea>
                                                                                </div>

                                                                                </div>
                                                                            </div>


                                                                            <!-- Address Line3-->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::contractor.address_line3')}}:</label>
                                                                                <div class="col-xs-10 col-sm-10 col-md-10">
                                                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                                                    <textarea class="form-control" rows="4" name="address_line3" id="address_line3" column="40" >@if(old('address_line3')){{old('address_line3')}}@elseif(null==old('_token') && isset($contractor) ){{strip_tags($contractor->address_line3)}}@endif</textarea>
                                                                                </div>

                                                                                </div>
                                                                            </div>
                                                                            @if(! isset($contractor))

                                                                            <!-- Attachments-->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right" style="margin-top:10px;">{{trans('maintenance::contractor.attachments')}}:</label>
                                                                                <div class="col-xs-10 col-sm-10 col-md-10">
                                                                                <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                                                <input type="file" id="files" name="files[]" class="" multiple style="margin-top:10px;">
                                                                                </div>

                                                                                </div>
                                                                            </div>

                                                                            <!-- Attachments Description-->
                                                                            <div class="form-group row">
                                                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right" style="margin-top:10px;">{{trans('maintenance::contractor.attachment_description')}}:</label>
                                                                                <div class="col-xs-10 col-sm-10 col-md-10">
                                                                                    <div class="input-group col-xs-10 col-sm-10 col-md-10">

                                                                                        <textarea class="form-control" rows="4" name="file_description" id="file_description" column="40">{{ old('file_description') }}</textarea>

                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        </div>




                                                                        <div class="box-footer text-right">


                                                                            <a href="/maintenance/contractors"><button type="button" class="btn btn-warning">{{trans('maintenance::maintenance.cancel')}}</button></a>



                                                                            <button type="submit" id="save_maintenance" class="btn btn-primary">{{trans('maintenance::maintenance.save')}}</button>



                                                                        </div>
                                                                    </form>
                                                                    @if( isset($contractor))
                                                                        <form action="/maintenance/contractor_file/upload" method="post" enctype="multipart/form-data">
                                                                        @csrf

                                                                            <div class="box-body">

                                                                                <input type="hidden" id="id_contractor" name="id_contractor"
                                                                                    value="{{ $contractor->id_contractor }}">


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
                                                                                                        <input type="file" type="file" multiple name="attachments[]" style="margin-top:10px;">

                                                                                                    </div>

                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="form-group row ">
                                                                                            <label
                                                                                                class="col-xs-4 col-sm-2 col-md-2 control-label text-right">{{ trans('maintenance::maintenance.file_description') }}:</label>
                                                                                                    <div class="col-xs-10 col-sm-10 col-md-10 ">

                                                                                                                <div class="input-group col-xs-10 col-sm-10 col-md-10 col-lg-10">

                                                                                                                    <textarea class="form-control" rows="4" name="file_description" id="file_description" >{{old('file_description')}}</textarea>
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
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="{{ (session('active_tab') == 'contractor_document') ? 'active' : '' }} tab-pane" id="contractor_document">
                                                            <div class="box box-primary">
                                                                <div class="box box-header">
                                                                    <h3>
                                                                        {{ trans('maintenance::contractor.contractor_documents') }}
                                                                    </h3>

                                                                </div>

                                                                <div class="box-body">


                                                                    <table id="contractor_documents_table"
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


                                                                        <tbody id="contractor_documents_body_tbl">


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

  <!-- delete contractor document Modal -->
    <div class="modal fade" id="deleteContractorDocumentModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteContractorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="deleteContractorDocumentModallabel">
                        {{ __('maintenance::contractor.delete_contractor_document_modal') }}</h4>

                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
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
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>

    <script src="{{ asset('resources/iCheck/icheck.min.js') }}"></script>

    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('resources/select2/select2.full.min.js') }}"></script>

    <script src="{{ asset('js/maintenance.js') }}"></script>

    <script>

      $('.select2').select2();

      $(document).ready(function() {


        @if( isset($contractor))

            prepareContractorDocumentsTable();

        @endif


      });
    ////////////////////////////////////////////////
        function prepareContractorDocumentsTable() {

                let contractor_id = $('#id_contractor').val();

                console.log('aaaaaa');

                console.log(contractor_id)

                send( '/maintenance/contractor/attachments/'+contractor_id,  {
            }, 'handleContractorAttachmentBody',[]);

        }
    ///////////////////////////////////////////////////
    function handleContractorAttachmentBody() {

        let code = return_value.code;
        let message = return_value.message;
        let contractor_documents_list = return_value.attachments;

        console.log(contractor_documents_list);
        if (code == "failure") {

            // document.getElementById("charge_mgt_report_text").innerHTML=message;
            $('#contractor_documents_table').DataTable().clear().destroy();


        } else if (contractor_documents_list != null && contractor_documents_list != "undefined") {


            let htmlValue = "";
            Object.keys(contractor_documents_list).forEach(function(k) {

                counter = 1 + parseInt(k);

                var id_contractor_document = contractor_documents_list[k]["id_contractor_document"];
                var document_name = contractor_documents_list[k]["document_name"];
                var document_extention = contractor_documents_list[k]["document_extention"];
                var description = contractor_documents_list[k]["description"];


                var operation =
                    '<a data-toggle="tooltip" title="Delete Contractor Document" class="btn btn-danger allign-btn"  data-original-title="Delete Maintenance Document" onclick="deleteContractorDocument(' +
                    id_contractor_document + ')" >' +
                    '<i class="fa-solid fa-trash" ></i> </a>';
                operation += '<a href="/maintenance/contractor_attachment/' + id_contractor_document +
                    '/download" style="margin-left:10px" class="btn btn-primary allign-btn" target="blank" ><i class="fa-solid fa-download"></i></a>';

                operation += '<a href="/contractor/files/' + document_name +'" style="margin-left:10px" class="btn btn-primary allign-btn" target="blank" title="Show Document" ><i class="fa-solid fa-eye "></i></a>';



                htmlValue += "<tr><td>" + counter + "</td><td>" + document_name + "</td><td>" +
                    document_extention +
                    "</td><td>" + description + "</td><td>" + operation + "</td></tr>";
            });


            $('#contractor_documents_table').DataTable().clear().destroy();
            $('#contractor_documents_table #contractor_documents_body_tbl').html('');
            $('#contractor_documents_table #contractor_documents_body_tbl').append(htmlValue);


            var table = $('#contractor_documents_table').DataTable({
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
        ////////////////////////////////////////////////////
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
    //////////////////////////////////////////////////////
       function submitDeleteContractorDocument(id_contractor_document){
            // let maintenance_id = $('#id_maintenance').val();
            console.log('are');

            send('/maintenance/contractor_document/delete', {

                id_contractor_document: id_contractor_document,
                // maintenance_id: maintenance_id,
            }, 'handleDisableContractorDocument', []);
        }
    /////////////////////////////////////////////////////
    function handleDisableContractorDocument()
    {

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

            prepareContractorDocumentsTable();

        }


    }
    ////////////////////////////////////////////////////

       $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

        });


/////////////////////////////////////////////////////////

    </script>

@endsection
