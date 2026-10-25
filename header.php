<?php
/**
 * Shared Header Component
 * Practical 2: Semantic HTML5 & Accessibility
 * Practical 4: Notification Banner & Theme Switcher
 * Practical 14: Role-Based Dynamic Navigation Menu
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$isLoggedIn = is_logged_in();
$isAdmin = is_admin();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';
$role = isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . " - " : ""; ?>StudentHub Portal</title>
    <!-- Main Responsive Stylesheet (Practical 3) -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Notification Banner (Practical 4) -->
    <div id="notificationBanner" class="notification-banner">
        <span>📢 Welcome to <strong>StudentHub Portal</strong> - Centralized Academic & Campus Management System</span>
        <button id="closeBannerBtn" class="banner-close-btn" title="Close Banner">✕</button>
    </div>

    <!-- Top Title Banner -->
    <div class="portal-title-bar">
        <h1>StudentHub Portal</h1>
        <p>CHARUSAT - Faculty of Technology & Engineering (ITUE203)</p>
    </div>

    <!-- Semantic Header (Practical 2) -->
    <header>
        <a href="index.php" class="logo-container">
            <img src="images/logo.png" alt="StudentHub Logo" width="48" height="48">
            <span class="brand-text">StudentHub</span>
        </a>

        <!-- Mobile Hamburger Button (Practical 4) -->
        <button id="hamburgerBtn" class="hamburger-btn" aria-label="Toggle Navigation">☰</button>

        <!-- Dynamic Navigation Menu (Practical 14) -->
        <nav id="mainNav">
            <ul>
                <?php if ($isAdmin): ?>
                    <!-- Admin Role Navigation -->
                    <li><a href="admin_dashboard.php" class="<?php echo ($currentPage == 'admin_dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="manage_students.php" class="<?php echo ($currentPage == 'manage_students.php') ? 'active' : ''; ?>">Manage Students</a></li>
                    <li><a href="manage_events.php" class="<?php echo ($currentPage == 'manage_events.php') ? 'active' : ''; ?>">Manage Events</a></li>
                    <li><a href="ajax_events.php" class="<?php echo ($currentPage == 'ajax_events.php') ? 'active' : ''; ?>">AJAX CRUD</a></li>
                    <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Public View</a></li>
                    <li><span class="admin-badge">ADMIN: <?php echo $username; ?></span></li>
                    <li><a href="logout.php" style="color: var(--danger-color);">Logout</a></li>

                <?php elseif ($isLoggedIn): ?>
                    <!-- Student Role Navigation -->
                    <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="about.php" class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">About</a></li>
                    <li><a href="events.php" class="<?php echo ($currentPage == 'events.php') ? 'active' : ''; ?>">Events</a></li>
                    <li><a href="faq.php" class="<?php echo ($currentPage == 'faq.php') ? 'active' : ''; ?>">FAQ</a></li>
                    <li><a href="dashboard.php" class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="profile.php" class="<?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">Profile</a></li>
                    <li><a href="feedback.php" class="<?php echo ($currentPage == 'feedback.php') ? 'active' : ''; ?>">Feedback</a></li>
                    <li><span class="user-badge">👨‍🎓 <?php echo $username; ?></span></li>
                    <li><a href="logout.php" style="color: var(--danger-color);">Logout</a></li>

                <?php else: ?>
                    <!-- Guest Navigation -->
                    <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="about.php" class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">About</a></li>
                    <li><a href="events.php" class="<?php echo ($currentPage == 'events.php') ? 'active' : ''; ?>">Events</a></li>
                    <li><a href="faq.php" class="<?php echo ($currentPage == 'faq.php') ? 'active' : ''; ?>">FAQ</a></li>
                    <li><a href="contact.php" class="<?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
                    <li><a href="register.php" class="<?php echo ($currentPage == 'register.php') ? 'active' : ''; ?>">Register</a></li>
                    <li><a href="login.php" class="btn btn-primary btn-sm <?php echo ($currentPage == 'login.php') ? 'active' : ''; ?>" style="color: #fff;">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Theme Toggle Button (Practical 4) -->
        <div class="header-actions">
            <button id="themeToggleBtn" class="theme-toggle-btn">🌙 Dark Mode</button>
        </div>
    </header>
