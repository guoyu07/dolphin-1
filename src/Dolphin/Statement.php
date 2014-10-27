<?php
namespace Dolphin;

class Statement implements \IteratorAggregate
{
    /** @var \mysqli */
    protected $mysqli;

    /** @var  \mysqli_stmt */
    protected $statement;

    /** @var string */
    protected $sql;

    /** @var array */
    protected $params;

    /**
     * @param \mysqli $mysqli
     * @param string $sql
     * @param array $params
     */
    public function __construct(\mysqli $mysqli, $sql, $params = [])
    {
        $this->sql = $sql;
        $this->params = $params;
        $this->mysqli = $mysqli;
    }

    /**
     * @return Result
     */
    public function execute($params = [])
    {
        $params = $params ?: $this->params;
        $sql = $this->sql;

        if ($params) {
            $emulatedNamedParameters = false;
            if (array_values($params) != $params) {
                $emulatedNamedParameters = true;
            }

            if ($emulatedNamedParameters) {
                $actualParameters = [];
                $sql = preg_replace_callback('`:(\w+)`', function ($matches) use (&$actualParameters, $params) {
                    $actualParameters[] = $params[$matches[1]];
                    return "?";
                }, $sql);
            } else {
                $actualParameters = $params;
            }
            $this->statement = $this->mysqli->prepare($sql);
            if ($this->statement === false) {
                throw new \InvalidArgumentException($this->mysqli->error);
            }

            foreach ($actualParameters as $parameter) {
                if (is_int($parameter)) {
                    $this->statement->bind_param('i', $parameter);
                } else if (is_double($parameter) || is_float($parameter)) {
                    $this->statement->bind_param('d', $parameter);
                } else {
                    $this->statement->bind_param('s', $parameter);
                }
            }
        } else {
            $this->statement = $this->mysqli->prepare($sql);
            if ($this->statement === false) {
                throw new \InvalidArgumentException($this->mysqli->error);
            }
        }

        $this->statement->execute();
    }

    public function getIterator()
    {
        return $this->statement->get_result();
    }

    public function fetchRow()
    {
        $result = $this->statement->get_result();
        return $result->fetch_assoc();
    }

    public function fetchAll()
    {
        return $this->statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchColumn()
    {
        $results = [];
        foreach ($this->fetchAll() as $row) {
            $results[] = array_values($row)[0];
        }
        return $results;
    }

    public function numRows()
    {
        return $this->statement->num_rows;
    }

    public function insertId()
    {
        return $this->statement->insert_id;
    }

    public function __destruct()
    {
        if ($this->statement) {
            $this->statement->close();
        }
    }
}