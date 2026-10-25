<?php
/**
 * Practical 12: Event Management CRUD with Poster Upload & File Validation
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// RBAC: Restricted to Admin
check_auth('admin');

$pageTitle = "Manage Events";
$successMsg = "";
$errorMsg = "";
$uploadDir = __DIR__ . '/uploads/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 1. Handle Delete Event
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = intval($_GET['id']);

    // Fetch poster filename to delete from disk
    $stmtPoster = $conn->prepare("SELECT title, poster FROM events WHERE id = ?");
    $stmtPoster->bind_param("i", $deleteId);
    $stmtPoster->execute();
    $resPoster = $stmtPoster->get_result();
    if ($evRow = $resPoster->fetch_assoc()) {
        $evTitle = $evRow['title'];
        if (!empty($evRow['poster']) && file_exists($uploadDir . $evRow['poster'])) {
            @unlink($uploadDir . $evRow['poster']);
        }
    } else {
        $evTitle = "ID #$deleteId";
    }
    $stmtPoster->close();

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        // Audit log
        $logAction = "Admin deleted event: " . $evTitle;
        $logStmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, 'admin')");
        $logStmt->bind_param("ss", $logAction, $_SESSION['username']);
        $logStmt->execute();
        $logStmt->close();

        $successMsg = "Event deleted successfully.";
    } else {
        $errorMsg = "Failed to delete event: " . $stmt->error;
    }
    $stmt->close();
}

// 2. Handle Add / Edit Event (POST with File Upload)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $formAction = $_POST['form_action'] ?? 'add';
    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
    $category = htmlspecialchars(trim($_POST['category'] ?? 'Workshop'));
    $eventDate = htmlspecialchars(trim($_POST['event_date'] ?? ''));
    $venue = htmlspecialchars(trim($_POST['venue'] ?? ''));
    $status = htmlspecialchars(trim($_POST['status'] ?? 'Upcoming'));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));

    if (empty($title) || empty($eventDate) || empty($venue) || empty($description)) {
        $errorMsg = "Title, date, venue, and description are required.";
    } else {
        $posterFilename = null;

        // Process File Upload with Validation (Practical 12)
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
            $fileError = $_FILES['poster']['error'];
            $fileSize = $_FILES['poster']['size'];
            $fileTmp = $_FILES['poster']['tmp_name'];
            $origName = $_FILES['poster']['name'];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB limit

            if ($fileError !== UPLOAD_ERR_OK) {
                $errorMsg = "File upload failed with error code: $fileError";
            } elseif (!in_array($ext, $allowedExtensions)) {
                $errorMsg = "Invalid file type. Allowed formats: JPG, PNG, WEBP, GIF.";
            } elseif ($fileSize > $maxFileSize) {
                $errorMsg = "Poster file size exceeds the 2MB limit.";
            } else {
                // Secure file renaming (Practical 12 Extension)
                $posterFilename = 'poster_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $destination = $uploadDir . $posterFilename;

                if (!move_uploaded_file($fileTmp, $destination)) {
                    $errorMsg = "Failed to save uploaded poster image to server.";
                    $posterFilename = null;
                }
            }
        }

        if (empty($errorMsg)) {
            if ($formAction === 'add') {
                $stmt = $conn->prepare("INSERT INTO events (title, description, category, event_date, venue, poster, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $title, $description, $category, $eventDate, $venue, $posterFilename, $status);

                if ($stmt->execute()) {
                    // Audit log
                    $logAction = "Admin added event: " . $title;
                    $logStmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, 'admin')");
                    $logStmt->bind_param("ss", $logAction, $_SESSION['username']);
                    $logStmt->execute();
                    $logStmt->close();

                    $successMsg = "Event '$title' added successfully with poster validation!";
                } else {
                    $errorMsg = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } elseif ($formAction === 'edit') {
                $eventId = intval($_POST['event_id'] ?? 0);
                if ($eventId > 0) {
                    if ($posterFilename) {
                        $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, category = ?, event_date = ?, venue = ?, poster = ?, status = ? WHERE id = ?");
                        $stmt->bind_param("sssssssi", $title, $description, $category, $eventDate, $venue, $posterFilename, $status, $eventId);
                    } else {
                        $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, category = ?, event_date = ?, venue = ?, status = ? WHERE id = ?");
                        $stmt->bind_param("ssssssi", $title, $description, $category, $eventDate, $venue, $status, $eventId);
                    }

                    if ($stmt->execute()) {
                        $logAction = "Admin updated event ID #$eventId: " . $title;
                        $logStmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, 'admin')");
                        $logStmt->bind_param("ss", $logAction, $_SESSION['username']);
                        $logStmt->execute();
                        $logStmt->close();

                        $successMsg = "Event record updated successfully!";
                    } else {
                        $errorMsg = "Update error: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
    }
}

// Fetch all events from DB
$eventsResult = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <div class="flex-between">
            <div>
                <h2>Event Management CRUD & Poster Upload</h2>
                <p>Add, Update, Delete & Upload Event Posters with MIME/Size Validation (Practical 12).</p>
            </div>
            <div>
                <button onclick="document.getElementById('eventFormSection').scrollIntoView({behavior: 'smooth'})" class="btn btn-primary btn-sm">➕ Add New Event</button>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <!-- Events List Table (Practical 12) -->
    <section class="card">
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">Published Campus Events</h3>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Title & Category</th>
                        <th>Date & Venue</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($eventsResult->num_rows === 0): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 25px;">No events recorded in database yet.</td></tr>
                    <?php else: ?>
                        <?php while ($ev = $eventsResult->fetch_assoc()): ?>
                            <tr>
                                <td style="width: 80px;">
                                    <?php if (!empty($ev['poster']) && file_exists($uploadDir . $ev['poster'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($ev['poster']); ?>" alt="Poster" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <img src="images/campus.png" alt="Default" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px; opacity: 0.6;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($ev['title']); ?></strong><br>
                                    <span class="badge" style="background: rgba(13,110,253,0.1); color: #0d6efd; padding: 2px 6px; border-radius: 4px; font-size: 11px;"><?php echo htmlspecialchars($ev['category']); ?></span>
                                </td>
                                <td>
                                    📅 <?php echo htmlspecialchars($ev['event_date']); ?><br>
                                    <small style="color: var(--text-muted);">📍 <?php echo htmlspecialchars($ev['venue']); ?></small>
                                </td>
                                <td>
                                    <span style="font-weight: bold; color: <?php echo ($ev['status'] == 'Upcoming') ? 'var(--success-color)' : 'var(--text-muted)'; ?>;">
                                        <?php echo htmlspecialchars($ev['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-secondary btn-sm edit-ev-btn"
                                            data-id="<?php echo $ev['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($ev['title']); ?>"
                                            data-category="<?php echo htmlspecialchars($ev['category']); ?>"
                                            data-date="<?php echo htmlspecialchars($ev['event_date']); ?>"
                                            data-venue="<?php echo htmlspecialchars($ev['venue']); ?>"
                                            data-status="<?php echo htmlspecialchars($ev['status']); ?>"
                                            data-desc="<?php echo htmlspecialchars($ev['description']); ?>">
                                        ✏️ Edit
                                    </button>
                                    <a href="manage_events.php?action=delete&id=<?php echo $ev['id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete event: <?php echo htmlspecialchars(addslashes($ev['title'])); ?>?');">
                                        🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Add / Edit Event Form (Practical 12) -->
    <section id="eventFormSection" class="card" style="max-width: 700px; margin: auto;">
        <h3 id="evFormHeading" style="color: var(--primary-color); margin-bottom: 15px;">➕ Add New Event & Upload Poster</h3>

        <form method="POST" action="manage_events.php" enctype="multipart/form-data" id="eventCrudForm">
            <input type="hidden" name="form_action" id="ev_form_action" value="add">
            <input type="hidden" name="event_id" id="ev_event_id" value="">

            <div class="form-group">
                <label for="ev_title">Event Title *</label>
                <input type="text" id="ev_title" name="title" required placeholder="e.g. AI & Machine Learning Workshop">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="ev_category">Category *</label>
                    <select id="ev_category" name="category" required>
                        <option value="Workshop">Workshop</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Exhibition">Exhibition</option>
                        <option value="Competition">Competition</option>
                        <option value="Sports">Sports</option>
                        <option value="Cultural">Cultural</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ev_status">Status *</label>
                    <select id="ev_status" name="status" required>
                        <option value="Upcoming">Upcoming</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="ev_date">Event Date *</label>
                    <input type="date" id="ev_date" name="event_date" required value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="ev_venue">Venue / Location *</label>
                    <input type="text" id="ev_venue" name="venue" required placeholder="Auditorium Hall A">
                </div>
            </div>

            <!-- Poster File Upload with Validation (Practical 12) -->
            <div class="form-group">
                <label for="ev_poster">Event Poster Image (Max 2MB - JPG, PNG, WEBP)</label>
                <input type="file" id="ev_poster" name="poster" accept=".jpg,.jpeg,.png,.webp,.gif">
                <small style="color: var(--text-muted); font-size: 12px;">Validated on server for file extension and size.</small>
            </div>

            <div class="form-group">
                <label for="ev_desc">Event Description *</label>
                <textarea id="ev_desc" name="description" required placeholder="Provide an informative summary of this event..."></textarea>
            </div>

            <div class="flex-row">
                <button type="submit" id="evSubmitBtn" class="btn btn-primary">
                    🚀 Save Event Record
                </button>
                <button type="button" class="btn btn-secondary" onclick="resetEvForm()">
                    Reset Form
                </button>
            </div>
        </form>
    </section>
</main>

<script>
document.querySelectorAll('.edit-ev-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('evFormHeading').textContent = '✏️ Edit Event (#' + this.dataset.id + ')';
        document.getElementById('ev_form_action').value = 'edit';
        document.getElementById('ev_event_id').value = this.dataset.id;
        document.getElementById('ev_title').value = this.dataset.title;
        document.getElementById('ev_category').value = this.dataset.category;
        document.getElementById('ev_date').value = this.dataset.date;
        document.getElementById('ev_venue').value = this.dataset.venue;
        document.getElementById('ev_status').value = this.dataset.status;
        document.getElementById('ev_desc').value = this.dataset.desc;
        document.getElementById('evSubmitBtn').textContent = '💾 Update Event Record';
        document.getElementById('eventFormSection').scrollIntoView({behavior: 'smooth'});
    });
});

function resetEvForm() {
    document.getElementById('evFormHeading').textContent = '➕ Add New Event & Upload Poster';
    document.getElementById('ev_form_action').value = 'add';
    document.getElementById('ev_event_id').value = '';
    document.getElementById('eventCrudForm').reset();
    document.getElementById('evSubmitBtn').textContent = '🚀 Save Event Record';
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
