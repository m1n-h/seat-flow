<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? "SeatFlow - 실시간 좌석 예약 시스템";
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

<body>
  <header class="site-header">
    <div class="header-inner container">
      <a href="/" class="brand">SeatFlow</a>
      <nav class="nav">
        <a href="/">룸/좌석 현황</a>
        <!-- 이후 예약, 내 예약, 로그인 링크 추가 -->
      </nav>
    </div>
  </header>
  <main class="container">