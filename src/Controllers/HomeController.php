<?php

declare(strict_types=1);

require_once __DIR__ . "/../Models/RoomRepository.php";

class HomeController
{
  public function __construct(private PDO $pdo) {}

  public function index(): void
  {
    $errorMessage = null;
    $roomWithSeats = [];

    try {
      $repo = new RoomRepository($this->pdo);
      $roomWithSeats = $repo->findAllWithSeats();
    } catch (Throwable $e) {
      $errorMessage = "데이터 조회 중 오류가 발생했습니다: " . $e->getMessage();
    }

    require __DIR__ . "/../Views/home.php";
  }
}
