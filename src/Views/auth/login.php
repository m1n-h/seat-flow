<?php

declare(strict_types=1);

$pageTitle = 'SeatFlow - 로그인';

require_once __DIR__ . '/../layouts/top.php';
?>

<section class="w-100">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h1 class="h4 fw-bold mb-1">로그인</h1>

          <?php if (!empty($successMessage)) : ?>
            <div class="alert alert-success" role="alert">
              <?= h($successMessage) ?>
            </div>
          <?php endif; ?>

          <form method="post" action="/?action=login">
            <div class="mb-2">
              <label for="email" class="form-label">이메일</label>
              <input type="email"
                id="email"
                name="email"
                class="form-control"
                value="<?= h((string)$oldEmail ?? '') ?>"
                required>
            </div>

            <div class="mb-2">
              <label for="password" class="form-label">비밀번호</label>
              <input type="password"
                id="password"
                name="password"
                class="form-control"
                required>
            </div>

            <button type="submit" class="btn btn-sm btn-secondary w-100">로그인</button>
          </form>

          <div class="text-center mt-3 small text-muted">
            아직 계정이 없으신가요?
            <button type="button" class="btn btn-sm btn-primary" onclick="goRegister()">회원가입</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  function goRegister() {
    location.href = '/?page=register';
  }
</script>

<?php require_once __DIR__ . '/../layouts/bottom.php';
