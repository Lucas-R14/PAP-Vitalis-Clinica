# PAP-Vitalis-Clinic

This project is part of my **"Projeto de Aptidão Profissional" (PAP)**, a final-year project required for the completion of my course. The **PAP** is a practical assessment that demonstrates the skills and knowledge acquired throughout the academic journey, showcasing the ability to develop a complete and functional solution.

## 📌 Project Overview

**Vitalis Clinic** is a **web application** designed to efficiently manage a medical clinic. The platform provides essential features such as:

✅ **Appointment Scheduling** – Users can book medical appointments and view their schedules.  
✅ **Reports & Records** – Patients can access their medical reports and history in a centralized system.  
✅ **Health & Well-being Tips** – A dedicated section to promote health awareness and preventive care.

---

## 🚀 How to Run the Project

### 📂 Requirements
Before starting, make sure you have the following installed on your system:

- **[Ampps](https://www.ampps.com/)** (Web Server Package with Apache, MySQL, PHP, and Softaculous)
- **PHP** (Version 7.4 or higher)
- **MySQL** (Included in Ampps)
- **Composer** (For dependency installation)

### 🛠️ Step-by-Step Guide

#### 1️⃣ Set Up the Local Server (Ampps)
1. Download and install [Ampps](https://www.ampps.com/).
2. Start **Ampps** and ensure Apache and MySQL are running.
3. Open a browser and go to `http://localhost/phpmyadmin`.

#### 2️⃣ Place the Project in the Correct Folder
1. Navigate to the Ampps installation directory (usually `C:\Ampps` on Windows or `/Applications/Ampps` on macOS).
2. Inside the `www` folder, create a new folder named `vitalis_clinica`.
3. Copy and paste the entire project files into `C:\Ampps\www\vitalis_clinica`.
4. You can now access the project via `http://localhost/vitalis_clinica`.

#### 3️⃣ Import the Database
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Click on **Databases** and create a new database named `vitalis_clinica`.
3. Select the `vitalis_clinica` database.
4. Go to the **Import** tab.
5. Click **Choose File** and select the SQL file located in your project folder:
   ```
   database/vitalis_clinic.sql
   ```
6. Click **Go** to execute the import.

#### 4️⃣ Configure the Database Connection
1. Open the project configuration file (`config.php` or `.env`).
2. Update the database credentials as needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'vitalis_clinica');
   ```

#### 5️⃣ Install Composer and Dependencies
##### Installing Composer
1. Download and install [Composer](https://getcomposer.org/download/).
2. After installation, open a terminal (Command Prompt or PowerShell on Windows, Terminal on macOS/Linux).
3. Run the following command to verify installation:
   ```bash
   composer -V
   ```
   You should see the installed version of Composer.

##### Installing PHPMailer
1. Inside the project folder (`C:\Ampps\www\vitalis_clinica`), open a terminal.
2. Run the following command to install **PHPMailer**:
   ```bash
   composer require phpmailer/phpmailer
   ```

#### 6️⃣ Configure PHPMailer
In the email configuration file (`config/email.php` or similar), set up your SMTP credentials:
```php
$mail->isSMTP();
$mail->Host = 'smtp.yourdomain.com';
$mail->SMTPAuth = true;
$mail->Username = 'youremail@yourdomain.com';
$mail->Password = 'yourpassword';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
```

#### 7️⃣ Run the Project
Now, start the local server and access the project in your browser:
1. Ensure Ampps is running.
2. Open a browser and go to:
   ```
   http://localhost/vitalis_clinica
   ```

---

## 📌 Technologies Used

- **PHP** (Back-end)
- **MySQL** (Database)
- **HTML, CSS & JavaScript** (Front-end)
- **PHPMailer** (Email Sending)
- **Ampps** (Local Server)
