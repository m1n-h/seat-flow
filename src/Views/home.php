<?php

declare(strict_types=1);

$pageTitle = "SeatFlow - 스터디룸 및 좌석 현황";
$statusLabels = [
  'active' => '이용 가능',
  'maintenance' => '점검 중',
];

require_once __DIR__ . '/layouts/top.php';
?>

<section class="w-100">
  <h1 class="h3 fw-bold mb-1">스터디룸 및 좌석 실시간 현황</h1>
  <p class="text-muted mb-0">원하는 스터디룸의 좌석 상태를 확인하세요.</p>

  <?php if ($errorMessage !== null): ?>
    <div class="alert alert-danger" role="alert">
      <?= h($errorMessage) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($roomWithSeats) && $errorMessage === null): ?>
    <div class="alert alert-secondary" role="alert">
      등록된 스터디룸이 없습니다.
    </div>
  <?php endif; ?>

  <div class="d-flex flex-column gap-3">
    <?php foreach ($roomWithSeats as $item):
      $room = $item['room'];
      $seats = $item['seats'];
    ?>
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 card-title fw-bold mb-0"><?= h($room['name']) ?></h2>
            <span class="badge rounded-pill text-bg-primary">
              정원 <?= h((string)$room['capacity']) ?>명
            </span>
          </div>

          <p class="card-text text-muted small mb-3">
            <?= h($room['description'] ?? '') ?>
          </p>

          <div class="d-flex flex-wrap gap-2">
            <?php if (empty($seats)): ?>
              <span class="text-muted small">등록된 좌석이 없습니다.</span>
              <?php else:
              foreach ($seats as $seat):
                $status = (string)$seat['status'];
                $info = $statusConfig[$status] ?? [
                  'label' => $status,
                  'badge' => 'text-bg-light border',
                ];
              ?>
                <span class="badge <?= h($info['badge']) ?> px-3 py-2 fs-6 fw-normal">
                  <?= h($seat['seat_number']) ?> · <?= h($info['label']) ?>
                </span>
            <?php endforeach;
            endif; ?>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__ . '/layouts/bottom.php';
