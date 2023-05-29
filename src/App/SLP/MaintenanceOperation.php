<?php
namespace Odisse\Maintenance\App\SLP;

use App\SLP\Formatter\SystemDateFormats;
use Carbon\Carbon;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobStatusHistory;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use Odisse\Maintenance\Models\MaintenanceLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            if($status){


                $maintenance = MaintenanceJob::find($id_maintenance);
                $maintenance->update([
                    'id_maintenance_job_status' => $status->id_maintenance_job_status_ref,
                    'job_finish_date_time' => $end_datetime,
                ]);

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


            Log::error($e->getMessage());
            DB::rollback();


            return
                [
                'code' => 'failure',
                'message' => $e->getMessage(),//trans('maintenance::dashboard.end_maintenance_was_not_successful'),
                ];


        }



    }




    private function calculateSlaRemainTime($id_maintenance , $job_report_date_time ,$expected_target_minutes ){

        $maintenance = MaintenanceJob::find($id_maintenance);
        if(!$maintenance){
            return null;
        }

        if($expected_target_minutes){
                $time = Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat() , $job_report_date_time )->addMinutes($expected_target_minutes);
                return $time->format(SystemDateFormats::getDateTimeFormat());
        }
        else{
            return null;

        }

    }




}
