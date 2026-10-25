<?php
/**
 * Practical 10: Secure Logout Handler
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// Log audit trail before destroying session
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    $action = "User logged out: " . $_SESSION['username'];
    $stmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $action, $_SESSION['username'], $_SESSION['role']);
        $stmt->execute();
        $stmt->close();
    }
}

// Unset all session variables
$_SESSION = [];

// Delete session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear Remember-Me cookie if set
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect to login page
header("Location: login.php?msg=logged_out");
exit();
?>
