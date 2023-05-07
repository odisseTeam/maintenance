<?php

namespace Odisse\Maintenance\Controllers;

use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobDocument;
use Odisse\Maintenance\Models\MaintenanceJobDetail;
use Odisse\Maintenance\Models\Maintainable;
use Odisse\Maintenance\Models\Contractor;
use App\Models\User;
use App\SLP\Enum\ActionStatusConstants;
use App\SLP\Enum\BookingStatusConstant;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use Illuminate\Support\Str;
use App\SLP\Formatter\SystemDateFormats;
use Odisse\Maintenance\Models\MaintenanceLog;

use Illuminate\Http\Request;


use App\Models\SaasClientBusiness;
use App\Models\Room;
use App\Models\Property;
use App\Models\Site;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Odisse\Maintenance\App\Traits\MaintenanceDetails;
use Odisse\Maintenance\App\Traits\MaintenanceTimelineDetails;

use Sentinel;
use Validator;

class MaintenanceController extends Controller
{

  use MaintenanceDetails;
  use MaintenanceTimelineDetails;

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

            Log::info(" in MaintenanceController- createNewMaintenancePage function " . " try to go to create maintenance page  ------- by user " . $user->first_name . " " . $user->last_name);

            try {
              
            //get all maintenance category
            $maintenance_category = MaintenanceJobCategoryRef::all();


            //get all businesses
            $saas_client_businesses = SaasClientBusiness::all();

            //get all maintenance priorities
            $priorities = MaintenanceJobPriorityRef::all();

            $locations = [];

            $rooms = Room::all();

            foreach($rooms as $room){
              $room->id = 'Room'.$room->id_room;
              $room->name = 'Room'.' '.$room->room_number_full;

            }

            foreach($rooms as $room){
              $locations[] = $room;
            }

            $properties = Property::all();
            
            foreach($properties as $property){
              $property->id = 'Property'.$property->id_property;
              $property->name = 'Property'.' '.$property->property_name;

            }

            foreach($properties as $property){
              $locations[] = $property;
            }


            $sites = Site::all();


            foreach($sites as $site){
              $site->id = 'Site'.$site->id_site;
              $site->name = 'Site'.' '.$site->site_full_name;

            }
            foreach($sites as $site){
              $locations[] = $site;
            }

            $jobs = MaintenanceJob::all();



            return view('maintenance::create_maintenance',
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
        
           return view('maintenance::create_maintenance')->with([ActionStatusConstants::ERROR=>  trans('maintenance.you_can_not_see_create_maintenance_page')]);
        
        }


      }

