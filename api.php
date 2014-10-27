<?php
require_once __DIR__ . '/vendor/autoload.php';

$conn = new Dolphin\Connection("localhost", "root", "", "offers_site");
foreach ($conn->fetchColumn("SELECT id FROM categories") as $col) {
    echo $col . "\n";
}

