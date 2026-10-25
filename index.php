<?php
/**
 * Practical 1, 2, 3, 4: StudentHub Home Page
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

$pageTitle = "Home";
require_once __DIR__ . '/header.php';
?>

<main>
    <!-- Content Slider (Practical 4 - Image / Content Slider) -->
    <div class="slider-container">
        <div class="slide active" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.6)), url('images/campus.png');">
            <div class="slide-caption">
                <h3>Welcome to StudentHub Portal</h3>
                <p>A centralized platform for student academics, events, and campus communication.</p>
            </div>
        </div>
        <div class="slide" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.6)), url('images/astro.jpg');">
            <div class="slide-caption">
                <h3>Explore Upcoming College Events</h3>
                <p>Register for hackathons, technical workshops, and campus cultural fests.</p>
            </div>
        </div>
        <div class="slide" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.6)), url('images/campus.png');">
            <div class="slide-caption">
                <h3>Fast & Seamless Academic Access</h3>
                <p>View your personalized dashboard, course notices, and attendance records.</p>
            </div>
        </div>
        <button id="sliderPrevBtn" class="slider-btn prev" aria-label="Previous Slide">‹</button>
        <button id="sliderNextBtn" class="slider-btn next" aria-label="Next Slide">›</button>
    </div>

    <!-- Introduction Section (Practical 2 - Semantic HTML5) -->
    <section class="card">
        <div class="flex-between">
            <div>
                <h2 style="color: var(--primary-color);">Welcome to StudentHub</h2>
                <p style="color: var(--text-muted); margin-top: 6px;">
                    StudentHub is designed to connect students with academic resources, college events, announcements, and campus activities in one unified interface.
                </p>
            </div>
            <div>
                <!-- Modal Trigger Button (Practical 4 - Modal Popup) -->
                <button class="btn btn-primary open-modal-trigger" 
                        data-modal-title="Welcome to StudentHub 2026-27" 
                        data-modal-text="StudentHub is currently active for CHARUSAT Sem-3 students. You can explore event schedules, search external JSON directories, or register a new student account!">
                    📌 View Important Notice
                </button>
            </div>
        </div>
    </section>

    <!-- Key Features Grid (Practical 3 - CSS Grid & Flexbox) -->
    <section>
        <div class="page-header">
            <h2>Key Portal Modules</h2>
            <p>Explore the features offered across the semester-long StudentHub system.</p>
        </div>

        <div class="grid-3">
            <article class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">📅 Campus Events</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px;">
                    Browse external JSON event records, filter by categories, and register for workshops.
                </p>
                <a href="events.php" class="btn btn-secondary btn-sm">Explore Events &rarr;</a>
            </article>

            <article class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">❓ Help & FAQs</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px;">
                    Find instant answers with our interactive collapsible accordion and college guidance.
                </p>
                <a href="faq.php" class="btn btn-secondary btn-sm">View FAQs &rarr;</a>
            </article>

            <article class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">📝 Student Registration</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px;">
                    Create a secure student account with frontend regex validation and hashed passwords.
                </p>
                <a href="register.php" class="btn btn-secondary btn-sm">Register Now &rarr;</a>
            </article>

            <article class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">🔐 Secure Login</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px;">
                    Role-based access control for students and administrators with auto-session timeout.
                </p>
                <a href="login.php" class="btn btn-secondary btn-sm">User Login &rarr;</a>
            </article>

            <article class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">✉️ Support & CSV Storage</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px;">
                    Submit contact messages processed by PHP and safely saved in CSV file storage.
                </p>
                <a href="contact.php" class="btn btn-secondary btn-sm">Contact Desk &rarr;</a>
            </article>

            <article class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">⚡ AJAX / Fetch CRUD</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px;">
                    Asynchronous data management without page reloads using our PHP JSON API.
                </p>
                <a href="ajax_events.php" class="btn btn-secondary btn-sm">Open AJAX CRUD &rarr;</a>
            </article>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
