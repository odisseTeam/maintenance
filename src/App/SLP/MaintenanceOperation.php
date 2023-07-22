<?php
namespace Odisse\Maintenance\App\SLP;

use App\Models\Room;
use App\SLP\Com\Configuration\SaasClientBusinessConfiguration;
use App\SLP\Formatter\SystemDateFormats;
use Carbon\Carbon;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobStatusHistory;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use Odisse\Maintenance\Models\MaintenanceLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Odisse\Maintenance\App\SLP\HistoricalDataManagement\HistoricalMaintenanceManager;
use Odisse\Maintenance\Models\Maintainable;

/**
 * Created by PhpStorm.
 * User: hedi
 * Date: 10/24/2019
 * Time: 3:58 PM
 */
trait MaintenanceOperation
{


    private function startMaintenance($id_user , $id_maintenance , $start_datetime){

        try {



            DB::beginTransaction();

            $status = MaintenanceJobStatusRef::where('job_status_code' ,'INPR' )->where('maintenance_job_status_ref_active' , 1)->first();
            if($status){


                $maintenance = MaintenanceJob::find($id_maintenance);



                $maintenance->update([
                    'id_maintenance_job_status' => $status->id_maintenance_job_status_ref,
                    'job_start_date_time' => $start_datetime,
                    'job_finish_date_time' => null,
                ]);


              $HistoricalMaintenanceManager = new HistoricalMaintenanceManager();
              $HistoricalMaintenanceManager->insertHistory($maintenance);
                $now = Carbon::createFromDate('now');


                $maintenance_log = new MaintenanceLog([
                    'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                    'id_staff'    =>  $id_user,
                    'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'log_note'  =>  trans('maintenance::dashboard.start_maintenance_by_user'),

                ]);
                $maintenance_log->save();


                $old_maintenance_status_history = MaintenanceJobStatusHistory::where('id_maintenance_job' , $maintenance->id_maintenance_job )->whereNull('maintenance_status_end_date_time')->first();
                $old_maintenance_status_history->update([
                    'maintenance_status_end_date_time'  =>  $now->format(SystemDateFormats::getDateTimeFormat()),
                ]);


                $maintenance_status_history = new MaintenanceJobStatusHistory([
                    'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                    'id_maintenance_staff'    =>  $id_user,
                    'id_maintenance_job_status'    =>  $status->id_maintenance_job_status_ref,
                    'maintenance_status_start_date_time'    =>  $now->format(SystemDateFormats::getDateTimeFormat()),
                    'maintenance_job_status_history_active'    =>  1,
                ]);
                $maintenance_status_history->save();



                //get all locations of maintenance
                $maintainables = Maintainable::where('id_maintenance_job' ,$maintenance->id_maintenance_job )->where('maintainable_active' , 1)->where('maintenable_type' , 'LIKE',"%Room%")->get();
                foreach($maintainables as $maintainable){

                    //change room_maintenance_status field of room
                    $maintenance_status = MaintenanceJobStatusRef::find($maintenance->id_maintenance_job_status);
                    $this->changeRoomMaintenanceStatus($maintenance_status->job_status_code , $maintainable->maintenable_id);


                }

                DB::commit();



                return[
                    'code'  =>  'success',
                    'message'  =>  trans('maintenance::dashboard.maintenance_started_successfully'),
                ];


            }
            else{
                return [
                    'code' => 'failure',
                    'message' => trans('maintenance::dashboard.something_wrong_start_status_not_found'),
                ];
            }

        } catch (\Exception $e) {


            Log::error($e->getMessage());
            DB::rollback();


            return
                [
                'code' => 'failure',
                'message' => $e->getMessage(),//trans('maintenance::dashboard.start_maintenance_was_not_successful'),
                ];


        }



    }




