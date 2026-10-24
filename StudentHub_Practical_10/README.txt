StudentHub Practical 10 — Complete Cumulative Project (Practicals 2–10)

Folder name: StudentHub_Practical_10

Practical 10 adds secure login/logout, PHP sessions, password_verify(), role-based access control, 15-minute inactivity timeout, last-login timestamps, and an optional remember-me token. Existing StudentHub pages, Practical 5 validation, Practical 6 JSON data, and Practical 8/9 MySQL functionality are retained.

Files added:
- login.php — login form, password verification, session regeneration, remember-me token
- logout.php — clears the login session and invalidates the remember-me token
- dashboard.php — student-only protected dashboard
- admin.php — admin-only dashboard and last-login list
- includes/auth.php — session setup and access-control helpers
- database/practical10_migration.sql — schema migration for an existing Practical 9 database

Database setup:
A) Fresh database:
1. Copy StudentHub_Practical_10 into C:\xampp\htdocs\.
2. Start Apache and MySQL.
3. Import database/studenthub.sql in phpMyAdmin.
4. Verify db.php credentials.

B) Existing Practical 9 database:
1. Select studenthub in phpMyAdmin.
2. Import database/practical10_migration.sql.
3. Do not run the migration twice; it adds columns that should exist only once.

Set up an administrator:
1. Register a normal user using register.php.
2. In phpMyAdmin, open the SQL tab for studenthub and run this, replacing the email with the account you registered:
   UPDATE students SET role = 'admin' WHERE email = 'your-admin-email@example.com';
3. Log out and log in again with that account. It should open admin.php.
4. Other accounts default to role student and are redirected to dashboard.php.

Run:
- Open http://localhost/StudentHub_Practical_10/
- Registration: http://localhost/StudentHub_Practical_10/register.php
- Login: http://localhost/StudentHub_Practical_10/login.php
- Student dashboard: http://localhost/StudentHub_Practical_10/dashboard.php
- Admin dashboard: http://localhost/StudentHub_Practical_10/admin.php

Security implementation:
- password_verify() checks password hashes.
- Session ID is regenerated after login.
- HttpOnly and SameSite cookie settings are used; Secure is enabled under HTTPS.
- Protected pages enforce login and role on the server.
- Idle sessions expire after 15 minutes on login requests; protected dashboards check authentication.
- Remember-me uses a random token; only SHA-256 hash and expiry are stored in MySQL. The raw token is stored in an HttpOnly cookie.
- Logout invalidates the saved token and destroys the session.
- Last login timestamp is updated after successful login.
- Queries use PDO prepared statements; displayed values are escaped.

Testing:
1. Register a user.
2. Set one user's role to admin using the SQL above.
3. Test correct and incorrect passwords.
4. Verify student and admin redirection.
5. Try opening admin.php while logged in as student; it should return HTTP 403.
6. Log out and try dashboard.php; it should redirect to login.php.
7. Select Remember me, close/reopen browser, and check the account remains signed in before token expiry.
8. Leave the session idle for 15 minutes and make a request to verify timeout handling.

Notes:
- Existing accounts from Practical 9 have no password_hash value unless they were registered after Practical 9 code was installed. To log in, use an account created through register.php after the updated schema/code is installed.
- This is an educational project. Remember-me is optional and can be disabled by leaving the checkbox unchecked.


Practical 10 login/navigation fix:
- Registration now inserts the password_hash value created by password_hash(). Without this, new accounts could not authenticate.
- Existing pages call auth-status.php and update navigation to show Login/Registration when logged out and Logout when logged in.
- Login is role-based: students go to dashboard.php; admins go to admin.php.
- After replacing the project folder, hard refresh the browser (Ctrl+F5).
- Existing users created before this fix may have an empty password_hash and cannot log in. Register a new account, then promote it to admin with:
  UPDATE students SET role='admin' WHERE email='your-email@example.com';
