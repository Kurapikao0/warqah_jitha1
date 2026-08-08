@extends('emails.layout')

@section('content')

    <h1 style="
        margin: 0 0 18px;
        font-size: 25px;
        line-height: 1.5;
        color: #263238;
        font-weight: 700;
    ">
        مرحباً بك في ورقة وجذع 🌿
    </h1>

    <p style="
        margin: 0 auto 12px;
        max-width: 480px;
        font-size: 16px;
        line-height: 1.9;
        color: #4f5b62;
    ">
        أهلاً بك
        <strong style="color: #263238;">
            {{ $customer->full_name }}
        </strong>
        ،
    </p>

    <p style="
        margin: 0 auto 28px;
        max-width: 480px;
        font-size: 15px;
        line-height: 1.9;
        color: #6b747a;
    ">
        يسعدنا انضمامك إلينا.
        تم إنشاء حسابك بنجاح، ونتطلع إلى تقديم تجربة مميزة لك.
    </p>

    <div style="
        margin: 0 auto 28px;
        max-width: 450px;
        padding: 18px 22px;
        background-color: #f7f9f8;
        border-radius: 10px;
        border: 1px solid #e6ebe8;
    ">
        <div style="
            font-size: 14px;
            color: #69747a;
            line-height: 1.8;
        ">
            يمكنك الآن استخدام حسابك والاستفادة من خدمات
            <strong style="color: #263238;">
                ورقة وجذع
            </strong>.
        </div>
    </div>

    <p style="
        margin: 0;
        font-size: 14px;
        line-height: 1.8;
        color: #899197;
    ">
        شكراً لثقتك بنا.
    </p>

@endsection
