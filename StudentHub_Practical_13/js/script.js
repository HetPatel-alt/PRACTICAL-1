const themeButton=document.getElementById("theme-toggle");
const menuButton=document.getElementById("menu-toggle");
const mainNav=document.getElementById("main-nav");
const noticeButton=document.getElementById("notice-button");
const notification=document.getElementById("notification");
const notificationMessage=document.getElementById("notification-message");
const closeNotification=document.getElementById("close-notification");
const modalBackdrop=document.getElementById("modal-backdrop");
const openModalButton=document.getElementById("open-modal");
const closeModalButton=document.getElementById("close-modal");
const faqButtons=document.querySelectorAll(".faq-question");
const eventCards=document.querySelectorAll(".event-card");
const previousSlideButton=document.getElementById("previous-slide");
const nextSlideButton=document.getElementById("next-slide");
const slideStatus=document.getElementById("slide-status");

function applyTheme(theme){
  const dark=theme==="dark";
  document.body.classList.toggle("dark-theme",dark);
  if(themeButton){themeButton.textContent=dark?"Light mode":"Dark mode";themeButton.setAttribute("aria-pressed",String(dark));}
}
let savedTheme="light";
try{savedTheme=localStorage.getItem("studenthub-theme")||"light";}catch(error){}
applyTheme(savedTheme);
if(themeButton)themeButton.addEventListener("click",()=>{
  const nextTheme=document.body.classList.contains("dark-theme")?"light":"dark";
  applyTheme(nextTheme);
  try{localStorage.setItem("studenthub-theme",nextTheme);}catch(error){
    if(notification&&notificationMessage){notificationMessage.textContent="Theme changed, but the browser could not save the preference.";notification.hidden=false;}
  }
});
if(menuButton&&mainNav)menuButton.addEventListener("click",()=>{
  const open=mainNav.classList.toggle("open");
  menuButton.setAttribute("aria-expanded",String(open));
  menuButton.setAttribute("aria-label",open?"Close navigation menu":"Open navigation menu");
});
if(mainNav&&menuButton)mainNav.querySelectorAll("a").forEach(link=>link.addEventListener("click",()=>{
  mainNav.classList.remove("open");menuButton.setAttribute("aria-expanded","false");menuButton.setAttribute("aria-label","Open navigation menu");
}));
if(noticeButton&&notification&&notificationMessage)noticeButton.addEventListener("click",()=>{
  notificationMessage.textContent="You are all caught up with the latest StudentHub updates.";notification.hidden=false;
});
if(closeNotification&&notification)closeNotification.addEventListener("click",()=>{notification.hidden=true;});
faqButtons.forEach(button=>button.addEventListener("click",()=>{
  const expanded=button.getAttribute("aria-expanded")==="true";
  button.setAttribute("aria-expanded",String(!expanded));
  const answer=document.getElementById(button.getAttribute("aria-controls"));if(answer)answer.hidden=expanded;
}));
let activeSlide=0;
function showSlide(index){
  if(!eventCards.length)return;
  activeSlide=(index+eventCards.length)%eventCards.length;
  eventCards.forEach((card,i)=>card.classList.toggle("active",i===activeSlide));
  if(slideStatus)slideStatus.textContent=`Event ${activeSlide+1} of ${eventCards.length}`;
}
if(previousSlideButton)previousSlideButton.addEventListener("click",()=>showSlide(activeSlide-1));
if(nextSlideButton)nextSlideButton.addEventListener("click",()=>showSlide(activeSlide+1));
let previousFocus=null;
if(openModalButton&&modalBackdrop&&closeModalButton)openModalButton.addEventListener("click",()=>{
  previousFocus=document.activeElement;modalBackdrop.hidden=false;closeModalButton.focus();
});
function closeModal(){if(!modalBackdrop)return;modalBackdrop.hidden=true;if(previousFocus)previousFocus.focus();}
if(closeModalButton)closeModalButton.addEventListener("click",closeModal);
if(modalBackdrop){
  modalBackdrop.addEventListener("click",event=>{if(event.target===modalBackdrop)closeModal();});
  document.addEventListener("keydown",event=>{if(event.key==="Escape"&&!modalBackdrop.hidden)closeModal();});
}


async function updateAuthenticationNavigation() {
  const nav = document.querySelector(".main-nav");
  if (!nav) return;
  try {
    const response = await fetch("auth-status.php", { cache: "no-store", headers: { Accept: "application/json" } });
    if (!response.ok) return;
    const auth = await response.json();
    const links = [...nav.querySelectorAll("a")];
    const registrationLinks = links.filter(a => /register\.php|registration/i.test(a.getAttribute("href") || a.textContent));
    const loginLinks = links.filter(a => /login\.php|^login$/i.test(a.getAttribute("href") || a.textContent.trim()));
    let logoutLink = nav.querySelector('a[data-auth-link="logout"]');
    let dashboardLink = nav.querySelector('a[href="dashboard.php"], a[href="admin.php"]');

    if (auth.loggedIn) {
      registrationLinks.forEach(a => a.hidden = true);
      loginLinks.forEach(a => a.hidden = true);
      if (!logoutLink) {
        logoutLink = document.createElement("a");
        logoutLink.href = "logout.php";
        logoutLink.textContent = "Logout";
        logoutLink.dataset.authLink = "logout";
        nav.appendChild(logoutLink);
      }
      logoutLink.hidden = false;
      if (dashboardLink) {
        dashboardLink.href = auth.role === "admin" ? "admin.php" : "dashboard.php";
        dashboardLink.textContent = auth.role === "admin" ? "Admin Dashboard" : "Dashboard";
        dashboardLink.hidden = false;
      } else {
        dashboardLink = document.createElement("a");
        dashboardLink.href = auth.role === "admin" ? "admin.php" : "dashboard.php";
        dashboardLink.textContent = auth.role === "admin" ? "Admin Dashboard" : "Dashboard";
        nav.appendChild(dashboardLink);
      }
    } else {
      registrationLinks.forEach(a => a.hidden = false);
      loginLinks.forEach(a => a.hidden = false);
      if (logoutLink) logoutLink.remove();
      if (dashboardLink) dashboardLink.hidden = true;
    }
  } catch (error) {
    console.error("Could not update authentication navigation:", error);
  }
}
document.addEventListener("DOMContentLoaded", updateAuthenticationNavigation);
