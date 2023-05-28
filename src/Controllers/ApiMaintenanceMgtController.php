<?php

namespace Odisse\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Odisse\Maintenance\Models\Contractor;

class ApiMaintenanceMgtController extends Contractor{

    public function saveNewMaintenance( Request $request)
    {

        if( $request->hasFile('files')){
            Log::info("has file");
        }
        else{
            Log::info("has no file");
            Log::info( print_r($request->all(), true));
        }


        foreach($request->all() as $key=>$data){
            Log::info($key);
        }


        return response()->json([
            'status' => 'ok',
            'data'=> 'created'

        ], 200);
    }
}


