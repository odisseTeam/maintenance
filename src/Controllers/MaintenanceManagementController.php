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
use Odisse\Maintenance\Models\ContractorSkillRef;
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

        Log::info("In Maintenance package MaintenanceManagementController- showManagementPage function " . " try to go to maintenance management page  ------- by user " . $user->first_name . " " . $user->last_name);


        $businesses = config('maintenances.businesses_name');
        $business = $businesses[0];

        $url =$business['maintenance_api_url'].'/api/maintenance/get_ref_date';


        $response = Http::post($url,[]);

        $responseObj = json_decode($response->body());

        if($responseObj){

            $categories = $responseObj->categories;
            $priorities = $responseObj->priorities;
            $statuses = $responseObj->statuses;
            $skills = $responseObj->skills;

        }
        else{

        $categories = [];
        $priorities = [];
        $statuses = [];
        $skills = [];


        }


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
                        'skills'=>$skills,

                    ]
                );

    }
    /////////////////////////////////////////////////////////////////////////////



    public Function ajaxLoadMaintenances(Request $request){


        Log::info("In Maintenance package MaintenanceManagementController- ajaxLoadMaintenances function ");

        $user = Sentinel::getUser();

        $responseObj= null;

        $maintenances = [];
        if( $request->has('business') and $request->business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            //dd($businesses);
            foreach($businesses as $business){
                if(in_array($business['id_saas_client_business'] , $request->business)){
                    //dd($request->business);

                    $url =$business['maintenance_api_url'].'/api/maintenancelist_details';

                    $params =[
                        'business'=>$business['id_saas_client_business'],
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
                    //dd($responseObj);
                    $maintenances = array_merge($responseObj->maintenances , $maintenances);


                }


            }



        }



        return response()->json(
            [
            'code' => 'success',
            'maintenances'=>$maintenances,

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

        Log::info("In maintenance package, MaintenanceManagementController- ajaxLoadBusinessContractors function " . " try to load business and contractors  ------- by user " . $user->first_name . " " . $user->last_name);

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

        if($responseObj && $responseObj->status == 200){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                  'businesses' => $responseObj->businesses,
                  'contractors' => $responseObj->contractors,
                  'selected_user_agent'=>$responseObj->selected_user_agent,
                  'selected_contractor'=>$responseObj->selected_contractor,
                  'selected_business'=>$responseObj->selected_business,
                  'contractor_skills'=>$responseObj->contractor_skills,
                  'coverage_areas'=>$responseObj->coverage_areas,
                  'users'=>$responseObj->users,
                  'agents'=>$responseObj->agents,
                ]);
        }
        elseif($responseObj){
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,
                  'businesses' => $responseObj->businesses,
                  'contractors' => $responseObj->contractors,
                  'selected_user_agent'=>$responseObj->selected_user_agent,
                  'selected_contractor'=>$responseObj->selected_contractor,
                  'selected_business'=>$responseObj->selected_business,
                  'contractor_skills'=>$responseObj->contractor_skills,
                  'coverage_areas'=>$responseObj->coverage_areas,
                  'users'=>$responseObj->users,
                  'agents'=>$responseObj->agents,
                ]);

        }

        else{
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => '',
                  'businesses' => [],
                  'contractors' => [],
                  'selected_user_agent'=>null,
                  'selected_contractor'=>null,
                  'selected_business'=>null,
                  'contractor_skills'=>[],
                  'coverage_areas'=>[],
                  'users'=>[],
                  'agents'=>[],
                ]);

        }




    }
    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxLoadMgtUserAgents(Request $request){



        $user = Sentinel::getUser();

        //dd($request->all());

        Log::info("In maintenance package, MaintenanceManagementController- ajaxLoadMgtUserAgents function " . " try to load users and agents  ------- by user " . $user->first_name . " " . $user->last_name);

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
                  'contractor' =>$responseObj->contractor,
                  'contractor_skills' =>$responseObj->contractor_skills,
                  'coverage_areas' =>$responseObj->coverage_areas,
                  'user_type' =>$responseObj->user_type
                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,
                  'agents' => $responseObj->agents,
                  'contractor' =>$responseObj->contractor,
                  'user_type' =>$responseObj->user_type
                ]);

        }




    }

    ///////////////////////////////////////////////////////////////////////////


    public Function ajaxMgtAssignMaintenanceToUser(Request $request){
        $user = Sentinel::getUser();


        $validator = Validator::make($request->all(), [

            'maintenance' => 'required|numeric',
            'user' => 'required|numeric',

        ]);

        if ($validator->fails()) {

            Log::error("In maintenance package, MaintenanceManagementController- ajaxMgtAssignMaintenanceToUser function ".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);



            return response()->json(
                [
                'code' => 'failure',
                'message' => $validator->errors(),
                ]);

        }



        $staff_user = User::find($user->id);


        Log::info("In maintenance package, MaintenanceManagementController- ajaxMgtAssignMaintenanceToUser function " . " try to assign maintenance to user  ------- by user " . $user->first_name . " " . $user->last_name);

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


            Log::error("In maintenance package, MaintenanceManagementController- ajaxMgtAssignMaintenanceToUser function " . $e->getMessage());
            DB::rollback();


            return response()->json(
                [
                  'code' => 'failure',
                  'result'=>[],
                  'message' => trans('maintenance::dashboard.assign_maintenance_to_staff_was_not_successful'),
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



         $skills = ContractorSkillRef::where('contractor_skill_ref_active' , 1)->get();
         $contactors = [];
         $users = null;
         $agents = null;


        //dd($maintenance_category, $locations, $priorities);
        return view(
            'maintenance::mgt_create_maintenance',
            [
                        'maintenance_categories' => $maintenance_category,
                        'saas_client_businesses' => $saas_client_businesses,
                        'skills' => $skills,
                        'businesses' => $businesses,
                        'contactors' => $contactors,
                        'users' => $users,
                        'agents' => $agents,
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
                            'start_date_time'=>$request->start_date_time,
                            'user'=>$request->user
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

        Log::info("In maintenance package, MaintenanceManagementController- ajaxMgtEndMaintenance function " . " try to end specific maintenance  ------- by user " . $user->first_name . " " . $user->last_name);

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

        $labels = [];

        $selected_businesses =  $request->businesses; //explode(",", $request->businesses);


        //get labels

        $businesses = config('maintenances.businesses_name');



        $business = $businesses[0];

        $url =$business['maintenance_api_url'].'/api/maintenance/get_ref_date';


        $response = Http::post($url,[]);

        $responseObj = json_decode($response->body());

        if($responseObj){
            $statuses = $responseObj->statuses;
        }
        else{

        $statuses = [];


        }

        foreach($statuses as $status){
            $labels[] = $status->job_status_name;

        }


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

        Log::info("In Maintenance package -  MaintenanceManagementController- ajaxGetSlaChartData function ");



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

        $business_index = 0;
        $businesses = config('maintenances.businesses_name');

        $url = $businesses[0]['maintenance_api_url'].'/api/maintenance/save/new';


        Log::info("In Maintenance package - MaintenanceManagementController createMaintenance function - In the controller, url is " . $url);

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


        if ($request->has('user_agent') ) {

            $data[] = [
                'name' => 'user_agent',
                'contents' => $request->user_agent,
            ] ;


        }
        if ($request->has('commencement_date') ) {

            $data[] = [
                'name' => 'commencement_date',
                'contents' => $request->commencement_date,
            ] ;


        }

        if ($request->has('complete_date') ) {

            $data[] = [
                'name' => 'complete_date',
                'contents' => $request->complete_date,
            ] ;


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
        Log::info("data is " . print_r( $options, true ));

        try {
            $response = $client->post($url, $options);
            $responseObj = json_decode($response->getBody());

            return redirect('/maintenance/mgt/create')->with([$responseObj->status => $responseObj->message]);


        }
        catch(\Exception $e){
            Log::info("Exception on call api ". $e->getMessage() . $e->getLine());
            return redirect('/maintenance/mgt/create')->with(['error' => $e->getMessage()]);

        }

    }


    /////////////////////////////////////////////////////////////////////////////
    public function ajaxGetContractorsWithSkill(Request $request){



        $user = Sentinel::getUser();
        $staff_user = User::find($user->id);

        Log::info("In maintenance package, MaintenanceManagementController- ajaxGetContractorsWithSkill function " . " try to get contractor with skill ------- by user " . $user->first_name . " " . $user->last_name);

        if( $request->has('business') and $request->business != null ){



            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->business){

                    $url =$business['maintenance_api_url'].'/api/contractor_skill/contractors';

                   // dd($request->all());

                    $params =[
                        'maintenance'=>$request->maintenance,
                        'business'=>$request->business,
                        'contractor_skill'=>$request->contractor_skill,
                        'staff_user'=>$staff_user->email,
                        'place'=>$request->place
                    ];



                    $response = Http::post($url,$params);

                    $responseObj = json_decode($response->body());

                    //dd($responseObj);

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


}

