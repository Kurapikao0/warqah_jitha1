@extends('emails.layout')

@section('content')

    <h1 style="
        margin: 0 0 18px;
        font-size: 24px;
        line-height: 1.5;
        color: #263238;
        font-weight: 700;
    ">
        تم تغيير البريد الإلكتروني
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
        تم تغيير البريد الإلكتروني المرتبط بحسابك بنجاح.
    </p>

    <!-- Email Information -->
    <table
        role="presentation"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="
            max-width: 440px;
            margin: 0 auto 25px;
            border-collapse: separate;
        "
    >

        <tr>
            <td style="
                padding: 13px 15px;
                background-color: #fafafa;
                border: 1px solid #eeeeee;
                font-size: 13px;
                color: #899197;
                text-align: right;
            ">
                البريد السابق
            </td>

            <td style="
                padding: 13px 15px;
                background-color: #fafafa;
                border: 1px solid #eeeeee;
                font-size: 13px;
                color: #263238;
                direction: ltr;
                text-align: left;
                word-break: break-word;
            ">
                {{ $old_email }}
            </td>
        </tr>

        <tr>
            <td style="
                padding: 13px 15px;
                background-color: #ffffff;
                border: 1px solid #eeeeee;
                font-size: 13px;
                color: #899197;
                text-align: right;
            ">
                البريد الجديد
            </td>

            <td style="
                padding: 13px 15px;
                background-color: #ffffff;
                border: 1px solid #eeeeee;
                font-size: 13px;
                color: #263238;
                direction: ltr;
                text-align: left;
                word-break: break-word;
            ">
                {{ $new_email }}
            </td>
        </tr>

    </table>

    <p style="
        margin: 0 auto 10px;
        max-width: 450px;
        font-size: 13px;
        line-height: 1.8;
        color: #7d878c;
    ">
        إذا كنت أنت من قام بهذا التغيير، فلا يلزم اتخاذ أي إجراء آخر.
    </p>

    <p style="
        margin: 0;
        font-size: 13px;
        line-height: 1.8;
        color: #9aa1a5;
    ">
        إذا لم تقم بهذا التغيير، يرجى التواصل مع فريق الدعم فوراً.
    </p>

@endsection
