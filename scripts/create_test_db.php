<?php

declare(strict_types=1);

$envPath = __DIR__ . '/../.env';
$env = is_file($envPath) ? parse_ini_file($envPath, false, INI_SCANNER_RAW) : [];

$host = $env['DB_HOST'] ?? '127.0.0.1';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';
$dbName = $env['DB_TEST_DATABASE'] ?? 'crms_test';

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "MySQL connection failed: {$mysqli->connect_error}\n");
    exit(1);
}

if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbName}`")) {
    fwrite(STDERR, "Failed to create database {$dbName}: {$mysqli->error}\n");
    exit(1);
}

echo "Test database ready: {$dbName}\n";
