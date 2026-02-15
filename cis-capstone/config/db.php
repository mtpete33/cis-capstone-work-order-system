<?php
declare(strict_types=1);

function getPDO(): PDO
{
$host = getenv('PGHOST') ?: getenv('DB_HOST') ?: 'localhost';
$port = getenv('PGPORT') ?: getenv('DB_PORT') ?: '5432';
$dbname = getenv('PGDATABASE') ?: getenv('DB_NAME') ?: 'postgres';
$user = getenv('PGUSER') ?: getenv('DB_USER') ?: 'postgres';
$pass = getenv('PGPASSWORD') ?: getenv('DB_PASS') ?: '';

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";


$pdo = new PDO($dsn, $user, $pass, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,

]);

return $pdo;
}

?>