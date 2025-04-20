/**
 * Script principal para el instalador
 * Este archivo maneja la funcionalidad interactiva del instalador
 */

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar los tooltips
  initTooltips();

  // Inicializar botones de información
  initInfoButtons();

  // Inicializar formularios
  initForms();

  // Inicializar tests de conexión
  initConnectionTests();

  // Inicializar animaciones de alerta
  initAlertAnimations();

  // Inicializar la funcionalidad de copiar al portapapeles
  initClipboardCopy();

  // Inicializar detección de modo oscuro
  initDarkModeDetection();
});

/**
 * Inicializa los tooltips para los elementos con atributo data-tooltip
 */
function initTooltips() {
  const tooltips = document.querySelectorAll("[data-tooltip]");
  tooltips.forEach((tooltip) => {
    tooltip.addEventListener("mouseenter", () => {
      const content = tooltip.getAttribute("data-tooltip");
      const tooltipEl = document.createElement("div");
      tooltipEl.className =
        "absolute z-50 p-2 bg-gray-900 text-white text-xs rounded shadow-lg max-w-xs";
      tooltipEl.textContent = content;
      tooltipEl.style.bottom = "100%";
      tooltipEl.style.left = "50%";
      tooltipEl.style.transform = "translateX(-50%) translateY(-5px)";
      tooltipEl.style.whiteSpace = "normal";
      tooltip.style.position = "relative";
      tooltip.appendChild(tooltipEl);
    });

    tooltip.addEventListener("mouseleave", () => {
      const tooltipEl = tooltip.querySelector("div");
      if (tooltipEl) tooltipEl.remove();
    });
  });
}

/**
 * Inicializa los botones de información que muestran paneles de ayuda
 */
function initInfoButtons() {
  const infoBtns = document.querySelectorAll(".info-btn");
  infoBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      const targetId = this.getAttribute("data-target");
      const infoPanel = document.getElementById(targetId);
      if (infoPanel) {
        if (infoPanel.classList.contains("hidden")) {
          infoPanel.classList.remove("hidden");
          infoPanel.classList.add("fade-in");
        } else {
          infoPanel.classList.add("hidden");
          infoPanel.classList.remove("fade-in");
        }
      }
    });
  });
}

/**
 * Inicializa los formularios para manejar validaciones y envíos
 */
function initForms() {
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    // Agregar clase de validación a los campos requeridos
    const requiredFields = form.querySelectorAll("[required]");
    requiredFields.forEach((field) => {
      field.addEventListener("blur", function () {
        if (this.value.trim() === "") {
          this.classList.add("border-red-500", "dark:border-red-400");
        } else {
          this.classList.remove("border-red-500", "dark:border-red-400");
        }
      });
    });

    // Mostrar indicador de carga al enviar el formulario
    form.addEventListener("submit", function () {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add("opacity-75");

        // Agregar spinner
        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                `;

        // Restaurar después de 30 segundos en caso de timeout
        setTimeout(() => {
          submitBtn.disabled = false;
          submitBtn.classList.remove("opacity-75");
          submitBtn.innerHTML = originalContent;
        }, 30000);
      }
    });
  });
}

/**
 * Inicializa los botones de prueba de conexión para la base de datos
 */
function initConnectionTests() {
  const testConnectionBtn = document.getElementById("test-connection");
  if (testConnectionBtn) {
    testConnectionBtn.addEventListener("click", function (e) {
      e.preventDefault();

      const form = document.getElementById("database-form");
      const formData = new FormData(form);
      const testUrl = this.getAttribute("data-url");

      // Mostrar indicador de carga
      const originalContent = this.innerHTML;
      this.disabled = true;
      this.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Probando...
            `;

      // Realizar la solicitud AJAX
      fetch(testUrl, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
      })
        .then((response) => response.json())
        .then((data) => {
          // Crear alerta
          const alertContainer = document.createElement("div");
          alertContainer.className = data.success
            ? "alert alert-success"
            : "alert alert-error";

          alertContainer.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 ${
                              data.success
                                ? "text-green-500 dark:text-green-400"
                                : "text-red-500 dark:text-red-400"
                            }" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="${
                                  data.success
                                    ? "M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    : "M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                }"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium ${
                              data.success
                                ? "text-green-800 dark:text-green-200"
                                : "text-red-800 dark:text-red-200"
                            }">${data.message}</p>
                        </div>
                    </div>
                `;

          // Insertar alerta antes del formulario
          form.parentNode.insertBefore(alertContainer, form);

          // Eliminar alerta después de 5 segundos
          setTimeout(() => {
            alertContainer.style.opacity = "0";
            alertContainer.style.transition = "opacity 0.5s ease-in-out";
            setTimeout(() => {
              alertContainer.remove();
            }, 500);
          }, 5000);
        })
        .catch((error) => {
          console.error("Error:", error);
          // Mostrar error en alerta
          const alertContainer = document.createElement("div");
          alertContainer.className = "alert alert-error";
          alertContainer.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">Error al probar la conexión. Por favor, inténtelo de nuevo.</p>
                        </div>
                    </div>
                `;

          form.parentNode.insertBefore(alertContainer, form);
        })
        .finally(() => {
          // Restaurar botón
          this.disabled = false;
          this.innerHTML = originalContent;
        });
    });
  }
}

/**
 * Inicializa las animaciones para las alertas
 */
function initAlertAnimations() {
  const alerts = document.querySelectorAll(".alert");
  if (alerts.length > 0) {
    alerts.forEach((alert) => {
      alert.classList.add("transition-opacity", "duration-500", "ease-in-out");
      alert.addEventListener("click", function () {
        this.style.opacity = "0";
        setTimeout(() => {
          this.remove();
        }, 500);
      });
    });
  }
}
/**
 * Inicializa la funcionalidad de copiar al portapapeles
 */
function initClipboardCopy() {
  const copyBtns = document.querySelectorAll(".copy-btn");
  copyBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      const textToCopy = this.getAttribute("data-copy-text");
      navigator.clipboard.writeText(textToCopy).then(() => {
        // Crear alerta de éxito
        const alertContainer = document.createElement("div");
        alertContainer.className = "alert alert-success";
        alertContainer.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500 dark:text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Texto copiado al portapapeles.</p>
                        </div>
                    </div>
                `;
        document.body.appendChild(alertContainer);

        // Eliminar alerta después de un tiempo
        setTimeout(() => {
          alertContainer.style.opacity = "0";
          setTimeout(() => {
            alertContainer.remove();
          }, 500);
        }, 3000);
      });
    });
  });
}
/**
 * Inicializa la detección de modo oscuro
 */
function initDarkModeDetection() {
  const darkModeToggle = document.getElementById("dark-mode-toggle");
  if (darkModeToggle) {
    darkModeToggle.addEventListener("click", function () {
      document.body.classList.toggle("dark");
      localStorage.setItem(
        "dark-mode",
        document.body.classList.contains("dark") ? "enabled" : "disabled"
      );
    });

    // Verificar el estado del modo oscuro al cargar la página
    if (localStorage.getItem("dark-mode") === "enabled") {
      document.body.classList.add("dark");
    }
  }
}
