<?php
/**
 * Practical 7: PHP Form Processing with Server-Side Validation & CSV File Storage
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

$pageTitle = "Contact Support";
require_once __DIR__ . '/header.php';

$successMsg = "";
$errorMsg = "";

// Check if form submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Sanitize inputs
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // 2. Server-side validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $errorMsg = "All fields are required. Please complete the form.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Please enter a valid email address.";
    } elseif (strlen($name) < 3) {
        $errorMsg = "Name must be at least 3 characters long.";
    } elseif (strlen($message) < 5) {
        $errorMsg = "Message must be at least 5 characters long.";
    } else {
        // 3. Safe CSV file writing
        $csvFile = __DIR__ . '/data/contacts.csv';
        $submittedAt = date('Y-m-d H:i:s');

        // Check if file exists to write header
        $writeHeader = !file_exists($csvFile);

        $fp = @fopen($csvFile, 'a');
        if ($fp) {
            if ($writeHeader) {
                fputcsv($fp, ['Name', 'Email', 'Subject', 'Message', 'SubmittedAt']);
            }
            fputcsv($fp, [$name, $email, $subject, $message, $submittedAt]);
            fclose($fp);
            $successMsg = "Thank you, $name! Your message has been received and stored in our records (data/contacts.csv).";
        } else {
            $errorMsg = "Failed to write to file storage. Please check permissions.";
        }
    }
}

// Read stored CSV records for display (Intermediate Extension)
$csvRecords = [];
$csvFile = __DIR__ . '/data/contacts.csv';
if (file_exists($csvFile)) {
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $header = fgetcsv($handle); // Read header
        while (($data = fgetcsv($handle)) !== FALSE) {
            $csvRecords[] = $data;
        }
        fclose($handle);
    }
}
?>

<main>
    <div class="page-header">
        <h2>Contact Student Support Desk</h2>
        <p>PHP Form Processing with Server-Side Validation and CSV File Storage (Practical 7).</p>
    </div>

    <!-- Alert Messages (Practical 7) -->
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success">
            ✅ <?php echo $successMsg; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger">
            ⚠️ <?php echo $errorMsg; ?>
        </div>
    <?php endif; ?>

    <div class="grid-2">
        <!-- Contact Form (Practical 2 & 7) -->
        <section class="card">
            <h3 style="color: var(--primary-color); margin-bottom: 15px;">Send a Message</h3>
            
            <form method="POST" action="contact.php">
                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required 
                           value="<?php echo isset($_POST['name']) && empty($successMsg) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" placeholder="Enter your college email" required
                           value="<?php echo isset($_POST['email']) && empty($successMsg) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input type="text" id="subject" name="subject" placeholder="Inquiry topic or issue" required
                           value="<?php echo isset($_POST['subject']) && empty($successMsg) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" placeholder="Describe your question or feedback in detail..." required><?php echo isset($_POST['message']) && empty($successMsg) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    📨 Submit Message (POST to CSV)
                </button>
            </form>
        </section>

        <!-- Campus Contact Details (Practical 2) -->
        <section>
            <div class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 12px;">Campus Support Office</h3>
                <p style="color: var(--text-muted); font-size: 14.5px; margin-bottom: 15px;">
                    Our student support desk is available Monday through Saturday from 9:00 AM to 5:00 PM for academic inquiries and account support.
                </p>

                <div style="font-size: 14px; line-height: 2;">
                    <strong>📍 Location:</strong> Faculty of Technology & Engineering, CHARUSAT<br>
                    <strong>📞 Phone:</strong> <a href="tel:+919876543210">+91 98765 43210</a><br>
                    <strong>✉️ Email:</strong> <a href="mailto:support@studenthub.com">support@studenthub.com</a><br>
                    <strong>🏢 Department:</strong> Student Affairs & Academic Records
                </div>
            </div>

            <!-- CSV Stored Records Display (Practical 7 Extension) -->
            <div class="card" style="margin-top: 20px;">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">Recent Submissions (from <code>data/contacts.csv</code>)</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
                    Practical 7 Extension: Demonstrating data retrieved from server CSV file storage.
                </p>

                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table class="data-table" style="font-size: 12.5px;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($csvRecords)): ?>
                                <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No records in CSV yet.</td></tr>
                            <?php else: ?>
                                <?php foreach (array_reverse(array_slice($csvRecords, -5)) as $row): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($row[0] ?? ''); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row[2] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($row[4] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
