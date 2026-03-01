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
  $totalStmt = $pdo->query('SELECT COUNT(*) FROM work_orders');
  $total = (int)$totalStmt->fetchColumn();

  //Open work orders
  $openStmt = $pdo->prepare(
    'SELECT COUNT(*) FROM work_orders wo
     JOIN statuses s ON s."statusID" = wo."currentStatusID"
     WHERE lower(s."statusName") = :status'
  );
  $openStmt->execute(['status' => 'open']);
  $open = (int)$openStmt->fetchColumn();

  echo json_encode(['ok' => true, 'total' => $total, 'open' => $open]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
                     'ok' => false,
                     'error' => 'Server error',
                     'detail' => $e->getMessage()
                     ]);
  }

