<?php
/**
 * Created by PhpStorm.
 * User: hedi
 * Date: 1/13/20
 * Time: 12:11 PM
 */

 namespace Odisse\Maintenance\App\Traits;

use App\Models\LegalCompany;
use App\Models\Property;
use App\Models\Resident;
use App\SLP\Enum\ActionStatusConstants;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;
use Odisse\Maintenance\Models\Contractor;
use App\Models\Room;
use App\Models\User;
use Odisse\Maintenance\Models\MaintenanceJob;
use Odisse\Maintenance\Models\MaintenanceJobCategoryRef;
use Odisse\Maintenance\Models\MaintenanceJobPriorityRef;
use Odisse\Maintenance\Models\MaintenanceJobStatusRef;
use App\SLP\Formatter\SystemDateFormats;
use Odisse\Maintenance\Models\Maintainable;
use Sentinel;
use JWTAuth;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\MaintenanceJobStaffHistory;

trait ReplaceTemplateBody{


    private function replaceMaintenanceTemplateVariables($template_body,$id_maintenance_job,$id_contractor,$commencement_date,$complete_date){


        try{


            $template_body = $template_body;


            // $id_maintenance_job = $request['id_maintenance_job'];

            // $id_contractor = $request['id_contractor'];

            //get the maintenance info
            $maintenance = MaintenanceJob::findOrFail($id_maintenance_job);


            //get the contractor info
            $contractor = Contractor::where('id_contractor',$id_contractor)->where('contractor_active',1)->first();





            if(str_contains($template_body,'%%DATE%%')){

                $now = Carbon::createFromDate('now');

                //replace the variable code with the accurate value of it in this booking
                $template_body = str_replace('%%DATE%%', $now->format(SystemDateFormats::getDateFormat()), $template_body);


            }


            if(str_contains($template_body,'%%MAINTENANCE_TITLE%%')){


                if($maintenance) {
                    $maintenance_title = $maintenance->maintenance_job_title;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_TITLE%%', $maintenance_title, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_TITLE%%', '', $template_body);

                }

            }





            if(str_contains($template_body,'%%MAINTENANCE_CATEGORY%%')){


                if($maintenance) {

                    $maintenance_category = MaintenanceJobCategoryRef::find($maintenance->id_maintenance_job_category);
                    $maintenance_category_name = $maintenance_category->job_category_name;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_CATEGORY%%', $maintenance_category_name, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_CATEGORY%%', '', $template_body);

                }

            }




            if(str_contains($template_body,'%%ORDER_NUMBER%%')){


                if($maintenance) {
                    $order_number = $maintenance->order_number;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%ORDER_NUMBER%%', $order_number, $template_body);
                }else{
                    $template_body = str_replace('%%ORDER_NUMBER%%', '', $template_body);

                }

            }


