<?php

namespace Api\Helpers;

use Api\Models\UsersModel;
use JetBrains\PhpStorm\NoReturn;

/**
 * Clase de Autenticación de usuarios
 */
class Services
{
    /**
     * Método que inicia la Autenticación
     * @return int Devuelve el Rol del usuario autenticado
     */
    public static function login(): int
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header("WWW-Authenticate: Basic realm='Private data'");
            header("HTTP/1.0 401 Unauthorized");
            die('Sorry. Incorrect Credentials');
        }
        $objUserModel = new UsersModel();
        $role = $objUserModel->authentication($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        if ($role === 0) {
            header("HTTP/1.0 401 Unauthorized");
            die('Sorry. Incorrect Credentials');
        };
        return $role;
    }

    /**
     * Método que lanza un error 404 si no está definido el Controller
     *
     * @return void
     */
    #[NoReturn] public static function undefinedController(): void
    {
        header("HTTP/1.0 404 Not Found");
        die('Sorry. Page not found');
    }

    /**
     * Método que lanza un error 404 si no está definido el Método a ejecutar
     *
     * @return void
     */
    #[NoReturn] public static function undefinedFunction(): void
    {
        header("HTTP/1.0 404 Not Found");
        die('Sorry. Page not found');
    }

    #[NoReturn] public static function undefinedMethod(): void
    {
        header("HTTP/1.0 404 Not Found");
        die('Sorry. Page not found');
    }

    #[NoReturn] public static function actionMethod(): void
    {
        header("HTTP/1.0 400 Bad request");
        die('Sorry. This request is not available');
    }

    #[NoReturn] public static function servicesMethod(): void
    {
        header("HTTP/1.0 404 Not Found");
        die('Sorry. Service is not exists');
    }

    #[NoReturn] public static function insertionOK(): void
    {
        header("HTTP/1.0 201 Created");
        echo('Congratulations. The operation was successful');
    }

    #[NoReturn] public static function undefinedError(): void
    {
        header("HTTP/1.0 400 Bad request");
        die('Sorry. Wrong query');
    }

    #[NoReturn] public static function unauthorizedAccess(): void
    {
        header("HTTP/1.0 401 Unauthorized");
        die('Sorry. Incorrect Credentials');
    }
}