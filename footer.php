<?php
/**
 * Shared Footer Component
 * Practical 2: Semantic HTML5 Footer & Accessibility
 * Practical 4: JavaScript Includes & Interactivity
 */
?>
    <!-- Semantic Footer (Practical 2) -->
    <footer>
        <div style="max-width: 1150px; margin: auto; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px; align-items: center;">
            <p>&copy; <?php echo date('Y'); ?> StudentHub Portal. Charotar University of Science and Technology (CHARUSAT).</p>
            <p>
                <a href="about.php">About</a> &bull; 
                <a href="faq.php">FAQs</a> &bull; 
                <a href="contact.php">Contact Support</a> &bull; 
                <a href="feedback.php">Feedback</a>
            </p>
        </div>
    </footer>

    <!-- Interactive Information Modal (Practical 4) -->
    <div id="infoModal" class="modal-overlay">
        <div class="modal-box">
            <button id="closeModalBtn" class="modal-close-btn" aria-label="Close Modal">&times;</button>
            <h3 id="modalTitle">Portal Announcement</h3>
            <p id="modalBody" style="margin-top: 10px; line-height: 1.6; color: var(--text-muted);"></p>
            <div style="margin-top: 20px; text-align: right;">
                <button id="modalOkBtn" class="btn btn-primary btn-sm">Understood</button>
            </div>
        </div>
    </div>

    <!-- Core Interactive Scripts (Practical 4) -->
    <script src="js/main.js"></script>
</body>
</html>
