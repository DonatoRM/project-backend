<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class MunicipalitiesModel extends Orm
{
    public function __construct()
    {
        parent::__construct('id', 'municipalities');
    }
}