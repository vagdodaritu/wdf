<?php
/**
 * Practical 2 & Practical 4: Frequently Asked Questions (FAQ)
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 * Features: Collapsible FAQ Accordion Component via JavaScript DOM Manipulation
 */

$pageTitle = "FAQ";
require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <h2>Frequently Asked Questions</h2>
        <p>Click on any question below to expand or collapse the answer (Practical 4: Interactive Accordion).</p>
    </div>

    <section class="card">
        <div class="faq-list">
            <article class="faq-item">
                <button class="faq-question">What is the StudentHub Portal?</button>
                <div class="faq-answer">
                    StudentHub is a centralized college web application that provides students with access to academic resources, event announcements, student directories, and administrative services.
                </div>
            </article>

            <article class="faq-item">
                <button class="faq-question">How do I create a new student account?</button>
                <div class="faq-answer">
                    Click the <strong>Register</strong> button in the navigation bar. Fill out your full name, student email, 10-digit mobile number, course, year, and secure password. The form features real-time client-side validation (Practical 5) and backend password hashing (Practical 9).
                </div>
            </article>

            <article class="faq-item">
                <button class="faq-question">How does StudentHub secure user passwords?</button>
                <div class="faq-answer">
                    Passwords are never saved in plain text. StudentHub employs PHP's native <code>password_hash()</code> function with the strong bcrypt algorithm before persisting credentials into the MySQL database.
                </div>
            </article>

            <article class="faq-item">
                <button class="faq-question">What is the automatic session timeout policy?</button>
                <div class="faq-answer">
                    For student data security (Practical 10), any active session is automatically terminated after 15 minutes of user inactivity. You will be redirected to the login screen with a friendly notification.
                </div>
            </article>

            <article class="faq-item">
                <button class="faq-question">How does the Events directory load data?</button>
                <div class="faq-answer">
                    The Events page demonstrates <strong>Practical 6</strong>: asynchronous data fetching from an external <code>data/events.json</code> file using the JavaScript Fetch API, with real-time keyword search, category filtering, and client-side pagination.
                </div>
            </article>

            <article class="faq-item">
                <button class="faq-question">How can I switch between Light and Dark mode?</button>
                <div class="faq-answer">
                    Click the <strong>Dark Mode / Light Mode</strong> button in the top right corner of the header. Your preference is automatically stored in your browser's <code>localStorage</code> so it persists across page reloads (Practical 4).
                </div>
            </article>

            <article class="faq-item">
                <button class="faq-question">Where are Contact form submissions saved?</button>
                <div class="faq-answer">
                    As specified in <strong>Practical 7</strong>, contact submissions are sanitized on the server and written to a secure <code>data/contacts.csv</code> file using PHP's file-handling functions.
                </div>
            </article>

            <article class="faq-item">
                <button class="faq-question">How do administrators manage students and events?</button>
                <div class="faq-answer">
                    Administrators have access to dedicated CRUD management panels (Practicals 11 & 12) featuring MySQLi prepared queries, student searching/filtering, and event poster file uploads with MIME validation.
                </div>
            </article>
        </div>
    </section>

    <section class="card" style="text-align: center;">
        <h3 style="color: var(--primary-color); margin-bottom: 8px;">Still have questions?</h3>
        <p style="color: var(--text-muted); margin-bottom: 15px;">Can't find the answer you're looking for? Feel free to reach out to our team.</p>
        <a href="contact.php" class="btn btn-primary">Contact Support &rarr;</a>
    </section>
</main>

<!-- Accordion Interaction Script (Practical 4) -->
<script src="js/faq.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
