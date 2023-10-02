<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class ProvincesModel extends Orm
{
    public function __construct()
    {
        parent::__construct('id', 'provinces');
    }
}