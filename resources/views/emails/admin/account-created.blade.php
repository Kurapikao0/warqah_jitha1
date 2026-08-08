@extends('emails.layout')

@section('content')

<h1 style="
    margin: 0 0 18px;
    font-size: 25px;
    line-height: 1.5;
    color: #263238;
    font-weight: 700;
">
    مرحبًا بك في لوحة الإدارة
</h1>

<p style="
    margin: 0 auto 12px;
    max-width: 480px;
    font-size: 16px;
    line-height: 1.9;
    color: #4f5b62;
">
    أهلًا بك
    <strong style="color: #263238;">
        {{ $admin->full_name }}
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
    تم إنشاء حسابك الإداري في
    <strong style="color: #263238;">
        ورقة وجذع
    </strong>
    بنجاح.
</p>

<div style="
    margin: 0 auto 28px;
    max-width: 450px;
    padding: 18px 22px;
    background-color: #f7f9f8;
    border-radius: 10px;
    border: 1px solid #e6ebe8;
    text-align: center;
">
    <div style="
        margin-bottom: 8px;
        font-size: 13px;
        color: #899197;
        line-height: 1.8;
    ">
        البريد الإلكتروني الإداري
    </div>

    <div style="
        font-size: 16px;
        color: #263238;
        font-weight: 600;
        line-height: 1.8;
        direction: ltr;
    ">
        {{ $admin->email }}
    </div>
</div>

<p style="
    margin: 0 auto 12px;
    max-width: 480px;
    font-size: 14px;
    line-height: 1.9;
    color: #69747a;
">
    يمكنك الآن استخدام بيانات حسابك للدخول إلى لوحة الإدارة
    والاستفادة من الصلاحيات الممنوحة لحسابك.
</p>

<p style="
    margin: 0;
    font-size: 14px;
    line-height: 1.8;
    color: #899197;
">
    إذا لم تكن تتوقع إنشاء هذا الحساب، يرجى التواصل مع مسؤول النظام.
</p>

@endsection
