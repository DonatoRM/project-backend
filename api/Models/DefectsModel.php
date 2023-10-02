<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class DefectsModel extends Orm
{
    public function __construct()
    {
        parent::__construct('id', 'defects');
    }
}