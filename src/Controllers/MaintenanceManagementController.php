<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;


use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\SaasClientBusiness;
use App\Models\User;
use App\SLP\Enum\APIStatusConstants;
use App\SLP\Formatter\SystemDateFormats;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Odisse\Maintenance\Models\Contractor;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\Maintenance;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;
use Odisse\Maintenance\Models\MaintenanceJobStaffHistory;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use Odisse\Maintenance\Models\MaintenanceLog as ModelsMaintenanceLog;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Sentinel;
use Spatie\LaravelRay\Commands\PublishConfigCommand;
use Validator;

class MaintenanceManagementController extends Controller
{




    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showManagementPage(){



        $user = Sentinel::getUser();

        Log::info(" in Maintenance package MaintenanceDshboardController- showDashboardPage function " . " try to go to maintenance dashboard page  ------- by user " . $user->first_name . " " . $user->last_name);


        $businesses = config('maintenances.businesses_name');
        $categories = MaintenanceJobCategoryRef::where('maintenance_job_category_ref_active' , 1)->get();
        $priorities = MaintenanceJobPriorityRef::where('maintenance_job_priority_ref_active' , 1)->get();
        $statuses = MaintenanceJobStatusRef::where('maintenance_job_status_ref_active' , 1)->get();

        $maintenance_users = User::where('users_active' , 1)->
        join('role_users','role_users.user_id','users.id')->
        join('roles','roles.id','role_users.role_id')->where('roles.name','maintenance')->get();

        $contractor_agents = [];

        $contractors = Contractor::where('contractor_active' , 1)->get();

        return view('maintenance::maintenance_management',
                    [


                        'businesses'=>$businesses,
                        'categories'=>$categories,
                        'priorities'=>$priorities,
                        'statuses'=>$statuses,
                        'contractors'=>$contractors,
                        'maintenance_users'=>$maintenance_users,
                        'contractor_agents'=>$contractor_agents,

                    ]
                );

    }
    /////////////////////////////////////////////////////////////////////////////



    public Function ajaxLoadMaintenances(Request $request){



        $user = Sentinel::getUser();

        $maintenances = [];
        if( $request->has('business') and $request->business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/maintenancelist_details';


                    $response = Http::post($url,$request->all());
                    // $response = Http::post($url,[
                    //     'name' => 'Steve',
                    // ]);

                    $responseObj = json_decode($response->body());
                    //dd($responseObj);

                }
            }



        }
        else{
            //get maintenance of all businesses
        }

        // Log::info(" in MaintenanceDashboardController- ajaxLoadMaintenances function " . " try to load maintenances data  ------- by user " . $user->first_name . " " . $user->last_name);





        return response()->json(
            [
            'code' => ActionStatusConstants::SUCCESS,
            'maintenances'=>$responseObj->maintenances,

            'message' => trans('maintenance::dashboard.your_maintenances_loaded'),
            ]);


    }




    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxMgtDeleteMaintenance(Request $request , $id_maintenance){



        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceManagementController- ajaxDeleteMaintenance function " . " try to delete specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

        if( $request->has('business') and $request->business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/maintenance/delete';


                    $response = Http::post($url,$request->all());

                    $responseObj = json_decode($response->body());

                }
            }



        }
        else{
            //get maintenance of all businesses
        }

        if($responseObj->status == APIStatusConstants::OK){
            return response()->json(
                [
                  'code' => ActionStatusConstants::SUCCESS,
                  'message' => $responseObj->message,
                ]);
        }
        else{
            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => $responseObj->message,
                ]);

        }




    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxLoadBusinessContractors(Request $request){



        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceManagementController- ajaxDeleteMaintenance function " . " try to delete specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

        if( $request->has('business') and $request->business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/business_contractor';


                    $response = Http::post($url,$request->all());

                    $responseObj = json_decode($response->body());

                }
            }



        }
        else{
            //get maintenance of all businesses
        }

        if($responseObj->status == APIStatusConstants::OK){
            return response()->json(
                [
                  'code' => ActionStatusConstants::SUCCESS,
                  'message' => $responseObj->message,
                  'businesses' => $responseObj->businesses,
                  'contractors' => $responseObj->contractors,
                ]);
        }
        else{
            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => $responseObj->message,
                  'businesses' => $responseObj->businesses,
                  'contractors' => $responseObj->contractors,
                ]);

        }




    }
    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxLoadMgtUserAgents(Request $request){



        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceManagementController- ajaxDeleteMaintenance function " . " try to delete specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

        if( $request->has('business') and $request->business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/user_agents';


                    $response = Http::post($url,$request->all());

                    $responseObj = json_decode($response->body());

                }
            }



        }
        else{
            //get maintenance of all businesses
        }

        if($responseObj->status == APIStatusConstants::OK){
            return response()->json(
                [
                  'code' => ActionStatusConstants::SUCCESS,
                  'message' => $responseObj->message,
                  'agents' => $responseObj->agents,
                ]);
        }
        else{
            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => $responseObj->message,
                  'agents' => $responseObj->agents,
                ]);

        }




    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxMgtAssignMaintenanceToUser(Request $request){



        $user = Sentinel::getUser();

        Log::info("In maintenance package, MaintenanceDashboardController- ajaxMgtAssignMaintenanceToUser function " . " try to assign maintenance to user  ------- by user " . $user->first_name . " " . $user->last_name);

        $validator = Validator::make($request->all(), [

            'business' => 'required|numeric',
            'maintenance' => 'required|numeric',
            'user' => 'required|numeric',

        ]);

        if ($validator->fails()) {

            Log::error("In maintenance package, MaintenanceDashboardController- ajaxMgtAssignMaintenanceToUser function ".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);



            return response()->json(
                [
                'code' => ActionStatusConstants::FAILURE,
                'message' => $validator,
                ]);

        }

        try{



            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/assign_user';


                    $response = Http::post($url,$request->all());

                    $responseObj = json_decode($response->body());

                }
            }





        }
        catch(\Exception $e){


            Log::error($e->getMessage());
            DB::rollback();


            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'result'=>[],
                  'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_not_successful'),
                ]);


        }


    }




}

