const themeButton = document.getElementById("theme-toggle");
const menuButton = document.getElementById("menu-toggle");
const mainNav = document.getElementById("main-nav");
const noticeButton = document.getElementById("notice-button");
const notification = document.getElementById("notification");
const notificationMessage = document.getElementById("notification-message");
const closeNotification = document.getElementById("close-notification");
const modalBackdrop = document.getElementById("modal-backdrop");
const openModalButton = document.getElementById("open-modal");
const closeModalButton = document.getElementById("close-modal");
const faqButtons = document.querySelectorAll(".faq-question");
const eventCards = document.querySelectorAll(".event-card");
const previousSlideButton = document.getElementById("previous-slide");
const nextSlideButton = document.getElementById("next-slide");
const slideStatus = document.getElementById("slide-status");
const registrationForm = document.getElementById("registration-form");
const formMessage = document.getElementById("form-message");

function applyTheme(theme) {
  const dark = theme === "dark";
  document.body.classList.toggle("dark-theme", dark);
  themeButton.textContent = dark ? "Light mode" : "Dark mode";
  themeButton.setAttribute("aria-pressed", String(dark));
}
let savedTheme = "light";
try { savedTheme = localStorage.getItem("studenthub-theme") || "light"; } catch (error) {}
applyTheme(savedTheme);

themeButton.addEventListener("click", () => {
  const nextTheme = document.body.classList.contains("dark-theme") ? "light" : "dark";
  applyTheme(nextTheme);
  try {
    localStorage.setItem("studenthub-theme", nextTheme);
  } catch (error) {
    if (notificationMessage && notification) {
      notificationMessage.textContent = "Theme changed, but this browser could not save the preference.";
      notification.hidden = false;
    }
  }
});

menuButton.addEventListener("click", () => {
  const isOpen = mainNav.classList.toggle("open");
  menuButton.setAttribute("aria-expanded", String(isOpen));
  menuButton.setAttribute("aria-label", isOpen ? "Close navigation menu" : "Open navigation menu");
});
mainNav.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    mainNav.classList.remove("open");
    menuButton.setAttribute("aria-expanded", "false");
    menuButton.setAttribute("aria-label", "Open navigation menu");
  });
});

if (noticeButton) noticeButton.addEventListener("click", () => {
  notificationMessage.textContent = "You are all caught up with the latest StudentHub updates.";
  notification.hidden = false;
});
if (closeNotification) closeNotification.addEventListener("click", () => { notification.hidden = true; });

faqButtons.forEach((button) => button.addEventListener("click", () => {
  const expanded = button.getAttribute("aria-expanded") === "true";
  button.setAttribute("aria-expanded", String(!expanded));
  document.getElementById(button.getAttribute("aria-controls")).hidden = expanded;
}));

let activeSlide = 0;
function showSlide(index) {
  if (!eventCards.length) return;
  activeSlide = (index + eventCards.length) % eventCards.length;
  eventCards.forEach((card, cardIndex) => card.classList.toggle("active", cardIndex === activeSlide));
  if (slideStatus) slideStatus.textContent = `Event ${activeSlide + 1} of ${eventCards.length}`;
}
if (previousSlideButton) previousSlideButton.addEventListener("click", () => showSlide(activeSlide - 1));
if (nextSlideButton) nextSlideButton.addEventListener("click", () => showSlide(activeSlide + 1));

let previousFocus = null;
if (openModalButton) openModalButton.addEventListener("click", () => {
  previousFocus = document.activeElement;
  modalBackdrop.hidden = false;
  closeModalButton.focus();
});
function closeModal() {
  if (!modalBackdrop) return;
  modalBackdrop.hidden = true;
  if (previousFocus) previousFocus.focus();
}
if (closeModalButton) closeModalButton.addEventListener("click", closeModal);
if (modalBackdrop) {
  modalBackdrop.addEventListener("click", (event) => {
    if (event.target === modalBackdrop) closeModal();
  });
}
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && modalBackdrop && !modalBackdrop.hidden) closeModal();
});

if (registrationForm) registrationForm.addEventListener("submit", (event) => {
  event.preventDefault();
  if (formMessage) formMessage.textContent = "Form checked successfully. This demo does not save or send your details.";
  registrationForm.reset();
});

