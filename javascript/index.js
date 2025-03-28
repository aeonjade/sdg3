function toggleSidebar() {
  document.querySelector("aside").classList.toggle("max-sm:w-72");
  document.querySelector("aside").classList.toggle("max-sm:px-0");
  document
    .getElementById("menu-button")
    .classList.toggle("bg-[rgba(0,_0,_0,_0.3)]");
  Array.from(document.getElementsByName("sidebar-text")).forEach((el) => {
    el.classList.toggle("opacity-100");
    el.classList.toggle("ml-3");
  });
}

document.getElementById("menu-button").addEventListener("click", () => {
  toggleSidebar();
});

document.querySelector("main").addEventListener("click", () => {
  document.querySelector("aside").classList.remove("max-sm:w-72");
  document.querySelector("aside").classList.remove("max-sm:px-0");
  document
    .getElementById("menu-button")
    .classList.remove("bg-[rgba(0,_0,_0,_0.3)]");
  Array.from(document.getElementsByName("sidebar-text")).forEach((el) => {
    el.classList.remove("opacity-100");
    el.classList.remove("ml-3");
  });
});
