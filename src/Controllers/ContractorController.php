<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;
use App\SLP\Enum\ActionStatusConstants;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Odisse\Maintenance\Models\Contractor;
use Odisse\Maintenance\Models\ContractorAgent;
use Odisse\Maintenance\Models\ContractorLocation;
use Odisse\Maintenance\Models\ContractorLocationRef;
use Odisse\Maintenance\Models\ContractorSkill;
use Odisse\Maintenance\Models\ContractorSkillRef;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Sentinel;
use Spatie\LaravelRay\Commands\PublishConfigCommand;
use Validator;

class ContractorController extends Controller
{




    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showContractorPage(){



        $user = Sentinel::getUser();

        Log::info(" in ContractorController- showContractorPage function " . " try to go to create contractor page  ------- by user " . $user->first_name . " " . $user->last_name);



        return view('maintenance::create_contractor',
                    [



                    ]
                );

    }
  /////////////////////////////////////////////////////////////////////////////



/**
 * showEditContractorPage function
 *
 * @param Request $request
 * @param [type] $id_contractor
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
 */
    public function showEditContractorPage(Request $request , $id_contractor)
    {



        $user = Sentinel::getUser();

        Log::info(" in ContractorController- showContractorPage function " . " try to go to create contractor page  ------- by user " . $user->first_name . " " . $user->last_name);


        $contractor = Contractor::findOrfail($id_contractor);

        return view('maintenance::create_contractor',
                    [

                        'contractor' => $contractor,

                    ]
                );

    }


/////////////////////////////////////////////////////////////////////////////////

    public function showContractorManagementPage(){



        $user = Sentinel::getUser();

        Log::info(" in ContractorController- showContractorPage function " . " try to go to create contractor page  ------- by user " . $user->first_name . " " . $user->last_name);

        //$contractors = Contractor::where('contractor_active' , 1)->get();

        $skills = ContractorSkillRef::where('contractor_skill_ref_active' , 1)->get();
        $locations = ContractorLocationRef::where('contractor_location_ref_active' , 1)->get();



        return view('maintenance::contractor_mgt',
                    [
                        'skills' => $skills,
                        'locations' => $locations,
                    ]
                );

    }

  /////////////////////////////////////////////////////////////////////////////////////

    public function storeContractor(Request $request)
    {
        $user = Sentinel::getUser();

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

            Log::error("in Maintenance Package inside ContractorController- createNewRoom function"."create new room : ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try{
            DB::beginTransaction();

            $contractor = new Contractor([
                'id_saas_client_business'=>$user->id_saas_client_business,
                'name'=>$request->name,
                'short_name'=>$request->short_name,
                'vat_number'=>$request->vat_number,
                'tel_number1'=>$request->tel_number1,
                'tel_number2'=>$request->tel_number2,
                'address_line1'=>$request->address_line1,
                'address_line2'=>$request->address_line2,
                'address_line3'=>$request->address_line3,
                'contractor_active'=>1,
            ]);
            $contractor->save();


            $contractorAgent = [
                'login_name' => $request->email,
                'email' => $request->email,
                'password' => $request->password,
                'gender'  => 'M',
                'user_title'  => 'Mr',
            ];

            $contractor_user = Sentinel::registerAndActivate($contractorAgent);
            $contractor_user->update(['users_active' => '1']);
            $contractor_user->update(['id_saas_client_business' => $user->id_saas_client_business]);

            $contractor_user->save();



            $contractor_agent = new ContractorAgent([
                'id_contractor'=>$contractor->id_contractor,
                'id_user'=>$contractor_user->id,
                'contractor_agent_active'=>1,
            ]);

            $contractor_agent->save();



            DB::commit();
            return redirect()->route('contractor_management_page')->with(ActionStatusConstants::SUCCESS, trans('contractor.new_contractor_created'));


        }
        catch(\Exception $e){


            Log::error($e->getMessage());
            DB::rollback();
            return redirect()->back()->withInput()->with(ActionStatusConstants::ERROR, $e->getMessage());//->with(ActionStatusConstants::ERROR, trans('room_mgt.new_room_not_created'));


        }


    }



