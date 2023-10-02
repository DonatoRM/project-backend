<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class LocationsModel extends Orm
{
    public function __construct()
    {
        parent::__construct('id', 'locations');
    }
}