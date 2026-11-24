<?php

declare(strict_types=1);

class ReservationRepository
{
  public function __construct(private PDO $pdo) {}

  public function createReservation(
    int $userId,
    int $seatId,
    string $reservationDate,
    string $startTime,
    string $endTime,
    string $activeStatus = 'CONFIRMED'
  ): int {
    $this->pdo->beginTransaction();

    try {
      $seatStmt = $this->pdo->prepare(
        "SELECT id, status FROM seats WHERE id = :seat_id FOR UPDATE"
      );
      $seatStmt->execute(['seat_id' => $seatId]);
      $seat = $seatStmt->fetch(PDO::FETCH_ASSOC);

      if ($seat === false) throw new RuntimeException("존재하지 않는 좌석입니다.");

      if (in_array(strtoupper((string)$seat['status']), ['MAINTENANCE', 'UNAVAILABLE', 'DISABLED'], true))
        throw new RuntimeException("현재 이용할 수 없는 좌석입니다.");

      $overlapStmt = $this->pdo->prepare(
        "SELECT id
        FROM reservations
        WHERE seat_id = :seat_id
        AND reservation_date = :reservation_date
        AND status = :status
        AND start_time < :end_time
        AND end_time > :start_time
        LIMIT 1
        FOR UPDATE"
      );

      $overlapStmt->execute([
        'seat_id' => $seatId,
        'reservation_date' => $reservationDate,
        'status' => $activeStatus,
        'start_time' => $startTime,
        'end_time' => $endTime,
      ]);

      if ($overlapStmt->fetch(PDO::FETCH_ASSOC) !== false)
        throw new RuntimeException("선택한 날짜와 시간대에 이미 예약된 좌석입니다.");

      $insertStmt = $this->pdo->prepare(
        "INSERT INTO resevations
        (user_id, seat_id, reservation_date, start_time, end_time, status, created_at)
        VALUES
        (:user_id, :seat_id, :reservation_date, :start_time, :end_tiem, :status, NOW())
        "
      );

      $insertStmt->execute([
        'user_id' => $userId,
        'seat_id' => $seatId,
        'reservation_date' => $reservationDate,
        'status' => $activeStatus,
        'start_time' => $startTime,
        'end_time' => $endTime
      ]);

      $newId = (int)$this->pdo->lastInsertId();
      $this->pdo->commit();

      return $newId;
    } catch (Throwable $e) {
      if ($this->pdo->inTransaction()) $this->pdo->rollBack();
      throw $e;
    }
  }

  public function findByUserId(int $userId): array
  {
    $stmt = $this->pdo->prepare(
      "SELECT
      res.id,
      res.seat_id,
      res.reservation_date,
      res.start_time,
      res.end_time,
      res.status,
      res.created_at,
      s.seat_number,
      r.name AS room_name
      FROM reservations res
      INNER JOIN seats s ON s.id = res.seat_id
      INNER JOIN rooms r ON r.id = s.room_id
      WHERE res.user_id = :user_id
      ORDER BY res.reservation_date DESC, res.start_time DESC"
    );
    $stmt->execute(['user_id' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function cancelReservation(int $reservationId, int $userId, string $cancelledStatus = 'CANCELLED'): bool
  {
    $stmt = $this->pdo->prepare(
      "UPDATE reservations
      SET status = :cancelled_status
      WHERE id = :id
      AND user_id = :user_id
      AND status <> :already_cancelled"
    );

    $stmt->execute([
      'cancelled_status' => $cancelledStatus,
      'id' => $reservationId,
      'user_id' => $userId,
      'already_cancelled' => $cancelledStatus
    ]);

    return $stmt->rowCount() > 0;
  }
}
