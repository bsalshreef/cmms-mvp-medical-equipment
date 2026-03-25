# 🏥 CMMS MVP for Medical Equipment

A robust, role-based **Computerized Maintenance Management System (CMMS)** built with **Laravel** for medical equipment, laboratories, and clinical environments.

![Laravel](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap)
![Chart.js](https://img.shields.io/badge/Chart.js-4.4-FF6384?style=flat-square&logo=chartdotjs)
![License](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)

[Features](#-features) • [Quick Start](#-quick-start) • [Roles & Authorization](#-roles--authorization) • [Architecture & Schema Notes](#-architecture--schema-notes) • [Roadmap](#-roadmap)

---

## 📌 Project Status

**Status:** Demo-ready MVP

---

## 📖 Overview

This project provides a practical Minimum Viable Product (MVP) for a **Computerized Maintenance Management System (CMMS)** specifically tailored for medical equipment, laboratories, and clinical environments.

It streamlines the entire maintenance lifecycle — from the moment a nurse reports a broken infusion pump, through the technician's repair process and spare parts consumption, to the final managerial sign-off and KPI reporting.

---

## ✨ Features

### 🛠️ Work Order Management

- **End-to-End Lifecycle:** Create, assign, update, and close work orders.
- **Status Tracking:** Real-time tracking across all workflow states (`OPEN`, `IN_PROGRESS`, `ON_HOLD`, `COMPLETED`, `CLOSED`, `CANCELLED`).
- **Audit Trail:** Automated logging of every status change and the user responsible for it.

### 📎 Attachments & Documentation

- Upload PDFs, images, and service manuals directly to work orders.
- View and delete attachments per work order.
- Secure file storage managed via Laravel's public disk.

### ⚙️ Spare Parts & Inventory

- Track `current_quantity` and `minimum_quantity` thresholds per part.
- Issue parts directly to a work order, recording `quantity_used` and `unit_cost`.
- Stock is automatically decremented on issuance, keeping inventory synchronized.

### 📅 Preventive Maintenance (PPM)

- Manage maintenance plans with configurable `frequency_type` (Daily, Weekly, Monthly, Quarterly, Annual).
- Track `last_done_date` and `next_due_date` with visual urgency indicators.
- Activate or deactivate plans via `is_active` flag.

### 📊 Executive Dashboard

- **KPI Cards:** Open orders, critical device failures, and low-stock alerts at a glance.
- **Visual Analytics:** Chart.js charts for status distribution, priority breakdowns, and monthly trends.
- **Role-Scoped Views:** Each role sees only the data relevant to their responsibilities.

### 🔐 Role-Based Authorization

Authorization is enforced using **Laravel Policies** and Blade `@can` directives throughout all controllers and views.

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- MySQL or MariaDB

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/your-username/cmms-mvp.git
cd cmms-mvp
```

**2. Install dependencies**

```bash
composer install
npm install
```

**3. Environment setup**

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cmms
DB_USERNAME=root
DB_PASSWORD=
```

**4. Build database and seed demo data**

```bash
php artisan migrate:fresh --seed
```

**5. Link storage**

```bash
php artisan storage:link
```

**6. Run the application**

```bash
php artisan serve
npm run dev
```

Visit `http://127.0.0.1:8000`

---

## 🔐 Roles & Authorization

All seeded accounts use the password: `Password123!`

| Role | Email | Capabilities |
|---|---|---|
| **ADMIN** | `admin@cmms.test` | Full system access, user management, global dashboard |
| **MANAGER** | `manager@cmms.test` | Full operational access, reporting, KPI oversight |
| **ENGINEER** | `engineer@cmms.test` | Assign work orders, close tickets, manage PPM |
| **TECHNICIAN** | `technician@cmms.test` | View assigned tasks, upload attachments, issue parts |
| **REQUESTER** | `requester@cmms.test` | Create work orders and view own requests |
| **STORE** | `store@cmms.test` | Manage spare parts inventory and stock movements |
| **VENDOR_COORDINATOR** | `vendor@cmms.test` | Coordinate vendor-related tasks and supplier follow-up |

---

## 🧪 Demo Flow

1. Login as `requester@cmms.test` — create a new work order.
2. Login as `engineer@cmms.test` — assign the work order to `technician@cmms.test`.
3. Login as `technician@cmms.test` — upload an attachment and issue a spare part.
4. Login as `engineer@cmms.test` — close the work order with resolution notes.
5. Login as `admin@cmms.test` or `manager@cmms.test` — review the Dashboard KPIs and operational summaries.

---

## 🏗️ Architecture & Schema Notes

Key design decisions:

- **Database Views** power optimized dashboard queries:
  - `v_open_work_orders`
  - `v_device_failure_summary`
- **Atomic Transactions** protect spare-parts issuance and ensure `current_quantity` is always consistent with work order records.

### Core Models

| Model | Key Relationships |
|---|---|
| `WorkOrder` | Belongs to Device, Creator, Assignee, Closer |
| `Device` | Belongs to DeviceCategory, Vendor |
| `SparePart` | Belongs to Vendor |
| `MaintenancePlan` | Belongs to Device, MaintenanceType |
| `WorkOrderPart` | Tracks `quantity_used` and `unit_cost` per work order |
| `WorkOrderAttachment` | File metadata and storage paths |
| `WorkOrderStatusHistory` | Audit log of every status transition |

---

## 📸 Screenshots

*(Replace these placeholders with real screenshots before publishing)*

**Dashboard View**

![Dashboard](https://via.placeholder.com/800x400.png?text=Dashboard+View+-+KPIs+and+Charts)

**Work Order Details**

![Work Order Details](https://via.placeholder.com/800x400.png?text=Work+Order+Details+-+Attachments+and+Parts)

---

## 🗺️ Roadmap

- [ ] **Phase 2:** Email and SMS notifications for critical work orders.
- [ ] **Phase 2:** QR Code / Barcode generation for physical device tagging.
- [ ] **Phase 3:** Mobile-responsive PWA for technicians in the field.
- [ ] **Phase 3:** API endpoints for integration with hospital HIS/ERP systems.
- [ ] **Phase 4:** AI-driven predictive maintenance suggestions based on failure history.

---

## 🛠️ Troubleshooting

**Attachments not opening**
Run `php artisan storage:link` if you have not done so already.

**Dashboard not loading correctly**
Verify that all migrations ran successfully and that the SQL views (`v_open_work_orders`, `v_device_failure_summary`) were created.

**Authorization errors**
Confirm that the `AuthServiceProvider` is registered in `config/app.php` and that user roles match the seeded values exactly (e.g., `VENDOR_COORDINATOR`, not `VENDOR_COORD`).

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
