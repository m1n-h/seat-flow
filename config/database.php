<?php

declare(strict_types=1);

function loadEnv(string $envPath): void
{
  if (!file_exists($envPath) || !is_readable($envPath)) return;

  $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if ($lines === false) return;

  foreach ($lines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '' || str_starts_with($trimmed, '#')) continue;

    [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
    $key = trim($key);
    $value = trim($value, " \t\n\r\0\x0B\"'");

    if ($key !== '' && getenv($key) === false) {
      putenv("{$key}={$value}");
      $_ENV[$key] = $value;
    }
  }
}


function getDbConnection(): PDO
{
  static $pdo = null;

  if ($pdo instanceof PDO) return $pdo;

  $baseDir = dirname(__DIR__);
  loadEnv($baseDir . '/.env');

  $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
  $port = $_ENV['DB_PORT'] ?? '3306';
  $dbName = $_ENV['DB_NAME'] ?? 'seat_flow';
  $user = $_ENV['DB_USER'] ?? 'root';
  $pass = $_ENV['DB_PASS'] ?? '';
  $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

  $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  $pdo = new PDO($dsn, $user, $pass, $options);

  return $pdo;
}
