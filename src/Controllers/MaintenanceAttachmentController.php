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

        // $request->validate([
        //     'attachments' => 'required',
        //     'attachments.*' => 'required|max:2048',
        // ]);

        $maintenance_job = MaintenanceJob::findOrFail($request->id_maintenance);


        $files = [];
        if ($request->file('attachments')){
            foreach($request->file('attachments') as $file)
            {
                try {
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
                    $maintenance_job_document->description = "";
                    $maintenance_job_document->maintenance_job_document_active = 1;


                    $maintenance_job_document->save();

                }
                catch(Exception $e){
                    dd($e->getMessage());
                }

            }
        }


        return redirect('/maintenance/detail/'.$request->id_maintenance)
                ->with('success','You have successfully upload file.');

    }
}