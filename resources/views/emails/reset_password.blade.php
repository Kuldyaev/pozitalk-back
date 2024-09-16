<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
<table align="center" cellpadding="0" cellspacing="0"
       style="max-width: 600px; width: 100%; margin: 0 auto; padding: 0; font-family: 'Lato', sans-serif; color: #465461;">
    <tr>
        <td align="center" style="padding: 20px;">
            <img src="{{ asset('images/Image.png') }}" alt=""
                 style="display: block; margin: 0 auto; max-width: 100%; height: auto;">
            <table border="0" cellpadding="0" cellspacing="0" style="margin-top: 20px; width: 100%;">
                <tr>
                    <td style="padding: 20px; background-color: #ffffff; border-radius: 10px; text-align: left;">
                        <p style="font-weight: 400; font-size: 14px; color: #465461; line-height: 21.7px; margin: 20px 0 0;">
                            Для восстановления пароля пройдите по
                            <a href="{{ $resetUrl }}" style="color: #5EB1BF; font-family: 'Tektur', sans-serif; font-weight: 700; font-size: 18px; margin: 0;">
                                {{ $resetUrl }}
                            </a>
                        </p>
                        <p style="margin: 20px 0 0; font-size: 12px; font-weight: 400; line-height: 15px;">
                            Вы получили данное письмо так как ваша электронная почта была указана при регистрации в
                            личном
                            кабинете сообщества. Если вы не указывали почту и не планируйте регистрироваться, то
                            просто
                            закройте это письмо. В таком случае регистрация не будет завершена.
                        </p>
                        <p style="margin-top: 20px; font-size: 12px; font-weight: 400; line-height: 15px;">
                            ©Copyright VBALANCE Commynity 2024.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>

</html>