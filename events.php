<?php
/**
 * Practical 6: Rendering External JSON Data using Fetch API, Search, Filter & Pagination
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

$pageTitle = "Events Directory";
require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <div class="flex-between">
            <div>
                <h2>College Events Directory</h2>
                <p>Dynamically loaded from external <code>data/events.json</code> via JavaScript Fetch API (Practical 6).</p>
            </div>
            <?php if (is_admin()): ?>
                <div>
                    <a href="manage_events.php" class="btn btn-primary btn-sm">Manage Events (CRUD) &rarr;</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Practical 6: Search, Filter, Sort Controls -->
    <section class="card" style="padding: 16px;">
        <div class="search-filter-box" style="margin-bottom: 0; border: none; padding: 0;">
            <input type="text" id="jsonSearchInput" placeholder="🔍 Search events by title, keyword, or venue..." aria-label="Search events">
            
            <select id="jsonCategoryFilter" aria-label="Filter by category">
                <option value="">All Categories</option>
            </select>

            <select id="jsonSortSelect" aria-label="Sort events">
                <option value="date-asc">📅 Date: Soonest First</option>
                <option value="date-desc">📅 Date: Latest First</option>
                <option value="title-asc">🔤 Title: A to Z</option>
                <option value="title-desc">🔤 Title: Z to A</option>
            </select>
        </div>
        <div style="margin-top: 10px; font-size: 13px; color: var(--text-muted); display: flex; justify-content: space-between;">
            <span id="jsonResultCount">Loading events...</span>
            <span>Dataset: <code>data/events.json</code> (15 records)</span>
        </div>
    </section>

    <!-- Loading & Error States (Practical 6) -->
    <div id="jsonLoadingState" style="text-align: center; padding: 40px; font-size: 16px; color: var(--primary-color);">
        ⏳ Loading events from external JSON data...
    </div>

    <div id="jsonErrorState" class="alert alert-danger" style="display: none;"></div>

    <!-- Render Container for Dynamic Event Cards (Practical 3 CSS Grid & Practical 6 Rendering) -->
    <section>
        <div id="jsonEventsContainer" class="grid-3">
            <!-- Event cards rendered dynamically by js/fetch_data.js -->
        </div>

        <!-- Dynamic Pagination Buttons (Practical 6) -->
        <div id="jsonPagination" class="pagination">
            <!-- Pagination buttons injected here -->
        </div>
    </section>
</main>

<!-- Practical 6 External JSON Fetch Script -->
<script src="js/fetch_data.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
