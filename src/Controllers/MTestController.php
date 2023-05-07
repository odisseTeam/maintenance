<?php

namespace Odisse\Maintenance\Controllers;

use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use App\Models\SaasClientBusiness;
use App\Http\Controllers\Controller;
use Sentinel;
use Validator;

class MTestController extends Controller
{
    public function testFunc()
    {
        return view('maintenance::test',['title' => 'sample component']);
    }



    public function newTest(){
        return view('maintenance::create_maintenance');
    }



    public function createNewMaintenancePage()
  {

    $user = Sentinel::getUser();

    Log::info(" in MTestController- createNewMaintenancePage function " . " try to go to create maintenance page  ------- by user " . $user->first_name . " " . $user->last_name);

    //get all maintenance category
    $maintenance_category = MaintenanceJobCategoryRef::all();

    //get all businesses
    $saas_client_businesses = SaasClientBusiness::all();

    //get all maintenance priorities
    $priorities = MintenanceJobPriorityRef::all();


    return view('maintenance.create_maintenance',
                [
                  'maintenance_categories' => $maintenance_category,
                  'saas_client_businesses' => $saas_client_businesses,
                  'priorities' => $priorities,


                ]
            );

  }
}
