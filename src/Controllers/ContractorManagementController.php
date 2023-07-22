<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;

use App\SLP\Enum\ActionStatusConstants;
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
use Odisse\Maintenance\Models\ContractorLocationRef;
use Odisse\Maintenance\Models\ContractorSkillRef;

use Odisse\Maintenance\Models\MaintenanceLog as ModelsMaintenanceLog;
use Odisse\Maintenance\App\SLP\MaintenanceOperation;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Sentinel;
use Spatie\LaravelRay\Commands\PublishConfigCommand;
use stdClass;
use Validator;

class ContractorManagementController extends Controller
{


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showContractorPage(){



        $user = Sentinel::getUser();

        Log::info(" zzzzzzzzz in Maintenance package ContractroManagementController- showContractorPage function " . " try to go to contractor management page  ------- by user " . $user->first_name . " " . $user->last_name);
       
        $businesses = config('maintenances.businesses_name');
        $skills = ContractorSkillRef::where('contractor_skill_ref_active',1)->get();
        $locations = ContractorLocationRef::where('contractor_location_ref_active',1)->get();


        
        return view('maintenance::contractor_management',
                    [
                     'businesses'=>$businesses,
                     'skills'=>$skills,
                     'locations'=>$locations,

                    ]
                );

    }



    public Function ajaxLoadContractors(Request $request){


        $user = Sentinel::getUser();

        $responseObj= null;

        $contractors = [];

        if( $request->has('business') and $request->business != null ){

            $requested_businesses = $request->business;

            foreach($requested_businesses as $requested_business){

               //get contractors of specific business

                $businesses = config('maintenances.businesses_name');
                foreach($businesses as $business){
                    if($business['id_saas_client_business'] == $requested_business){

                        $url =$business['maintenance_api_url'].'/api/contractor/list_details';


                        $params =[
                            'business'=>$request->business,
                            'skills'=>$request->skills,
                            'locations'=>$request->locations,
                            'contractor_name'=>$request->contractor_name,
                        
                        ];


                        $response = Http::post($url,$params);
                        $responseObj = json_decode($response->body());

                    }
                }

           }

        }

        return response()->json(
            [
            'code' => 'success',
            'contractors'=>$responseObj->contractors,

            'message' => trans('maintenance::dashboard.your_contractors_loaded'),
            ]);


    }

    public Function showPageForContractorCreation(){

        $user = Sentinel::getUser();

        Log::info(" in ContractorManagementController- showPageForContractorCreation function " . " try to go to create contractor page  ------- by user " . $user->first_name . " " . $user->last_name);

        // $wiki_link = WikiLinkGenerator::GetWikiLinkOfPage('contractor');


        return view('maintenance::portal_create_contractor',
                    [
                        // 'wiki_link'=>$wiki_link,
                    ]
                );
    }

