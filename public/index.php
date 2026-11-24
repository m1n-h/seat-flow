<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../src/Controllers/HomeController.php";
require_once __DIR__ . "/../src/Controllers/AuthController.php";
require_once __DIR__ . "/../src/Controllers/ReservationController.php";

function h(?string $value): string
{
  return htmlspecialchars((string)$value, ENT_QUOTES  | ENT_SUBSTITUTE, "UTF-8");
}

$pdo = getDbConnection();
$authController = new AuthController($pdo);
$reservationController = new ReservationController($pdo);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = (string)($_GET['action'] ?? $_POST['action'] ?? '');
$page = (string)($_GET['page'] ?? 'home');

if ($method === 'POST') {
  match ($action) {
    'register' => $authController->register(),
    'login' => $authController->login(),
    'logout' => $authController->logout(),
    'reservation_create' => $reservationController->create(),
    'reservation_cancel' => $reservationController->cancel(),
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
  'my_reservations' => $reservationController->myReservations(),
  'home' => (new HomeController($pdo))->index(),
  default => (function () {
    http_response_code(404);
    echo '페이지를 찾을 수 없습니다.';
  })(),
};
