<?php
/**
 * Practical 10: Secure Login with Sessions, Role-Based Access & Remember-Me
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// Redirect if already logged in
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$pageTitle = "User Login";
$errorMsg = "";
$infoMsg = "";

// Check messages in URL
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'timeout') {
        $errorMsg = "Your session has expired due to 15 minutes of inactivity. Please log in again.";
    } elseif ($_GET['error'] === 'unauthorized') {
        $errorMsg = "You must be logged in to access that protected page.";
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $infoMsg = "You have been logged out successfully.";
}

// Handle Login Form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $loginInput = isset($_POST['login_input']) ? trim($_POST['login_input']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember_me']);

    if (empty($loginInput) || empty($password)) {
        $errorMsg = "Please enter both your username/email and password.";
    } else {
        // Query user using prepared statement
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $loginInput, $loginInput);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verify password using password_verify() (Practical 10)
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation attacks
                session_regenerate_id(true);

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();

                // Optional: Handle Remember-Me Cookie (Practical 10)
                if ($remember) {
                    $cookieValue = base64_encode($user['id'] . ':' . $user['username']);
                    setcookie('remember_user', $cookieValue, time() + (86400 * 7), "/", "", false, true);
                }

                // Log audit trail (Practical 14)
                $action = "User logged in: " . $user['username'] . " (" . $user['role'] . ")";
                $logStmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, ?)");
                $logStmt->bind_param("sss", $action, $user['username'], $user['role']);
                $logStmt->execute();
                $logStmt->close();

                // Role-based redirection (Practical 10)
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $errorMsg = "Invalid username or password. Please try again.";
            }
        } else {
            $errorMsg = "No account found with those credentials. Please check your spelling or register.";
        }
        $stmt->close();
    }
}

require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header" style="text-align: center;">
        <h2>User Authentication</h2>
        <p>Practical 10: Secure Login with Sessions, Role-Based Access Control, and Timeout Management.</p>
    </div>

    <!-- Alert / Feedback Messages -->
    <?php if (!empty($infoMsg)): ?>
        <div class="alert alert-info" style="max-width: 480px; margin: 0 auto 20px auto;">
            ℹ️ <?php echo htmlspecialchars($infoMsg); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger" style="max-width: 480px; margin: 0 auto 20px auto;">
            ⚠️ <?php echo htmlspecialchars($errorMsg); ?>
        </div>
    <?php endif; ?>

    <section class="card" style="max-width: 480px; margin: auto;">
        <h3 style="color: var(--primary-color); margin-bottom: 20px; text-align: center;">Sign In to StudentHub</h3>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="login_input">Username or Email *</label>
                <input type="text" id="login_input" name="login_input" placeholder="e.g. admin or student" required
                       value="<?php echo isset($_POST['login_input']) ? htmlspecialchars($_POST['login_input']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="remember_me" value="1">
                        Remember me for 7 days
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                🔐 Sign In (Verify Password & Session)
            </button>
        </form>

        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid var(--border-color); font-size: 13px; color: var(--text-muted);">
            <strong>Demo Credentials (from Seed Database):</strong><br>
            • Admin: <code>admin</code> / <code>admin123</code> (Redirects to Admin Dashboard)<br>
            • Student: <code>student</code> / <code>student123</code> (Redirects to Student Dashboard)<br>
            • Student 2: <code>manali</code> / <code>manali123</code>
        </div>

        <p style="text-align: center; margin-top: 18px; font-size: 14px;">
            Don't have an account yet? <a href="register.php" style="color: var(--primary-color); font-weight: bold;">Register here</a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
