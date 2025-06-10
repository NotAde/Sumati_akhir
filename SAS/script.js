const passwordInput = document.getElementById("password");
const toggleIcon = document.getElementById("togglePassword");

if (toggleIcon && passwordInput) {
  toggleIcon.addEventListener("click", function () {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    this.src = type === "password" ? "asset/lock.png" : "asset/unlock.png";
  });
}
