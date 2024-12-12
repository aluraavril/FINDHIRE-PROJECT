<?php
require 'dbConfig.php';

/**
 * Function to register a new user.
 */
function registerUser($username, $password, $role)
{
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $hashedPassword, $role]);
}

/**
 * Function to authenticate user login.
 */
function loginUser($username, $password)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user; // Return user data on successful login
    }
    return false; // Return false on failure
}



/**
 * Fetch all job posts.
 */
function getJobPosts()
{
    global $pdo;
    $stmt = $pdo->query("SELECT job_posts.*, users.username AS created_by 
                         FROM job_posts 
                         INNER JOIN users ON job_posts.created_by = users.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch job posts created by a specific HR.
 */
function getJobPostsByHR($hrId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM job_posts WHERE created_by = ?");
    $stmt->execute([$hrId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Function to create a new job post.
 */
function createJobPost($title, $description, $hrId)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
    return $stmt->execute([$title, $description, $hrId]);
}

/**
 * Fetch applications for jobs created by HR.
 */
function getApplicationsForHR($hrId)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT applications.*, job_posts.title AS job_title, users.username AS applicant_name 
        FROM applications 
        INNER JOIN job_posts ON applications.job_id = job_posts.id
        INNER JOIN users ON applications.applicant_id = users.id
        WHERE job_posts.created_by = ?");
    $stmt->execute([$hrId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update the status of a job application.
 */
function updateApplicationStatus($applicationId, $status, $message)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE applications SET status = ?, message = ? WHERE id = ?");
    return $stmt->execute([$status, $message, $applicationId]);
}

/**
 * Fetch messages between an applicant and HR.
 */
function getMessages($userId, $otherUserId)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT messages.*, 
               sender.username AS sender_name, 
               receiver.username AS receiver_name 
        FROM messages 
        INNER JOIN users AS sender ON messages.sender_id = sender.id
        INNER JOIN users AS receiver ON messages.receiver_id = receiver.id
        WHERE (messages.sender_id = :userId AND messages.receiver_id = :otherUserId)
           OR (messages.sender_id = :otherUserId AND messages.receiver_id = :userId)
        ORDER BY messages.sent_at ASC
    ");
    $stmt->execute(['userId' => $userId, 'otherUserId' => $otherUserId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Send a message between users.
 */
function sendMessage($senderId, $receiverId, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
    return $stmt->execute([$senderId, $receiverId, $content]);
}
?>
