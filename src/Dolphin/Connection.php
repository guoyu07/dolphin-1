<?php
namespace Dolphin;

class Connection
{
    /** @var \mysqli */
    protected $mysqli;

    public function __construct($hostname, $username, $password, $database = null)
    {
        $this->mysqli = new \mysqli($hostname, $username, $password, $database);
    }

    public function query($sql, $params = [])
    {
        $statement = new Statement($this->mysqli, $sql, $params);
        $statement->execute();
        return $statement;
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchObjects($sql, $params, $class = null)
    {
        if ($class === null) {
            $class = \stdClass::class;
        }
    }

    public function fetchRow($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchRow();
    }

    public function fetchColumn($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function fetchValue($sql, $params)
    {

    }

    public function fetchKeyVal($sql, $params)
    {

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
}