<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;


use App\Http\Controllers\Controller;
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

class MaintenanceDashboardController extends Controller
{

    use MaintenanceOperation;




    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDashboardPage(){



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

                    ]
                );

    }
    /////////////////////////////////////////////////////////////////////////////

    public Function ajaxLoadMaintenances(Request $request){


        $user = Sentinel::getUser();

        Log::info(" in MaintenanceDashboardController- ajaxLoadMaintenances function " . " try to load maintenances data  ------- by user " . $user->first_name . " " . $user->last_name);

        $maintenances = MaintenanceJob::where('maintenance_job_active' , 1)->where('maintenance_job.id_saas_client_business' , $user->id_saas_client_business)->
        join('maintenance_job_category_ref' , 'maintenance_job_category_ref.id_maintenance_job_category_ref' , 'maintenance_job.id_maintenance_job_category')->
        join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
        join('maintenance_job_priority_ref' , 'maintenance_job_priority_ref.id_maintenance_job_priority_ref' , 'maintenance_job.id_maintenance_job_priority')->
        join('users' , 'users.id' , 'maintenance_job.id_saas_staff_reporter')->
        join('maintenance_job_sla', 'maintenance_job_sla.id_maintenance_job' , 'maintenance_job.id_maintenance_job')->where('maintenance_job_sla_active' , 1)->
        join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1)->
        leftjoin('resident', 'maintenance_job.id_resident_reporter' , 'resident.id_resident');

        if( $request->has('assignee') and $request->assignee != null ){
            $maintenances = $maintenances->
            join('maintenance_job_staff_history', 'maintenance_job.id_maintenance_job' , 'maintenance_job_staff_history.id_maintenance_job')->where('maintenance_job_staff_history_active' , 1)->
            join('contractor_agent', 'maintenance_job_staff_history.id_maintenance_assignee' , 'contractor_agent.id_user')->
            join('contractor', 'contractor_agent.id_contractor' , 'contractor.id_contractor');
            $maintenances = $maintenances->where('contractor.name','ilike', "%".$request->assignee."%");

        }
        else{
            //$maintenances = $maintenances->whereNull('maintenance_job_staff_history.staff_end_date_time');
        }


        if( $request->has('business') and $request->business != null )
        $maintenances = $maintenances->where('maintenance_job.id_saas_client_business','=', $request->business);

        if( $request->has('category') and $request->category != null )
        $maintenances = $maintenances->where('maintenance_job_category_ref.id_maintenance_job_category_ref','=', $request->category);

        if( $request->has('priority') and $request->priority != null )
        $maintenances = $maintenances->where('maintenance_job_priority_ref.id_maintenance_job_priority_ref','=', $request->priority);

        if( $request->has('status') and $request->status != null )
        $maintenances = $maintenances->where('maintenance_job_status_ref.id_maintenance_job_status_ref','=', $request->status);

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
            $maintenances = $maintenances->whereNull('staff_end_date_time');
            $maintenances = $maintenances->groupBy('maintenance_job.id_saas_client_business','maintenance_job.id_maintenance_job','maintenance_job_category_ref.id_maintenance_job_category_ref','maintenance_job_status_ref.id_maintenance_job_status_ref','maintenance_job_priority_ref.id_maintenance_job_priority_ref','users.id','maintenance_job_sla.id_maintenance_job_sla' , 'maintenance_job_sla_ref.id_maintenance_job_sla_ref','resident.id_resident','maintenance_job_staff_history.id_maintenance_job_staff_history','contractor_agent.id_contractor_agent','contractor.id_contractor');
        }
        else{
            $maintenances = $maintenances->groupBy('maintenance_job.id_saas_client_business','maintenance_job.id_maintenance_job','maintenance_job_category_ref.id_maintenance_job_category_ref','maintenance_job_status_ref.id_maintenance_job_status_ref','maintenance_job_priority_ref.id_maintenance_job_priority_ref','users.id','maintenance_job_sla.id_maintenance_job_sla' , 'maintenance_job_sla_ref.id_maintenance_job_sla_ref','resident.id_resident');
        }

        Log::debug($maintenances->toSql());



        $maintenances = $maintenances->get();

        foreach($maintenances as $maintenance){

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


            Log::error($e->getMessage());
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
            }



        return response()->json(
            [
              'code' => ActionStatusConstants::SUCCESS,
              'message' => trans('maintenance::contractor.load_users_agents_was_successful'),
              'result' => $result,
              'user_type' => $user_type,
              'contractor' => $contractor,
            ]);

        }
        catch(\Exception $e){


            Log::error($e->getMessage());

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'result'=>[],
                  'contractor'=>null,
                  'message' => $e->getMessage(),//trans('maintenance::dashboard.load_users_agents_was_not_successful'),
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

        try{
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
                        ]);
                    }

                }


                //insert into maintenance_job_staff table
                $maintenance_staff = new MaintenanceJobStaffHistory([
                    'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                    'id_maintenance_staff'    =>  $user->id,
                    'id_maintenance_assignee'    =>  $request->user,
                    'staff_assign_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'staff_start_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'maintenance_job_staff_history_active'  =>  1,

                ]);
                $maintenance_staff->save();



                //insert into maintenance_job_staff table
                $maintenance_log = new MaintenanceLog([
                    'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                    'id_staff'    =>  $user->id,
                    'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'log_note'  =>  trans('maintenance::dashboard.assign_maintenance_to_user'),

                ]);
                $maintenance_log->save();



                DB::commit();

                return response()->json(
                    [
                    'code' => ActionStatusConstants::SUCCESS,
                    'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_successful'),
                    ]);

            }else{

                DB::rollback();

                return response()->json(
                    [
                    'code' => ActionStatusConstants::FAILURE,
                    'message' => trans('maintenance::dashboard.maintenance_assigned_to_this_user_already'),
                    ]);


            }




        }
        catch(\Exception $e){


            Log::error($e->getMessage());
            DB::rollback();


            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'result'=>[],
                  'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_not_successful'),
                ]);


        }


    }
    ///////////////////////////////////////////////////////////////////////////
    public Function ajaxStartMaintenance(Request $request , $id_maintenance){

        //dd($request->all());


        $user = Sentinel::getUser();

        $validator = Validator::make($request->all(), [

            'start_date_time' => 'required|date_format:'.SystemDateFormats::getDateTimeFormat(),

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


        $result = $this->startMaintenance($user->id ,$id_maintenance ,$request->start_date_time);


        return response()->json(
            [
              'code' => $result['code'],
              'message' => $result['message'],
            ]);



    }


    ///////////////////////////////////////////////////////////////////////////
    public Function ajaxEndMaintenance(Request $request , $id_maintenance){

        //dd($request->all());
        //dd(SystemDateFormats::getDateTimeFormat());


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





}

