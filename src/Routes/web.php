<?php

use Illuminate\Support\Facades\Route;
use Odisse\Maintenance\Controllers\MaintenanceController;
use Odisse\Maintenance\Controllers\MaintenanceDashboardController;
use Odisse\Maintenance\Controllers\MaintenanceManagementController;
use Odisse\Maintenance\Controllers\ContractorController;
use Odisse\Maintenance\Controllers\MaintenanceAttachmentController;

Route::group(['prefix' => 'maintenance'],function () {

    Route::any('/testItem', [MTestController::class,'testFunc']);
    Route::any('/demo', function(){
        echo "OK";
    });
});

Route::middleware(['web','ProxyCAS'])->group(
    function () {
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
           Route::get('/mgt/create', [MaintenanceManagementController::class,'showCreateMaintenancePage']);



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

           Route::post('/get/resident_reporter', [MaintenanceController::class,'ajaxGetResidentReporter'])->name('save_new_maintenance');

           //maintenance detail
           Route::get('/detail/{maintenanceId}', [MaintenanceController::class,'showMaintenanceDetailPage'])->name('maintenance_detail');

           Route::post('/detail/edit', [MaintenanceController::class,'editMaintenanceDetail'])->name('save_new_maintenance');

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

        });

});



