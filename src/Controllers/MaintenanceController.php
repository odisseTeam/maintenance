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
use Odisse\Maintenance\App\SLP\MaintenanceOperation;


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
use App\Models\LegalCompany;
use App\SLP\Com\LinkGenerator\WikiLinkGenerator;
use Illuminate\Support\Facades\Http;
use Sentinel;
use Illuminate\Support\Facades\Validator;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\ContractorSkillRef;

class MaintenanceController extends Controller
{
    use MaintenanceDetails;
    use MaintenanceTimelineDetails;
    use MaintenanceOperation;

    public function testFunc()
    {
        return view('maintenance::test', ['title' => 'sample component']);
    }



    public function newTest()
    {
        return view('maintenance::create_maintenance');
    }


    public function getOrderNumber($property , $maintenance_job_id){

        $today = Carbon::createFromDate('now');

        //get largest maintenance no;
        $last_maintenance = MaintenanceJob::where('id_maintenance_job' ,'!=' , $maintenance_job_id)->orderBy('id_maintenance_job' , 'desc')->first();
        if($last_maintenance){
            $last_order_no_part3 = substr($last_maintenance->order_number , 11);

        }
        else{
            $last_order_no_part3 = 99;
        }

        $legal_company = LegalCompany::find($property->id_legal_company);
        if($legal_company){

            $order_no = $legal_company->short_name .'-'. $today->format('ymd').'-'.(intval($last_order_no_part3) +1);
        }
        else{
            $order_no =null;

        }
        return $order_no;

    }



