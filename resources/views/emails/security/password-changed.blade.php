@extends('emails.layout')

@section('content')

    <h1 style="
        margin: 0 0 18px;
        font-size: 24px;
        line-height: 1.5;
        color: #263238;
        font-weight: 700;
    ">
        تم تغيير كلمة المرور
    </h1>

    <p style="
        margin: 0 auto 15px;
        max-width: 470px;
        font-size: 15px;
        line-height: 1.9;
        color: #4f5b62;
    ">
        مرحباً
        <strong style="color: #263238;">
            {{ $user->full_name }}
        </strong>
        ،
    </p>

    <p style="
        margin: 0 auto 28px;
        max-width: 470px;
        font-size: 15px;
        line-height: 1.9;
        color: #6b747a;
    ">
        نود إعلامك بأنه تم تغيير كلمة المرور الخاصة بحسابك بنجاح.
    </p>

    <div style="
        margin: 0 auto 25px;
        max-width: 430px;
        padding: 20px 22px;
        background-color: #f7f9f8;
        border: 1px solid #dfe7e3;
        border-radius: 10px;
        text-align: center;
    ">

        <div style="
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #263238;
        ">
            تم تحديث بيانات الأمان
        </div>

        <div style="
            font-size: 13px;
            line-height: 1.8;
            color: #7d878c;
        ">
            يمكنك الآن تسجيل الدخول باستخدام كلمة المرور الجديدة.
        </div>

    </div>

    <p style="
        margin: 0;
        font-size: 13px;
        line-height: 1.8;
        color: #9aa1a5;
    ">
        إذا لم تقم بهذا التغيير، يرجى التواصل مع فريق الدعم فوراً
        لحماية حسابك.
    </p>

@endsection
