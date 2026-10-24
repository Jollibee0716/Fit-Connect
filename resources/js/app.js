function toggleMenu() {
  document.getElementById("menu").classList.toggle("show");
  document.getElementById("overlay").classList.toggle("show");
  document.body.classList.toggle("menu-open");
}

function goToLogin() {
  window.location.href = "/log_in";
}

document.querySelectorAll("#menu a").forEach(link => {
  link.addEventListener("click", () => {
    document.getElementById("menu").classList.remove("show");
    document.getElementById("overlay").classList.remove("show");
    document.body.classList.remove("menu-open");
  });
});

window.addEventListener("load", () => window.scrollTo(0, 0));

// Vite files are modules, so expose the functions used by onclick=""
window.toggleMenu = toggleMenu;
window.goToLogin = goToLogin;