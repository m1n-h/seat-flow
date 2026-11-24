<?php
$pageTitle = "SeatFlow - 내 예약 내역";
require __DIR__ . '/../layouts/top.php';

$reservations = $reservations ?? [];
?>

<section class="w-100">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h4 fw-bold mb-1">내 예약 내역</h1>
      <p class="text-muted small mb-0">예약 일정을 확인하고 필요 시 취소할 수 있습니다.</p>
    </div>
    <a href="/" class="btn btn-primary btn-sm">새예약</a>
  </div>

  <?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= h((string)$errorMessage) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="닫기"></button>
    </div>
  <?php endif; ?>

  <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= h((string)$successMessage) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="닫기"></button>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-3">룸 / 좌석</th>
              <th>예약 날짜</th>
              <th>이용 시간</th>
              <th>상태</th>
              <th class="text-end pe-3">관리</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($reservations)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">예약 내역이 없습니다.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($reservations as $res): ?>
                <?php
                $status = strtoupper((string)($res['status'] ?? 'CONFIRMED'));
                $isCancelled = ($status === 'CANCELLED');
                $startShort = substr((string)$res['start_time'], 0, 5);
                $endShort = substr((string)$res['end_time'], 0, 5);
                ?>
                <tr>
                  <td class="ps-3 fw-semibold">
                    <?= h((string)$res['room_name']) ?> · <?= h((string)$res['seat_number']) ?>
                  </td>
                  <td><?= h((string)$res['reservation_date']) ?></td>
                  <td><?= h($startShort) ?> ~ <?= h($endShort) ?></td>
                  <td>
                    <?php if ($isCancelled): ?>
                      <span class="badge bg-secondary">취소됨</span>
                    <?php else: ?>
                      <span class="badge bg-success">예약 확정</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end pe-3">
                    <?php if (!$isCancelled): ?>
                      <form
                        method="post"
                        action="/?action=reservation_cancel"
                        class="d-inline m-0"
                        onsubmit="return confirm('이 예약을 취소하시겠습니까?');">
                        <input type="hidden" name="reservation_id" value="<?= (int)$res['id'] ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm">예약 취소</button>
                      </form>
                    <?php else: ?>
                      <span class="text-muted small">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../layouts/bottom.php';
