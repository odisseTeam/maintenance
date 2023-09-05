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

        Log::info("In Maintenance package - ContractroManagementController- showContractorPage function " . " try to go to contractor management page  ------- by user " . $user->first_name . " " . $user->last_name);

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


                        //$response = Http::post($url,$params);
                        $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$params);

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

        Log::info("In Maintenance Package - in ContractorManagementController- showPageForContractorCreation function " . " try to go to create contractor page  ------- by user " . $user->first_name . " " . $user->last_name);

        $businesses = config('maintenances.businesses_name');


        return view('maintenance::portal_create_contractor',
                    [
                        'businesses'=>$businesses,
                    ]
                );
    }

    public function storeContractor( Request $request)
    {

        $user = Sentinel::getUser();

        Log::info("In Maintenance package - ContractroManagementController- storeContractor function " . " try to send contractor data by API     ------- by user " . $user->first_name . " " . $user->last_name);

        $selected_business =  $request->saas_client_business;

        $businesses = config('maintenances.businesses_name');


        foreach($businesses as $business){

            if($business['id_saas_client_business'] == $selected_business ){

                $url = $business['maintenance_api_url'].'/api/contractor/save/new';



                $data = [];

                if ($request->files != null ) {

                    $files = $request->files;

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

                $datum =  $request->all() ;
                unset($datum['files']);
                unset($datum['_token']);
                $datum['user'] = $user->email;

                foreach( $datum as $key=>$value){
                    $data[] = [
                        'name'  => $key,
                        'contents' => $value
                    ];

                }

                $client = new Client(['auth' => [$business['basic_auth_user'], $business['basic_auth_password']]]);


                $options = [
                    'multipart' => $data,
                ];

                try {

                    $response = $client->post($url, $options);

                    if($response->getStatusCode() == "220"){
                        //validator error

                        $responseObj = json_decode($response->getBody());

                        return redirect()->back()
                        ->withErrors($responseObj->message)
                        ->withInput();
                    }
                    else{
                        return redirect('/maintenance/contractor_management')->with(['success' => 'Contractor created']);

                    }





                }
                catch(\Exception $e){

                    Log::error("In Maintenance Package - in ContractorManagementController - storeContractor function : send data by APi was not successful ");
                    Log::error($e->getMessage(). $e->getLine());

                    DB::rollBack();
                    // var_dump($e->getMessage());

                    // dd($e->getMessage());


                    $status = 'error';
                    $message = $e->getMessage();//trans('maintenance::contractor.contractor_not_created');

                    return redirect()->back()
                    ->withErrors($message)
                    ->withInput();

                    // return redirect('/maintenance/new_contractor')->with([$status => $message]);


                }

            }
        }

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


                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());



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

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtGetContractorAttachments function " . " try to send data by API for getting contractor attachments ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_attachments';

                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());



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

        Log::info("In maintenance package - ContractorManagementController- ajaxMgtGetContractorTasks function " . " try to send data by API for getting contractor tasks ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_tasks';

                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());



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


        Log::info("In maintenance package - ContractorManagementController- ajaxMgtGetContractorEmailInfo function " . " try to send data by API for getting contractor email info ------- by user" . $user->first_name . " " . $user->last_name);


        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_email';

                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());


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

        Log::info("In maintenance package - ContractorManagementController- ajaxMgtChangeContractorLoginSettings function " . " try to send data by API for getting contractor login setting ------- by user " . $user->first_name . " " . $user->last_name);


        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/change/login_setting';

                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());



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

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtGetContractorLocation function " . " try to send data by API for getting contractor locations ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_locations';

                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());



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

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtChangeContractorLocation function " . " try to send data by API for change contractor locations ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/mgt_locations/change';

                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());



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

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtGetContractorSkill function " . " try to send data by API for change contractor locations ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){


            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/get_skills';

                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());



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

        Log::info("In maintenance package, ContractorManagementController- ajaxMgtChangeContractorSkills function " . " try to send data by API for change contractor skills ------- by user" . $user->first_name . " " . $user->last_name);

        if( $request->has('id_business') and $request->id_business != null ){
            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $request->id_business){

                    $url =$business['maintenance_api_url'].'/api/contractor/mgt_skills/change';

                    //$response = Http::post($url,$request->all());
                    $response = Http::withBasicAuth($business['basic_auth_user'], $business['basic_auth_password'])->post($url,$request->all());



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

}

