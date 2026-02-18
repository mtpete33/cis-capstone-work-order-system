<?php

declare(strict_types=1);

function startSession(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
      ]);
      session_start();
    }
  }

function currentUser(): ?array
  {
    startSession();
    return $_SESSION['user'] ?? null;
  }

function isLoggedIn(): bool
  {
    return currentUser() !== null;

  }

function requireLogin(): void
  {
    if (!isLoggedIn()) {
      header('Location: /login');
      exit;
    }
  }

function logoutUser(): void
  {
    startSession();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params['path'] ?? '/', $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
    }
    session_destroy();
  }