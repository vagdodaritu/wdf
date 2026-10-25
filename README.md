# 🎓 StudentHub Portal
**Subject:** Web Development Frameworks (ITUE203)  
**Curriculum:** B.Tech Semester 3 (Odd 2026-27), CHARUSAT - Faculty of Technology and Engineering (FTE)

---

## 📌 Project Overview
StudentHub is a centralized college portal developed to provide students and faculty with streamlined access to academic resources, campus event management, student directory management, and administrative services.

The codebase strictly adheres to the **ITUE203 Practical List (Practicals 1 to 15)** using clean HTML5, CSS3 (Grid & Flexbox), vanilla JavaScript (ES6+), and procedural/OOP PHP with MySQLi and PDO.

---

## 🗺️ Practical List Mapping (Practicals 1 to 15)

| Practical No. | Title / Concept | Implementation in Codebase |
|---|---|---|
| **Practical 1** | Requirement Analysis, Sitemap, Wireframe & GitHub Setup | `wireframes/`, `README.md`, sitemap navigation |
| **Practical 2** | Semantic HTML5 Pages with Accessibility-Ready Structure | Semantic tags (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`) in `html/` & `*.php` |
| **Practical 3** | Responsive UI Design using CSS Grid and Flexbox | `css/style.css` (Grid & Flexbox layouts, mobile-first media queries) |
| **Practical 4** | JavaScript DOM Manipulation, Event Handling & Interactivity | `js/main.js` & `js/faq.js` (Accordion, slider, modal, notification banner, dark mode switcher with `localStorage`) |
| **Practical 5** | Registration Form with Frontend Regex Validation & Strength Meter | `js/register.js` & `register.php` (client-side validation for name, email, mobile, password strength meter, course, terms) |
| **Practical 6** | Rendering External JSON Data using Fetch API, Search & Filter | `data/events.json`, `data/students.json`, `data/faqs.json` (15+ records each), rendered in `events.php` via `js/fetch_data.js` |
| **Practical 7** | PHP Form Processing with Server Validation & CSV/JSON File Storage | `contact.php` (stores to `data/contacts.csv`) & `feedback.php` (stores to `data/feedback.json`) |
| **Practical 8** | MySQL Schema Design, ER Model, PDO/MySQLi Connectivity | `database.sql` (schema & seed records) and `db.php` (MySQLi & PDO connection) |
| **Practical 9** | Secure User Registration with Database Insert, Duplicate Check & Hashing | `register.php` (duplicate checks, `password_hash()`, prepared statements) |
| **Practical 10** | Secure Login/Logout with Sessions, Role-Based Access, Timeout & Cookies | `login.php`, `logout.php`, `auth.php` (`password_verify()`, `session_regenerate_id()`, 15-min timeout, remember-me) |
| **Practical 11** | Student Management CRUD with Search, Filter & MySQLi Statements | `manage_students.php` (Add, View, Edit, Delete with confirmation, search, filter, CSV export, 21 seeded records) |
| **Practical 12** | Event Management CRUD with Poster Upload & File Validation | `manage_events.php` (MIME type & size validation, upload to `uploads/`, add, edit, delete events) |
| **Practical 13** | PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload | `api/events_api.php` (RESTful JSON endpoint) & `ajax_events.php` / `js/ajax_crud.js` (Fetch API async CRUD) |
| **Practical 14** | Admin Dashboard with Role-Based Access Control, Dynamic Menu & Audit Log | `admin_dashboard.php` (live SQL metrics, recent registrations, dynamic role navbar, audit trail from `audit_logs`) |
| **Practical 15** | Final Integration, Testing, Documentation & Local XAMPP Deployment | Complete verified integrated portal in `C:\xampp\htdocs\wdfphppract/` |

---

## 💻 Tech Stack & Compatibility
- **Web Server:** Apache (via XAMPP)
- **Backend:** PHP 8.x (using MySQLi & PDO)
- **Database:** MySQL / MariaDB (via phpMyAdmin)
- **Frontend:** Semantic HTML5, CSS3 (CSS Grid & Flexbox), Vanilla JavaScript (Fetch API, DOM events, `localStorage`)
- **Storage:** MySQL Relational Database, Flat-file CSV (`data/contacts.csv`), Flat-file JSON (`data/feedback.json`)

---

## 🚀 How to Run on Local XAMPP

### 1. Database Setup
1. Start **Apache** and **MySQL** from the **XAMPP Control Panel**.
2. Open **phpMyAdmin** in your browser: `http://localhost/phpmyadmin/`.
3. Create a new database named `studenthub` (or `pract8`).
4. Click on **Import** and select the file `database.sql` from this project.
5. Click **Go** to create the tables and insert seeded records.

### 2. Project Directory in XAMPP
The project is hosted inside XAMPP `htdocs`:
```text
C:\xampp\htdocs\wdfphppract\
```

### 3. Open in Web Browser
Open your browser and navigate to:
```text
http://localhost/wdfphppract/
```

---

## 🔑 Default Login Credentials

| Role | Username / Email | Password | Redirect Target |
|---|---|---|---|
| **Admin** | `admin` (or `admin@studenthub.com`) | `admin123` | `admin_dashboard.php` |
| **Student** | `student` (or `student@studenthub.com`) | `student123` | `dashboard.php` |
| **Student (Manali)** | `manali` (or `manali@studenthub.com`) | `manali123` | `dashboard.php` |

---

## 📁 Project Directory Structure
```text
wdfphppract/
├── api/
│   └── events_api.php           # Practical 13: PHP JSON API Endpoint
├── css/
│   └── style.css                # Practical 3: Responsive CSS Grid & Flexbox
├── data/
│   ├── contacts.csv             # Practical 7: CSV Form Storage
│   ├── feedback.json            # Practical 7: JSON Form Storage
│   ├── events.json              # Practical 6: 15 External Event records
│   ├── students.json            # Practical 6: 15 External Student records
│   └── faqs.json                # Practical 6: 15 External FAQ records
├── html/                        # Practical 2: Semantic HTML5 Static Templates
│   ├── home.html
│   ├── about.html
│   ├── events.html
│   ├── faq.html
│   ├── contact.html
│   ├── register.html
│   ├── login.html
│   ├── dashboard.html
│   ├── profile.html
│   ├── feedback.html
│   └── admin.html
├── images/
│   ├── logo.png
│   ├── campus.png
│   └── astro.jpg
├── js/
│   ├── main.js                  # Practical 4: Slider, modal, theme switcher, banner
│   ├── faq.js                   # Practical 4: Collapsible accordion
│   ├── register.js              # Practical 5: Regex validation & password meter
│   ├── fetch_data.js            # Practical 6: Fetch API JSON render, search & pagination
│   └── ajax_crud.js             # Practical 13: Asynchronous Fetch CRUD
├── uploads/                     # Practical 12: Validated uploaded event posters
├── wireframes/                  # Practical 1: Wireframe design diagrams
├── about.php                    # About page
├── admin_dashboard.php          # Practical 14: Admin Dashboard with RBAC & Audit Log
├── ajax_events.php              # Practical 13: Fetch API Async CRUD Page
├── auth.php                     # Practical 10: Auth verification, session timeout helper
├── contact.php                  # Practical 7: Contact Form with CSV storage
├── dashboard.php                # Practical 10: Student Area Dashboard
├── database.sql                 # Practical 8: Complete SQL Schema & Seed Data
├── db.php                       # Practical 8: Database Connection (MySQLi + PDO)
├── events.php                   # Practical 6: Events directory with Fetch API
├── faq.php                      # Practical 4: Interactive Collapsible FAQ
├── feedback.php                 # Practical 7: Feedback Form with JSON storage
├── footer.php                   # Shared semantic footer & modal include
├── header.php                   # Practical 14: Shared semantic header & dynamic role menu
├── index.php                    # Public Home Page
├── login.php                    # Practical 10: Secure User Login with Sessions
├── logout.php                   # Practical 10: Secure Session Destroy & Logout
├── manage_events.php            # Practical 12: Event CRUD with poster upload
├── manage_students.php          # Practical 11: Student CRUD with search, filter & CSV export
├── profile.php                  # Student Profile View
├── register.php                 # Practical 5 & 9: Full Registration Module
└── README.md                    # Project Documentation
```
