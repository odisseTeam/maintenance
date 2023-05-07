<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Odisse\Maintenance\Models\Contractor;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\Maintenance;
use Odisse\Maintenance\Models\MaintenanceJob;
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



        return view('maintenance::maintenance_dashboard',
                    [



                    ]
                );

    }
  /////////////////////////////////////////////////////////////////////////////

  public Function ajaxLoadMaintenances(){


    $user = Sentinel::getUser();

    Log::info(" in MaintenanceDashboardController- ajaxLoadMaintenances function " . " try to load maintenances data  ------- by user " . $user->first_name . " " . $user->last_name);

    $maintenances = MaintenanceJob::where('contractor_job_active' , 1)->where('id_saas_client_business' , $user->id_saas_client_business)->get();



    return response()->json(
        [
          'code' => ActionStatusConstants::SUCCESS,
          'contractors'=>$maintenances,

          'message' => trans('maintenance::dashboard.your_maintenances_loaded'),
        ]);


}



}

