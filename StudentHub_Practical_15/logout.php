<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/includes/auth.php";
auth_start_session();

if (!empty($_SESSION["user_id"])) {
    $stmt = $pdo->prepare("UPDATE students SET remember_token_hash = NULL, remember_token_expires = NULL WHERE student_id = :id");
    $stmt->execute([":id" => (int)$_SESSION["user_id"]]);
}
clear_remember_cookie();
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), "", [
        "expires" => time() - 3600,
        "path" => $params["path"] ?: "/",
        "domain" => $params["domain"] ?? "",
        "secure" => (bool)($params["secure"] ?? false),
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}
session_destroy();
header("Location: login.php?logged_out=1");
exit;
