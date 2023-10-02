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
        switch ($this->method) {
            case 'GET':
                if ($this->model !== '') {
                    $nameClass = "Api\Models\\" . ucfirst(mb_strtolower($this->model)) . 'Model';
                    if (class_exists($nameClass)) {
                        $objQuery = new $nameClass();
                        header('Content-type: application/json');
                        if ($this->function === 'id') {
                            if (count($this->args) >= 1) {
                                echo json_encode($objQuery->getById($this->args[0]));
                            } else {
                                Services::undefinedMethod();
                            }
                        } else if ($this->function === '') {
                            echo json_encode($objQuery->getAll());
                        } else {
                            Services::undefinedMethod();
                        }
                    } else {
                        Services::undefinedController();
                    }
                } else {
                    Services::undefinedController();
                }
                break;
            case 'POST':
                // TODO: Estoy aquí

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