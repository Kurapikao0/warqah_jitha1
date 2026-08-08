@extends('emails.layout')

@section('content')

    <h1 style="
        margin: 0 0 18px;
        font-size: 24px;
        line-height: 1.5;
        color: #263238;
        font-weight: 700;
    ">
        تأكيد البريد الإلكتروني
    </h1>

    <p style="
        margin: 0 auto 12px;
        max-width: 470px;
        font-size: 15px;
        line-height: 1.9;
        color: #4f5b62;
    ">
        مرحباً
        <strong style="color: #263238;">
            {{ $customer->full_name }}
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
        استخدم رمز التحقق التالي لتأكيد بريدك الإلكتروني.
    </p>

    <!-- OTP -->
    <div style="
        margin: 0 auto 25px;
        padding: 20px 25px;
        max-width: 320px;
        background-color: #f7f9f8;
        border: 1px solid #dfe7e3;
        border-radius: 12px;
        text-align: center;
    ">

        <div style="
            margin-bottom: 8px;
            font-size: 12px;
            color: #899197;
        ">
            رمز التحقق
        </div>

        <div style="
            font-size: 32px;
            line-height: 1.4;
            letter-spacing: 7px;
            font-weight: 700;
            color: #263238;
            direction: ltr;
        ">
            {{ $otp }}
        </div>

    </div>

    <p style="
        margin: 0 auto 10px;
        font-size: 13px;
        line-height: 1.8;
        color: #7d878c;
    ">
        صلاحية هذا الرمز محدودة.
    </p>

    <p style="
        margin: 0;
        font-size: 13px;
        line-height: 1.8;
        color: #9aa1a5;
    ">
        إذا لم تطلب هذا الرمز، يمكنك تجاهل هذه الرسالة.
    </p>

@endsection
