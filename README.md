
# SkillBridge – Freelance Task Management Platform

[![MIT License](https://img.shields.io/github/license/Mohamed-Irfan-git/skillbridge?color=green)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7%2B-blue?logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%2FMariaDB-blue?logo=mysql)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap%205-blue?logo=bootstrap)](https://getbootstrap.com/)
[![HTML](https://img.shields.io/badge/HTML-5-orange?logo=html5)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS](https://img.shields.io/badge/CSS-3-blue?logo=css3)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![Last Commit](https://img.shields.io/github/last-commit/Mohamed-Irfan-git/skillbridge?logo=github)](https://github.com/Mohamed-Irfan-git/skillbridge/commits/main)
[![Issues](https://img.shields.io/github/issues/Mohamed-Irfan-git/skillbridge?logo=github)](https://github.com/Mohamed-Irfan-git/skillbridge/issues)
[![Pull Requests](https://img.shields.io/github/issues-pr/Mohamed-Irfan-git/skillbridge?logo=github)](https://github.com/Mohamed-Irfan-git/skillbridge/pulls)
[![Stars](https://img.shields.io/github/stars/Mohamed-Irfan-git/skillbridge?style=social)](https://github.com/Mohamed-Irfan-git/skillbridge/stargazers)

**SkillBridge** is a modern web application that connects freelancers with task creators, enabling seamless collaboration through task browsing, applications, real-time chat, and automated notifications. Built with a robust PHP backend and a responsive Bootstrap 5 frontend, SkillBridge simplifies freelance task management.


## 🌟 Overview
SkillBridge provides a centralized platform for freelance task management, offering:
- Real-time task browsing with advanced filtering
- Streamlined application tracking for freelancers
- Integrated real-time chat for direct communication
- Automated notifications for key events

The platform ensures an efficient, transparent, and user-friendly experience for both freelancers and task creators.



## ✨ Features
- **User Authentication**: Secure login and registration system
- **Task Browsing**: Filter tasks by skill, title, or deadline
- **Task Application**: Apply for tasks with instant creator notifications
- **Real-Time Chat**: Seamless messaging between freelancers and task creators
- **Notifications**: Alerts for new messages and task applications
- **Responsive UI**: Mobile-friendly design powered by Bootstrap 5



## 🛠️ Tech Stack
- **Backend**: PHP 7+
- **Frontend**: HTML, CSS, Bootstrap 5
- **Database**: MySQL / MariaDB
- **Server**: Apache / Nginx
- **Session Management**: PHP Sessions

---

## 📦 Installation
1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Mohamed-Irfan-git/skillBridge.git
   ```

2. **Move to Web Server Directory**:
    - Copy the project to your web server’s directory (e.g., `htdocs` for XAMPP).

3. **Configure Database**:
    - Update the database connection in `config/db_connection.php`:
      ```php
      <?php
      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "skillbridge";
 
      $conn = new mysqli($servername, $username, $password, $dbname);
 
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      ?>
      ```

4. **Set Up Database**:
    - Import the SQL schema (see [Database Schema](#database-schema)) into MySQL/MariaDB.

5. **Start Server**:
    - Launch your server (e.g., XAMPP, WAMP, or LAMP).
    - Navigate to `http://localhost/skillBridge` in your browser.



## 🗄️ Database Schema
The database consists of the following tables:

### Users
```sql
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
```

### Tasks
```sql
CREATE TABLE tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    skill_required VARCHAR(255),
    deadline DATE,
    status ENUM('open', 'closed') DEFAULT 'open',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
```

### Applications
```sql
CREATE TABLE applications (
    app_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    freelancer_id INT NOT NULL,
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (freelancer_id) REFERENCES users(user_id)
);
```

### Messages
```sql
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (sender_id) REFERENCES users(user_id)
);
```

### Notifications
```sql
CREATE TABLE notifications (
    notif_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
```



## 🚀 Usage
1. Navigate to `http://localhost/skillBridge` in your browser.
2. Register or log in to access the platform.
3. Browse tasks using the search bar to filter by skill, title, or deadline.
4. Click **Apply for Task** to submit an application, triggering a confirmation alert for the freelancer and a notification for the task creator.
5. Use the **View & Chat** feature to communicate in real-time with task creators.
6. Task creators receive instant notifications for new applications and messages.



## 📸 Screenshots
Below are placeholders for key interface snapshots showcasing SkillBridge’s functionality. To add screenshots:
1. Save images (e.g., `task_list.png`, `chat_modal.png`, `notifications.png`) in a `screenshots` folder within the repository.
2. Update this section with the appropriate Markdown image links.


## 🔮 Future Enhancements
- **Real-Time Notifications**: Integrate WebSockets or Pusher for instant alerts
- **Admin Panel**: Centralized management for users and tasks
- **Advanced Filtering**: Enhanced task filtering by skills, deadlines, or priority
- **Rating & Feedback System**: Enable task creators to rate freelancers
- **Email Notifications**: Notify users via email for tasks and messages


## 🧑‍💻 Contributing
1. **Fork** the repository.
2. Add features, improve the UI, or enhance backend functionality.
3. Ensure code is tested and follows PHP best practices.
4. Submit a **pull request** with a clear description of changes.

## 📄 License
This project is licensed under the [MIT License](LICENSE).
## 👨‍💻 Author
Developed by [Mohamed Irfan](https://github.com/Mohamed-Irfan-git).



