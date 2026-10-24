<?php
require_once __DIR__ . "/db.php";

$errors = [];
$successMessage = "";
$old = [
    "full_name" => "", "email" => "", "mobile" => "", "student_id" => "",
    "course" => "", "year" => "", "gender" => ""
];

function clean_input($value) {
    return trim((string)$value);
}
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($old as $key => $value) {
        $old[$key] = clean_input($_POST[$key] ?? "");
    }
    $password = (string)($_POST["password"] ?? "");
    $confirmPassword = (string)($_POST["confirm_password"] ?? "");

    if (!preg_match("/^[A-Za-z][A-Za-z .'-]{1,49}$/", $old["full_name"])) $errors["full_name"] = "Enter a valid name.";
    if (!filter_var($old["email"], FILTER_VALIDATE_EMAIL)) $errors["email"] = "Enter a valid email address.";
    if (!preg_match("/^(?:\+91[- ]?)?[6-9][0-9]{9}$/", $old["mobile"])) $errors["mobile"] = "Enter a valid 10-digit Indian mobile number.";
    if (!in_array($old["course"], ["Information Technology","Computer Science","Computer Engineering","Other"], true)) $errors["course"] = "Select a valid course.";
    if (!in_array($old["year"], ["1","2","3","4"], true)) $errors["year"] = "Select a valid year.";
    if (!in_array($old["gender"], ["Female","Male","Other","Prefer not to say"], true)) $errors["gender"] = "Select a gender option.";
    if (strlen($password) < 8 || !preg_match("/[A-Z]/",$password) || !preg_match("/[a-z]/",$password) || !preg_match("/[0-9]/",$password) || !preg_match("/[^A-Za-z0-9]/",$password)) $errors["password"] = "Use at least 8 characters with uppercase, lowercase, a number, and a special character.";
    if ($confirmPassword === "" || $password !== $confirmPassword) $errors["confirm_password"] = "Passwords do not match.";
    if (!isset($_POST["terms"])) $errors["terms"] = "You must accept the terms and conditions.";

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $studentSql = "INSERT INTO students (full_name, email, mobile, course, study_year, gender)
                           VALUES (:full_name, :email, :mobile, :course, :study_year, :gender)
                           ON DUPLICATE KEY UPDATE student_id = LAST_INSERT_ID(student_id),
                           full_name = VALUES(full_name), mobile = VALUES(mobile),
                           course = VALUES(course), study_year = VALUES(study_year), gender = VALUES(gender)";
            $studentStmt = $pdo->prepare($studentSql);
            $studentStmt->execute([
                ":full_name" => $old["full_name"],
                ":email" => $old["email"],
                ":mobile" => $old["mobile"],
                ":course" => $old["course"],
                ":study_year" => (int)$old["year"],
                ":gender" => $old["gender"]
            ]);
            $studentId = (int)$pdo->lastInsertId();

            $registrationStmt = $pdo->prepare(
                "INSERT INTO registrations (student_id) VALUES (:student_id)
                 ON DUPLICATE KEY UPDATE registration_date = CURRENT_TIMESTAMP"
            );
            $registrationStmt->execute([":student_id" => $studentId]);
            $pdo->commit();
            $successMessage = "Registration saved successfully to the StudentHub MySQL database.";
            foreach ($old as $key => $value) $old[$key] = "";
        } catch (PDOException $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("StudentHub registration failed: " . $exception->getMessage());
            $errors["general"] = "Registration could not be saved. Check the database connection and whether the email already exists.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StudentHub | Registration</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/script.js" defer></script>
  <script src="js/validation.js" defer></script>
</head>
<body>
  <header class="site-header"><div class="header-inner"><a class="site-title" href="index.html">StudentHub</a>
    <button class="menu-toggle" id="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Open navigation menu"><span></span><span></span><span></span></button>
    <nav id="main-nav" class="main-nav" aria-label="Main navigation">
      <a href="index.html" >Home</a>
      <a href="about.html" >About</a>
      <a href="register.php" aria-current="page">Registration</a>
      <a href="dashboard.html" >Dashboard</a>
      <a href="events.html" >Events</a>
      <a href="students.html">Students</a>
      <a href="database.php">Database</a>
    </nav>
    <button class="theme-toggle" id="theme-toggle" type="button" aria-pressed="false">Dark mode</button></div></header>
  <main class="page-container">
    
<section class="intro-panel">
  <p class="eyebrow">Student account</p><h1>Student Registration</h1>
  <p>Complete the form below. This practical extends the existing StudentHub registration page with accessible frontend validation.</p>
</section>
<?php if ($successMessage !== ""): ?>
  <div class="server-message server-success" role="status"><?= e($successMessage) ?></div>
<?php endif; ?>
<?php if (isset($errors["general"])): ?>
  <div class="server-message server-error" role="alert"><?= e($errors["general"]) ?></div>
<?php endif; ?>
<section class="section-block form-panel">
  <h2>Student Registration Form</h2>
  <form id="registration-form" action="register.php" method="post" novalidate>
    <div class="form-grid">
      <div class="form-field"><label for="full-name">Full name *</label><input id="full-name" name="full_name" type="text" autocomplete="name" required aria-describedby="full-name-error" value="<?= e($old["full_name"]) ?>"><p class="field-error" id="full-name-error" aria-live="polite"><?= e($errors["full_name"] ?? "") ?></p></div>
      <div class="form-field"><label for="email">Email address *</label><input id="email" name="email" type="email" autocomplete="email" required aria-describedby="email-error" value="<?= e($old["email"]) ?>"><p class="field-error" id="email-error" aria-live="polite"><?= e($errors["email"] ?? "") ?></p></div>
      <div class="form-field"><label for="mobile">Mobile number *</label><input id="mobile" name="mobile" type="tel" inputmode="numeric" autocomplete="tel" placeholder="10-digit mobile number" required aria-describedby="mobile-error" value="<?= e($old["mobile"]) ?>"><p class="field-error" id="mobile-error" aria-live="polite"><?= e($errors["mobile"] ?? "") ?></p></div>
      <div class="form-field"><label for="student-id">Student ID</label><input id="student-id" name="student_id" type="text" autocomplete="off" value="<?= e($old["student_id"]) ?>"></div>
      <div class="form-field"><label for="course">Course *</label><select id="course" name="course" required aria-describedby="course-error"><option value="">Select course</option><option value="Information Technology" <?= $old["course"] === "Information Technology" ? "selected" : "" ?>>Information Technology</option><option value="Computer Science" <?= $old["course"] === "Computer Science" ? "selected" : "" ?>>Computer Science</option><option value="Computer Engineering" <?= $old["course"] === "Computer Engineering" ? "selected" : "" ?>>Computer Engineering</option><option value="Other" <?= $old["course"] === "Other" ? "selected" : "" ?>>Other</option></select><p class="field-error" id="course-error" aria-live="polite"><?= e($errors["course"] ?? "") ?></p></div>
      <div class="form-field"><label for="year">Year of study *</label><select id="year" name="year" required aria-describedby="year-error"><option value="">Select year</option><option value="1" <?= $old["year"] === "1" ? "selected" : "" ?>>First Year</option><option value="2" <?= $old["year"] === "2" ? "selected" : "" ?>>Second Year</option><option value="3" <?= $old["year"] === "3" ? "selected" : "" ?>>Third Year</option><option value="4" <?= $old["year"] === "4" ? "selected" : "" ?>>Fourth Year</option></select><p class="field-error" id="year-error" aria-live="polite"><?= e($errors["year"] ?? "") ?></p></div>
      <fieldset class="form-field gender-field"><legend>Gender *</legend><div class="radio-options"><label><input type="radio" name="gender" value="Female" <?= $old["gender"] === "Female" ? "checked" : "" ?>> Female</label><label><input type="radio" name="gender" value="Male" <?= $old["gender"] === "Male" ? "checked" : "" ?>> Male</label><label><input type="radio" name="gender" value="Other" <?= $old["gender"] === "Other" ? "checked" : "" ?>> Other</label><label><input type="radio" name="gender" value="Prefer not to say" <?= $old["gender"] === "Prefer not to say" ? "checked" : "" ?>> Prefer not to say</label></div><p class="field-error" id="gender-error" aria-live="polite"><?= e($errors["gender"] ?? "") ?></p></fieldset>
      <div class="form-field"><label for="password">Password *</label><input id="password" name="password" type="password" autocomplete="new-password" required aria-describedby="password-help password-error"><p class="field-help" id="password-help">At least 8 characters, including uppercase, lowercase, a number, and a special character.</p><p class="field-error" id="password-error" aria-live="polite"><?= e($errors["password"] ?? "") ?></p><div class="strength-track" aria-hidden="true"><div id="strength-bar"></div></div><p id="strength-text" class="field-help" aria-live="polite">Password strength: not entered</p></div>
      <div class="form-field"><label for="confirm-password">Confirm password *</label><input id="confirm-password" name="confirm_password" type="password" autocomplete="new-password" required aria-describedby="confirm-password-error"><p class="field-error" id="confirm-password-error" aria-live="polite"><?= e($errors["confirm_password"] ?? "") ?></p></div>
    </div>
    <div class="terms-row"><input id="terms" name="terms" type="checkbox" required><label for="terms">I accept the terms and conditions. *</label></div>
    <p class="field-error" id="terms-error" aria-live="polite"><?= e($errors["terms"] ?? "") ?></p>
    <div id="form-message" class="form-message" role="status" aria-live="polite"></div>
    <button type="submit">Create Account</button>
    <button type="reset" class="secondary-button">Clear Form</button>
  </form>
</section>


  </main>
  <div class="notification" id="notification" role="status" aria-live="polite" hidden>
    <p id="notification-message">You are all caught up.</p>
    <button id="close-notification" type="button" aria-label="Dismiss notification">×</button>
  </div>
  <div class="modal-backdrop" id="modal-backdrop" hidden>
    <section class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
      <button class="modal-close" id="close-modal" type="button" aria-label="Close dialog">×</button>
      <p class="eyebrow">StudentHub</p>
      <h2 id="modal-title">About this demo</h2>
      <p id="modal-description">This page demonstrates JavaScript DOM selection, event handling, class manipulation, localStorage, and interface interaction.</p>
    </section>
  </div>
  <footer class="site-footer"><p>StudentHub Practicals 2–5 · Registration Validation</p></footer>
</body>
</html>
