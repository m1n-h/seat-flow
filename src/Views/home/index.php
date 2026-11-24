<?php

$pageTitle = 'SeatFlow - 좌석/스터디룸 예약';

require_once __DIR__ . '/../layouts/top.php';

$oldReservation = $oldReservation ?? [
  'seat_id' => '',
  'reservation_date' => date('Y-m-d'),
  'start_time' => date('H:00', strtotime('+1 hours')),
  'end_time' => date('H:00', strtotime('+3 hours')),
];

$roomSeatRows = $roomSeatRows ?? [];
?>

<section class="w-100">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-1">좌석 및 스터디룸 예약</h1>
  </div>
  <?php if ($currentUser !== null) : ?>
    <a href="/?page=my_reservations" class="btn btn-outline-primary btn-sm">MY예약</a>
  <?php endif; ?>

  <?php if (!empty($successMessage)) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= h((string)$successMessage) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="닫기"></button>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">예약하기</div>
        <div class="card-body">
          <?php if ($currentUser !== null) : ?>
            <div class="alert alert-secondary small mb-3">
              로그인 후 예약 가능 합니다.
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="goLogin()">로그인</button>
          <?php else : ?>
            <form method="post" action="/?action=reservation_create">
              <div class="mb-2">
                <label for="seat_id" class="form-label">룸/좌석</label>
                <select id="seat_id" name="seat_id" class="form-select" required>
                  <option value="">선택</option>
                  <?php foreach ($roomSeatRows as $row) :
                    if (empty($row['seat_id'])) continue;
                    $seatIdVal = (int)$row['seat_id'];
                    $seatStatus = strtoupper((string)$row['seat_status'] ?? '');
                    $isDisabled = in_array($seatStatus, ['MAINTENANCE', 'UNAVAILABLE', 'DISABLED'], true);
                    $selected = ((string)$oldReservation['seat_id'] === (string)$seatIdVal) ? 'selected' : '';
                  ?>
                    <option value="<?= $seatIdVal ?>" <?= $selected ?> <?= $isDisabled ? 'disabled' : '' ?>>
                      <?= h((string)$row['room_name']) . '-' . h((string)$row['seat_number']) ?>
                      <?= $isDisabled ? ' (이용불가)' : '' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-2">
                <label for="reservation_date" class="form-label fw-semibold">예약일시</label>
                <input type="date"
                  id="reservation_date"
                  name="reservation_date"
                  class="form-control"
                  value="<?= h((string)$oldReservation['reservation_date']) ?>"
                  min="<?= h(date('Y-m-d')) ?>"
                  required>
              </div>

              <div class="row g-2 mb-2">
                <div class="col-6">
                  <label for="start_time" class="form-label fw-semibold">시작시간</label>
                  <input type="time"
                    id="start_time"
                    name="start_time"
                    class="form-control"
                    value="<?= h(substr((string)$oldReservation['start_time'], 0, 5)) ?>"
                    required>
                </div>

                <div class="col-6">
                  <label for="end_time" class="form-label fw-semibold">종료시간</label>
                  <input type="time"
                    id="end_time"
                    name="end_time"
                    class="form-control"
                    value="<?= h(substr((string)$oldReservation['end_time'], 0, 5)) ?>"
                    required>
                </div>
              </div>

              <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">예약 신청</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">룸 및 좌석 현황</div>
        <div class="card-body">
          <?php if (empty($roomSeatRows)) : ?>
            <p class="text-muted mb-0">등록된 룸/좌석 정보가 없습니다.</p>
          <?php else : ?>
            <div class="row g-3">
              <?php foreach ($roomSeatRows as $row) :
                if (empty($row['seat_id'])) continue;
                $seatStatus = strtoupper((string)$row['seat_status'] ?? 'AVAILABLE');
                $isUnavailable = in_array($seatStatus, ['MAINTENANCE', 'UNAVAILABLE', 'DISABLED'], true);
                $badgeClass = $isDisabled ? 'bg-secondary' : 'bg-success';
                $badgeText = $isUnavailable ? '이용 불가' : '예약 가능';
              ?>
                <div class="col-md-6">
                  <div class="border rounded p-3 h-100 d-flex flex-column justify-content-between bg-white">
                    <div>
                      <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="fw-bold"><?= h((string)$row['room_name']) ?> · <?= h((string)$row['seat_number']) ?></span>
                        <span class="badge <?= $badgeClass ?>"><?= h($badgeText) ?></span>
                      </div>

                      <?php if (!empty($row['room_description'])) : ?>
                        <p class="text-muted small mb-2"><?= h((string)$row['room_description']) ?></p>
                      <?php endif; ?>
                    </div>

                    <div class="text-muted small">
                      정원: <?= (int)($row['room_capacity'] ?? 1) . '명' ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  function goLogin() {
    location.href = '/?page=login';
  }
</script>

<?php require_once __DIR__ . '/../layouts/bottom.php';
