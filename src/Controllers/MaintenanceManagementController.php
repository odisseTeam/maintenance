<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;


use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\SaasClientBusiness;
use App\Models\User;
use App\SLP\Enum\APIStatusConstants;
use App\SLP\Formatter\SystemDateFormats;
use Carbon\Carbon;
use GuzzleHttp\Client;
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
            'code' => 'success',
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

        if($responseObj->status == 200){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
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

                   // dd($request->all());

                    $params =[
                        'business'=>$request->business,
                        'staff_user'=>$user->id
                    ];



                    $response = Http::post($url,$params);

                    $responseObj = json_decode($response->body());

                }
            }



        }
        else{
            //get maintenance of all businesses
        }

        if($responseObj->status == 200){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                  'businesses' => $responseObj->businesses,
                  'contractors' => $responseObj->contractors,
                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
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

        if($responseObj->status == 200){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                  'agents' => $responseObj->agents,
                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
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
                'code' => 'failure',
                'message' => $validator,
                ]);

        }

        try{



            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/assign_user';

                    $params =[
                        'business'=>$request->business,
                        'maintenance'=>$request->maintenance,
                        'user'=>$request->user,
                        'staff_user'=>$user->id
                    ];


                    $response = Http::post($url,$params);

                    $responseObj = json_decode($response->body());

                    //dd($responseObj);

                    return response()->json(
                        [
                          'code' => $responseObj->code,
                          'message' => $responseObj->message,
                        ]);

                }
            }





        }
        catch(\Exception $e){


            Log::error($e->getMessage());
            DB::rollback();


            return response()->json(
                [
                  'code' => 'failure',
                  'result'=>[],
                  'message' => $e->getMessage(),//trans('maintenance::dashboard.assign_maintenance_to_staff_was_not_successful'),
                ]);


        }


    }



    ///////////////////////////////////////////////////////////////////////////

    public function showCreateMaintenancePage()
    {



        $user = Sentinel::getUser();

        Log::info(" in MaintenanceManagementController- showCreateMaintenancePage function " . " try to go to create maintenance page  ------- by user " . $user->first_name . " " . $user->last_name);

        try {

            //get all maintenance category
            $maintenance_category = MaintenanceJobCategoryRef::all();


            //get all businesses
            $saas_client_businesses = SaasClientBusiness::all();

            //get all maintenance priorities
            $priorities = MaintenanceJobPriorityRef::all();

            $locations = $this->getMaintainables();;

            $jobs = MaintenanceJob::all();

            return view(
                'maintenance::mgt_create_maintenance',
                [
                          'maintenance_categories' => $maintenance_category,
                          'saas_client_businesses' => $saas_client_businesses,
                          'priorities' => $priorities,
                          'locations' => $locations,
                          'jobs' => $jobs,


                        ]
            );

        } catch (\Exception $e) {
            Log::error("in MaintenanceController- createNewMaintenancePage function  " . " by user "
            . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

            return view('maintenance::mgt_create_maintenance')->with(['error'=>  trans('maintenance.you_can_not_see_create_maintenance_page')]);

        }


    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxMgtStartMaintenance(Request $request , $id_maintenance){



            $user = Sentinel::getUser();

            Log::info("In maintenance package, MaintenanceManagementController- ajaxStartMaintenance function " . " try to start specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

            if( $request->has('business') and $request->business != null ){


                //get maintenances of specific business

                $businesses = config('maintenances.businesses_name');
                foreach($businesses as $business){
                    if($business['id_saas_client_business'] == $request->business){

                        $url =$business['maintenance_api_url'].'/api/maintenance/start';
                        $params =[
                            'maintenance'=>$id_maintenance,
                            'staff_user'=>$user->id,
                            'start_date_time'=>$request->start_date_time
                        ];


                        $response = Http::post($url,$params);

                        $responseObj = json_decode($response->body());

                    }
                }



            }
            else{
                //get maintenance of all businesses
            }

            return response()->json(
                [
                    'code' => $responseObj->code,
                    'message' => $responseObj->message,
                ]);






    }
    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxMgtEndMaintenance(Request $request , $id_maintenance){



            $user = Sentinel::getUser();

            Log::info("In maintenance package, MaintenanceManagementController- ajaxEndMaintenance function " . " try to end specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

            if( $request->has('business') and $request->business != null ){


                //get maintenances of specific business

                $businesses = config('maintenances.businesses_name');
                foreach($businesses as $business){
                    if($business['id_saas_client_business'] == $request->business){

                        $url =$business['maintenance_api_url'].'/api/maintenance/end';
                        $params =[
                            'maintenance'=>$id_maintenance,
                            'staff_user'=>$user->id,
                            'end_date_time'=>$request->end_date_time
                        ];


                        $response = Http::post($url,$params);

                        $responseObj = json_decode($response->body());

                    }
                }



            }
            else{
                //get maintenance of all businesses
            }

            return response()->json(
                [
                    'code' => $responseObj->code,
                    'message' => $responseObj->message,
                ]);





    }
    /////////////////////////////////////////////////////////////////////////////



    public Function ajaxGetStatusChartData(Request $request){



        $user = Sentinel::getUser();
        $selected_businesses =  $request->businesses; //explode(",", $request->businesses);


        //get labels
        $labels = MaintenanceJobStatusRef::where('maintenance_job_status_ref_active',1)->pluck('job_status_name');
        $datasets=[];
            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if(in_array($business['id_saas_client_business'] , $selected_businesses )){

                    $url =$business['maintenance_api_url'].'/api/maintenance/status/chart_data';
                    $params =[
                        'business'=>$business['id_saas_client_business'],
                    ];

                    $response = Http::post($url,$params);

                    $responseObj = json_decode($response->body());
                    //dd($responseObj);
                    $datasets[$business['business_name']] = $responseObj->result;

                }
            }


            $data = new stdClass();
            $data->labels = $labels;
            $data->datasets = $datasets;

            // dd($data);

            $result = json_encode($data);








        return response()->json(
            [
            'code' => 'success',
            'widget_data'=>$datasets,
            'labels'=>$labels,

            'message' => trans('maintenance::dashboard.your_data_loaded'),
            ]);


    }


    public function createMaintenance( Request $request)
    {
        $business_index = 0;
        $businesses = config('maintenances.businesses_name');

        $url = $businesses[0]['maintenance_api_url'].'/api/maintenance/save/new';



        $data = [];
        if ($request->hasFile('files') ) {
            // get Illuminate\Http\UploadedFile instance
            $files = $request->file('files');

            $index = 1;
            foreach($files as $file) {
                // post request with attachment

                $name = $file->getClientOriginalName();
                if( file_get_contents($file) == "") continue;
                $data[] = [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'files[]',
                    'contents' => file_get_contents($file),
                    'filename' => $name,
                ] ;
            }


        }


        $datum =  $request->all() ;
        unset($datum['files']);
        unset($datum['_token']);


        foreach( $datum as $key=>$value){
            $data[] = [
                'name'  => $key,
                'contents' => $value
            ];

        }
        // dd($data);

        $client = new Client(['headers' => ['Authorization' => 'auth_trusted_header']]);
        $options = [
            'multipart' => $data,
        ];
        $response = $client->post($url, $options);


        /* $req = Http::withoutVerifying()->asForm();

        if ($request->hasFile('files') ) {
            // get Illuminate\Http\UploadedFile instance
            $files = $request->file('files');

            $index = 1;
            foreach($files as $file) {
                // post request with attachment

                $name = $file->getClientOriginalName();
                if( file_get_contents($file) == "") continue;
                $req = $req->attach('attachment'.$index++, file_get_contents($file), $name);
            }

            $datum =  $request->all() ;
            unset($datum['files']);
            unset($datum['_token']);


            $data = [];
            foreach( $datum as $key=>$value){
                $data[] = [
                    'name'  => $key,
                    'contents' => $value
                ];

            }
            // dd($data);
            $response = $req->post($url, $data);
        } else {
            $response = Http::post($url, $request->all());
        }
 */
        return $response;


    }


}

