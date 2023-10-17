<?php

namespace Odisse\Maintenance\Controllers;


use App\SLP\Enum\ActionStatusConstants;
use App\SLP\Enum\APIStatusConstants;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Odisse\Maintenance\Models\Contractor;
use App\SLP\Enum\BookingStatusConstants;
use App\SLP\Formatter\SystemDateFormats;
use Hedi\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Odisse\Maintenance\App\SLP\Enum\MaintenanceStatusConstants;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobDetail;
use Odisse\Maintenance\Models\MaintenanceJobDocument;
use Odisse\Maintenance\Models\MaintenanceJobPriorityHistory;
use Odisse\Maintenance\Models\MaintenanceJobSla;
use Odisse\Maintenance\Models\MaintenanceJobSlaRef;
use Odisse\Maintenance\Models\MaintenanceJobStatusHistory;
use Odisse\Maintenance\Models\MaintenanceLog;
use Illuminate\Support\Str;
use Odisse\Maintenance\App\Traits\MaintenanceDetails;
use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\ContractorDocument;
use Odisse\Maintenance\Models\ContractorLocation;
use Odisse\Maintenance\Models\ContractorSkill;


// use Sentinel;



class ApiContractorAppController extends Controller{




    public function getContractorListforApp(Request $request)
    {



        try {


            Log::info("In maintenance package, ApiContractorAppController- getContractorListforApp function ");


            $contractors = Contractor::where('contractor_active' , 1)->
            leftjoin('contractor_location' , 'contractor_location.id_contractor' , 'contractor.id_contractor')->
            leftjoin('contractor_location_ref' , 'contractor_location.id_contractor_location_ref' , 'contractor_location_ref.id_contractor_location_ref')->
            leftjoin('contractor_skill' , 'contractor_skill.id_contractor' , 'contractor.id_contractor')->
            leftjoin('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref');




            if( $request->has('business') and $request->business != null )
            $contractors = $contractors->whereIn('contractor.id_saas_client_business', $request->business);


            if( $request->has('skills') and $request->skills != null )
            $contractors = $contractors->whereIn('contractor_skill_ref.id_contractor_skill_ref', $request->skills);

            if( $request->has('locations') and $request->locations != null )
            $contractors = $contractors->whereIn('contractor_location_ref.id_contractor_location_ref', $request->locations);

            if( $request->has('contractor_name') and $request->contractor_name != null )
            $contractors = $contractors->where('contractor.name','ilike', "%".$request->contractor_name."%");


            $contractors = $contractors->select('contractor.id_contractor as first_contractor','contractor_location.id_contractor as second_contractor','contractor_skill.id_contractor as third_contractor','contractor.*','contractor_location.*','contractor_location_ref.*','contractor_skill.*','contractor_skill_ref.*')->distinct();

            // $contractors = $contractors->groupBy('contractor.id_saas_client_business','contractor.id_contractor','contractor_location.id_contractor_location','contractor_location_ref.id_contractor_location_ref','contractor_skill.id_contractor_skill','contractor_skill_ref.id_contractor_skill_ref');


            // $contractors = $contractors->orderBy('contractor.id_contractor');

            $contractors = $contractors->get()->unique('first_contractor');


            // foreach( $contractors as $contractor ){

            //     $contractor->c_url = env('APP_URL').'/maintenance/contractor/'. $contractor->first_contractor;
            // }




            $status = APIStatusConstants::OK;
            $message = trans('maintenance::contractor.load_contractors_successfully');


        } catch (\Exception $e) {

            Log::error("In maintenance package, ApiContractorAppController- getContractorListforApp function " . $e->getMessage());
            $message = trans('maintenance::contractor.unsuccessful_getContractors');
            $status = APIStatusConstants::BAD_REQUEST;
            $contractors = null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'contractors'  => $contractors,
            ]
        );
    }


    public function getContractorTasksforApp(Request $request)
    {

        try {

            Log::info("In maintenance package, ApiContractorAppController- getContractorTasksforApp function ");


            //get contractor taks
                $tasks = MaintenanceJob::join('maintenance_job_staff_history' , 'maintenance_job.id_maintenance_job' , 'maintenance_job_staff_history.id_maintenance_job')->whereNull('maintenance_job_staff_history.staff_end_date_time')->
                join('contractor_agent' , 'contractor_agent.id_user' , 'maintenance_job_staff_history.id_maintenance_assignee')->
                join('contractor' , 'contractor.id_contractor' , 'contractor_agent.id_contractor')->
                join('maintenance_job_status_ref' , 'maintenance_job_status_ref.id_maintenance_job_status_ref' , 'maintenance_job.id_maintenance_job_status')->
                join('maintenance_job_priority_ref' , 'maintenance_job_priority_ref.id_maintenance_job_priority_ref' , 'maintenance_job.id_maintenance_job_priority')->
                where('contractor.id_contractor' , $request->id_contractor)->get();

            //    foreach( $tasks as $task ){

            //         $task->m_url = env('APP_URL').'/maintenance/detail/'. $task->id_maintenance_job;
            //    }

            $status = APIStatusConstants::OK;
            $message = trans('maintenance::contractor.get_contractor_tasks_successfully');


        } catch (\Exception $e) {

            Log::error("In maintenance package, ApiContractorAppController- getContractorTasksforApp function " . $e->getMessage());
            $message = trans('maintenance::contractor.get_contractor_tasks_unsuccessfully');
            $status = APIStatusConstants::BAD_REQUEST;
            $tasks = null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'tasks'  => $tasks,
            ]
        );
    }


    public function getContractorEmailInfoforApp(Request $request)
    {

        try {

            Log::info("In maintenance package, ApiContractorAppController- getContractorEmailInfoforApp function " );


            $user_info = Contractor::where('contractor.id_contractor' , $request->id_contractor)->
            join('contractor_agent' , 'contractor_agent.id_contractor','contractor.id_contractor')->where('contractor_agent.contractor_agent_active' , 1)->
            join('users' , 'users.id' , 'contractor_agent.id_user' )->where('users.is_deleted' , 0)->where('users.users_active' , 1)->first();


                if($user_info){

                    return response()->json(
                        [
                        'status' => ActionStatusConstants::SUCCESS,
                        'message' => trans('maintenance::contractor.contractor_agent_info_returned'),
                        'user_info' =>$user_info,
                        ]);
                }
                else{

                    return response()->json(
                        [
                        'status' => ActionStatusConstants::FAILURE,
                        'message' => trans('maintenance::contractor.contractor_agent_info_was_not_returned'),
                        'user_info' =>null,

                        ]);

                }


        } catch (\Exception $e) {

            Log::error("In maintenance package, ApiContractorAppController- getContractorEmailInfoforApp function " . $e->getMessage());
            $message = trans('maintenance::contractor.get_contractor_email_unsuccessfully');
            $status = APIStatusConstants::BAD_REQUEST;
            $user_info = null;

            return response()->json(
                [
                'status' => ActionStatusConstants::FAILURE,
                'message' =>$message,
                'user_info' =>$user_info,

                ]);

        }


    }


    public function getContractorDocumentsforApp(Request $request)
    {

        try {

            Log::info("In maintenance package, ApiContractorAppController- getContractorDocumentsforApp function ");

            //get contractor attachments
            $attachments = ContractorDocument::where('id_contractor',$request->id_contractor)->where('contractor_document_active',1)->get();


            $status = APIStatusConstants::OK;
            $message = trans('maintenance::contractor.get_contractorattachments_successfully');

        //  return response()->json($attachments);

        } catch (\Exception $e) {

            Log::error("In maintenance package, ApiContractorAppController- getContractorDocumentsforApp function " . $e->getMessage());
            $message = trans('maintenance::contractor.get_contractorattachments_unsuccessfully');
            $status = APIStatusConstants::BAD_REQUEST;
            $attachments = null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'attachments'  => $attachments,
            ]
        );
    }



    public function changeContractorLoginSettingsforApp(Request $request)
    {


        Log::info("In maintenance package, ApiContractorAppController- changeContractorLoginSettingsforApp function ");


        $validator = Validator::make($request->all(), [

            'contractor' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8',

        ]);

        if ($validator->fails()) {

            Log::error("In maintenance package, ApiContractorAppController- changeContractorLoginSettingsforApp function ".": ". $validator->errors()." by user ");

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => $validator->errors(),
                ]);
        }

        try{

            // DB::beginTransaction();
            $contractor_agents = ContractorAgent::where('id_contractor' , $request->contractor)->where('contractor_agent_active' ,1 )->get();

            if(count($contractor_agents)>0){
                //update password
                foreach($contractor_agents as $contractor_agent){
                    $agent_user = User::find($contractor_agent->id_user);
                    if($agent_user->email == $request->email){
                        //only update password

                        $user = User::find($contractor_agent->id_user);

                       

                        // $sentinel_user = Sentinel::findById($contractor_agent->id_user);
                        if( $agent_user != null ) {
                            // $hasher = Sentinel::getHasher();

                          
                            $password = $request->password;
                            $passwordConf = $request->password_confirmation;

                            if ($password != $passwordConf) {

                                return response()->json(
                                    [
                                    'code' => ActionStatusConstants::FAILURE,
                                    'message' => trans('maintenance::contractor.password_and_confirmation_is_not_equal'),
                                    ]);
                            }

                            $user->update([
                                'password' => bcrypt($password),

                            ]);
                
                            return response()->json(
                                [
                                'user' => $user,
                                ]);
                            
                            // Sentinel::update($sentinel_user, array('password' => $password));

                            Log::info("In maintenance package, ApiContractorAppController- changeContractorLoginSettingsforApp function "."Password changed for user " );

                            return response()->json(
                                [
                                'code' => ActionStatusConstants::SUCCESS,
                                'message' => trans('maintenance::contractor.contractor_login_setting_edited'),
                                ]);
                        }
                        else{

                            Log::error("In maintenance package, ApiContractorAppController- changeContractorLoginSettingsforApp function "."update user pass : "."by user:");
                            return response()->json(
                                [
                                'code' => ActionStatusConstants::ERROR,
                                'message' => trans('maintenance::contractor.contractor_have_no_agent'),
                                ]);

                        }
                    }
                    else{
                        //update email is not possible

                        DB::rollback();
                        return response()->json(
                            [
                            'code' => ActionStatusConstants::FAILURE,
                            'message' => trans('maintenance::contractor.update_email_is_not_possible'),
                            ]);

                    }
                }


            }
            else{
                //insert new user and contractor_agent for first time
            }








            // DB::commit();
        }
        catch(\Exception $e){


            Log::error("In maintenance package, ApiContractorAppController- changeContractorLoginSettingsforApp function " . $e->getMessage());
            DB::rollback();
            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => $e->getmessage(),

                //   'message' => trans('maintenance::contractor.contractor_login_setting_not_changed'),
                ]);

        }




    }


        public function getContractorSkillsforApp(Request $request){


            try {
    
                Log::info("In maintenance package, ApiContractorAppController- getContractorSkillsforApp function ");
    
                $skills = Contractor::where('contractor.id_contractor' , $request->id_contractor)->
                join('contractor_skill' , 'contractor_skill.id_contractor','contractor.id_contractor')->where('contractor_skill.contractor_skill_active' , 1)->
                join('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref' )->where('contractor_skill_ref.contractor_skill_ref_active' , 1)->get();
    
                } catch (\Exception $e) {
    
                        Log::error($e->getMessage());
                        $message = trans('maintenance::contractor.get_contractor_skills_unsuccessfully');
                        $status = APIStatusConstants::BAD_REQUEST;
                        $contractor_skills = null;
                 }
                 return response()->json(
                    [
                    'status' => ActionStatusConstants::SUCCESS,
                    'message' => trans('maintenance::contractor.contractor_skills_info_returned'),
                    'contractor_skills' =>$skills,
                    ]);
    
        }

        public function changeContractorSkillsforApp(Request $request)
        {
    
            Log::info("In maintenance package, ApiContractorAppController- changeContractorSkillsforApp function ");
    
            try{
    
                DB::beginTransaction();
                $contractor_skills = ContractorSkill::where('id_contractor' , $request->contractor)->where('contractor_skill_active' ,1 )->get();
    
                if(count($contractor_skills)>0){
                    //update contractor skills
                    foreach($contractor_skills as $contractor_skill){
                        if($request->skills == null or !in_array($contractor_skill->id_contractor_skill_ref , $request->skills)){
                            //we have to delete old skills are not in skills array
                            $contractor_skill->update([
                                'contractor_skill_active'=>0
                            ]);
    
                        }
    
                    }
    
                if( $request->skills != null)
                    foreach($request->skills as $skill){
                        $contractor_skill = ContractorSkill::where('id_contractor' , $request->contractor)->where('id_contractor_skill_ref' ,$skill )->first();
                        if($contractor_skill){
                            $contractor_skill->update([
                                'contractor_skill_active'=>1,
                            ]);
                        }
                        else{
                            $contractor_skill = new ContractorSkill([
                                'id_contractor_skill_ref'=>$skill,
                                'id_contractor'=>$request->contractor,
                                'contractor_skill_active'=>1,
                            ]);
                            $contractor_skill->save();
                        }
    
    
                    }
    
    
                }
                else{
                    //insert new skills for first time
                    if($request->skills){
                        foreach($request->skills as $skill){
                            $new_skill = new ContractorSkill([
                                'id_contractor_skill_ref'=>$skill,
                                'id_contractor'=>$request->contractor,
                                'contractor_skill_active'=>1,
                            ]);
    
                            $new_skill->save();
                        }
                    }
                }
    
    
                Log::info("In maintenance package, ApiContractorAppController- changeContractorSkillsforApp function " . trans('maintenance::contractor.contractor_skills_updated'));
    
                DB::commit();
    
                return response()->json(
                    [
                      'status' => ActionStatusConstants::SUCCESS,
                      'message' => trans('maintenance::contractor.contractor_skills_updated'),
                    ]);
    
            }
            catch(\Exception $e){
    
                Log::error("In maintenance package, ApiContractorAppController- changeContractorSkillsforApp function " . $e->getMessage());
                DB::rollback();
                return response()->json(
                    [
                      'status' => ActionStatusConstants::FAILURE,
                      'message' => $e->getmessage(),

                    //   'message' => trans('maintenance::contractor.contractor_skills_not_changed'),
                    ]);
    
            }
    
        }



    public function getContractorLocationsforApp(Request $request)
    {

        try {

            Log::info("In maintenance package, ApiContractorAppController- getContractorLocationsforApp function ");

            // return response()->json($request->all());

            //get contractor locations
            $locations = Contractor::where('contractor.id_contractor' , $request->id_contractor)->
            join('contractor_location' , 'contractor_location.id_contractor','contractor.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
            join('contractor_location_ref' , 'contractor_location.id_contractor_location_ref' , 'contractor_location_ref.id_contractor_location_ref' )->where('contractor_location_ref.contractor_location_ref_active' , 1)->get();

            $status = APIStatusConstants::OK;
            $message = trans('maintenance::contractor.get_contractor_locations_successfully');

        } catch (\Exception $e) {

            Log::error("In maintenance package, ApiContractorAppController- getContractorLocationsforApp function " . $e->getMessage());
            $message = trans('maintenance::contractor.get_contractor_locations_unsuccessfully');
            $status = APIStatusConstants::BAD_REQUEST;
            $locations = null;

        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
                'locations'  => $locations,
            ]
        );
    }


    public function changeContractorLocationsforApp(Request $request)
    {

        Log::info("In maintenance package, ApiContractorAppController- changeContractorLocationsforApp function ");

        try{

            DB::beginTransaction();
            $contractor_locations = ContractorLocation::where('id_contractor' , $request->contractor)->where('contractor_location_active' ,1 )->get();

            if(count($contractor_locations)>0){
                //update password
                foreach($contractor_locations as $contractor_location){
                    if( $request->locations == null or !in_array($contractor_location->id_contractor_location_ref , $request->locations)){
                        //we have to delete old skills are not in skills array
                        $contractor_location->update([
                            'contractor_location_active'=>0
                        ]);

                    }

                }

                if($request->locations != null )
                    foreach($request->locations as $location){
                        $contractor_location = ContractorLocation::where('id_contractor' , $request->contractor)->where('id_contractor_location_ref' ,$location )->first();
                        if($contractor_location){
                            $contractor_location->update([
                                'contractor_location_active'=>1,
                            ]);
                        }
                        else{
                            $contractor_location = new ContractorLocation([
                                'id_contractor_location_ref'=>$location,
                                'id_contractor'=>$request->contractor,
                                'contractor_location_active'=>1,
                            ]);
                            $contractor_location->save();
                        }
                    }
            }
            else{
                //insert new locations for first time
                if($request->locations){
                    foreach($request->locations as $location){
                        $new_location = new ContractorLocation([
                            'id_contractor_location_ref'=>$location,
                            'id_contractor'=>$request->contractor,
                            'contractor_location_active'=>1,
                        ]);

                        $new_location->save();
                    }
                }
            }

            // return response()->json($request->all());


            Log::info(trans('maintenance::contractor.contractor_locations_updated'));

            DB::commit();

            return response()->json(
                [
                  'status' => ActionStatusConstants::SUCCESS,
                  'message' => trans('maintenance::contractor.contractor_locations_updated'),
                ]);

        }
        catch(\Exception $e){


            Log::error("In maintenance package, ApiContractorAppController- changeContractorLocationsforApp function " . $e->getMessage());
            DB::rollback();
            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => trans('maintenance::contractor.contractor_locations_not_changed'),
                ]);

        }




    }

    public function saveNewContractorforApp( Request $request)
    {

        $user = User::where('email' ,$request->user)->first();
        if($user){

            Log::info("In maintenance package, ApiContractorAppController- saveNewContractorforApp function ");


            $validator = $this->validateContractor($request);

            if( null != $validator) {
                return response()->json(['message' => $validator->errors()], 220);
            }

            $result = $this->createContractor($request);



            Log::info("In maintenance package, ApiContractorAppController- saveNewContractorforApp function - result=" .$result['status']);
            if( $result['status'] == 'success')

                return response()->json($result, 200);
            else{
                return response()->json($result, 400);

            }
        }
        else{


            $status = 'error';
            $message = trans('maintenance:contractor.portal_user_not_exist_in_business');

            $result=[
                'status' => $status,
                'message' => $message
            ];

            return response()->json($result, 400);



        }
    }


    private function validateContractor( Request $request)
    {

        Log::info("In maintenance package, ApiContractorAppController- validateContractor function ");

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'short_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'vat_number' => 'nullable|string',
            'tel_number1' => 'nullable|uk_phone',
            'tel_number2' => 'nullable|uk_phone',
            'address_line1' => 'nullable',
            'address_line2' => 'nullable',
            'address_line3' => 'nullable',

          ]);
        if ($validator->fails()) {

            Log::error("In maintenance package, ApiContractorAppController- validateContractor function ". $validator->errors());




            return $validator;
        }

    }

    private function createContractor( $request )
    {

        $user = User::where('email' ,$request->user)->first();
        if($user){
            $id_saas_client_business = $request->saas_client_business;

            Log::info("In maintenance package, ApiContractorAppController- createContractor function ");


            try {
                DB::beginTransaction();


                //save a new contractor
                $contractor = new Contractor();
                $contractor->id_saas_client_business =  $id_saas_client_business;
                $contractor->name = $request->name;
                $contractor->short_name = $request->short_name;
                $contractor->vat_number = $request->vat_number;
                $contractor->tel_number1 = $request->tel_number1;
                $contractor->tel_number2 = $request->tel_number2;
                $contractor->address_line1 = $request->address_line1;
                $contractor->address_line2 = $request->address_line2;
                $contractor->address_line3 = $request->address_line3;
                $contractor->contractor_active = 1;


                $contractor->save();


                $contractorAgent = [
                    'login_name' => $request->email,
                    'email' => $request->email,
                    'password' => $request->password,
                    'gender'  => 'M',
                    'user_title'  => 'Mr',
                ];

                $contractor_user = new User([
                    'login_name'=>$request->email,
                    'email' => $request->email,
                    'password' => $request->password,
                    'gender'  => 'M',
                    'user_title'  => 'Mr',
                    'users_active'=>1,
                    'id_saas_client_business'=>$request->saas_client_business,
                ]);
                $contractor_user->save();




                $contractor_agent = new ContractorAgent([
                    'id_contractor'=>$contractor->id_contractor,
                    'id_user'=>$contractor_user->id,
                    'contractor_agent_active'=>1,
                ]);

                $contractor_agent->save();




                $files = $request->files;



                $object = new Contractor();


                $file_description = $request->file_description;


                $this->uploadFile($files,$object,$file_description,$contractor);




                DB::commit();


                $status = 'success';
                $message = 'Contractor created successfully';



            } catch (\Exception $e) {

                Log::error("In maintenance package, ApiContractorAppController- createContractor function " . $e->getMessage(). $e->getLine());

                DB::rollBack();


                $status = 'error';
                $message = trans('maintenance:contractor.contractor_not_created');


            }
        }
        else{


            $status = 'error';
            $message = trans('maintenance:contractor.portal_user_not_exist_in_business');



        }

        return [
            'status' => $status,
            'message' => $message
        ];


    }



    private function uploadFile($files,$object,$file_description,$object_model){

        Log::info("In maintenance package, ApiContractorAppController- uploadFile function ");


        try {
            DB::beginTransaction();

            foreach($files as $upload_file) {
                foreach($upload_file as $file) {


                $fileName = date('Y-m-d').'_'.$file->getClientOriginalName();

                // dd($fileName);

                // File extension
                $extension = $file->getClientOriginalExtension();

                // if($object instanceof Contractor){

                $contractor_file_path = config('maintenances.contractor_file_path');

                    $path = $contractor_file_path . 'uploaded_files/' ;
                    if (!\File::exists($path)) {
                        \File::makeDirectory($path, 0755, true);
                    }

                    $file->move($path, $fileName);


                //save documents of contractor
                $contractor_document = new ContractorDocument();
                $contractor_document->id_contractor =  $object_model->id_contractor;
                $contractor_document->document_name = $fileName;
                $contractor_document->document_address = $path;
                $contractor_document->document_extention = $extension;
                $contractor_document->description = $file_description;


                $contractor_document->save();


              
              }
            }
        DB::commit();

       } catch (\Exception $e) {

        Log::error("In maintenance package, ApiContractorAppController- uploadFile function ". $e->getMessage(). $e->getLine());

        DB::rollBack();


        $status = 'error';
        $message = trans('maintenance::contractor.contractor_not_created');


       }
    }


    public function deleteContractorforApp(Request $request)
    {

        try {

            Log::info("In maintenance package, ApiContractorAppController- deleteContractorforApp function ");


            $contractor = Contractor::find($request->deleted_contractor);

            $contractor->update([
                'contractor_active' => 0,
            ]);


            $status = APIStatusConstants::OK;
            $message = trans('maintenance::contractor.delete_contractor_was_successful');


        } catch (\Exception $e) {

            Log::error("In maintenance package, ApiContractorAppController- deleteContractorforApp function " .$e->getMessage());
            $message = trans('maintenance::contractor.delete_contractor_was_unsuccessful');
            $status = APIStatusConstants::BAD_REQUEST;
            $$maintenances=null;


        }

        return response()->json(
            [
                'status' => $status,
                'message'   => $message,
            ]
        );
    }




  




    // public function getDataToCreate( Request $request )
    // {

    //     Log::info("In maintenance package, ApiContractorMgtController- getDataToCreate function ");

    //         //get all maintenance category
    //         $maintenance_category = MaintenanceJobCategoryRef::all();

    //         //get all maintenance priorities
    //         $priorities = MaintenanceJobPriorityRef::all();

    //         $locations = $this->getMaintainables();;

    //         $jobs = MaintenanceJob::all();

    //         return response()->json(
    //             [
    //                 'jobs' => $jobs,
    //                 'maintenance_category' => $maintenance_category,
    //                 'priorities' => $priorities,
    //                 'locations' => $locations,
    //             ]
    //         );
    // }


    // private function getMaintainables()
    // {
    //     Log::info("In maintenance package, ApiContractorMgtController- getMaintainables function ");

    //     $locations = [];

    //     $rooms = Room::all();

    //     foreach($rooms as $room) {
    //         $property = $room->property;
    //         $room->id = 'Room'.$room->id_room;
    //         $room->name = '[Room] '.$property->property_short_name .'/'.$room->room_number_full;

    //     }

    //     foreach($rooms as $room) {
    //         $locations[] = $room;
    //     }

    //     $properties = Property::all();

    //     foreach($properties as $property) {
    //         $property->id = 'Property'.$property->id_property;
    //         $property->name = '[Property] '.$property->property_name;

    //     }

    //     foreach($properties as $property) {
    //         $locations[] = $property;
    //     }


    //     $sites = Site::all();
    //     foreach($sites as $site) {
    //         $site->id = 'Site'.$site->id_site;
    //         $site->name = '[Site] '.$site->site_full_name;

    //     }

    //     foreach($sites as $site) {
    //         $locations[] = $site;
    //     }

    //     return $locations;
    // }


    // //api to load residents of location
    // public function getLocationResident(Request $request)
    // {

    //     Log::info("In maintenance package, ApiContractorMgtController- getLocationResident function ");

    //     try {

    //         $rooms = [];
    //         $locations = $request->locations;

    //         foreach($locations as $location) {

    //             if(Str::contains($location, 'Room')) {

    //                 $room_id_part = strtok($location, 'Room');
    //                 $rooms[] = $room_id_part;
    //             }
    //         }

    //         if(sizeof($rooms) == 0) {
    //             $residents = [];
    //         } else {
    //             $residents = $this->getMaintenanceResidentInfo($rooms);
    //         }

    //         return response()->json(
    //             [
    //               'residents'=> $residents,
    //               'message' => trans('maintenance::maintenance.get_resident_was_successful'),
    //             ], 200
    //         );

    //     } catch (\Exception $e) {
    //         Log::error("In maintenance package, ApiContractorMgtController- getLocationResident function ".$e->getMessage());

    //         return response()->json([ 'message' =>  trans('maintenance::maintenance.get_resident_reporter_was_not_successful'), 400]);

    //     }

    // }
}


