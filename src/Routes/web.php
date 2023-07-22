<?php

use Illuminate\Support\Facades\Route;
use Odisse\Maintenance\Controllers\ApiMaintenanceMgtController;
use Odisse\Maintenance\Controllers\MaintenanceController;
use Odisse\Maintenance\Controllers\MaintenanceDashboardController;
use Odisse\Maintenance\Controllers\MaintenanceManagementController;
use Odisse\Maintenance\Controllers\ContractorController;
use Odisse\Maintenance\Controllers\MaintenanceAttachmentController;
use Odisse\Maintenance\Controllers\ContractorManagementController;
use Odisse\Maintenance\Controllers\ApiContractorMgtController;



Route::group(['prefix' => 'maintenance'],function () {

    Route::any('/testItem', [MTestController::class,'testFunc']);
    Route::any('/demo', function(){
        echo "OK";
    });
});

Route::middleware(['web','ProxyCAS'])->group(
    function () {


        Route::get('/maintenance/files/{filename}', function($filename){



            $path = storage_path('../../systemfiles/maintenance_files/uploaded_files/' . $filename);



            if (!file_exists($path)) {
                abort(404);
            }

            $file = file_get_contents($path);

            return response($file, 200)->header('Content-Type', mime_content_type($path));

        });


        Route::get('/contractor/files/{filename}', function($filename){

            $path = config('database.backup_path').'/'.str_replace(" ", "",$saas_client->id_saas_client_business.$saas_client->business_name);


            $path = storage_path('../../systemfiles/contractor_files/uploaded_files/' . $filename);

            $contractor_file_path = config('maintenances.contractor_file_path');


            if (!file_exists($path)) {
                abort(404);
            }

            $file = file_get_contents($path);

            return response($file, 200)->header('Content-Type', mime_content_type($path));

        });


        Route::group(['prefix' => 'maintenance', 'middleware' => ['AuthenticatedUsersMiddleware']], function(){

           Route::get('/management', [MaintenanceManagementController::class,'showManagementPage'])->name('maintenance_management');
           Route::post('/mgt_maintenances_list', [MaintenanceManagementController::class,'ajaxLoadMaintenances'])->name('load_mgt_maintenances');
           Route::post('/mgt/delete/{id_maintenance}', [MaintenanceManagementController::class,'ajaxMgtDeleteMaintenance'])->name('delete_maintenance');
           Route::post('/mgt/start/{id_maintenance}', [MaintenanceManagementController::class,'ajaxMgtStartMaintenance'])->name('start_maintenance');
           Route::post('/mgt/end/{id_maintenance}', [MaintenanceManagementController::class,'ajaxMgtEndMaintenance'])->name('end_maintenance');
           Route::post('/mgt/business_contractors', [MaintenanceManagementController::class,'ajaxLoadBusinessContractors']);
           Route::post('/mgt/business_contractor/user_agents', [MaintenanceManagementController::class,'ajaxLoadMgtUserAgents']);
           Route::post('/mgt/assign_user', [MaintenanceManagementController::class,'ajaxMgtAssignMaintenanceToUser']);
           Route::post('/mgt/statuses/charts', [MaintenanceManagementController::class,'ajaxGetStatusChartData']);
           Route::post('/mgt/sla/charts', [MaintenanceManagementController::class,'ajaxGetSlaChartData']);
           Route::get('/mgt/create', [MaintenanceManagementController::class,'showCreateMaintenancePage']);
           Route::post('/mgt/new/save', [MaintenanceManagementController::class,'createMaintenance']);
           Route::post('/mgt/resident_reporter', [MaintenanceController::class,'getLocationResidents']);
           Route::post('/mgt/contractor_skill/contractors', [MaintenanceManagementController::class,'ajaxGetContractorsWithSkill']);

        });



        Route::group(['prefix' => 'maintenance', 'middleware' => ['AuthenticatedUsersMiddleware', 'settingsLoader']],function () {

            // Route::any('/testItem', [MTestController::class,'testFunc']);
            //create maintenance
            Route::any('/testItem', [MaintenanceController::class,'testFunc']);

            Route::get('/create/page', [MaintenanceController::class,'createNewMaintenancePage'])->name('create_maintenance_page');
            Route::get('/contractor', [ContractorController::class,'showContractorPage']);
            Route::post('/contractor', [ContractorController::class,'storeContractor']);
            Route::get('/contractor/{id_contractor}', [ContractorController::class,'showEditContractorPage']);
            Route::post('/contractor/{id_contractor}', [ContractorController::class,'updateContractor']);
            Route::get('/contractors', [ContractorController::class,'showContractorManagementPage'])->name('contractor_management_page');
            Route::post('/contractors', [ContractorController::class,'ajaxLoadContractors'])->name('load_contractors');
            Route::post('/contractor/delete/{id_contractor}', [ContractorController::class,'ajaxDeleteContractor'])->name('delete_contractor');
            Route::post('/delete/{id_maintenance}', [MaintenanceDashboardController::class,'ajaxDeleteMaintenance'])->name('delete_maintenance');
            Route::get('/dashboard', [MaintenanceDashboardController::class,'showDashboardPage'])->name('maintenance_dashboard');
            Route::post('/maintenances_list', [MaintenanceDashboardController::class,'ajaxLoadMaintenances'])->name('load_maintenances');
            Route::post('/business_contractor/user_agents', [MaintenanceDashboardController::class,'ajaxLoadUserAgents'])->name('load_users_agents');
            Route::post('/assign_user', [MaintenanceDashboardController::class,'ajaxAssignMaintenanceToUser'])->name('assign_maintenance_to_user');
            Route::post('/contractor/login_settings/change', [ContractorController::class,'ajaxChangeContractorLoginSetting']);
            Route::post('/contractor/email/{id_contractor}', [ContractorController::class,'ajaxGetContractorEmail']);

            Route::post('/upload/file', [MaintenanceController::class,'ajaxUploadMaintenanceFile'])->name('file_upload');

            Route::get('/attachment/{id_attachment}/download', [MaintenanceAttachmentController::class,'downloadAttachment']);
            Route::post('/attachment/upload', [MaintenanceAttachmentController::class,'uploadAttachment']);

           Route::post('/find/maintenance_title', [MaintenanceController::class,'ajaxFindMaintenanceTitle'])->name('find_maintenance_title');

           Route::post('/new/save', [MaintenanceController::class,'saveNewMaintenence'])->name('save_new_maintenance');

           Route::post('/get/resident_reporter', [MaintenanceController::class,'ajaxGetResidentReporter'])->name('resident_list_of_location');

           //maintenance detail
           Route::get('/detail/{maintenanceId}', [MaintenanceController::class,'showMaintenanceDetailPage'])->name('maintenance_detail');

           Route::post('/detail/edit', [MaintenanceController::class,'editMaintenanceDetail'])->name('update_maintenance');

           Route::post('/timeline/get', [MaintenanceController::class,'ajaxGetMaintenanceTimeline'])->name('get_maintenance_timeline');

           Route::post('/documents/get', [MaintenanceController::class,'ajaxGetMaintenanceDocuments'])->name('get_maintenance_document');

           Route::post('/maintenance_document/delete', [MaintenanceController::class,'ajaxDeleteMaintenanceDocument'])->name('delete_maintenance_document');


           //contractor skills
           Route::post('/contractor/skill/{id_contractor}', [ContractorController::class,'ajaxGetContractorSkills']);
           Route::post('/contractor/skills/change', [ContractorController::class,'ajaxChangeContractorSkills']);


           //contractor locations
           Route::post('/contractor/location/{id_contractor}', [ContractorController::class,'ajaxGetContractorLocations']);
           Route::post('/contractor/locations/change', [ContractorController::class,'ajaxChangeContractorLocations']);

           //start & end maintenance job
           Route::post('/start/{id_maintenance}', [MaintenanceDashboardController::class,'ajaxStartMaintenance']);
           Route::post('/end/{id_maintenance}', [MaintenanceDashboardController::class,'ajaxEndMaintenance']);


           //dashboard widgets
           Route::post('/statuses/charts', [MaintenanceDashboardController::class,'ajaxPrepareStatusChartData']);
           Route::post('/sla/charts', [MaintenanceDashboardController::class,'ajaxPrepareSlaChartData']);

           //load contractors for assignment
           Route::post('/contractors_for_assignment', [ContractorController::class,'ajaxGetContractors']);

           //load active tasks of contractor
           Route::post('/contractor/tasks/{id_contractor}', [ContractorController::class,'ajaxGetContractorTasks']);

           //get contractor based skill
           Route::post('/contractor_skill/contractors' , [ContractorController::class,'ajaxGetContractorsWithSkill']);


            //get  attachments of contractor
            Route::post('/contractor/attachments/{id_contractor}', [ContractorController::class,'ajaxGetContractorAttachments']);

           //delete  attachment of a contractor
           Route::post('/contractor_document/delete', [ContractorController::class,'deleteContractorAttachment']);

           Route::get('/contractor_attachment/{id_attachment}/download', [ContractorController::class,'downloadAttachment']);

           Route::post('/contractor_file/upload', [ContractorController::class,'uploadAttachment']);

           //send email to contractor
           Route::post('/contractor/job_document/{id_maintenance_job}', [MaintenanceDashboardController::class,'getContractorJobDocuments']);

           Route::get('/create/email_temp/{id_maintenance_job}', [MaintenanceDashboardController::class,'createEmailTemplateForContractor'])->name('emailTemplateCreation');

           Route::post('/contractor/send/email', [MaintenanceDashboardController::class,'sendEmailToContractor']);

           //email preview
           Route::post('/contractor_email/preview', [MaintenanceDashboardController::class,'previewEmailContent']);


        });



          //contractor management in portal

          Route::group(['prefix' => 'maintenance', 'middleware' => ['AuthenticatedUsersMiddleware']], function(){

            Route::get('/contractor_management', [ContractorManagementController::class,'showContractorPage'])->name('contractor_management');
            Route::post('/mgt_contractors_list', [ContractorManagementController::class,'ajaxLoadContractors'])->name('load_mgt_contractors');
            Route::get('/new_contractor', [ContractorManagementController::class,'showPageForContractorCreation']);
            Route::post('/mgt_contractor/portal/store', [ContractorManagementController::class,'storeContractor']);
            Route::post('/contractor/mgt/delete/{id_contractor}', [ContractorManagementController::class,'ajaxMgtDeleteContractor'])->name('mgt_delete_contractor');
            Route::post('/mgt_contractor/attachments/{id_contractor}', [ContractorManagementController::class,'ajaxMgtGetContractorAttachments'])->name('mgt_get_contractor_docs');
            Route::post('/mgt_contractor/tasks/{id_contractor}', [ContractorManagementController::class,'ajaxMgtGetContractorTasks'])->name('mgt_get_contractor_task');

            Route::post('/mgt_contractor/email/{id_contractor}', [ContractorManagementController::class,'ajaxMgtGetContractorEmailInfo'])->name('mgt_get_contractor_email_info');
            Route::post('/mgt_contractor/login_settings/change', [ContractorManagementController::class,'ajaxMgtChangeContractorLoginSettings'])->name('mgt_change_contractor_login_setting');

            Route::post('/mgt_contractor/location/{id_contractor}', [ContractorManagementController::class,'ajaxMgtGetContractorLocation'])->name('mgt_get_contractor_location');

            Route::post('/mgt_contractor/locations/change', [ContractorManagementController::class,'ajaxMgtChangeContractorLocation'])->name('mgt_get_contractor_location');

            Route::post('/mgt_contractor/skill/{id_contractor}', [ContractorManagementController::class,'ajaxMgtGetContractorSkill'])->name('mgt_get_contractor_skill');

            Route::post('/mgt_contractor/skills/change', [ContractorManagementController::class,'ajaxMgtChangeContractorSkills'])->name('mgt_get_contractor_location');

         });


});



