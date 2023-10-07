<?php

namespace Api\Controllers;

use Api\Controllers\Base\BaseAction;
use Api\Helpers\Services;

class PageController extends BaseAction
{
    private string $role;

    public function __construct(string $role)
    {
        parent::__construct();
        $this->role = $role;
    }

    public function init(): void
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        $this->method = $_SERVER['REQUEST_METHOD'];
        switch ($this->method) {
            case 'GET':
                if ($this->model !== '') {
                    $nameClass = "Api\Models\\" . ucfirst(mb_strtolower($this->model)) . 'Model';
                    if (class_exists($nameClass)) {
                        $objQuery = new $nameClass($this->role);
                        header('Content-type: application/json');
                        echo json_encode($objQuery->getByParams());
                    } else {
                        Services::undefinedController();
                    }
                } else {
                    Services::undefinedController();
                }
                break;
            case 'POST':
                if ($this->model !== '') {
                    $nameClass = "Api\Models\\" . ucfirst(mb_strtolower($this->model)) . 'Model';
                    if (class_exists($nameClass)) {
                        $objQuery = new $nameClass($this->role);
                        header('Content-type: application/json');
                        $objQuery->insert();
                    } else {
                        Services::undefinedController();
                    }
                } else {
                    Services::undefinedController();
                }
                break;
            case 'PUT':
                echo 'PUT';
                break;
            case 'DELETE':
                echo 'DELETE';
                break;
            default:
                Services::undefinedMethod();
        }
    }
}