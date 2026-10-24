StudentHub Practical 6 — Cumulative Project (Practicals 2–6)

Practical 6 is integrated into the existing Practical 4/5 StudentHub website. Existing pages and interactions are retained; new JSON-backed content is added to the current site.

Main pages:
- index.html — existing home page + JSON-powered notices
- about.html — existing About page + FAQs loaded from JSON with search/category filtering
- register.html — existing registration page with Practical 5 frontend validation
- dashboard.html — existing dashboard and modal interaction
- events.html — existing Practical 4 event slider + JSON-powered events with search, category filter, sorting and pagination
- students.html — new student directory with search, course/year filters, sorting and pagination
- css/style.css — shared styles for all pages
- js/script.js — Practical 4 shared interactions and theme persistence
- js/validation.js — Practical 5 registration validation
- js/data.js — Practical 6 Fetch API, JSON parsing, rendering, search/filter/sort/pagination, loading/error states
- data/events.json — 15 sample events
- data/students.json — 15 fictional student profiles
- data/faqs.json — 15 FAQs
- data/notices.json — 15 sample notices

How to run (Fetch API needs an HTTP server):
1. Extract the ZIP.
2. Open the extracted project folder in VS Code.
3. Open Terminal in that folder.
4. Run: python -m http.server 8000
5. Open http://localhost:8000 in your browser.
6. Test Home notices, About FAQs, Events data controls, Students filters, and Practical 5 registration validation.
7. Test Practical 4 theme toggle, hamburger menu, FAQ interactions, modal, notification and original event slider.

Do not double-click index.html for this practical. Browsers commonly block Fetch API requests to JSON files from file:// URLs. Use the local HTTP server above.

Error handling:
- Each JSON-backed section shows loading, empty, and request-error states.
- Browser console logs fetch/JSON errors for debugging.
- Data is sample content only. Registration and other demo forms do not send data to a backend.

Practical 6 extensions:
- Dependent country/state/city dropdowns and localStorage caching are optional extensions and are not implemented because the instruction is to implement only the clearly stated core problem definition.
