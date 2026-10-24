StudentHub Practical 7 — Cumulative Project (Practicals 2–7)

Folder name: StudentHub_Practical_7

Practical 7 extends the existing StudentHub project. The earlier HTML/CSS/JavaScript pages, Practical 5 client-side registration validation, and Practical 6 JSON-powered lists are retained. The registration page is now register.php so it can process POST requests on the server.

Pages and files:
- index.html — home page + notices loaded from data/notices.json
- about.html — FAQs loaded from data/faqs.json
- register.php — registration form with browser-side and server-side validation
- dashboard.html — existing dashboard and modal
- events.html — original event slider + Fetch API search/filter/sort/pagination
- students.html — JSON-powered student directory
- css/style.css — shared styling, including server response messages
- js/script.js — Practical 4 interactions and theme persistence
- js/validation.js — Practical 5 client-side validation
- js/data.js — Practical 6 Fetch API data rendering
- data/events.json — 15 sample events
- data/students.json — 15 fictional student profiles
- data/faqs.json — 15 FAQs
- data/notices.json — 15 sample notices
- storage/registrations.csv — created when a valid registration is submitted
- storage/registrations.json — created when a valid registration is submitted
- storage/.htaccess — blocks direct HTTP access to stored personal form data on Apache

Run using XAMPP:
1. Install and open XAMPP.
2. Copy the complete StudentHub_Practical_7 folder into C:\xampp\htdocs\
3. Start Apache from the XAMPP Control Panel.
4. Open http://localhost/StudentHub_Practical_7/ in your browser.
5. Open Registration from the navigation.
6. Submit invalid data and verify server-side errors.
7. Submit valid data and verify success feedback.
8. Inspect the generated files in StudentHub_Practical_7/storage/.

Server-side checks:
- PHP handles POST requests.
- Inputs are trimmed and validated on the server.
- Name, email, mobile, course, year, gender, password, confirmation and terms are checked.
- Output shown in HTML is escaped with htmlspecialchars.
- CSV writes use file locking; JSON updates use a temporary file and rename.
- Stored records exclude passwords.
- Registration is saved to CSV and JSON files.

Important:
- Use Apache/XAMPP; PHP will not execute if you double-click register.php or open it through a static file URL.
- This is a classroom file-storage demonstration, not a production account system. It does not hash or store passwords, and it does not implement authentication.
- The JSON and CSV files are written during valid submissions; they are not pre-populated with fake registrations.
- CSRF protection and a webpage listing saved registrations are extensions, so they are not included in the core implementation.
