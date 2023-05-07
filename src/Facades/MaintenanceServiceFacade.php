<?php

namespace Odisse\Maintenance\Facades;

use Illuminate\Support\Facades\Facade;

class MaintenanceServiceFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'OdisseMaintenances';
    }
}
