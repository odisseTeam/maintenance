<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;


use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\SaasClientBusiness;
use App\Models\User;
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
use Odisse\Maintenance\Models\MaintenanceLog as ModelsMaintenanceLog;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Sentinel;
use Spatie\LaravelRay\Commands\PublishConfigCommand;
use Validator;

class MaintenanceDashboardController extends Controller
{




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

        return view('maintenance::maintenance_dashboard',
                    [


                        'businesses'=>$businesses,
                        'categories'=>$categories,
                        'priorities'=>$priorities,
                        'statuses'=>$statuses,
                        'contractors'=>$contractors,
                        'maintenance_users'=>$maintenance_users,
                        'contractor_agents'=>$contractor_agents,

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

        if( $request->has('business') and $request->business != null )
        $maintenances = $maintenances->where('maintenance_job.id_saas_client_business','=', $request->business);

        if( $request->has('category') and $request->category != null )
        $maintenances = $maintenances->where('maintenance_job_category_ref.id_maintenance_job_category_ref','=', $request->category);

        if( $request->has('priority') and $request->priority != null )
        $maintenances = $maintenances->where('maintenance_job_priority_ref.id_maintenance_job_priority_ref','=', $request->priority);

        if( $request->has('status') and $request->status != null )
        $maintenances = $maintenances->where('maintenance_job_status_ref.id_maintenance_job_status_ref','=', $request->status);

        if( $request->has('title') and $request->title != null )
        $maintenances = $maintenances->where('maintenance_job.maintenance_job_title','like', "%".$request->title."%");

        if( $request->has('start_date') and $request->start_date != null )
        $maintenances = $maintenances->where('maintenance_job.job_start_date_time','=', $request->start_date);

        if( $request->has('end_date') and $request->end_date != null )
        $maintenances = $maintenances->where('maintenance_job.job_finished_date_time','=', $request->end_date);

        $maintenances = $maintenances->get();

        foreach($maintenances as $maintenance){


            if($maintenance->expected_target_minutes){
                $time = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() , $maintenance->job_report_date_time )->addMinutes($maintenance->expected_target_minutes);
                $maintenance->remain_time = $time->format(SystemDateFormats::getDateTimeFormat());
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



        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceDashboardController- ajaxDeleteMaintenance function " . " try to delete specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

        $maintenance = MaintenanceJob::find($id_maintenance);
        $maintenance->update([
            'maintenance_job_active' => 0,
        ]);



        return response()->json(
            [
              'code' => ActionStatusConstants::SUCCESS,
              'message' => trans('maintenance::contractor.your_selected_maintenance_deleted'),
            ]);


    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxLoadUserAgents(Request $request){



        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceDashboardController- ajaxLoadUserAgents function " . " try to load User & Agents  ------- by user " . $user->first_name . " " . $user->last_name);

        try{
            $business_contractor = $request->business_contractor;
            $result=[];
            if($business_contractor && $business_contractor[0] == "B"){

                //return business maintenance users
                $users = User::where('users_active' , 1)->where('is_deleted' , 0)->
                join('role_users','role_users.user_id','users.id')->where('role_users_active' , 1)->
                join('roles','roles.id','role_users.role_id')->where('roles.name','Maintenance')->get();
                $result = $users;
            }
            elseif($business_contractor && $business_contractor[0] == "C"){

                //return contractor agents
                $agents = Contractor::where('contractor.id_contractor' , substr($business_contractor, 1))->
                join('contractor_agent','contractor_agent.id_contractor','contractor.id_contractor')->
                join('users','users.id','contractor_agent.id_user')->get();
                $result = $agents;
            }



        return response()->json(
            [
              'code' => ActionStatusConstants::SUCCESS,
              'message' => trans('maintenance::contractor.load_users_agents_was_successful'),
              'result' => $result,
            ]);

        }
        catch(\Exception $e){


            Log::error($e->getMessage());

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'result'=>[],
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
                'message' => $validator,
                ]);

        }

        try{
            DB::beginTransaction();


            $now = Carbon::createFromDate('now');

            $maintenance = MaintenanceJob::find($request->maintenance);

            //check this task assigned to this user already
            $check = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenance->id_maintenance_job )->
                                                where('id_maintenance_staff' , $request->user)->
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
                    'id_maintenance_staff'    =>  $request->user,
                    'staff_assign_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'staff_start_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'maintenance_job_staff_history_active'  =>  1,

                ]);
                $maintenance_staff->save();



                //insert into maintenance_job_staff table
                $maintenance_log = new ModelsMaintenanceLog([
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




}

