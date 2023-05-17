@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title').' '.__('maintenance::contractor.contractor_mgt'))


@section('body_class', 'login-page')

@section('css')

    <link rel="stylesheet" href="{{ asset('resources/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.css') }}"/>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/bootstrap-timepicker/css/timepicker.less') }}"/>
    <link rel="stylesheet" href="{{ asset('resources/iCheck/all.css')}}" />

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

        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="{{ ( null == session('active_tab') or session('active_tab') == 'contractor_mgt') ? "active" : "" }}" id="contractor_mgt_tab"><a href="#contractor_mgt" data-toggle="tab">{{trans('maintenance::contractor.contractor_mgt')}}</a></li>

                    </ul>
                    <div class="tab-content">
                       <!-- start of tab report -->
                        <div class="{{ ( null == session('active_tab') or session('active_tab') == 'contractor_mgt') ? "active" : "" }} tab-pane" id="contractor_mgt">

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box box-primary">
                                        <div class="box-header">
                                            <h4 class="text-danger" id="room_type_text"> </h4>
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body table-responsive no-padding" style="text-align: left;">




                                            <div class="row"><a href="/maintenance/contractor"><button type="button"  style="float: right; margin: 10px 20px; min-width:135px;" id="create_contractor_btn" class="btn btn-primary" >{{trans('maintenance::contractor.create_new_contractor')}}</button></a></div>


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
                                                    {{-- @php $counter=0; @endphp

                                                    @foreach($contractors as $contractor)
                                                    <tr>
                                                        <td>{{++$counter}}</td>
                                                        <td>{{$contractor->name}}</td>
                                                        <td>{{$contractor->short_name}}</td>
                                                        <td>{{$contractor->vat_number}}</td>
                                                        <td>{{$contractor->tel_number1}}</td>
                                                        <td>{{$contractor->tel_number2}}</td>
                                                        <td>{{$contractor->address_line1}}</td>
                                                        <td>
                                                            <a href="/maintenance/contractor/{{$contractor->id_contractor}}"><button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="{{trans('maintenance::contractor.edit_contractor')}}" data-toggle="modal" data-target="#disableStatusModal">
                                                                <i class="fa-solid fa-edit"></i>
                                                            </button></a>
                                                            <button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="{{trans('maintenance::contractor.login_settings')}}" data-toggle="modal" data-target="#showLoginSettingsModal">
                                                                <i class="fa-solid fa-cogs"></i>
                                                            </button>
                                                            <button style="margin-right: 1px;" type="button" class="btn btn-danger allign-btn" title="{{trans('maintenance::contractor.delete_contractor')}}" data-toggle="modal" data-target="#deleteContractorModal">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach --}}



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
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->




    </section>
    <!-- /.content -->




    <!-- Modal -->
    <div class="modal fade" id="deleteContractorModal" tabindex="-1" role="dialog" aria-labelledby="deleteContractorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="deleteContractorModalLabel">{{trans('maintenance::contractor.delete_contractor')}}</h4>
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
                        data-dismiss="modal">{{trans('maintenance::contractor.cancel')}}</button>
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

                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="showLoginSettingsModalLabel">{{trans('maintenance::contractor.login_agent_settings')}}</h4>
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
                        data-dismiss="modal">{{trans('maintenance::contractor.cancel')}}</button>
                    <button type="button" class="btn btn-primary"
                        id="save_contractor" onclick="changeContractorLoginSettings()">{{trans('maintenance::contractor.save')}}</button>
                </div>


            </div>
        </div>
    </div>



@endsection



@section('script')
    <script src="{{ asset('resources/bootstrap-timepicker/js/moment.min.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('resources/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>


    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script src="{{ asset('resources/modalLoading/modalLoading.min.js') }}"></script>


    <script src="{{ asset('resources/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('resources/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script src="{{ asset('resources/iCheck/icheck.min.js') }}"></script>

    <script>







        $(document).ready(function () {

            loadAllContractors();

        });


        function loadAllContractors(){

            var spinHandle = loadingOverlay.activate();

            send( '/maintenance/contractors',  {
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


                    var id_contractor = contractor_list[k]["id_contractor"];
                    var name = contractor_list[k]["name"];
                    var short_name = contractor_list[k]["short_name"];
                    var vat_number = contractor_list[k]["vat_number"];
                    var tel_number1 = contractor_list[k]["tel_number1"];
                    var tel_number2 = contractor_list[k]["tel_number2"];
                    var address_line1 = contractor_list[k]["address_line1"];

                    var operation = '<a href="/maintenance/contractor/' + id_contractor + '" target="_blank" data-toggle="tooltip" title="Edit Contractor" data-original-title="EDit Contractor">' +
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" id="edit' + counter + '" >' +
                        '<i class="fa-solid fa-edit" aria-hidden="true"></i></button>' +
                        '</a>' +
                        '<button style="margin-right: 1px;" type="button" class="btn btn-primary allign-btn" title="Login Settings" onclick="showLoginSettingsModal('+id_contractor+')">'+
                        '<i class="fa-solid fa-cogs"></i>'+
                        '</button>'+

                        '<button style="margin-right: 1px;" type="button" class="btn btn-danger allign-btn" title="Delete Contractor" onclick="showDeleteContractorModal('+id_contractor+')">'+
                        '<i class="fa-solid fa-trash"></i>'+
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

                    { "sClass": "leftSide", "aTargets": [ 0 ,1,2,3,4,5,6,7] }
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



            loadingOverlay.cancelAll();

        }

        //////////////////////////////////////////////////////
        function showDeleteContractorModal(id_contractor){

            $('#deleted_contractor').val(id_contractor);
            $("#err_msg_box_delete_contractor").css('display' , 'none');
            $("#suc_msg_box_delete_contractor").css('display' , 'none');
            $('#deleteContractorModal').modal('show');

        }
        ///////////////////////////////////////////////////////
        function deleteContractor(){
            var spinHandle = loadingOverlay.activate();

            let deleted_contractor = $( '#deleted_contractor' ).val();


            send( '/maintenance/contractor/delete/'+deleted_contractor,  {
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


            loadingOverlay.cancelAll();

        }
        /////////////////////////////////////////////////////////
        function showLoginSettingsModal(id_contractor){

            $('#password').val("");
            $('#confirm_password').val("");
            $('#email').val("");

            var spinHandle = loadingOverlay.activate();



            send( '/maintenance/contractor/email/'+id_contractor,  {
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
            loadingOverlay.cancelAll();

        }
        /////////////////////////////////////////////////////////
        function changeContractorLoginSettings(){
            var spinHandle = loadingOverlay.activate();
            $("#err_msg_box_login_setting").css('display' , 'none');

            let changed_contractor = $('#changed_contractor' ).val();
            let email = $('#email' ).val();
            let password = $('#password' ).val();
            let password_confirmation = $('#confirm_password' ).val();


            send( '/maintenance/contractor/login_settings/change',  {
                contractor:changed_contractor,
                email:email,
                password:password,
                password_confirmation:password_confirmation,
            }, 'handleChangeContractorLoginSettings', []);
        }
        ///////////////////////////////////////////////////////////
        function handleChangeContractorLoginSettings(){
            loadingOverlay.cancelAll();

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
            else{
                $("#ajx_suc_msg_login_setting").html(message);
                $("#suc_msg_box_login_setting").css('display' , 'block');
                setTimeout(function() {$('#showLoginSettingsModal').modal('hide');}, 3000);

            }





        }

    </script>




@endsection
