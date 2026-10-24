<?php
require_once __DIR__ . "/includes/auth.php";
auth_start_session();
$user = require_role("admin");
require_once __DIR__ . "/db.php";
$stmt = $pdo->prepare("SELECT student_id, full_name, email, role, last_login_at FROM students ORDER BY full_name");
$stmt->execute();
$users = $stmt->fetchAll();
function e_admin($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"); }
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub | Admin Dashboard</title><link rel="stylesheet" href="css/style.css"><script src="js/script.js" defer></script>
</head><body>
<header class="site-header"><div class="header-inner"><a class="site-title" href="index.html">StudentHub</a><nav class="main-nav" aria-label="Main navigation"><a href="admin.php" aria-current="page">Admin Dashboard</a></nav><a class="secondary-button" href="logout.php">Log Out</a></div></header>
<main class="page-container"><section class="intro-panel"><p class="eyebrow">Administrator</p><h1>Admin Dashboard</h1><p>Signed in as <?= e_admin($user["name"]) ?>.</p></section>
<section class="section-block"><h2>Registered Users</h2><div class="table-wrap"><table><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Last Login</th></tr></thead><tbody>
<?php foreach ($users as $row): ?><tr><td><?= e_admin($row["student_id"]) ?></td><td><?= e_admin($row["full_name"]) ?></td><td><?= e_admin($row["email"]) ?></td><td><?= e_admin($row["role"]) ?></td><td><?= e_admin($row["last_login_at"] ?? "Never") ?></td></tr><?php endforeach; ?>
<?php if (!$users): ?><tr><td colspan="5">No users found.</td></tr><?php endif; ?></tbody></table></div></section></main>
<footer class="site-footer"><p>StudentHub · Practicals 2–10 cumulative project</p></footer></body></html>