<?php

declare(strict_types=1);

require_once __DIR__ . "/../Models/ReservationRepository.php";
require_once __DIR__ . "/../Models/SeatRepository.php";

class ReservationController
{
  private ReservationRepository $reservationRepository;
  private SeatRepository $seatRepository;

  public function __construct(private PDO $pdo)
  {
    $this->reservationRepository = new ReservationRepository($pdo);
    $this->seatRepository = new SeatRepository($pdo);
  }

  public function myReservations(): void
  {
    $user = $_SESSION['user'] ?? null;
    if ($user === null) {
      $_SESSION['flash_error'] = "로그인이 필요한 서비스입니다.";
      header('Location: /?page=login');
      exit;
    }

    $errorMessage = $_SESSION['flash_error'] ?? null;
    $successMessage = $_SESSION['flash_success'] ?? null;
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);

    $reservations = $this->reservationRepository->findByUserId((int)$user['id']);

    require __DIR__ . '/../Views/reservations/my_list.php';
  }

  public function create(): void
  {
    $user = $_SESSION['user'] ?? null;
    if ($user === null) {
      $_SESSION['flash_error'] = "로그인 후 예약할 수 있습니다.";
      header('Location: /?page=login');
      exit;
    }

    $seatId = filter_input(INPUT_POST, 'seat_id', FILTER_VALIDATE_INT);
    $reservationDate = trim((string)($_POST['reservation_date'] ?? ''));
    $startTime = $this->normalizeTime((string)($_POST['start_time'] ?? ''));
    $endTime = $this->normalizeTime((string)($_POST['end_time'] ?? ''));

    $_SESSION['old_reservation'] = [
      'seat_id' => $seatId ?: '',
      'reservation_date' => $reservationDate,
      'start_time' => $startTime ?? '',
      'end_time' => $endTime ?? '',
    ];

    if ($seatId === false || $seatId === null || $seatId <= 0) {
      $_SESSION['flash_error'] = "예약할 좌석을 선택해 주세요.";
      header('Location: /');
      exit;
    }

    if (!$this->isValidDate($reservationDate)) {
      $_SESSION['flash_error'] = "올바른 예약 날짜를 선택해 주세요.";
      header('Location: /');
      exit;
    }

    if ($startTime === null || $endTime === null) {
      $_SESSION['flash_error'] = "시작 일시와 종료 일시를 올바르게 입력해 주세요.";
      header('Location: /');
      exit;
    }

    if ($startTime >= $endTime) {
      $_SESSION['flash_error'] = "종료 일시는 시작 일시보다 늦어야 합니다.";
      header('Location: /');
      exit;
    }

    $today = date('Y-m-d');
    $nowTime = date('H:i:s');

    if ($reservationDate < $today || ($reservationDate === $today && $startTime <= $nowTime)) {
      $_SESSION['flash_error'] = "지난 날짜나 현재 이전 시간으로는 예약이 불가 합니다.";
      header('Location: /');
      exit;
    }

    try {
      $this->reservationRepository->createReservation(
        userId: (int)$user['id'],
        seatId: $seatId,
        reservationDate: $reservationDate,
        startTime: $startTime,
        endTime: $endTime,
        activeStatus: 'CONFIRMED'
      );

      unset($_SESSION['old_reservation']);
      $_SESSION['flash_success'] = "예약이 완료되었습니다.";
      header('Location: /?page=my_reservations');
      exit;
    } catch (RuntimeException $e) {
      $_SESSION['flash_error'] = $e->getMessage();
      header('Location: /');
      exit;
    } catch (Throwable $e) {
      error_log("[Reservation Create Error] " . $e->getMessage());
      $_SESSION['flash_error'] = "예약 처리 중 오류가 발생했습니다.";
      header('Location: /');
      exit;
    }
  }

  public function cancel(): void
  {
    $user = $_SESSION['user'] ?? null;
    if ($user === null) {
      $_SESSION['flash_error'] = "로그인이 필요한 서비스 입니다.";
      header('Location: /?page=login');
      exit;
    }

    $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
    if ($reservationId === false || $reservationId === null || $reservationId <= 0) {
      $_SESSION['flash_error'] = "취소할 예약을 올바르게 선택해 주세요.";
      header('Location: /?page=my_reservations');
      exit;
    }

    try {
      $cancelled = $this->reservationRepository->cancelReservation(
        reservationId: $reservationId,
        userId: (int)$user['id'],
        cancelledStatus: 'CANCELLED'
      );

      if ($cancelled) $_SESSION['flash_success'] = "예약이 취소되었습니다.";
      else $_SESSION['flash_error'] = "취소할 수 없는 예약이거나 권한이 없습니다.";
    } catch (Throwable $e) {
      error_log("[Reservation Cancel Error] " . $e->getMessage());
      $_SESSION['flash_error'] = "예약 취소 처리 중 오류가 발생했습니다.";
    }

    header('Location: /?page=my_reservations');
    exit;
  }

  private function isValidDate(string $date): bool
  {
    $dt = DateTimeImmutable::createFromFormat('Y-m-d', $date);
    return $dt !== false && $dt->format('Y-m-d') === $date;
  }

  private function normalizeTime(string $time): ?string
  {
    $trimmed = trim($time);
    foreach (['H:i', 'H:i:s'] as $format) {
      $dt = DateTimeImmutable::createFromFormat($format, $trimmed);
      if ($dt !== false && $dt->format($format) === $trimmed)
        return $dt->format('H:i:s');
    }
    return null;
  }
}
