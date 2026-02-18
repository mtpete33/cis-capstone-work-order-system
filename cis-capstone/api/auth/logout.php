<?php

declare(strict_types=1);
require_once __DIR__ . '/../../config/session.php';

logoutUser();

header('Location: public/login.php');
exit;