    public function updateContractor(Request $request , $id_contractor)
    {
        $user = Sentinel::getUser();

        $validator = Validator::make($request->all(), [

            'name' => 'required|string',
            'short_name' => 'required|string',
            'vat_number' => 'nullable|string',
            'tel_number1' => 'nullable|uk_phone',
            'tel_number2' => 'nullable|uk_phone',
            'address_line1' => 'nullable',
            'address_line2' => 'nullable',
            'address_line3' => 'nullable',

        ]);

        if ($validator->fails()) {

            Log::error("in Maintenance Package inside ContractorController- updateContractor function".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try{
            DB::beginTransaction();

            $contractor = Contractor::findOrfail($id_contractor);

            $contractor->update([
                'name'=>$request->name,
                'short_name'=>$request->short_name,
                'vat_number'=>$request->vat_number,
                'tel_number1'=>$request->tel_number1,
                'tel_number2'=>$request->tel_number2,
                'address_line1'=>$request->address_line1,
                'address_line2'=>$request->address_line2,
                'address_line3'=>$request->address_line3,
            ]);



            DB::commit();
            return redirect()->route('contractor_management_page')->with(ActionStatusConstants::SUCCESS, trans('contractor.contractor_updated'));


        }
        catch(\Exception $e){


            Log::error($e->getMessage());
            DB::rollback();
            return redirect()->back()->withInput()->with(ActionStatusConstants::ERROR, $e->getMessage());//->with(ActionStatusConstants::ERROR, trans('room_mgt.new_room_not_created'));


        }


    }


    public Function ajaxLoadContractors(){


        $user = Sentinel::getUser();

        Log::info(" in ContractorController- ajaxLoadContractors function " . " try to load contractors data  ------- by user " . $user->first_name . " " . $user->last_name);

        $contractors = Contractor::where('contractor_active' , 1)->where('id_saas_client_business' , $user->id_saas_client_business)->get();



        return response()->json(
            [
              'code' => ActionStatusConstants::SUCCESS,
              'contractors'=>$contractors,

              'message' => trans('maintenance::contractor.your_contractors_loaded'),
            ]);


    }



    public Function ajaxDeleteContractor(Request $request , $id_contractor){


        $user = Sentinel::getUser();

        Log::info(" in ContractorController- ajaxDeleteContractor function " . " try to delete specific contractor  ------- by user " . $user->first_name . " " . $user->last_name);

        $contractor = Contractor::find($id_contractor);
        $contractor->update([
            'contractor_active' =>0,
        ]);



        return response()->json(
            [
              'code' => ActionStatusConstants::SUCCESS,
              'message' => trans('maintenance::contractor.your_selected_contractor_deleted'),
            ]);


    }


    public function ajaxGetContractorEmail(Request $request , $id_contractor){

        $user_info = Contractor::where('contractor.id_contractor' , $id_contractor)->
        join('contractor_agent' , 'contractor_agent.id_contractor','contractor.id_contractor')->where('contractor_agent.contractor_agent_active' , 1)->
        join('users' , 'users.id' , 'contractor_agent.id_user' )->where('users.is_deleted' , 0)->where('users.users_active' , 1)->first();


        if($user_info){

            return response()->json(
                [
                'code' => ActionStatusConstants::SUCCESS,
                'message' => trans('maintenance::contractor.contractor_agent_info_returned'),
                'user_info' =>$user_info,
                ]);
        }
        else{

            return response()->json(
                [
                'code' => ActionStatusConstants::FAILURE,
                'message' => trans('maintenance::contractor.contractor_agent_info_was_not_returned'),
                ]);

        }



    }

    public function ajaxChangeContractorLoginSetting(Request $request)
    {

        $user = Sentinel::getUser();

        $validator = Validator::make($request->all(), [

            'contractor' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8',

        ]);

        if ($validator->fails()) {

            Log::error("in Maintenance Package inside ContractorController- ajaxChangeContractorLoginSetting function".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => $validator->errors(),
                ]);
        }

        try{

            DB::beginTransaction();
            $contractor_agents = ContractorAgent::where('id_contractor' , $request->contractor)->where('contractor_agent_active' ,1 )->get();

            if(count($contractor_agents)>0){
                //update password
                foreach($contractor_agents as $contractor_agent){
                    $agent_user = User::find($contractor_agent->id_user);
                    if($agent_user->email == $request->email){
                        //only update password

                        $sentinel_user = Sentinel::findById($contractor_agent->id_user);
                        if( $agent_user != null ) {
                            $hasher = Sentinel::getHasher();

                            $password = $request->password;
                            $passwordConf = $request->password_confirmation;

                            if ($password != $passwordConf) {

                                return response()->json(
                                    [
                                    'code' => ActionStatusConstants::FAILURE,
                                    'message' => trans('maintenance::contractor.password_and_confirmation_is_not_equal'),
                                    ]);
                            }


                            Sentinel::update($sentinel_user, array('password' => $password));

                            Log::info(" in ContractorController- ajaxDeleteContractor function "."Password changed for user ". $agent_user->email .  " (user id ". $user->id ." ) on " . date('Y-m-d H:i:s'));

                            return response()->json(
                                [
                                'code' => ActionStatusConstants::ERROR,
                                'message' => trans('maintenance::contractor.contractor_login_setting_edited'),
                                ]);
                        }
                        else{

                            Log::error("in ContractorController- ajaxChangeContractorLoginSetting function"."update user pass : "."by user:".$user->first_name . " " . $user->last_name);
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








            DB::commit();
        }
        catch(\Exception $e){


            Log::error($e->getMessage());
            DB::rollback();
            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => trans('maintenance::contractor.contractor_login_setting_not_changed'),
                ]);

        }




    }



    public function ajaxGetContractorSkills(Request $request , $id_contractor){

        $skills = Contractor::where('contractor.id_contractor' , $id_contractor)->
        join('contractor_skill' , 'contractor_skill.id_contractor','contractor.id_contractor')->where('contractor_skill.contractor_skill_active' , 1)->
        join('contractor_skill_ref' , 'contractor_skill.id_contractor_skill_ref' , 'contractor_skill_ref.id_contractor_skill_ref' )->where('contractor_skill_ref.contractor_skill_ref_active' , 1)->get();

        return response()->json(
            [
            'code' => ActionStatusConstants::SUCCESS,
            'message' => trans('maintenance::contractor.contractor_agent_info_returned'),
            'contractor_skills' =>$skills,
            ]);


    }




    public function ajaxChangeContractorSkills(Request $request)
    {

        $user = Sentinel::getUser();

        $validator = Validator::make($request->all(), [

            'contractor' => 'required|numeric',
            'skills' => 'nullable|array',

        ]);

        if ($validator->fails()) {

            Log::error("in Maintenance Package inside ContractorController- ajaxChangeContractorSkills function".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => $validator->errors(),
                ]);
        }

        try{

            DB::beginTransaction();
            $contractor_skills = ContractorSkill::where('id_contractor' , $request->contractor)->where('contractor_skill_active' ,1 )->get();

            if(count($contractor_skills)>0){
                //update password
                foreach($contractor_skills as $contractor_skill){
                    if(!in_array($contractor_skill->id_contractor_skill_ref , $request->skills)){
                        //we have to delete old skills are not in skills array
                        $contractor_skill->update([
                            'contractor_skill_active'=>0
                        ]);

                    }

                }

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


            Log::info(trans('maintenance::contractor.contractor_skills_updated'));

            DB::commit();

            return response()->json(
                [
                  'code' => ActionStatusConstants::SUCCESS,
                  'message' => trans('maintenance::contractor.contractor_skills_updated'),
                ]);

        }
        catch(\Exception $e){


            Log::error($e->getMessage());
            DB::rollback();
            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => trans('maintenance::contractor.contractor_skills_not_changed'),
                ]);

        }




    }



    public function ajaxGetContractorLocations(Request $request , $id_contractor){

        $locations = Contractor::where('contractor.id_contractor' , $id_contractor)->
        join('contractor_location' , 'contractor_location.id_contractor','contractor.id_contractor')->where('contractor_location.contractor_location_active' , 1)->
        join('contractor_location_ref' , 'contractor_location.id_contractor_location_ref' , 'contractor_location_ref.id_contractor_location_ref' )->where('contractor_location_ref.contractor_location_ref_active' , 1)->get();

        return response()->json(
            [
            'code' => ActionStatusConstants::SUCCESS,
            'message' => trans('maintenance::contractor.contractor_agent_info_returned'),
            'contractor_locations' =>$locations,
            ]);


    }




    public function ajaxChangeContractorLocations(Request $request)
    {

        $user = Sentinel::getUser();

        $validator = Validator::make($request->all(), [

            'contractor' => 'required|numeric',
            'locations' => 'nullable|array',

        ]);

        if ($validator->fails()) {

            Log::error("in Maintenance Package inside ContractorController- ajaxChangeContractorLocations function".": ". $validator->errors()." by user ".$user->first_name . " " . $user->last_name);

            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => $validator->errors(),
                ]);
        }

        try{

            DB::beginTransaction();
            $contractor_locations = ContractorLocation::where('id_contractor' , $request->contractor)->where('contractor_location_active' ,1 )->get();

            if(count($contractor_locations)>0){
                //update password
                foreach($contractor_locations as $contractor_location){
                    if(!in_array($contractor_location->id_contractor_location_ref , $request->locations)){
                        //we have to delete old skills are not in skills array
                        $contractor_location->update([
                            'contractor_location_active'=>0
                        ]);

                    }

                }

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


            Log::info(trans('maintenance::contractor.contractor_locations_updated'));

            DB::commit();

            return response()->json(
                [
                  'code' => ActionStatusConstants::SUCCESS,
                  'message' => trans('maintenance::contractor.contractor_locations_updated'),
                ]);

        }
        catch(\Exception $e){


            Log::error($e->getMessage());
            DB::rollback();
            return response()->json(
                [
                  'code' => ActionStatusConstants::FAILURE,
                  'message' => trans('maintenance::contractor.contractor_locations_not_changed'),
                ]);

        }




    }




}

