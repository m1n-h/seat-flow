<?php

declare(strict_types=1);

require_once __DIR__ . "/../Models/RoomRepository.php";
require_once __DIR__ . "/../Models/SeatRepository.php";

class HomeController
{
  private SeatRepository $seatRepository;

  public function __construct(private PDO $pdo)
  {
    $this->seatRepository = new SeatRepository($pdo);
  }

  public function index(): void
  {
    $errorMessage = $_SESSION['flash_error'] ?? null;
    $successMessage = $_SESSION['flash_success'] ?? null;
    $oldReservation = $_SESSION['old_reservation'] ?? [
      'seat_id' => '',
      'reservation_date' => date('Y-m-d'),
      'start_time' => date('H:00', strtotime('+2 hours')),
      'end_time' => date('H:00', strtotime('+3 hours')),
    ];

    unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['old_reservation']);

    $roomWithSeats = [];

    try {
      $roomWithSeats = $this->seatRepository->findAllRoomsWithSeats();
    } catch (Throwable $e) {
      error_log('[Home Index Error] ' . $e->getMessage());
      $errorMessage = $errorMessage ?? "룸 및 좌석 목록을 불러오는 중 오류가 발생했습니다.";
    }

    // require __DIR__ . "/../Views/home.php";
    require __DIR__ . "/../Views/home/index.php";
  }
}
