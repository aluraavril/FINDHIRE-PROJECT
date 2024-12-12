session_start();
require '../core/dbConfig.php';
require '../core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: ../dashboard.php");
    exit;
}

$hrId = $_SESSION['user_id'];
$applicantId = $_GET['applicant_id'] ?? null;

// Fetch messages
$messages = $applicantId ? getMessages($hrId, $applicantId) : [];

// Fetch applicants who messaged the HR
$stmt = $pdo->prepare("
    SELECT DISTINCT sender.id, sender.username 
    FROM messages 
    INNER JOIN users AS sender ON messages.sender_id = sender.id
    WHERE messages.receiver_id = ?
");
$stmt->execute([$hrId]);
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = $_POST['applicant_id'];
    $content = trim($_POST['content']);
    if (!empty($content)) {
        sendMessage($hrId, $receiverId, $content);
        $success = "Reply sent successfully.";
        $messages = getMessages($hrId, $receiverId); // Refresh messages
    } else {
        $error = "Reply cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR - Messages</title>
</head>
<body>
    <h1>Messages</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <form method="GET">
        <label>Applicant:
            <select name="applicant_id" required onchange="this.form.submit()">
                <option value="">Select Applicant</option>
                <?php foreach ($applicants as $applicant): ?>
                    <option value="<?= $applicant['id'] ?>" <?= $applicantId == $applicant['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($applicant['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </form>

    <?php if ($applicantId): ?>
        <div>
            <h2>Messages with <?= htmlspecialchars($applicants[array_search($applicantId, array_column($applicants, 'id'))]['username']) ?></h2>
            <div style="border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: auto;">
                <?php foreach ($messages as $msg): ?>
                    <p>
                        <strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong>
                        <?= nl2br(htmlspecialchars($msg['content'])) ?>
                        <small>(<?= htmlspecialchars($msg['sent_at']) ?>)</small>
                    </p>
                <?php endforeach; ?>
            </div>
            <form method="POST">
                <input type="hidden" name="applicant_id" value="<?= $applicantId ?>">
                <textarea name="content" placeholder="Type your reply here..." required></textarea><br>
                <button type="submit">Send Reply</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
