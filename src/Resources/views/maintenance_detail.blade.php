@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title').' '.__('maintenance.maintenance'))


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

@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
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

    <!-- Content Header (Page header) -->
    <section class="content-header">
       
    </section>

    <!-- Main content -->
    <section class="content">


        <div class="nav-tabs-custom">
                @if (isset($maintenance))

                <ul class="nav nav-tabs">

                    <li class="{{ (null == session('active_tab') or session('active_tab') == 'maintenanceDetail') ? 'active' : '' }}">
                        <a id="maintenanceDetail_tab" href="#maintenanceDetail" data-toggle="tab">{{ __('maintenance::maintenance.maintenance_detail') }}</a>
                    </li>
                    <li class="{{ session('active_tab') == 'maintenance_timeline' ? 'active' : '' }}">
                        <a id="maintenance_timeline_tab" href="#maintenance_timeline" data-toggle="tab">{{ __('maintenance::maintenance.maintenance_timeline') }}</a>
                    </li>
                </ul>
                @endif

             <div class="tab-content row">
                <div class="{{ (null == session('active_tab') or session('active_tab') == 'maintenanceDetail') ? 'active' : '' }} tab-pane" id="maintenanceDetail">

                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box box-header">
                                    <h3>
                                        {{trans('maintenance::maintenance.maintenance_detail')}}
                                    </h3>
                        
                            </div>

                            <div class="box-body">
                                
                                <!-- maintenance title-->

                                    <div class="col-sm-9 col-md-9 col-lg-9">
                                        <h3>{{$maintenance->maintenance_job_title}}</h3>
                                    </div>
                                
                                    <!-- maintenance status-->
                                    <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
                                            <div  class="form-group row ">
                                                        <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::maintenance.status')}}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                        <select name="maintenance_status" id="maintenance_status" class="form-control select ">
                                                                <option value="">{{trans('maintenance::maintenance.select_status')}}</option>
                                                                @foreach ($maintenance_status as $maintenance_sts)
                                                                <option value="{{ $maintenance_sts->id_maintenance_job_status_ref }}"
                                                                    @if(($maintenance_sts->id_maintenance_job_status_ref == $maintenance->id_maintenance_job_status )||(old('maintenance_status') == $maintenance->id_maintenance_job_status)) {{ 'selected' }} @endif>
                                                                    {{ $maintenance_sts->job_status_name }}
                                                                </option>
                                                                @endforeach

                                                        </select>
                                                </div>
                                            </div>
                                    </div>


                                    <!-- comment-->

                                    <div class="form-group col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                            <label class="col-xs-6 col-sm-6 col-md-6 control-label text-left"><h3>{{trans('maintenance::maintenance.add_comment')}}:</h3></label>
                                            <div class="col-xs-10 col-sm-10 col-md-10">
                                                <div class="input-group col-xs-11 col-sm-11 col-md-11 col-lg-11">

                                                <textarea class="form-control" rows="4" name="coment" id="coment" column="40" ></textarea>
                                                </div>

                                            </div>
                                    </div>


                                    <!-- people-->
                                    <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
                                            <!-- assignee-->

                                            <div  class="form-group row ">
                                                <h4><strong>{{trans('maintenance::maintenance.people')}}</strong></h4>

                                                        <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::maintenance.assignee')}}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                        <select name="maintenance_assignee" id="maintenance_assignee" class="form-control select ">
                                                                <option value="">{{trans('maintenance::maintenance.select_assignee')}}</option>
                                                                @foreach ($contactors as $contractor)
                                                                <option value="{{ $contractor->id_contractor }}"
                                                                    @if(($contractor->id_contractor == $maintenance_job_detail->id_staff )||(old('maintenance_assignee') == $maintenance_job_detail->id_staff)) {{ 'selected' }} @endif>
                                                                    {{ $contractor->name }}
                                                                </option>
                                                                @endforeach

                                                        </select>
                                                </div>
                                            </div>
                                            <!-- reporter-->

                                            <div  class="form-group row ">

                                                        <label class="col-xs-4 col-sm-4 col-md-4 control-label text-right">{{trans('maintenance::maintenance.reporter')}}:</label>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                        <select name="maintenance_reporter" id="maintenance_reporter" class="form-control select ">
                                                                <option value="">{{trans('maintenance::maintenance.select_maintenance_reporter')}}</option>
                                                                @foreach ($reporters as $reporter)
                                                                <option value="{{ $reporter->id }}"
                                                                    @if(($reporter->id == $maintenance->id_saas_staff_reporter )||(old('maintenance_reporter') == $maintenance->id_saas_staff_reporter)) {{ 'selected' }} @endif>
                                                                    {{ $reporter->first_name }}  {{ $reporter->last_name }}
                                                                </option>
                                                                @endforeach

                                                        </select>
                                                </div>
                                            </div>

                                    </div>


                                    <!-- attachment-->

                                    <div class="form-group ">
                                            <label class="col-xs-6 col-sm-6 col-md-6 control-label text-left"><h3>{{trans('maintenance::maintenance.attachments')}}:</h3></label>
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="input-group col-xs-7 col-sm-7 col-md-7 col-lg-7">

                                                </div>

                                            </div>
                                    </div>


                                    <!-- saas client budiness-->
                                    <div class="form-group row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::maintenance.saas_client_business')}}:</label>

                                            <div class="col-md-5 col-sm-5 col-lg-5 col-xs-5">
                                                    <select name="saas_client_business" id="saas_client_business" class="form-control select ">
                                                        <option value="">{{__('maintenance::maintenance.select_saas_client_business')}}</option>
                                                        @if(isset($saas_client_businesses))
                                                        @foreach ($saas_client_businesses as $saas_client_business)
                                                        <option value="{{ $saas_client_business->id_saas_client_business }}"
                                                        @if(($saas_client_business->id_saas_client_business == $maintenance->id_saas_client_business )||(old('saas_client_business') == $maintenance->id_saas_client_business)) {{ 'selected' }} @endif>
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
                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::maintenance.category')}}:</label>
                                            <div class="col-sm-5 col-md-5 col-lg-5">


                                                <select name="maintenance_category" id="maintenance_category" class="form-control select ">
                                                        <option value="">{{trans('maintenance::maintenance.select_category')}}</option>
                                                        @if(isset($maintenance_categories))
                                                        @foreach ($maintenance_categories as $maintenance_category)
                                                        <option value="{{ $maintenance_category->id_maintenance_job_category_ref }}"
                                                            @if(($maintenance_category->id_maintenance_job_category_ref == $maintenance->id_maintenance_job_category )||(old('maintenance_category') == $maintenance->id_maintenance_job_category)) {{ 'selected' }} @endif>
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
                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::maintenance.locations')}}:</label>
                                        <div class="col-sm-5 col-md-5 col-lg-5">


                                            <select name="locations[]" id="locations" class="form-control select2" placeholder="{{__('maintenance.select_locations')}}" multiple="multiple" onchange="loadResidentReporters()">
                                                        <!-- <option value="">{{__('maintenance.select_locations')}}</option> -->
                                                        @if(isset($locations))
                                                        @foreach ($locations as $location)
                                                        <option value="{{ $location->id }}"
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
                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-right">{{trans('maintenance::maintenance.priority')}}:</label>
                                                <div class="col-sm-5 col-md-5 col-lg-5">


                                                    <select name="priority" id="priority" class="form-control select ">
                                                        <option value="">{{trans('maintenance::maintenance.select_priority')}}</option>
                                                        @if(isset($priorities))
                                                        @foreach ($priorities as $priority)
                                                        <option value="{{ $priority->id_maintenance_job_priority_ref }}"
                                                            @if(($priority->id_maintenance_job_priority_ref == $priority->id_maintenance_job_priority_ref )||(old('priority') == $priority->id_maintenance_job_priority_ref)) {{ 'selected' }} @endif>
                                                            {{ $priority->priority_name }}
                                                        </option>
                                                        @endforeach
                                                        @else
                                                            
                                                        @endif


                                                    </select>


                                                </div>
                                        </div>






                            </div>
                        </div>
                    </div>
                </div>

                <div class="{{ (null == session('active_tab') or session('active_tab') == 'maintenance_timeline') ? 'active' : '' }} tab-pane" id="maintenance_timeline">
                <div class="box box-primary">
                            <div class="box box-header">
                                <h3>
                                    {{trans('maintenance::maintenance.maintenance_timeline')}}
                                </h3>
                        
                            </div>

                            <div class="box-body">


                                @foreach($maintenance_timelines as $activity)
                                    <ul class="timeline">

                                            <!-- timeline time label -->
                                            <li class="time-label">
                                                <span class="bg-blue">
                                                {{ $activity->log_date_time }}                                                </span>
                                            </li>
                                            <!-- /.timeline-label -->

                                            <!-- timeline item -->
                                            <li>
                                                <!-- timeline icon -->
                                                <i class="fa fa-user bg-blue"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                                                    <h3 class="timeline-header">
                                                    {{ $activity->header }}
                                                   </h3>

                                                    <div class="timeline-body">
                                                    {{ $activity->log_note }}                                                   </div>

                                                    <div class="timeline-footer">
                                                    </div>
                                                </div>
                                            </li>
                                            <!-- END timeline item -->

                                            ...

                                    </ul>

                                @endforeach


                                         
                            </div>
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

      $(document).ready(function () {

          //Date picker
            $('#maintenance_date').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePickerSeconds: true,
                //timePicker12Hour: false,
                showDropdowns: true,
                // minYear: 2000,
                format: window._date_time_format ,

                timePickerIncrement: 5,
                locale:{
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

       });
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
    send( '/maintenance/get/resident_reporter',  {
        'locations':locations,
    }, 'handleLoadResidentReporter', []);
}
       /////////////////////////////////////////////////////////////