    public function createNewMaintenancePage()
    {



        $user = Sentinel::getUser();

        Log::info("In maintenance package - in MaintenanceController- createNewMaintenancePage function " . " try to go to create maintenance page  ------- by user " . $user->first_name . " " . $user->last_name);

        try {

            //get all maintenance category
            $maintenance_category = MaintenanceJobCategoryRef::all();


            //get all businesses
            $saas_client_businesses = SaasClientBusiness::where('saas_client_business_active' , 1)->get();

            //get all maintenance priorities
            $priorities = MaintenanceJobPriorityRef::all();

            $locations = $this->getMaintainables();;

            $skills = ContractorSkillRef::where('contractor_skill_ref_active' , 1)->get();
            $businesses = SaasClientBusiness::where('saas_client_business_active' , 1)->get();
            $contactors = [];
            $users = null;
            $agents = null;


            $jobs = MaintenanceJob::all();
            $wiki_link = WikiLinkGenerator::GetWikiLinkOfPage('create_maintenance');


            return view(
                'maintenance::create_maintenance',
                // UserData::getTheme().'.m.create_maintenance',
                [
                          'maintenance_categories' => $maintenance_category,
                          'saas_client_businesses' => $saas_client_businesses,
                          'priorities' => $priorities,
                          'locations' => $locations,
                          'skills' => $skills,
                          'businesses' => $businesses,
                          'contactors' => $contactors,
                          'users' => $users,
                          'agents' => $agents,
                          'jobs' => $jobs,
                          'wiki_link' => $wiki_link,


                        ]
            );

        } catch (\Exception $e) {
            Log::error("In maintenance package - in MaintenanceController- createNewMaintenancePage function ".$e->getMessage() . " by user "
            . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

            return view('maintenance::create_maintenance')->with([ActionStatusConstants::ERROR=>  trans('maintenance.you_can_not_see_create_maintenance_page')]);

        }


    }

      public function ajaxUploadMaintenanceFile(Request $request)
      {


          $user = Sentinel::getUser();

          Log::info("In maintenance package in MaintenanceController- ajaxUploadMaintenanceFile function " . " try  to upload an maintenance file  ------- by user " . $user->first_name . " " . $user->last_name);


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


              Log::error("In maintenance package in MaintenanceController - ajaxUploadMaintenanceFile function " . " upload maintenance document was not successful " . " by user " . $user->first_name . " " . $user->last_name);
              Log::error("In maintenance package in MaintenanceController - ajaxUploadMaintenanceFile function " .$e->getMessage());

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

          Log::info("In maintenance package in MaintenanceController- ajaxFindMaintenanceTitle function " . " try  to get maintenance title  ------- by user " . $user->first_name . " " . $user->last_name);

          try {

              $id_maintenance_job = $request->id_maintenance_job;
              $maintenace_job = MaintenanceJob::findOrFail($id_maintenance_job);
              $maintenace_job_title_only = $maintenace_job->maintenance_job_title;
              $maintenace_job_date = $maintenace_job->job_report_date_time;

              $maintenace_job_title = $maintenace_job_title_only.$maintenace_job_date;

              Log::info("In maintenance package in MaintenanceController- ajaxFindMaintenanceTitle function ". $maintenace_job_title. " try  to get maintenance title  ------- by user " . $user->first_name . " " . $user->last_name);

              return response()->json(
                  [
                    'code' => ActionStatusConstants::SUCCESS,
                    'maintenace_job_title'=>$maintenace_job_title,
                    'message' => trans('maintenance.find_maintenance_title_was_successful'),
                    ]
              );

          } catch (\Exception $e) {
              Log::error("In maintenance package in MaintenanceController- ajaxFindMaintenanceTitle function find maintenance title " . " by user "
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
              'commencement_date'=>'nullable|date_format:' . SystemDateFormats::getDateFormat(),
              'complete_date'=>'nullable|date_format:' . SystemDateFormats::getDateFormat().'|after_or_equal:commencement_date',
              'maintenance_category'=>'required',
              'saas_client_business'=>'required',
              'locations'=>'required',
              'priority'=>'required',

            ]);
          if ($validator->fails()) {

              Log::error("In maintenance package - in MaintenanceController- saveNewMaintenence function ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

              return redirect()->back()
              ->withErrors($validator)
              ->withInput();
          }

          Log::info("In maintenance package - in MaintenanceController- saveNewMaintenence function " . " try  to save new maintenance   ------- by user " . $user->first_name . " " . $user->last_name);


          try {
              DB::beginTransaction();

              //save a new maintenance job
              $maintenance_job = new MaintenanceJob();
              $maintenance_job->id_saas_client_business =  $request->saas_client_business;
              $maintenance_job->id_parent_job = 1;
              $maintenance_job->id_saas_staff_reporter = $user->id;
              $maintenance_job->job_report_date_time = $request->maintenance_date;
              $maintenance_job->commencement_date = $request->commencement_date;
              $maintenance_job->complete_date = $request->complete_date;
              $maintenance_job->id_maintenance_job_category = $request->maintenance_category;
              $maintenance_job->id_maintenance_job_priority = $request->priority;
              $maintenance_job->id_maintenance_job_status = MaintenanceStatusConstants::OPUN;
              $maintenance_job->maintenance_job_title = $request->maintenance_title;
              $maintenance_job->maintenance_job_description = $request->description;
              $maintenance_job->id_resident_reporter = $request->resident_reporter;
              $maintenance_job->order_number = 'AAA';//by default
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

              $object = new MaintenanceJob();

              $file_description = $request->file_description;

              $this->uploadFile($files,$object,$file_description,$maintenance_job);




              $log_note = $user->first_name . " " . $user->last_name." created a new maintenance titled : ".$maintenance_job->maintenance_job_title ;

              //add a log for saving a new maintenance job
              $maintenance_log = new MaintenanceLog();
              $maintenance_log->id_maintenance_job =  $maintenance_job->id_maintenance_job;
              $maintenance_log->id_staff = $user->id;
              $maintenance_log->log_date_time = $request->maintenance_date;
              $maintenance_log->log_note = $log_note;

              $maintenance_log->save();

              $locations = $request->locations;
              $first_property = 0;


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

                        $room = Room::find($maintainable_id);
                        if($room){
                            if($first_property == 0){
                                $first_property = $room->id_property;
                            }
                            else if($first_property != $room->id_property){

                                $message = trans('maintenance::maintenance.locations_must_be_in_same_property');

                                Log::error("In maintenance package - in MaintenanceController- saveNewMaintenence function " . $message);

                                DB::rollBack();
                                $status = ActionStatusConstants::ERROR;
                                return redirect()->back()
                                    ->withError($message)
                                    ->withInput();

                            }
                        }

                        //change room_maintenance_status field of room
                        $maintenance_status = MaintenanceJobStatusRef::find($maintenance_job->id_maintenance_job_status);
                        $this->changeRoomMaintenanceStatus($maintenance_status->job_status_code , $maintainable_id);


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

                        if($first_property == 0){
                            $first_property = $maintainable_id;
                        }
                        else if($first_property != $maintainable_id){

                            $message = trans('maintenance::maintenance.locations_must_be_in_same_property');


                            Log::error("In maintenance package - in MaintenanceController- saveNewMaintenence function " . $message);

                            DB::rollBack();
                            $status = ActionStatusConstants::ERROR;
                            return redirect()->back()
                                ->withError($message)
                                ->withInput();

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


              //save assign maintenance
              $assign_response = $this->assignJobToUser($maintenance_job->id_maintenance_job , $request->user_agent , $user->id);

              if($assign_response['code'] == ActionStatusConstants::FAILURE){
                return redirect()->back()
                    ->withError($assign_response['message'])
                    ->withInput();
              }



              $property = Property::find($first_property);
              $order_number = $this->getOrderNumber($property , $maintenance_job->id_maintenance_job);
              if($order_number){
                $maintenance_job->update([
                    'order_number' => $order_number,
                  ]);
              }
              else{


                $message = trans('maintenance::maintenance.set_order_number_was_unsuccessful');


                Log::error("In maintenance package - in MaintenanceController- saveNewMaintenence function " . $message);

                DB::rollBack();
                $status = ActionStatusConstants::ERROR;
                return redirect()->back()
                    ->withError($message)
                    ->withInput();


              }





              // session(['success' => 'value']);
              DB::commit();


              $status = ActionStatusConstants::SUCCESS;
              $message = 'Maintenance created successfully';



          } catch (\Exception $e) {

              Log::error("In maintenance package - in MaintenanceController - saveNewMaintenence function : save a new maintenance  was not successful by user " . $user->first_name . " " . $user->last_name);
              Log::error("In maintenance package - in MaintenanceController - saveNewMaintenence function " .$e->getMessage());

              DB::rollBack();


              $status = ActionStatusConstants::ERROR;
              $message = trans('maintenance::maintenance.maintenance_not_created');

              return redirect()->back()
                  ->withError($message)
                  ->withInput();

            //return redirect()->back()->withError($e->getMessage());


          }
        return redirect('/maintenance/dashboard')
                ->with(
                    [ $status  => $message ]
                );




      }

      public function ajaxGetResidentReporter(Request $request)
      {

          $user = Sentinel::getUser();

          Log::info("In maintenance package - in MaintenanceController- ajaxGetResidentReporter function " . " try to get list of resident reporter based on selected locations  ------- by user " . $user->first_name . " " . $user->last_name);

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
              Log::error("In maintenance package - in MaintenanceController- ajaxGetResidentReporter function " . " by user "
                  . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

              return response()->json([ActionStatusConstants::ERROR=>  trans('maintenance::maintenance.get_resident_reporter_was_not_successful')]);

          }


      }


      //api to load residents of location in portal area
      public function getLocationResidents(Request $request)
      {


        if( $request->has('business') and $request->business != null ){

            $requested_business = $request->business;

            $businesses = config('maintenances.businesses_name');
            foreach($businesses as $business){
                if($business['id_saas_client_business'] == $requested_business){
                    $url =$business['maintenance_api_url'].'/api/maintenance/resident_reporter';
                    $response = Http::get($url, $request->all());

                    return $response;
                }
            }
        }
        else{
            return null;
        }


      }


      public function showMaintenanceDetailPage($maintenanceId)
      {

          $user = Sentinel::getUser();

          Log::info("In  aintenance package -  in MaintenanceController- showMaintenanceDetailPage function " . " try to go to maintenance detail page  ------- by user " . $user->first_name . " " . $user->last_name);

          try {

              $maintenance = MaintenanceJob::where('maintenance_job_active', 1)->where('id_maintenance_job',$maintenanceId)->first();
              if( !$maintenance )
              {

              return redirect('/maintenance/dashboard')->withError('Maintenance not exists');
            }


              //get all businesses
              $saas_client_businesses = SaasClientBusiness::all();


              //get all maintenance category
              $maintenance_category = MaintenanceJobCategoryRef::all();

              //get all maintenance status
              $maintenance_status = MaintenanceJobStatusRef::all();

              $maintenance_job_detail = MaintenanceJobDetail::where('id_maintenance_job', '=', $maintenanceId)->first();


              //get all the conductors are in maintenance location

              $room_contractors = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $maintenanceId)->
              join('maintainable','maintenance_job.id_maintenance_job','maintainable.id_maintenance_job')->where('maintainable.maintenable_type' , 'App\Models\Rooms')->
              join('room' , 'maintainable.maintenable_id' , 'room.id_room')->
              join('property' , 'room.id_property' , 'property.id_property')->
              join('city' , 'property.id_city' , 'city.id_city')->
              join('contractor' , 'maintenance_job.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
              join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
              join('contractor_location_ref', function ($join) {
                $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                $join->on('contractor_location_ref.location', '=', 'city.name');
            })->select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();

            $property_contractors = MaintenanceJob::where('maintenance_job.id_maintenance_job' , $maintenanceId)->
              join('maintainable','maintenance_job.id_maintenance_job','maintainable.id_maintenance_job')->where('maintainable.maintenable_type' , 'App\Models\Property')->
              join('property' , 'maintainable.maintenable_id' , 'property.id_property')->
              join('city' , 'property.id_city' , 'city.id_city')->
              join('contractor' , 'maintenance_job.id_saas_client_business' , 'contractor.id_saas_client_business')->where('contractor_active' , 1)->
              join ('contractor_location' , 'contractor.id_contractor' , 'contractor_location.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
              join('contractor_location_ref', function ($join) {
                  $join->on('contractor_location.id_contractor_location_ref', '=', 'contractor_location_ref.id_contractor_location_ref');
                  $join->on('contractor_location_ref.location', '=', 'city.name');
              })->select('contractor.*')->where('contractor_location_ref.contractor_location_ref_active' , 1)->get()->toArray();

              //dd($property_contractors);

              $contactors = array_unique(array_merge($room_contractors , $property_contractors) , SORT_REGULAR);
              $businesses = SaasClientBusiness::where('saas_client_business_active' , 1)->get();

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



            // get selected User/agent
            $mjsh = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenanceId )->
            whereNull('staff_end_date_time')->
            where('maintenance_job_staff_history_active' , 1)->get();


            if(count($mjsh) >1){
                return redirect('/maintenance/dashboard')->with([ActionStatusConstants::ERROR=>  trans('maintenance::maintenance.maintenance_have_multiple_assignee_please_fix_it')]);

            }
            $selected_user_agent = null;
            $selected_contractor = null;
            $selected_business = null;
            $users = null;
            $agents = null;

            if(count($mjsh) == 1){
                $selected_user_agent = $mjsh[0]->id_maintenance_assignee;
                $contractor_agent = ContractorAgent::where('id_user' , $selected_user_agent)->
                                 where('contractor_agent_active' , 1)->first();

                $users = User::where('users_active' , 1)->where('is_deleted' , 0)->
                                 join('role_users','role_users.user_id','users.id')->where('role_users_active' , 1)->
                                 join('roles','roles.id','role_users.role_id')->where('roles.name','Maintenance')->get();

                if($contractor_agent){
                    $selected_contractor = Contractor::find($contractor_agent->id_contractor);
                    $agents = Contractor::where('contractor.id_contractor' , $selected_contractor->id_contractor)->
                    join('contractor_agent','contractor_agent.id_contractor','contractor.id_contractor')->
                    join('users','users.id','contractor_agent.id_user')->get();

                }
                else{
                    $selected_business = SaasClientBusiness::where('saas_client_business.id_saas_client_business' ,'>' ,0)->
                                         join('users' , 'users.id_saas_client_business' , 'saas_client_business.id_saas_client_business')->
                                         where('users.id' , $selected_user_agent)->first();

                }

            }

            $locations = $this->getMaintainables();
            $skills = ContractorSkillRef::where('contractor_skill_ref_active' , 1)->get();


            $wiki_link = WikiLinkGenerator::GetWikiLinkOfPage('maintenance_detail');

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
                'businesses'=>$businesses,
                'contactors'=>$contactors,
                'reporters'=>$reporters,
                'maintainables'=>$maintainables,
                'maintenance_documents'=>$maintenance_documents,
		        'selected_user_agent'=>$selected_user_agent,
		        'selected_contractor'=>$selected_contractor,
		        'selected_business'=>$selected_business,
		        'users'=>$users,
		        'agents'=>$agents,
		        'skills'=>$skills,
		        'wiki_link'=>$wiki_link,


              ]
              )->with(['active_tab' => 'maintenanceDetail']);

          } catch (\Exception $e) {
              Log::error("In maintenance package - in MaintenanceController- showMaintenanceDetailPage function  " . " by user "
              . $user->first_name . " " . $user->last_name . " " . $e->getMessage());

              return redirect('/maintenance/dashboard')->with([ActionStatusConstants::ERROR=> $e->getMessage()]); //trans('maintenance::maintenance.you_can_not_see_maintenance_detail_page')]);

          }
      }

      public function editMaintenanceDetail(Request $request)
      {


          $user = Sentinel::getUser();

          Log::info("In maintenance package -  in MaintenanceController- editMaintenanceDetail function " . " try to save details of maintenance   ------- by user " . $user->first_name . " " . $user->last_name);


          $validator = Validator::make($request->all(), [
            'maintenance_title' => 'required',
            'maintenance_category'=>'required',
            'locations'=>'required',
            'priority'=>'required',
            'commencement_date'=>'nullable|date_format:' . SystemDateFormats::getDateFormat(),
            'complete_date'=>'nullable|date_format:' . SystemDateFormats::getDateFormat().'|after_or_equal:commencement_date',



          ]);
        if ($validator->fails()) {


            Log::error("in MaintenanceController- saveNewMaintenence function ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);


            return redirect()->back()
            ->withErrors($validator->errors())
            ->withInput();
        }


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
                    'maintenance_job_title' => $request->maintenance_title,
                        ]);

               $note = $note. " ". $user->first_name . " " . $user->last_name." changed maintenance title to ".$request->maintenance_title."\r\n";


               }

