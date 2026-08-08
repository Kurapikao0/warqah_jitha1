<!DOCTYPE html>

<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد البريد الإلكتروني - ورقة وجذع</title>
</head>
<body>
    <div>
        <h2>تأكيد البريد الإلكتروني</h2>


    <p>
        مرحباً {{ $customer->full_name }}،
    </p>

    <p>
        لتأكيد بريدك الإلكتروني في ورقة وجذع، استخدم رمز التحقق التالي:
    </p>

    <h1>
        {{ $otp }}
    </h1>

    <p>
        هذا الرمز صالح لمدة 10 دقائق فقط.
    </p>

    <p>
        إذا لم تطلب إنشاء هذا الحساب، يمكنك تجاهل هذه الرسالة.
    </p>

    <p>
        مع تحيات فريق ورقة وجذع 🌿
    </p>
</div>


</body>
</html>
