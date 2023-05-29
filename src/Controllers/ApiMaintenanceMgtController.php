<?php

namespace Odisse\Maintenance\Controllers;

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
use Odisse\Maintenance\Models\Maintainable;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobDetail;
use Odisse\Maintenance\Models\MaintenanceJobDocument;
use Odisse\Maintenance\Models\MaintenanceJobPriorityHistory;
use Odisse\Maintenance\Models\MaintenanceJobSla;
use Odisse\Maintenance\Models\MaintenanceJobSlaRef;
use Odisse\Maintenance\Models\MaintenanceJobStatusHistory;
use Odisse\Maintenance\Models\MaintenanceLog;
use Illuminate\Support\Str;
use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;

class ApiMaintenanceMgtController extends Contractor{


    public function saveNewMaintenance( Request $request)
    {

        $user = User::find($request->user);;


        $validator = $this->validateMaintenance($request);

        if( null != $validator) {
            Log::info("AAAA");
            return response()->json(['message' => $validator->errors()], 422);
        }

        Log::info("BBB");
        $result = $this->createMaintenance($request);



        // if( $request->hasFile('files')){
        //     Log::info("has file");
        // }
        // else{
        //     Log::info("has no file");
        //     Log::info( print_r($request->all(), true));
        // }


        // foreach($request->all() as $key=>$data){
        //     Log::info($key);
        // }

        if( $result['status'] == 'success')

            return response()->json($result, 200);
        else{
            return response()->json($result, 400);

        }
    }


    private function validateMaintenance( Request $request)
    {

        $validator = Validator::make($request->all(), [
            'maintenance_title' => 'nullable',
            'description'=>'nullable',
            'maintenance_date'=>'nullable|date_format:' . SystemDateFormats::getDateTimeFormat(),
            'maintenance_category'=>'nullable',
            'saas_client_business'=>'nullable',
            'locations'=>'nullable',
            'priority'=>'nullable',


          ]);
        if ($validator->fails()) {

            Log::error("in maintenance validatior saveNewMaintenence function ". $validator->errors());


            // return redirect()->back()
            // ->withErrors($validator)
            // ->withInput();

            return $validator;
        }

    }

    private function createMaintenance( $request )
    {

        $user = User::find($request->user);;


        try {
            DB::beginTransaction();


            //save a new maintenance job
            $maintenance_job = new MaintenanceJob();
            $maintenance_job->id_saas_client_business =  $request->saas_client_business;
            $maintenance_job->id_parent_job = 1;
            $maintenance_job->id_saas_staff_reporter = $user->id;
            // $maintenance_job->job_report_date_time =  null == $request->maintenance_date ? Carbon::now() : $request->maintenance_date ;
            $maintenance_job->id_maintenance_job_category = $request->maintenance_category;
            $maintenance_job->id_maintenance_job_priority = $request->priority;
            $maintenance_job->id_maintenance_job_status = MaintenanceStatusConstants::OPNU;
            $maintenance_job->maintenance_job_title = $request->maintenance_title;
            $maintenance_job->maintenance_job_description = $request->description;
            $maintenance_job->id_resident_reporter = $request->resident_reporter;
            $maintenance_job->maintenance_job_active = 1;

            $maintenance_job->save();


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
            $maintenance_job_status_history->id_maintenance_job_status = MaintenanceStatusConstants::OPNU;
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
            if($locations) {
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
            }

            // session(['success' => 'value']);
            DB::commit();


            $status = 'success';
            $message = 'Maintenance created successfully';



        } catch (\Exception $e) {

            Log::error(" in MaintenanceController - saveNewMaintenence function : save a new maintenance  was not successful");
            Log::error($e->getMessage(). $e->getLine());

            DB::rollBack();


            $status = 'error';
            $message = trans('maintenance:maintenance.maintenance_not_created');

            // return redirect()->back()
            //     ->withErrors($message)
            //     ->withInput();

        }

        return [
            'status' => $status,
            'message' => $message
        ];

    }


    public function getDataToCreate( Request $request )
    {


            //get all maintenance category
            $maintenance_category = MaintenanceJobCategoryRef::all();

            //get all maintenance priorities
            $priorities = MaintenanceJobPriorityRef::all();

            $locations = $this->getMaintainables();;

            $jobs = MaintenanceJob::all();

            return response()->json(
                [
                    'jobs' => $jobs,
                    'maintenance_category' => $maintenance_category,
                    'priorities' => $priorities,
                    'locations' => $locations,
                ]
            );
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


          $sites = Site::all();


          foreach($sites as $site) {
              $site->id = 'Site'.$site->id_site;
              $site->name = '[Site] '.$site->site_full_name;

          }

          foreach($sites as $site) {
              $locations[] = $site;
          }

          return $locations;
    }
}


