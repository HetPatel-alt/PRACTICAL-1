# Wireframe Notes

The supplied wireframe screenshots are the visual reference for the initial StudentHub implementation. This document records the layout decisions; no `wireframe.html` file is required.

## Shared shell
- StudentHub logo/wordmark at the left of the header.
- Horizontal navigation for Home, About, Dashboard, Events, Feedback, FAQ, Contact, and Login.
- Consistent footer with StudentHub summary, Quick Links, University links, contact details, and copyright.
- Simple rectangular cards, clear spacing, and restrained colours.

## Home
- Introductory hero with portal label, main headline, supporting text, Create Account and View Dashboard actions.
- Three feature cards: Digital marksheets, Campus events, and Assignments.
- Four summary-statistic cards.

## About
- Short page banner.
- Two-column introduction and mission/vision panel.
- Four student-benefit cards.
- Three team cards with initials, names, and roles.

## Dashboard
- Welcome banner.
- Four statistic cards for CGPA, attendance, assignments due, and active courses.
- Two-column content area: student information, attendance progress, announcements, upcoming events, notifications, and quick links.

## Events
- Campus-calendar banner.
- Featured event panel with title, dates, description, and registration action.
- Three upcoming event cards and two past-event cards.

## Feedback
- Introductory banner.
- Two-column area with feedback form and student testimonials.

## FAQ
- Page banner and questions grouped under General, Academic, Marksheet, and Events headings.
- Native HTML `details` and `summary` controls make each question expandable.

## Login and Register
- StudentHub wordmark near the upper left.
- Centered bordered form card with title, short instructions, labelled inputs, primary action, and link to the alternate account page.

## Contact
- Three stacked cards for address, phone/email, and office hours.

## Responsive adaptation
On narrow screens, navigation collapses behind a menu button, multi-column card layouts become one column, and statistic cards use two columns. The aim is to preserve the content order and legibility rather than exactly reproduce desktop proportions on mobile.
