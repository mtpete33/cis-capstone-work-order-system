<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/session.php';

$user = currentUser();

echo json_encode([
                  'ok' => true,
                  'loggedIn' => $user !== null,
                  'user' => $user
]);