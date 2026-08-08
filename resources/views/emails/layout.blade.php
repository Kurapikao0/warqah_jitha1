<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'ورقة وجذع' }}</title>
</head>

<body style="
    margin: 0;
    padding: 0;
    background-color: #f4f6f8;
    font-family: Arial, Tahoma, sans-serif;
    color: #263238;
    direction: rtl;
">

<table
    role="presentation"
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        width: 100%;
        margin: 0;
        padding: 0;
        background-color: #f4f6f8;
    "
>
    <tr>
        <td align="center" style="padding: 40px 15px;">

            <!-- Main Container -->
            <table
                role="presentation"
                width="600"
                cellpadding="0"
                cellspacing="0"
                border="0"
                style="
                    width: 100%;
                    max-width: 600px;
                    background-color: #ffffff;
                    border-radius: 14px;
                    overflow: hidden;
                    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
                "
            >

                <!-- Header -->
                <tr>
                    <td
                        align="center"
                        style="
                            padding: 32px 30px 24px;
                            background-color: #ffffff;
                            border-bottom: 1px solid #eeeeee;
                        "
                    >

                        <img
                            src="{{ asset('storage/email/logo.png') }}"
                            alt="ورقة وجذع"
                            style="
                                display: block;
                                max-width: 180px;
                                width: auto;
                                height: auto;
                                margin: 0 auto 15px;
                                border: 0;
                                outline: none;
                                text-decoration: none;
                            "
                        >

                        

                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td
                        style="
                            padding: 40px 35px;
                            text-align: center;
                        "
                    >

                        @yield('content')

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td
                        align="center"
                        style="
                            padding: 22px 30px;
                            background-color: #fafafa;
                            border-top: 1px solid #eeeeee;
                        "
                    >

                        <div style="
                            font-size: 12px;
                            line-height: 1.8;
                            color: #888888;
                        ">
                            هذه رسالة آلية من نظام ورقة وجذع.
                            <br>
                            يرجى عدم الرد على هذه الرسالة.
                        </div>

                        <div style="
                            margin-top: 8px;
                            font-size: 11px;
                            color: #aaaaaa;
                        ">
                            © {{ date('Y') }} ورقة وجذع. جميع الحقوق محفوظة.
                        </div>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
