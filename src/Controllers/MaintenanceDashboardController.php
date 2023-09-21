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

use PDF;
use File;
use Illuminate\Support\Facades\Storage;

use Odisse\Maintenance\Models\ContractorLocation;
use Odisse\Maintenance\Models\ContractorSkill;


class MaintenanceDashboardController extends Controller
{

    use MaintenanceOperation;

    use ReplaceTemplateBody;



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDashboardPage(Request $request){


        $user = Sentinel::getUser();

        Log::info(" in Maintenance package MaintenanceDshboardController- showDashboardPage function " . " try to go to maintenance dashboard page  ------- by user " . $user->first_name . " " . $user->last_name);


        $businesses = SaasClientBusiness::where('saas_client_business_active' , 1)->get();
        $categories = MaintenanceJobCategoryRef::where('maintenance_job_category_ref_active' , 1)->get();
        $priorities = MaintenanceJobPriorityRef::where('maintenance_job_priority_ref_active' , 1)->get();
        $statuses = MaintenanceJobStatusRef::where('maintenance_job_status_ref_active' , 1)->get();

        $maintenance_users = User::where('users_active' , 1)->where('is_deleted' , 0)->
        join('role_users','role_users.user_id','users.id')->where('role_users_active' , 1)->
        join('roles','roles.id','role_users.role_id')->where('roles.name','maintenance')->get();

        $contractor_agents = [];

        $contractors = Contractor::where('contractor_active' , 1)->get();

        $skills = ContractorSkillRef::where('contractor_skill_ref_active' , 1)->get();

        $wiki_link = WikiLinkGenerator::GetWikiLinkOfPage('maintenance_dashboard');


        return view('maintenance::maintenance_dashboard',
                    [


                        'businesses'=>$businesses,
                        'categories'=>$categories,
                        'priorities'=>$priorities,
                        'statuses'=>$statuses,
                        'contractors'=>$contractors,
                        'maintenance_users'=>$maintenance_users,
                        'contractor_agents'=>$contractor_agents,
                        'skills'=>$skills,
                        'wiki_link'=>$wiki_link,
                        // 'message'=>$message

                    ]
                );

    }
    /////////////////////////////////////////////////////////////////////////////

    public Function ajaxLoadMaintenances(Request $request){


        $user = Sentinel::getUser();

        Log::info(" In maintenance package, MaintenanceDashboardController- ajaxLoadMaintenances function " . " try to load maintenances data  ------- by user " . $user->first_name . " " . $user->last_name);

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
            // join('contractor_agent', 'maintenance_job_staff_history.id_maintenance_assignee' , 'contractor_agent.id_user')->
            // join('contractor', 'contractor_agent.id_contractor' , 'contractor.id_contractor');
            // $maintenances = $maintenances->where('contractor.name','ilike', "%".$request->assignee."%");

            //$maintenances = $maintenances->whereNull('maintenance_job_staff_history.staff_end_date_time');

            $maintenances = $maintenances->select('contractor.name AS contractor_name','maintenance_job_staff_history.*' , 'u2.first_name AS assignee_first_name' ,'u2.last_name AS assignee_last_name','u2.email AS assignee_email','maintenance_job.*' , 'maintenance_job_category_ref.job_category_name AS job_category_name' , 'maintenance_job_status_ref.*' , 'maintenance_job_priority_ref.*' ,'u1.first_name AS staff_first_name' ,'u1.last_name AS staff_last_name' , 'maintenance_job_sla.*' , 'maintenance_job_sla_ref.*' , 'resident.*' );

        }


        //dd($maintenances->toSql());


        if( $request->has('business') and $request->business != null )
        $maintenances = $maintenances->where('maintenance_job.id_saas_client_business','=', $request->business);

        if( $request->has('category') and $request->category != null )
        $maintenances = $maintenances->where('maintenance_job_category_ref.id_maintenance_job_category_ref','=', $request->category);

        if( $request->has('priority') and $request->priority != null )
        $maintenances = $maintenances->where('maintenance_job_priority_ref.id_maintenance_job_priority_ref','=', $request->priority);

        if( $request->has('status') and $request->status != null ){
            //dd($request->status);
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
            //$maintenances = $maintenances->where('is_last_one' , 1)->orWhereNull('is_last_one');

            $maintenances = $maintenances->where(function ($query)  {
                $query->where('is_last_one' , 1)
                      ->orWhereNull('is_last_one');
            });
        }

        Log::debug('In maintenance package, MaintenanceDashboardController- ajaxLoadMaintenances function' . $maintenances->toSql());