            if($maintenance){


                $maintenance_location = Maintainable::where('id_maintenance_job' , $maintenance->id_maintenance_job)->where('maintainable_active' , 1)->first();


                if($maintenance_location){


                    if($maintenance_location->maintenable_type == 'App\Models\Property'){

                        $property = Property::find($maintenance_location->maintenable_id);

                        $legal_company = LegalCompany::find($property->id_legal_company);
                        $company_logo ="<img style='width:90px;' src='".config('app.url', 'http://localhost')  . $legal_company->logo."'"."\><br>";

                        $maintenance_site = $property->address_line1 .' '.$property->city.'<br/>'.$property->county.' '.$property->postcode;

                        $center_maintenance_site = $property->address_line1 .' '.$property->city.' '.$property->county.' '.$property->postcode;


                        if(str_contains($template_body,'%%MAINTENANCE_SITE%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%MAINTENANCE_SITE%%', $maintenance_site, $template_body);


                        }

                        if(str_contains($template_body,'%%CENTER_MAINTENANCE_SITE%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%CENTER_MAINTENANCE_SITE%%', $center_maintenance_site, $template_body);


                        }

                        if(str_contains($template_body,'%%MAINTENANCE_LOCATION%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%MAINTENANCE_LOCATION%%', $property->property_name, $template_body);


                        }


                        if(str_contains($template_body,'%%LEGAL_COMPANY_LOGO%%')){


                            $template_body = str_replace('%%LEGAL_COMPANY_LOGO%%', $company_logo, $template_body);

                        }




                        if(str_contains($template_body,'%%LEGAL_COMPANY_NAME%%')){

                            $legal_company_name = $legal_company->name;


                            $template_body = str_replace('%%LEGAL_COMPANY_NAME%%', $legal_company_name, $template_body);

                        }





                    }
                    if($maintenance_location->maintenable_type == 'App\Models\Rooms'){


                        $room = Room::find($maintenance_location->maintenable_id);


                        $property = Property::find($room->id_property);

                        $maintenance_site = $property->address_line1 .'<br/>'.$property->city.'<br/>'.$property->county.'<br/>'.$property->postcode;

                        $center_maintenance_site = $property->address_line1 .' '.$property->city.' '.$property->county.' '.$property->postcode;

                        if(str_contains($template_body,'%%MAINTENANCE_SITE%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%MAINTENANCE_SITE%%', $maintenance_site, $template_body);


                        }
                        if(str_contains($template_body,'%%CENTER_MAINTENANCE_SITE%%')){



                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%CENTER_MAINTENANCE_SITE%%', $center_maintenance_site, $template_body);


                        }


                        if($property->id_legal_company){

                            $legal_company = LegalCompany::find($property->id_legal_company);


                            $company_logo = "<br />";
                            if( $legal_company)
                                $company_logo ="<img style='width:90px;' src='".config('app.url', 'http://localhost') . $legal_company->logo."'"."\><br>";


                                if(str_contains($template_body,'%%LEGAL_COMPANY_LOGO%%')){


                                    $template_body = str_replace('%%LEGAL_COMPANY_LOGO%%', $company_logo, $template_body);

                            }
                        }else{
                            if(str_contains($template_body,'%%LEGAL_COMPANY_LOGO%%')){


                                $template_body = str_replace('%%LEGAL_COMPANY_LOGO%%', '', $template_body);

                        }

                        }

                        if(str_contains($template_body,'%%MAINTENANCE_LOCATION%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%MAINTENANCE_LOCATION%%', $property->property_name, $template_body);


                        }
                        if(str_contains($template_body,'%%LEGAL_COMPANY_NAME%%')){

                            $legal_company_name = $legal_company->name;


                            $template_body = str_replace('%%LEGAL_COMPANY_NAME%%', $legal_company_name, $template_body);

                        }


                    }
                }
            }






