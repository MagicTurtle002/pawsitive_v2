// Toggle password visibility
function togglePassword(inputId, icon) {
  const input = document.getElementById(inputId);
  const iconElement = icon.querySelector("i");
  if (input.type === "password") {
    input.type = "text";
    iconElement.classList.remove("fa-eye");
    iconElement.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    iconElement.classList.remove("fa-eye-slash");
    iconElement.classList.add("fa-eye");
  }
}

// Form validation with only one error message at a time
document
  .getElementById("loginForm")
  .addEventListener("submit", function (event) {
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    let isValid = true;
    let firstErrorMessage = "";

    // Clear previous error messages
    const existingErrors = document.getElementById("client-validation-errors");
    if (existingErrors) {
      existingErrors.remove();
    }

    // Reset previous error styling
    email.classList.remove("border-red-500");
    password.classList.remove("border-red-500");

    // Basic validation - check email first
    if (!email.value.trim()) {
      isValid = false;
      email.classList.add("border-red-500");
      firstErrorMessage = "Email address is required.";
    } else {
      // Check email format
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(email.value.trim())) {
        isValid = false;
        email.classList.add("border-red-500");
        firstErrorMessage = "Please enter a valid email address.";
      }
    }

    // Only check password if email is valid
    if (isValid && !password.value.trim()) {
      isValid = false;
      password.classList.add("border-red-500");
      firstErrorMessage = "Password is required.";
    }

    // Display only the first error message
    if (!isValid) {
      event.preventDefault();

      // Create error message container
      const errorDiv = document.createElement("div");
      errorDiv.id = "client-validation-errors";
      errorDiv.className =
        "bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-6 rounded shadow-sm";

      const flexDiv = document.createElement("div");
      flexDiv.className = "flex";

      const iconDiv = document.createElement("div");
      iconDiv.className = "flex-shrink-0";
      iconDiv.innerHTML = '<i class="fas fa-exclamation-circle mt-0.5"></i>';

      const contentDiv = document.createElement("div");
      contentDiv.className = "ml-3";

      // Add just the first error message
      contentDiv.textContent = firstErrorMessage;

      flexDiv.appendChild(iconDiv);
      flexDiv.appendChild(contentDiv);
      errorDiv.appendChild(flexDiv);

      // Insert error div before the form
      const form = document.getElementById("loginForm");
      form.parentNode.insertBefore(errorDiv, form);
    }
  });