      public function ajaxUploadMaintenanceFile(Request $request){

          $user = Sentinel::getUser();

          Log::info(" in MaintenanceController- ajaxUploadMaintenanceFile function " . " try  to upload an maintenance file  ------- by user " . $user->first_name . " " . $user->last_name);


          try {
            DB::beginTransaction();


      

            $validator = Validator::make($request->all(), [
              'file' => 'required|mimes:doc,docx,jpg,jpeg,pdf,PNG,png,zip,rar|max:2048',
              'description'=>'required'
            ]);
            if ($validator->fails()) {

              Log::error("in MaintenanceController- ajaxUploadMaintenanceFile function ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

              return response()->json(['code' => ActionStatusConstants::FAILURE, 'message' => $validator->errors() ]);
          }
            


              $file = $request->file('file');
              $fileName = date('Y-m-d').'_'.$file->getClientOriginalName();

                // File extension
                $extension = $file->getClientOriginalExtension();

            
      

              $maintenance_file_path = config('maintenances.maintenance_file_path');

                  $path = $maintenance_file_path . 'uploaded_files' ;
                  if (!\File::exists($path)) {
                      \File::makeDirectory($path,0755,true);
                  }


              $request->file->move($path, $fileName);

              $uploaded_file = $fileName;

        
              DB::commit();

                return response()->json(
                  [
                    'code' => ActionStatusConstants::SUCCESS,
                    'uploaded_file'=>$file,
                    'fileName'=>$fileName,
                    'description'=>$request->description,

                    'message' => trans('resident.other_contacts_not_updated'),
                ]          
              );

            } catch (\Exception $e) {


              Log::error(" in MaintenanceController - ajaxUploadMaintenanceFile function " . " upload maintenance document was not successful " . " by user " . $user->first_name . " " . $user->last_name);
              Log::error($e->getMessage());
  
              DB::rollBack();
  
              return response()->json([
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => trans('maintenance.maintenance_document_did_not_uploaded'),
              ]);
  
          }
  
      }

      public function ajaxFindMaintenanceTitle(Request $request){

                $user = Sentinel::getUser();

                Log::info(" in MaintenanceController- ajaxFindMaintenanceTitle function " . " try  to get maintenance title  ------- by user " . $user->first_name . " " . $user->last_name);

                try {

                $id_maintenance_job = $request->id_maintenance_job;
                $maintenace_job = MaintenanceJob::findOrFail($id_maintenance_job);
                $maintenace_job_title_only = $maintenace_job->maintenance_job_title;
                $maintenace_job_date = $maintenace_job->job_report_date_time;

                $maintenace_job_title = $maintenace_job_title_only.$maintenace_job_date;

                Log::info(" ine ". $maintenace_job_title." MaintenanceController- ajaxFindMaintenanceTitle function " . " try  to get maintenance title  ------- by user " . $user->first_name . " " . $user->last_name);

                  return response()->json(
                    [
                        'code' => ActionStatusConstants::SUCCESS,
                        'maintenace_job_title'=>$maintenace_job_title,
                        'message' => trans('maintenance.find_maintenance_title_was_successful'),
                    ]

                  );

                } catch (\Exception $e) {
                  Log::error("in MaintenanceController- ajaxFindMaintenanceTitle function find maintenance title " . " by user "
                      . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

                  return response()->json()->with([ActionStatusConstants::ERROR=>  trans('maintenance.get_maintenance_title_was_not_successful')]);

              }

      }

      public function saveNewMaintenence(Request $request){


        $user = Sentinel::getUser();



        Log::info(" in MaintenanceController- saveNewMaintenence function " . " try  to save new maintenance   ------- by user " . $user->first_name . " " . $user->last_name);


        try {
          DB::beginTransaction();


            $validator = Validator::make($request->all(), [
              'maintenance_title' => 'required',
              'description'=>'required',
              'maintenance_date'=>'required|date_format:' . SystemDateFormats::getDateTimeFormat(),
              'maintenance_category'=>'required',
              'saas_client_business'=>'required',
              'locations'=>'required',
              'priority'=>'required',


            ]);
            if ($validator->fails()) {

              Log::error("in MaintenanceController- ajaxUploadMaintenanceFile function ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

              return response()->json(['code' => ActionStatusConstants::FAILURE, 'message' => $validator->errors() ]);
             }

            $maintenance_job = new MaintenanceJob();
            $maintenance_job->id_saas_client_business =  $request->saas_client_business;
            $maintenance_job->id_parent_job = 1;
            $maintenance_job->id_saas_staff_reporter = $user->id;
            $maintenance_job->job_report_date_time = $request->maintenance_date;
            $maintenance_job->id_maintenance_job_category = $request->maintenance_category;
            $maintenance_job->id_maintenance_job_priority = $request->priority;
            $maintenance_job->id_maintenance_job_status = 1;
            $maintenance_job->maintenance_job_title = $request->maintenance_title;
            $maintenance_job->maintenance_job_description = $request->description;
            $maintenance_job->id_resident_reporter = $request->resident_reporter[0];
          
            $maintenance_job->save();

            

            $maintenance_file_path = config('maintenances.maintenance_file_path');


            $desc =strtok($request->nahayat, '/');

            $doc_number = substr_count($request->nahayat, '/');

            $doc_parts = explode("/",$request->nahayat,$doc_number);

            foreach( $doc_parts as $doc_part){

                    $description_part = strtok($doc_part, '+');

                  
                    $name_part = substr($doc_part, strpos($doc_part, "+") + 1);    

                    $name_extension_part = substr($name_part, strpos($name_part, ".") + 1); 
                    



                  $maintenance_job_document = new MaintenanceJobDocument();
                  $maintenance_job_document->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                  $maintenance_job_document->document_name = $name_part;
                  $maintenance_job_document->document_address = $maintenance_file_path;
                  $maintenance_job_document->document_extention = $name_extension_part;
                  $maintenance_job_document->description = $description_part;
                  $maintenance_job_document->maintenance_job_document_active = 1;

                
                  $maintenance_job_document->save();

            }

            $log_note = $user->first_name . " " . $user->last_name." Created a New Maintenance titled ".$maintenance_job->maintenance_job_title;

            $maintenance_log = new MaintenanceLog();
            $maintenance_log->id_maintenance_job =  $maintenance_job->id_maintenance_job;
            $maintenance_log->id_staff = $user->id;
            $maintenance_log->log_date_time = $request->maintenance_date;
            $maintenance_log->id_maintenance_job_priority = $request->priority;
            $maintenance_log->log_note = $log_note;
   
            $maintenance_log->save();

            $locations = $request->locations;

            foreach($locations as $location){

              switch ($location) {
                case  Str::contains($location, 'Room'):

                  $maintainable_id =  strtok($location, 'Room');

                  $maintainable = new Maintainable();
                  $maintainable->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                  $maintainable->maintenable_id =  $maintainable_id;
                  $maintainable->maintenable_type = 'App\Models\Rooms';
                
                  $maintainable->save();
                  break;

                case Str::contains($location, 'Property'):
                
                  $maintainable_id =  strtok($location, 'Property');
                 
                  $maintainable = new Maintainable();
                  $maintainable->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                  $maintainable->maintenable_id =  $maintainable_id;
                  $maintainable->maintenable_type = 'App\Models\Property';
                
                  $maintainable->save();

                  break;
                case Str::contains($location, 'Site'):

                  $maintainable_id =  strtok($location, 'Site');

                  $maintainable = new Maintainable();
                  $maintainable->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                  $maintainable->maintenable_id =  $maintainable_id;
                  $maintainable->maintenable_type = 'App\Models\Site';
                
                  $maintainable->save();
                  
                  break;
               
                  
              } 

          
            }

            DB::commit();

            return redirect()->back()
            ->with([
              ActionStatusConstants::SUCCESS => trans('maintenance.maintenance_created_successfully')
            ]);

          } catch (\Exception $e) {


            Log::error(" in MaintenanceController - saveNewMaintenence function " . "save a new maintenance " . $maintenance_job->maintenance_job_title . " was not successful " . " by user " . $user->first_name . " " . $user->last_name);
            Log::error($e->getMessage());

            DB::rollBack();

            return redirect()->back([
                'code' => ActionStatusConstants::FAILURE,
                'message' => trans('maintenance.maintenance_not_created'),
            ]);

        }
          

      }

      public function ajaxGetResidentReporter(Request $request){

              $user = Sentinel::getUser();

              Log::info(" in MaintenanceController- ajaxGetResidentReporter function " . " try to get list of resident reporter based on selected locations  ------- by user " . $user->first_name . " " . $user->last_name);
              
              try {

              $rooms = [];
              $locations = $request->locations;
              
              foreach($locations as $location){
                if( Str::contains($location, 'Room')){

                  $room_id_part = strtok($location, 'Room');
                  $rooms[] = $room_id_part;
                }
              }

              $residents = $this->getMaintenanceResidentInfo($rooms); 
              

              return response()->json(
                [
                    'code' => ActionStatusConstants::SUCCESS,
                    'residents'=>$residents,
                    'message' => trans('resident.other_contacts_not_updated'),
                ]

              );

            } catch (\Exception $e) {
              Log::error("in TemplatesController- listTemplates function list templates " . " by user "
                  . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

              return response()->json()->with([ActionStatusConstants::ERROR=>  trans('maintenance.get_resident_reporter_was_not_successful')]);

          }


      }


      public function showMaintenanceDetailPage($maintenanceId){

        $user = Sentinel::getUser();

            Log::info(" in MaintenanceController- showMaintenanceDetailPage function " . " try to go to maintenance detail page  ------- by user " . $user->first_name . " " . $user->last_name);

            try {
        
             $maintenance = MaintenanceJob::findOrFail($maintenanceId); 

              //get all businesses
            $saas_client_businesses = SaasClientBusiness::all();


             //get all maintenance category
             $maintenance_category = MaintenanceJobCategoryRef::all();

             //get all maintenance status
             $maintenance_status = MaintenanceJobStatusRef::all();

            
             $maintenance_job_detail = MaintenanceJobDetail::where('id_maintenance_job','=',$maintenanceId)->get();

            

             //get all the conductors
            $contactors = Contractor::all();

            //get all users as reporters
            $reporters = User::all();

            $maintenance_timelines = $this->getMaintenanceTimelineInfo($maintenanceId); 

            // dd( $maintenance_timelines);

            foreach($maintenance_timelines as $maintenance_timeline){

              $maintenance_log_model = new MaintenanceLog();
              $maintenance_timeline->log_date_time = $maintenance_log_model->getLogDateTimeAttribute($maintenance_timeline->log_date_time);

          }

          $maintainables = Maintainable::where('id_maintenance_job','=',$maintenanceId)->get();

             $locations = [];

            $rooms = Room::all();

            foreach($rooms as $room){
              $room->id = 'Room'.$room->id_room;
              $room->name = 'Room'.' '.$room->room_number_full;

            }

            foreach($rooms as $room){
              $locations[] = $room;
            }

            $properties = Property::all();
            
            foreach($properties as $property){
              $property->id = 'Property'.$property->id_property;
              $property->name = 'Property'.' '.$property->property_name;

            }

            foreach($properties as $property){
              $locations[] = $property;
            }


            $sites = Site::all();


            foreach($sites as $site){
              $site->id = 'Site'.$site->id_site;
              $site->name = 'Site'.' '.$site->site_full_name;

            }
            foreach($sites as $site){
              $locations[] = $site;
            }

             //get all maintenance priorities
             $priorities = MaintenanceJobPriorityRef::all();

              return view('maintenance::maintenance_detail',
              [
                'maintenance' => $maintenance,
                'saas_client_businesses' => $saas_client_businesses,
                'maintenance_categories' => $maintenance_category,
                'locations' => $locations,
                'priorities' => $priorities,
                'maintenance_status'=>$maintenance_status,
                'maintenance_job_detail'=>$maintenance_job_detail[0],
                'contactors'=>$contactors,
                'reporters'=>$reporters,
                'maintenance_timelines'=>$maintenance_timelines,


              ]
          );

            } catch (\Exception $e) {
                Log::error("in MaintenanceController- showMaintenanceDetailPage function  " . " by user "
                . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

            return view('maintenance::create_maintenance')->with([ActionStatusConstants::ERROR=>  trans('maintenance.you_can_not_see_maintenance_detail_page')]);

            }
      }


}
