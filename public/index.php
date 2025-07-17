<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/autoload.php';

// DB-Setup (CSV-DSN, Daten liegen im data-Ordner)
$str_driver = 'csv';
$str_host = str_replace('\\', '/', realpath(__DIR__ . '/../data'));
$str_dsn = sprintf('%s://%s', $str_driver, $str_host);

use Test\Database;
use Test\Controller\AuthController;

$obj_database = Database::factory($str_dsn);

// Controller starten
$controller = new AuthController($obj_database);
$controller->handleRequest();
