<?php

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';
startSession();

if (!isset($_SESSION['user'])) {
  http_response_code(401);
  echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
}

$user =  $_SESSION['user'];
$roleID = (int)($user['roleID'] ?? 0);

if ($roleID !== 1) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Only admins can update work orders']);
  exit;
}

try {
  $input = json_decode(file_get_contents('php://input'), true);

  if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON body']);
    exit;
  }

  $workOrderID = (int)($input['workOrderID'] ?? 0);
  $currentStatusID = (int)($input['currentStatusID'] ?? 0);

  $assignedToUserID = null;
  if (array_key_exists('assignedToUserID', $input) && $input['assignedToUserID'] !== '' && $input['assignedToUserID'] !== null) {
    $assignedToUserID = (int)$input['assignedToUserID'];
  }

  if $workOrderID <= 0 {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid work order ID']);
    exit;
  }

  if ($currentStatusID <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid status ID']);
    exit;
  }

  $pdo = getPDO();

//Confirm work order exists
$checkWO = $pdo->prepare('SELECT "workOrderID" FROM work_orders WHERE "workOrderID" = :workOrderID');
$checkWO->execute(['workOrderID' => $workOrderID]);

if (!$checkWO->fetch()) {
  http_response_code(404);
  echo json_encode(['ok' => false, 'error' => 'Work order not found']);
  exit;
}

//Confirm status exists
$checkStatus = $pdo->prepare('SELECT "statusID" FROM statuses WHERE "statusID" = :statusID');
$checkStatus->execute(['statusID' => $currentStatusID]);

if (!$checkStatus->fetch()) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Invalid status ID']);
  exit;
}

// If technician selected, confirm that user is a technician
if ($assignedToUserID !== null) {
  $checkTech = $pdo->prepare('SELECT "userID" FROM users WHERE "userID" = :userID AND "roleID" = 2');
  $checkTech->execute(['userID' => $assignedToUserID]);

  if (!$checkTech->fetch()) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid technician ID']);
    exit;
  }
}

$sql = 'UPDATE work_orders SET "assignedToUserID" = :assignedToUserID, "currentStatusID" = :currentStatusID WHERE "workOrderID" = :workOrderID';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':assignedToUserID', $assignedToUserID, $assignedToUserID === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
$stmt->bindValue(':currentStatusID', $currentStatusID, PDO::PARAM_INT);
$stmt->bindValue(':workOrderID', $workOrderID, PDO::PARAM_INT);
$stmt->execute();

echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()]);
}
