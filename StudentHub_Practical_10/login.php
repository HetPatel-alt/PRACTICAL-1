<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/includes/auth.php";

auth_start_session();

$idleTimeout = 900;
if (isset($_SESSION["last_activity"]) && time() - (int)$_SESSION["last_activity"] > $idleTimeout) {
    $_SESSION = [];
    session_destroy();
    auth_start_session();
    $timeoutMessage = "Your session expired after 15 minutes of inactivity. Please log in again.";
} else {
    $timeoutMessage = "";
}
$_SESSION["last_activity"] = time();

if (auth_user() !== null) {
    header("Location: " . ($_SESSION["user_role"] === "admin" ? "admin.php" : "dashboard.php"));
    exit;
}

if (!empty($_COOKIE["studenthub_remember"])) {
    $parts = explode(":", (string)$_COOKIE["studenthub_remember"], 2);
    if (count($parts) === 2 && ctype_digit($parts[0]) && preg_match('/^[a-f0-9]{64}$/', $parts[1])) {
        try {
            $stmt = $pdo->prepare("SELECT student_id, full_name, role, remember_token_hash, remember_token_expires FROM students WHERE student_id = :id LIMIT 1");
            $stmt->execute([":id" => (int)$parts[0]]);
            $rememberUser = $stmt->fetch();
            if ($rememberUser && $rememberUser["remember_token_hash"] &&
                hash_equals($rememberUser["remember_token_hash"], hash("sha256", $parts[1])) &&
                $rememberUser["remember_token_expires"] &&
                strtotime($rememberUser["remember_token_expires"]) > time()) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = (int)$rememberUser["student_id"];
                $_SESSION["user_name"] = $rememberUser["full_name"];
                $_SESSION["user_role"] = $rememberUser["role"];
                $_SESSION["last_activity"] = time();
                header("Location: " . ($rememberUser["role"] === "admin" ? "admin.php" : "dashboard.php"));
                exit;
            }
        } catch (PDOException $exception) {
            error_log("Remember-me lookup failed: " . $exception->getMessage());
        }
    }
}

$errorMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim((string)($_POST["email"] ?? ""));
    $password = (string)($_POST["password"] ?? "");
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
        $errorMessage = "Enter a valid email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT student_id, full_name, email, password_hash, role FROM students WHERE email = :email LIMIT 1");
            $stmt->execute([":email" => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password_hash"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = (int)$user["student_id"];
                $_SESSION["user_name"] = $user["full_name"];
                $_SESSION["user_role"] = $user["role"];
                $_SESSION["last_activity"] = time();

                $lastLogin = $pdo->prepare("UPDATE students SET last_login_at = NOW() WHERE student_id = :id");
                $lastLogin->execute([":id" => (int)$user["student_id"]]);

                if (!empty($_POST["remember_me"])) {
                    $rawToken = bin2hex(random_bytes(32));
                    $tokenHash = hash("sha256", $rawToken);
                    $expires = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60));
                    $rememberStmt = $pdo->prepare("UPDATE students SET remember_token_hash = :token_hash, remember_token_expires = :expires WHERE student_id = :id");
                    $rememberStmt->execute([
                        ":token_hash" => $tokenHash,
                        ":expires" => $expires,
                        ":id" => (int)$user["student_id"]
                    ]);
                    setcookie("studenthub_remember", $user["student_id"] . ":" . $rawToken, [
                        "expires" => time() + (30 * 24 * 60 * 60),
                        "path" => "/",
                        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
                        "httponly" => true,
                        "samesite" => "Lax"
                    ]);
                }

                header("Location: " . ($user["role"] === "admin" ? "admin.php" : "dashboard.php"));
                exit;
            }
            $errorMessage = "Invalid email or password.";
        } catch (PDOException $exception) {
            error_log("Login query failed: " . $exception->getMessage());
            $errorMessage = "Login is temporarily unavailable. Check the database connection.";
        }
    }
}
function e_login($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub | Login</title><link rel="stylesheet" href="css/style.css">
<script src="js/script.js" defer></script>
</head>
<body>
<header class="site-header"><div class="header-inner">
<a class="site-title" href="index.html">StudentHub</a>
<button class="menu-toggle" id="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Open navigation menu"><span></span><span></span><span></span></button>
<nav id="main-nav" class="main-nav" aria-label="Main navigation">
<a href="index.html">Home</a><a href="about.html">About</a><a href="register.php">Registration</a><a href="login.php" aria-current="page">Login</a><a href="dashboard.php">Dashboard</a><a href="events.html">Events</a><a href="students.html">Students</a>
</nav><button class="theme-toggle" id="theme-toggle" type="button" aria-pressed="false">Dark mode</button>
</div></header>
<main class="page-container">
<section class="intro-panel"><p class="eyebrow">Secure access</p><h1>StudentHub Login</h1><p>Sign in with the email and password used during registration.</p></section>
<?php if ($timeoutMessage !== ""): ?><p class="server-message server-error" role="status"><?= e_login($timeoutMessage) ?></p><?php endif; ?>
<?php if ($errorMessage !== ""): ?><p class="server-message server-error" role="alert"><?= e_login($errorMessage) ?></p><?php endif; ?>
<section class="section-block form-panel">
<h2>Login Form</h2>
<form method="post" action="login.php">
<div class="form-grid">
<div class="field"><label for="login-email">Email address</label><input id="login-email" name="email" type="email" autocomplete="username" required></div>
<div class="field"><label for="login-password">Password</label><input id="login-password" name="password" type="password" autocomplete="current-password" required></div>
</div>
<div class="field checkbox-field"><input id="remember-me" name="remember_me" type="checkbox" value="1"><label for="remember-me">Remember me for 30 days</label></div>
<button type="submit" class="primary-button">Log In</button>
</form>
</section>
</main>
<footer class="site-footer"><p>StudentHub · Practicals 2–10 cumulative project</p></footer>
</body></html>