<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;


use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\SaasClientBusiness;
use App\Models\User;
use App\SLP\Com\LinkGenerator\WikiLinkGenerator;
use App\SLP\Formatter\SystemDateFormats;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Odisse\Maintenance\Models\Contractor;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\Maintenance;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;
use Odisse\Maintenance\Models\MaintenanceJobStaffHistory;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use Odisse\Maintenance\Models\MaintenanceLog;
use App\Models\Role;
use App\SLP\Enum\APIStatusConstants;

use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Sentinel;
use Spatie\LaravelRay\Commands\PublishConfigCommand;
use Odisse\Maintenance\App\SLP\MaintenanceOperation;
use Odisse\Maintenance\Models\ContractorSkillRef;
use Validator;
use Odisse\Maintenance\Models\MaintenanceJobDocument;
use Odisse\Maintenance\Models\Maintainable;
use Odisse\Maintenance\App\SLP\Enum\MaintainableTypeConstants;
use App\Models\LegalCompany;
use App\Models\Room;
use App\Models\Property;
use App\Models\CommsJobQueueSaas;
use App\Models\CommsJobQueueDetailSaas;
use App\SLP\Enum\TemplateTypeConstants;
use Jenssegers\Date\Date;

use Odisse\Maintenance\App\Traits\ReplaceTemplateBody;
use Odisse\Maintenance\App\Traits\MaintenanceTrait;

use PDF;
use File;
use Illuminate\Support\Facades\Storage;

use Odisse\Maintenance\Models\ContractorLocation;
use Odisse\Maintenance\Models\ContractorSkill;
use JWTAuth;

class ApiMaintenanceAppController extends Controller
{

    use MaintenanceOperation;
    use ReplaceTemplateBody;
    use MaintenanceTrait;

    public Function getMaintenancesListforApp(Request $request){

        $user = JWTAuth::user();


        Log::info(" In maintenance package, ApiMaintenanceAppController- getMaintenancesListforApp function " . " try to load maintenances data  ------- by user " . $user->first_name . " " . $user->last_name);

        $select_str="";

        $maintenances = MaintenanceJob::where('maintenance_job_active' , 1)->where('maintenance_job.id_saas_client_business' , $user->id_saas_client_business)->
        join('maintenance_job_category_ref' , 'maintenance_job_category_ref.id_maintenance_job_category_ref' , 'maintenance_job.id_maintenance_job_category')->
        join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
        join('maintenance_job_priority_ref' , 'maintenance_job_priority_ref.id_maintenance_job_priority_ref' , 'maintenance_job.id_maintenance_job_priority')->
        join('users as u1' , 'u1.id' , 'maintenance_job.id_saas_staff_reporter')->
        join('maintenance_job_sla', 'maintenance_job_sla.id_maintenance_job' , 'maintenance_job.id_maintenance_job')->where('maintenance_job_sla_active' , 1)->
        join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1)->
        leftjoin('resident', 'maintenance_job.id_resident_reporter' , 'resident.id_resident');

        if( $request->has('assignee') and $request->assignee != null ){
            $maintenances = $maintenances->
            join('maintenance_job_staff_history', 'maintenance_job.id_maintenance_job' , 'maintenance_job_staff_history.id_maintenance_job')->where('maintenance_job_staff_history_active' , 1)->
            join('contractor_agent', 'maintenance_job_staff_history.id_maintenance_assignee' , 'contractor_agent.id_user')->
            join('contractor', 'contractor_agent.id_contractor' , 'contractor.id_contractor');
            $maintenances = $maintenances->where('contractor.name','ilike', "%".$request->assignee."%");

            $maintenances = $maintenances->select('maintenance_job_staff_history.*' ,'contractor_agent.*' , 'contractor.name AS contractor_name' ,'maintenance_job.*' , 'maintenance_job_category_ref.job_category_name AS job_category_name' , 'maintenance_job_status_ref.*' , 'maintenance_job_priority_ref.*' ,'u1.first_name AS staff_first_name' ,'u1.last_name AS staff_last_name' , 'maintenance_job_sla.*' , 'maintenance_job_sla_ref.*' , 'resident.*' );

        }
        else{
            $maintenances = $maintenances->
            leftjoin('maintenance_job_staff_history', function($join) {
                $join->on('maintenance_job.id_maintenance_job', 'maintenance_job_staff_history.id_maintenance_job')
                ->where('maintenance_job_staff_history.maintenance_job_staff_history_active' , 1);
              })->
            leftjoin('users AS u2', 'maintenance_job_staff_history.id_maintenance_assignee' , 'u2.id')->
            leftjoin('contractor_agent', 'contractor_agent.id_user' , 'u2.id')->
            leftjoin('contractor', 'contractor_agent.id_contractor' , 'contractor.id_contractor');
           
            $maintenances = $maintenances->select('contractor.name AS contractor_name','maintenance_job_staff_history.*' , 'u2.first_name AS assignee_first_name' ,'u2.last_name AS assignee_last_name','u2.email AS assignee_email','maintenance_job.*' , 'maintenance_job_category_ref.job_category_name AS job_category_name' , 'maintenance_job_status_ref.*' , 'maintenance_job_priority_ref.*' ,'u1.first_name AS staff_first_name' ,'u1.last_name AS staff_last_name' , 'maintenance_job_sla.*' , 'maintenance_job_sla_ref.*' , 'resident.*' );

        }



