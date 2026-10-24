<?php
require_once __DIR__ . "/includes/auth.php";
auth_start_session();
$user = require_role("student");
$now = time();
$_SESSION["last_activity"] = $now;
function e_dash($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"); }
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub | Student Dashboard</title><link rel="stylesheet" href="css/style.css"><script src="js/script.js" defer></script>
</head><body>
<header class="site-header"><div class="header-inner">
<a class="site-title" href="index.html">StudentHub</a>
<nav id="main-nav" class="main-nav" aria-label="Main navigation"><a href="index.html">Home</a><a href="about.html">About</a><a href="events.html">Events</a><a href="students.html">Students</a><a href="dashboard.php" aria-current="page">Dashboard</a></nav>
<a class="secondary-button" href="logout.php">Log Out</a>
</div></header>
<main class="page-container"><section class="intro-panel"><p class="eyebrow">Student account</p><h1>Welcome, <?= e_dash($user["name"]) ?></h1><p>You are logged in with the student role.</p></section>
<section class="section-block"><h2>Your Dashboard</h2><p>This page is protected. You must log in as a student to view it.</p><p><strong>Role:</strong> <?= e_dash($user["role"]) ?></p></section></main>
<footer class="site-footer"><p>StudentHub · Practicals 2–10 cumulative project</p></footer></body></html>