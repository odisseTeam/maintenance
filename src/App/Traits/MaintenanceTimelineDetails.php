<?php
/**
 * Created by PhpStorm.
 * User: hedi
 * Date: 1/13/20
 * Time: 12:11 PM
 */

 namespace Odisse\Maintenance\App\Traits;

use App\SLP\Enum\GeneralConfigConstants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;
use Odisse\Maintenance\App\SLP\Enum\BookingStatusConstants;
use Illuminate\Support\Str;
use Odisse\Maintenance\Models\MaintenanceLog;

trait MaintenanceTimelineDetails{

    private function getMaintenanceTimelineInfo($maintenanceId ){

       

        $query = DB::table('maintenance_log')
        ->join('maintenance_job', 'maintenance_log.id_maintenance_job', '=', 'maintenance_job.id_maintenance_job')
        ->leftjoin('maintenance_job_staff_history', 'maintenance_log.id_maintenance_job', '=', 'maintenance_job_staff_history.id_maintenance_job')
        ->leftjoin('maintenance_job_status_history', 'maintenance_log.id_maintenance_job', '=', 'maintenance_job_status_history.id_maintenance_job')
        ->leftjoin('maintenance_job_priority_history', 'maintenance_log.id_maintenance_job', '=', 'maintenance_job_priority_history.id_maintenance_job')
        ->leftjoin('users', 'maintenance_log.id_staff', '=', 'users.id')
        ->where('maintenance_log.id_maintenance_job',$maintenanceId)
        ->select('maintenance_log.*',DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS staff"))
        ->groupBy('maintenance_log.id_maintenance_log')
        ->groupBy('users.first_name')
        ->groupBy('users.last_name')

        ->orderBy('log_date_time', 'asc')
        ->get();

        foreach($query as $qry){
            if ($contains = Str::contains($qry->log_note, 'Created')){
                $header = $qry->staff.' Created a New Maintenance';
                $qry->header = $header;

            }else{
                $header = $qry->staff.' Changed the Maintenance';
                $qry->header = $header;
            }


        }
         

        return $query;



    }
}
