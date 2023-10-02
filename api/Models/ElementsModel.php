<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class ElementsModel extends Orm
{
    public function __construct()
    {
        parent::__construct('id', 'elements');
    }
}