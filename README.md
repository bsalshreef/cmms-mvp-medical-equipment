
# CMMS MVP – Medical Equipment Maintenance

Minimal yet complete **Computerized Maintenance Management System (CMMS)** focused on **medical equipment** in hospitals and laboratories.  
يشمل دورة كاملة لأوامر العمل، إدارة الأجهزة، الصيانة الوقائية، المرفقات، وقطع الغيار، مع صلاحيات أدوار متعددة.

---

## 1. Features

- Work Orders (إنشاء، تعديل، تعيين، إغلاق، سجل حالات).
- Medical Devices registry (أجهزة + فئات + موردين).
- Preventive Maintenance Plans (PPM) + Executions.
- Spare Parts inventory + استهلاك مرتبط بأوامر العمل.
- Attachments (صور أعطال، تقارير PDF، فواتير…).
- Dashboard بمهام رئيسية (KPIs + جداول مختصرة).
- Roles & Policies (Admin, Engineer, Technician, Requester, Store, Vendor Coordinator, Manager).

---

## 2. Tech Stack

- Laravel 10/11 (PHP 8.1+)
- MySQL / MariaDB
- Bootstrap 5 (RTL)
- Eloquent ORM + Policies + Seeders

---

## 3. Installation

### 3.1. Clone

```bash
git clone https://github.com/bsalshreef/cmms-mvp-medical-equipment.git
cd cmms-mvp-medical-equipment
```

### 3.2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

عدّل إعدادات قاعدة البيانات في `.env`:

- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

### 3.3. Migrations + Seeders

بيئة تطوير جديدة (موصى به):

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

بيئة موجودة:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

---

## 4. Default Login Accounts

حسابات تجريبية جاهزة لكل دور:

| Role               | Email                  | Password     |
| ------------------ | ---------------------- | ------------ |
| ADMIN              | admin@cmms.test        | Password123! |
| MANAGER            | manager@cmms.test      | Password123! |
| ENGINEER           | engineer@cmms.test     | Password123! |
| TECHNICIAN         | technician@cmms.test   | Password123! |
| REQUESTER          | requester@cmms.test    | Password123! |
| STORE              | store@cmms.test        | Password123! |
| VENDOR_COORDINATOR | vendor@cmms.test       | Password123! |

> يمكنك تعديل هذه الحسابات من لوحة المستخدمين بعد الدخول كـ ADMIN.

---

## 5. Main Modules & Screens

### 5.1. Authentication

- `/login` – صفحة الدخول.
- توجيه المستخدم بعد النجاح إلى `/dashboard`.

### 5.2. Dashboard

- `/dashboard`  
يعرض:

- إجمالي أوامر العمل المفتوحة، قيد التنفيذ، والمتأخرة.
- عدد زيارات PPM خلال الشهر الحالي.
- أجهزة حرجة ذات طلبات مفتوحة.
- قطع غيار منخفضة المخزون.
- قائمة مختصرة بأحدث أوامر العمل المفتوحة (Top 10).
- الأجهزة الأعلى تكرارًا في الأعطال.
- PPM القادمة (Upcoming).
- أحدث أوامر العمل (Recent Activity).

### 5.3. Devices & Vendors

- `/devices` – قائمة الأجهزة الطبية:
  - الكود، الاسم، الفئة، القسم، الموقع، الشركة المصنعة، المورد، حالة التشغيل، مستوى الأهمية.
- `/devices/{id}` – تفاصيل جهاز:
  - بيانات الجهاز الكاملة.
  - تاريخ أوامر العمل والأعطال.
  - خطط الصيانة الوقائية المرتبطة.
  - قطع الغيار المستخدمة سابقًا.
  - المرفقات والعقود المرتبطة بالمورد.
- `/device-categories` – إدارة فئات الأجهزة (Imaging, Lab, ICU, Dental, CSSD, Monitoring, …).
- `/vendors` – إدارة الموردين:
  - بيانات الاتصال.
  - العقود وفترات الضمان.
  - الأجهزة التابعة لكل مورد.

### 5.4. Work Orders

- `/work-orders` – قائمة أوامر العمل مع فلاتر متقدمة:
  - الحالة (workflow_status)
  - نوع الخدمة (service_category)
  - نوع الصيانة (maintenance_type)
  - الجهاز / القسم
  - الفني المسؤول
  - الفترة الزمنية
  - الأولوية
- `/work-orders/create` – إنشاء أمر عمل جديد:
  - اختيار الجهاز.
  - نوع الخدمة (صيانة، طلب محاليل، تركيب، تشغيل…).
  - نوع الصيانة (PPM, Corrective, On Call, Emergency) عند الحاجة.
  - بيانات مقدم الطلب، الأولوية، وصف المشكلة.
- `/work-orders/{id}` – تفاصيل أمر العمل:
  - الحالة والأولوية.
  - بيانات مقدم الطلب والجهاز.
  - نوع الخدمة/الصيانة.
  - وصف المشكلة.
  - نتيجة المعالجة وملاحظات الإغلاق.
  - سجل تغيّر الحالة (Status History).
  - المرفقات (صور، تقارير، فواتير…).
  - قطع الغيار المستخدمة وتكلفتها.