               //check if commencement_date has been changed
               if($maintenance_old_data->commencement_date != $request->commencement_date) {

                // edit commencement_date of maintenance job
                $maintenance_old_data->update([
                    'commencement_date'=> $request->commencement_date,
                        ]);

               $note = $note. " ". $user->first_name . " " . $user->last_name." changed commencement date to ".$request->commencement_date."\r\n";
               }


               //check if complete_date has been changed
               if($maintenance_old_data->complete_date != $request->complete_date) {

                // edit complete_date of maintenance job
                $maintenance_old_data->update([
                    'complete_date' => $request->complete_date,
                        ]);

               $note = $note. " ". $user->first_name . " " . $user->last_name." changed complete date to ".$request->complete_date."\r\n";
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

              //check if maintenance staff has been changed
              if($request->user_agent != null) {

                Log::info("In maintenance package -  in MaintenanceController- editMaintenanceDetail function " ."going to save new assignee");

                //check this task assigned to this user already
                $check = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenance_old_data->id_maintenance_job )->
                where('id_maintenance_assignee' , $request->user_agent)->
                whereNull('staff_end_date_time')->
                where('maintenance_job_staff_history_active' , 1)->get();

                if(count($check)==0 ){

                    //check if this task is assigned to another person
                    $check2 = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenance_old_data->id_maintenance_job )->
                    whereNull('staff_end_date_time')->
                    where('maintenance_job_staff_history_active' , 1)->get();

                    if(count($check2)>0){
                        foreach($check2  as $assign_staf_obj){
                            $assign_staf_obj->update([
                                'staff_end_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                            ]);
                        }

                    }


                    //insert into maintenance_job_staff table
                    $maintenance_staff = new MaintenanceJobStaffHistory([
                        'id_maintenance_job'    =>  $maintenance_old_data->id_maintenance_job,
                        'id_maintenance_staff'    =>  $user->id,
                        'id_maintenance_assignee'    =>  $request->user_agent,
                        'staff_assign_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                        'staff_start_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                        'maintenance_job_staff_history_active'  =>  1,

                    ]);
                    $maintenance_staff->save();



                    //insert into maintenance_job_staff table
                    $maintenance_log = new MaintenanceLog([
                        'id_maintenance_job'    =>  $maintenance_old_data->id_maintenance_job,
                        'id_staff'    =>  $user->id,
                        'log_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                        'log_note'  =>  trans('maintenance::dashboard.assign_maintenance_to_user'),

                    ]);
                    $maintenance_log->save();


                }else{


                }


              }
              else{
                //remove active assignee
                //check if this task is assigned to another person
                $check2 = MaintenanceJobStaffHistory::where('id_maintenance_job' ,$maintenance_old_data->id_maintenance_job )->
                whereNull('staff_end_date_time')->
                where('maintenance_job_staff_history_active' , 1)->get();

                if(count($check2)>0){
                    foreach($check2  as $assign_staf_obj){
                        $assign_staf_obj->update([
                            'staff_end_date_time'    =>$now->format(SystemDateFormats::getDateTimeFormat()),
                            'is_last_one'    =>0,
                        ]);
                    }

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
              $first_property = 0;

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


                          //change room_maintenance_status field of room
                          $maintenance = MaintenanceJob::find($id_maintenance);
                          $maintenance_status = MaintenanceJobStatusRef::find($maintenance->id_maintenance_job_status);
                          $this->changeRoomMaintenanceStatus($maintenance_status->job_status_code , $maintainable_id);



                        $room = Room::find($maintainable_id);
                        if($room){
                            if($first_property == 0){
                                $first_property = $room->id_property;
                            }
                            else if($first_property != $room->id_property){

                                $message = trans('maintenance::maintenance.locations_must_be_in_same_property');


                                Log::error($message);

                                DB::rollBack();
                                $status = ActionStatusConstants::ERROR;
                                return redirect()->back()
                                    ->withError($message)
                                    ->withInput();

                            }
                        }




                              break;

                          case Str::contains($location, 'Property'):

                              $maintainable_id =  strtok($location, 'Property');

                              $maintainable = new Maintainable();
                              $maintainable->id_maintenance_job =  $maintenance_old_data->id_maintenance_job;
                              $maintainable->maintenable_id =  $maintainable_id;
                              $maintainable->maintenable_type = 'App\Models\Property';

                              $maintainable->save();


                            if($first_property == 0){
                                $first_property = $maintainable_id;
                            }
                            else if($first_property != $maintainable_id){

                                $message = trans('maintenance:maintenance.locations_must_be_in_same_property');


                                Log::error($message);

                                DB::rollBack();
                                $status = ActionStatusConstants::ERROR;
                                return redirect()->back()
                                    ->withError($message)
                                    ->withInput();

                            }

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


              Log::error("In maintenance package -  in MaintenanceController - editMaintenanceDetail function " . " edita maintenance " . $maintenance_old_data->maintenance_job_title . " was not successful " . " by user " . $user->first_name . " " . $user->last_name);
              Log::error("In maintenance package -  in MaintenanceController- editMaintenanceDetail function " . $e->getMessage());

              DB::rollBack();

              return redirect()->back()->withError($e->getMessage());
            //   ->with([
            //       'code' => ActionStatusConstants::FAILURE,
            //       'message' => trans('maintenance::maintenance.maintenance_not_edited'),
            //   ]);

          }

      }

      public function ajaxGetMaintenanceDocuments(Request $request)
      {

          $user = Sentinel::getUser();

          Log::info("In maintenance package -  in MaintenanceController- ajaxGetMaintenanceDocuments function " . " try to get documents of maintenance   ------- by user " . $user->first_name . " " . $user->last_name);


          //get all documents of a maintenance job
          $maintenance_documents = MaintenanceJobDocument::where('id_maintenance_job', $request->maintenance_id)->where('maintenance_job_document_active' , 1)->get();


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

          Log::info("In maintenance package -  in MaintenanceController- ajaxGetMaintenanceTimeline function " . " try to get all logs of maintenance   ------- by user " . $user->first_name . " " . $user->last_name);

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


          Log::info("In maintenance package -  in MaintenanceController- ajaxDeleteMaintenanceDocument function " . " try to delete  maintenance document  ------- by user " . $user->first_name . " " . $user->last_name);


          try {
              DB::beginTransaction();

              $id_maintenance_document = $request->id_maintenance_job_document;

              $maintenance_id = $request->maintenance_id;

              $maintenance_job = MaintenanceJob::findOrFail($maintenance_id);

              $maintenance_job_document = MaintenanceJobDocument::findOrFail($id_maintenance_document);

              $file = $maintenance_job_document->document_address."/".$maintenance_job_document->document_name;
              unlink($file);


            //delete maintenance document
            $maintenance_job_document->update([
                'maintenance_job_document_active'=>0,
            ]);

            //   MaintenanceJobDocument::where('id_maintenance_job_document', $id_maintenance_document)->delete();



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
              Log::error("In maintenance package -  in MaintenanceController- ajaxDeleteMaintenanceDocument function " . $e->getMessage());

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
