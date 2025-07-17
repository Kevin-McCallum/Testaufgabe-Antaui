<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
            width: 320px;
        }
        .login-box h2 {
            margin-bottom: 1rem;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-box input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            border: none;
            padding: 0.5rem;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
        }
    </style>
    <script>
        function validateForm(event) {
            const emailField = document.getElementById('username');
            const email = emailField.value.trim();
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!regex.test(email)) {
                alert('Bitte geben Sie eine gültige E-Mail-Adresse ein (z. B. user@domain.org)');
                emailField.focus();
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" onsubmit="validateForm(event)">
            <input type="text" name="username" id="username" placeholder="E-Mail-Adresse" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
