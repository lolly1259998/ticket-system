// Scripts généraux pour l'application
document.addEventListener("DOMContentLoaded", function () {
  // Confirmation pour les actions importantes
  const deleteButtons = document.querySelectorAll(".btn-delete");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      if (!confirm("Êtes-vous sûr de vouloir effectuer cette action ?")) {
        e.preventDefault();
      }
    });
  });

  // Auto-dismiss alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });

  // File upload preview (si nécessaire)
  const fileInputs = document.querySelectorAll('input[type="file"]');
  fileInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const fileName = this.files[0]?.name;
      if (fileName) {
        const label = this.previousElementSibling;
        if (label && label.tagName === "LABEL") {
          label.textContent = "Fichier: " + fileName;
        }
      }
    });
  });

  // Toggle visibilité mot de passe
  const toggles = document.querySelectorAll(".toggle-password");
  toggles.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const wrapper = btn.closest(".input-with-icon");
      const input = wrapper ? wrapper.querySelector("input") : null;
      if (!input) return;
      const isPassword = input.type === "password";
      input.type = isPassword ? "text" : "password";
      btn.setAttribute("aria-pressed", isPassword ? "true" : "false");
      const icon = btn.querySelector("i");
      if (icon) {
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
      }
    });
  });
});