function handleLoadResidentReporter(){
    
    console.log('tt');

    let res = return_value.code;
    let message = return_value.message;
    let residents = return_value.residents;
   
    console.log(return_value.residents);

    if(res == "failure") {

    }
    else if(residents != null && residents !="undefined"){

        // $('#template_sub_category').prop('disabled', false);


        let htmlValue = '<option value="">Select Resident Reporter</option>';
        Object.keys(residents).forEach(function(k){

            var counter = 1+parseInt(k);


            var resident_id = residents[k]["id_resident"];
            var resident_name = residents[k]["resident_title"] + " "+residents[k]["resident_first_name"]+" "+residents[k]["resident_surname"];

            htmlValue = htmlValue + "<option value='"+resident_id+"'>"+resident_name+"</option>";


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

                    method:'POST',

                    url: "/maintenance/upload/file",

                    data:formData,

                    contentType: false,

                    processData: false,

                    success:function(response){

                            let description = response.description;
                            let text1 = "+";
                            let result = text1.concat(description);
                            // console.log(result);

                            let doc_name = response.fileName;
                            let doc = doc_name+"/";
                            let seperated_doc = text1.concat(doc);
                            let doc_result = description.concat(seperated_doc);

                            console.log(doc_result);

                            document.getElementById('attach_file_btn').style.display= 'none';
                            $('#attach_file_btn').attr('type','hidden') ;

                            let p =  document.getElementById('selected_file');
                            let nnn =  document.getElementById('nahayat');


                            let previous_name = p.getAttribute('name');

                            let previous_doc= nnn.value;
                            console.log(previous_doc);


                            // console.log(previous_name);

                            let update_name = result.concat(previous_name);


                            htmlVal = response.fileName +' ';
                            $('#selected_file').append( htmlVal);
                            $('#selected_file').attr('name',update_name) ;
                            let bb =  + response.fileName+'/'+description+",";

                            // console.log(bb);
                            let update_doc = doc_result.concat(previous_doc);

                            $('#nahayat').val(update_doc);

                            // let aa = "<option>"+response.fileName+"/"+description+"<option>";

                            let aa = "<option value="+bb+" selected>"+response.fileName+"/"+description+"<option>";


                            let cc = "<input value="+bb+" >"+response.fileName+"/"+description+"<option>";

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

                    error: function(response){

                        alert(response.responseJSON.message);


                    }

        });

});

    /////////////////////////////////////////////////////////////
    $('#add_parent_job').on('click', function(e) {

        console.log('are');

        let id_maintenance_job = $('#parent_job_modal').val();

        send( '/maintenance/find/maintenance_title',  {id_maintenance_job: id_maintenance_job}, 
        'handleShowMaintenanceJobTitle', []);
    

})
//////////////////////////////////////////////////////
function handleShowMaintenanceJobTitle () {

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
