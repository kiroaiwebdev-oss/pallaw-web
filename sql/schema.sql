-- =============================================================
--  NEXORA INSTITUTE  -  MySQL Database Schema + Seed Data
--  Import:  mysql -u root -p < schema.sql
--  Or via phpMyAdmin: create DB, then import this file.
-- =============================================================

CREATE DATABASE IF NOT EXISTS nexora_institute
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;
USE nexora_institute;

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
-- Site settings (key/value) - everything editable from admin
-- -------------------------------------------------------------
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  skey        VARCHAR(80) NOT NULL UNIQUE,
  svalue      TEXT NULL,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (skey, svalue) VALUES
  ('site_name',   'Nexora Institute'),
  ('tagline',     'Industry-Ready Skills. Globally Recognized Certificates.'),
  ('phone',       '+91 98466 48947'),
  ('whatsapp',    '+919846648947'),
  ('email',       'info@nexorainstitute.com'),
  ('address',     'MG Road, Bangalore, Karnataka, India - 560001'),
  ('about_short', 'Nexora Institute is a next-generation skill development center delivering hands-on, project-based training in design, engineering and IT — trusted by thousands of students and 200+ hiring partners.'),
  ('hero_stat_students', '12,500+'),
  ('hero_stat_courses',  '40+'),
  ('hero_stat_partners', '200+'),
  ('hero_stat_rating',   '4.9/5'),
  ('facebook',    '#'),
  ('instagram',   '#'),
  ('linkedin',    '#'),
  ('youtube',     '#'),
  ('map_embed',   'https://www.google.com/maps?q=MG+Road+Bangalore&output=embed'),
  -- Institute profile (editable from admin -> shown on homepage & splash)
  ('director_name',  'Arun Kumar'),
  ('director_role',  'Founder & Lead Mentor'),
  ('established',     '2018'),
  ('institute_highlights', 'Industry-aligned curriculum|Mentor-led small batches|Verified certificates|Placement support'),
  -- Splash / flash screen
  ('splash_enabled', '1'),
  ('splash_text',    'Industry-Ready Skills. Globally Recognized Certificates.');

-- -------------------------------------------------------------
-- Admins
-- -------------------------------------------------------------
DROP TABLE IF EXISTS admins;
CREATE TABLE admins (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120) NOT NULL,
  username      VARCHAR(60)  NOT NULL UNIQUE,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('superadmin','admin') DEFAULT 'admin',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default login -> username: admin   password: admin@123
INSERT INTO admins (name, username, email, password_hash, role) VALUES
  ('Super Admin', 'admin', 'admin@nexorainstitute.com',
   '$2y$12$QJ.4ynpPUzPJ/QvJxWMm.eZYfmNcru4tG2ES3C3bXv3daVM8h4lOW', 'superadmin');

-- -------------------------------------------------------------
-- Course categories
-- -------------------------------------------------------------
DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
  id    INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL,
  slug  VARCHAR(120) NOT NULL UNIQUE,
  icon  VARCHAR(60)  DEFAULT 'graduation-cap'
) ENGINE=InnoDB;

INSERT INTO categories (name, slug, icon) VALUES
  ('CAD / Design',        'cad-design',      'compass'),
  ('Software & IT',       'software-it',     'code'),
  ('Data & AI',           'data-ai',         'cpu'),
  ('Business & SAP',      'business-sap',    'briefcase'),
  ('Digital Marketing',   'digital-marketing','megaphone');

