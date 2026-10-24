StudentHub Practical 5 — Practical 4 Integrated + Registration Validation

This project is built directly on the multi-page Practical 4 project. It preserves all five Practical 4 pages and the shared navigation/theme/FAQ/notification/modal/event-slider interactions. Practical 5 adds validation to the existing register.html page; it does not create a separate registration mini-project.

Pages:
- index.html — Home and notification banner
- about.html — About page and collapsible FAQ
- register.html — Existing StudentHub registration page, extended with Practical 5 validation
- dashboard.html — Dashboard and modal popup
- events.html — Event slider
- css/style.css — Shared styling for every page
- js/script.js — Practical 4 interactions shared across pages
- js/validation.js — Practical 5 registration validation and password strength meter

Practical 5 requirements:
- HTML5 input types for name, email, mobile, password, confirmation
- Course, year, gender and terms acceptance
- Regex checks for name, email and mobile
- Password complexity and matching confirmation
- Inline field errors, aria-invalid indicators, live error messages, first-invalid focus
- Real-time validation on input/change and password strength indicator

Run:
1. Extract the ZIP.
2. Open index.html in your browser.
3. Navigate between all five pages using the shared navigation.
4. Use the existing registration page and test valid/invalid entries.
5. Test the Practical 4 FAQ, modal, event slider, notification, hamburger menu and theme persistence.

This is a frontend-only demo. Registration does not save or send data to a server. Optional Canvas CAPTCHA is not included.
