<?php

declare(strict_types=1);

class RoomRepository
{

  public function __construct(private PDO $pdo) {}

  public function findAllWithSeats(): array
  {
    $rooms = $this->pdo
      ->query("SELECT id, name, description, capacity FROM rooms ORDER BY id")
      ->fetchAll(PDO::FETCH_ASSOC);

    $seatStmt = $this->pdo->prepare(
      "SELECT id, room_id, seat_number, status FROM seats WHERE room_id = :room_id ORDER BY id"
    );

    $result = [];
    foreach ($rooms as $room) {
      $seatStmt->execute(['room_id' => $room['id']]);
      $result[] = [
        'room' => $room,
        'seats' => $seatStmt->fetchAll(PDO::FETCH_ASSOC),
      ];
    }

    return $result;
  }
}
