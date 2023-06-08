<?php


namespace Odisse\Maintenance\App\SLP\HistoricalDataManagement;


use Odisse\Maintenance\Models\HistoricalContractor;
use Odisse\Maintenance\Models\Contractor;

use Complex\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Sentinel;

class HistoricalContractorManager
{

    public function insertHistory(Model $model)
    {
        $date = date('Y-m-d');

        $user = Sentinel::getUser();
        try {

            if ($model instanceof Contractor) {

                $oldHistoricalContractors = HistoricalContractor::where('history_valid_to', null)->where('id_contractor','=',$model->id_contractor)->get();
                foreach ($oldHistoricalContractors as $oldHistoricalContractor) {
                    $oldHistoricalContractor->update(
                        [
                            'history_valid_to' => $date
                        ]
                    );
                }

                $oldHistoricalContractor = new HistoricalContractor();
                foreach ($model->getAttributes() as $key => $value) {
                    $oldHistoricalContractor->setAttribute($key, $value);
                }

                $oldHistoricalContractor->setAttribute('history_valid_from', $date);
                $oldHistoricalContractor->setAttribute('edited_by', Sentinel::getUser()->id);
                $oldHistoricalContractor->save();
                Log::debug("New log saved for Contractor ( " . $model->id_contractor . " ) by user ( " . Sentinel::getUser()->id . " ) at ( " . $date . " )");
            }
            else
            {
                Log::debug("Try to save new log for Contractor by user ( " . Sentinel::getUser()->id . " ) at ( " . $date . " ) while original object was not instance of Contractor");
            }
        }
        catch (Exception $e)
        {
            Log::debug("Exception on save new log for Contractor ( " . $model->id_contractor . " ) by user ( " . Sentinel::getUser()->id . " ) at ( " . $date . " )");
            Log::debug($e->getMessage());
        }

    }
}