<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    // Always show something if routing breaks:
    function debug_out($data) {
      header('Content-Type: text/plain; charset=utf-8');
      echo $data;
      exit;
    }

    $uri  = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH);

    // If parse_url fails, fail loudly
    if ($path === null) {
      debug_out("parse_url failed\nURI: " . $uri);
    }

    // Normalize empty path to "/"
    if ($path === '') {
      $path = '/';
    }

// Serve static assets (css/js/images) from /public only
$staticFile = __DIR__ . '/public' . $path;
if (
    $path !== '/' &&
    file_exists($staticFile) &&
    !is_dir($staticFile)
) {
    return false;
}



// API routing
if (str_starts_with($path, '/api/')) {
  $apiFile = __DIR__ . $path;

  if (file_exists($apiFile) && !is_dir($apiFile)) {

    require $apiFile;
    exit;
  }

  http_response_code(404);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'ok' => false,
    'error' => 'API endpoint not found',
    'path' => $path,
    'expectedFile' => $apiFile
  ]);
  exit;
}

// Page routing
if ($path === '/login' || $path === '/login.php') {
  require __DIR__ . '/public/login.php';
  exit;
}

    // Default route -> dashboard
    require __DIR__ . '/public/index.php';