        if( $request->has('business') and $request->business != null )
        $maintenances = $maintenances->where('maintenance_job.id_saas_client_business','=', $request->business);

        if( $request->has('category') and $request->category != null )
        $maintenances = $maintenances->where('maintenance_job_category_ref.id_maintenance_job_category_ref','=', $request->category);

        if( $request->has('priority') and $request->priority != null )
        $maintenances = $maintenances->where('maintenance_job_priority_ref.id_maintenance_job_priority_ref','=', $request->priority);

        if( $request->has('status') and $request->status != null ){

            $maintenances = $maintenances->where('maintenance_job.id_maintenance_job_status','=', $request->status);

        }

        if( $request->has('title') and $request->title != null )
        $maintenances = $maintenances->where('maintenance_job.maintenance_job_title','ilike', "%".$request->title."%");

        if( $request->has('start_date') and $request->start_date != null )
            $maintenances = $maintenances
                ->where('maintenance_job.job_start_date_time','>=', Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $request->start_date)->format('Y-m-d 00:00:00'))
                ->where('maintenance_job.job_start_date_time','<=', Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $request->start_date)->format('Y-m-d 23:59:59'));

        if( $request->has('end_date') and $request->end_date != null )
            $maintenances = $maintenances
                ->where('maintenance_job.job_finish_date_time','>=', Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $request->end_date)->format('Y-m-d 00:00:00'))
                ->where('maintenance_job.job_finish_date_time','<=', Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $request->end_date)->format('Y-m-d 23:59:59'));


        if( $request->has('assignee') and $request->assignee != null ){
            $maintenances = $maintenances->whereNull('staff_end_date_time')->where('is_last_one' , 1);
        }
        else{

            $maintenances = $maintenances->where(function ($query)  {
                $query->where('is_last_one' , 1)
                      ->orWhereNull('is_last_one');
            });
        }

        Log::debug('In maintenance package, ApiMaintenanceAppController- getMaintenancesListforApp function' . $maintenances->toSql());


        $maintenances = $maintenances->get();

        // foreach($maintenances as $maintenance){

        //     $remain_time = $this->calculateSlaRemainTime($user->id_saas_client_business,$maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);


        //     if($remain_time){
        //         $maintenance->remain_time = $remain_time;
        //     }
        //     else{
        //         $maintenance->remain_time = '-';

        //     }

        // }



        return response()->json(
            [
            'code' => ActionStatusConstants::SUCCESS,
            'maintenances'=>$maintenances,

            'message' => trans('maintenance::dashboard.your_maintenances_loaded'),
            ]);


    }

    public function createNewMaintenance( Request $request)
    {

        $user = User::where('email' ,$request->user)->first();
        if($user){


            $validator = $this->validateMaintenance($request);

            if( null != $validator) {
                Log::info("AAAA");
                return response()->json(['message' => $validator->errors()], 422);
            }

            Log::info("BBB");
            $result = $this->createMaintenanceForApp($request);


            Log::info($result['status']);
            if( $result['status'] == 'success')

                return response()->json($result, 200);
            else{
                return response()->json($result, 400);

            }

        }
        else{


            $status = 'error';
            $message = trans('maintenance:maintenance.portal_user_not_exist_in_business');
            // $message = $e->getMessage();

            $result=[
                'status' => $status,
                'message' => $message
            ];

            return response()->json($result, 400);


        }
    }

  


    public function startMaintenanceApp(Request $request)
    {

            Log::info("In maintenance package, ApiMaintenanceAppController- startMaintenanceApi function ");

            try{

                $validator = Validator::make($request->all(), [

                    'start_date_time' => 'required|date_format:'.$this->getDateTimeFormat('date_time_format_validation'),

                ]);

                if ($validator->fails()) {

                    Log::error("In maintenance package, ApiMaintenanceDetailController- startMaintenanceApi function ".": ". $validator->errors());



                    return response()->json(
                        [
                        'code' => 'failure',
                        'message' => $validator->errors(),
                        ]);

                }

                $staff_user = User::where('email' , $request->staff_user)->first();


               

                if($staff_user){
                    // $user = Sentinel::findById($staff_user->id);
                    // Sentinel::login($user);

                    $response = $this->assignJobToUser($request->maintenance , $request->user ,$staff_user->id );
                    $result = $this->startMaintenanceforApp($staff_user->id ,$request->maintenance ,$request->start_date_time);

                    // return response()->json(
                    //     [
                    //     'staff_user' => $staff_user,
                    //     'response' => $result,

                    //     ]);
                    if($result['code']== 'success'){

                        DB::commit();
                        return response()->json($result);




                    }
                    else{
                        DB::rollback();


                        return response()->json(
                            [
                            'code' => $result['code'],
                            'message' => $result['message'],
                            ]);


                    }

                }
                else{

                    //get api user
                    $api_user = User::where('email' , 'api.user@sdr.uk')->first();
                    $response = $this->assignJobToUser($request->maintenance , $request->user ,$api_user->id );

                    $result = $this->startMaintenance($api_user->id ,$request->maintenance ,$request->start_date_time);
                    if($result['code']== 'success'){

                        DB::commit();
                        return response()->json($result);




                    }
                    else{
                        DB::rollback();


                        return response()->json(
                            [
                            'code' => $result['code'],
                            'message' => $result['message'],
                            ]);


                    }

                }





            }
            catch(\Exception $e){

                Log::error("In maintenance package, ApiMaintenanceDetailController- startMaintenanceApi function " . $e->getMessage());

                return response()->json([
                    'code'=> 'failure',
                    'message'=>$e->getMessage(),
                ]);

            }

    }


    public function endMaintenanceApp(Request $request)
    {

        try {


            Log::info("In maintenance package, ApiMaintenanceDetailController - endMaintenanceApi function ");

            $validator = Validator::make($request->all(), [

                'end_date_time' => 'required|date_format:'.$this->getDateTimeFormat('date_time_format_validation'),

            ]);

            if ($validator->fails()) {

                Log::error("In maintenance package, ApiMaintenanceDetailController- endMaintenanceApi function ".": ". $validator->errors());

                return response()->json(
                    [
                    'code' => 'failure',
                    'message' => $validator->errors(),
                    ]);

            }

            Log::info("In maintenance package, ApiMaintenanceDetailController- endMaintenanceApi function - "."after validation");

            $maintenance = MaintenanceJob::find($request->maintenance);

            if(!$maintenance->job_start_date_time){


                Log::error("In maintenance package, ApiMaintenanceDetailController- endMaintenanceApi function ".": ". 'maintenance start date must have start date for this action! ' );



                return response()->json(
                    [
                    'code' => 'failure',
                    'message' => trans('maintenance::dashboard.maintenance_must_have_start_date_for_this_action'),
                    ]);


            }

            if(Carbon::createFromFormat($this->getDateTimeFormat('date_time_format_validation'), $maintenance->job_start_date_time)->gt(Carbon::createFromFormat($this->getDateTimeFormat('date_time_format_validation'), $request->end_date_time))){

                Log::error("In maintenance package, ApiMaintenanceDetailController- endMaintenanceApi function ".": ". 'maintenance start date is after maintenance end date! ');

                return response()->json(
                    [
                    'code' => 'failure',
                    'message' => trans('maintenance::dashboard.start_date_is_after_end_date'),
                    ]);

            }

            $staff_user = User::where('email' , $request->staff_user)->first();

            if($staff_user){
                // $user = Sentinel::findById($staff_user->id);
                // Sentinel::login($user);
                $result = $this->endMaintenanceforApp($staff_user->id ,$request->maintenance ,$request->end_date_time);

            }
            else{
                //get api user
                $api_user = User::where('email' , 'api.user@sdr.uk')->first();
                $result = $this->endMaintenanceforApp($api_user->id ,$request->maintenance ,$request->end_date_time);


            }


            return response()->json(
                [
                  'code' => $result['code'],
                  'message' => $result['message'],
                ]);



        } catch (\Exception $e) {

            Log::error("In maintenance package, ApiMaintenanceDetailController- endMaintenanceApi function " . $e->getMessage() . " " . $e->getLine() );
            $message = $e->getMessage();//trans('maintenance::maintenance_mgt.end_maintenance_was_unsuccessful');
            $status = APIStatusConstants::BAD_REQUEST;

            return response()->json(
                [
                    'code'=>'failure',
                    'status' => $status,
                    'message'   => $message,
                ]
            );


        }


    }


    public function assignMaintenanceToUserApp(Request $request)
    {


        try {


            $validator = Validator::make($request->all(), [

                'business' => 'required|numeric',
                'maintenance' => 'required|numeric',
                'user' => 'required|numeric',

            ]);

            if ($validator->fails()) {

                Log::error("In maintenance package, ApiMaintenanceDetailController- assignMaintenanceToUser function ".": ". $validator->errors());



                return response()->json(
                    [
                    'code' => 'failure',
                    'message' => $validator->errors(),
                    ]);

            }




            Log::info("In maintenance package, ApiMaintenanceDetailController- assignMaintenanceToUser function ");

            DB::beginTransaction();



            $now = Carbon::createFromDate('now');

            $maintenance = MaintenanceJob::find($request->maintenance);

            //check this task assigned to this user already
            $check = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenance->id_maintenance_job )->
                                                where('id_maintenance_assignee' , $request->user)->
                                                whereNull('staff_end_date_time')->
                                                where('maintenance_job_staff_history_active' , 1)->get();
            if(count($check)==0 ){

                //check if this task is assigned to another person
                $check2 = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenance->id_maintenance_job )->
                whereNull('staff_end_date_time')->
                where('maintenance_job_staff_history_active' , 1)->get();

                if(count($check2)>0){
                    foreach($check2  as $assign_staf_obj){
                        $assign_staf_obj->update([
                            'staff_end_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                            'is_last_one'    =>0,
                        ]);
                    }

                }




                $staff_user = User::where('email' , $request->staff_user)->first();

                if($staff_user){

                    $user = Sentinel::findById($staff_user->id);
                    Sentinel::login($user);


                    //insert into maintenance_job_staff table
                    $maintenance_staff = new MaintenanceJobStaffHistory([
                        'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                        'id_maintenance_assignee'    =>  $request->user,
                        'id_maintenance_staff'    =>  $staff_user->id,
                        'staff_assign_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                        'staff_start_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                        'maintenance_job_staff_history_active'  =>  1,

                    ]);
                    $maintenance_staff->save();
                    //insert into maintenance_log table
                    $maintenance_log = new MaintenanceLog([
                        'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                        'id_staff'    =>  $staff_user->id,
                        'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                        'log_note'  =>  trans('maintenance::dashboard.assign_maintenance_to_user'),

                    ]);
                    $maintenance_log->save();


                }
                else{

                    //get api user
                    $api_user = User::where('email' , 'api.user@sdr.uk')->first();

                    //insert into maintenance_job_staff table
                    $maintenance_staff = new MaintenanceJobStaffHistory([
                        'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                        'id_maintenance_assignee'    =>  $request->user,
                        'id_maintenance_staff'    =>  $api_user->id,
                        'staff_assign_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                        'staff_start_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                        'maintenance_job_staff_history_active'  =>  1,

                    ]);
                    $maintenance_staff->save();


                //insert into maintenance_log table
                $maintenance_log = new MaintenanceLog([
                    'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                    'id_staff'    =>  $api_user->id,
                    'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'log_note'  =>  trans('maintenance::dashboard.assign_maintenance_to_user'),

                ]);
                $maintenance_log->save();


                }


                $change_status = $this->changeMaintenanceStatusOnAssignJob($maintenance->id_maintenance_job);
                if(!$change_status){
                    DB::rollback();
                    return response()->json(
                        [
                        'code' => ActionStatusConstants::FAILURE,
                        'message' => trans('maintenance::dashboard.change_maintenance_status_was_not_successful'),
                        ]);
                }



                DB::commit();

                return response()->json(
                    [
                    'status' => APIStatusConstants::OK,
                    'code' => ActionStatusConstants::SUCCESS,
                    'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_successful'),
                    ]);

            }else{

                DB::rollback();

                return response()->json(
                    [
                    'status' => 400,
                    'code' => ActionStatusConstants::FAILURE,
                    'message' => trans('maintenance::dashboard.maintenance_assigned_to_this_user_already'),
                    ]);


            }


        } catch (\Exception $e) {


            Log::error("In maintenance package, ApiMaintenanceDetailController- assignMaintenanceToUser function " . $e->getMessage());
            DB::rollback();


            return response()->json(
                [
                  'status' => 400,
                  'code' => 'failure',
                  'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_not_successful'),
                ]);


        }


    }

    public function sendMaintenanceEmailApp(Request $request){


        // dd($request->all());
        try {

            $user = JWTAuth::user();

           
            $maintenance = MaintenanceJob::findOrFail($request->id_maintenance_job);

            DB::beginTransaction();

            Log::info("In maintenance package, MaintenanceDashboardController- sendEmailToContractor function " . " try to send email to contractor:" . " ------- by user " . $user->first_name . " " . $user->last_name);

         

            $contractor_email = ContractorAgent::join('users','users.id','contractor_agent.id_user')->where('contractor_agent.id_contractor',$request->id_contractor)->select('users.email')->first();

            $final_email_text = "";

            // html text of email
            $email_html_text = $request->html_maintenance_temp;

            $final_email_text = $final_email_text . $email_html_text;
          
            // notes of email content
            $note_list = "<h2>Notes List</h2>";

            $email_template_body = $request->html_maintenance_temp;

            if($request->notes){

                $notes = $request->notes;

                $notes = MaintenanceLog::whereIn('id_maintenance_log',$notes)->get();

                    foreach($notes as $note){
                        $note_list = $note_list."<p>".$note->log_note."</p></hr>";
                    }
            }
            $final_email_text = $final_email_text . $note_list;

            // attached_files of email content

            $attached_files_list = "<h2>Attached Files List</h2>";

            $message_attachment_uri = "";

            if($request->job_attachments){
                $attached_files = $request->job_attachments;

                $attached_files = MaintenanceJobDocument::whereIn('id_maintenance_job_document',$attached_files)->get();

                $maintenance_file_path = config('maintenances.maintenance_file_path');

                $path = $maintenance_file_path . 'uploaded_files/' ;

                $i=1;
                $message_attachment_uri = '';

                $files = [];

                    foreach($attached_files as $attached_file){


                        // $attached_files_list = $attached_files_list."<p>".$attached_file->document_name."</p></hr>";

                        $files[] = public_path($path. $attached_file->document_name);

                        // $message_attachment_uri = $message_attachment_uri.'"file'.$i.'":"'.$path. $attached_file->document_name.'",';

                        // $i++;

                        }
                        $final_message_attachment_uri = json_encode($files);


            }else{

                $final_message_attachment_uri = null;

            }
          
            // return response()->json(
            //     [
            //         'code'   => 'injjjjjjjjjjamm',
            //         'message'   => $final_email_text,
            //         'a'=>$request->commencement_date,
            //         'b'=>$request->complete_date,
            //         'c'=>$maintenance->complete_date,
            //         'd'=>$maintenance->commencement_date,


            //     ]
            // );

            if(($maintenance->commencement_date != $request->commencement_date)or ($maintenance->complete_date != $request->complete_date)){


            
                $maintenance->update([
                    'commencement_date' => $request->commencement_date,
                    'complete_date' => $request->complete_date,

                ]);

            }
            $final_email_text = $final_email_text . $attached_files_list;

            // return response()->json(
            //     [
            //         'code'   => 'injjjjjjjjjjamm',
            //         'message'   => $final_email_text,

            //     ]
            // );

            // additional comment of email content
            $comment = $request->contractor_job_attachment_text;

            $final_email_text = $final_email_text . $comment;
          
            $final_email_text = $this->replaceMaintenanceTemplateVariablesforApp($final_email_text,$request->id_maintenance_job,$request->id_contractor,$request->commencement_date,$request->complete_date);


            $comms_job_queue_detail = new CommsJobQueueDetailSaas();
            //    $comms_job_queue_detail->id_comms_job_queue_saas = $comms_job_queue->id_comms_job_queue_saas;
            $comms_job_queue_detail->id_comms_message_type_ref = TemplateTypeConstants::Email;
            $comms_job_queue_detail->message_subject = "Contactor's notification";
            $comms_job_queue_detail->job_create_date_time = Carbon::now();
            $comms_job_queue_detail->message_to = $contractor_email->email;
            $comms_job_queue_detail->message_body = $final_email_text;
            $comms_job_queue_detail->message_attachment_uri = $final_message_attachment_uri;

            $comms_job_queue_detail->save();




            //add final comment to maintenance_log table

            if($comment){
                $now = Carbon::createFromDate('now');
                $maintenance_log = new MaintenanceLog([
                    'id_maintenance_job'    =>  $request->id_maintenance_job,
                    'id_staff'    =>  $user->id,
                    'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'log_note'  =>  trans('maintenance::dashboard.comment_by_user_when_send_email').' : '.$comment ,

                ]);
                $maintenance_log->save();
            }


            $maintainable = Maintainable::where('id_maintenance_job',$request->id_maintenance_job)->where('maintainable_active',1)->first();

            if($maintainable->maintenable_type == MaintainableTypeConstants::Room){

                $legal_company = Room::where('id_room',$maintainable->maintenable_id)->where('room_active',1)->
                join('property', 'property.id_property' , 'room.id_property')->where('property.property_active' , 1)->
                join('legal_company','legal_company.id_legal_company','property.id_legal_company')
                ->select('legal_company.short_name')->get();

            }elseif($maintainable->maintenable_type == MaintainableTypeConstants::Property){

                $legal_company = Property::where('id_property',$maintainable->maintenable_id)->where('property_active',1)->
                join('legal_company','legal_company.id_legal_company','property.id_legal_company')->select('legal_company.short_name')->get();

            }

         
             //get all document of pdf file of maintenance email in database that are for this maintenance job
             $maintenance_document = MaintenanceJobDocument::where('id_maintenance_job',$request->id_maintenance_job)->where('maintenance_job_document_active',1)
             ->where('is_uploaded_file',0)
             ->orderBy('id_maintenance_job_document','desc')->first();

            //  dd($maintenance_document);

             if( $maintenance_document ){
                           
                $part_3digit_document_name = substr($maintenance_document->document_name,11,3);

                $part_2digit_document_name = substr($maintenance_document->document_name,15,2);

                $part_2digit_document_name = intval($part_2digit_document_name)+1;
                if ($part_2digit_document_name < 10){

                    $part_2digit_document_name = sprintf("%02d", $part_2digit_document_name);

                }

                $file_name = $legal_company[0]->short_name.'-'.date('ymd').'-'.$part_3digit_document_name.'-'.$part_2digit_document_name;


            }else{

                //get all document of pdf file of maintenance email in database that are not for this maintenance job
               $maintenance_document = MaintenanceJobDocument::where('maintenance_job_document_active',1)->where('is_uploaded_file',0)
                ->orderBy('id_maintenance_job_document','desc')->first();


                if( $maintenance_document ){
               
                    $part_3digit_document_name = substr($maintenance_document->document_name,11,3);
                    
                   
                    $part_3digit_document_name = intval($part_3digit_document_name)+1;

                    $part_2digit_document_name = '01';

                    $file_name = $legal_company[0]->short_name.'-'.date('ymd').'-'.$part_3digit_document_name.'-'.$part_2digit_document_name;


                }else{

                    //file name if no pdf file for maintenance email content has been created yet
                    $part_3digit_document_name = 100;
                    $part_2digit_document_name = '01';
                   
                    $file_name = $legal_company[0]->short_name.'-'.date('ymd').'-'.$part_3digit_document_name.'-'.$part_2digit_document_name;


                }


            }

            $now = Carbon::createFromDate('now');


            $maintenance_job = MaintenanceJob::findOrFail($request['id_maintenance_job']);

            if($request['notes'] == null){
                $selected_notes = 'N/A';
            }else{
                
                $notes = MaintenanceLog::whereIn('id_maintenance_log',$request['notes'])->get();

                $selected_notes = '<html>';
                foreach ($notes as $note){

                    $selected_notes =  $selected_notes .$note->log_note .'.<br>';
                }

                $selected_notes =  $selected_notes .'</html>';


            }

            if($request['job_attachments'] == null){
                      
                $selected_document = 'N/A';
            }else{
               
                $selected_document = '<html>';

                $contractor_job_attachments = MaintenanceJobDocument::whereIn('id_maintenance_job_document',$request['job_attachments'])->get();
           
           
                foreach ($contractor_job_attachments as $contractor_job_attachment){

                    if(($contractor_job_attachment->document_extention == 'png')||($contractor_job_attachment->document_extention == 'jpg')||($contractor_job_attachment->document_extention == 'jpeg')){
                       
                        $selected_document = $selected_document ."<img style='width:100px;' src='".$contractor_job_attachment->document_address.$contractor_job_attachment->document_name."'"."\>";

                    }else{
                        $selected_document = $selected_document .$contractor_job_attachment->document_name.' .<br>';
                    }


                }
                $selected_document =  $selected_document .'</html>';


            }

            $additional_comment = '<html>'.$request['contractor_job_attachment_text'].'</html>';

            $complete_date = $request['complete_date'];

            $commencement_date = $request['commencement_date'];

           

            $final_email_text = $this->replaceMaintenanceTemplateVariablesforApp($request->html_maintenance_temp,$request->id_maintenance_job,$request->id_contractor,$commencement_date,$complete_date);

          
            $final_email_text = '<html>'.$final_email_text.'</html>';

         
            $data = [
                'id_maintenance_job'=> $request['id_maintenance_job'],
                'id_contractor'=> $request['id_contractor'],
                'template_message_body'=> $final_email_text,
                'additional_comment'=> $additional_comment,
                'commencement_date'=> $request['commencement_date'],
                'complete_date'=> $request['complete_date'],
                'maintenance'=> $maintenance_job,
                'selected_notes'=> $selected_notes,
                'selected_document'=> $selected_document,

            ];
          


            $base_path = config('pdf.tempDir') ;
          
          
            $pdf = PDF::loadView('maintenance::download_maintenance_email', $data);
           
       
            // put the file in determined destination path
            $pdf->save($base_path.'/mpdf/' . $file_name.'.pdf');

            $file_path = config('file_storage.maintenance_email_file_path');


        

            //add this pdf file to maintenance job document table
            $maintenance_job_document = new MaintenanceJobDocument();
            $maintenance_job_document->id_maintenance_job = $request->id_maintenance_job;
            $maintenance_job_document->document_name = $file_name.'.pdf';
            $maintenance_job_document->document_address = $file_path;
            $maintenance_job_document->document_extention = 'pdf';
            $maintenance_job_document->is_uploaded_file = 0;
            $maintenance_job_document->maintenance_job_document_active = '1';
            $maintenance_job_document->save();



            //record creating this pdf file in maintenance log table
            $maintenance_log = new MaintenanceLog();
            $maintenance_log->id_maintenance_job = $request->id_maintenance_job;
            $maintenance_log->id_staff = $user->id;
            $maintenance_log->log_date_time = $now->format(SystemDateFormats::getDateTimeFormat());
            $maintenance_log->log_note = trans('maintenance::dashboard.system_created_a_pdf_file_automatically');
            $maintenance_log->save();



            DB::commit();

            $status = APIStatusConstants::OK;
            $message = "Email Will Be Sent In One Minute";


            return response()->json(
                [
                    'status' => $status,
                    'message'   => $message,
                ]
            );

            // return redirect()->route('emailTemplateCreation',$request->id_maintenance_job)->with('success', 'Email Will Be Sent In One Minute');


            } catch (\Exception $e) {


                Log::error('In maintenance package, MaintenanceDashboardController- sendEmailToContractor function ' . $e->getMessage());
                DB::rollback();

                return response()->json(
                    [
                        // 'status' => $status,
                        'message'   => 'Email Will not be Sent',
                        'message'   => $e->getMessage(),

                    ]
                );

                // return redirect()->route('emailTemplateCreation',$request->id_maintenance_job)->with('failure', 'Email Will not be Sent ');



            }


    }


    public function deleteMaintenanceApp(Request $request)
    {

        try {

       
            Log::info("In maintenance package - in ApiMaintenanceAppController - deleteMaintenanceApp function");


        $maintenance = MaintenanceJob::find($request->maintenance);

        $maintenance->update([
            'maintenance_job_active' => 0,
        ]);


            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.delete_maintenance_was_successful');


        } catch (\Exception $e) {

            Log::error("In maintenance package, ApiMaintenanceDetailController- deleteMaintenance function " . $e->getMessage());
            $message = trans('maintenance::maintenance_mgt.delete_maintenance_was_unsuccessful');
            $status = APIStatusConstants::BAD_REQUEST;
            $$maintenances=null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
            ]
        );
    }






}

