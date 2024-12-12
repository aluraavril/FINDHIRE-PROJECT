<?php
session_start();
require 'models.php';

/**
 * Handle user registration form submission.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    if (!empty($username) && !empty($password) && in_array($role, ['Applicant', 'HR'])) {
        if (registerUser($username, $password, $role)) {
            header("Location: login.php?success=registered");
            exit;
        } else {
            $error = "Registration failed. Please try again.";
        }
    } else {
        $error = "All fields are required, and role must be valid.";
    }
}

/**
 * Handle user login form submission.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $user = loginUser($username, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "All fields are required.";
    }
}

/**
 * Handle job post creation by HR.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_job'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $hrId = $_SESSION['user_id'];

    if (!empty($title) && !empty($description)) {
        if (createJobPost($title, $description, $hrId)) {
            header("Location: hr/job_posts.php");
            exit;
        } else {
            $error = "Failed to create job post.";
        }
    } else {
        $error = "All fields are required.";
    }
}

/**
 * Handle application status update by HR.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_application'])) {
    $applicationId = $_POST['application_id'];
    $status = $_POST['status'];
    $message = trim($_POST['message']);

    if (updateApplicationStatus($applicationId, $status, $message)) {
        header("Location: hr/view_applications.php");
        exit;
    } else {
        $error = "Failed to update application status.";
    }
}

/**
 * Handle message sending form submission.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiverId = $_POST['receiver_id'];
    $content = trim($_POST['content']);
    $senderId = $_SESSION['user_id'];

    if (!empty($content)) {
        if (sendMessage($senderId, $receiverId, $content)) {
            header("Location: messages.php");
            exit;
        } else {
            $error = "Failed to send message.";
        }
    } else {
        $error = "Message content cannot be empty.";
    }
}

if (isset($_GET['logout'])) {
    session_start();
    session_unset();
    session_destroy();

    // Redirect to the login page
    header("Location: ../login.php");
    exit;
}
if (isset($_GET['action']) && isset($_GET['application_id'])) {
    $application_id = intval($_GET['application_id']);
    $action = $_GET['action'] === 'accept' ? 'Accepted' : 'Rejected';

    $stmt = $pdo->prepare("UPDATE applications SET application_status = ? WHERE id = ?");
    $stmt->execute([$action, $application_id]);

    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}

?>
