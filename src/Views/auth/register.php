<?php

declare(strict_types=1);

$pageTitle = 'SeatFlow - 회원가입';

require_once __DIR__ . '/../layouts/top.php';
?>

<section class="w-100">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h1 class="h4 fw-bold mb-1">회원가입</h1>

          <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
              <?= h($errorMessage) ?>
            </div>
          <?php endif; ?>

          <form method="post" action="/?action=register">
            <div class="mb-2">
              <label for="name" class="form-label">이름</label>
              <input type="text"
                id="name"
                name="name"
                class="form-control"
                value="<?= h((string)($oldName ?? '')) ?>"
                required>
            </div>

            <div class="mb-2">
              <label for="email" class="form-label">이메일</label>
              <input type="email"
                id="email"
                name="email"
                class="form-control"
                value="<?= h((string)($oldEmail ?? '')) ?>"
                required>
            </div>

            <div class="mb-2">
              <label for="password" class="form-label">비밀번호</label>
              <input type="password"
                id="password"
                name="password"
                class="form-control"
                required>
              <p class="small m-0 p-0">비밀번호는 6자 이상 입력해주세요.</p>
            </div>

            <div class="mb-2">
              <label for="password_confirm" class="form-label">비밀번호 확인</label>
              <input type="password"
                id="password_confirm"
                name="password_confirm"
                class="form-control"
                required>
            </div>

            <button type="submit" class="btn btn-sm btn-primary w-100">회원가입</button>
          </form>

          <div class="text-center mt-3 small text-muted">
            이미 계정이 있으신가요?
            <button type="button" class="btn btn-sm btn-secondary" onclick="goLogin()">로그인</button>
          </div>
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
