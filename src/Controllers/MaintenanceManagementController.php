<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;


use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\SaasClientBusiness;
use App\Models\User;
use App\SLP\Enum\APIStatusConstants;
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
use Odisse\Maintenance\App\SLP\MaintenanceOperation;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Sentinel;
use Spatie\LaravelRay\Commands\PublishConfigCommand;
use stdClass;
use Validator;

class MaintenanceManagementController extends Controller
{

    use MaintenanceOperation;




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

        $responseObj= null;

        $maintenances = [];
        if( $request->has('business') and $request->business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/maintenancelist_details';

                    $params =[
                        'business'=>$request->business,
                        'category'=>$request->category,
                        'priority'=>$request->priority,
                        'status'=>$request->status,
                        'title'=>$request->title,
                        'start_date'=>$request->start_date,
                        'end_date'=>$request->end_date,
                        'assignee'=>$request->assignee,
                    ];



                    $response = Http::post($url,$params);

                    $responseObj = json_decode($response->body());

                }
            }



        }


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


    public Function ajaxLoadBusinessContractors(Request $request ){



        $user = Sentinel::getUser();
        $staff_user = User::find($user->id);

        Log::info("In maintenance package, MaintenanceManagementController- ajaxDeleteMaintenance function " . " try to delete specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

        if( $request->has('business') and $request->business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/business_contractor';

                   // dd($request->all());

                    $params =[
                        'maintenance'=>$request->maintenance,
                        'business'=>$request->business,
                        'staff_user'=>$staff_user->email
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
                  'selected_user_agent'=>$responseObj->selected_user_agent,
                  'selected_contractor'=>$responseObj->selected_contractor,
                  'selected_business'=>$responseObj->selected_business,
                  'users'=>$responseObj->users,
                  'agents'=>$responseObj->agents,
                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,
                  'businesses' => $responseObj->businesses,
                  'contractors' => $responseObj->contractors,
                  'selected_user_agent'=>$responseObj->selected_user_agent,
                  'selected_contractor'=>$responseObj->selected_contractor,
                  'selected_business'=>$responseObj->selected_business,
                  'users'=>$responseObj->users,
                  'agents'=>$responseObj->agents,
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
        $staff_user = User::find($user->id);


        Log::info("In maintenance package, MaintenanceDashboardController- ajaxMgtAssignMaintenanceToUser function " . " try to assign maintenance to user  ------- by user " . $user->first_name . " " . $user->last_name);

        // $validator = Validator::make($request->all(), [

        //     'business' => 'required|numeric',
        //     'maintenance' => 'required|numeric',
        //     'user' => 'required|numeric',

        // ]);

        // if ($validator->fails()) {

        //     Log::error("In maintenance package, MaintenanceDashboardController- ajaxMgtAssignMaintenanceToUser function ".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);



        //     return response()->json(
        //         [
        //         'code' => 'failure',
        //         'message' => $validator->errors(),
        //         ]);

        // }

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
                        'staff_user'=>$staff_user->email,
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


        $businesses = config('maintenances.businesses_name');

        $url = $businesses[0]['maintenance_api_url'].'/api/maintenance/get_data_to_create';

        $params =[
            'user'=>$user->id,
        ];

        $data = Http::get($url,$params);

        $objects = json_decode( $data->body() );


        //get all maintenance category
        $maintenance_category =$objects->maintenance_category;


        //get all businesses
        $saas_client_businesses = SaasClientBusiness::all();

        //get all maintenance priorities
         //get all maintenance priorities
         $priorities = $objects->priorities;

         $locations = $objects->locations;

         $jobs = $objects->jobs;

         $businesses = config('maintenances.businesses_name');


        //dd($maintenance_category, $locations, $priorities);
        return view(
            'maintenance::mgt_create_maintenance',
            [
                        'maintenance_categories' => $maintenance_category,
                        'saas_client_businesses' => $saas_client_businesses,
                        'businesses' => $businesses,
                        'priorities' => $priorities,
                        'locations' => $locations,
                        'jobs' => $jobs,


                    ]
        );




    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxMgtStartMaintenance(Request $request , $id_maintenance){



            $user = Sentinel::getUser();
            $staff_user = User::find($user->id);


            Log::info("In maintenance package, MaintenanceManagementController- ajaxStartMaintenance function " . " try to start specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);


            if( $request->has('business') and $request->business != null ){


                //get maintenances of specific business

                $businesses = config('maintenances.businesses_name');
                foreach($businesses as $business){
                    if($business['id_saas_client_business'] == $request->business){

                        $url =$business['maintenance_api_url'].'/api/maintenance/start';
                        $params =[
                            'maintenance'=>$id_maintenance,
                            'staff_user'=>$staff_user->email,
                            'start_date_time'=>$request->start_date_time
                        ];


                        $response = Http::post($url,$params);

                        $responseObj = json_decode($response->body());

                    }
                }



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
        $staff_user = User::find($user->id);

        //dd($this->getDateTimeFormat('date_time_format_javascript'));

        Log::info("In maintenance package, MaintenanceManagementController- ajaxEndMaintenance function " . " try to end specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

        // $validator = Validator::make($request->all(), [

        //     'end_date_time' => 'required|date_format:'.$this->getDateTimeFormat('date_time_format_javascript'),

        // ]);

        // if ($validator->fails()) {

        //     Log::error("In maintenance package, MaintenanceManagementController- ajaxMgtEndMaintenance function ".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

        //     return response()->json(
        //         [
        //         'code' => 'failure',
        //         'message' => $validator->errors(),
        //         ]);

        // }

        // $maintenance = MaintenanceJob::find($id_maintenance);

        // if(!$maintenance->job_start_date_time){


        //     Log::error("In maintenance package, MaintenanceDashboardController- ajaxEndMaintenance function ".": ". 'maintenance start date must have start date for this action! ' ." by user ".$user->first_name . " " . $user->last_name);



        //     return response()->json(
        //         [
        //         'code' => 'failure',
        //         'message' => trans('maintenance::dashboard.maintenance_must_have_start_date_for_this_action'),
        //         ]);


        // }

        // if(Carbon::createFromFormat($this->getDateTimeFormat('date_time_format'), $maintenance->job_start_date_time)->gt(Carbon::createFromFormat($this->getDateTimeFormat('date_time_format_javascript'), $request->end_date_time))){

        //     Log::error("In maintenance package, MaintenanceManagementController- ajaxMgtEndMaintenance function ".": ". 'maintenance start date is after maintenance end date! ' ." by user ".$user->first_name . " " . $user->last_name);

        //     return response()->json(
        //         [
        //         'code' => 'failure',
        //         'message' => trans('maintenance::dashboard.start_date_is_after_end_date'),
        //         ]);

        // }

        if( $request->has('business') and $request->business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/maintenance/end';
                    $params =[
                        'maintenance'=>$id_maintenance,
                        'staff_user'=>$staff_user->email,
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

                    $datasets[] = $responseObj->result;

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
            'widget_data'=>$data,
            'labels'=>$labels,

            'message' => trans('maintenance::dashboard.your_data_loaded'),
            ]);


    }

    /////////////////////////////////////////////////////////////////////////////



    public Function ajaxGetSlaChartData(Request $request){



        $user = Sentinel::getUser();
        $selected_businesses =  $request->businesses; //explode(",", $request->businesses);


        //get labels
        $labels = ['Expired' ,'Near to Expire' , 'Not Expired'];

        $datasets=[];
            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if(in_array($business['id_saas_client_business'] , $selected_businesses )){

                    $url =$business['maintenance_api_url'].'/api/maintenance/sla/chart_data';
                    $params =[
                        'business'=>$business['id_saas_client_business'],
                    ];

                    $response = Http::post($url,$params);

                    $responseObj = json_decode($response->body());

                    $datasets[] = $responseObj->result;

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
            'widget_data'=>$data,
            'labels'=>$labels,

            'message' => trans('maintenance::dashboard.your_data_loaded'),
            ]);


    }

    /////////////////////////////////////////////////////////////////////////////


    public function createMaintenance( Request $request)
    {

        $user = Sentinel::getUser();


        // dd("***");
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

        if ($request->has('locations') ) {
            // get Illuminate\Http\UploadedFile instance
            $locations = $request->get('locations');

            $index = 1;
            foreach($locations as $location) {
                // post request with attachment
                $data[] = [
                    'name' => 'locations[]',
                    'contents' => $location,
                ] ;
            }

        }


        $datum =  $request->all() ;
        unset($datum['files']);
        unset($datum['_token']);
        unset($datum['locations']);
        $datum['user'] = $user->id;

        foreach( $datum as $key=>$value){
            $data[] = [
                'name'  => $key,
                'contents' => $value
            ];

        }

        $client = new Client(['headers' => ['Authorization' => 'auth_trusted_header']]);
        $options = [
            'multipart' => $data,
        ];

        try {
            $response = $client->post($url, $options);

        }
        catch(\Exception $e){
        }

        return redirect('/maintenance/mgt/create')->with(['success' => 'maintenance created']);

    }


}

