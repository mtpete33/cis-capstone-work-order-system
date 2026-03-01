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
      wo."workOrderID",
      wo."title",
      wo."createdAt",
      l."locationName",
      s."statusName",
      p."priorityName"
    FROM work_orders wo
    JOIN locations  l ON l."locationID" = wo."locationID"
    JOIN statuses   s ON s."statusID" = wo."currentStatusID"
    JOIN priorities p ON p."priorityID" = wo."priorityID"
    ORDER BY wo."createdAt" DESC
    LIMIT 100
  ';

  $rows = $pdo->query($sql)->fetchAll();

  echo json_encode(['ok' => true, 'items' => $rows]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'error' => 'Server error',
    'detail' => $e->getMessage()
  ]);
}