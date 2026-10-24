StudentHub Practical 9 — Complete Cumulative Project (Practicals 2–9)

Folder name: StudentHub_Practical_9

Practical 9 extends Practical 8. Existing StudentHub pages, JSON-powered lists, MySQL schema, PDO connection, and prepared statements are retained.

Practical 9 changes:
- Secure account registration is handled by register.php using POST.
- Server-side validation checks name, email, mobile, course, year, gender, password strength, password confirmation, terms acceptance, and CSRF token.
- Duplicate email is checked before insert and also enforced by the unique email constraint.
- Passwords are hashed with password_hash() and stored only in students.password_hash. Plain-text passwords are never stored.
- Student and registration records are inserted in a database transaction using PDO prepared statements.
- Success and validation errors are displayed on the registration page.
- A registration audit entry is appended to storage/registration_audit.log without recording the password or password hash.

Important database update:
Practical 9 adds students.password_hash. If you already imported the Practical 8 database, run this SQL in phpMyAdmin before using registration:

ALTER TABLE students ADD COLUMN password_hash VARCHAR(255) NOT NULL DEFAULT '';

For a fresh setup, import database/studenthub.sql instead; it creates the updated schema and sample records.

Run with XAMPP:
1. Copy StudentHub_Practical_9 to C:\xampp\htdocs\.
2. Start Apache and MySQL.
3. For a fresh install, import database/studenthub.sql using phpMyAdmin.
4. For an existing Practical 8 database, run the ALTER TABLE statement above. Do not import the entire seed SQL over an existing database unless you understand the existing data.
5. Check db.php for the correct MySQL username/password.
6. Open http://localhost/StudentHub_Practical_9/.
7. Submit a valid registration and verify students.password_hash is populated and a matching row exists in registrations.
8. Try the same email again; it should be rejected as a duplicate.

Key files:
- register.php — secure POST handler and registration form
- db.php — PDO database connection
- database/studenthub.sql — updated schema and sample data
- database.php — database record overview (does not display password hashes)
- storage/registration_audit.log — created after successful registration
- js/validation.js — retained frontend validation; invalid forms are blocked and valid forms submit to PHP.

Security notes:
- Password hashes are never displayed in database.php or audit logs.
- CSRF token is checked server-side.
- SQL uses prepared statements.
- HTML output is escaped.
- This is an educational project; production deployment needs additional controls such as rate limiting and HTTPS.
