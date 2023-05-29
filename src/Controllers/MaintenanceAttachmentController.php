<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;


use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Odisse\Maintenance\Models\Contractor;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobDocument;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Carbon;
use Odisse\Maintenance\Models\MaintenanceLog;
use App\SLP\Formatter\SystemDateFormats;

use Sentinel;
use Validator;

class MaintenanceAttachmentController extends Controller
{
    public function downloadAttachment($id_attachment)
    {
        //get data of booking document
        $user = Sentinel::getUser();


        $maintenance_job_document = MaintenanceJobDocument::findOrFail($id_attachment);

        // dd($maintenance_job_document);
        // $bookingDocument = BookingDocument::findOrFail($id_booking_document);

        //get saas client business id of user who download the document
        // $id_saas_client_business_user_down = $user->id_saas_client_business;

        //get saas client  business id of user who uploaded the document
        // $staff = $maintenance_job_document->id_staff;
        // $user = User::findOrFail($staff);
        // $id_saas_client_business_user_upload = $user->id_saas_client_business;

        //TODO check if this booking document belongs to this saas client, if not return

        // if ($id_saas_client_business_user_down == $id_saas_client_business_user_upload) {
            //get file name + the extension
            $file_name = $maintenance_job_document->document_name;

            //calculate relative path for the file
            $file_path = $maintenance_job_document->document_address . $file_name;

            Log::info("in DocumentManagementController- downloadDocument function " . " try to download  documents of a booking:" . " ------- by user " . $user->first_name . " " . $user->last_name);


            //download file, use public path to calculate absolute path of the file
            return response()->download(public_path($file_path), $file_name);
        // } else {

        //     throw new \Exception( trans('booking.you_are_not_allowed_to_download_this_document'));
        // }



    }

    public function uploadAttachment(Request $request)
    {


        // dd($request->all());
        // $request->validate([
        //     'attachments' => 'required',
        //     'attachments.*' => 'required|max:2048',
        // ]);
        $user = Sentinel::getUser();


        $validator = Validator::make($request->all(), [
            'attachments'        => 'array|required',
            'attachments.*' => 'required|mimes:doc,docx,jpg,odt,jpeg,pdf,PNG,png,zip,rar|max:2048',
          ]);

          if ($validator->fails()) {

            Log::error("in MaintenanceAttachmentController- uploadAttachment function ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);


            return redirect('/maintenance/detail/'.$request->id_maintenance)
            ->with('error',trans('maintenance::maintenance.maintenance_file_is_empty'));
        }
      


        $maintenance_job = MaintenanceJob::findOrFail($request->id_maintenance);


        $all_attachments = "";

        $files = [];
        if ($request->file('attachments')){
            foreach($request->file('attachments') as $file)
            {
                try {

                    $now = Carbon::create('now');

                    $fileName = date('Y-m-d').'_'.$file->getClientOriginalName();

                    Log::info("store file ". $fileName);
                    // File extension
                    $extension = $file->getClientOriginalExtension();

                    //make a new directory for uploaded documents
                    $maintenance_file_path = config('maintenances.maintenance_file_path');
                    Log::info("store maintenance_file_path ". $maintenance_file_path);

                    $path = $maintenance_file_path . 'uploaded_files/' ;
                    if (!\File::exists($path)) {
                        \File::makeDirectory($path, 0755, true);
                    }


                    //save file in the directory
                    $file->move($path, $fileName);


                    $maintenance_job_document = new MaintenanceJobDocument();
                    $maintenance_job_document->id_maintenance_job =  $maintenance_job->id_maintenance_job;
                    $maintenance_job_document->document_name = $fileName;
                    $maintenance_job_document->document_address = $path;
                    $maintenance_job_document->document_extention = $extension;
                    $maintenance_job_document->description = $request->file_description;
                    $maintenance_job_document->maintenance_job_document_active = 1;


                    $maintenance_job_document->save();

                    $all_attachments = $all_attachments.$fileName." ";

                   
                    

                }
                catch(Exception $e){
                    dd($e->getMessage());
                }

            }

            $log_note = $user->first_name . " " . $user->last_name." upload a new maintenance document titled : ".$all_attachments ;

                    //add a log for uploading a new maintenance document
                    $maintenance_log = new MaintenanceLog();
                    $maintenance_log->id_maintenance_job =  $request->id_maintenance;
                    $maintenance_log->id_staff = $user->id;
                    $maintenance_log->log_date_time = $now->format(SystemDateFormats::getDateTimeFormat());
                    $maintenance_log->log_note = $log_note;

                    $maintenance_log->save();
        }

        return redirect('/maintenance/detail/'.$request->id_maintenance)
                ->with('success',trans('maintenance::maintenance.maintenance_file_uploaded_successfully'));

    }
}
