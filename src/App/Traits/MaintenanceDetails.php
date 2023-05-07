<?php
/**
 * Created by PhpStorm.
 * User: hedi
 * Date: 1/13/20
 * Time: 12:11 PM
 */

 namespace Odisse\Maintenance\App\Traits;

use App\SLP\Enum\GeneralConfigConstants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;
use Odisse\Maintenance\App\SLP\Enum\BookingStatusConstants;

trait MaintenanceDetails{

    private function getMaintenanceResidentInfo($rooms ){

        $now = date('Y-m-d H:i:s');

        
        $query = DB::table('room')
        ->join('booking_room', 'booking_room.id_room', '=', 'room.id_room')
        ->join('booking_resident', 'booking_resident.id_booking', '=', 'booking_room.id_booking')
        ->join('booking', 'booking_resident.id_booking', '=', 'booking.id_booking')
        ->join('resident', 'booking_resident.id_resident', '=', 'resident.id_resident')
        ->where('booking.booking_status',BookingStatusConstants::Active)
        ->whereIn('room.id_room',$rooms);
        


        $query = $query ->where(function ($query) use ($now){

          $query = $query->whereDate('booking_resident.resident_check_out_date_time','>=',$now)
          ->orWhere('booking_resident.resident_check_out_date_time','=',null);

      });

      $query = $query ->where(function ($query) use ($now){

        $query = $query->whereDate('booking_room.room_check_out_date_time','>=',$now)
        ->orWhere('booking_room.room_check_out_date_time','=',null);

    });

        $query = $query

        ->select('resident.*' )
        ->groupBy('booking_room.id_booking_room')
        ->groupBy('booking_resident.id_booking_resident')
        ->groupBy('room.id_room')
        ->groupBy('resident.id_resident')
        ->get();

        return $query;



    }
}
