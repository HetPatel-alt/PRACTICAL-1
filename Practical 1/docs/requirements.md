# Requirement Analysis

## Problem definition
Student information and services are often separated across different pages or processes. Students may need to check academic information, find announcements, and discover events in different places. StudentHub is planned as a single student-facing portal for these common activities.

## Project objectives
- Provide one clear navigation system for student services.
- Present academic overview information in a dashboard.
- Make campus events easy to discover.
- Provide FAQ, feedback, and contact information.
- Establish a maintainable folder structure for later practicals.

## User roles
| Role | Planned responsibilities |
|---|---|
| Student | View own profile and academic overview, browse events, submit feedback |
| Administrator | Maintain portal content, publish announcements, manage events and student records |
| Visitor | Browse public pages, FAQs, contact information, and public event information |

## Functional requirements
1. Users can navigate between the planned portal pages.
2. Home page presents the portal purpose, main features, and summary statistics.
3. About page presents mission, vision, benefits, and team information.
4. Dashboard presents example academic indicators, announcements, and quick links.
5. Events page separates featured, upcoming, and past events.
6. FAQ page groups common questions by topic.
7. Feedback page accepts a suggestion in the front-end demonstration.
8. Login and registration pages provide labelled form fields.
9. Contact page displays contact details and office hours.
10. Profile and admin overview pages establish future page layouts.

## Non-functional requirements
- Responsive layouts for mobile, tablet, and desktop.
- Semantic HTML and basic accessibility practices.
- Shared styles and navigation conventions across pages.
- Readable, maintainable file names and directory structure.
- No secret keys or credentials stored in the front end.

## Practical 1 scope boundary
This practical is for planning and project foundation. Form submissions are not stored, and login/registration are not connected to a database. Secure authentication, data persistence, dynamic event loading, and role-based permissions are future implementation work.