Route::prefix('api/maintenance/')->group(
    function () {
        Route::post('save/new', [ApiMaintenanceMgtController::class, 'saveNewMaintenance']);
        Route::get('/get_data_to_create', [ApiMaintenanceMgtController::class, 'getDataToCreate']);
        Route::get('/resident_reporter', [ApiMaintenanceMgtController::class,'getLocationResident']);
    }
);

  //API routes for portal
Route::prefix('/api/contractor/')->group(
    function () {

        Route::any('list_details', [ApiContractorMgtController::class, 'getContractorListDetail']);

        Route::post('save/new', [ApiContractorMgtController::class, 'saveNewContractor']);

        Route::post('delete', [ApiContractorMgtController::class, 'deleteContractor']);

        Route::post('get_attachments', [ApiContractorMgtController::class, 'getContractorAttachments']);

        Route::post('get_tasks', [ApiContractorMgtController::class, 'getContractorTasks']);

        Route::post('get_email', [ApiContractorMgtController::class, 'getContractorEmailInfo']);

        Route::post('change/login_setting', [ApiContractorMgtController::class, 'changeContractorLoginSetting']);


        Route::post('get_locations', [ApiContractorMgtController::class, 'getContractorLocationsInfo']);

        Route::post('mgt_locations/change', [ApiContractorMgtController::class, 'changeContractorLocations']);

        Route::post('get_skills', [ApiContractorMgtController::class, 'getContractorSkillsInfo']);

        Route::post('mgt_skills/change', [ApiContractorMgtController::class, 'changeContractorSkills']);

    }
);




