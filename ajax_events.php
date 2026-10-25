<?php
/**
 * Practical 13: AJAX / Fetch-Based CRUD Interface without Page Reload
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

$pageTitle = "AJAX CRUD (Fetch API)";
require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <div class="flex-between">
            <div>
                <h2>Asynchronous CRUD via Fetch API (Practical 13)</h2>
                <p>Real-time data interaction consuming <code>api/events_api.php</code> without full page reloads.</p>
            </div>
            <div>
                <a href="manage_events.php" class="btn btn-secondary btn-sm">Switch to Standard PHP CRUD &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Alert / Toast Status Message (Practical 13) -->
    <div id="ajaxAlert" class="alert" style="display: none;"></div>

    <div class="grid-2">
        <!-- Add Event Form (AJAX / Fetch API) -->
        <section class="card">
            <h3 style="color: var(--primary-color); margin-bottom: 15px;">➕ Add Event via AJAX POST</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">
                Submits payload to <code>api/events_api.php</code> and updates table instantly without page refresh.
            </p>

            <form id="ajaxAddEventForm">
                <div class="form-group">
                    <label for="ajax_title">Event Title *</label>
                    <input type="text" id="ajax_title" required placeholder="e.g. Next-Gen Tech Talk">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="ajax_category">Category *</label>
                        <select id="ajax_category" required>
                            <option value="Workshop">Workshop</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Exhibition">Exhibition</option>
                            <option value="Competition">Competition</option>
                            <option value="Cultural">Cultural</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ajax_date">Date *</label>
                        <input type="date" id="ajax_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="ajax_venue">Venue *</label>
                    <input type="text" id="ajax_venue" required placeholder="Seminar Room 101">
                </div>

                <div class="form-group">
                    <label for="ajax_desc">Event Description *</label>
                    <textarea id="ajax_desc" required placeholder="Brief description of the event..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    ⚡ Send Asynchronous POST Request
                </button>
            </form>
        </section>

        <!-- Dynamic Live Events Table (AJAX / Fetch API) -->
        <section class="card">
            <div class="flex-between" style="margin-bottom: 15px;">
                <h3 style="color: var(--primary-color);">Live Events Feed (Fetch API)</h3>
                <span style="font-size: 13px; color: var(--text-muted);">Asynchronous JSON Response</span>
            </div>

            <!-- Loading Indicator -->
            <div id="ajaxLoading" style="text-align: center; padding: 20px; color: var(--primary-color);">
                ⏳ Fetching records from <code>api/events_api.php</code>...
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ajaxEventsTableBody">
                        <!-- Loaded dynamically via Fetch API -->
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<!-- Practical 13 AJAX CRUD Script -->
<script src="js/ajax_crud.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
