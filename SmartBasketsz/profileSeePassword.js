function togglePassword() {
    const passwordField = document.getElementById("password");
    // Toggle between "password" and "text"
    passwordField.type = (passwordField.type === "password") ? "text" : "password";
}