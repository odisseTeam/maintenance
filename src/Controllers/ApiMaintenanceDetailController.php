<?php

namespace Odisse\Maintenance\Controllers;

use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;
use Odisse\Maintenance\Models\MaintenanceJob;
use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\SaasClientBusiness;
use App\Models\User;
use App\SLP\Enum\APIStatusConstants;
use App\SLP\Formatter\SystemDateFormats;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JWTAuth;
use Odisse\Maintenance\Models\Contractor;
use Odisse\Maintenance\Models\MaintenanceJobStaffHistory;
use Odisse\Maintenance\Models\MaintenanceLog;
use Odisse\Maintenance\App\SLP\MaintenanceOperation;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\ContractorLocation;
use Odisse\Maintenance\Models\ContractorSkill;
use Odisse\Maintenance\Models\ContractorSkillRef;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use stdClass;
use Validator;
use Sentinel;


class ApiMaintenanceDetailController extends Controller
{

    use MaintenanceOperation;



    public function getContractorsWithSkill(Request $request){


        try{

            $maintenanceId = $request->maintenance;
            $contractors = [];
            $businesses = [];



            if($maintenanceId){


                if($request->contractor_skill){

                    //get all the conductors are in maintenance location

                    $room_contractors = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $maintenanceId)->
                        join('maintainable','maintenance_job.id_maintenance_job','maintainable.id_maintenance_job')->where('maintainable.maintenable_type' , 'App\Models\Rooms')->
                        join('room' , 'maintainable.maintenable_id' , 'room.id_room')->
                        join('property' , 'room.id_property' , 'property.id_property')->
                        join('city' , 'property.id_city' , 'city.id_city')->
                        join('contractor' , 'maintenance_job.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
                        join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
                        join('contractor_location_ref', function ($join) {
                        $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                        $join->on('contractor_location_ref.location', '=', 'city.name');
                        })->
                        join('contractor_skill' , 'contractor_skill.id_contractor','contractor.id_contractor')->where('contractor_skill.contractor_skill_active' , 1)->whereIn('contractor_skill.id_contractor_skill_ref' , $request->contractor_skill)->
                        join('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref' )->
                        select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();

                    $property_contractors = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $maintenanceId)->
                        join('maintainable','maintenance_job.id_maintenance_job','maintainable.id_maintenance_job')->where('maintainable.maintenable_type' , 'App\Models\Property')->
                        join('property' , 'maintainable.maintenable_id' , 'property.id_property')->
                        join('city' , 'property.id_city' , 'city.id_city')->
                        join('contractor' , 'maintenance_job.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
                        join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
                        join('contractor_location_ref', function ($join) {
                            $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                            $join->on('contractor_location_ref.location', '=', 'city.name');
                        })->
                        join('contractor_skill' , 'contractor_skill.id_contractor','contractor.id_contractor')->where('contractor_skill.contractor_skill_active' , 1)->whereIn('contractor_skill.id_contractor_skill_ref' , $request->contractor_skill)->
                        join('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref' )->
                        select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();

                    //dd($property_contractors);

                    $contractors = array_unique(array_merge($room_contractors , $property_contractors) , SORT_REGULAR);
                }
                else{
                    //get all the conductors are in maintenance location

                    $room_contractors = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $maintenanceId)->
                    join('maintainable','maintenance_job.id_maintenance_job','maintainable.id_maintenance_job')->where('maintainable.maintenable_type' , 'App\Models\Rooms')->
                    join('room' , 'maintainable.maintenable_id' , 'room.id_room')->
                    join('property' , 'room.id_property' , 'property.id_property')->
                    join('city' , 'property.id_city' , 'city.id_city')->
                    join('contractor' , 'maintenance_job.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
                    join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
                    join('contractor_location_ref', function ($join) {
                    $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                    $join->on('contractor_location_ref.location', '=', 'city.name');
                    })->select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();

                    $property_contractors = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $maintenanceId)->
                    join('maintainable','maintenance_job.id_maintenance_job','maintainable.id_maintenance_job')->where('maintainable.maintenable_type' , 'App\Models\Property')->
                    join('property' , 'maintainable.maintenable_id' , 'property.id_property')->
                    join('city' , 'property.id_city' , 'city.id_city')->
                    join('contractor' , 'maintenance_job.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
                    join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
                    join('contractor_location_ref', function ($join) {
                        $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                        $join->on('contractor_location_ref.location', '=', 'city.name');
                    })->select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();

                    //dd($property_contractors);

                    $contractors = array_unique(array_merge($room_contractors , $property_contractors) , SORT_REGULAR);

                }

            }

            else{


                if($request->contractor_skill){

                    $room_contractors = $property_contractors = [];


                    if($request->place){



                        if(Str::contains($request->place, 'Room')){
                            $roomId = substr($request->place , 4);

                            //get all the conductors are in maintenance location

                            $room_contractors = Room::where('room.id_room' , $roomId)->
                            join('property' , 'room.id_property' , 'property.id_property')->
                            join('site' , 'property.id_site' , 'site.id_site')->
                            join('city' , 'property.id_city' , 'city.id_city')->
                            join('contractor' , 'site.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
                            join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
                            join('contractor_location_ref', function ($join) {
                                $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                                $join->on('contractor_location_ref.location', '=', 'city.name');
                            })->
                            join('contractor_skill' , 'contractor_skill.id_contractor','contractor.id_contractor')->where('contractor_skill.contractor_skill_active' , 1)->whereIn('contractor_skill.id_contractor_skill_ref' , $request->contractor_skill)->
                            join('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref' )->
                            select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();

                        }
                        elseif(Str::contains($request->place, 'Property')){
                            $propertyId = substr($request->place , 8);

                            $property_contractors = Property::where('property.id_property' , $propertyId)->
                            join('site' , 'site.id_site' , 'property.id_site')->
                            join('city' , 'property.id_city' , 'city.id_city')->
                            join('contractor' , 'site.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
                            join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
                            join('contractor_location_ref', function ($join) {
                                $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                                $join->on('contractor_location_ref.location', '=', 'city.name');
                            })->
                            join('contractor_skill' , 'contractor_skill.id_contractor','contractor.id_contractor')->where('contractor_skill.contractor_skill_active' , 1)->whereIn('contractor_skill.id_contractor_skill_ref' , $request->contractor_skill)->
                            join('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref' )->
                            select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();
                        }
                    }
                    else{
                        //nothing returned

                    }


                    $contractors = array_unique(array_merge($room_contractors , $property_contractors) , SORT_REGULAR);

                }
                else{

                    $room_contractors = $property_contractors = [];


                    if($request->place){



                        if(Str::contains($request->place, 'Room')){
                            $roomId = substr($request->place , 4);

                            //get all the conductors are in maintenance location

                            $room_contractors = Room::where('room.id_room' , $roomId)->
                            join('property' , 'room.id_property' , 'property.id_property')->
                            join('site' , 'property.id_site' , 'site.id_site')->
                            join('city' , 'property.id_city' , 'city.id_city')->
                            join('contractor' , 'site.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
                            join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
                            join('contractor_location_ref', function ($join) {
                                $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                                $join->on('contractor_location_ref.location', '=', 'city.name');
                            })->
                            select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();

                        }
                        elseif(Str::contains($request->place, 'Property')){
                            $propertyId = substr($request->place , 8);

                            $property_contractors = Property::where('property.id_property' , $propertyId)->
                            join('site' , 'site.id_site' , 'property.id_site')->
                            join('city' , 'property.id_city' , 'city.id_city')->
                            join('contractor' , 'site.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
                            join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
                            join('contractor_location_ref', function ($join) {
                                $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                                $join->on('contractor_location_ref.location', '=', 'city.name');
                            })->
                            select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();
                        }
                    }
                    else{
                        //nothing returned

                    }


                    $contractors = array_unique(array_merge($room_contractors , $property_contractors) , SORT_REGULAR);


                }

            }


            $businesses = SaasClientBusiness::where('saas_client_business_active' , 1)->get();


            return response()->json(
                [
                'status' => APIStatusConstants::OK,
                'message' => trans('maintenance::contractor.contractor_agent_info_returned'),
                'contractors' =>$contractors,
                'businesses' =>$businesses,
                ]);





        }catch(\Exception $e){

            Log::error("In Maintenance package - ApiMaintenanceDetailController - getContractorsWithSkill " . $e->getMessage());
            return response()->json([
                'status' =>APIStatusConstants::BAD_REQUEST,
                'message'=>$e->getMessage(),
                'contractors' =>[],
                'businesses'=>[],
            ]);

        }


    }



    public function getMaintenanceMgtRefData(){

        $skills = ContractorSkillRef::where('contractor_skill_ref_active' , 1)->get();
        $statuses = MaintenanceJobStatusRef::where('maintenance_job_status_ref_active' , 1)->get();
        $priorities = MaintenanceJobPriorityRef::where('maintenance_job_priority_ref_active' , 1 )->get();
        $categories = MaintenanceJobCategoryRef::where('maintenance_job_category_ref_active' , 1 )->get();


        $status = APIStatusConstants::OK;
        $message = trans('maintenance::maintenance_mgt.maintenance_ref_data_loaded_successfully');


        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'skills'  => $skills,
                'statuses'  => $statuses,
                'priorities'  => $priorities,
                'categories'  => $categories,
            ]
        );
    }



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
            Log::info("In maintenance package - in ApiMaintenanceDetailController - getMaintenanceListDetail function");


        $maintenances = MaintenanceJob::where('maintenance_job_active' , 1)->
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


            //Log::debug($maintenances->toSql());
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

            $maintenances = $maintenances->select('contractor.name AS contractor_name','maintenance_job_staff_history.*' , 'u2.first_name AS assignee_first_name' ,'u2.last_name AS assignee_last_name','maintenance_job.*' , 'maintenance_job_category_ref.job_category_name AS job_category_name' , 'maintenance_job_status_ref.*' , 'maintenance_job_priority_ref.*' ,'u1.first_name AS staff_first_name' ,'u1.last_name AS staff_last_name' , 'maintenance_job_sla.*' , 'maintenance_job_sla_ref.*' , 'resident.*' );

        }


        // return response()->json(
        //     [
        //         'status' => 'ok',
        //         'message'   => $maintenances->toSql(),
        //         'maintenances'  => [],
        //     ]
        // );




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
            $maintenances = $maintenances->whereNull('staff_end_date_time')->where('is_last_one' , 1);

        }
        else{
            //$maintenances = $maintenances->where('is_last_one' , 1)->orWhereNull('is_last_one');
            $maintenances = $maintenances->where(function ($query)  {
                $query->where('is_last_one' , 1)
                      ->orWhereNull('is_last_one');
            });

        }


