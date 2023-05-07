<?php

use Illuminate\Support\Facades\Route;
use Odisse\Maintenance\Controllers\MTestController;
use Odisse\Maintenance\Controllers\MaintenanceController;
use Odisse\Maintenance\Controllers\MaintenanceDashboardController;
use Odisse\Maintenance\Controllers\ContractorController;


Route::group(['prefix' => 'maintenance'],function () {

Route::any('/demo', function(){
    echo "OK";
});
});
Route::group(['prefix' => 'maintenance', 'middleware' => ['ProxyCAS', 'AuthenticatedUsersMiddleware', 'settingsLoader']],function () {

    // Route::any('/testItem', [MTestController::class,'testFunc']);
    //create maintenance
    Route::any('/testItem', [MaintenanceController::class,'testFunc']);

    Route::get('/create/page', [MaintenanceController::class,'createNewMaintenancePage']);
    Route::get('/contractor', [ContractorController::class,'showContractorPage']);
    Route::post('/contractor', [ContractorController::class,'storeContractor']);
    Route::get('/contractor/{id_contractor}', [ContractorController::class,'showEditContractorPage']);
    Route::post('/contractor/{id_contractor}', [ContractorController::class,'updateContractor']);
    Route::get('/contractors', [ContractorController::class,'showContractorManagementPage'])->name('contractor_management_page');
    Route::post('/contractors', [ContractorController::class,'ajaxLoadContractors'])->name('load_contractors');
    Route::post('/contractor/delete/{id_contractor}', [ContractorController::class,'ajaxDeleteContractor'])->name('delete_contractor');
    Route::get('/dashboard', [MaintenanceDashboardController::class,'showDashboardPage'])->name('maintenance_dashboard');
    Route::post('/maintenances_list', [MaintenanceDashboardController::class,'ajaxLoadMaintenances'])->name('load_maintenances');

    Route::post('/upload/file', [MaintenanceController::class,'ajaxUploadMaintenanceFile'])->name('file_upload');

   Route::post('/find/maintenance_title', [MaintenanceController::class,'ajaxFindMaintenanceTitle'])->name('find_maintenance_title');

   Route::post('/new/save', [MaintenanceController::class,'saveNewMaintenence'])->name('save_new_maintenance');

   Route::post('/get/resident_reporter', [MaintenanceController::class,'ajaxGetResidentReporter'])->name('save_new_maintenance');

   //maintenance detail
   Route::get('/detail/{maintenanceId}', [MaintenanceController::class,'showMaintenanceDetailPage'])->name('save_new_maintenance');


}
);
