@extends('emails.layout')

@section('content')

    <h1 style="
        margin: 0 0 18px;
        font-size: 24px;
        line-height: 1.5;
        color: #263238;
        font-weight: 700;
    ">
        إعادة تعيين كلمة المرور
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
        تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.
        استخدم الرمز التالي لإكمال عملية إعادة التعيين.
    </p>

    <!-- Reset Token -->
    <div style="
        margin: 0 auto 25px;
        padding: 18px 20px;
        max-width: 430px;
        background-color: #f7f9f8;
        border: 1px solid #dfe7e3;
        border-radius: 10px;
        text-align: center;
        word-break: break-all;
    ">

        <div style="
            margin-bottom: 8px;
            font-size: 12px;
            color: #899197;
        ">
            رمز إعادة التعيين
        </div>

        <div style="
            font-size: 14px;
            line-height: 1.8;
            font-weight: 600;
            color: #263238;
            direction: ltr;
        ">
            {{ $token }}
        </div>

    </div>

    <p style="
        margin: 0 auto 10px;
        font-size: 13px;
        line-height: 1.8;
        color: #7d878c;
    ">
        صلاحية رمز إعادة التعيين محدودة.
    </p>

    <p style="
        margin: 0;
        font-size: 13px;
        line-height: 1.8;
        color: #9aa1a5;
    ">
        إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذه الرسالة.
    </p>

@endsection