-- -------------------------------------------------------------
-- Courses  (price, discount, syllabus, software all controlled by admin)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS courses;
CREATE TABLE courses (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  title           VARCHAR(160) NOT NULL,
  slug            VARCHAR(180) NOT NULL UNIQUE,
  category_id     INT NULL,
  short_desc      VARCHAR(255) NULL,
  description     TEXT NULL,
  duration        VARCHAR(60)  DEFAULT '3 Months',
  level           ENUM('Beginner','Intermediate','Advanced') DEFAULT 'Beginner',
  price           DECIMAL(10,2) DEFAULT 0,
  discount_price  DECIMAL(10,2) NULL,
  software        VARCHAR(255) NULL,
  syllabus        TEXT NULL,
  image           VARCHAR(255) NULL,
  is_featured     TINYINT(1) DEFAULT 0,
  status          ENUM('active','draft') DEFAULT 'active',
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_course_cat FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO courses (title, slug, category_id, short_desc, description, duration, level, price, discount_price, software, syllabus, is_featured, status) VALUES
  ('AutoCAD Professional', 'autocad-professional', 1,
   'Master 2D & 3D drafting used by architects and engineers worldwide.',
   'A complete industry-grade AutoCAD program covering 2D drafting, 3D modeling, layouts, plotting and real architectural & mechanical projects. Includes a verified certificate and a portfolio project.',
   '3 Months', 'Beginner', 25000, 17999, 'AutoCAD 2024',
   'Interface & Navigation|Precision Drawing Tools|Layers & Object Properties|Dimensioning & Annotation|3D Modeling Basics|Rendering & Plotting|Live Architectural Project', 1, 'active'),

  ('SolidWorks Mechanical Design', 'solidworks-mechanical-design', 1,
   'Industry standard 3D CAD for product & mechanical design.',
   'Learn parametric part modeling, assemblies, sheet metal, surfacing and simulation with SolidWorks. Build a manufacturable product from concept to drawing.',
   '4 Months', 'Intermediate', 38000, 28500, 'SolidWorks 2024',
   'Sketching & Part Modeling|Assemblies & Mates|Sheet Metal Design|Surface Modeling|Drafting & GD&T|Motion & Simulation|Capstone Product Project', 1, 'active'),

  ('Full Stack Web Development', 'full-stack-web-development', 2,
   'Become a job-ready developer: HTML, CSS, JS, PHP & MySQL.',
   'A hands-on full stack bootcamp. Build responsive front-ends with Tailwind and dynamic back-ends with PHP & MySQL. Ship 3 real deployable projects.',
   '6 Months', 'Beginner', 45000, 32999, 'VS Code, Git, MySQL',
   'HTML5 & Semantic Markup|Tailwind CSS & Responsive Design|JavaScript & DOM|PHP & MySQL|REST APIs & Auth|Deployment & Git|3 Portfolio Projects', 1, 'active'),

  ('Data Science with Python', 'data-science-with-python', 3,
   'Turn raw data into insights with Python, Pandas & ML.',
   'From Python fundamentals to machine learning. Work with real datasets, build dashboards and train predictive models.',
   '6 Months', 'Intermediate', 55000, 41999, 'Python, Jupyter, scikit-learn',
   'Python for Data|NumPy & Pandas|Data Visualization|Statistics & EDA|Machine Learning|Model Deployment|Capstone Data Project', 1, 'active'),

  ('SAP FICO End-to-End', 'sap-fico-end-to-end', 4,
   'Master SAP Financial Accounting & Controlling for finance roles.',
   'Comprehensive SAP FICO training aligned with enterprise finance processes, configuration and real-time scenarios.',
   '4 Months', 'Advanced', 65000, 49999, 'SAP S/4HANA',
   'SAP Navigation|General Ledger|Accounts Payable/Receivable|Asset Accounting|Cost Center Accounting|Integration with MM/SD|Live Configuration Project', 0, 'active'),

  ('Digital Marketing Mastery', 'digital-marketing-mastery', 5,
   'SEO, Ads, Social & Analytics to grow any business online.',
   'A practical, campaign-driven digital marketing course. Run live ad campaigns, rank pages and measure ROI.',
   '3 Months', 'Beginner', 28000, 19999, 'Google Ads, GA4, Meta Suite',
   'Digital Marketing Foundations|SEO & Content|Google & Meta Ads|Social Media Strategy|Email & Automation|Analytics & Reporting|Live Campaign Project', 0, 'active');

-- -------------------------------------------------------------
-- Students  (login panel)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS students;
CREATE TABLE students (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  student_code  VARCHAR(40) NOT NULL UNIQUE,
  name          VARCHAR(140) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(20)  NULL,
  password_hash VARCHAR(255) NOT NULL,
  dob           DATE NULL,
  gender        ENUM('Male','Female','Other') NULL,
  address       VARCHAR(255) NULL,
  photo         VARCHAR(255) NULL,
  -- once a certificate is generated, personal details get locked
  details_locked TINYINT(1) DEFAULT 0,
  status        ENUM('active','inactive') DEFAULT 'active',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default student login -> email: student@nexora.com  password: student@123
INSERT INTO students (student_code, name, email, phone, password_hash, dob, gender, address) VALUES
  ('NEX-2026-0001', 'Arun Kumar', 'student@nexora.com', '+91 90000 00000',
   '$2y$12$jWm0xK.Xl3ewZvw3Bv8lfe3MxbHGswnivZFo7CLuy5naKlNiPzMbu',
   '2000-05-14', 'Male', 'Bangalore, Karnataka');

-- -------------------------------------------------------------
-- Enrollments
-- -------------------------------------------------------------
DROP TABLE IF EXISTS enrollments;
CREATE TABLE enrollments (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  student_id  INT NOT NULL,
  course_id   INT NOT NULL,
  batch       VARCHAR(60) NULL,
  enroll_date DATE DEFAULT (CURRENT_DATE),
  status      ENUM('active','completed','dropped') DEFAULT 'active',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_enr_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_enr_course  FOREIGN KEY (course_id)  REFERENCES courses(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO enrollments (student_id, course_id, batch, status) VALUES
  (1, 1, 'Morning-A', 'active'),
  (1, 3, 'Weekend-B', 'completed');

-- -------------------------------------------------------------
-- Payments / Fee receipts  (generated by Admin only)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS payments;
CREATE TABLE payments (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  receipt_no   VARCHAR(40) NOT NULL UNIQUE,
  student_id   INT NOT NULL,
  course_id    INT NULL,
  amount       DECIMAL(10,2) NOT NULL,
  mode         ENUM('Cash','UPI','Card','Bank Transfer','Cheque') DEFAULT 'Cash',
  status       ENUM('paid','pending','partial') DEFAULT 'paid',
  paid_on      DATE DEFAULT (CURRENT_DATE),
  remarks      VARCHAR(255) NULL,
  generated_by INT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pay_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_pay_course  FOREIGN KEY (course_id)  REFERENCES courses(id)  ON DELETE SET NULL,
  CONSTRAINT fk_pay_admin   FOREIGN KEY (generated_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO payments (receipt_no, student_id, course_id, amount, mode, status, remarks, generated_by) VALUES
  ('RCPT-2026-0001', 1, 1, 17999, 'UPI', 'paid', 'Full course fee', 1),
  ('RCPT-2026-0002', 1, 3, 15000, 'Card', 'partial', 'Installment 1 of 3', 1);

-- -------------------------------------------------------------
-- Certificates  (generated by Admin; locks student details)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS certificates;
CREATE TABLE certificates (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  certificate_no  VARCHAR(50) NOT NULL UNIQUE,
  student_id      INT NOT NULL,
  course_id       INT NOT NULL,
  grade           VARCHAR(10) DEFAULT 'A',
  issue_date      DATE DEFAULT (CURRENT_DATE),
  remarks         VARCHAR(255) NULL,
  generated_by    INT NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cert_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_cert_course  FOREIGN KEY (course_id)  REFERENCES courses(id)  ON DELETE CASCADE,
  CONSTRAINT fk_cert_admin   FOREIGN KEY (generated_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO certificates (certificate_no, student_id, course_id, grade, remarks, generated_by) VALUES
  ('NEX-CERT-2026-0001', 1, 3, 'A+', 'Completed Full Stack Web Development with distinction', 1);

-- -------------------------------------------------------------
-- Projects (Project Information module)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS projects;
CREATE TABLE projects (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(180) NOT NULL,
  student_id  INT NULL,
  course_id   INT NULL,
  description TEXT NULL,
  project_url VARCHAR(255) NULL,
  image       VARCHAR(255) NULL,
  status      ENUM('published','draft') DEFAULT 'published',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_proj_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
  CONSTRAINT fk_proj_course  FOREIGN KEY (course_id)  REFERENCES courses(id)  ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO projects (title, student_id, course_id, description, project_url, status) VALUES
  ('Smart Home Floor Plan (AutoCAD)', 1, 1, 'A fully dimensioned 2D + 3D residential floor plan built as a capstone project.', '#', 'published'),
  ('E-Commerce Store (PHP & MySQL)', 1, 3, 'A complete shopping platform with cart, auth and admin dashboard.', '#', 'published');

-- -------------------------------------------------------------
-- Contact form submissions
-- -------------------------------------------------------------
DROP TABLE IF EXISTS contacts;
CREATE TABLE contacts (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(140) NOT NULL,
  email      VARCHAR(150) NOT NULL,
  phone      VARCHAR(20)  NULL,
  subject    VARCHAR(180) NULL,
  message    TEXT NOT NULL,
  status     ENUM('new','read','responded') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Faculty profiles  (managed from Admin -> Faculty)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS faculty;
CREATE TABLE faculty (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(140) NOT NULL,
  role        VARCHAR(140) NULL,
  expertise   VARCHAR(200) NULL,
  bio         TEXT NULL,
  photo       VARCHAR(255) NULL,
  linkedin    VARCHAR(255) NULL,
  sort_order  INT DEFAULT 0,
  status      ENUM('active','hidden') DEFAULT 'active',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO faculty (name, role, expertise, bio, sort_order, status) VALUES
  ('Arun Kumar', 'Founder & Lead Mentor', 'Full Stack, Data Science', 'A software developer and educator with years of industry experience building real products and mentoring job-ready developers.', 1, 'active'),
  ('Priya Nair', 'Senior CAD Faculty', 'AutoCAD, SolidWorks', 'Mechanical design specialist helping students master industry-standard CAD workflows with hands-on projects.', 2, 'active'),
  ('Rahul Verma', 'SAP & Business Mentor', 'SAP FICO, Analytics', 'Enterprise consultant guiding learners through real SAP configuration and finance scenarios.', 3, 'active');

-- -------------------------------------------------------------
-- Our Work  (institute / owner's professional projects)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS works;
CREATE TABLE works (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(180) NOT NULL,
  type        VARCHAR(100) NULL,
  description TEXT NULL,
  link        VARCHAR(255) NULL,
  image       VARCHAR(255) NULL,
  sort_order  INT DEFAULT 0,
  status      ENUM('published','draft') DEFAULT 'published',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO works (title, type, description, link, sort_order, status) VALUES
  ('Institute ERP & Admissions Portal', 'Web Application', 'A custom student management and online admissions platform built end-to-end with PHP & MySQL.', '#', 1, 'published'),
  ('Manufacturing Dashboard', 'Data & Automation', 'A real-time production analytics dashboard delivered for a mid-size manufacturing client.', '#', 2, 'published'),
  ('E-Commerce Brand Store', 'Web Application', 'A complete online store with payments, inventory and an admin panel.', '#', 3, 'published');

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
--  DEFAULT LOGINS
--  Admin   ->  /admin/login.php    username: admin            password: admin@123
--  Student ->  /student/login.php  email: student@nexora.com  password: student@123
-- =============================================================
