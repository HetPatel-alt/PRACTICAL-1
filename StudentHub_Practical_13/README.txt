StudentHub Practical 13 — Cumulative Project (Practicals 2–13)

Folder name: StudentHub_Practical_13

FEATURES
All previous features plus JSON API endpoints and Fetch/AJAX search without full page reload.

This ZIP preserves the existing StudentHub structure and includes the prior HTML pages, shared CSS/JavaScript, registration validation, JSON datasets, PHP/MySQL registration, password hashing, login/logout, sessions and role-based access.

SETUP
1. Extract into C:\xampp\htdocs\StudentHub_Practical_13.
2. Start Apache and MySQL in XAMPP.
3. For a fresh database, import database/studenthub.sql through phpMyAdmin.
4. Confirm credentials in db.php.
5. Open http://localhost/StudentHub_Practical_13/.
6. Register a new account, then promote it to admin in phpMyAdmin → studenthub → SQL:
   UPDATE students SET role='admin' WHERE email='your-email@example.com';
7. Log out and log in again.

DATABASE WARNING
- database/studenthub.sql is for a fresh installation.
- For an existing Practical 10 database, back it up and import database/practicals11_14_migration.sql once.
- Do not import the fresh-install SQL into an existing database or run the migration twice.

PRACTICAL FILES
- manage_students.php — student CRUD, search/filter, pagination and CSV export.
- manage_events.php — event CRUD and validated poster uploads.
- api/students.php and api/events.php — JSON endpoints; api_demo.php — Fetch/AJAX search.

TESTING
Test registration/login, role restrictions, logout and each feature included in this practical using http://localhost. Do not open PHP files through file://. PHP syntax can be checked locally, but database behavior must be tested against your XAMPP MySQL instance.

This is an educational project. Back up the database before migrations or deleting records.
