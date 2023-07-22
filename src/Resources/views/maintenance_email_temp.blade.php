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
                                <div class="box-body">

                                        <div class="col-md-11">

                                            <h4>{{ __('maintenance::contractor.maintenance_template') }}</h4>

                                            <input type="hidden" name="html_maintenance_temp" id="html_maintenance_temp" value="{{$template_message_body}}" >
                                            
                                            <div id="maintenance_template"></div>


                                            <hr>
                                            <h4>{{ __('maintenance::contractor.select_notes_to_attach') }}</h4>


                                            @foreach($notes as $note)
                                                <input type="checkbox" name="notes[]" value="{{$note->id_maintenance_log}}" id="{{$note->id_maintenance_log}}" >
                                                <label for="{{$note->id_maintenance_job_document}}">{{$note->log_note}}-{{$note->log_date_time}}</label></br>
                                            @endforeach

                                              <hr>

                                            <h4>{{ __('maintenance::contractor.select_fields_to_attach') }}</h4>

                                            <input type="hidden" name="id_contractor" id="hidden_id_contractor"  value="{{$contractor_job_attachments[0]->id_contractor}}">
                                            <input type="hidden" name="id_maintenance_job" id="hidden_id_maintenance_job" value="{{$contractor_job_attachments[0]->id_maintenance_job}}" >


                                            @foreach($contractor_job_attachments as $contractor_job_attachment)
                                                <input type="checkbox" name="job_attachments[]" value="{{$contractor_job_attachment->id_maintenance_job_document}}" id="{{$contractor_job_attachment->id_maintenance_job_document}}" >
                                                <label for="{{$contractor_job_attachment->id_maintenance_job_document}}">{{$contractor_job_attachment->document_name}}</label></br>
                                            @endforeach

                                            <hr>

                                            <div class="form-group row">
                                                <label class="col-xs-2 col-sm-2 col-md-2 control-label text-left" for="exampleFormControlTextarea1">{{ __('maintenance::contractor.additional_comments') }} :</label>
                                                
                                                <div class="input-group col-xs-8 col-sm-8 col-md-8">

                                                    <textarea class="form-control" id="contractor_job_attachment_text" name="contractor_job_attachment_text" rows="4" cols="2"></textarea>
                                            
                                                </div>
                                             </div>
                                        </div>
                                

                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary"
                                        style="float:right;min-width:55px"  >{{ __('maintenance::contractor.send_email') }}</button>
                                        <button type="button" class="btn btn-primary"
                                        style="float:right;min-width:55px;margin-right:5px" onclick="previewEmailContent()" >{{ __('general.preview') }}</button>
                                    <a href="/maintenance/dashboard" ><button style="float:right;margin-right:5px" type="button" id="close_btn" class="btn btn-warning"
                                        >{{ __('maintenance::contractor.back') }}</button></a>
                                

                                </div>
                            </div>

                        </form>




                    </div>
                </div>

            

            </div>

        </div>

              <!-- preview email content of contractor  -->
              <div class="modal fade" id="previewEmailContentModal" tabindex="-1" role="dialog"
                                                aria-labelledby="previewEmailContentModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">

                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                                                    class="sr-only">{{ __('general.close') }}</span></button>

                                                            <h3 class="modal-title" id="previewEmailContentModallabel">
                                                                {{ __('maintenance::contractor.preview_email_content_modal') }}</h3>

                                                                

                                                        </div>

                                                            
                                                            <div class="modal-body">
                                                                <div class="alert alert-danger alert-dismissible" id="preview_email_content_err_msg_box"
                                                                        style="display: none;">
                                                                        <div id="preview_email_content_ajx_err_msg"></div>
                                                                    </div>
                                                                    <div class="alert alert-success alert-dismissible" id="preview_email_content_success_msg_box"
                                                                        style="display: none;">
                                                                        <div id="preview_email_content_ajx_success_msg"></div>
                                                                    </div>


                                        
                                                                        <div class="box">
                                                                            <div class="box-body">

                                                                                    <div class="col-md-11">

                                                                                        <h3>{{ __('maintenance::contractor.preview_email_content') }}</h3>
                                                                                        <div id="maintenance_template_body"></div>


                                                                                        <h4>{{ __('maintenance::contractor.additional_comment') }}</h4>
                                                                                        <div id="additional_comment" name="additional_comment" ></div>


                                                                                        <h4>{{ __('maintenance::contractor.email_attachments_list') }}</h4>
                                                                                        <div id="email_attachments_list" name="email_attachments_list" ></div>


                                                                                        <h4>{{ __('maintenance::contractor.email_notes_list') }}</h4>
                                                                                        <div id="email_notes_list" name="email_notes_list" ></div>


                                                                                       

                                                                                    </div>

                                                                                    <div id="email_content" name="email_content">

                                                                                    </div>
                                                                            

                                                                            </div>
                                                                            <div class="box-footer">
                                                                                
                                                                                <button style="float:right;margin-right:5px" type="button" id="close_btn" class="btn btn-warning"
                                                                                    data-dismiss="modal">{{ __('general.close') }}</button>
                                                                            

                                                                            </div>
                                                                        </div>

                                                               
                                                            
                                                            </div>
                                                            <div class="modal-footer">
                                                               
                                                            </div>

                                                    </div>
                                                </div>
            </div>
    </section>



