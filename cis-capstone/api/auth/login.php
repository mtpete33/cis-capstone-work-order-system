<?php

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
  exit;
}

$raw = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);

$email = strtolower(trim((string)($data['email'] ?? '')));
$password = (string)($data['password'] ?? '');

$fields = [];
if ($email === '') $fields['email'] = 'Email is required';
if ($password === '') $fields['password'] = 'Password is required';

if ($fields) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Validation error', 'fields' => $fields]);
  exit;
}

try {
  $pdo = getPDO();
   $stmt = $pdo->prepare(
    'SELECT "userID", "userName", "email", "pwHash", "roleID"
     FROM users 
     WHERE lower(email) = :email 
     LIMIT 1'
   );
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch();

  if (!$user || !password_verify($password, $user['pwHash'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Invalid credentials']);
    exit;
  }

  startSession();
  $_SESSION['user'] = [
    'userID' => (int)$user['userID'],
    'userName' => $user['userName'],
    'email' => $user['email'],
  ];
  echo json_encode(['ok' => true, 'user' => $_SESSION['user']]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()]);
}