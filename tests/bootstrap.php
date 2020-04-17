<?php

if (isset($_SERVER['DB_CONNECTION']) and isset($_SERVER['DB_DATABASE'])) {
    if ($_SERVER['DB_CONNECTION'] == 'sqlite' and !is_file($_SERVER['DB_DATABASE'])) {
        $dbPath = dirname($_SERVER['DB_DATABASE']);
        if (!is_file($dbPath) and !is_dir($dbPath)) {
            mkdir($dbPath, 0777, true);
        }
        if (!is_file($_SERVER['DB_DATABASE'])) {
            touch($_SERVER['DB_DATABASE']);
        }
    }
}

require_once __DIR__ . '/../vendor/autoload.php';
