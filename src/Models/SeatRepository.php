<?php

declare(strict_types=1);

class SeatRepository
{
  public function __construct(private PDO $pdo) {}

  public function findAllRoomsWithSeats(): array
  {
    $stms = $this->pdo->query(
      "SELECT
      r.id AS room_id,
      r.name AS room_name,
      r.description AS room_description,
      r.capacity AS room_capacity,
      s.id AS seat_id,
      s.seat_number,
      s.status AS seat_status,
      CASE
           WHEN LOWER(TRIM(s.status)) = 'active' AND active_res.seat_id IS NOT NULL
           THEN 1 ELSE 0
         END AS is_in_use_now
      FROM rooms r
      LEFT JOIN seats s ON s.room_id = r.id
      LEFT JOIN (
      SELECT DISTINCT seat_id
      FROM reservations
      WHERE reservation_date = CURDATE()
        AND UPPER(status) = 'CONFIRMED'
        AND start_time <= CURTIME()
        AND end_time > CURTIME()
      ) AS active_res ON active_res.seat_id = s.id
      ORDER BY r.id ASC, s.id ASC"
    );

    return $stms->fetchAll(PDO::FETCH_ASSOC);
  }

  public function findSeatWithRoomById(int $seatId): ?array
  {
    $stmt = $this->pdo->prepare(
      "SELECT
      s.id AS seat_id,
      s.room_id,
      s.seat_number,
      s.status AS seat_status,
      r.name AS room_name,
      r.capacity AS room_capacity
      FROM seats s
      INNER JOIN rooms r ON r.id = s.room_id
      WHERE s.id = :seat_id
      LIMIT 1
      "
    );

    $stmt->execute(['seat_id' => $seatId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row !== false ? $row : null;
  }
}
