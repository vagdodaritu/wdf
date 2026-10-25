<?php
/**
 * Practical 2 & 10: Student Profile View
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

check_auth();

$pageTitle = "My Profile";
$username = $_SESSION['username'];

// Fetch student record
$student = null;
$stmt = $conn->prepare("SELECT * FROM students WHERE email = ? OR name LIKE ? LIMIT 1");
$searchName = "%" . $username . "%";
$stmt->bind_param("ss", $_SESSION['email'], $searchName);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $student = $row;
}
$stmt->close();

require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <h2>Student Profile</h2>
        <p>Your academic profile and registered portal account credentials.</p>
    </div>

    <section class="card" style="max-width: 650px; margin: auto;">
        <div style="display: flex; align-items: center; gap: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px; margin-bottom: 20px;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(13,110,253,0.12); display: flex; justify-content: center; align-items: center; font-size: 32px;">
                👨‍🎓
            </div>
            <div>
                <h3 style="color: var(--primary-color); margin-bottom: 4px;"><?php echo htmlspecialchars($student['name'] ?? $username); ?></h3>
                <span class="user-badge"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                <span style="font-size: 13px; color: var(--text-muted); margin-left: 8px;">ID: <?php echo htmlspecialchars($student['student_id'] ?? 'ST2026'); ?></span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 140px 1fr; row-gap: 14px; font-size: 14.5px;">
            <strong style="color: var(--text-muted);">Username:</strong>
            <span><?php echo htmlspecialchars($username); ?></span>

            <strong style="color: var(--text-muted);">Email:</strong>
            <span><?php echo htmlspecialchars($_SESSION['email'] ?? ($student['email'] ?? 'Not set')); ?></span>

            <strong style="color: var(--text-muted);">Mobile:</strong>
            <span><?php echo htmlspecialchars($student['mobile'] ?? '9876543201'); ?></span>

            <strong style="color: var(--text-muted);">Department:</strong>
            <span><?php echo htmlspecialchars($student['course'] ?? 'Computer Engineering'); ?></span>

            <strong style="color: var(--text-muted);">Current Year:</strong>
            <span><?php echo htmlspecialchars($student['year'] ?? '2nd Year'); ?></span>

            <strong style="color: var(--text-muted);">Gender:</strong>
            <span><?php echo htmlspecialchars($student['gender'] ?? 'Not specified'); ?></span>

            <strong style="color: var(--text-muted);">Campus:</strong>
            <span>CHARUSAT, Changa</span>
        </div>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between;">
            <a href="dashboard.php" class="btn btn-secondary btn-sm">&larr; Back to Dashboard</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Log Out</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
