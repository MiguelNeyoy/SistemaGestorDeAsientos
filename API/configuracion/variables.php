<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();



$DB_HOST=$_ENV["DB_HOST"];
$DB_USER = $_ENV["BB_USER"];
$DB_PASS=$_ENV["DB_PASS"];
$DB_NAME=$_ENV["DB_NAME"];
$dbPort = $_ENV["DB_PORT"];