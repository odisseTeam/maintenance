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
use Odisse\Maintenance\App\SLP\MaintenanceOperation;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use stdClass;

class ApiMaintenanceDetailController extends Controller
{

    use MaintenanceOperation;



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
        join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1)->
        leftjoin('resident', 'maintenance_job.id_resident_reporter' , 'resident.id_resident');

        if( $request->has('assignee') and $request->assignee != null ){
            $maintenances = $maintenances->
            leftjoin('maintenance_job_staff_history', 'maintenance_job.id_maintenance_job' , 'maintenance_job_staff_history.id_maintenance_job')->where('maintenance_job_staff_history_active' , 1)->
            leftjoin('contractor_agent', 'maintenance_job_staff_history.id_maintenance_staff' , 'contractor_agent.id_user')->
            leftjoin('contractor', 'contractor_agent.id_contractor' , 'contractor.id_contractor');
            $maintenances = $maintenances->where('contractor.name','like', "%".$request->assignee."%");
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
        $maintenances = $maintenances->where('maintenance_job.maintenance_job_title','like', "%".$request->title."%");

        if( $request->has('start_date') and $request->start_date != null )
        $maintenances = $maintenances->where('maintenance_job.job_start_date_time','=', Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $request->start_date)->format('Y-m-d H:i:s'));

        if( $request->has('end_date') and $request->end_date != null )
        $maintenances = $maintenances->where('maintenance_job.job_finish_date_time','=', Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $request->end_date)->format('Y-m-d H:i:s'));



        if( $request->has('assignee') and $request->assignee != null ){
            $maintenances = $maintenances->groupBy('maintenance_job.id_saas_client_business','maintenance_job.id_maintenance_job','maintenance_job_category_ref.id_maintenance_job_category_ref','maintenance_job_status_ref.id_maintenance_job_status_ref','maintenance_job_priority_ref.id_maintenance_job_priority_ref','users.id','maintenance_job_sla.id_maintenance_job_sla' , 'maintenance_job_sla_ref.id_maintenance_job_sla_ref','resident.id_resident','maintenance_job_staff_history.id_maintenance_job_staff_history','contractor_agent.id_contractor_agent','contractor.id_contractor');
        }
        else{
            $maintenances = $maintenances->groupBy('maintenance_job.id_saas_client_business','maintenance_job.id_maintenance_job','maintenance_job_category_ref.id_maintenance_job_category_ref','maintenance_job_status_ref.id_maintenance_job_status_ref','maintenance_job_priority_ref.id_maintenance_job_priority_ref','users.id','maintenance_job_sla.id_maintenance_job_sla' , 'maintenance_job_sla_ref.id_maintenance_job_sla_ref','resident.id_resident');
        }



        $maintenances = $maintenances->get();

        $businesses = config('maintenances.businesses_name');


        foreach($maintenances as $maintenance){

            $maintenance->id_business = $maintenance->id_saas_client_business;
            foreach($businesses as $business){
                if($maintenance->id_saas_client_business == $business['id_saas_client_business']){
                    $maintenance->business_name = $business['business_name'];

                }
            }
            unset($maintenance->password);
            unset($maintenance->permissions);

            $maintenance->m_url = env('APP_URL').'/maintenance/detail/'. $maintenance->id_maintenance_job;

            $remain_time = $this->calculateSlaRemainTime($maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);

            if($remain_time){
                $maintenance->remain_time = $remain_time;
            }
            else{
                $maintenance->remain_time = '-';

            }


            // if($maintenance->expected_target_minutes){

            //     $time = Carbon::createFromFormat('Y-m-d H:i:s', $maintenance->job_report_date_time )->addMinutes($maintenance->expected_target_minutes);
            //     $maintenance->remain_time = $time->format('Y-m-d H:i:s');
            // }
            // else{
            //     $maintenance->remain_time = '-';


            // }

        }






            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.load_maintenances_successfully');


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


    public function getMaintenancesListHistory(Request $request)
    {

        // return response()->json(
        //     [
        //         'status' => '200',
        //         'message'   => 'ok',
        //         'request'  => $request->all(),
        //     ]
        // );

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
            Log::info("Call API :: ApiMaintenanceDetailController - getMaintenancesListHistory function");


        $maintenances = MaintenanceJob::where('maintenance_job_active' , 1)->
        join('maintenance_job_category_ref' , 'maintenance_job_category_ref.id_maintenance_job_category_ref' , 'maintenance_job.id_maintenance_job_category')->
        join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
        join('maintenance_job_priority_ref' , 'maintenance_job_priority_ref.id_maintenance_job_priority_ref' , 'maintenance_job.id_maintenance_job_priority')->
        join('users' , 'users.id' , 'maintenance_job.id_saas_staff_reporter')->
        join('maintenance_job_sla', 'maintenance_job_sla.id_maintenance_job' , 'maintenance_job.id_maintenance_job')->where('maintenance_job_sla_active' , 1)->
        join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1)->
        join('maintainable' , 'maintainable.id_maintenance_job' , 'maintenance_job.id_maintenance_job');

        if( $request->has('business') and $request->business != null )
        $maintenances = $maintenances->where('maintenance_job.id_saas_client_business','=', $request->business);


        if( $request->has('maintenable_id') and $request->maintenable_id != null )
        $maintenances = $maintenances->where('maintainable.maintenable_id','=', $request->maintenable_id);


        if( $request->has('maintenable_type') and $request->maintenable_type != null )
        $maintenances = $maintenances->where('maintainable.maintenable_type','=', $request->maintenable_type);


        $maintenances = $maintenances->get();

        $businesses = config('maintenances.businesses_name');


        return response()->json(
            [
                'status' => '200',
                'message'   => 'ok',
                'maintenances'  => $maintenances,
            ]
        );


        // foreach($maintenances as $maintenance){

        //     $maintenance->id_business = $maintenance->id_saas_client_business;
        //     foreach($businesses as $business){
        //         if($maintenance->id_saas_client_business == $business['id_saas_client_business']){
        //             $maintenance->business_name = $business['business_name'];

        //         }
        //     }


        //     if($maintenance->expected_target_minutes){

        //         $time = Carbon::createFromFormat('Y-m-d H:i:s', $maintenance->job_report_date_time )->addMinutes($maintenance->expected_target_minutes);
        //         $maintenance->remain_time = $time->format('Y-m-d H:i:s');
        //     }
        //     else{
        //         $maintenance->remain_time = '-';


        //     }

        // }






            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.successful_getMaintenanceListHistory');


        } catch (\Exception $e) {

            Log::error($e->getMessage());
            $message = trans('maintenance::maintenance_mgt.unsuccessful_getMaintenanceListHistory');
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
                $users = User::where('users_active' , 1)->
                join('role_users','role_users.user_id','users.id')->
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


    public function assignMaintenanceToUser(Request $request)
    {


        try {




            Log::info("Call API :: ApiMaintenanceDetailController - assignMaintenanceToUser function");

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

                // return response()->json(
                // [
                // 'status' => APIStatusConstants::OK,
                // 'code' => ActionStatusConstants::SUCCESS,
                // 'message' => 'yah yah yah',
                // 'req' => $request->all(),
                // ]);


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
                $maintenance_log = new MaintenanceLog([
                    'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                    'id_staff'    =>  $request->staff_user,
                    'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'log_note'  =>  trans('maintenance::dashboard.assign_maintenance_to_user'),

                ]);
                $maintenance_log->save();



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


            Log::error($e->getMessage());
            DB::rollback();


            return response()->json(
                [
                  'status' => 400,
                  'code' => 'failure',
                  'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_not_successful'),
                ]);


        }


    }


    public function startMaintenanceApi(Request $request)
    {


        // return response()->json(
        //     [
        //         'request'=>$request->all(),
        //         'message' => 'Hahaha',
        //     ]);


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


            $result = $this->startMaintenance($request->staff_user ,$request->maintenance ,$request->start_date_time);


            return response()->json(
                [
                  'code' => $result['code'],
                  'message' => $result['message'],
                ]);






    }


    public function endMaintenanceApi(Request $request)
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


            $result = $this->endMaintenance($request->staff_user ,$request->maintenance ,$request->end_date_time);


            return response()->json(
                [
                  'code' => $result['code'],
                  'message' => $result['message'],
                ]);



        } catch (\Exception $e) {

            Log::error($e->getMessage());
            $message = trans('maintenance::maintenance_mgt.end_maintenance_was_unsuccessful');
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


    ///////////////////////////////////////////////////////////////////////////
    public function getMaintenanceStatusChartData(Request $request){




        $statuses = MaintenanceJobStatusRef::where('maintenance_job_status_ref_active' , 1)->get();
        $colour_code = ['rgba(95, 190, 170, 0.99)' , 'rgba(26, 188, 156, 0.88)' , 'rgba(93, 156, 236, 0.93)', 'rgba(0, 255, 236, 0.99)', 'rgba(100, 25, 126, 0.99)', 'rgba(10, 25, 16, 0.99)'];







        $temp_val = new stdClass();
        $temp_val->label = SaasClientBusiness::find($request->business)->business_name;

        $temp_val->data = [];
        $temp_val->backgroundColor = [];
        $temp_val->hoverBackgroundColor = [];
        $temp_val->status = [];

        // return response()->json(
        //     [
        //     'code' => 'success',
        //     'message' => trans('maintenance::dashboard.chart_data_prepared'),
        //     'request' => $request->all(),
        //     'temp_val' => $temp_val,
        //     ]);


        $counter =0;
        foreach($statuses as $status){

            $maintenances = MaintenanceJob::where('maintenance_job_active' , 1)->where('id_maintenance_job_status' , $status->id_maintenance_job_status_ref)->get();
            $maintenance_count = count($maintenances);
            // $result[$status->job_status_code]= $maintenance_count;

            array_push($temp_val->status, $status->job_status_name);
            array_push($temp_val->data, $maintenance_count);
            array_push($temp_val->backgroundColor, $colour_code[$counter]);
            array_push($temp_val->hoverBackgroundColor, $colour_code[$counter++]);
        }




        return response()->json(
            [
            'code' => 'success',
            'message' => trans('maintenance::dashboard.chart_data_prepared'),
            'result' => $temp_val,
            ]);



    }


    ///////////////////////////////////////////////////////////////////////////
    public function getMaintenanceSlaChartData(Request $request){




        $states = ['Expired' , 'Not Expired'];
        $colour_code = ['rgba(95, 190, 170, 0.99)' , 'rgba(26, 188, 156, 0.88)' , 'rgba(93, 156, 236, 0.93)', 'rgba(0, 255, 236, 0.99)', 'rgba(100, 25, 126, 0.99)', 'rgba(10, 25, 16, 0.99)'];







        $temp_val = new stdClass();
        $temp_val->label = SaasClientBusiness::find($request->business)->business_name;

        $temp_val->data = [];
        $temp_val->backgroundColor = [];
        $temp_val->hoverBackgroundColor = [];
        $temp_val->status = [];

        // return response()->json(
        //     [
        //     'code' => 'success',
        //     'message' => trans('maintenance::dashboard.chart_data_prepared'),
        //     'request' => $request->all(),
        //     'temp_val' => $temp_val,
        //     ]);


        $counter =0;
        $sla_count = ['Expired'=>0,'Not Expired'=>0];


            $maintenaces = MaintenanceJob::where('maintenance_job_active' , 1)->
            join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
            join('maintenance_job_sla', 'maintenance_job_sla.id_maintenance_job' , 'maintenance_job.id_maintenance_job')->where('maintenance_job_sla_active' , 1)->
            join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1)->
            where('maintenance_job_status_ref.job_status_code' , '!=' , 'CLOS')->get();

            foreach($maintenaces as $maintenance){
                $remain_time = $this->calculateSlaRemainTime($maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);
                if($remain_time){
                    $date1 =Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() , $remain_time);
                    $date2 = Carbon::createFromDate('now');
                    if($date1->gt($date2)){
                        $sla_count['Not Expired']++;
                    }
                    else{
                        $sla_count['Expired']++;
                    }

                }
            }

            array_push($temp_val->status, 'Expired');
            array_push($temp_val->data, $sla_count['Expired']);
            array_push($temp_val->backgroundColor, $colour_code[$counter]);
            array_push($temp_val->hoverBackgroundColor, $colour_code[$counter++]);


            array_push($temp_val->status, 'Not Expired');
            array_push($temp_val->data, $sla_count['Not Expired']);
            array_push($temp_val->backgroundColor, $colour_code[$counter]);
            array_push($temp_val->hoverBackgroundColor, $colour_code[$counter++]);





        return response()->json(
            [
            'code' => 'success',
            'message' => trans('maintenance::dashboard.chart_data_prepared'),
            'result' => $temp_val,
            ]);



    }



}
