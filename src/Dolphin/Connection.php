<?php
namespace Dolphin;

class Connection
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function query($sql, $params = [])
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return new Result($statement, $this->pdo);
    }

    public function find($table, array $criteria)
    {
        $sql = "SELECT * FROM `$table` WHERE ";
        $criteriaStatements = [];
        foreach ($criteria as $column => $value) {
            $criteriaStatements[] = "`$column` = :$column";
        }
        $sql .= implode(" AND ", $criteriaStatements);
        return $this->query($sql, $criteria);
    }

    public function insert($table, array $data)
    {
        $columnNames = array_keys($data);
        $columns = "`" . implode("`, `", $columnNames) . "`";
        $values = ":" . implode(", :", $columnNames);

        $sql = "INSERT INTO `$table` ($columns) VALUES ($values)";
        $result = $this->query($sql, $data);
        return $result->insertId();
    }

    public function update($table, array $criteria, array $data)
    {
        $sql = "UPDATE `$table` SET ";
        foreach ($data as $key => $val) {
            $updates[] = "$key = :d$key";
            $params["d$key"] = $val;
        }
        $sql .= implode(", ", $updates);

        foreach ($criteria as $key => $val) {
            $wheres[] = "$key = :w$key";
            $params["w$key"] = $val;
        }
        $sql .= " WHERE " . implode(" AND ", $wheres);
        $result = $this->query($sql, $params);
        return $result->rowCount();
    }

    public function delete($table, array $criteria)
    {
        $sql = "DELETE FROM `$table` WHERE ";
        $criteriaStatements = [];
        foreach ($criteria as $column => $value) {
            $criteriaStatements[] = "`$column` = :$column";
        }
        $sql .= implode(" AND ", $criteriaStatements);
        return $this->query($sql, $criteria)->rowCount();
    }

    public function upsert($table, array $criteria, array $data)
    {

    }

    public static function pdoFactory($connectionString)
    {

    }
}