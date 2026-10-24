const menuButton = document.querySelector(".menu-toggle");
const mainNav = document.querySelector("#main-nav");

if (menuButton && mainNav) {
  menuButton.addEventListener("click", () => {
    const isOpen = mainNav.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(isOpen));
  });
}

document.querySelectorAll(".demo-form").forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const message = form.querySelector(".form-message");
    const password = form.querySelector('[name="password"]');
    const confirmPassword = form.querySelector('[name="confirm-password"]');

    if (password && confirmPassword && password.value !== confirmPassword.value) {
      confirmPassword.setCustomValidity("Passwords do not match.");
      confirmPassword.reportValidity();
      confirmPassword.setCustomValidity("");
      return;
    }

    if (message) {
      message.textContent = form.querySelector('[name="suggestion"]')
        ? "Thank you. This Practical 1 demo does not store feedback yet."
        : "Form validation passed. Account processing will be added in a later practical.";
    }
  });
});
