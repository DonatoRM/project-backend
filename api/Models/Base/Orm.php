<?php

namespace Api\Models\Base;

use Api\Helpers\Services;
use PDO, PDOException;
use Models\Base\Connection;

class Orm extends Connection
{
    protected string $table;
    protected string $query = '';
    private int $role;
    private array $params;
    private array $attributes;

    public function __construct(string $table, array $params, array $attributes, int $role = 0)
    {
        parent::__construct();
        $this->table = $table;
        $this->role = $role;
        $this->params = $params;
        $this->attributes = $attributes;
    }

    /**
     * Método que compara la constante superglobal $_GET con el array $this->params
     * y si existe algún parámetro que no esté contemplado, corta la ejecución del
     * programa
     * @return void
     */
    private function checkParams(): void
    {
        $count = 0;
        foreach ($_GET as $key => $value) {
            if ($count >= 3) {
                $index = array_search($key, $this->params);
                if ($index === false) Services::servicesMethod();
            }
            $count++;
        }
    }

    /**
     * Método que devuelve el registro que posee el ID de una tabla
     *
     * @return array|bool
     */
    public function getByParams(): array|bool
    {
        if ($this->table === 'users' && $this->role !== 3) {
            Services::actionMethod();
        } else {
            try {
                $this->checkParams();
                $page = null;
                $limit = 0;
                if (isset($_GET['page']) && isset($_GET['limit'])) {
                    $page = intval($_GET['page']);
                    unset($_GET['page']);
                    $limit = intval($_GET['limit']);
                    unset($_GET['limit']);
                }
                if ((isset($_GET['page']) && !isset($_GET['limit'])) || (!isset($_GET['page']) && isset($_GET['limit']))) {
                    Services::servicesMethod();
                }
                $query = "SELECT * FROM $this->table ";
                $queryCount = "SELECT COUNT(*) FROM $this->table ";
                if (count($_GET) > 3) {
                    $query .= "WHERE ";
                    $queryCount .= "WHERE ";
                }
                $noFirst = false;
                for ($i = 0; $i < count($this->params); $i++) {
                    if ($noFirst && isset($_GET["{$this->params[$i]}"])) $query .= ' AND ';
                    if (isset($_GET["{$this->params[$i]}"])) {
                        $query .= $this->params[$i] . "=:" . $this->params[$i];
                        $queryCount .= $this->params[$i] . "=:" . $this->params[$i];
                        $noFirst = true;
                    }
                }
                if ($page !== null && $limit !== null) {
                    $offset = ($page - 1) * $limit;
                    $query .= " LIMIT $offset,$limit";
                }
                $stmt = $this->connection->prepare($query);
                foreach ($this->params as $param) {
                    if (isset($_GET["$param"])) {
                        $stmt->bindValue(":$param", $_GET["$param"], PDO::PARAM_INT | PDO::PARAM_STR);
                    }
                }
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt = $this->connection->prepare($queryCount);
                foreach ($this->params as $param) {
                    if (isset($_GET["$param"])) {
                        $stmt->bindValue(":$param", $_GET["$param"], PDO::PARAM_INT | PDO::PARAM_STR);
                    }
                }
                $stmt->execute();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
                $rows = $res['COUNT(*)'];
                $stmt = null;
                if ($limit !== 0) $pages = ceil($rows / $limit);
                if ($page === null && $limit === 0) {
                    $page = 1;
                    $limit = $rows;
                    $pages = 1;
                }
                return [
                    'data' => $result,
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => $pages,
                    'rows' => $rows
                ];
            } catch (PDOException $ex) {
                die("Failed to search database. Message: " . $ex->getMessage());
            }
        }
    }

    /**
     * Método que elimina el registro correspondiente al ID de una tabla
     *
     * @param string $id Identificador del registro
     * @return void
     */
    public function deleteById(string $id): void
    {
        if ($this->table === 'users' && $this->role !== 3) {
            Services::actionMethod();
        } else {
            try {
                $stmt = $this->connection->prepare("DELETE FROM $this->table WHERE id=:id");
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $ex) {
                die("Failed to clear database log. Message: " . $ex->getMessage());
            }
        }
    }

    public function updateById(string $id, array $data): void
    {
        if ($this->table === 'users' && $this->role !== 3) {
            Services::actionMethod();
        } else {
            try {
                $sql = "UPDATE $this->table SET ";
                foreach ($data as $key => $value) {
                    $sql .= "$key=:$key,";
                }
                $sql = trim($sql, ',');
                $sql .= " WHERE id=:id";
                $stmt = $this->connection->prepare($sql);
                foreach ($data as $key => $value) {
                    $stmt->bindValue(":$key", "$value");
                }
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $ex) {
                die("Failed to update database record. Message: " . $ex->getMessage());
            }
        }
    }

    private function okAttributes($dataObject): object|null
    {
        if (count(get_object_vars($dataObject)) !== count($this->attributes)) {
            Services::actionMethod();
        }
        $okAllAttributes = true;
        foreach ($this->attributes as $attribute) {
            if (!property_exists($dataObject, $attribute)) $okAllAttributes = false;
        }
        if (!$okAllAttributes) {
            Services::servicesMethod();
        }
        return $dataObject;
    }

    public function insert(): void
    {
        if (($this->table === 'users' || $this->table === 'roles') && $this->role !== 3) {
            Services::actionMethod();
        } else {
            try {
                $JSONData = file_get_contents("php://input");
                $dataObject = json_decode($JSONData);
                $dataObject = $this->okAttributes($dataObject);
                if ($this->role === 3 && $this->table === 'users') {
                    $dataObject->password = hash('sha256', $dataObject->password);
                }
                $sql = "INSERT INTO $this->table(";
                foreach ($dataObject as $key => $value) {
                    $sql .= "$key,";
                }
                $sql = trim($sql, ',');
                $sql .= ") VALUES(";
                foreach ($dataObject as $key => $value) {
                    $sql .= ":$key,";
                }
                $sql = trim($sql, ',');
                $sql .= ")";
                $stmt = $this->connection->prepare($sql);
                foreach ($dataObject as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }
                $stmt->execute();
                Services::operationOK();

            } catch (PDOException $ex) {
                die("Failed to insert database record. Message: " . $ex->getMessage());
            }
        }
    }

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