# Backend Architecture Rules — Laravel 12 API

> هذا الملف هو "الدستور" الدائم لهذا المستودع.
> يجب قراءته والالتزام به قبل أي تعديل أو إضافة كود.
> لا تخالف أي بند هنا إلا إذا طلبت ذلك صراحة في نفس الرسالة.

---

## 0. السياق العام

هذا مشروع Laravel 12 REST API **موجود ومكتمل جزئيًا**.
المطلوب هو **الاستمرار** فيه، وليس إعادة بنائه أو اقتراح معمارية بديلة.

قبل أي مهمة:
1. افحص الملفات الموجودة فعليًا في المسار المطلوب.
2. لا تفترض وجود شيء — تحقق منه بالفحص.
3. لا تنفذ أي تعديل قبل عرض خطة نصية مختصرة.

---

## 1. الطبقات المعمارية (لا تُغيَّر)

```
API Route
   ↓
Controller        → HTTP layer فقط، رقيق جدًا
   ↓
Form Request       → Validation + Authorization عند الحاجة
   ↓
Service            → منطق العمل، Transactions، تنسيق Repositories
   ↓
Repository         → الوصول لقاعدة البيانات فقط
   ↓
Eloquent Model
   ↓
Database
```

مكونات مساندة:
- **API Resource** → شكل استجابة الـ JSON فقط.
- **Policy** → التفويض/الصلاحيات.
- **Exception** → أخطاء الدومين المعبّرة.

---

## 2. قواعد صارمة

- ❌ لا تُنشئ Migrations جديدة — قاعدة البيانات موجودة مسبقًا.
- ❌ لا تُنشئ Models جديدة — استخدم الموجود، وعدّل عليه فقط عند الحاجة الصريحة.
- ❌ لا تُدخل منطق عمل داخل Controller.
- ❌ لا تستخدم `Mail::to(...)->send()` مباشرة داخل Controller أو Service — البريد يمر عبر Notification/Mailable.
- ❌ لا تُدخل أنماط جديدة بدون إذن صريح: Actions, DTOs, CQRS, Event Sourcing, Use Cases, Handlers, Managers, Hexagonal Architecture، أو أي طبقة تجريد إضافية.
- ✅ PHP 8.4 مع `declare(strict_types=1);` في كل كلاس ينطبق عليه.
- ✅ SOLID + Clean Code.

---

## 3. المصادقة (Authentication)

نظامان منفصلان تمامًا، لا يجوز خلطهما:

### Customer
- Laravel Sanctum مباشرة.

### Admin
```
AdminUser → admin guard → Laravel Sanctum → admin-access-token
```
- Guard name: `admin`
- Token name: `admin-access-token`
- تدفق: `AdminAuthController → AuthService → AuthRepository → AdminUser`
- Logout يُبطل الـ token الحالي فقط.

---

## 4. نطاق العمل الحالي (Modules in scope)

اعمل فقط على هذه الجداول/الموديولات ما لم يُطلب غير ذلك:

- Category
- verification_code
- address
- customer_notifications
- reviews / review_image
- raw_materials
- admin_notifications
- admin_password_resets
- admin_users
- activity_logs
- role_permissions / permissions / roles
- colors
- Product Media (upload architecture)

❌ لا تعيد بناء أو "تحسين" معمارية: Products (الأساسية) / Orders / Cart / Payments إلا بطلب صريح.

---

## 5. معمارية الإشعارات (Notifications)

```
Business Event
      ↓
 Notification
   ├── Database   → customer_notifications / admin_notifications
   └── Mail       → Mailable → SMTP → Recipient
```

- البريد = قناة توصيل داخل Notification، **ليس معمارية منفصلة**.
- Customer notifications و Admin notifications منفصلان تمامًا في المنطق والجداول.

قبل أي تعديل متعلق بالبريد، افحص بالترتيب:
1. الـ Notifications الموجودة
2. الـ Mailables الموجودة
3. قوالب البريد
4. تنفيذ إشعارات العميل
5. تنفيذ إشعارات الأدمن
6. الـ Services المرتبطة
7. الإعدادات الحالية (config/mail)

ثم نفّذ **أصغر تعديل ممكن**. لا تبني نظام بريد مواز.

---

## 6. Product Media Upload (مهمة نشطة حاليًا)

الوضع الحالي: `ProductMedia` يستقبل `url` نصي فقط. المطلوب طبقة رفع ملفات حقيقية:

- `StoreProductMediaRequest` → تحقق من نوع/حجم/عدد الملفات.
- `ProductMediaService` → التعامل مع Laravel Storage (تخزين، حذف، ترتيب، تحديد صورة رئيسية) + استدعاء Repository.
- `ProductMediaRepository` → persistence فقط.
- Controller رقيق: يستقبل الطلب، يستدعي Service، يرجّع `ProductMediaResource`.

قبل التنفيذ: تحقق هل يوجد Storage disk معرّف مسبقًا (public/s3) في `config/filesystems.php`.

---

## 7. قاعدة العمل مع أي طلب جديد

1. افهم الطبقة الموجودة أولًا (اقرأ الكود الفعلي).
2. حدد الطبقة الصحيحة للتعديل (Controller / Service / Repository / إلخ).
3. لا تُنشئ طبقة غير ضرورية.
4. لا تكرر منطق موجود.
5. لا تغيّر المعمارية بدون إذن.
6. حافظ على الأسلوب والتسميات المستخدمة فعليًا في المشروع.
