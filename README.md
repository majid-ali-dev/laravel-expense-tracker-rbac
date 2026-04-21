# Laravel Expense Tracker (RBAC)

A simple and scalable Expense Tracker built with Laravel, implementing Role-Based Access Control (RBAC).

## 🚀 Features

* Authentication (Login/Register)
* Multi Dashboard (Manager, Staff, Member, Super Admin)
* User Management (CRUD)
* Role Management
* Permission Management
* Assign Permissions to Roles
* Assign Roles to Users
* Secure Access Control using Middleware

## 🧠 Roles

* **Super Admin** → Full access
* **Manager** → Manage users, roles, permissions
* **Staff** → Manage expenses
* **Member** → View & manage own data

## 🔐 RBAC System

* Many-to-Many Relationship:

  * Users ↔ Roles
  * Roles ↔ Permissions
* Pivot Tables:

  * `role_user`
  * `permission_role`

## ⚙️ Tech Stack

* Laravel
* MySQL
* Blade (UI)
* Bootstrap 5
* SweetAlert2

## 📦 Installation

```bash
git clone https://github.com/majid-ali-dev/laravel-expense-tracker-rbac.git
cd laravel-expense-tracker-rbac

composer install
cp .env.example .env
php artisan key:generate

php artisan migrate:fresh --seed

php artisan serve
```

## 👨‍💻 Author

**Majid Ali**

---

## ⭐ Notes

This project is built for learning and demonstrates a clean RBAC implementation in Laravel.
