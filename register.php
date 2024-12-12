<?php
require 'core/dbConfig.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    // Validate inputs
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (!in_array($role, ['Applicant', 'HR'])) $errors[] = "Invalid role selected.";

    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $role]);
            header("Location: login.php");
            exit;
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
    <title>Register - FindHire</title>
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
        input, select {
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
        <h1>Register</h1>
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
            <label>Role:</label>
            <select name="role">
                <option value="Applicant">Applicant</option>
                <option value="HR">HR</option>
            </select>
            <button type="submit">Register</button>
        </form>
        <a href="login.php">Already have an account? Login here.</a>
    </div>
</body>
</html>
