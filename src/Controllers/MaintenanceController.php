<?php

namespace Odisse\Maintenance\Controllers;

use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobDocument;
use Odisse\Maintenance\Models\MaintenanceJobDetail;
use Odisse\Maintenance\Models\Maintainable;
use Odisse\Maintenance\Models\MaintenanceJobStatusHistory;
use Odisse\Maintenance\Models\MaintenanceJobPriorityHistory;
use Odisse\Maintenance\Models\Contractor;
use Odisse\Maintenance\Models\MaintenanceJobSlaRef;
use Odisse\Maintenance\Models\MaintenanceJobSla;
use Odisse\Maintenance\App\SLP\HistoricalDataManagement\HistoricalMaintenanceManager;

use App\Models\User;
use App\SLP\Enum\ActionStatusConstants;
use App\SLP\Enum\BookingStatusConstant;
use Odisse\Maintenance\App\SLP\Enum\MaintenanceStatusConstants;
use Odisse\Maintenance\App\SLP\Enum\BookingStatusConstants;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use Illuminate\Support\Str;
use App\SLP\Formatter\SystemDateFormats;
use Odisse\Maintenance\Models\MaintenanceLog;
use Odisse\Maintenance\Models\MaintenanceJobStaffHistory;
use Illuminate\Http\Request;


use App\Models\SaasClientBusiness;
use App\Models\Room;
use App\Models\Property;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Odisse\Maintenance\App\Traits\MaintenanceDetails;
use Odisse\Maintenance\App\Traits\MaintenanceTimelineDetails;
use Odisse\Maintenance\Models\ManintenanceJob;


