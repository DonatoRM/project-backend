<?php

namespace Api\Models\Base;

use PDO, PDOException;
use Models\Base\Connection;

class Orm extends Connection
{
    protected string $id;
    protected string $table;
    protected string $query = '';

    public function __construct(string $id, string $table)
    {
        parent::__construct();
        $this->id = $id;
        $this->table = $table;
    }

    /**
     * Método que devuelve todos los registros de una tabla
     * @return array
     */
    public function getAll(): array
    {
        try {
            if ($this->query !== "") {
                $stmt = $this->connection->prepare($this->query);
            } else {
                $stmt = $this->connection->prepare("SELECT * FROM " . $this->table);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            return $result;
        } catch (PDOException $ex) {
            die("Failed to search database. Message: " . $ex->getMessage());
        }
    }

    /**
     * Método que devuelve el registro que posee el ID de una tabla
     *
     * @param string $id Identificador del Registro
     * @return array|bool
     */
    public function getById(string $id): array|bool
    {
        try {
            $stmt = $this->connection->prepare(("SELECT * FROM {$this->table} WHERE id=:id"));
            $stmt->bindValue(":id", $id, PDO::PARAM_INT | PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            return $result;
        } catch (PDOException $ex) {
            die("Failed to search database. Message: " . $ex->getMessage());
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
        try {
            $stmt = $this->connection->prepare("DELETE FROM {$this->table} WHERE id=:id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $ex) {
            die("Failed to clear database log. Message: " . $ex->getMessage());
        }
    }

    public function updateById(string $id, array $data): void
    {
        try {
            $sql = "UPDATE {$this->table} SET ";
            foreach ($data as $key => $value) {
                $sql .= "{$key}=:{$key},";
            }
            $sql = trim($sql, ',');
            $sql .= " WHERE id=:id";
            $stmt = $this->connection->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", "{$value}");
            }
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $ex) {
            die("Failed to update database record. Message: " . $ex->getMessage());
        }
    }

    public function insert(string $id, array $data): void
    {
        try {
            $sql = "INSERT INTO {$this->table}(";
            foreach ($data as $key => $value) {
                $sql .= "{$key},";
            }
            $sql = trim($sql, ',');
            $sql .= ") VALUES(";
            foreach ($data as $key => $value) {
                $sql .= ":{$key},";
            }
            $sql = trim($sql, ',');
            $sql .= ")";
            $stmt = $this->connection->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->execute();
        } catch (PDOException $ex) {
            die("Failed to insert database record. Message: " . $ex->getMessage());
        }
    }

    public function paginate(int $page, int $limit): array
    {
        try {
            $offset = ($page - 1) * $limit;
            $rows = $this->connection->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
            $sql = "SELECT * FROM {$this->table} LIMIT {$offset},{$limit}";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $pages = ceil($rows / $limit);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [
                'data' => $data,
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