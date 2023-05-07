<?php

namespace Odisse\Maintenance\Services;

use Odisse\Maintenance\Models\Maintenance;
use Odisse\Maintenance\Models\Template;

class MaintenanceService
{

    protected $MaintenanceRepository;
    protected $attributeRepository;


    public function __construct($MaintenanceRepository){
        $this->MaintenanceRepository = $MaintenanceRepository;
    }


    //create new Maintenance
    public function testMethod()
    {
        return "this is in service ";
    }

    //create new Maintenance
    public function createMaintenance( $data )
    {
        return $this->MaintenanceRepository->create( $data );
    }

}
