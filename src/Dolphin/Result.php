<?php
namespace Dolphin;

class Result
{
    private $statement;
    private $pdo;

    public function __construct(\PDOStatement $result, \PDO $pdo)
    {
        $this->statement = $result;
        $this->pdo = $pdo;
    }

    public function asArrays()
    {
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function asArray()
    {
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function asColumn($columnName)
    {
        $data = [];
        $rows = $this->asArrays();
        foreach ($rows as $row) {
            $data[] = $row[$columnName];
        }
        return $data;
    }

    public function asValue()
    {
        return $this->statement->fetchColumn(0);
    }

    public function asKeyValue()
    {

    }

    public function asObject($class)
    {
        return $this->statement->fetchObject($class);
    }

    public function asObjects($class)
    {
        $objects = $this->statement->fetchAll(\Pdo::FETCH_CLASS, $class);
        return $objects;
    }

    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    public function insertId()
    {
        return $this->pdo->lastInsertId();
    }
}