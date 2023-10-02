<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class PositionsModel extends Orm
{
    public function __construct()
    {
        parent::__construct('id', 'positions');
    }
}