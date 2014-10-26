<?php
namespace Dolphin;

class ConnectionManager
{
    protected $connections = [];
    protected $defaultConnection;

    public function __call($func, $args)
    {
        $connectionObj = $this->connections[$this->defaultConnection];
        if (method_exists($connectionObj, $func)) {
            call_user_func_array([$connectionObj, $func], $args);
        }
    }

    public function addConnection($connectionName, Connection $connection)
    {
        $this->connections[$connectionName] = $connection;
    }

    public function __invoke($connection)
    {
        return $this->connections[$connection];
    }
}