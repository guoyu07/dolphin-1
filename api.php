<?php
$cm = new Dolphin\ConnectionManager();
$cm->addConnection($pdo, 'default');
$cm('default')->query("SELECT * FROM foo LIMIT 1")->asObject();

$cm()->delete('items', ['foo' => 'bar']);