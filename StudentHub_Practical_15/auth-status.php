<?php
require_once __DIR__ . "/includes/auth.php";
auth_start_session();
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
$user = auth_user();
echo json_encode([
    "loggedIn" => $user !== null,
    "role" => $user["role"] ?? null,
    "name" => $user["name"] ?? null
]);
