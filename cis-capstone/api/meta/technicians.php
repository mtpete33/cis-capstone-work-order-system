<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';

startSession();

if (!isset($_SESSION['user'])) {
  http_response_code(401);
  echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
  exit;
}

try {
  $pdo = getPDO();

  $sql = '
    SELECT
      u."userID",
      u."userName",
      u."email"
    FROM users u
    WHERE u."roleID" = 2
    ORDER BY u."userName" ASC, u."email" ASC
    ';

  $stmt = $pdo->query($sql);
  $rows = $stmt->fetchAll();

  echo json_encode(['ok' => true, 'items' => $rows]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()]);
}