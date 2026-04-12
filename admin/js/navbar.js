let activeSubmenu = null;

function toggleUserManagement(event) {
  event.preventDefault();
  const navItem = event.currentTarget.parentElement;
  const submenu = navItem.querySelector(".nav-submenu");

  if (activeSubmenu && activeSubmenu !== submenu) {
    activeSubmenu.classList.remove("show");
    activeSubmenu.parentElement.classList.remove("active");
  }

  submenu.classList.toggle("show");
  navItem.classList.toggle("active");

  if (submenu.classList.contains("show")) {
    activeSubmenu = submenu;
  } else {
    activeSubmenu = null;
  }
}

document
  .querySelectorAll('.nav-link:not([onclick="toggleUserManagement(event)"])')
  .forEach((link) => {
    link.addEventListener("click", function () {
      if (activeSubmenu) {
        activeSubmenu.classList.remove("show");
        activeSubmenu.parentElement.classList.remove("active");
        activeSubmenu = null;
      }
    });
  });

document.addEventListener("DOMContentLoaded", function () {
  const currentPage = window.location.pathname.split("/").pop();
  const submenuPages = [
    "loginhistory.php",
    "user_management.php",
    "admin-accounts.php",
  ];

  if (submenuPages.includes(currentPage)) {
    const userManagementItem = document.querySelector(".has-submenu");
    const submenu = userManagementItem.querySelector(".nav-submenu");

    submenu.classList.add("show");
    userManagementItem.classList.add("active");
    activeSubmenu = submenu;
  }
});