    private function endMaintenance($id_user , $id_maintenance , $end_datetime){

        try {



            DB::beginTransaction();

            $status = MaintenanceJobStatusRef::where('job_status_code' ,'CLOS' )->where('maintenance_job_status_ref_active' , 1)->first();
            Log::info("e");
            if($status){

                Log::info("e1");
                $maintenance = MaintenanceJob::find($id_maintenance);
                Log::info("e5");
                $maintenance->update([
                    'id_maintenance_job_status' => $status->id_maintenance_job_status_ref,
                    'job_finish_date_time' => $end_datetime,
                ]);
                Log::info("e6");

              $HistoricalMaintenanceManager = new HistoricalMaintenanceManager();
              $HistoricalMaintenanceManager->insertHistory($maintenance);
              Log::info("e2");

                $now = Carbon::createFromDate('now');


                $maintenance_log = new MaintenanceLog([
                    'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                    'id_staff'    =>  $id_user,
                    'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                    'log_note'  =>  trans('maintenance::dashboard.end_maintenance_by_user'),

                ]);
                $maintenance_log->save();

                $old_maintenance_status_history = MaintenanceJobStatusHistory::where('id_maintenance_job' , $maintenance->id_maintenance_job )->whereNull('maintenance_status_end_date_time')->first();
                $old_maintenance_status_history->update([
                    'maintenance_status_end_date_time'  =>  $now->format(SystemDateFormats::getDateTimeFormat()),
                ]);

                $maintenance_status_history = new MaintenanceJobStatusHistory([
                    'id_maintenance_job'    =>  $maintenance->id_maintenance_job,
                    'id_maintenance_staff'    =>  $id_user,
                    'id_maintenance_job_status'    =>  $status->id_maintenance_job_status_ref,
                    'maintenance_status_start_date_time'    =>  $now->format(SystemDateFormats::getDateTimeFormat()),
                    'maintenance_job_status_history_active'    =>  1,
                ]);
                $maintenance_status_history->save();



                //get all locations of maintenance
                $maintainables = Maintainable::where('id_maintenance_job' ,$maintenance->id_maintenance_job )->where('maintainable_active' , 1)->where('maintenable_type' , 'LIKE',"%Room%")->get();
                foreach($maintainables as $maintainable){

                    //change room_maintenance_status field of room
                    $maintenance_status = MaintenanceJobStatusRef::find($maintenance->id_maintenance_job_status);
                    $this->changeRoomMaintenanceStatus($maintenance_status->job_status_code , $maintainable->maintenable_id);


                }


                DB::commit();



                return[
                    'code'  =>  'success',
                    'message'  =>  trans('maintenance::dashboard.maintenance_ended_successfully'),
                ];


            }
            else{
                return [
                    'code' => 'failure',
                    'message' => trans('maintenance::dashboard.something_wrong_end_status_not_found'),
                ];
            }

        } catch (\Exception $e) {


            Log::error($e->getMessage() . $e->getLine());
            DB::rollback();


            return
                [
                'code' => 'failure',
                'message' => $e->getMessage(),//trans('maintenance::dashboard.end_maintenance_was_not_successful'),
                ];


        }



    }




    private function calculateSlaRemainTime($id_saas_client_business,$id_maintenance , $job_report_date_time ,$expected_target_minutes ){

        $maintenance = MaintenanceJob::find($id_maintenance);
        if(!$maintenance){
            return null;
        }

        $now = Carbon::createFromDate('now');
        $api_url = config('app.url').'/api/get_holidays';
        $holiday_objs = $this->getHolidaysOfBusiness($id_saas_client_business , $now->format('Y') , $api_url);
        $holidays=[];
        foreach($holiday_objs as $obj){
            $holidays[] = $obj->calendar_date;
        }


        if($expected_target_minutes){
            $expected_target_hour = $expected_target_minutes /60;
            $days_count = $expected_target_hour/ 8 ;
            $base_date_time = $job_report_date_time;
            $counter=1;
            while($counter<=$days_count){
                $base_date_time =Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() , $base_date_time)->addDay()->format(SystemDateFormats::getDateTimeFormat());


                if(in_array(Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() , $base_date_time)->format('Y-m-d'), $holidays)){
                    //if day is holiday
                }
                else{
                    //if day is not holiday
                    $counter++;

                }


            }

            $minute_count = (fmod($expected_target_hour , 8) * 60) + (fmod($expected_target_minutes ,60));
            $base_date_time =Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() , $base_date_time)->addMinutes($minute_count);

            return $base_date_time->format(SystemDateFormats::getDateTimeFormat());
        }
        else{
            return null;

        }

    }


    private function getDateTimeFormat($key_format){

        $dates_format = [

            'date_format' => 'm/d/Y',
            'date_format_javascript' => 'mm/dd/yyyy',
            'date_format_moment' => 'MMM/DD/yyyy',
            'date_format_vue' => 'MMM/dd/yyyy',

            'date_time_format_moment' => 'mm/DD/yyyy HH:mm:ss',
            'date_time_format' => 'd-m-Y H:i:s',
            'date_time_format_validation' => 'd-m-Y H:i',
            'date_time_format_javascript' => 'DD-MM-YYYY H:i',
            'date_time_format_vue' => 'DD-MM-YYYY H:i',
        ];


        return $dates_format[$key_format];
    }



    private function getHolidaysOfBusiness($id_saas_client_business ,$year , $api_url='https://living.odisse.local/api/get_holidays'){


        $params =[
            'year'=>$year,
            'id_saas_client_business'=>$id_saas_client_business,
        ];


        $response = Http::post($api_url,$params);

        $responseObj = json_decode($response->body());
        if($responseObj){
            return $responseObj->holidays;
        }
        return null;


    }


    private function isPassedSlaShowPercent($id_saas_client_business , $sla_minutes, $first_date_time  , $source_date_time){

        $configs = new SaasClientBusinessConfiguration($id_saas_client_business);
        $sla_show_percent = $configs->getSlaShowPercent();

        $percent = ($first_date_time->diffInMinutes($source_date_time) * 100 )/$sla_minutes;


        if($percent>$sla_show_percent)
            return true;

        return false;

    }


    private function changeRoomMaintenanceStatus($maintenance_status_code , $room_id){

        //dd($maintenance_status_code ."***". $room_id);
        //get room info
        $room = Room::find($room_id);
        if($room){

            if($maintenance_status_code == "CLOS"){

                $room->update([
                    'room_maintenance_status' => 1
                ]);

            }
            else if($maintenance_status_code == "OPUN" ){

                $room->update([
                    'room_maintenance_status' => 0
                ]);

            }
        }
    }




}
