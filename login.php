<?php
require 'core/dbConfig.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'HR') {
                    header("Location: hr/job_posts.php");
                } else {
                    header("Location: applicant/apply_job.php");
                }
                exit;
            } else {
                $errors[] = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FindHire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffe6f0;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #ff66a3;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
        }
        input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        button {
            background-color: #ff66a3;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #ff3380;
        }
        a {
            color: #ff66a3;
            text-decoration: none;
            font-size: 14px;
            margin-top: 10px;
        }
        a:hover {
            text-decoration: underline;
        }
        .errors {
            background-color: #ffe6e6;
            color: #d9534f;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if ($errors): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <a href="register.php">Don't have an account? Register here.</a>
    </div>
</body>
</html>
