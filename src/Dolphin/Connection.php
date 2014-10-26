<?php
namespace Dolphin;

class Connection
{
    protected $mysqli;

    public function __construct($hostname, $username, $password, $database = null)
    {
        $this->mysqli = new \mysqli($hostname, $username, $password, $database);
    }

    protected function query($sql, $params = [])
    {
        // detect if params is a hash or an array to determine statement preparation steps
        $emulatedNamedParameters = false;
        if (array_values($params) != $params) {
            $emulatedNamedParameters = true;
        }

        if ($emulatedNamedParameters) {
            $actualParameters = [];
            preg_replace_callback(`:(\w+)`, function($matches) use (&$actualParameters, $params) {
                $actualParameters[] = $params[$matches[1]];
                return "?";
            }, $sql);
        } else {
            $actualParameters = $params;
        }

        $stmt = $this->mysqli->prepare($sql);

        $bindFormatString = "";
        foreach ($actualParameters as $parameter) {
            if (is_int($parameter)) {
                $bindFormatString .= "i";
            } else if (is_double($parameter) || is_float($parameter)) {
                $bindFormatString .= "d";
            } else {
                $bindFormatString .= "s";
            }
        }
        $bindArgs = $actualParameters;
        array_unshift($bindArgs, $bindFormatString);

        call_user_func_array([$stmt, 'bind_param'], $bindArgs);
        $stmt->execute();
        $stmt->store_result();
        $results =
        $stmt->close();
    }

    public function fetchAll($sql, $params)
    {

    }

    public function fetchRow($sql, $params)
    {

    }

    public function fetchColumn($sql, $params)
    {

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