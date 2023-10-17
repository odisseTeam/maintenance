<?php


namespace Odisse\Maintenance\App\SLP\HistoricalDataAppManagement;


use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\HistoricalMaintenanceJob;

use Complex\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use JWTAuth;

class HistoricalMaintenanceAppManager
{

    public function insertHistory(Model $model)
    {
        $date = date('Y-m-d');

        $user = JWTAuth::user();

        // $user = Sentinel::getUser();
        try {

            if ($model instanceof MaintenanceJob) {

                $oldHistoricalMaintenanceJobs = HistoricalMaintenanceJob::where('history_valid_to', null)->where('id_maintenance_job','=',$model->id_maintenance_job)->get();
                foreach ($oldHistoricalMaintenanceJobs as $oldHistoricalMaintenanceJob) {
                    $oldHistoricalMaintenanceJob->update(
                        [
                            'history_valid_to' => $date
                        ]
                    );
                }

                $oldHistoricalMaintenanceJob = new HistoricalMaintenanceJob();
                foreach ($model->getAttributes() as $key => $value) {
                    $oldHistoricalMaintenanceJob->setAttribute($key, $value);
                }

                $oldHistoricalMaintenanceJob->setAttribute('history_valid_from', $date);
                $oldHistoricalMaintenanceJob->setAttribute('edited_by', $user->id);
                $oldHistoricalMaintenanceJob->save();
                Log::debug("New log saved for Maintenance Job ( " . $model->id_maintenance_job . " ) by user ( " . $user->id . " ) at ( " . $date . " )");
            }
            else
            {
                Log::debug("Try to save new log for Maintenance Job by user ( " . $user->id . " ) at ( " . $date . " ) while original object was not instance of Maintenance Job");
            }
        }
        catch (Exception $e)
        {
            Log::debug("Exception on save new log for Maintenance Job ( " . $model->id_maintenance_job . " ) by user ( " . $user->id . " ) at ( " . $date . " )");
            Log::debug($e->getMessage());
        }

    }
}