<?php
/**
 * Practical 10: Authentication, Session Management, Role-Based Access & Timeout
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session Timeout duration: 15 minutes (900 seconds)
$timeout_duration = 900;

if (isset($_SESSION['last_activity'])) {
    if ((time() - $_SESSION['last_activity']) > $timeout_duration) {
        // Session expired: Clear and destroy
        session_unset();
        session_destroy();
        header("Location: login.php?error=timeout");
        exit();
    }
}

// Update last activity timestamp
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

/**
 * Check if the user is logged in and optionally verify role
 *
 * @param string|null $required_role 'admin' or 'student'
 */
function check_auth($required_role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?error=unauthorized");
        exit();
    }

    if ($required_role !== null && $_SESSION['role'] !== $required_role) {
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>
