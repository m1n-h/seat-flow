<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../src/Controllers/HomeController.php";
require_once __DIR__ . "/../src/Controllers/AuthController.php";

function h(?string $value): string
{
  return htmlspecialchars((string)$value, ENT_QUOTES  | ENT_SUBSTITUTE, "UTF-8");
}

$pdo = getDbConnection();
$authController = new AuthController($pdo);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = (string)($_GET['action'] ?? $_POST['action'] ?? '');
$page = (string)($_GET['page'] ?? 'home');

if ($method === 'POST') {
  match ($action) {
    'register' => $authController->register(),
    'login' => $authController->login(),
    'logout' => $authController->logout(),
    default => (function () {
      http_response_code(404);
      echo '잘못된 요청입니다.';
    })(),
  };
  exit;
}

match ($page) {
  'login' => $authController->showLogin(),
  'register' => $authController->showRegister(),
  'home' => (new HomeController($pdo))->index(),
  default => (function () {
    http_response_code(404);
    echo '페이지를 찾을 수 없습니다.';
  })(),
};
