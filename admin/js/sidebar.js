document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector(".sidebar");
  const menuToggler = document.querySelector(".menu-toggler");
  const sidebarClose = document.querySelector(".sidebar-close");

  menuToggler.addEventListener("click", function (e) {
    e.stopPropagation();
    sidebar.classList.add("show");
    menuToggler.classList.add("hide");
  });

  sidebarClose.addEventListener("click", function (e) {
    e.stopPropagation();
    sidebar.classList.remove("show");
    menuToggler.classList.remove("hide");
  });

  document.addEventListener("click", function (e) {
    if (
      window.innerWidth <= 992 &&
      !sidebar.contains(e.target) &&
      e.target !== menuToggler &&
      !menuToggler.contains(e.target)
    ) {
      sidebar.classList.remove("show");
      menuToggler.classList.remove("hide");
    }
  });

  window.addEventListener("resize", function () {
    if (window.innerWidth > 992) {
      sidebar.classList.remove("show");
      menuToggler.classList.add("hide");
    } else if (!sidebar.classList.contains("show")) {
      menuToggler.classList.remove("hide");
    }
  });
});

function toggleUserManagement(e) {
  e.preventDefault();
  const submenu = e.currentTarget.parentElement.querySelector(".nav-submenu");
  const parentItem = e.currentTarget.parentElement;

  parentItem.classList.toggle("active");
  submenu.classList.toggle("show");
}
