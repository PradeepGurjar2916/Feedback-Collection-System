A web-based application that allows users to submit feedback with a star rating, and enables administrators to view and export feedback data.

Features:
  Dynamic feedback form with AJAX submission
  
  Star rating system using jQuery RateYo plugin
  
  Responsive admin dashboard to view all feedback
  
  Export feedback data to CSV
  
  MySQL database integration using PHP

Tech Stack:
  Frontend: HTML, CSS (Bootstrap), JavaScript, jQuery
  
  Backend: PHP, MySQL
  
  Plugins: jQuery RateYo (for star rating)
  
  Others: AJAX for form submission, XAMPP for local server

How to Run:
  Install XAMPP and start Apache and MySQL.
  
  Copy the feedback_system folder to C:/xampp/htdocs/.
  
  Open phpMyAdmin, create a database named feedback_db, and run this SQL:
  
  sql
  Copy
  Edit
  CREATE TABLE feedback (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100),
      email VARCHAR(100),
      comment TEXT,
      rating DECIMAL(2,1),
      submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
  );
  Open your browser and go to:
  http://localhost/feedback_system/index.html
  
  Submit feedback and view it via:
  http://localhost/feedback_system/admin/dashboard.php

Output:
  Feedback entries are stored in the feedback table in feedback_db.
  
  Admin can export all feedback as a .csv file.

Author:
  Developed by Pradeep Kumar
