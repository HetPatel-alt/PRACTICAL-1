<?php
require_once __DIR__ . "/db.php";
$students = [];
$events = [];
$registrations = [];
$errorMessage = "";
try {
    $studentStmt = $pdo->prepare("SELECT student_id, full_name, email, course, study_year FROM students ORDER BY full_name LIMIT :limit");
    $studentStmt->bindValue(":limit", 10, PDO::PARAM_INT);
    $studentStmt->execute();
    $students = $studentStmt->fetchAll();

    $eventStmt = $pdo->prepare("SELECT event_id, title, category, event_date, location FROM events ORDER BY event_date LIMIT :limit");
    $eventStmt->bindValue(":limit", 10, PDO::PARAM_INT);
    $eventStmt->execute();
    $events = $eventStmt->fetchAll();

    $registrationStmt = $pdo->prepare(
        "SELECT r.registration_id, s.full_name, s.email, r.registration_date
         FROM registrations r JOIN students s ON r.student_id = s.student_id
         ORDER BY r.registration_date DESC LIMIT :limit"
    );
    $registrationStmt->bindValue(":limit", 10, PDO::PARAM_INT);
    $registrationStmt->execute();
    $registrations = $registrationStmt->fetchAll();
} catch (PDOException $exception) {
    error_log("StudentHub database page query failed: " . $exception->getMessage());
    $errorMessage = "Could not load database records. Verify that database/studenthub.sql has been imported.";
}
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub | Database Overview</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js" defer></script>
</head>
<body>
<header class="site-header"><div class="header-inner">
<a class="site-title" href="index.html">StudentHub</a>
<button class="menu-toggle" id="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Open navigation menu"><span></span><span></span><span></span></button>
<nav id="main-nav" class="main-nav" aria-label="Main navigation">
<a href="index.html">Home</a><a href="about.html">About</a><a href="register.php">Registration</a><a href="login.php">Login</a><a href="dashboard.php">Dashboard</a><a href="events.html">Events</a><a href="students.html">Students</a><a href="database.php" aria-current="page">Database</a>
</nav><button class="theme-toggle" id="theme-toggle" type="button" aria-pressed="false">Dark mode</button>
</div></header>
<main class="page-container">
<section class="intro-panel"><p class="eyebrow">Practical 8 · PDO + MySQL</p><h1>Database Overview</h1><p>Records below are retrieved from MySQL using PDO prepared statements.</p></section>
<?php if ($errorMessage !== ""): ?><p class="server-message server-error" role="alert"><?= e($errorMessage) ?></p><?php endif; ?>
<section class="section-block"><h2>Students</h2><div class="table-wrap"><table><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th></tr></thead><tbody>
<?php foreach ($students as $row): ?><tr><td><?= e($row["student_id"]) ?></td><td><?= e($row["full_name"]) ?></td><td><?= e($row["email"]) ?></td><td><?= e($row["course"]) ?></td><td><?= e($row["study_year"]) ?></td></tr><?php endforeach; ?>
<?php if (!$students): ?><tr><td colspan="5">No student records found.</td></tr><?php endif; ?></tbody></table></div></section>
<section class="section-block"><h2>Events</h2><div class="table-wrap"><table><thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Location</th></tr></thead><tbody>
<?php foreach ($events as $row): ?><tr><td><?= e($row["event_id"]) ?></td><td><?= e($row["title"]) ?></td><td><?= e($row["category"]) ?></td><td><?= e($row["event_date"]) ?></td><td><?= e($row["location"]) ?></td></tr><?php endforeach; ?>
<?php if (!$events): ?><tr><td colspan="5">No event records found.</td></tr><?php endif; ?></tbody></table></div></section>
<section class="section-block"><h2>Recent Registrations</h2><div class="table-wrap"><table><thead><tr><th>Registration ID</th><th>Student</th><th>Email</th><th>Registered at</th></tr></thead><tbody>
<?php foreach ($registrations as $row): ?><tr><td><?= e($row["registration_id"]) ?></td><td><?= e($row["full_name"]) ?></td><td><?= e($row["email"]) ?></td><td><?= e($row["registration_date"]) ?></td></tr><?php endforeach; ?>
<?php if (!$registrations): ?><tr><td colspan="4">No registrations found.</td></tr><?php endif; ?></tbody></table></div></section>
</main>
<div class="notification" id="notification" role="status" aria-live="polite" hidden><p id="notification-message">You are all caught up.</p><button id="close-notification" type="button" aria-label="Dismiss notification">×</button></div>
<footer class="site-footer"><p>StudentHub · Practicals 2–10 cumulative project</p></footer>
</body></html>