use App\Http\General\UserData;
use Illuminate\Support\Facades\Http;
use Sentinel;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
    use MaintenanceDetails;
    use MaintenanceTimelineDetails;

    public function testFunc()
    {
        return view('maintenance::test', ['title' => 'sample component']);
    }



    public function newTest()
    {
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

            $locations = $this->getMaintainables();;

            $jobs = MaintenanceJob::all();

            return view(
                'maintenance::create_maintenance',
                // UserData::getTheme().'.m.create_maintenance',
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

      public function ajaxUploadMaintenanceFile(Request $request)
      {


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

              //make a new directory for uploaded documents
              $maintenance_file_path = config('maintenances.maintenance_file_path');

              $path = $maintenance_file_path . 'uploaded_files/' ;
              if (!\File::exists($path)) {
                  \File::makeDirectory($path, 0755, true);
              }


              //save file in the directory
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

      public function ajaxFindMaintenanceTitle(Request $request)
      {

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

      public function saveNewMaintenence(Request $request)
      {


          $user = Sentinel::getUser();


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

              Log::error("in MaintenanceController- saveNewMaintenence function ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);


              return redirect()->back()
              ->withErrors($validator)
              ->withInput();
          }

          Log::info(" in MaintenanceController- saveNewMaintenence function " . " try  to save new maintenance   ------- by user " . $user->first_name . " " . $user->last_name);


          try {
              DB::beginTransaction();


              //save a new maintenance job
              $maintenance_job = new MaintenanceJob();
              $maintenance_job->id_saas_client_business =  $request->saas_client_business;
              $maintenance_job->id_parent_job = 1;
              $maintenance_job->id_saas_staff_reporter = $user->id;
              $maintenance_job->job_report_date_time = $request->maintenance_date;
              $maintenance_job->id_maintenance_job_category = $request->maintenance_category;
              $maintenance_job->id_maintenance_job_priority = $request->priority;
              $maintenance_job->id_maintenance_job_status = MaintenanceStatusConstants::OPUN;
              $maintenance_job->maintenance_job_title = $request->maintenance_title;
              $maintenance_job->maintenance_job_description = $request->description;
              $maintenance_job->id_resident_reporter = $request->resident_reporter;
              $maintenance_job->maintenance_job_active = 1;

              $maintenance_job->save();


              $HistoricalMaintenanceManager = new HistoricalMaintenanceManager();
              $HistoricalMaintenanceManager->insertHistory($maintenance_job);




              $date_time = $request->maintenance_date ? Carbon::createFromFormat(SystemDateFormats::getDateTimeFormat(), $request->maintenance_date)->format('Y-m-d') : null;


              //save a new maintenance job detail
              $maintenance_job_detail = new MaintenanceJobDetail();
              $maintenance_job_detail->id_maintenance_job =  $maintenance_job->id_maintenance_job;
              $maintenance_job_detail->maintenance_job_detail_date_time = $date_time;
              $maintenance_job_detail->id_staff = null;
              $maintenance_job_detail->job_detail_note = null;
              $maintenance_job_detail->maintenance_job_detail_active = 1;

              $maintenance_job_detail->save();


              //save a new status history for maintenance job
              $maintenance_job_status_history = new MaintenanceJobStatusHistory();
              $maintenance_job_status_history->id_maintenance_job =  $maintenance_job->id_maintenance_job;
              $maintenance_job_status_history->id_maintenance_staff = $user->id;
              $maintenance_job_status_history->id_maintenance_job_status = MaintenanceStatusConstants::OPUN;
              $maintenance_job_status_history->maintenance_status_start_date_time = $request->maintenance_date;
              $maintenance_job_status_history->maintenance_status_end_date_time = null;
              $maintenance_job_status_history->maintenance_job_status_history_active = 1;

              $maintenance_job_status_history->save();

              //save a new priority history for maintenance job
              $maintenance_job_priority_history = new MaintenanceJobPriorityHistory();
              $maintenance_job_priority_history->id_maintenance_job =  $maintenance_job->id_maintenance_job;
              $maintenance_job_priority_history->id_maintenance_job_priority_ref = $request->priority;
              $maintenance_job_priority_history->priority_start_date_time = $request->maintenance_date;
              $maintenance_job_priority_history->priority_end_date_time = null;
              $maintenance_job_priority_history->maintenance_job_priority_history_active = 1;

              $maintenance_job_priority_history->save();



              $maintenance_file_path = config('maintenances.maintenance_file_path');

              $path = $maintenance_file_path . 'uploaded_files/' ;
              if (!\File::exists($path)) {
                  \File::makeDirectory($path, 0755, true);
              }

              $files = $request->files;



            foreach($files as $upload_file) {
                foreach($upload_file as $file) {


                $fileName = date('Y-m-d').'_'.$file->getClientOriginalName();

                // File extension
                $extension = $file->getClientOriginalExtension();


                  $file_description = $request->file_description;

                  $file->move($path, $fileName);




                  //save documents of maintenance job
                  $maintenance_job_document = new MaintenanceJobDocument();
                  $maintenance_job_document->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                  $maintenance_job_document->document_name = $fileName;
                  $maintenance_job_document->document_address = $path;
                  $maintenance_job_document->document_extention = $extension;
                  $maintenance_job_document->description = $file_description;
                  $maintenance_job_document->maintenance_job_document_active = 1;


                  $maintenance_job_document->save();
               }
            }

              $log_note = $user->first_name . " " . $user->last_name." created a new maintenance titled : ".$maintenance_job->maintenance_job_title ;

              //add a log for saving a new maintenance job
              $maintenance_log = new MaintenanceLog();
              $maintenance_log->id_maintenance_job =  $maintenance_job->id_maintenance_job;
              $maintenance_log->id_staff = $user->id;
              $maintenance_log->log_date_time = $request->maintenance_date;
              $maintenance_log->log_note = $log_note;

              $maintenance_log->save();

              $locations = $request->locations;


              //save all maintenance locations in maintainable table
              foreach($locations as $location) {

                  switch ($location) {
                      case  Str::contains($location, 'Room'):

                          $maintainable_id =  strtok($location, 'Room');

                          $maintainable = new Maintainable();
                          $maintainable->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                          $maintainable->maintenable_id =  $maintainable_id;
                          $maintainable->maintenable_type = 'App\Models\Rooms';

                          $maintainable->save();

                          $active_booking = DB::table('booking_room')
                          ->join('booking', 'booking_room.id_booking', '=', 'booking.id_booking')
                          ->where('booking_room.id_room', $maintainable_id)
                          ->where('booking.booking_status', BookingStatusConstants::Active)
                          ->where('booking_room.room_check_out_date_time', null)
                          ->select('booking.*', 'booking_room.*')
                          ->groupBy('booking_room.id_booking_room')
                          ->groupBy('booking.id_booking')
                           ->get();


                          if($active_booking->isNotEmpty()) {
                              $id_client = $active_booking[0]->id_client;

                          } else {
                              $id_client = null;
                          }

                          $maintenance_sla_ref = MaintenanceJobSlaRef::where('id_client', $id_client)
                          ->where('id_maintenance_job_priority_ref', $request->priority)->where('id_saas_client_business', $request->saas_client_business)->get();

                          if($maintenance_sla_ref->isNotEmpty()) {

                              $existence_of_maintenance_job_sla = MaintenanceJobSla::where('id_maintenance_job', $maintenance_job->id_maintenance_job)->where('maintenance_job_sla_active', 1)->get();

                              if($existence_of_maintenance_job_sla->isEmpty()) {

                                  $maintenance_job_sla = new MaintenanceJobSla();
                                  $maintenance_job_sla->id_maintenance_job_sla_ref = $maintenance_sla_ref[0]->id_maintenance_job_sla_ref;
                                  $maintenance_job_sla->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                                  $maintenance_job_sla->maintenance_job_sla_active = 1;

                                  $maintenance_job_sla->save();


                              }

                          }


                          break;

                      case Str::contains($location, 'Property'):

                          $maintainable_id =  strtok($location, 'Property');

                          $maintainable = new Maintainable();
                          $maintainable->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                          $maintainable->maintenable_id =  $maintainable_id;
                          $maintainable->maintenable_type = 'App\Models\Property';

                          $maintainable->save();

                          $id_client = null;

                          $maintenance_sla_ref = MaintenanceJobSlaRef::where('id_client', $id_client)
                          ->where('id_maintenance_job_priority_ref', $request->priority)->where('id_saas_client_business', $request->saas_client_business)->get();


                          if($maintenance_sla_ref->isNotEmpty()) {

                            $existence_of_maintenance_job_sla = MaintenanceJobSla::where('id_maintenance_job', $maintenance_job->id_maintenance_job)->where('maintenance_job_sla_active', 1)->get();

                            if($existence_of_maintenance_job_sla->isEmpty()) {

                                $maintenance_job_sla = new MaintenanceJobSla();
                                $maintenance_job_sla->id_maintenance_job_sla_ref = $maintenance_sla_ref[0]->id_maintenance_job_sla_ref;
                                $maintenance_job_sla->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                                $maintenance_job_sla->maintenance_job_sla_active = 1;

                                $maintenance_job_sla->save();


                              }

                           }


                          break;
                      case Str::contains($location, 'Site'):

                          $maintainable_id =  strtok($location, 'Site');

                          $maintainable = new Maintainable();
                          $maintainable->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                          $maintainable->maintenable_id =  $maintainable_id;
                          $maintainable->maintenable_type = 'App\Models\Site';

                          $maintainable->save();


                          $id_client = null;

                          $maintenance_sla_ref = MaintenanceJobSlaRef::where('id_client', $id_client)
                          ->where('id_maintenance_job_priority_ref', $request->priority)->where('id_saas_client_business', $request->saas_client_business)->get();


                          if($maintenance_sla_ref->isNotEmpty()) {

                            $existence_of_maintenance_job_sla = MaintenanceJobSla::where('id_maintenance_job', $maintenance_job->id_maintenance_job)->where('maintenance_job_sla_active', 1)->get();

                            if($existence_of_maintenance_job_sla->isEmpty()) {

                                $maintenance_job_sla = new MaintenanceJobSla();
                                $maintenance_job_sla->id_maintenance_job_sla_ref = $maintenance_sla_ref[0]->id_maintenance_job_sla_ref;
                                $maintenance_job_sla->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                                $maintenance_job_sla->maintenance_job_sla_active = 1;

                                $maintenance_job_sla->save();


                              }

                           }

                          break;


                  }


              }



              // session(['success' => 'value']);
              DB::commit();


              $status = ActionStatusConstants::SUCCESS;
              $message = 'Maintenance created successfully';



          } catch (\Exception $e) {

              Log::error(" in MaintenanceController - saveNewMaintenence function : save a new maintenance  was not successful by user " . $user->first_name . " " . $user->last_name);
              Log::error($e->getMessage());

              DB::rollBack();


              $status = ActionStatusConstants::ERROR;
              $message = trans('maintenance:maintenance.maintenance_not_created');

              return redirect()->back()
                  ->withErrors($message)
                  ->withInput();

          }
        return redirect('/maintenance/dashboard')
                ->with(
                    [ $status  => $message ]
                );




      }

      public function ajaxGetResidentReporter(Request $request)
      {

          $user = Sentinel::getUser();

          Log::info(" in MaintenanceController- ajaxGetResidentReporter function " . " try to get list of resident reporter based on selected locations  ------- by user " . $user->first_name . " " . $user->last_name);

          try {

              $rooms = [];
              $locations = $request->locations;

              foreach($locations as $location) {

                  if(Str::contains($location, 'Room')) {

                      $room_id_part = strtok($location, 'Room');
                      $rooms[] = $room_id_part;
                  }
              }

              if(sizeof($rooms) == 0) {
                  $residents = [];
              } else {
                  $residents = $this->getMaintenanceResidentInfo($rooms);
              }

              return response()->json(
                  [
                    'code' => ActionStatusConstants::SUCCESS,
                    'residents'=>$residents,
                    'message' => trans('maintenance::maintenance.get_resident_was_successful'),
                ]
              );

          } catch (\Exception $e) {
              Log::error("in TemplatesController- listTemplates function list templates " . " by user "
                  . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

              return response()->json([ActionStatusConstants::ERROR=>  trans('maintenance::maintenance.get_resident_reporter_was_not_successful')]);

          }


      }


      //api to load residents of location in portal area
      public function getLocationResidents(Request $request)
      {

        //TODO fix the problem on hardcoding the business id


        // if($request->has('business') and $request->business != null) {

            //get maintenances of specific business

            $businesses = config('maintenances.businesses_name');

            $url =$businesses[0]['maintenance_api_url'].'/api/maintenance/resident_reporter';
            $response = Http::get($url, $request->all());

            return $response;


      }


      public function showMaintenanceDetailPage($maintenanceId)
      {

          $user = Sentinel::getUser();

          Log::info(" in MaintenanceController- showMaintenanceDetailPage function " . " try to go to maintenance detail page  ------- by user " . $user->first_name . " " . $user->last_name);

          try {

              $maintenance = MaintenanceJob::where('maintenance_job_active', 1)->where('id_maintenance_job',$maintenanceId)->first();
              if( !$maintenance )
              {

              return redirect('/maintenance/dashboard')->withErrors('Maintenance not exists');
            }


              //get all businesses
              $saas_client_businesses = SaasClientBusiness::all();


              //get all maintenance category
              $maintenance_category = MaintenanceJobCategoryRef::all();

              //get all maintenance status
              $maintenance_status = MaintenanceJobStatusRef::all();

              $maintenance_job_detail = MaintenanceJobDetail::where('id_maintenance_job', '=', $maintenanceId)->first();


              //get all the conductors
              $contactors = Contractor::all();

              //get all users as reporters
              $reporters = User::all();


              $maintainables = Maintainable::where('id_maintenance_job', '=', $maintenanceId)->where('maintainable_active', 1)->get();




              foreach($maintainables as $maintainable) {

                  switch ($maintainable) {
                      case  Str::contains($maintainable->maintenable_type, 'Room'):

                          $maintainable->id_location = 'Room'.$maintainable->maintenable_id;

                          break;

                      case Str::contains($maintainable->maintenable_type, 'Property'):

                          $maintainable->id_location = 'Property'.$maintainable->maintenable_id;

                          break;
                      case Str::contains($maintainable->maintenable_type, 'Site'):

                          $maintainable->id_location = 'Site'.$maintainable->maintenable_id;

                          break;

                  }


              }



              $locations = $this->getMaintainables();



              //get all maintenance priorities
              $priorities = MaintenanceJobPriorityRef::all();

              $maintenance_documents = MaintenanceJobDocument::where('id_maintenance_job', $maintenanceId)->get();

              session(['active_tab' => 'maintenanceDetail']);

              return view(
                  'maintenance::maintenance_detail',
                  [
                'maintenance' => $maintenance,
                'saas_client_businesses' => $saas_client_businesses,
                'maintenance_categories' => $maintenance_category,
                'locations' => $locations,
                'priorities' => $priorities,
                'maintenance_status'=>$maintenance_status,
                'maintenance_job_detail'=>$maintenance_job_detail,
                'contactors'=>$contactors,
                'reporters'=>$reporters,
                'maintainables'=>$maintainables,
                'maintenance_documents'=>$maintenance_documents,


              ]
              )->with(['active_tab' => 'maintenanceDetail']);

          } catch (\Exception $e) {
              Log::error("in MaintenanceController- showMaintenanceDetailPage function  " . " by user "
              . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

              return redirect('/maintenance/dashboard')->with([ActionStatusConstants::ERROR=>  trans('maintenance::maintenance.you_can_not_see_maintenance_detail_page')]);

          }
      }

      public function editMaintenanceDetail(Request $request)
      {

          $user = Sentinel::getUser();

          Log::info(" in MaintenanceController- editMaintenanceDetail function " . " try to save details of maintenance   ------- by user " . $user->first_name . " " . $user->last_name);

          try {
              DB::beginTransaction();

              $note = "";


              $now = Carbon::create('now');


              $id_maintenance = $request->id_maintenance;

              // get maintenance data before edit
              $maintenance_old_data = MaintenanceJob::findOrFail($id_maintenance);

              //get all maintenance detail before edit
              $maintenance_detail_old_data = MaintenanceJobDetail::where('id_maintenance_job', $id_maintenance)->first();


                if($request->coment != null) {

                    $coment_note = $user->first_name . " " . $user->last_name." Added a Comment : ".$request->coment;
                    $maintenance_log = new MaintenanceLog();
                    $maintenance_log->id_maintenance_job =  $id_maintenance;
                    $maintenance_log->id_staff =  $user->id;
                    $maintenance_log->log_date_time =  $now->format(SystemDateFormats::getDateTimeFormat());
                    //   $maintenance_log->id_maintenance_job_priority = $request->priority;
                    $maintenance_log->log_note = $coment_note;

                    $maintenance_log->save();
                }

               //check if maintenance title has been changed
               if($maintenance_old_data->maintenance_job_title != $request->maintenance_title) {

                // edit title of maintenance job
                $maintenance_old_data->update([
                    'maintenance_job_title' => $request->maintenance_title
                        ]);

               $note = $note. " ". $user->first_name . " " . $user->last_name." changed maintenance title to ".$request->maintenance_title."\r\n";


               }


              //check if maintenance status has been changed
              if($maintenance_old_data->id_maintenance_job_status != $request->maintenance_status) {


                $this->setJobStartAndFinishDateTime($user,$id_maintenance,$request->maintenance_status);


                  // edit status of maintenance job
                  $maintenance_old_data->update([
                    'id_maintenance_job_status' => $request->maintenance_status
                        ]);

                  //get data of maintenance status history
                  $previous_maintenance_job_status_history = MaintenanceJobStatusHistory::where('id_maintenance_job', '=', $id_maintenance)->where('maintenance_status_end_date_time', null)->first();

                  //update data of maintenance status history
                  $previous_maintenance_job_status_history->update([
                    'maintenance_status_end_date_time' => $now->format(SystemDateFormats::getDateTimeFormat())
                        ]);

                  //make a new history for maintenance status history
                  $maintenance_job_status_history = new MaintenanceJobStatusHistory();
                  $maintenance_job_status_history->id_maintenance_job =  $id_maintenance;
                  $maintenance_job_status_history->id_maintenance_staff =  $user->id;
                  $maintenance_job_status_history->id_maintenance_job_status = $request->maintenance_status;
                  $maintenance_job_status_history->maintenance_status_start_date_time = $now->format(SystemDateFormats::getDateTimeFormat());
                  $maintenance_job_status_history->maintenance_status_end_date_time = null;
                  $maintenance_job_status_history->maintenance_job_status_history_active = 1;

                  $maintenance_job_status_history->save();


                  $status_ref = MaintenanceJobStatusRef::findOrFail($request->maintenance_status);

                  $note = $note. " ". $user->first_name . " " . $user->last_name." changed maintenance status to ".$status_ref->job_status_name."\r\n";


              }

              //check if maintenance reporter has been changed
              if($maintenance_old_data->id_saas_staff_reporter != $request->maintenance_reporter) {

                  // edit reporter of maintenance job
                  $maintenance_old_data->update([
                    'id_saas_staff_reporter' => $request->maintenance_reporter
                        ]);

                  $staff_reporter = User::findOrFail($request->maintenance_reporter);

                  $note = $note. " " . $user->first_name . " " . $user->last_name." changed maintenance reporter to ".$staff_reporter->first_name." ".$staff_reporter->last_name;

              }

              //check if maintenance staff has been changed
              if($request->maintenance_assignee != null) {

                Log::info("going to save new assignee");

                  if($maintenance_detail_old_data->id_staff != $request->maintenance_assignee) {


                    Log::info("1");
                      // edit staff of maintenance job detail

                      $maintenance_detail_old_data->update([
                        'id_staff' => $request->maintenance_assignee
                        ]);

                        Log::info("old data updated");
                      //get data of maintenance staff history
                      $previous_maintenance_job_staff_history = MaintenanceJobStaffHistory::where('id_maintenance_job', '=', $id_maintenance)
                      ->where('staff_end_date_time', null)->get();

                      //update data of maintenance status history
                      if(sizeof($previous_maintenance_job_staff_history) > 0 ) {
                            $previous_maintenance_job_staff_history = $previous_maintenance_job_staff_history[0];
                          $previous_maintenance_job_staff_history->update([
                                'staff_end_date_time' => $now->format(SystemDateFormats::getDateTimeFormat())
                            ]);

                        Log::info("prev job staff history updated");

                      }

                      //make a new history for maintenance staff history
                      $maintenance_job_staff_history = new MaintenanceJobStaffHistory();
                      $maintenance_job_staff_history->id_maintenance_job =  $id_maintenance;
                      $maintenance_job_staff_history->id_maintenance_staff =  $user->id;
                      $maintenance_job_staff_history->id_maintenance_assignee =  $request->maintenance_assignee;
                      $maintenance_job_staff_history->staff_assign_date_time = $now->format(SystemDateFormats::getDateTimeFormat());
                      $maintenance_job_staff_history->staff_start_date_time = $now->format(SystemDateFormats::getDateTimeFormat());
                      $maintenance_job_staff_history->staff_end_date_time = null;
                      $maintenance_job_staff_history->maintenance_job_staff_history_active = 1;

                      $maintenance_job_staff_history->save();

                    Log::info("job staff hist saved");

                      $new_staff = Contractor::findOrFail($request->maintenance_assignee);


                    Log::info("after find or fail");
                      $note = $note. " " .$user->first_name . " " . $user->last_name." changed maintenance staff to ".$new_staff->name;


                  }
              }
              //check if maintenance priority has been changed
              if($maintenance_old_data->id_maintenance_job_priority != $request->priority) {

                  // edit priority of maintenance job
                  $maintenance_old_data->update([
                    'id_maintenance_job_priority' => $request->priority
                    ]);

                  //get data of maintenance priority history
                  $previous_maintenance_job_priority_history = MaintenanceJobPriorityHistory::where('id_maintenance_job', '=', $id_maintenance)->where('priority_end_date_time', null)->first();

                  //update data of maintenance priority history
                  $previous_maintenance_job_priority_history->update([
                    'priority_end_date_time' => $now->format(SystemDateFormats::getDateTimeFormat())
                  ]);

                  //make a new history for maintenance priority history
                  $maintenance_job_priority_history = new MaintenanceJobPriorityHistory();
                  $maintenance_job_priority_history->id_maintenance_job =  $id_maintenance;
                  $maintenance_job_priority_history->id_maintenance_job_priority_ref =  $request->priority;
                  $maintenance_job_priority_history->priority_start_date_time = $now->format(SystemDateFormats::getDateTimeFormat());
                  $maintenance_job_priority_history->priority_end_date_time =null;
                  $maintenance_job_priority_history->maintenance_job_priority_history_active = 1;

                  $maintenance_job_priority_history->save();

                  $priority_ref = MaintenanceJobPriorityRef::findOrFail($request->priority);

                  $note = $note.$user->first_name . " " . $user->last_name." changed maintenance priority to ".$priority_ref->priority_name;


              }

              //check if maintenance category has been changed
              if($maintenance_old_data->id_maintenance_job_category != $request->maintenance_category) {

                  // edit category of maintenance job
                  $maintenance_old_data->update([
                    'id_maintenance_job_category' => $request->maintenance_category
                    ]);



                  $category_ref = MaintenanceJobCategoryRef::findOrFail($request->maintenance_category);

                  $note = $note.$user->first_name . " " . $user->last_name." changed maintenance category to ".$category_ref->job_category_name;


              }

              $maintainable_location_id = [];

              //get locations of maintenance job
              $maintainables = Maintainable::where('id_maintenance_job', $id_maintenance)->where('maintainable_active', 1)->get();

              //add a new attribute for all maintenance locations
              foreach($maintainables as $maintainable) {

                  switch ($maintainable) {
                      case  Str::contains($maintainable->maintenable_type, 'Room'):

                          $maintainable->id_location = 'Room'.$maintainable->maintenable_id;

                          break;

                      case Str::contains($maintainable->maintenable_type, 'Property'):

                          $maintainable->id_location = 'Property'.$maintainable->maintenable_id;

                          break;
                      case Str::contains($maintainable->maintenable_type, 'Site'):

                          $maintainable->id_location = 'Site'.$maintainable->maintenable_id;

                          break;
                  }
                  $maintainable_location_id [] = $maintainable->id_location;

              }

              //check if maintenance locations has been changed
              if($maintainable_location_id != $request->locations) {

                  $maintainables = Maintainable::where('id_maintenance_job', $id_maintenance)->where('maintainable_active', 1)->get();

                  // soft delete previous locations of maintenance job

                  foreach($maintainables as $maintainable) {
                      $maintainable->update([
                           'maintainable_active' => 0
                       ]);
                  }
                  $locations = $request->locations;

                  //add selected locations to database
                  foreach($locations as $location) {

                      switch ($location) {
                          case  Str::contains($location, 'Room'):

                              $maintainable_id =  strtok($location, 'Room');

                              $maintainable = new Maintainable();
                              $maintainable->id_maintenance_job =  $maintenance_old_data->id_maintenance_job;
                              $maintainable->maintenable_id =  $maintainable_id;
                              $maintainable->maintenable_type = 'App\Models\Rooms';

                              $maintainable->save();
                              break;

                          case Str::contains($location, 'Property'):

                              $maintainable_id =  strtok($location, 'Property');

                              $maintainable = new Maintainable();
                              $maintainable->id_maintenance_job =  $maintenance_old_data->id_maintenance_job;
                              $maintainable->maintenable_id =  $maintainable_id;
                              $maintainable->maintenable_type = 'App\Models\Property';

                              $maintainable->save();

                              break;
                          case Str::contains($location, 'Site'):

                              $maintainable_id =  strtok($location, 'Site');

                              $maintainable = new Maintainable();
                              $maintainable->id_maintenance_job =  $maintenance_old_data->id_maintenance_job;
                              $maintainable->maintenable_id =  $maintainable_id;
                              $maintainable->maintenable_type = 'App\Models\Site';

                              $maintainable->save();

                              break;


                      }


                  }


                  $note = $note.$user->first_name . " " . $user->last_name." changed maintenance locations ";
                  //TODO


              }



              if($note != null) {
                  //add a new log for all maintenance changes
                  $maintenance_log = new MaintenanceLog();
                  $maintenance_log->id_maintenance_job =  $id_maintenance;
                  $maintenance_log->id_staff =  $user->id;
                  $maintenance_log->log_date_time =  $now->format(SystemDateFormats::getDateTimeFormat());
                //   $maintenance_log->id_maintenance_job_priority = $request->priority;
                  $maintenance_log->log_note = $note;

                  $maintenance_log->save();
              }

              $HistoricalMaintenanceManager = new HistoricalMaintenanceManager();
             $HistoricalMaintenanceManager->insertHistory($maintenance_old_data);


              DB::commit();


              return redirect()->back()
              ->with([
                ActionStatusConstants::SUCCESS => trans('maintenance::maintenance.maintenance_edited_successfully')
              ]);

          } catch (\Exception $e) {


              Log::error(" in MaintenanceController - editMaintenanceDetail function " . " edita maintenance " . $maintenance_old_data->maintenance_job_title . " was not successful " . " by user " . $user->first_name . " " . $user->last_name);
              Log::error($e->getMessage());

              DB::rollBack();

              return redirect()->back()->with([
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => trans('maintenance::maintenance.maintenance_not_edited'),
              ]);

          }

      }

      public function ajaxGetMaintenanceDocuments(Request $request)
      {

          $user = Sentinel::getUser();

          Log::info(" in MaintenanceController- ajaxGetMaintenanceDocuments function " . " try to get documents of maintenance   ------- by user " . $user->first_name . " " . $user->last_name);


          //get all documents of a maintenance job
          $maintenance_documents = MaintenanceJobDocument::where('id_maintenance_job', $request->maintenance_id)->get();


          return response()->json(
              [
                'code' => ActionStatusConstants::SUCCESS,
                'report'=>$maintenance_documents,
                'message' => trans('maintenance::maintenance.get_maintenance_document_was_successful'),
              ]
          );
      }

      public function ajaxGetMaintenanceTimeline(Request $request)
      {

          $user = Sentinel::getUser();

          Log::info(" in MaintenanceController- ajaxGetMaintenanceDocuments function " . " try to get documents of maintenance   ------- by user " . $user->first_name . " " . $user->last_name);

          $maintenance_id = $request->maintenance_id;

          //get maintenance logs for timeline
          $maintenance_timelines = $this->getMaintenanceTimelineInfo($maintenance_id);


          foreach($maintenance_timelines as $maintenance_timeline) {

            $maintenance_log_model = new MaintenanceLog();
            $maintenance_timeline->log_date_time = $maintenance_log_model->getLogDateTimeAttribute($maintenance_timeline->log_date_time);

        }

          return response()->json(
              [
                'code' => ActionStatusConstants::SUCCESS,
                'report'=>$maintenance_timelines,
                'message' => trans('maintenance::maintenance.get_maintenance_timeline_was_successful'),
              ]
          );
      }
      public function ajaxDeleteMaintenanceDocument(Request $request)
      {

          $user = Sentinel::getUser();

          $now = Carbon::create('now');


          Log::info(" in MaintenanceController- ajaxDeleteMaintenanceDocument function " . " try to delete  maintenance document  ------- by user " . $user->first_name . " " . $user->last_name);


          try {
              DB::beginTransaction();

              $id_maintenance_document = $request->id_maintenance_job_document;

              $maintenance_id = $request->maintenance_id;

              $maintenance_job = MaintenanceJob::findOrFail($maintenance_id);

              $maintenance_job_document = MaintenanceJobDocument::findOrFail($id_maintenance_document);

              $file = $maintenance_job_document->document_address."/".$maintenance_job_document->document_name;
              unlink($file);

              //delete maintenance document
              MaintenanceJobDocument::where('id_maintenance_job_document', $id_maintenance_document)->delete();



              $note = $user->first_name . " " . $user->last_name." Delete a Maintenance Document.";

              //make a maintenance log for deleting the document
              $maintenance_log = new MaintenanceLog();
              $maintenance_log->id_maintenance_job = $maintenance_id;
              $maintenance_log->id_staff =  $user->id;
              $maintenance_log->log_date_time =  $now->format(SystemDateFormats::getDateTimeFormat());
            //   $maintenance_log->id_maintenance_job_priority = $maintenance_job->id_maintenance_job_priority;
              $maintenance_log->log_note = $note;

              $maintenance_log->save();

              DB::commit();

              return response()->json(
                  [
                    'code' => ActionStatusConstants::SUCCESS,

                    'message' => trans('maintenance::maintenance.maintenance_document_deleted_succssfully'),
                  ]
              );
          } catch (\Exception $e) {


              Log::error(" in MaintenanceController - ajaxDeleteMaintenanceDocument function " . " delete maintenance document was not successful " . " by user " . $user->first_name . " " . $user->last_name);
              Log::error($e->getMessage());

              DB::rollBack();

              return response()->json([
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => trans('maintenance::maintenance.maintenance_document_did_not_deleted'),
              ]);

          }

      }


      private function getMaintainables()
      {
            $locations = [];

            $rooms = Room::all();

            foreach($rooms as $room) {
                $property = $room->property;
                $room->id = 'Room'.$room->id_room;
                $room->name = '[Room] '.$property->property_short_name .'/'.$room->room_number_full;

            }

            foreach($rooms as $room) {
                $locations[] = $room;
            }

            $properties = Property::all();

            foreach($properties as $property) {
                $property->id = 'Property'.$property->id_property;
                $property->name = '[Property] '.$property->property_name;

            }

            foreach($properties as $property) {
                $locations[] = $property;
            }


            // $sites = Site::all();


            // foreach($sites as $site) {
            //     $site->id = 'Site'.$site->id_site;
            //     $site->name = '[Site] '.$site->site_full_name;

            // }

            // foreach($sites as $site) {
            //     $locations[] = $site;
            // }

            return $locations;
      }
}
