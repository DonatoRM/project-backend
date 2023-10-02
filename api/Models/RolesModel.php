<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class RolesModel extends Orm
{
    public function __construct()
    {
        parent::__construct('id', 'roles');
    }
}