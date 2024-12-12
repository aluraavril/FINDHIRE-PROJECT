<?php
// delete_job.php
session_start();
require '../core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: ../dashboard.php");
    exit;
}

if (isset($_GET['job_id'])) {
    $jobId = intval($_GET['job_id']);

    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE id = ?");
    if ($stmt->execute([$jobId])) {
        header("Location: job_posts.php?success=delete");
        exit;
    } else {
        $error = "Failed to delete the job post.";
    }
} else {
    header("Location: job_posts.php?error=notfound");
    exit;
}
?>
