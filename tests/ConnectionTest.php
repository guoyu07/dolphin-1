<?php
use Dolphin\Connection;
use Pseudo\Pdo;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
    public function testQuery()
    {
        $p = new Pdo();
        $p->mock("SELECT * FROM foo", [[1,2,3]]);
        $c = new Connection($p);

        $result = $c->query("SELECT * FROM foo");
        $this->assertInstanceOf('Dolphin\Result', $result);
    }

    public function testInsert()
    {
        $p = new Pdo();
        $p->mock("INSERT INTO items (`foo`, `bar`, `baz`) VALUES (:foo, :bar, :baz)", 1, []);
        $c = new Connection($p);
        $result = $c->insert("items", ["foo" => 1, "bar" => 2, "baz" => 3]);
        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {

    }

    public function testUpsert()
    {

    }

    public function testDelete()
    {

    }
}