        //dd($maintenances->toSql());
        $maintenances = $maintenances->get();

        foreach($maintenances as $maintenance){
            //$remain_time = null;

            $remain_time = $this->calculateSlaRemainTime($user->id_saas_client_business,$maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);


            if($remain_time){
                $maintenance->remain_time = $remain_time;
            }
            else{
                $maintenance->remain_time = '-';

            }

        }









        return response()->json(
            [
            'code' => ActionStatusConstants::SUCCESS,
            'maintenances'=>$maintenances,

            'message' => trans('maintenance::dashboard.your_maintenances_loaded'),
            ]);


    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxDeleteMaintenance(Request $request , $id_maintenance){


        try {

        $user = Sentinel::getUser();

        DB::beginTransaction();


        Log::info("In maintenance package, MaintenanceDashboardController- ajaxDeleteMaintenance function " . " try to delete specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

        $maintenance = MaintenanceJob::find($id_maintenance);
        $maintenance->update([
            'maintenance_job_active' => 0,
        ]);


        $now = Carbon::createFromDate('now');


        //insert into maintenance_job_staff table
        $maintenance_log = new MaintenanceLog([
            'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
            'id_staff'    =>  $user->id,
            'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
            'log_note'  =>  trans('maintenance::dashboard.delete_maintenance_by_user'),

        ]);

        DB::commit();

        return response()->json(
            [
              'code' => ActionStatusConstants::SUCCESS,
              'message' => trans('maintenance::dashboard.your_selected_maintenance_deleted'),
            ]);


        } catch (\Exception $e) {


            Log::error('In maintenance package, MaintenanceDashboardController- ajaxDeleteMaintenance function' . $e->getMessage());
            DB::rollback();


            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => trans('maintenance::dashboard.delete_maintenance_was_not_successful'),
                ]);


        }





    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxLoadUserAgents(Request $request){



        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceDashboardController- ajaxLoadUserAgents function " . " try to load User & Agents  ------- by user " . $user->first_name . " " . $user->last_name);

        try{

            $contractor = null;
            $contractor_skills = null;
            $coverage_areas = null;
            $business_contractor = $request->business_contractor;
            $result=[];
            if($business_contractor && $business_contractor[0] == "B"){

                //return business maintenance users
                $users = User::where('users_active' , 1)->where('is_deleted' , 0)->
                join('role_users','role_users.user_id','users.id')->where('role_users_active' , 1)->
                join('roles','roles.id','role_users.role_id')->where('roles.name','Maintenance')->get();
                $result = $users;
                $user_type="user";
            }
            elseif($business_contractor && $business_contractor[0] == "C"){

                //return contractor agents
                $agents = Contractor::where('contractor.id_contractor' , substr($business_contractor, 1))->
                join('contractor_agent','contractor_agent.id_contractor','contractor.id_contractor')->
                join('users','users.id','contractor_agent.id_user')->get();
                $result = $agents;
                $user_type = "agent";
                $contractor = Contractor::find(substr($business_contractor, 1));

                $contractor_skills = ContractorSkill::where('id_contractor' , $contractor->id_contractor)->where('contractor_skill_active' , 1)->
                                     join('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref')->get();


                $coverage_areas = ContractorLocation::where('id_contractor' ,$contractor->id_contractor )->where('contractor_location_active' , 1)->
                                  join('contractor_location_ref' , 'contractor_location.id_contractor_location_ref' , 'contractor_location_ref.id_contractor_location_ref')->get();



            }



        return response()->json(
            [
              'code' => ActionStatusConstants::SUCCESS,
              'message' => trans('maintenance::contractor.load_users_agents_was_successful'),
              'result' => $result,
              'user_type' => $user_type,
              'contractor' => $contractor,
              'contractor_skills' => $contractor_skills,
              'coverage_areas' => $coverage_areas,
            ]);

        }
        catch(\Exception $e){


            Log::error('In maintenance package, MaintenanceDashboardController- ajaxLoadUserAgents function' . $e->getMessage());

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'result'=>[],
                  'contractor'=>null,
                  'message' => trans('maintenance::dashboard.load_users_agents_was_not_successful'),
                ]);


        }


    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxAssignMaintenanceToUser(Request $request){



        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceDashboardController- ajaxAssignMaintenanceToUser function " . " try to assign maintenance to user  ------- by user " . $user->first_name . " " . $user->last_name);

        $validator = Validator::make($request->all(), [

            'maintenance' => 'required|numeric',
            'user' => 'required|numeric',

        ]);

        if ($validator->fails()) {

            Log::error("In maintenance package, MaintenanceDashboardController- ajaxAssignMaintenanceToUser function ".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);



            return response()->json(
                [
                'code' => ActionStatusConstants::FAILURE,
                'message' => $validator->errors(),
                ]);

        }


        $response = $this->assignJobToUser($request->maintenance , $request->user ,$user->id );
        return response()->json($response);

    }
    ///////////////////////////////////////////////////////////////////////////
    public Function ajaxStartMaintenance(Request $request , $id_maintenance){

        $user = Sentinel::getUser();

        $validator = Validator::make($request->all(), [

            'start_date_time' => 'required|date_format:'.SystemDateFormats::getDateTimeFormat(),
            'user' => 'required|numeric',


        ]);

        if ($validator->fails()) {

            Log::error("In maintenance package, MaintenanceDashboardController- ajaxStartMaintenance function ".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);



            return response()->json(
                [
                'code' => ActionStatusConstants::FAILURE,
                'message' => $validator->errors(),
                ]);

        }


        Log::info("In maintenance package, MaintenanceDashboardController- ajaxStartMaintenance function " . " try to start specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);


        try{
            DB::beginTransaction();
            $response = $this->assignJobToUser($id_maintenance , $request->user ,$user->id );
            $result = $this->startMaintenance($user->id ,$id_maintenance ,$request->start_date_time);
            if($result['code']== 'success'){

                DB::commit();
                return response()->json($response);




            }
            else{
                DB::rollback();


                return response()->json(
                    [
                    'code' => $result['code'],
                    'message' => $result['message'],
                    ]);


            }

        } catch (\Exception $e) {


            Log::error("In maintenance package, MaintenanceDashboardController- ajaxStartMaintenance function " . $e->getMessage());
            DB::rollback();


            return
                [
                'code' => 'failure',
                'message' => trans('maintenance::dashboard.start_maintenance_was_not_successful'),
                ];


        }


        return response()->json(
            [
              'code' => $result['code'],
              'message' => $result['message'],
            ]);



    }


    ///////////////////////////////////////////////////////////////////////////
    public Function ajaxEndMaintenance(Request $request , $id_maintenance){

        $user = Sentinel::getUser();

        $validator = Validator::make($request->all(), [

            'end_date_time' => 'required|date_format:'.SystemDateFormats::getDateTimeFormat(),

        ]);

        if ($validator->fails()) {

            Log::error("In maintenance package, MaintenanceDashboardController- ajaxEndMaintenance function ".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);



            return response()->json(
                [
                'code' => ActionStatusConstants::FAILURE,
                'message' => $validator->errors(),
                ]);

        }

        $maintenance = MaintenanceJob::find($id_maintenance);

        if(!$maintenance->job_start_date_time){


            Log::error("In maintenance package, MaintenanceDashboardController- ajaxEndMaintenance function ".": ". 'maintenance start date must have start date for this action! ' ." by user ".$user->first_name . " " . $user->last_name);



            return response()->json(
                [
                'code' => 'failure',
                'message' => trans('maintenance::dashboard.maintenance_must_have_start_date_for_this_action'),
                ]);


        }

        if(Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $maintenance->job_start_date_time)->gt(Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $request->end_date_time))){

            Log::error("In maintenance package, MaintenanceDashboardController- ajaxEndMaintenance function ".": ". 'maintenance start date is after maintenance end date! ' ." by user ".$user->first_name . " " . $user->last_name);



            return response()->json(
                [
                'code' => 'failure',
                'message' => trans('maintenance::dashboard.start_date_is_after_end_date'),
                ]);

        }


        Log::info("In maintenance package, MaintenanceDashboardController- ajaxEndMaintenance function " . " try to end specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);


        $now = Carbon::createFromDate('now');


        $result = $this->endMaintenance($user->id ,$id_maintenance ,$request->end_date_time);


        return response()->json(
            [
              'code' => $result['code'],
              'message' => $result['message'],
            ]);



    }

    ///////////////////////////////////////////////////////////////////////////
    public function ajaxPrepareStatusChartData(Request $request){


        $user = Sentinel::getUser();
        Log::info("In maintenance package, MaintenanceDashboardController- ajaxPrepareStatusChartData function " . " try to prepare data for widgets  ------- by user " . $user->first_name . " " . $user->last_name);

        $statuses = MaintenanceJobStatusRef::where('maintenance_job_status_ref_active' , 1)->get();
        $result = [];
        foreach($statuses as $status){
            $maintenances = MaintenanceJob::where('maintenance_job_active' , 1)->where('id_maintenance_job_status' , $status->id_maintenance_job_status_ref)->get();
            $maintenance_count = count($maintenances);
            $result[$status->job_status_code]= $maintenance_count;
        }




        return response()->json(
            [
            'code' => ActionStatusConstants::SUCCESS,
            'message' => trans('maintenance::dashboard.chart_data_prepared'),
            'result' => $result,
            ]);



    }


    ///////////////////////////////////////////////////////////////////////////
    public function ajaxPrepareSlaChartData(Request $request){

        $user = Sentinel::getUser();


        $sla_count = ['Expired'=>0, 'Near to Expire'=>0 ,'Not Expired'=>0];


        $user = Sentinel::getUser();
        Log::info("In maintenance package, MaintenanceDashboardController- ajaxPrepareSlaChartData function " . " try to prepare data for widgets  ------- by user " . $user->first_name . " " . $user->last_name);

        $maintenaces = MaintenanceJob::where('maintenance_job_active' , 1)->
        join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
        join('maintenance_job_sla', 'maintenance_job_sla.id_maintenance_job' , 'maintenance_job.id_maintenance_job')->where('maintenance_job_sla_active' , 1)->
        join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1)->
        where('maintenance_job_status_ref.job_status_code' , '!=' , 'CLOS')->get();

        foreach($maintenaces as $maintenance){
            $remain_time = $this->calculateSlaRemainTime($user->id_saas_client_business , $maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);
            if($remain_time){
                $date1 =Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() , $remain_time);
                $date2 = Carbon::createFromDate('now');
                $date3 = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() ,$maintenance->job_report_date_time);
                if($date2->gt($date1)){
                    $sla_count['Expired']++;
                }
                else{
                    $sla_show_percent_passed = false;
                    $sla_show_percent_passed = $this->isPassedSlaShowPercent($user->id_saas_client_business,$maintenance->expected_target_minutes, $date3 , $date2);
                    if($sla_show_percent_passed){
                        $sla_count['Near to Expire']++;
                    }
                    else{
                        $sla_count['Not Expired']++;
                    }
                }

            }
        }





        return response()->json(
            [
            'code' => ActionStatusConstants::SUCCESS,
            'message' => trans('maintenance::dashboard.chart_data_prepared'),
            'result' => $sla_count,
            ]);



    }


    public function getContractorJobDocuments($id_maintenance_job){

        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceDashboardController- getContractorJobDocuments function " . " try to get  documents of a contractor job:" . " ------- by user " . $user->first_name . " " . $user->last_name);

        try{
                //get contractor of maintenance job

                $contractor = MaintenanceJob::join('maintenance_job_staff_history' , 'maintenance_job.id_maintenance_job' , 'maintenance_job_staff_history.id_maintenance_job')->whereNull('maintenance_job_staff_history.staff_end_date_time')->
                join('contractor_agent' , 'contractor_agent.id_user' , 'maintenance_job_staff_history.id_maintenance_assignee')->
                join('contractor' , 'contractor.id_contractor' , 'contractor_agent.id_contractor')->
                where('maintenance_job.id_maintenance_job',$id_maintenance_job)->get();

                $contractor = $contractor->toArray();

                if($contractor) {


                        $contractor = $contractor[0];
                        $id_contractor = $contractor['id_contractor'];

                            //get contractor job documents
                        $contractor_job_documents = MaintenanceJobDocument::join('maintenance_job','maintenance_job.id_maintenance_job','maintenance_job_document.id_maintenance_job')->
                        join('maintenance_job_staff_history' , 'maintenance_job.id_maintenance_job' , 'maintenance_job_staff_history.id_maintenance_job')->whereNull('maintenance_job_staff_history.staff_end_date_time')->
                        join('contractor_agent' , 'contractor_agent.id_user' , 'maintenance_job_staff_history.id_maintenance_assignee')->
                        join('contractor' , 'contractor.id_contractor' , 'contractor_agent.id_contractor')->
                        join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
                        join('maintenance_job_priority_ref' , 'maintenance_job_priority_ref.id_maintenance_job_priority_ref' , 'maintenance_job.id_maintenance_job_priority')->
                        where('maintenance_job_document.maintenance_job_document_active',1)->
                        where('contractor.id_contractor' , $id_contractor)->get();


                            return response()->json(
                            [
                            'code' => ActionStatusConstants::SUCCESS,
                            'message' => trans('maintenance::contractor.contractor_job_documents_returned'),
                            'contractor_job_documents' =>$contractor_job_documents,
                            ]);
                }
                    else {
                        return response()->json(
                            [
                            'code' => ActionStatusConstants::FAILURE,
                            'message' => trans('maintenance::contractor.this_job_has_no_contractor'),
                            ]);
                    }
            }catch(\Exception $e){


                Log::error('In maintenance package, MaintenanceDashboardController- getContractorJobDocuments function : '.$e->getMessage());

                return response()->json(
                    [
                    'code' => ActionStatusConstants::FAILURE,
                    'message' => $e->getMessage(),
                    ]);


            }


    }


    public function createEmailTemplateForContractor($id_maintenance_job){

                   $user = Sentinel::getUser();

        try {
                    Log::info("In maintenance package, MaintenanceDashboardController- createEmailTemplateForContractor function " . " try to go page for create template  email :" . " ------- by user " . $user->first_name . " " . $user->last_name);

                    $wiki_link = WikiLinkGenerator::GetWikiLinkOfPage('maintenance_dashboard');

                    $now = \Illuminate\Support\Carbon::create('now');



                    $maintenance = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $id_maintenance_job)
                    ->where('maintenance_job.id_saas_client_business' , $user->id_saas_client_business)->where('maintenance_job.maintenance_job_active',1)
                    ->join('maintenance_job_sla', 'maintenance_job_sla.id_maintenance_job' , 'maintenance_job.id_maintenance_job')->where('maintenance_job_sla_active' , 1)->
                    join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1)
                     ->first();


                        if($maintenance->commencement_date == null){

                            $maintenance->commencement_date = $now->format(SystemDateFormats::getDateFormat());

                        }

                        if($maintenance->complete_date == null){

                            $remain_time = $this->calculateSlaRemainTime($user->id_saas_client_business,$maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);


                            $maintenance->complete_date = Carbon::parse($remain_time)->format(SystemDateFormats::getDateFormat());
                        }



                    //get contractor of maintenance job
                    $contractor = MaintenanceJob::join('maintenance_job_staff_history' , 'maintenance_job.id_maintenance_job' , 'maintenance_job_staff_history.id_maintenance_job')->whereNull('maintenance_job_staff_history.staff_end_date_time')->
                    join('contractor_agent' , 'contractor_agent.id_user' , 'maintenance_job_staff_history.id_maintenance_assignee')->
                    join('contractor' , 'contractor.id_contractor' , 'contractor_agent.id_contractor')->
                    where('maintenance_job.id_maintenance_job',$id_maintenance_job)->first();

                    if($contractor) {


                        //$contractor = $contractor[0];
                        $id_contractor = $contractor->id_contractor;

                        //get maintenance job documents
                        $contractor_job_documents = MaintenanceJobDocument::join('maintenance_job','maintenance_job.id_maintenance_job','maintenance_job_document.id_maintenance_job')->
                        // join('maintenance_job_staff_history' , 'maintenance_job.id_maintenance_job' , 'maintenance_job_staff_history.id_maintenance_job')->whereNull('maintenance_job_staff_history.staff_end_date_time')->
                        // join('contractor_agent' , 'contractor_agent.id_user' , 'maintenance_job_staff_history.id_maintenance_assignee')->
                        // join('contractor' , 'contractor.id_contractor' , 'contractor_agent.id_contractor')->
                        // join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
                        // join('maintenance_job_priority_ref' , 'maintenance_job_priority_ref.id_maintenance_job_priority_ref' , 'maintenance_job.id_maintenance_job_priority')->
                        where('maintenance_job_document.maintenance_job_document_active',1)->
                        where('maintenance_job.maintenance_job_active',1)->
                        where('maintenance_job.id_maintenance_job' , $id_maintenance_job)->get();

                        //get all notes of a job document
                        $notes =  MaintenanceLog::where('id_maintenance_job',$id_maintenance_job)->get();

                        $template = Template::join('template_category','template_category.id_template_category','template.id_template_category')
                        ->leftjoin('template_sub_category','template_sub_category.id_template_sub_category','template.id_template_sub_category')
                        ->where(function($template)  {
                                $template->where('template_category.template_category_name','maintenance')
                                    ->orWhere('template_sub_category.template_sub_category_name','maintenance sent to contractor');
                        })->orderBy('id_template', 'desc')
                        ->first();

                        $template_message_body = $this->replaceMaintenanceTemplateVariables($template->template_message_body,$id_maintenance_job,$id_contractor,$maintenance->commencement_date,$maintenance->complete_date);

                        $maintenance_location = Maintainable::where('id_maintenance_job',$id_maintenance_job)->first();

                        // dd($maintenance_location);
                        if($maintenance_location->maintenable_type == MaintainableTypeConstants::Room){

                            $legal_company = LegalCompany::join('property','property.id_legal_company','legal_company.id_legal_company')
                            ->join('room','room.id_property','property.id_property')
                            ->where('room.id_room',$maintenance_location->maintenable_id)
                            ->first();

                        }elseif($maintenance_location->maintenable_type == MaintainableTypeConstants::Property){

                            $legal_company = LegalCompany::join('property','property.id_legal_company','legal_company.id_legal_company')
                            ->where('property.id_property',$maintenance_location->maintenable_id)
                            ->first();

                        }

                        if(!$template){

                            return redirect()->route('maintenance_dashboard')->with('error', trans('maintenance::maintenance.you_have_to_first_define_template'));;


                        }


                        return view('maintenance::maintenance_email_temp',
                        [


                            'contractor' =>$contractor,
                            'contractor_job_attachments' =>$contractor_job_documents,
                            'template_message_body' =>$template_message_body,
                            'notes' =>$notes,
                            'code' => ActionStatusConstants::SUCCESS,
                            'wiki_link'=>$wiki_link,
                            'legal_company'=>$legal_company,
                            'maintenance'=>$maintenance,

                        ]);



                    }
                    else {

                        return redirect()->route('maintenance_dashboard')->with('error', trans('maintenance::contractor.this_job_has_no_contractor'));;

                    }
            }
            catch(\Exception $e){

                Log::error('In maintenance package, MaintenanceDashboardController- createEmailTemplateForContractor' . $e->getMessage());

                return redirect()->route('maintenance_dashboard')->with('error', $e->getMessage());

            }
    }




    public function previewEmailContent(Request $request){


        $user = Sentinel::getUser();

        Log::info("in MaintenanceDashboardController- previewEmailContent function " . " try to get all details of maintenance :" . " ------- by user " . $user->first_name . " " . $user->last_name);

        try{

            $id_maintenance_job = $request['id_maintenance_job'];

            $id_contractor = $request['id_contractor'];

            $template_body = $request['email_html_text'];

            $commencement_date = $request['commencement_date'];

            $complete_date = $request['complete_date'];

            $complete_date = Carbon::createFromFormat('Y-m-d' , $request['complete_date'] )->format(SystemDateFormats::getDateFormat());

            $commencement_date = Carbon::createFromFormat('Y-m-d' , $request['commencement_date'] )->format(SystemDateFormats::getDateFormat());

            $maintenance_template_body = $this->replaceMaintenanceTemplateVariables($template_body,$id_maintenance_job,$id_contractor,$commencement_date,$complete_date);

            // dd($maintenance_template_body);
            $notes = [];
            $maintenance_job_attachments = [];


            if($request->notes_output){
            $notes = MaintenanceLog::whereIn('id_maintenance_log',$request->notes_output)->get();
            }


            if($request->job_attachments_output){
            $maintenance_job_attachments = MaintenanceJobDocument::whereIn('id_maintenance_job_document',$request->job_attachments_output)->get();
            }

            return response()->json(
                [
                'code' => ActionStatusConstants::SUCCESS,
                'message' => trans('maintenance::contractor.preview_of_email_content_returned'),
                'maintenance_job_attachments'=> $maintenance_job_attachments,
                'notes'=> $notes,
                'maintenance_template_body'=> $maintenance_template_body,
                'additional_comment'=>$request->additional_comment


                ]);
        }
        catch(\Exception $e){


            Log::error('In maintenance package, MaintenanceDashboardController- previewEmailContent function'. $e->getMessage());

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'result'=>[],
                  'contractor'=>null,
                  'message' =>trans('maintenance::maintenance.maintenance_info_did_not_returned'), //$e->getMessage(),
                ]);


        }

    }

    public function sendEmailToContractor(Request $request){


        // dd($request->all());
        try {
            $user = Sentinel::getUser();

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

            if(($maintenance->commencement_date != $request->commencement_date)or ($maintenance->complete_date != $request->complete_date)){

                $maintenance->update([
                    'commencement_date' => $request->commencement_date,
                    'complete_date' => $request->complete_date,

                ]);

            }
            $final_email_text = $final_email_text . $attached_files_list;

            // additional comment of email content
            $comment = $request->contractor_job_attachment_text;

            $final_email_text = $final_email_text . $comment;

            $final_email_text = $this->replaceMaintenanceTemplateVariables($final_email_text,$request->id_maintenance_job,$request->id_contractor,$request->commencement_date,$request->complete_date);


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

            $final_email_text = $this->replaceMaintenanceTemplateVariables($request->html_maintenance_temp,$request->id_maintenance_job,$request->id_contractor,$commencement_date,$complete_date);

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
            $config = [
                'format'                => 'A4',            
            ];
            $pdf = PDF::loadView('maintenance::download_maintenance_email', $data, [] , $config);


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

            return redirect()->route('emailTemplateCreation',$request->id_maintenance_job)->with('success', 'Email Will Be Sent In One Minute');


            } catch (\Exception $e) {


                Log::error('In maintenance package, MaintenanceDashboardController- sendEmailToContractor function ' . $e->getMessage());
                DB::rollback();


                return redirect()->route('emailTemplateCreation',$request->id_maintenance_job)->with('failure', 'Email Will not be Sent ');



            }


    }

    public function previewEmailContentForDownload(Request $request){


        $user = Sentinel::getUser();

        Log::info("In maintenance package - in MaintenanceDashboardController- previewEmailContentForDownload function " . " try to get all details of maintenance :" . " ------- by user " . $user->first_name . " " . $user->last_name);

        try{

            $html_text = '';
            $id_maintenance_job = $request['id_maintenance_job'];

            $id_contractor = $request['id_contractor'];

            $template_body = $request['email_html_text'];

            $maintenance_template_body = $this->replaceMaintenanceTemplateVariables($template_body,$id_maintenance_job,$id_contractor,$commencement_date,$complete_date);

            $html_text = $html_text . $maintenance_template_body;
            $notes = [];
            $maintenance_job_attachments = [];


            if($request->notes_output){

                $html_text = $html_text.'<h3> Select Files To be attached</h3>';

                $notes = MaintenanceLog::whereIn('id_maintenance_log',$request->notes_output)->get();

             foreach ($notes as $note){
                $html_text = $html_text .'<p>'.$note->log_note.'</p>';
             }
            }


            if($request->job_attachments_output){

                $html_text = $html_text.'<h3> Select Notes To be attached</h3>';

                $maintenance_job_attachments = MaintenanceJobDocument::whereIn('id_maintenance_job_document',$request->job_attachments_output)->get();

                foreach ($maintenance_job_attachments as $maintenance_job_attachment){
                    $html_text = $html_text .'<p>'.$maintenance_job_attachment->document_name.'</p>';
                 }
            }

            $html_text = $html_text.'<h3> Additional Comments</h3>';
            $html_text = $html_text.$request->additional_comment;

            // $html_text =  str_replace('<p>', '', $html_text);
            // $html_text =  str_replace('</p>', '', $html_text);

            $date = new Date();

            $data = [
                'date' => $date->format('Y/m/d'),
                'content' => $html_text,
            ];


            //make directory to save email pdf file in this directory

            $directory = config('Maintenance.maintenance_email_path') ;
                if (!\File::exists($directory)) {
                    \File::makeDirectory($directory,0755,true);
                }


                $config = [
                    'format'                => 'A4',            
                ];

            $pdf = PDF::loadView('pdf.letter_template', $data, [], $config);

            $base_path = config('pdf.tempDir') ;

            $file_name = 'emailcontent_'.date('Y-m-d H:i:s');

            $pdf->save($base_path.'/mpdf/' . $file_name.'.pdf');

            $file_path = "maintenance_email/$file_name.pdf";
            Storage::put($file_path, $pdf->output());
            $path = Storage::path($file_path);


            return response()->json(
                [
                'code' => ActionStatusConstants::SUCCESS,
                'message' => trans('maintenance::contractor.preview_of_email_content_returned'),
                'maintenance_job_attachments'=> $maintenance_job_attachments,
                'notes'=> $notes,
                'maintenance_template_body'=> $maintenance_template_body,
                'additional_comment'=>$request->additional_comment,
                'html_text'=> $html_text,


                ]);
        }
        catch(\Exception $e){


            Log::error('In maintenance package, MaintenanceDashboardController- previewEmailContentForDownload function - '. $e->getMessage());

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'result'=>[],
                  'contractor'=>null,
                  'message' =>trans('maintenance::maintenance.maintenance_info_did_not_returned'), //$e->getMessage(),
                ]);


        }

    }


    public function downloadEmailContent(Request $request){

        // dd($request->all());
      try {


                    $user = Sentinel::getUser();

                    // DB::beginTransaction();


                    //change format of complete_date
                    $complete_date = $request['complete_date'];


                    Log::info("In maintenance package, MaintenanceDashboardController- downloadEmailContent function " . " try to download email content as pdf:" . " ------- by user " . $user->first_name . " " . $user->last_name);


                    $data = $request->all();

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

 
                        // dd($selected_document);

                        //change format of commencement_date
                        $commencement_date = $request['commencement_date'];

 

                            $additional_comment = '<html>'.$request['contractor_job_attachment_text'].'</html>';

                     


                    $final_email_text = $this->replaceMaintenanceTemplateVariables($request->html_maintenance_temp,$request->id_maintenance_job,$request->id_contractor,$commencement_date,$complete_date);
                    

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
                    $config = [
                        'format'                => 'A4',            
                    ];

                    $pdf = PDF::loadView('maintenance::download_maintenance_email', $data, [], $config);

                    $maintainable = Maintainable::where('id_maintenance_job',$maintenance_job->id_maintenance_job)->where('maintainable_active',1)->first();

                    if($maintainable->maintenable_type == MaintainableTypeConstants::Room){

                        $legal_company = Room::where('id_room',$maintainable->maintenable_id)->where('room_active',1)->
                        join('property', 'property.id_property' , 'room.id_property')->where('property.property_active' , 1)->
                        join('legal_company','legal_company.id_legal_company','property.id_legal_company')
                        ->select('legal_company.short_name')->get();

                    }elseif($maintainable->maintenable_type == MaintainableTypeConstants::Property){

                        $legal_company = Property::where('id_property',$maintainable->maintenable_id)->where('property_active',1)->
                        join('legal_company','legal_company.id_legal_company','property.id_legal_company')->select('legal_company.short_name')->get();

                    }


                    //count all pdf files that previously have been generated
                        $log_for_maintenance_email_pdf_file = MaintenanceLog::where('log_note',trans('maintenance::dashboard.system_created_a_pdf_file_automatically'))->get();

      
                

                        //get all document of pdf file of maintenance email in database that are for this maintenance job
                        $maintenance_document = MaintenanceJobDocument::where('id_maintenance_job',$request['id_maintenance_job'])->where('maintenance_job_document_active',1)
                        ->where('is_uploaded_file',0)
                        ->get()->sortByDesc('id_maintenance_job_document')->first();


                        if( $maintenance_document ){
                           
                            $part_3digit_document_name = substr($maintenance_document->document_name,11,3);

                            $part_2digit_document_name = substr($maintenance_document->document_name,15,2);

                            $part_2digit_document_name = intval($part_2digit_document_name)+1;
                            if ($part_2digit_document_name < 10){

                                $part_2digit_document_name = sprintf("%02d", $part_2digit_document_name);

                            }

                            $file_name = $legal_company[0]->short_name.'-'.date('ymd').'-'.$part_3digit_document_name.'-'.$part_2digit_document_name;

                            // dd($file_name);

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


                        // put the file in determined destination path
                        $pdf->save($base_path.'/mpdf/' . $file_name.'.pdf');

                        $file_path = config('file_storage.maintenance_email_file_path');



                        //add this pdf file to maintenance job document table

                        // $maintenance_job_document = new MaintenanceJobDocument();
                        // $maintenance_job_document->id_maintenance_job = $request->id_maintenance_job;
                        // $maintenance_job_document->document_name = $file_name.'.pdf';
                        // $maintenance_job_document->document_address = $file_path;
                        // $maintenance_job_document->document_extention = 'pdf';
                        // $maintenance_job_document->maintenance_job_document_active = '1';
                        // $maintenance_job_document->save();



                    //record creating this pdf file in maintenance log table

                        // $maintenance_log = new MaintenanceLog();
                        // $maintenance_log->id_maintenance_job = $request->id_maintenance_job;
                        // $maintenance_log->id_staff = $user->id;
                        // $maintenance_log->log_date_time = $now->format(SystemDateFormats::getDateTimeFormat());
                        // $maintenance_log->log_note = trans('maintenance::dashboard.system_created_a_pdf_file_automatically');
                        // $maintenance_log->save();


                        return response()->download(public_path($file_path.'/'.$file_name.'.pdf'), $file_name.'.pdf');


                } catch (\Exception $e) {


                    Log::error('In maintenance package, MaintenanceDashboardController- downloadEmailContent function ' . $e->getMessage());
                    // DB::rollback();


                    return response()->json(
                        [
                          'code' => ActionStatusConstants::FAILURE,
                          'message' => $e->getmessage(),
                        ]);


                }

    }

}

