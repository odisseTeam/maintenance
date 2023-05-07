<?php

namespace Odisse\Maintenance\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceRepository implements RepositoryInterface
{

    public function getAll(){}
    public function getById($model_id){}
    public function delete( $model_id){}
    public function create(array $data){}
    public function update( Model $model, array $data){}
    public function getFulfilled(){}

}
