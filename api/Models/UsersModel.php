<?php

namespace Models;


use Models\Base\Connection;
use PDO, PDOException;

/**
 * Clase de acceso a la tabla Usuarios
 */
class UsersModel extends Connection
{
    /**
     * Método para buscar si existe o no un usuario registrado en la BD
     * @param string $user Usuario
     * @param string $pass Contraseña
     * @return int Devuelve el Rol del Usuario
     */
    public function authentication(string $user, string $pass): int
    {
        $passHash = strtoupper(hash('sha256', $pass));
        try {
            $query = "SELECT rol FROM users WHERE username=:u AND password=:p";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(':u', $user);
            $stmt->bindValue(':p', $passHash);
            $stmt->execute();
            if ($stmt->rowCount() == 0) return 0;
            $role = $stmt->fetch(PDO::FETCH_OBJ);
            $stmt = null;
            return $role->rol;
        } catch (PDOException $ex) {
            die('The database query failed. Message: ' . $ex->getMessage());
        }
    }
}