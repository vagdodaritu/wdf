<?php
/**
 * Practical 5: Frontend Form Validation with Regex & Password Strength
 * Practical 9: Secure User Registration with Duplicate Check & Password Hashing
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

$pageTitle = "Student Registration";
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/db.php';

$successMsg = "";
$errorMsg = "";

// Handle Backend PHP Registration (Practical 9)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $mobile = isset($_POST['mobile']) ? htmlspecialchars(trim($_POST['mobile'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $course = isset($_POST['course']) ? htmlspecialchars(trim($_POST['course'])) : '';
    $year = isset($_POST['year']) ? htmlspecialchars(trim($_POST['year'])) : '';
    $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : '';
    $terms = isset($_POST['terms']);

    // Generate username from email or name if blank
    if (empty($username)) {
        $username = strtolower(explode('@', $email)[0]);
    }

    // 1. Backend Validation
    if (empty($name) || empty($email) || empty($mobile) || empty($password) || empty($course) || empty($year) || empty($gender)) {
        $errorMsg = "All registration fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errorMsg = "Mobile number must be exactly 10 digits.";
    } elseif (strlen($password) < 6) {
        $errorMsg = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $errorMsg = "Password and confirm password do not match.";
    } elseif (!$terms) {
        $errorMsg = "You must agree to the terms and conditions.";
    } else {
        // 2. Duplicate Check using MySQLi Prepared Statements (Practical 9)
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $errorMsg = "An account with this username or email address already exists. Please login or use a different email.";
            $checkStmt->close();
        } else {
            $checkStmt->close();

            // 3. Password Hashing using password_hash() (Practical 9)
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';

            // 4. Insert into users table
            $userStmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $userStmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

            if ($userStmt->execute()) {
                $newUserId = $userStmt->insert_id;
                $userStmt->close();

                // 5. Insert student profile into students table
                $studentId = "ST" . date('Y') . sprintf("%03d", rand(100, 999));
                $studentStmt = $conn->prepare("INSERT INTO students (student_id, name, email, mobile, course, year, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $studentStmt->bind_param("sssssss", $studentId, $name, $email, $mobile, $course, $year, $gender);
                $studentStmt->execute();
                $studentStmt->close();

                // 6. Log Audit Record (Practical 14)
                $logAction = "New student registered: " . $name . " (" . $email . ")";
                $logStmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, ?)");
                $logStmt->bind_param("sss", $logAction, $username, $role);
                $logStmt->execute();
                $logStmt->close();

                $successMsg = "Registration successful! Your account has been securely created. You can now log in using your credentials.";
            } else {
                $errorMsg = "Database error: " . $conn->error;
            }
        }
    }
}
?>

<main>
    <div class="page-header">
        <h2>Student Registration</h2>
        <p>Client-Side Regex Validation (Practical 5) and Secure MySQL Database Insert with Password Hashing (Practical 9).</p>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success">
            🎉 <?php echo $successMsg; ?>
            <div style="margin-top: 10px;">
                <a href="login.php" class="btn btn-primary btn-sm">Proceed to Login &rarr;</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger">
            ⚠️ <?php echo $errorMsg; ?>
        </div>
    <?php endif; ?>

    <section class="card" style="max-width: 650px; margin: auto;">
        <!-- Registration Form (Practical 5 & 9) -->
        <form id="studentRegisterForm" method="POST" action="register.php" novalidate>
            <!-- Full Name -->
            <div class="form-group">
                <label for="reg_name">Full Name *</label>
                <input type="text" id="reg_name" name="name" placeholder="e.g. Manali Patel" required
                       value="<?php echo isset($_POST['name']) && empty($successMsg) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <span id="nameError" class="field-error"></span>
            </div>

            <!-- Username (Optional / Auto-generated) -->
            <div class="form-group">
                <label for="reg_username">Username (Optional)</label>
                <input type="text" id="reg_username" name="username" placeholder="Choose a unique username"
                       value="<?php echo isset($_POST['username']) && empty($successMsg) ? htmlspecialchars($_POST['username']) : ''; ?>">
                <small style="color: var(--text-muted); font-size: 12px;">If left blank, will use the prefix of your email address.</small>
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="reg_email">Email Address *</label>
                <input type="email" id="reg_email" name="email" placeholder="e.g. manali@studenthub.com" required
                       value="<?php echo isset($_POST['email']) && empty($successMsg) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <span id="emailError" class="field-error"></span>
            </div>

            <!-- Mobile Number -->
            <div class="form-group">
                <label for="reg_mobile">10-Digit Mobile Number *</label>
                <input type="tel" id="reg_mobile" name="mobile" placeholder="e.g. 9876543201" maxlength="10" required
                       value="<?php echo isset($_POST['mobile']) && empty($successMsg) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
                <span id="mobileError" class="field-error"></span>
            </div>

            <!-- Password & Strength Meter (Practical 5) -->
            <div class="form-group">
                <label for="reg_password">Password (Minimum 6 Characters) *</label>
                <input type="password" id="reg_password" name="password" placeholder="Create a strong password" required>
                <span id="passwordError" class="field-error"></span>

                <!-- Password Strength Meter -->
                <div class="password-meter-container">
                    <div class="password-meter-bar">
                        <div id="passwordStrengthBar" class="password-meter-fill"></div>
                    </div>
                    <span id="passwordStrengthText" class="password-strength-text">Strength: Enter a password</span>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="reg_confirm_password">Confirm Password *</label>
                <input type="password" id="reg_confirm_password" name="confirm_password" placeholder="Re-type your password" required>
                <span id="confirmPasswordError" class="field-error"></span>
            </div>

            <!-- Course Selection -->
            <div class="form-group">
                <label for="reg_course">Course / Department *</label>
                <select id="reg_course" name="course" required>
                    <option value="">-- Select Department --</option>
                    <option value="Computer Engineering" <?php echo (isset($_POST['course']) && $_POST['course'] == 'Computer Engineering') ? 'selected' : ''; ?>>Computer Engineering</option>
                    <option value="IT Engineering" <?php echo (isset($_POST['course']) && $_POST['course'] == 'IT Engineering') ? 'selected' : ''; ?>>IT Engineering</option>
                    <option value="AI & ML" <?php echo (isset($_POST['course']) && $_POST['course'] == 'AI & ML') ? 'selected' : ''; ?>>AI & ML</option>
                    <option value="Mechanical Engineering" <?php echo (isset($_POST['course']) && $_POST['course'] == 'Mechanical Engineering') ? 'selected' : ''; ?>>Mechanical Engineering</option>
                    <option value="Civil Engineering" <?php echo (isset($_POST['course']) && $_POST['course'] == 'Civil Engineering') ? 'selected' : ''; ?>>Civil Engineering</option>
                </select>
                <span id="courseError" class="field-error"></span>
            </div>

            <!-- Academic Year -->
            <div class="form-group">
                <label for="reg_year">Academic Year *</label>
                <select id="reg_year" name="year" required>
                    <option value="">-- Select Year --</option>
                    <option value="1st Year" <?php echo (isset($_POST['year']) && $_POST['year'] == '1st Year') ? 'selected' : ''; ?>>1st Year (Semester 1 & 2)</option>
                    <option value="2nd Year" <?php echo (isset($_POST['year']) && $_POST['year'] == '2nd Year') ? 'selected' : ''; ?>>2nd Year (Semester 3 & 4)</option>
                    <option value="3rd Year" <?php echo (isset($_POST['year']) && $_POST['year'] == '3rd Year') ? 'selected' : ''; ?>>3rd Year (Semester 5 & 6)</option>
                    <option value="4th Year" <?php echo (isset($_POST['year']) && $_POST['year'] == '4th Year') ? 'selected' : ''; ?>>4th Year (Semester 7 & 8)</option>
                </select>
                <span id="yearError" class="field-error"></span>
            </div>

            <!-- Gender -->
            <div class="form-group">
                <label>Gender *</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'checked' : ''; ?>> Male</label>
                    <label><input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'checked' : ''; ?>> Female</label>
                    <label><input type="radio" name="gender" value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'checked' : ''; ?>> Other</label>
                </div>
                <span id="genderError" class="field-error"></span>
            </div>

            <!-- Terms & Conditions Acceptance -->
            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" id="reg_terms" name="terms" value="1" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
                        I agree to the <a href="#" onclick="alert('StudentHub Terms: Users must provide accurate college details and uphold campus academic guidelines.'); return false;">Terms and Conditions</a>.
                    </label>
                </div>
                <span id="termsError" class="field-error"></span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">
                🚀 Register Account (Practical 5 & 9)
            </button>
        </form>

        <p style="text-align: center; margin-top: 18px; font-size: 14px; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight: bold;">Login here</a>
        </p>
    </section>
</main>

<!-- Practical 5 Frontend Validation Script -->
<script src="js/register.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
