<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../src/Controllers/HomeController.php";

function h(?string $value): string
{
  return htmlspecialchars((string)$value, ENT_QUOTES  | ENT_SUBSTITUTE, "UTF-8");
}

$pdo = getDbConnection();
$controller = new HomeController($pdo);
$controller->index();
