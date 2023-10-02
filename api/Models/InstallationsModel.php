<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class InstallationsModel extends Orm
{
    public function __construct()
    {
        parent::__construct('id', 'installations');
    }
}