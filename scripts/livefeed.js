document.addEventListener("DOMContentLoaded", () => {
  /* ---- Logout modal ---- */
  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", (ev) => {
      ev.preventDefault();
      const modal = document.getElementById("confirmModal");
      const msgEl = document.getElementById("confirmMessage");
      const yes = document.getElementById("confirmYes");
      const no = document.getElementById("confirmNo");

      msgEl.textContent = "Are you sure you want to log out?";
      modal.classList.add("show");

      yes.onclick = () => { window.location.href = logoutLink.href; };
      no.onclick = () => { modal.classList.remove("show"); };
    });
  }

  /* ---- Update live feed ---- */
  function updateLiveFeed() {
    const img = document.getElementById('livefeed');
    if (img) {
      img.src = 'http://localhost:8000/camera/frame?' + new Date().getTime();
    }
  }

  updateLiveFeed();
  setInterval(updateLiveFeed, 200);
});
