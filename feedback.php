<?php
/**
 * Practical 7 (JSON Storage Variant): Feedback Processing with JSON File Storage
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

$pageTitle = "Portal Feedback";
require_once __DIR__ . '/header.php';

$successMsg = "";
$errorMsg = "";
$jsonFile = __DIR__ . '/data/feedback.json';

// Handle POST Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $rating = isset($_POST['rating']) ? htmlspecialchars(trim($_POST['rating'])) : 'Excellent';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    if (empty($name) || empty($email) || empty($message)) {
        $errorMsg = "Please fill in all required feedback fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Please enter a valid email address.";
    } else {
        // Read existing JSON
        $existingFeedback = [];
        if (file_exists($jsonFile)) {
            $jsonContent = file_get_contents($jsonFile);
            $existingFeedback = json_decode($jsonContent, true) ?: [];
        }

        // Add new record
        $newEntry = [
            'name' => $name,
            'email' => $email,
            'rating' => $rating,
            'message' => $message,
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        $existingFeedback[] = $newEntry;

        // Save back to JSON
        if (file_put_contents($jsonFile, json_encode($existingFeedback, JSON_PRETTY_PRINT))) {
            $successMsg = "Thank you! Your feedback has been stored successfully in data/feedback.json.";
        } else {
            $errorMsg = "Could not write to JSON file. Please check file permissions.";
        }
    }
}

// Read JSON records for display
$allFeedback = [];
if (file_exists($jsonFile)) {
    $allFeedback = json_decode(file_get_contents($jsonFile), true) ?: [];
}
?>

<main>
    <div class="page-header">
        <h2>Student Feedback Portal</h2>
        <p>PHP Form Processing with Server-Side Validation and JSON File Storage (Practical 7).</p>
    </div>

    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success">✅ <?php echo $successMsg; ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger">⚠️ <?php echo $errorMsg; ?></div>
    <?php endif; ?>

    <div class="grid-2">
        <section class="card">
            <h3 style="color: var(--primary-color); margin-bottom: 15px;">Share Your Experience</h3>
            <form method="POST" action="feedback.php">
                <div class="form-group">
                    <label for="name">Student Name *</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your name"
                           value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Student Email *</label>
                    <input type="email" id="email" name="email" required placeholder="name@studenthub.com">
                </div>

                <div class="form-group">
                    <label for="rating">Rating *</label>
                    <select id="rating" name="rating">
                        <option value="Excellent">⭐⭐⭐⭐⭐ Excellent</option>
                        <option value="Good">⭐⭐⭐⭐ Good</option>
                        <option value="Average">⭐⭐⭐ Average</option>
                        <option value="Needs Improvement">⭐⭐ Needs Improvement</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Feedback Comments *</label>
                    <textarea id="message" name="message" required placeholder="What do you think about StudentHub? Any suggestions?"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    📤 Submit Feedback (Save to JSON)
                </button>
            </form>
        </section>

        <section class="card">
            <h3 style="color: var(--primary-color); margin-bottom: 12px;">Recent Feedbacks (from <code>data/feedback.json</code>)</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">
                Demonstrating server-side retrieval of JSON records stored via PHP.
            </p>

            <div style="display: flex; flex-direction: column; gap: 12px; max-height: 400px; overflow-y: auto;">
                <?php if (empty($allFeedback)): ?>
                    <p style="color: var(--text-muted);">No feedback records yet.</p>
                <?php else: ?>
                    <?php foreach (array_reverse(array_slice($allFeedback, -5)) as $fb): ?>
                        <div style="border-left: 3px solid var(--primary-color); padding: 10px 14px; background: rgba(0,0,0,0.02); border-radius: 4px;">
                            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                                <strong><?php echo htmlspecialchars($fb['name']); ?></strong>
                                <span style="color: var(--primary-color); font-weight: bold;"><?php echo htmlspecialchars($fb['rating']); ?></span>
                            </div>
                            <p style="font-size: 13.5px; margin: 6px 0; color: var(--text-color);"><?php echo htmlspecialchars($fb['message']); ?></p>
                            <small style="color: var(--text-muted);"><?php echo htmlspecialchars($fb['submitted_at']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
