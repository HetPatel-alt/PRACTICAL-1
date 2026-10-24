<?php
function auth_start_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "secure" => $secure,
        "httponly" => true,
        "samesite" => "Lax"
    ]);
    ini_set("session.use_strict_mode", "1");
    session_start();
}

function auth_user(): ?array {
    if (!isset($_SESSION["user_id"], $_SESSION["user_role"], $_SESSION["user_name"])) {
        return null;
    }
    return [
        "id" => (int)$_SESSION["user_id"],
        "role" => (string)$_SESSION["user_role"],
        "name" => (string)$_SESSION["user_name"]
    ];
}

function require_login(): array {
    $user = auth_user();
    if ($user === null) {
        header("Location: login.php");
        exit;
    }
    return $user;
}

function require_role(string $role): array {
    $user = require_login();
    if ($user["role"] !== $role) {
        http_response_code(403);
        exit("Access denied: this page is not available for your role.");
    }
    return $user;
}

function clear_remember_cookie(): void {
    setcookie("studenthub_remember", "", [
        "expires" => time() - 3600,
        "path" => "/",
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}
