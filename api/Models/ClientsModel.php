<?php

namespace Api\Models;

use Api\Models\Base\Orm;

class ClientsModel extends Orm
{
    public function __construct(int $role = 0)
    {
        $this->query = "select c.id as id,c.name as name,c.address as address,c.cp as cp,
       p.name as country,pr.name as province,m.name as municipality,c.location as 
           location,c.phone as phone,c.email as email from clients c inner join countries p on 
               c.country=p.id inner join provinces pr on c.province=pr.id inner join 
               municipalities m on c.municipality =m.id";
        $params = array('id', 'name', 'page', 'limit');
        $attributes = ['name', 'address', 'cp', 'country', 'province', 'municipality', 'location', 'phone', 'email'];
        parent::__construct('clients', $params, $attributes, $role);
    }
}