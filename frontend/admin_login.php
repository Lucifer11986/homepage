<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Login</h2>
    <p id="error-message" style="color:red;"></p>
    <form id="login-form">
        <input type="text" name="username" placeholder="Benutzername" required><br><br>
        <input type="password" name="password" placeholder="Passwort" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p>Noch kein Konto? <a href="register.php">Registrieren</a></p>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(loginForm);
        const data = Object.fromEntries(formData.entries());

        fetch('../backend/api/users/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json().then(result => ({ status: response.status, body: result })))
        .then(({ status, body }) => {
            if (status === 200 && body.success) {
                if (body.isAdmin) {
                    window.location.href = 'dashboard.php';
                } else {
                    window.location.href = 'forum.php';
                }
            } else {
                errorMessage.textContent = body.error || 'Ein unerwarteter Fehler ist aufgetreten.';
            }
        })
        .catch(error => {
            console.error('Error logging in:', error);
            errorMessage.textContent = 'Ein unerwarteter Fehler ist aufgetreten.';
        });
    });
});
</script>

</body>
</html>
