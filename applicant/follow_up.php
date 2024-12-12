<?php
session_start();
require '../core/dbConfig.php';
require '../core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: ../dashboard.php");
    exit;
}

$applicantId = $_SESSION['user_id'];
$selectedHR = null;

$stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'HR'");
$hrUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selectedHR = $_GET['hr_id'] ?? null;
$messages = $selectedHR ? getMessages($applicantId, $selectedHR) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = $_POST['hr_id'];
    $content = trim($_POST['content']);

    if (!empty($content)) {
        sendMessage($applicantId, $receiverId, $content);
        $success = "Message sent successfully.";
        $messages = getMessages($applicantId, $receiverId);
    } else {
        $error = "Message content cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant - Follow Up</title>
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffe6f0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #e91e63;
        }

        select, textarea, button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #e91e63;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #c2185b;
        }

        .messages {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
        }

        .back {
            text-align: center;
            margin-top: 20px;
        }

        .back a {
            color: #e91e63;
            text-decoration: none;
        }

        .back a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Message HR</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <form method="GET">
        <label for="hr_id">Select HR Representative:</label>
        <select name="hr_id" id="hr_id" required onchange="this.form.submit()">
            <option value="">Select HR</option>
            <?php foreach ($hrUsers as $hr): ?>
                <option value="<?= $hr['id'] ?>" <?= $selectedHR == $hr['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hr['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($selectedHR): ?>
        <div>
            <h2>Messages with <?= htmlspecialchars($hrUsers[array_search($selectedHR, array_column($hrUsers, 'id'))]['username']) ?></h2>
            <div class="messages">
                <?php foreach ($messages as $msg): ?>
                    <p>
                        <strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong>
                        <?= nl2br(htmlspecialchars($msg['content'])) ?>
                        <small>(<?= htmlspecialchars($msg['sent_at']) ?>)</small>
                    </p>
                <?php endforeach; ?>
            </div>
            <form method="POST">
                <input type="hidden" name="hr_id" value="<?= $selectedHR ?>">
                <textarea name="content" placeholder="Type your message here..." required></textarea><br>
                <button type="submit">Send Follow-Up</button>
            </form>
        </div>
    <?php endif; ?>
    <div class="back">
        <a href="apply_job.php">Back to Job Listings</a>
    </div>
</div>
</body>
</html>
