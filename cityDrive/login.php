<?php
include 'config.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $auth = firebaseSignIn($email, $password, $api_key);

    if (isset($auth['localId'])) {
        $uid = $auth['localId'];
        if (isAdmin($uid)) {
            session_start();
            $_SESSION['admin_uid'] = $uid;
            header("Location: index.php");
            exit;
        } else {
            $message = "Access denied: You are not an admin.";
        }
    } else {
        $message = "Login failed: Invalid email or password.";
    }
}
?>

<!-- HTML FORM -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CityDrive</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial;
            padding: 40px;
            background-color: #f5f5f5;
          
        }

        .login-box {
            background: white;
            padding: 10px 20px;
            border-radius: 8px;
            width: 300px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

      

        .message {
            text-align: center;
            margin-top: 15px;
            color: red;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>CityDrive Admin Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>
</body>

</html>