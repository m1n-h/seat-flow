<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? "SeatFlow - 실시간 좌석 예약 시스템";
$currentUser = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle) ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="bg-light text-dark d-flex flex-column min-vh-100">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="/">SeatFlow</a>

      <div class="d-flex align-items-center gap-2">
        <a class="nav-link text-light px-2 fw-bold small" href="/">룸/좌석 현황</a>

        <?php if ($currentUser !== null): ?>
          <a class="nav-link text-light px-2 fw-bold small" href="/?page=my_reservations">MY예약</a>
          <span class="navbar-text text-light small px-2">
            <strong><?= h((string)$currentUser['name']) ?></strong>님
          </span>
          <form method="post" action="/?action=logout" class="d-inline m-0">
            <button type="submit" class="btn btn-outline-light btn-sm">로그아웃</button>
          </form>
        <?php else: ?>
          <a class="btn btn-secondary btn-sm" href="/?page=login">로그인</a>
          <a class="btn btn-primary btn-sm" href="/?page=register">회원가입</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <main class="container my-4 flex-grow-1">