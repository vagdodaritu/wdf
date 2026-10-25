<?php
/**
 * Practical 11: Student Management CRUD with Search, Filter & MySQLi Statements
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// RBAC: Restricted to Admin
check_auth('admin');

$pageTitle = "Manage Students";
$successMsg = "";
$errorMsg = "";

// 1. Export to CSV (Practical 11 Intermediate Extension)
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=students_export_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Student ID', 'Full Name', 'Email', 'Mobile', 'Department', 'Year', 'Gender', 'Created At']);

    $res = $conn->query("SELECT * FROM students ORDER BY id ASC");
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['student_id'],
            $row['name'],
            $row['email'],
            $row['mobile'],
            $row['course'],
            $row['year'],
            $row['gender'],
            $row['created_at']
        ]);
    }
    fclose($output);
    exit();
}

// 2. Handle Delete Student
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = intval($_GET['id']);
    
    // Fetch student name for audit log
    $stmtName = $conn->prepare("SELECT name FROM students WHERE id = ?");
    $stmtName->bind_param("i", $deleteId);
    $stmtName->execute();
    $nameRes = $stmtName->get_result();
    $stName = ($r = $nameRes->fetch_assoc()) ? $r['name'] : "ID #$deleteId";
    $stmtName->close();

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        // Audit log
        $logAction = "Admin deleted student: " . $stName;
        $adminUser = $_SESSION['username'];
        $role = "admin";
        $logStmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, ?)");
        $logStmt->bind_param("sss", $logAction, $adminUser, $role);
        $logStmt->execute();
        $logStmt->close();

        $successMsg = "Student record deleted successfully.";
    } else {
        $errorMsg = "Failed to delete student: " . $stmt->error;
    }
    $stmt->close();
}

// 3. Handle Add / Edit Student (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = isset($_POST['form_action']) ? $_POST['form_action'] : 'add';
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $mobile = htmlspecialchars(trim($_POST['mobile'] ?? ''));
    $course = htmlspecialchars(trim($_POST['course'] ?? ''));
    $year = htmlspecialchars(trim($_POST['year'] ?? ''));
    $gender = htmlspecialchars(trim($_POST['gender'] ?? 'Male'));

    if (empty($name) || empty($email) || empty($mobile) || empty($course) || empty($year)) {
        $errorMsg = "All student fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errorMsg = "Mobile must be 10 digits.";
    } else {
        if ($action === 'add') {
            // Check email duplication
            $chk = $conn->prepare("SELECT id FROM students WHERE email = ?");
            $chk->bind_param("s", $email);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) {
                $errorMsg = "A student with this email address already exists.";
                $chk->close();
            } else {
                $chk->close();
                $studentId = "ST" . date('Y') . sprintf("%03d", rand(100, 999));
                $stmt = $conn->prepare("INSERT INTO students (student_id, name, email, mobile, course, year, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $studentId, $name, $email, $mobile, $course, $year, $gender);

                if ($stmt->execute()) {
                    // Audit log
                    $logAction = "Admin added student: " . $name;
                    $logStmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, 'admin')");
                    $logStmt->bind_param("ss", $logAction, $_SESSION['username']);
                    $logStmt->execute();
                    $logStmt->close();

                    $successMsg = "Student '$name' added successfully!";
                } else {
                    $errorMsg = "Failed to add student: " . $stmt->error;
                }
                $stmt->close();
            }
        } elseif ($action === 'edit') {
            $studentIdNum = intval($_POST['student_id_num'] ?? 0);
            if ($studentIdNum > 0) {
                $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, mobile = ?, course = ?, year = ?, gender = ? WHERE id = ?");
                $stmt->bind_param("ssssssi", $name, $email, $mobile, $course, $year, $gender, $studentIdNum);

                if ($stmt->execute()) {
                    // Audit log
                    $logAction = "Admin updated student ID #$studentIdNum: " . $name;
                    $logStmt = $conn->prepare("INSERT INTO audit_logs (action, username, role) VALUES (?, ?, 'admin')");
                    $logStmt->bind_param("ss", $logAction, $_SESSION['username']);
                    $logStmt->execute();
                    $logStmt->close();

                    $successMsg = "Student record updated successfully!";
                } else {
                    $errorMsg = "Failed to update student: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// 4. Search and Filter Query Building (Practical 11)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$courseFilter = isset($_GET['filter_course']) ? trim($_GET['filter_course']) : '';
$yearFilter = isset($_GET['filter_year']) ? trim($_GET['filter_year']) : '';

$sql = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR student_id LIKE ? OR course LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "ssss";
}

if (!empty($courseFilter)) {
    $sql .= " AND course = ?";
    $params[] = $courseFilter;
    $types .= "s";
}

if (!empty($yearFilter)) {
    $sql .= " AND year = ?";
    $params[] = $yearFilter;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$studentsResult = $stmt->get_result();

require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <div class="flex-between">
            <div>
                <h2>Student Records Management (CRUD)</h2>
                <p>Add, View, Edit, Delete, Search & Filter Student Records (Practical 11).</p>
            </div>
            <div class="flex-row">
                <a href="manage_students.php?action=export" class="btn btn-secondary btn-sm">📥 Export to CSV</a>
                <button onclick="document.getElementById('addStudentSection').scrollIntoView({behavior: 'smooth'})" class="btn btn-primary btn-sm">➕ Add New Student</button>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <!-- Search & Filter Controls (Practical 11) -->
    <section class="card" style="padding: 16px;">
        <form method="GET" action="manage_students.php" class="search-filter-box" style="margin-bottom: 0; border: none; padding: 0;">
            <input type="text" name="search" placeholder="🔍 Search by name, ID, email, or course..." value="<?php echo htmlspecialchars($search); ?>">

            <select name="filter_course">
                <option value="">All Courses</option>
                <option value="Computer Engineering" <?php echo ($courseFilter == 'Computer Engineering') ? 'selected' : ''; ?>>Computer Engineering</option>
                <option value="IT Engineering" <?php echo ($courseFilter == 'IT Engineering') ? 'selected' : ''; ?>>IT Engineering</option>
                <option value="AI & ML" <?php echo ($courseFilter == 'AI & ML') ? 'selected' : ''; ?>>AI & ML</option>
                <option value="Mechanical Engineering" <?php echo ($courseFilter == 'Mechanical Engineering') ? 'selected' : ''; ?>>Mechanical Engineering</option>
                <option value="Civil Engineering" <?php echo ($courseFilter == 'Civil Engineering') ? 'selected' : ''; ?>>Civil Engineering</option>
            </select>

            <select name="filter_year">
                <option value="">All Years</option>
                <option value="1st Year" <?php echo ($yearFilter == '1st Year') ? 'selected' : ''; ?>>1st Year</option>
                <option value="2nd Year" <?php echo ($yearFilter == '2nd Year') ? 'selected' : ''; ?>>2nd Year</option>
                <option value="3rd Year" <?php echo ($yearFilter == '3rd Year') ? 'selected' : ''; ?>>3rd Year</option>
                <option value="4th Year" <?php echo ($yearFilter == '4th Year') ? 'selected' : ''; ?>>4th Year</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if (!empty($search) || !empty($courseFilter) || !empty($yearFilter)): ?>
                <a href="manage_students.php" class="btn btn-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </section>

    <!-- Student Table (Practical 11) -->
    <section class="card">
        <div class="flex-between" style="margin-bottom: 15px;">
            <h3 style="color: var(--primary-color);">Enrolled Students List</h3>
            <span style="font-size: 13.5px; color: var(--text-muted);">
                Total: <strong><?php echo $studentsResult->num_rows; ?></strong> records found (Minimum 20 seeded records requirement met)
            </span>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($studentsResult->num_rows === 0): ?>
                        <tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 25px;">No student records match your query.</td></tr>
                    <?php else: ?>
                        <?php while ($st = $studentsResult->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $st['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($st['student_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($st['name']); ?></td>
                                <td><?php echo htmlspecialchars($st['email']); ?></td>
                                <td><?php echo htmlspecialchars($st['mobile']); ?></td>
                                <td><span class="badge" style="background: rgba(13,110,253,0.1); color: #0d6efd; padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($st['course']); ?></span></td>
                                <td><?php echo htmlspecialchars($st['year']); ?></td>
                                <td><?php echo htmlspecialchars($st['gender']); ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm edit-btn" 
                                            data-id="<?php echo $st['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($st['name']); ?>"
                                            data-email="<?php echo htmlspecialchars($st['email']); ?>"
                                            data-mobile="<?php echo htmlspecialchars($st['mobile']); ?>"
                                            data-course="<?php echo htmlspecialchars($st['course']); ?>"
                                            data-year="<?php echo htmlspecialchars($st['year']); ?>"
                                            data-gender="<?php echo htmlspecialchars($st['gender']); ?>">
                                        ✏️ Edit
                                    </button>
                                    <a href="manage_students.php?action=delete&id=<?php echo $st['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete student: <?php echo htmlspecialchars(addslashes($st['name'])); ?>?');">
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

    <!-- Add / Edit Student Form (Practical 11) -->
    <section id="addStudentSection" class="card" style="max-width: 700px; margin: auto;">
        <h3 id="formHeading" style="color: var(--primary-color); margin-bottom: 15px;">➕ Add New Student Record</h3>
        
        <form method="POST" action="manage_students.php" id="studentCrudForm">
            <input type="hidden" name="form_action" id="form_action" value="add">
            <input type="hidden" name="student_id_num" id="student_id_num" value="">

            <div class="grid-2">
                <div class="form-group">
                    <label for="crud_name">Full Name *</label>
                    <input type="text" id="crud_name" name="name" required placeholder="e.g. Diya Mehta">
                </div>

                <div class="form-group">
                    <label for="crud_email">Student Email *</label>
                    <input type="email" id="crud_email" name="email" required placeholder="diya@studenthub.com">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="crud_mobile">10-Digit Mobile *</label>
                    <input type="tel" id="crud_mobile" name="mobile" required maxlength="10" placeholder="9876543201">
                </div>

                <div class="form-group">
                    <label for="crud_gender">Gender *</label>
                    <select id="crud_gender" name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="crud_course">Department / Course *</label>
                    <select id="crud_course" name="course" required>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="IT Engineering">IT Engineering</option>
                        <option value="AI & ML">AI & ML</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                        <option value="Civil Engineering">Civil Engineering</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="crud_year">Academic Year *</label>
                    <select id="crud_year" name="year" required>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>
            </div>

            <div class="flex-row" style="margin-top: 10px;">
                <button type="submit" id="crudSubmitBtn" class="btn btn-primary">
                    💾 Save Student Record
                </button>
                <button type="button" id="crudResetBtn" class="btn btn-secondary" onclick="resetForm()">
                    Reset Form
                </button>
            </div>
        </form>
    </section>
</main>

<script>
// Edit button click handler
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('formHeading').textContent = '✏️ Edit Student Record (#' + this.dataset.id + ')';
        document.getElementById('form_action').value = 'edit';
        document.getElementById('student_id_num').value = this.dataset.id;
        document.getElementById('crud_name').value = this.dataset.name;
        document.getElementById('crud_email').value = this.dataset.email;
        document.getElementById('crud_mobile').value = this.dataset.mobile;
        document.getElementById('crud_course').value = this.dataset.course;
        document.getElementById('crud_year').value = this.dataset.year;
        document.getElementById('crud_gender').value = this.dataset.gender;
        document.getElementById('crudSubmitBtn').textContent = '💾 Update Student Record';
        document.getElementById('addStudentSection').scrollIntoView({behavior: 'smooth'});
    });
});

function resetForm() {
    document.getElementById('formHeading').textContent = '➕ Add New Student Record';
    document.getElementById('form_action').value = 'add';
    document.getElementById('student_id_num').value = '';
    document.getElementById('studentCrudForm').reset();
    document.getElementById('crudSubmitBtn').textContent = '💾 Save Student Record';
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
