<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {
$pdo = getPDO();
$pdo->query('SELECT 1');

echo json_encode([
'ok' => true,
'db' => 'connected',

]);
} catch (Throwable $e) {
http_response_code(500);

echo json_encode([
'ok' => false,
'db' => 'error',
'error' => $e->getMessage(),
]);

}