- `/work-orders/{id}/edit` – تعديل بيانات أمر العمل (للأدوار المخوّلة).
- `/work-orders/{id}/assign` – تعيين الفني/المهندس المسؤول مع تسجيل ملاحظة في الـ History.
- `/work-orders/{id}/close` – إغلاق الطلب:
  - تحديد نتيجة المعالجة (مثلاً: تم الإصلاح، يحتاج قطع غيار، غير قابل للإصلاح…).
  - ملاحظة الإغلاق التقنية/الإدارية.
- `/work-orders/{id}/attachments` – وحدة إدارة المرفقات.
- `/work-orders/{id}/parts` – وحدة إدارة قطع الغيار المستهلكة.

### 5.5. Preventive Maintenance (PPM)

- `/maintenance-plans` – إدارة خطط الصيانة الوقائية:
  - ربط كل خطة بجهاز ونوع صيانة (PPM/Calibration/Inspection).
  - تكرار الخطة (يومي، أسبوعي، شهري، ربع سنوي، سنوي).
  - تاريخ البداية، تاريخ الاستحقاق التالي، الحالة (فعّال/غير فعّال).
- `/maintenance-plans/{id}` – تفاصيل خطة PPM.
- `/maintenance-plans/{id}/execute` – تسجيل زيارة وقائية منفّذة:
  - يمكن ربطها بأمر عمل (Work Order) إن لزم.
  - تسجيل نتيجة التنفيذ وملاحظات الفني.
- `/ppm-calendar` – عرض زمني للصيانة الوقائية (Monthly/Weekly view).

### 5.6. Spare Parts

- `/spare-parts` – مخزون قطع الغيار:
  - الكود، الاسم، المخزون الحالي، الحد الأدنى، سعر الوحدة، المورد، الموقع في المستودع.
- `/work-orders/{id}/parts` – استهلاك قطع الغيار داخل أمر العمل:
  - اختيار القطعة والكمية.
  - خصم أوتوماتيكي من المخزون (current_quantity).
  - إعادة الكمية عند تعديل السطر أو حذفه.
  - حساب إجمالي تكلفة القطع لكل أمر عمل (total_price + grand total).

### 5.7. Reports

- `/reports` – نقطة دخول للتقارير (skeleton جاهز).
- أمثلة:
  - الأعطال حسب الجهاز / القسم.
  - MTTR (متوسط زمن الإصلاح) و MTBF (متوسط الزمن بين الأعطال).
  - PPM المنفذة مقابل المخططة.
  - استهلاك قطع الغيار حسب الجهاز أو المورد.
  - أوامر العمل حسب المورد أو القسم أو الأولوية.

---

## 6. Roles & Permissions (High-Level)

يتم التحكم في الصلاحيات عبر Laravel Policies و `AuthServiceProvider`:

- **ADMIN**
  - تحكم كامل في النظام، إدارة المستخدمين، الإعدادات، كافة الوحدات.
- **ENGINEER**
  - إدارة الأجهزة، أوامر العمل، خطط PPM، مراجعة التقارير الأساسية.
- **TECHNICIAN**
  - عرض الأوامر المعيّنة له فقط.
  - تحديث الحالة، إضافة ملاحظات، رفع مرفقات، تسجيل قطع غيار.
- **REQUESTER**
  - إنشاء أوامر عمل جديدة.
  - متابعة أوامره الخاصة فقط.
- **STORE**
  - إدارة مخزون قطع الغيار.
  - مراقبة الحد الأدنى.
  - تقارير الاستهلاك.
- **VENDOR_COORDINATOR**
  - إدارة الموردين.
  - متابعة الطلبات المحوّلة لهم والعقود.
- **MANAGER**
  - الوصول إلى Dashboard والتقارير.
  - اعتماد وإغلاق عالي المستوى حسب السياسات المعتمدة.

Models المرتبطة بالسياسات (Policies):

- WorkOrder  
- Device  
- SparePart  
- MaintenancePlan  
- Vendor  
- WorkOrderAttachment  
- WorkOrderPart  

---

## 7. Use Cases

- مشروع تخرج أو بحث تطبيقي في:
  - إدارة صيانة الأجهزة الطبية.
  - دمج CMMS مع مفاهيم PPM وقطع الغيار والتكلفة.
- Proof-of-Concept لنظام CMMS في:
  - مختبرات جامعية.
  - مستشفيات تعليمية.
  - مراكز أشعة وعيادات خاصة.
- أساس لتطوير منتج تجاري SaaS:
  - استهداف مختبرات صغيرة/متوسطة لا تملك نظام CMMS متقدم.
  - إمكانية التوسع لاحقًا لإرسال تنبيهات بريدية، تكامل مع IoT/Predictive Maintenance، إلخ.

---

## 8. License

انظر ملف `LICENSE` في المستودع لمزيد من التفاصيل حول الرخصة وشروط الاستخدام.
```
