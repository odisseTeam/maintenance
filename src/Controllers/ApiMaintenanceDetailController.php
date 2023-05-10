<?php

namespace Odisse\Maintenance\Controllers;

use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;
use Odisse\Maintenance\Models\MaintenanceJob;
use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;

use App\Http\Controllers\Controller;
use App\Models\SaasClientBusiness;
use App\Models\User;
use App\SLP\Enum\APIStatusConstants;
use App\SLP\Formatter\SystemDateFormats;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Odisse\Maintenance\Models\Contractor;
use Odisse\Maintenance\Models\MaintenanceJobStaffHistory;
use Odisse\Maintenance\Models\MaintenanceLog;

class ApiMaintenanceDetailController extends Controller
{



    public function getMaintenancesListDetail(Request $request)
    {

        //return response()->json($request->all());

        try {

        //$user =  JWTAuth::parseToken()->authenticate();


        // if(! $user){

        //     $status = APIStatusConstants::UNAUTHORIZED;
        //     $message = trans('general.you_have_to_login_first');

        //     return response()->json(
        //         [
        //             'status' => $status,
        //             'message'   => $message,
        //             'data'  => ''
        //         ]
        //     );
        // }
            Log::info("Call API :: MaintenanceDetailController - getMaintenanceListDetail function");


        $maintenances = MaintenanceJob::where('maintenance_job_active' , 1)->
        join('maintenance_job_category_ref' , 'maintenance_job_category_ref.id_maintenance_job_category_ref' , 'maintenance_job.id_maintenance_job_category')->
        join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
        join('maintenance_job_priority_ref' , 'maintenance_job_priority_ref.id_maintenance_job_priority_ref' , 'maintenance_job.id_maintenance_job_priority')->
        join('users' , 'users.id' , 'maintenance_job.id_saas_staff_reporter')->
        join('maintenance_job_sla', 'maintenance_job_sla.id_maintenance_job' , 'maintenance_job.id_maintenance_job')->where('maintenance_job_sla_active' , 1)->
        join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1);

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

        $businesses = config('maintenances.businesses_name');


        foreach($maintenances as $maintenance){

            $maintenance->id_business = $maintenance->id_saas_client_business;
            foreach($businesses as $business){
                if($maintenance->id_saas_client_business == $business['id_saas_client_business']){
                    $maintenance->business_name = $business['business_name'];

                }
            }


            if($maintenance->expected_target_minutes){

                $time = Carbon::createFromFormat('Y-m-d H:i:s', $maintenance->job_report_date_time )->addMinutes($maintenance->expected_target_minutes);
                $maintenance->remain_time = $time->format('Y-m-d H:i:s');
            }
            else{
                $maintenance->remain_time = '-';


            }

        }






            $status = APIStatusConstants::OK;
            $message = trans('roomView.successful_getRoomsListDetail');


        } catch (\Exception $e) {

            Log::error($e->getMessage());
            $message = trans('roomView.unsuccessful_getRoomsListDetail');
            $status = APIStatusConstants::BAD_REQUEST;
            $$maintenances=null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'maintenances'  => $maintenances,
            ]
        );
    }

    public function deleteMaintenance(Request $request)
    {

        try {

        //$user =  JWTAuth::parseToken()->authenticate();


        // if(! $user){

        //     $status = APIStatusConstants::UNAUTHORIZED;
        //     $message = trans('general.you_have_to_login_first');

        //     return response()->json(
        //         [
        //             'status' => $status,
        //             'message'   => $message,
        //             'data'  => ''
        //         ]
        //     );
        // }
            Log::info("Call API :: ApiMaintenanceDetailController - deleteMaintenance function");


        $maintenance = MaintenanceJob::find($request->maintenance);

        $maintenance->update([
            'maintenance_job_active' => 0,
        ]);


            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.delete_maintenance_was_successful');


        } catch (\Exception $e) {

            Log::error($e->getMessage());
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


    public function getBusinessContractors(Request $request)
    {

        //return response()->json($request->all());

        try {

        //$user =  JWTAuth::parseToken()->authenticate();


        // if(! $user){

        //     $status = APIStatusConstants::UNAUTHORIZED;
        //     $message = trans('general.you_have_to_login_first');

        //     return response()->json(
        //         [
        //             'status' => $status,
        //             'message'   => $message,
        //             'data'  => ''
        //         ]
        //     );
        // }
            Log::info("Call API :: MaintenanceDetailController - getMaintenanceListDetail function");


            $businesses = SaasClientBusiness::where('saas_client_business_active' , 1)->get();
            $contractors = Contractor::where('contractor_active' , 1)->get();










            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.load_business_contractors_was_successful');


        } catch (\Exception $e) {

            Log::error($e->getMessage());
            $message = trans('maintenance::maintenance_mgt.load_business_contractors_was_unsuccessful');
            $status = APIStatusConstants::BAD_REQUEST;
            $businesses=null;
            $contractors=null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'businesses'  => $businesses,
                'contractors'  => $contractors,
            ]
        );
    }


    public function getUserAgents(Request $request)
    {

        //return response()->json($request->all());

        try {

        //$user =  JWTAuth::parseToken()->authenticate();


        // if(! $user){

        //     $status = APIStatusConstants::UNAUTHORIZED;
        //     $message = trans('general.you_have_to_login_first');

        //     return response()->json(
        //         [
        //             'status' => $status,
        //             'message'   => $message,
        //             'data'  => ''
        //         ]
        //     );
        // }
            Log::info("Call API :: ApiMaintenanceDetailController - getUserAgents function");


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










            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.load_user_agents_was_successful');


        } catch (\Exception $e) {

            Log::error($e->getMessage());
            $message = trans('maintenance::maintenance_mgt.load_user_agents_was_unsuccessful');
            $status = APIStatusConstants::BAD_REQUEST;
            $agents=null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'agents'  => $result,
            ]
        );
    }


    // public function assignMaintenanceToUser(Request $request)
    // {


    //     try {


    //         Log::info("Call API :: ApiMaintenanceDetailController - assignMaintenanceToUser function");

    //         DB::beginTransaction();



    //         $now = Carbon::createFromDate('now');

    //         $maintenance = MaintenanceJob::find($request->maintenance);

    //         //check this task assigned to this user already
    //         $check = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenance->id_maintenance_job )->
    //                                             where('id_maintenance_staff' , $request->user)->
    //                                             whereNull('staff_end_date_time')->
    //                                             where('maintenance_job_staff_history_active' , 1)->get();
    //         if(count($check)==0 ){

    //             //check if this task is assigned to another person
    //             $check2 = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenance->id_maintenance_job )->
    //             whereNull('staff_end_date_time')->
    //             where('maintenance_job_staff_history_active' , 1)->get();

    //             if(count($check2)>0){
    //                 foreach($check2  as $assign_staf_obj){
    //                     $assign_staf_obj->update([
    //                         'staff_end_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
    //                     ]);
    //                 }

    //             }


    //             //insert into maintenance_job_staff table
    //             $maintenance_staff = new MaintenanceJobStaffHistory([
    //                 'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
    //                 'id_maintenance_staff'    =>  $request->user,
    //                 'staff_assign_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
    //                 'staff_start_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
    //                 'maintenance_job_staff_history_active'  =>  1,

    //             ]);
    //             $maintenance_staff->save();



    //             //insert into maintenance_job_staff table
    //             $maintenance_log = new MaintenanceLog([
    //                 'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
    //                 'id_staff'    =>  $user->id,
    //                 'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
    //                 'log_note'  =>  trans('maintenance::dashboard.assign_maintenance_to_user'),

    //             ]);
    //             $maintenance_log->save();



    //             DB::commit();

    //             return response()->json(
    //                 [
    //                 'status' => APIStatusConstants::OK,
    //                 'code' => ActionStatusConstants::SUCCESS,
    //                 'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_successful'),
    //                 ]);

    //         }else{

    //             DB::rollback();

    //             return response()->json(
    //                 [
    //                 'status' => APIStatusConstants::BAD_REQUEST,
    //                 'code' => ActionStatusConstants::FAILURE,
    //                 'message' => trans('maintenance::dashboard.maintenance_assigned_to_this_user_already'),
    //                 ]);


    //         }


    //     } catch (\Exception $e) {


    //         Log::error($e->getMessage());
    //         DB::rollback();


    //         return response()->json(
    //             [
    //               'status' => $status,
    //               'code' => ActionStatusConstants::FAILURE,
    //               'result'=>[],
    //               'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_not_successful'),
    //             ]);


    //     }


    // }


}