@endsection

@section('script')
   
    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>

 <script>

        $(document).ready(function () {


            let html_maintenance_temp = $( '#html_maintenance_temp' ).val();

            $('#maintenance_template').html('');
            $('#maintenance_template').append(html_maintenance_temp);
        });
        //////////////////////////////////////////////////////////
        function previewEmailContent(){

            let id_contractor = $('#hidden_id_contractor').val();
            let id_maintenance_job = $('#hidden_id_maintenance_job').val();

            let notes_checkboxes= document.querySelectorAll('input[name="notes[]"]:checked');
            let notes_output= [];
            notes_checkboxes.forEach((note_checkbox) => {
                notes_output.push(note_checkbox.value);
            });

            console.log(notes_output);

            let files_checkboxes= document.querySelectorAll('input[name="job_attachments[]"]:checked');
            let job_attachments_output= [];
            files_checkboxes.forEach((file_checkbox) => {
                job_attachments_output.push(file_checkbox.value);
            });


            let email_html_text = $('#html_maintenance_temp').val();

            let additional_comment = document.getElementById("contractor_job_attachment_text").value;

            // var email_html_text = document.getElementById('maintenance_template').getAttribute('value');

            console.log(email_html_text);

            send('/maintenance/contractor_email/preview', {
                id_maintenance_job: id_maintenance_job,
                id_contractor: id_contractor,
                notes_output:notes_output,
                job_attachments_output:job_attachments_output,
                email_html_text:email_html_text,
                additional_comment:additional_comment,

            }, 'handlePreviewEmailContent', []);


        }
        ///////////////////////////////////////////////////////////
        function handlePreviewEmailContent(){

            let message = return_value.message;
            let res = return_value.code;
            let maintenance_template_body = return_value.maintenance_template_body;
            let maintenance_job_attachments = return_value.maintenance_job_attachments;
            let notes = return_value.notes;
            let additional_comment = return_value.additional_comment;

            console.log(additional_comment);

            if(maintenance_job_attachments != null && maintenance_job_attachments !="undefined"){
              
                var htmlDocumentValue = "";
                Object.keys(maintenance_job_attachments).forEach(function(k){

                    var counter = 1+parseInt(k);


                    var maintenance_doc_name = maintenance_job_attachments[k]["document_name"];
                    

                    htmlDocumentValue= htmlDocumentValue +"<tr><td>"+maintenance_doc_name+"</td></tr>";


                });

                $('#email_attachments_list').html('');
                $('#email_attachments_list').append(htmlDocumentValue);
           
            }

            if(notes != null && notes !="undefined"){
              
              var htmlNoteValue = "";
              Object.keys(notes).forEach(function(k){

                  var counter = 1+parseInt(k);


                  var maintenance_log_note = notes[k]["log_note"];
                  

                  htmlNoteValue= htmlNoteValue +"<tr><td>"+maintenance_log_note+"</td></tr>";


              });

              $('#email_notes_list ').html('');
                $('#email_notes_list ').append(htmlNoteValue);
           
         
            }

            $('#maintenance_template_body ').html('');
            $('#maintenance_template_body ').append(maintenance_template_body);

            $('#additional_comment ').html('');
            $('#additional_comment ').append(additional_comment);
           
            $('#previewEmailContentModal').modal('show');



        }
 </script>





@endsection