            if(str_contains($template_body,'%%MAINTENANCE_PRIORITY%%')){


                if($maintenance) {
                    $maintenance_priority = MaintenanceJobPriorityRef::find($maintenance->id_maintenance_job_priority);
                    $maintenance_priority_name = $maintenance_priority->priority_name;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_PRIORITY%%', $maintenance_priority_name, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_PRIORITY%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%MAINTENANCE_STATUS%%')){


                if($maintenance) {
                    $maintenance_status = MaintenanceJobStatusRef::find($maintenance->id_maintenance_job_status);
                    $job_status_name = $maintenance_status->job_status_name;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_STATUS%%', $job_status_name, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_STATUS%%', '', $template_body);

                }

            }




            if(str_contains($template_body,'%%RESIDENT_REPORTER%%')){


                if($maintenance) {
                    $resident_reporter = Resident::find($maintenance->id_resident_reporter);
                    if($resident_reporter){
                        $resident_name = $resident_reporter->resident_first_name . ' '. $resident_reporter->resident_surname;

                    }
                    else{
                        $resident_name = '-';

                    }

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%RESIDENT_REPORTER%%', $resident_name, $template_body);
                }else{
                    $template_body = str_replace('%%RESIDENT_REPORTER%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%JOB_START_DATE_TIME%%')){


                if($maintenance) {
                    $job_start_date_time =  $maintenance->job_start_date_time;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%JOB_START_DATE_TIME%%', $job_start_date_time, $template_body);
                }else{
                    $template_body = str_replace('%%JOB_START_DATE_TIME%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%COMMENCEMENT_DATE%%')){


                if($maintenance) {
                    // $commencement_date =  $maintenance->commencement_date;
                    if($commencement_date == null){

                        $now = \Illuminate\Support\Carbon::create('now');

                        $commencement_date = $now->format(SystemDateFormats::getDateFormat());

                    }

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%COMMENCEMENT_DATE%%', $commencement_date, $template_body);
                }else{
                    $template_body = str_replace('%%COMMENCEMENT_DATE%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%COMPLETE_DATE%%')){


                if($maintenance) {
                    // $complete_date =  $maintenance->complete_date;

                    if($complete_date == null){

                        $user = Sentinel::getUser();


                        $remain_time = $this->calculateSlaRemainTime($user->id_saas_client_business,$maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);

                        $complete_date = Carbon::parse($remain_time)->format(SystemDateFormats::getDateFormat());;

                    }
                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%COMPLETE_DATE%%', $complete_date, $template_body);
                }else{
                    $template_body = str_replace('%%COMPLETE_DATE%%', '', $template_body);

                }

            }

            // return $template_body;

            if(str_contains($template_body,'%%MAINTENANCE_DETAIL%%')){


                if($maintenance) {
                    $detail =  $maintenance->maintenance_job_description;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_DETAIL%%', $detail, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_DETAIL%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%JOB_FINISH_DATE_TIME%%')){


                if($maintenance) {
                    $job_finish_date_time =  $maintenance->job_finish_date_time;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%JOB_FINISH_DATE_TIME%%', $job_finish_date_time, $template_body);
                }else{
                    $template_body = str_replace('%%JOB_FINISH_DATE_TIME%%', '', $template_body);

                }

            }






            if(str_contains($template_body,'%%CONTRACTOR_NAME%%')){


                if($contractor) {
                    $contractor_name =  $contractor->name;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_NAME%%', $contractor_name, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_NAME%%', '', $template_body);

                }

            }




            if(str_contains($template_body,'%%CONTRACTOR_SHORT_NAME%%')){


                if($contractor) {
                    $contractor_short_name =  $contractor->short_name;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_SHORT_NAME%%', $contractor_short_name, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_SHORT_NAME%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%CONTRACTOR_VAT_NUMBER%%')){


                if($contractor) {
                    $contractor_vat_number =  $contractor->vat_number;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_VAT_NUMBER%%', $contractor_vat_number, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_VAT_NUMBER%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%CONTRACTOR_TEL_NUMBER1%%')){


                if($contractor) {
                    $contractor_tel_number1 =  $contractor->tel_number1;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_TEL_NUMBER1%%', $contractor_tel_number1, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_TEL_NUMBER1%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%CONTRACTOR_TEL_NUMBER2%%')){


                if($contractor) {
                    $contractor_tel_number2 =  $contractor->tel_number2;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_TEL_NUMBER2%%', $contractor_tel_number2, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_TEL_NUMBER2%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%CONTRACTOR_ADDRESS%%')){


                if($contractor) {
                    $contractor_address =  $contractor->address_line1 .' ' .$contractor->address_line2 .' '. $contractor->address_line3 ;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_ADDRESS%%', $contractor_address, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_ADDRESS%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%CONTRACTOR_NOTE%%')){


                if($contractor) {
                    $contractor_note =  $contractor->note  ;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_NOTE%%', $contractor_note, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_NOTE%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%CONTRACTOR_EMAIL%%')){


                if($contractor) {

                    $contractor_agent = ContractorAgent::where('id_contractor' , $contractor->id_contractor)->where('contractor_agent_active' , 1)->first();
                    $contractor_user = User::where('id' , $contractor_agent->id_user)->first();
                    $contractor_email =  $contractor_user->email  ;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_EMAIL%%', $contractor_email, $template_body);
                }
                elseif($maintenance){
                    $maintenance_staff = MaintenanceJobStaffHistory::where('id_maintenance_job' , $maintenance->id_maintenance)->where('maintenance_job_staff_history_active' , 1);
                    $maintenance_staff = $maintenance_staff->where(function ($query)  {
                        $query->where('is_last_one' , 1)
                              ->orWhereNull('is_last_one');
                    });
                    $maintenance_staff = $maintenance_staff->first();
                    $staff = User::where('id_user', $maintenance_staff->id_maintenance_staff)->first();
                    $staff_email = $staff->email;

                    $template_body = str_replace('%%CONTRACTOR_EMAIL%%', $staff_email, $template_body);


                }
                else{

                    $template_body = str_replace('%%CONTRACTOR_EMAIL%%', '', $template_body);

                }

            }






            return $template_body;



        } catch (\Exception $e) {
            Log::error("in ReplaceTemplateBody trait- replaceMaintenanceTemplateVariables function get template body  " . " by user "
               . $e->getMessage());
            Log::info($e->getLine());
            return null;
        }

    }
    private function replaceMaintenanceTemplateVariablesforApp($template_body,$id_maintenance_job,$id_contractor,$commencement_date,$complete_date){


        try{


            $template_body = $template_body;


            // $id_maintenance_job = $request['id_maintenance_job'];

            // $id_contractor = $request['id_contractor'];

            //get the maintenance info
            $maintenance = MaintenanceJob::findOrFail($id_maintenance_job);


            //get the contractor info
            $contractor = Contractor::where('id_contractor',$id_contractor)->where('contractor_active',1)->first();





            if(str_contains($template_body,'%%DATE%%')){

                $now = Carbon::createFromDate('now');

                //replace the variable code with the accurate value of it in this booking
                $template_body = str_replace('%%DATE%%', $now->format(SystemDateFormats::getDateFormat()), $template_body);


            }


            if(str_contains($template_body,'%%MAINTENANCE_TITLE%%')){


                if($maintenance) {
                    $maintenance_title = $maintenance->maintenance_job_title;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_TITLE%%', $maintenance_title, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_TITLE%%', '', $template_body);

                }

            }





            if(str_contains($template_body,'%%MAINTENANCE_CATEGORY%%')){


                if($maintenance) {

                    $maintenance_category = MaintenanceJobCategoryRef::find($maintenance->id_maintenance_job_category);
                    $maintenance_category_name = $maintenance_category->job_category_name;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_CATEGORY%%', $maintenance_category_name, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_CATEGORY%%', '', $template_body);

                }

            }




            if(str_contains($template_body,'%%ORDER_NUMBER%%')){


                if($maintenance) {
                    $order_number = $maintenance->order_number;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%ORDER_NUMBER%%', $order_number, $template_body);
                }else{
                    $template_body = str_replace('%%ORDER_NUMBER%%', '', $template_body);

                }

            }


            if($maintenance){


                $maintenance_location = Maintainable::where('id_maintenance_job' , $maintenance->id_maintenance_job)->where('maintainable_active' , 1)->first();


                if($maintenance_location){


                    if($maintenance_location->maintenable_type == 'App\Models\Property'){

                        $property = Property::find($maintenance_location->maintenable_id);

                        $legal_company = LegalCompany::find($property->id_legal_company);
                        $company_logo ="<img style='width:90px;' src='".config('app.url', 'http://localhost')  . $legal_company->logo."'"."\><br>";

                        $maintenance_site = $property->address_line1 .' '.$property->city.'<br/>'.$property->county.' '.$property->postcode;

                        $center_maintenance_site = $property->address_line1 .' '.$property->city.' '.$property->county.' '.$property->postcode;


                        if(str_contains($template_body,'%%MAINTENANCE_SITE%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%MAINTENANCE_SITE%%', $maintenance_site, $template_body);


                        }

                        if(str_contains($template_body,'%%CENTER_MAINTENANCE_SITE%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%CENTER_MAINTENANCE_SITE%%', $center_maintenance_site, $template_body);


                        }

                        if(str_contains($template_body,'%%MAINTENANCE_LOCATION%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%MAINTENANCE_LOCATION%%', $property->property_name, $template_body);


                        }


                        if(str_contains($template_body,'%%LEGAL_COMPANY_LOGO%%')){


                            $template_body = str_replace('%%LEGAL_COMPANY_LOGO%%', $company_logo, $template_body);

                        }




                        if(str_contains($template_body,'%%LEGAL_COMPANY_NAME%%')){

                            $legal_company_name = $legal_company->name;


                            $template_body = str_replace('%%LEGAL_COMPANY_NAME%%', $legal_company_name, $template_body);

                        }





                    }
                    if($maintenance_location->maintenable_type == 'App\Models\Rooms'){


                        $room = Room::find($maintenance_location->maintenable_id);


                        $property = Property::find($room->id_property);

                        $maintenance_site = $property->address_line1 .'<br/>'.$property->city.'<br/>'.$property->county.'<br/>'.$property->postcode;

                        $center_maintenance_site = $property->address_line1 .' '.$property->city.' '.$property->county.' '.$property->postcode;

                        if(str_contains($template_body,'%%MAINTENANCE_SITE%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%MAINTENANCE_SITE%%', $maintenance_site, $template_body);


                        }
                        if(str_contains($template_body,'%%CENTER_MAINTENANCE_SITE%%')){



                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%CENTER_MAINTENANCE_SITE%%', $center_maintenance_site, $template_body);


                        }


                        if($property->id_legal_company){

                            $legal_company = LegalCompany::find($property->id_legal_company);


                            $company_logo = "<br />";
                            if( $legal_company)
                                $company_logo ="<img style='width:90px;' src='".config('app.url', 'http://localhost') . $legal_company->logo."'"."\><br>";


                                if(str_contains($template_body,'%%LEGAL_COMPANY_LOGO%%')){


                                    $template_body = str_replace('%%LEGAL_COMPANY_LOGO%%', $company_logo, $template_body);

                            }
                        }else{
                            if(str_contains($template_body,'%%LEGAL_COMPANY_LOGO%%')){


                                $template_body = str_replace('%%LEGAL_COMPANY_LOGO%%', '', $template_body);

                        }

                        }

                        if(str_contains($template_body,'%%MAINTENANCE_LOCATION%%')){


                            //replace the variable code with the accurate value of it in this maintenance
                            $template_body = str_replace('%%MAINTENANCE_LOCATION%%', $property->property_name, $template_body);


                        }
                        if(str_contains($template_body,'%%LEGAL_COMPANY_NAME%%')){

                            $legal_company_name = $legal_company->name;


                            $template_body = str_replace('%%LEGAL_COMPANY_NAME%%', $legal_company_name, $template_body);

                        }


                    }
                }
            }






            if(str_contains($template_body,'%%MAINTENANCE_PRIORITY%%')){


                if($maintenance) {
                    $maintenance_priority = MaintenanceJobPriorityRef::find($maintenance->id_maintenance_job_priority);
                    $maintenance_priority_name = $maintenance_priority->priority_name;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_PRIORITY%%', $maintenance_priority_name, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_PRIORITY%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%MAINTENANCE_STATUS%%')){


                if($maintenance) {
                    $maintenance_status = MaintenanceJobStatusRef::find($maintenance->id_maintenance_job_status);
                    $job_status_name = $maintenance_status->job_status_name;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_STATUS%%', $job_status_name, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_STATUS%%', '', $template_body);

                }

            }




            if(str_contains($template_body,'%%RESIDENT_REPORTER%%')){


                if($maintenance) {
                    $resident_reporter = Resident::find($maintenance->id_resident_reporter);
                    if($resident_reporter){
                        $resident_name = $resident_reporter->resident_first_name . ' '. $resident_reporter->resident_surname;

                    }
                    else{
                        $resident_name = '-';

                    }

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%RESIDENT_REPORTER%%', $resident_name, $template_body);
                }else{
                    $template_body = str_replace('%%RESIDENT_REPORTER%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%JOB_START_DATE_TIME%%')){


                if($maintenance) {
                    $job_start_date_time =  $maintenance->job_start_date_time;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%JOB_START_DATE_TIME%%', $job_start_date_time, $template_body);
                }else{
                    $template_body = str_replace('%%JOB_START_DATE_TIME%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%COMMENCEMENT_DATE%%')){


                if($maintenance) {
                    // $commencement_date =  $maintenance->commencement_date;
                    if($commencement_date == null){

                        $now = \Illuminate\Support\Carbon::create('now');

                        $commencement_date = $now->format(SystemDateFormats::getDateFormat());

                    }

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%COMMENCEMENT_DATE%%', $commencement_date, $template_body);
                }else{
                    $template_body = str_replace('%%COMMENCEMENT_DATE%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%COMPLETE_DATE%%')){


                if($maintenance) {
                    // $complete_date =  $maintenance->complete_date;

                    if($complete_date == null){

                        $user = JWTAuth::user();


                        $remain_time = $this->calculateSlaRemainTime($user->id_saas_client_business,$maintenance->id_maintenance_job , $maintenance->job_report_date_time , $maintenance->expected_target_minutes);

                        $complete_date = Carbon::parse($remain_time)->format(SystemDateFormats::getDateFormat());;

                    }
                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%COMPLETE_DATE%%', $complete_date, $template_body);
                }else{
                    $template_body = str_replace('%%COMPLETE_DATE%%', '', $template_body);

                }

            }

            // return $template_body;

            if(str_contains($template_body,'%%MAINTENANCE_DETAIL%%')){


                if($maintenance) {
                    $detail =  $maintenance->maintenance_job_description;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%MAINTENANCE_DETAIL%%', $detail, $template_body);
                }else{
                    $template_body = str_replace('%%MAINTENANCE_DETAIL%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%JOB_FINISH_DATE_TIME%%')){


                if($maintenance) {
                    $job_finish_date_time =  $maintenance->job_finish_date_time;

                    //replace the variable code with the accurate value of it in this booking
                    $template_body = str_replace('%%JOB_FINISH_DATE_TIME%%', $job_finish_date_time, $template_body);
                }else{
                    $template_body = str_replace('%%JOB_FINISH_DATE_TIME%%', '', $template_body);

                }

            }






            if(str_contains($template_body,'%%CONTRACTOR_NAME%%')){


                if($contractor) {
                    $contractor_name =  $contractor->name;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_NAME%%', $contractor_name, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_NAME%%', '', $template_body);

                }

            }




            if(str_contains($template_body,'%%CONTRACTOR_SHORT_NAME%%')){


                if($contractor) {
                    $contractor_short_name =  $contractor->short_name;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_SHORT_NAME%%', $contractor_short_name, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_SHORT_NAME%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%CONTRACTOR_VAT_NUMBER%%')){


                if($contractor) {
                    $contractor_vat_number =  $contractor->vat_number;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_VAT_NUMBER%%', $contractor_vat_number, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_VAT_NUMBER%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%CONTRACTOR_TEL_NUMBER1%%')){


                if($contractor) {
                    $contractor_tel_number1 =  $contractor->tel_number1;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_TEL_NUMBER1%%', $contractor_tel_number1, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_TEL_NUMBER1%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%CONTRACTOR_TEL_NUMBER2%%')){


                if($contractor) {
                    $contractor_tel_number2 =  $contractor->tel_number2;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_TEL_NUMBER2%%', $contractor_tel_number2, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_TEL_NUMBER2%%', '', $template_body);

                }

            }



            if(str_contains($template_body,'%%CONTRACTOR_ADDRESS%%')){


                if($contractor) {
                    $contractor_address =  $contractor->address_line1 .' ' .$contractor->address_line2 .' '. $contractor->address_line3 ;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_ADDRESS%%', $contractor_address, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_ADDRESS%%', '', $template_body);

                }

            }


            if(str_contains($template_body,'%%CONTRACTOR_NOTE%%')){


                if($contractor) {
                    $contractor_note =  $contractor->note  ;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_NOTE%%', $contractor_note, $template_body);
                }else{
                    $template_body = str_replace('%%CONTRACTOR_NOTE%%', '', $template_body);

                }

            }




            if(str_contains($template_body,'%%CONTRACTOR_EMAIL%%')){


                if($contractor) {

                    $contractor_agent = ContractorAgent::where('id_contractor' , $contractor->id_contractor)->where('contractor_agent_active' , 1)->first();
                    $contractor_user = User::where('id' , $contractor_agent->id_user)->first();
                    $contractor_email =  $contractor_user->email  ;

                    //replace the variable code with the accurate value of it in this contractor
                    $template_body = str_replace('%%CONTRACTOR_EMAIL%%', $contractor_email, $template_body);
                }
                elseif($maintenance){
                    $maintenance_staff = MaintenanceJobStaffHistory::where('id_maintenance_job' , $maintenance->id_maintenance)->where('maintenance_job_staff_history_active' , 1);
                    $maintenance_staff = $maintenance_staff->where(function ($query)  {
                        $query->where('is_last_one' , 1)
                              ->orWhereNull('is_last_one');
                    });
                    $maintenance_staff = $maintenance_staff->first();
                    $staff = User::where('id_user', $maintenance_staff->id_maintenance_staff)->first();
                    $staff_email = $staff->email;

                    $template_body = str_replace('%%CONTRACTOR_EMAIL%%', $staff_email, $template_body);


                }
                else{

                    $template_body = str_replace('%%CONTRACTOR_EMAIL%%', '', $template_body);

                }

            }






            return $template_body;



        } catch (\Exception $e) {
            Log::error("in ReplaceTemplateBody trait- replaceMaintenanceTemplateVariables function get template body  " . " by user "
               . $e->getMessage());
            Log::info($e->getLine());
            return null;
        }

    }


}
