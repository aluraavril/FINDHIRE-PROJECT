<?php
session_start();
require '../core/dbConfig.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../dashboard.php");
    exit;
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $created_by = $_SESSION['user_id'];

    if (empty($title) || empty($description)) {
        $errorMessage = 'Title and description cannot be empty.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
            $stmt->execute([$title, $description, $created_by]);
            header('Location: job_posts.php');
            exit();
        } catch (PDOException $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffe6f0; /* Light pink background */
            color: #333;
        }
        nav {
            background-color: #ff99c8; /* Pink nav bar */
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        .container h1 {
            text-align: center;
            color: #d63384; /* Deep pink */
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            color: #d63384;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #f1c6da;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #ff69b4; /* Light pink */
        }
        button {
            padding: 12px 20px;
            background-color: #ff69b4; /* Pink button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #e6398e; /* Slightly darker pink */
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .btn {
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #218838;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 15px;
            background-color: #ff69b4; /* Pink back button */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .back-btn:hover {
            background-color: #e6398e; /* Slightly darker pink */
        }
        .logout-btn {
            text-decoration: none;
            background-color: #ff69b4; /* Pink logout button */
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .logout-btn:hover {
            background-color: #e6398e; /* Slightly darker pink */
        }
    </style>
</head>
<body>
    <nav>
        <div>
            <strong>FindHire HR Dashboard</strong>
        </div>
        <div>
            <a href="../core/handleForms.php?logout=1" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <a href="job_posts.php" class="back-btn">Back to Job Posts</a>
        <h1>New Job Post</h1>

        <?php if (!empty($errorMessage)): ?>
            <div class="error">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form action="add_job.php" method="POST">
            <div class="form-group">
                <label for="title">Job Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Job Description:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <button type="submit">Add Job</button>
        </form>
    </div>
</body>
</html>
