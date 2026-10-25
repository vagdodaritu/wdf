<?php
/**
 * Practical 14: Admin Dashboard with RBAC, Dynamic Metrics & Audit Log
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// Strict RBAC: only users with role 'admin' are permitted
check_auth('admin');

$pageTitle = "Admin Dashboard";

// 1. Dynamic Statistics Loaded from Database (Practical 14)
$studentCount = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM students");
if ($res) {
    $row = $res->fetch_assoc();
    $studentCount = $row['total'];
}

$eventCount = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM events");
if ($res) {
    $row = $res->fetch_assoc();
    $eventCount = $row['total'];
}

$userCount = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($res) {
    $row = $res->fetch_assoc();
    $userCount = $row['total'];
}

$regCount = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM registrations");
if ($res) {
    $row = $res->fetch_assoc();
    $regCount = $row['total'];
}

// 2. Fetch Latest Registrations
$latestRegistrations = [];
$res = $conn->query("SELECT * FROM registrations ORDER BY id DESC LIMIT 5");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $latestRegistrations[] = $row;
    }
}

// 3. Fetch Audit Logs (Practical 14)
$auditLogs = [];
$res = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 8");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $auditLogs[] = $row;
    }
}

require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <div class="flex-between">
            <div>
                <h2>Admin Control Center 👨‍💼</h2>
                <p>Role-Based Access Control, Live System Statistics, and System Audit Trail (Practical 14).</p>
            </div>
            <div class="flex-row">
                <a href="manage_students.php" class="btn btn-primary btn-sm">👥 Manage Students</a>
                <a href="manage_events.php" class="btn btn-secondary btn-sm">📅 Manage Events</a>
                <a href="ajax_events.php" class="btn btn-secondary btn-sm">⚡ AJAX CRUD</a>
            </div>
        </div>
    </div>

    <!-- Live Statistics Cards (Practical 14) -->
    <div class="grid-4" style="margin-bottom: 25px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(13,110,253,0.12); color: #0d6efd;">👥</div>
            <div class="stat-info">
                <h4>Total Students</h4>
                <div class="stat-number"><?php echo $studentCount; ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(25,135,84,0.12); color: #198754;">📅</div>
            <div class="stat-info">
                <h4>Total Events</h4>
                <div class="stat-number"><?php echo $eventCount; ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(255,193,7,0.12); color: #ffc107;">🔐</div>
            <div class="stat-info">
                <h4>User Accounts</h4>
                <div class="stat-number"><?php echo $userCount; ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(13,202,240,0.12); color: #0dcaf0;">🎟️</div>
            <div class="stat-info">
                <h4>Event Signups</h4>
                <div class="stat-number"><?php echo $regCount; ?></div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <!-- Latest Registrations Table -->
        <section class="card">
            <div class="flex-between" style="margin-bottom: 15px;">
                <h3 style="color: var(--primary-color);">Recent Event Registrations</h3>
                <span style="font-size: 13px; color: var(--text-muted);">From <code>registrations</code> table</span>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Event</th>
                            <th>Registered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestRegistrations)): ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No event registrations recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($latestRegistrations as $reg): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($reg['student_name']); ?></strong><br>
                                        <small style="color: var(--text-muted);"><?php echo htmlspecialchars($reg['student_email']); ?></small>
                                    </td>
                                    <td><span class="badge" style="background: rgba(13,110,253,0.1); color: #0d6efd; padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($reg['event_title']); ?></span></td>
                                    <td><small><?php echo htmlspecialchars($reg['registered_at']); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Audit Log Table (Practical 14) -->
        <section class="card">
            <div class="flex-between" style="margin-bottom: 15px;">
                <h3 style="color: var(--primary-color);">System Audit Log</h3>
                <span style="font-size: 13px; color: var(--text-muted);">From <code>audit_logs</code> table</span>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>User</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($auditLogs)): ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No audit log entries recorded.</td></tr>
                        <?php else: ?>
                            <?php foreach ($auditLogs as $log): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($log['action']); ?></strong></td>
                                    <td>
                                        <span class="user-badge" style="font-size: 11px;">
                                            <?php echo htmlspecialchars($log['username']); ?>
                                        </span>
                                    </td>
                                    <td><small style="color: var(--text-muted);"><?php echo htmlspecialchars($log['timestamp']); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
