<?php
/**
 * Practical 2 & 3: About Page
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 */

$pageTitle = "About";
require_once __DIR__ . '/header.php';
?>

<main>
    <div class="page-header">
        <h2>About StudentHub</h2>
        <p>Centralized digital portal for students and faculty members.</p>
    </div>

    <section class="card">
        <div class="grid-2" style="align-items: center;">
            <div>
                <h3 style="color: var(--primary-color); margin-bottom: 12px;">Our Purpose</h3>
                <p style="margin-bottom: 12px; color: var(--text-color);">
                    StudentHub is a comprehensive college portal designed to eliminate the fragmentation of student academic information. Traditionally, students have to navigate separate portals for events, attendance, notices, and course materials.
                </p>
                <p style="color: var(--text-color);">
                    Developed as part of the <strong>Web Development Frameworks (ITUE203)</strong> curriculum at Charotar University of Science and Technology (CHARUSAT), StudentHub brings these resources together in a modern, accessible, and secure platform.
                </p>
            </div>
            <div style="text-align: center;">
                <img src="images/campus.png" alt="CHARUSAT Campus View" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: var(--shadow);">
            </div>
        </div>
    </section>

    <section>
        <div class="grid-2">
            <article class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">🎯 Our Mission</h3>
                <p style="color: var(--text-muted); font-size: 14.5px;">
                    To deliver a responsive, accessible, and intuitive web application that simplifies college communication, enables rapid event discovery, and supports transparent student academic tracking.
                </p>
            </article>

            <article class="card">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">🌟 Core Values</h3>
                <p style="color: var(--text-muted); font-size: 14.5px;">
                    Built with semantic HTML5 for high accessibility, modular CSS Grid and Flexbox for mobile responsiveness, client-side validation for seamless UX, and robust PHP/MySQL backend security.
                </p>
            </article>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
