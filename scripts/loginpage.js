document.addEventListener("DOMContentLoaded", function () {
  // Delay showing the login error modal to avoid aria-hidden/focus issue
  const loginErrorModal = document.getElementById("loginErrorModal");
  if (loginErrorModal) {
    setTimeout(() => {
      const modal = new bootstrap.Modal(loginErrorModal);
      modal.show();
    }, 100);
  }

  // Log every button and link click
  document.querySelectorAll("button, a").forEach(el => {
    el.addEventListener("click", function () {
      const label =
        this.innerText.trim() ||
        this.getAttribute("aria-label") ||
        this.getAttribute("title") ||
        this.id ||
        "Unnamed element";

      logAction("Clicked: " + label);
    });
  });

  // Log modal open/close events
  document.querySelectorAll(".modal").forEach(modalEl => {
    modalEl.addEventListener("show.bs.modal", function () {
      logAction("Opened modal: " + (this.id || "Unnamed modal"));
    });
    modalEl.addEventListener("hide.bs.modal", function () {
      logAction("Closed modal: " + (this.id || "Unnamed modal"));
    });
  });
});

function logAction(action) {
  fetch("./audit_log.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "action=" + encodeURIComponent(action)
  }).catch(err => console.error("Audit log failed:", err));
}