        // Log::debug($maintenances->toSql());


        $maintenances = $maintenances->get();


        $businesses = config('maintenances.businesses_name');


        foreach($maintenances as $maintenance){

            $maintenance_obj = MaintenanceJob::find($maintenance->id_maintenance_job);

            $maintenance->id_business = $maintenance_obj->id_saas_client_business;
            foreach($businesses as $business){
                if($maintenance_obj->id_saas_client_business == $business['id_saas_client_business']){
                    $maintenance->business_name = $business['business_name'];

                }
            }
            //unset($maintenance->password);
            //unset($maintenance->permissions);

            $maintenance->m_url = env('APP_URL').'/maintenance/detail/'. $maintenance->id_maintenance_job;
            $maintenance->mail_url = env('APP_URL').'/maintenance/create/email_temp/'. $maintenance->id_maintenance_job;

            $remain_time = $this->calculateSlaRemainTime($request->business , $maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);

            if($remain_time){
                $maintenance->remain_time = $remain_time;
            }
            else{
                $maintenance->remain_time = '-';

            }


        }






            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.load_maintenances_successfully');


        } catch (\Exception $e) {

            Log::error("In maintenance package - in ApiMaintenanceDetailController - getMaintenanceListDetail function" . $e->getMessage());
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
        Log::info("In maintenance package - in ApiMaintenanceDetailController - getMaintenancesListHistory function");


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

            Log::error("In maintenance package - in ApiMaintenanceDetailController - getMaintenancesListHistory function" . $e->getMessage());
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
            Log::info("In maintenance package - in ApiMaintenanceDetailController - deleteMaintenance function");


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
            Log::info("Call API :: MaintenanceDetailController - getBusinessContractors function");


            $staff_user = User::where('email' , $request->staff_user)->first();
            if($staff_user){
                $user = Sentinel::findById($staff_user->id);
                Sentinel::login($user);
                $businesses = SaasClientBusiness::where('saas_client_business_active' , 1)->where('id_saas_client_business' , $staff_user->id_saas_client_business)->get();
            }
            else{

                //get api user
                $api_user = User::where('email' , 'api.user@sdr.uk')->first();
                $businesses = SaasClientBusiness::where('saas_client_business_active' , 1)->where('id_saas_client_business' , $api_user->id_saas_client_business)->get();


            }

            $room_contractors = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $request->maintenance)->
            join('maintainable','maintenance_job.id_maintenance_job','maintainable.id_maintenance_job')->where('maintainable.maintenable_type' , 'App\Models\Rooms')->
            join('room' , 'maintainable.maintenable_id' , 'room.id_room')->
            join('property' , 'room.id_property' , 'property.id_property')->
            join('city' , 'property.id_city' , 'city.id_city')->
            join('contractor' , 'maintenance_job.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
            join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
            join('contractor_location_ref', function ($join) {
                $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                $join->on('contractor_location_ref.location', '=', 'city.name');
            })->select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();
            // join('contractor_location_ref' , 'contractor_location.id_contractor_location_ref' , 'contractor_location_ref.id_contractor_location_ref')->where('contractor_location_ref.contractor_location_ref_active' , 1)->
            // where('contractor_location_ref.location' , 'city.name')->select('contractor.*')->get()->toArray();

            $property_contractors = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $request->maintenance)->
            join('maintainable','maintenance_job.id_maintenance_job','maintainable.id_maintenance_job')->where('maintainable.maintenable_type' , 'App\Models\Property')->
            join('property' , 'maintainable.maintenable_id' , 'property.id_property')->
            join('city' , 'property.id_city' , 'city.id_city')->
            join('contractor' , 'maintenance_job.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
            join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
            join('contractor_location_ref', function ($join) {
                $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                $join->on('contractor_location_ref.location', '=', 'city.name');
            })->select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();
            // join('contractor_location_ref' , 'contractor_location.id_contractor_location_ref' , 'contractor_location_ref.id_contractor_location_ref')->where('contractor_location_ref.contractor_location_ref_active' , 1)->
            // where('contractor_location_ref.location' , 'city.name')->select('contractor.*');//->get()->toArray();

            // dd($property_contractors);

            $contractors = array_unique(array_merge($room_contractors , $property_contractors) , SORT_REGULAR);


            //$contractors = Contractor::where('contractor_active' , 1)->get();


            // get selected User/agent
            $mjsh = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$request->maintenance )->
            whereNull('staff_end_date_time')->
            where('maintenance_job_staff_history_active' , 1)->get();


            if(count($mjsh) >1){


                return response()->json(
                    [
                    'code' => ActionStatusConstants::FAILURE,
                    'message' => trans('maintenance::maintenance.maintenance_have_multiple_assignee_please_fix_it'),
                    ]);

            }
            $selected_user_agent = null;
            $selected_contractor = null;
            $selected_business = null;
            $contractor_skills=null;
            $coverage_areas=null;
            $users = null;
            $agents = null;

            if(count($mjsh) == 1){
                $selected_user_agent = $mjsh[0]->id_maintenance_assignee;
                $contractor_agent = ContractorAgent::where('id_user' , $selected_user_agent)->
                                 where('contractor_agent_active' , 1)->first();

                $users = User::where('users_active' , 1)->where('is_deleted' , 0)->
                                 join('role_users','role_users.user_id','users.id')->where('role_users_active' , 1)->
                                 join('roles','roles.id','role_users.role_id')->where('roles.name','Maintenance')->get();

                if($contractor_agent){
                    $selected_contractor = Contractor::find($contractor_agent->id_contractor);
                    $agents = Contractor::where('contractor.id_contractor' , $selected_contractor->id_contractor)->
                    join('contractor_agent','contractor_agent.id_contractor','contractor.id_contractor')->
                    join('users','users.id','contractor_agent.id_user')->get();


                    $contractor_skills = ContractorSkill::where('id_contractor' , $selected_contractor->id_contractor)->where('contractor_skill_active' , 1)->
                    join('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref')->get();


                    $coverage_areas = ContractorLocation::where('id_contractor' ,$selected_contractor->id_contractor )->where('contractor_location_active' , 1)->
                    join('contractor_location_ref' , 'contractor_location.id_contractor_location_ref' , 'contractor_location_ref.id_contractor_location_ref')->get();



                }
                else{
                    $selected_business = SaasClientBusiness::where('saas_client_business.id_saas_client_business' ,'>' ,0)->
                                         join('users' , 'users.id_saas_client_business' , 'saas_client_business.id_saas_client_business')->
                                         where('users.id' , $selected_user_agent)->first();

                }

            }


            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.load_business_contractors_was_successful');


        } catch (\Exception $e) {

            Log::error("In maintenance package - in ApiMaintenanceDetailController - getBusinessContractors function" . $e->getMessage());
            $message = $e->getMessage();//trans('maintenance::maintenance_mgt.load_business_contractors_was_unsuccessful');
            $status = APIStatusConstants::BAD_REQUEST;
            $businesses=null;
            $contractors=null;
            $selected_user_agent=null;
            $selected_contractor=null;
            $selected_business=null;
            $contractor_skills=null;
            $coverage_areas=null;
            $users=null;
            $agents=null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'businesses'  => $businesses,
                'contractors'  => $contractors,
                'selected_user_agent'=>$selected_user_agent,
                'selected_contractor'=>$selected_contractor,
                'selected_business'=>$selected_business,
                'contractor_skills'=>$contractor_skills,
                'coverage_areas'=>$coverage_areas,
                'users'=>$users,
                'agents'=>$agents,
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
            Log::info("In maintenance package - in ApiMaintenanceDetailController - getUserAgents function");

            $contractor = null;
            $user_type = null;
            $contractor_skills = null;
            $coverage_areas = null;


            $business_contractor = $request->business_contractor;
            $result=[];
            if($business_contractor && $business_contractor[0] == "B"){

                //return business maintenance users
                $users = User::where('users_active' , 1)->
                join('role_users','role_users.user_id','users.id')->
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










            $status = APIStatusConstants::OK;
            $message = trans('maintenance::maintenance_mgt.load_user_agents_was_successful');


        } catch (\Exception $e) {

            Log::error("In maintenance package - in ApiMaintenanceDetailController - getUserAgents function" . $e->getMessage());
            $message = trans('maintenance::maintenance_mgt.load_user_agents_was_unsuccessful');
            $status = APIStatusConstants::BAD_REQUEST;
            $agents=null;

        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'agents'  => $result,
                'contractor'  => $contractor,
                'user_type' => $user_type,
                'contractor_skills' => $contractor_skills,
                'coverage_areas' => $coverage_areas,
            ]
        );
    }


    public function assignMaintenanceToUser(Request $request)
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
            Log::info("In maintenance package, ApiMaintenanceDetailController- startMaintenanceApi function ");

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
                    $user = Sentinel::findById($staff_user->id);
                    Sentinel::login($user);

                    $response = $this->assignJobToUser($request->maintenance , $request->user ,$staff_user->id );
                    $result = $this->startMaintenance($staff_user->id ,$request->maintenance ,$request->start_date_time);


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

                }
                else{

                    //get api user
                    $api_user = User::where('email' , 'api.user@sdr.uk')->first();
                    $response = $this->assignJobToUser($request->maintenance , $request->user ,$api_user->id );

                    $result = $this->startMaintenance($api_user->id ,$request->maintenance ,$request->start_date_time);
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
                $user = Sentinel::findById($staff_user->id);
                Sentinel::login($user);
                $result = $this->endMaintenance($staff_user->id ,$request->maintenance ,$request->end_date_time);

            }
            else{
                //get api user
                $api_user = User::where('email' , 'api.user@sdr.uk')->first();
                $result = $this->endMaintenance($api_user->id ,$request->maintenance ,$request->end_date_time);


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


    ///////////////////////////////////////////////////////////////////////////
    public function getMaintenanceStatusChartData(Request $request){




        $statuses = MaintenanceJobStatusRef::where('maintenance_job_status_ref_active' , 1)->get();
        $colour_code = ['rgba(95, 190, 170, 0.99)' , 'rgba(26, 188, 156, 0.88)' , 'rgba(93, 156, 236, 0.93)', 'rgba(0, 255, 236, 0.99)', 'rgba(100, 25, 126, 0.99)', 'rgba(10, 25, 216, 0.99)'];







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
    public function getMaintenanceStatusCount(Request $request){




        $statuses = MaintenanceJobStatusRef::where('maintenance_job_status_ref_active' , 1)->get();
        $result = [];

        $counter =0;
        foreach($statuses as $status){

            $maintenances = MaintenanceJob::where('maintenance_job_active' , 1)->where('id_maintenance_job_status' , $status->id_maintenance_job_status_ref)->get();
            $maintenance_count = count($maintenances);

            $result[$status->job_status_code] = $maintenance_count;

        }




        return response()->json(
            [
            'code' => 'success',
            'message' => trans('maintenance::dashboard.status_data_prepared'),
            'status_counts' => $result,
            ]);



    }


    ///////////////////////////////////////////////////////////////////////////
    public function getMaintenanceSlaChartData(Request $request){




        $states = ['Expired' ,'Near to Expire' , 'Not Expired'];
        $colour_code = ['rgba(255, 19, 17, 0.99)' , 'rgba(26, 188, 156, 0.88)' , 'rgba(93, 156, 236, 0.93)', 'rgba(0, 255, 236, 0.99)', 'rgba(100, 25, 126, 0.99)', 'rgba(10, 25, 16, 0.99)'];







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
        $sla_count = ['Expired'=>0,'Near to Expire'=>0,'Not Expired'=>0];


            $maintenaces = MaintenanceJob::where('maintenance_job_active' , 1)->
            join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
            join('maintenance_job_sla', 'maintenance_job_sla.id_maintenance_job' , 'maintenance_job.id_maintenance_job')->where('maintenance_job_sla_active' , 1)->
            join('maintenance_job_sla_ref', 'maintenance_job_sla_ref.id_maintenance_job_sla_ref' , 'maintenance_job_sla.id_maintenance_job_sla_ref')->where('maintenance_job_sla_ref_active' , 1)->
            where('maintenance_job_status_ref.job_status_code' , '!=' , 'CLOS')->get();

            foreach($maintenaces as $maintenance){
                $remain_time = $this->calculateSlaRemainTime($request->business , $maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);

                if($remain_time){
                    $date1 =Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() , $remain_time);
                    $date2 = Carbon::createFromDate('now');
                    $date3 = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() ,$maintenance->job_report_date_time);

                    if($date2->gt($date1)){
                        $sla_count['Expired']++;
                    }
                    else{
                        $sla_show_percent_passed = false;
                        $sla_show_percent_passed = $this->isPassedSlaShowPercent($request->business,$maintenance->expected_target_minutes, $date3 , $date2);
                        if($sla_show_percent_passed){
                            $sla_count['Near to Expire']++;
                        }
                        else{
                            $sla_count['Not Expired']++;
                        }
                    }


                }
            }

            array_push($temp_val->status, 'Expired');
            array_push($temp_val->data, $sla_count['Expired']);
            array_push($temp_val->backgroundColor, $colour_code[$counter]);
            array_push($temp_val->hoverBackgroundColor, $colour_code[$counter++]);



            array_push($temp_val->status, 'Near to Expire');
            array_push($temp_val->data, $sla_count['Near to Expire']);
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
