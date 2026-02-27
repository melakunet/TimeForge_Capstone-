# TimeForge Capstone Project

**Student:** Etefworkie Melaku  
**Course:** Mobile and Web App Development  
**Institution:** triOS College  
**Date:** February 2026

---

## 📖 Project Description

TimeForge helps freelancers maximize revenue through accurate time tracking. This web app features project management, professional reporting, and a client portal for billing transparency. Replace manual spreadsheets and capture every billable hour.

---

## ✨ Features Implemented

### Phase 1: Authentication System ✅
- User registration and login
- Role-based access control (Admin, Freelancer, Client)
- Secure password hashing
- Session management
- Logout functionality

### Phase 2: Project Management ✅
- Add new projects
- View project list (dashboard)
- Archive/soft delete projects
- Role-based project visibility
- Project tracking with created_by field

### Phase 3: Client Management ✅ (CURRENT)
- **Add new clients** with full validation
- **Edit existing clients** with duplicate prevention
- **Client list view** with search and filtering
- **Search functionality** (name, company, email, phone)
- **Filter by status** (Active, Inactive, All)
- **Role-based permissions** (Admin & Freelancer manage, Client views own)
- **Empty states** with helpful CTAs
- **Breadcrumb navigation**
- **Success/Error messages**
- **Dark mode support**

### Coming Soon:
- Phase 4: Enhanced project management
- Phase 5: Time tracking
- Phase 6: Reporting and invoicing
- Phase 7: Email notifications

---

## 🚀 Getting Started

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Modern web browser
- Text editor (VS Code recommended)

### Installation

1. **Clone or download** this project to your XAMPP htdocs folder:
   ```
   /Applications/XAMPP/xamppfiles/htdocs/TimeForge_Capstone
   ```

2. **Start XAMPP:**
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

3. **Create Database:**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create new database: `TimeForge_Capstone`
   - Import SQL file: `sql/TimeForge_Capstone_Phase3.sql`

4. **Access the Application:**
   ```
   http://localhost/TimeForge_Capstone/
   ```

---

## 🔐 Test Credentials

| Role | Username | Password |
|------|----------|----------|
| **Admin** | admin_user | password123 |
| **Freelancer** | dev_sarah | password123 |
| **Client** | client_bob | password123 |

*See `CREDENTIALS.md` for complete list*

---

## 📂 Project Structure

```
TimeForge_Capstone/
├── index.php                    # Main dashboard/landing page
├── login.php                    # User login
├── register.php                 # User registration
├── add_project.php              # Add new project form
├── add_project_process.php      # Project creation logic
├── delete_project.php           # Archive project (soft delete)
├── add_client.php               # Add new client form
├── add_client_process.php       # Client creation logic
├── clients.php                  # Client list with search/filter
├── edit_client.php              # Edit client form
├── edit_client_process.php      # Client update logic
├── admin/
│   └── dashboard.php            # Admin portal
├── client/
│   └── dashboard.php            # Client portal
├── freelancer/
│   └── dashboard.php            # Freelancer portal
├── config/
│   ├── session.php              # Session configuration
│   └── theme.php                # Theme settings
├── css/
│   ├── style.css                # Main stylesheet
│   └── auth_layout.css          # Authentication page styles
├── includes/
│   ├── auth.php                 # Authentication functions
│   ├── header_partial.php       # Header component
│   ├── footer_partial.php       # Footer component
│   ├── login_process.php        # Login logic
│   ├── register_process.php     # Registration logic
│   ├── logout.php               # Logout logic
│   └── theme_handler.php        # Theme toggle logic
├── js/
│   ├── theme.js                 # Dark mode toggle
│   ├── animations.js            # UI animations
│   └── hero.js                  # Landing page effects
├── sql/
│   └── TimeForge_Capstone_Phase3.sql  # Database schema
└── db.php                       # Database connection
```

---

## 🛠️ Technologies Used

- **Backend:** PHP 8.1+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Server:** Apache (XAMPP)
- **Version Control:** Git

---

## 🎨 Design Features

- ✅ Clean, modern UI design
- ✅ Dark mode support
- ✅ Responsive/mobile-friendly
- ✅ Accessible navigation
- ✅ Professional color scheme
- ✅ Smooth animations
- ✅ Intuitive user flows

---

## 🔒 Security Features

- ✅ Password hashing (bcrypt)
- ✅ Prepared SQL statements (PDO)
- ✅ XSS protection (htmlspecialchars)
- ✅ CSRF protection (session validation)
- ✅ Role-based access control
- ✅ Input validation (client & server-side)
- ✅ Session security

---

## 📚 Documentation

- `CREDENTIALS.md` - Test user credentials
- `PHASE3_PROGRESS.md` - Phase 3 progress report
- `TESTING_GUIDE.md` - Step-by-step testing scenarios
- `DATABASE_UPDATE_GUIDE.md` - Database setup instructions

---

## 🧪 Testing

See `TESTING_GUIDE.md` for comprehensive testing scenarios covering:
- Client management (add, edit, search, filter)
- Authentication and authorization
- Role-based permissions
- Input validation
- Dark mode compatibility
- Mobile responsiveness

---

## 📝 License

This project is created for educational purposes as part of the Web Development Capstone course at triOS College.

---

## 👨‍💻 Author

**Etefworkie Melaku**  
Mobile and Web App Development Student  
triOS College  
February 2026

---

## 🙏 Acknowledgments

- triOS College instructors and staff
- XAMPP development team
- PHP and MySQL communities
- Open source contributors
