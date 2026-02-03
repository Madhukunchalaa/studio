<?php
session_start();
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mock Credentials
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['user_logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials. Try admin/admin";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Studio X Dashboard</title>
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <style>
        .login-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at center, #1a1a1a 0%, #000 100%);
        }

        .login-card {
            background: var(--bg-surface);
            padding: 40px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .login-logo {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 30px;
        }

        .login-logo span {
            color: var(--accent-gold);
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">Studio<span>X</span> Console</div>

            <?php if ($error): ?>
                <div style="color: #ff6b6b; margin-bottom: 20px; text-align: center; font-size: 0.9rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom: 20px;">
                    <label
                        style="display: block; margin-bottom: 8px; color: var(--text-secondary); font-size: 0.9rem;">Username</label>
                    <input type="text" name="username" class="form-input" required>
                </div>
                <div style="margin-bottom: 30px;">
                    <label
                        style="display: block; margin-bottom: 8px; color: var(--text-secondary); font-size: 0.9rem;">Password</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 14px;">ENTER CONSOLE</button>
            </form>
        </div>
    </div>

</body>

</html>