StudentHub Practical 8 — Complete Cumulative Project (Practicals 2–8)

Folder name: StudentHub_Practical_8

Practical 8 builds on the Practical 7 project and preserves earlier StudentHub pages and JavaScript features. The registration form now stores student and registration data in MySQL using PHP PDO and prepared statements.

Main files:
- index.html — Home + Practical 6 JSON notices
- about.html — JSON-powered FAQ search/filter
- register.php — Practical 5 frontend validation + PHP server validation + MySQL inserts
- dashboard.html — existing dashboard and modal
- events.html — existing event slider + Fetch API event search/filter/sort/pagination
- students.html — JSON-powered student directory
- database.php — PDO queries showing students, events, and registrations
- db.php — PDO connection configuration
- database/studenthub.sql — database schema and sample records
- css/style.css — shared styling
- js/script.js — previous practical interactions and theme persistence
- js/validation.js — client-side registration validation; valid forms submit to PHP
- js/data.js — Practical 6 Fetch API data rendering
- data/*.json — sample JSON datasets retained from Practical 6
- storage/.htaccess — blocks direct HTTP access to the old file-storage folder

Database design:
- students: student details; email is unique
- events: event title, category, date, location, description and organizer
- registrations: registration record linked to students through a foreign key
- No passwords are stored in the database.

Run with XAMPP:
1. Extract StudentHub_Practical_8.zip.
2. Copy the StudentHub_Practical_8 folder to C:\xampp\htdocs\.
3. Start Apache and MySQL in the XAMPP Control Panel.
4. Open http://localhost/phpmyadmin/.
5. Choose Import and import StudentHub_Practical_8/database/studenthub.sql.
   The SQL creates the studenthub database and the students, events, and registrations tables.
6. Check db.php. Default XAMPP settings are username root and blank password. Change these values if your MySQL setup differs.
7. Open http://localhost/StudentHub_Practical_8/.
8. Submit the registration form at register.php.
9. Open http://localhost/StudentHub_Practical_8/database.php to view records from MySQL.

Testing:
- Submit invalid form data and confirm server-side errors are displayed.
- Submit valid data and check students and registrations in phpMyAdmin.
- Open database.php and verify the student, event and registration tables display records.
- Check the browser console if Practical 6 JSON data does not load.

Important:
- Do not open PHP files directly using file://. Use Apache through localhost.
- The project uses prepared statements and escaped output for database operations.
- Existing Practical 6 JSON files remain for the Fetch API tasks. Practical 8 database tables are managed through MySQL.
- This is a college practical, not a production deployment. Use a restricted database account and add CSRF protection before using real user data.
