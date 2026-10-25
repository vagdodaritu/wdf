<?php
/**
 * Practical 10: Student Dashboard (Protected by Session & Role-Based Access Control)
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// Restrict access: user must be logged in
check_auth();

// If admin accesses, redirect to admin dashboard
if (is_admin()) {
    header("Location: admin_dashboard.php");
    exit();
}

$pageTitle = "Student Dashboard";
$username = $_SESSION['username'];

// Fetch student profile details from MySQL
$student = null;
$stmt = $conn->prepare("SELECT * FROM students WHERE email = ? OR name LIKE ? LIMIT 1");
$searchPattern = "%" . $username . "%";
$stmt->bind_param("ss", $_SESSION['email'], $searchPattern);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $student = $row;
}
$stmt->close();

// Fetch events registered by this student
$registrations = [];
$regStmt = $conn->prepare("SELECT * FROM registrations WHERE student_name LIKE ? OR student_email = ? ORDER BY id DESC LIMIT 5");
$regStmt->bind_param("ss", $searchPattern, $_SESSION['email']);
$regStmt->execute();
$regRes = $regStmt->get_result();
while ($r = $regRes->fetch_assoc()) {
    $registrations[] = $r;
}
$regStmt->close();

require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <div class="flex-between">
            <div>
                <h2>Welcome, <?php echo htmlspecialchars($student['name'] ?? $username); ?>! 👨‍🎓</h2>
                <p>Student Academic Portal & Personal Dashboard (Practical 10: Protected Session Area).</p>
            </div>
            <div>
                <span class="user-badge" style="font-size: 14px; padding: 6px 14px;">
                    Department: <?php echo htmlspecialchars($student['course'] ?? 'Engineering'); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Student Key Metrics Grid (Practical 3 & 14 Layouts) -->
    <div class="grid-4" style="margin-bottom: 25px;">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <h4>Attendance</h4>
                <div class="stat-number">88.5%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <h4>Semester</h4>
                <div class="stat-number">3rd (Odd)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-info">
                <h4>Registered Events</h4>
                <div class="stat-number"><?php echo count($registrations); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-info">
                <h4>Status</h4>
                <div class="stat-number" style="font-size: 18px; color: var(--success-color);">Active</div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <!-- Announcements & Notices -->
        <section class="card">
            <h3 style="color: var(--primary-color); margin-bottom: 15px;">📢 Academic Announcements</h3>
            
            <article style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 12px;">
                <h4 style="font-size: 15px; color: var(--text-color);">Practical Exam Schedule Released</h4>
                <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">
                    Web Development Frameworks (ITUE203) practical viva and laboratory tests will commence next week.
                </p>
                <small style="color: var(--primary-color);">📅 Posted: Oct 12, 2026</small>
            </article>

            <article style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 12px;">
                <h4 style="font-size: 15px; color: var(--text-color);">Web Development Bootcamp Registration Open</h4>
                <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">
                    Explore hands-on full-stack development with PHP, MySQL, and modern JavaScript.
                </p>
                <small style="color: var(--primary-color);">📅 Posted: Oct 10, 2026</small>
            </article>

            <article>
                <h4 style="font-size: 15px; color: var(--text-color);">Library Book Return Reminder</h4>
                <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 4px;">
                    Students must renew or return borrowed reference manuals before the semester-end deadline.
                </p>
                <small style="color: var(--primary-color);">📅 Posted: Oct 05, 2026</small>
            </article>
        </section>

        <!-- Quick Access & My Events -->
        <section>
            <div class="card" style="margin-bottom: 25px;">
                <h3 style="color: var(--primary-color); margin-bottom: 15px;">⚡ Quick Actions</h3>
                <div class="flex-row" style="flex-wrap: wrap;">
                    <a href="profile.php" class="btn btn-secondary btn-sm">👤 My Profile</a>
                    <a href="events.php" class="btn btn-primary btn-sm">📅 View Events</a>
                    <a href="feedback.php" class="btn btn-secondary btn-sm">💬 Give Feedback</a>
                    <a href="faq.php" class="btn btn-secondary btn-sm">❓ Help & FAQs</a>
                </div>
            </div>

            <div class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 12px;">🎟️ My Event Registrations</h3>
                <?php if (empty($registrations)): ?>
                    <p style="font-size: 14px; color: var(--text-muted);">
                        You have not registered for any events yet. Check out the <a href="events.php">Events directory</a>!
                    </p>
                <?php else: ?>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($registrations as $reg): ?>
                            <li style="padding: 8px 12px; background: rgba(13,110,253,0.06); border-radius: 4px; font-size: 13.5px;">
                                <strong><?php echo htmlspecialchars($reg['event_title']); ?></strong><br>
                                <small style="color: var(--text-muted);">Registered on: <?php echo htmlspecialchars($reg['registered_at']); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
