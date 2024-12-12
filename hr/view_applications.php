<?php
session_start();
require '../core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: ../dashboard.php");
    exit;
}

if (!isset($_GET['job_id'])) {
    echo "No job selected.";
    exit;
}

$job_id = intval($_GET['job_id']);

// Fetch the job details
$stmt = $pdo->prepare("SELECT * FROM job_posts WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    echo "Job not found.";
    exit;
}

// Fetch the applications for the job
$stmt = $pdo->prepare("
    SELECT a.id AS application_id, u.username, a.resume_path, a.application_status, a.submitted_at
    FROM applications a
    JOIN users u ON a.applicant_id = u.id
    WHERE a.job_id = ?
");
$stmt->execute([$job_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicants</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffe6f0; /* Light pink background */
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #d63384; /* Deep pink */
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #f1c6da;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #ff69b4; /* Pink header */
            color: white;
        }
        td a {
            color: #ff69b4; /* Pink links */
        }
        .actions button {
            margin-right: 5px;
            padding: 5px 10px;
            cursor: pointer;
            border: none;
            border-radius: 3px;
            font-weight: bold;
        }
        .accept {
            background-color: #ff69b4; /* Pink accept button */
            color: white;
        }
        .reject {
            background-color: #ff4c8b; /* Slightly darker pink for reject */
            color: white;
        }
        .back {
            text-decoration: none;
            padding: 10px 15px;
            background-color: #ff69b4; /* Pink back button */
            color: white;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back:hover {
            background-color: #e6398e; /* Slightly darker pink */
        }
        .actions button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Applicants for Job: <?= htmlspecialchars($job['title']) ?></h2>
        <a href="job_posts.php" class="back">Back to Job Posts</a>
        <?php if ($applications): ?>
            <table>
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Resume</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['username']) ?></td>
                            <td><a href="../uploads/<?= htmlspecialchars($app['resume_path']) ?>" target="_blank">View Resume</a></td>
                            <td><?= htmlspecialchars($app['application_status']) ?></td>
                            <td><?= htmlspecialchars($app['submitted_at']) ?></td>
                            <td class="actions">
                                <?php if ($app['application_status'] === 'Pending'): ?>
                                    <a href="../core/handleForms.php?action=accept&application_id=<?= $app['application_id'] ?>">
                                        <button class="accept">Accept</button>
                                    </a>
                                    <a href="../core/handleForms.php?action=reject&application_id=<?= $app['application_id'] ?>">
                                        <button class="reject">Reject</button>
                                    </a>
                                <?php else: ?>
                                    <em><?= htmlspecialchars($app['application_status']) ?></em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No applications for this job yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