    public function storeContractor( Request $request)
    {

        $user = Sentinel::getUser();

        Log::info("  in Maintenance package ContractroManagementController- storeContractor function " . " try to send contractor data by API     ------- by user " . $user->first_name . " " . $user->last_name);

        // dd("***");
        $business_index = 0;
        $businesses = config('maintenances.businesses_name');

        $url = $businesses[0]['maintenance_api_url'].'/api/contractor/save/new';


        // $contractor_files = $request->files['contractor_files'];
        // dd($request->files[0]);

        $data = [];
        // if ($request->hasFile('files') ) {
            if ($request->files != null ) {

            // get Illuminate\Http\UploadedFile instance
            // $files = $request->file('files');
            $files = $request->files;

            Log::info(" dakhele  file ");

            // dd($request->files);

            $index = 1;
            foreach($files as $upload_file) {
                foreach($upload_file as $file) {

                    // post request with attachment
                    Log::info(" dakhele dakhele file ");

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


        }

        // if ($request->has('locations') ) {
        //     // get Illuminate\Http\UploadedFile instance
        //     $locations = $request->get('locations');

        //     $index = 1;
        //     foreach($locations as $location) {
        //         // post request with attachment
        //         $data[] = [
        //             'name' => 'locations[]',
        //             'contents' => $location,
        //         ] ;
        //     }

        // }

        Log::info(" khareje  file ");

        $datum =  $request->all() ;
        unset($datum['files']);
        unset($datum['_token']);
        // unset($datum['locations']);
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
            Log::info(" dakhele  try ");

            $response = $client->post($url, $options);

            // dd($response);

            // dump($response);

        }
        catch(\Exception $e){

            Log::error(" in ContractorManagementController - storeContractor function : send data by APi was not successful");
        Log::error($e->getMessage(). $e->getLine());

        DB::rollBack();


        $status = 'error';
        $message = trans('maintenance:contractor.contractor_not_created');

        }
        Log::info(" khareje  try ");

        return redirect('/maintenance/contractor_management')->with(['success' => 'Contractor created']);

    }


    public Function ajaxMgtDeleteContractor(Request $request , $id_contractor){


        $user = Sentinel::getUser();

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtDeleteContractor function " . " try to send data by API for delete a specific contractor  ------- by user " . $user->first_name . " " . $user->last_name);

        if( $request->has('deleted_business') and $request->deleted_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->deleted_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/delete';


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

    public Function ajaxMgtGetContractorAttachments(Request $request, $id_contractor){

        $user = Sentinel::getUser();

        // dd($request->all());

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtGetContractorAttachments function " . " try to send data by API for getting contractor attachments ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_attachments/';

                    $response = Http::post($url,$request->all());


                    $responseObj = json_decode($response->body());



                }
            }



        }

        if($responseObj->status == 200){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                  'attachments' => $responseObj->attachments,

                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,
                  'attachments' => null,

                ]);

        }


    }

    public Function ajaxMgtGetContractorTasks(Request $request, $id_contractor){

        $user = Sentinel::getUser();

        // dd($request->all());

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtGetContractorAttachments function " . " try to send data by API for getting contractor tasks ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_tasks/';

                    $response = Http::post($url,$request->all());


                    $responseObj = json_decode($response->body());


                }
            }



        }

        if($responseObj->status == 200){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                  'tasks' => $responseObj->tasks,

                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,
                  'tasks' => null,

                ]);

        }


    }

    public Function ajaxMgtGetContractorEmailInfo(Request $request){

        $user = Sentinel::getUser();

        // dd($request->all());

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtGetContractorEmailInfo function " . " try to send data by API for getting contractor email info ------- by user" . $user->first_name . " " . $user->last_name);


        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_email/';

                    $response = Http::post($url,$request->all());

                    // dd( $response);

                    $responseObj = json_decode($response->body());



                }
            }



        }

        if($responseObj->status == 'success'){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                  'user_info' => $responseObj->user_info,

                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,
                  'user_info' => null,

                ]);

        }



    }


    public Function ajaxMgtChangeContractorLoginSettings(Request $request){

        $user = Sentinel::getUser();

        // dd($request->all());

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtChangeContractorLoginSettings function " . " try to send data by API for getting contractor login setting ------- by user " . $user->first_name . " " . $user->last_name);


        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/change/login_setting';

                    $response = Http::post($url,$request->all());


                    $responseObj = json_decode($response->body());

                    // dd($responseObj);

                }
            }



        }

        if($responseObj->code == 'success'){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,

                ]);
        }
        elseif($responseObj->code == 'failure'){
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,

                ]);

        }
        elseif($responseObj->code == 'error'){
            return response()->json(
                [
                  'code' => 'error',
                  'message' => $responseObj->message,

                ]);

        }


    }

    public Function ajaxMgtGetContractorLocation(Request $request, $id_contractor){

        $user = Sentinel::getUser();

        // dd($request->all());

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtGetContractorLocation function " . " try to send data by API for getting contractor locations ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_locations/';

                    $response = Http::post($url,$request->all());


                    $responseObj = json_decode($response->body());

                  // dd($responseObj);

                }
            }



        }

        if($responseObj->status == 200){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                  'locations' => $responseObj->locations,

                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,
                  'locations' => null,

                ]);

        }


    }
  
    public Function ajaxMgtChangeContractorLocation(Request $request){

        $user = Sentinel::getUser();

        // dd($request->all());

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtChangeContractorLocation function " . " try to send data by API for change contractor locations ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/mgt_locations/change';

                    $response = Http::post($url,$request->all());


                    $responseObj = json_decode($response->body());

                //   dd($responseObj);

                }
            }



        }

        if($responseObj->status == 'success'){
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
   
    
    public Function ajaxMgtGetContractorSkill(Request $request){

        $user = Sentinel::getUser();

        // dd($request->all());

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtChangeContractorLocation function " . " try to send data by API for change contractor locations ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_skills';

                    $response = Http::post($url,$request->all());


                    $responseObj = json_decode($response->body());

                //   dd($responseObj);

                }
            }



        }

        if($responseObj->status == 'success'){
            return response()->json(
                [
                  'code' => 'success',
                  'message' => $responseObj->message,
                  'contractor_skills'=>$responseObj->contractor_skills

                ]);
        }
        else{
            return response()->json(
                [
                  'code' => 'failure',
                  'message' => $responseObj->message,
                  'contractor_skills'=>null

                ]);

        }


    }

    public function ajaxMgtChangeContractorSkills(Request $request){


        $user = Sentinel::getUser();

        // dd($request->all());

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtChangeContractorSkills function " . " try to send data by API for change contractor skills ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/mgt_skills/change';

                    $response = Http::post($url,$request->all());


                    $responseObj = json_decode($response->body());

                    // dd( $responseObj);


                }
            }



        }

        if($responseObj->status == 'success'){
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

