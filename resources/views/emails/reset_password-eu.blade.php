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
                        <h1
                                style="color: #5EB1BF; font-family: 'Tektur', sans-serif; font-weight: 700; font-size: 18px; margin: 0;">
                            Password recovery in the <br> VBALANCE.Community personal account
                        </h1>
                        <p> We have received a request to reset the password for accessing the VBALANCE.Community
                            personal account. To proceed with the password reset, please click on the link below:
                        </p>
                        <a href="{{ $resetUrl }}" style="background-color: #fff; width: 100%; height: 40px; border-radius: 20px; color: #5EB1BF; font-weight: 700; font-size: 20px; text-align: center; border: none; outline: none;">
                            {{ $resetUrl }}
                        </a>
                        <p style="margin: 20px 0 0; font-size: 12px; font-weight: 400; line-height: 15px;">
                            You have received this email because your email address was provided during the
                            registration process for the community account. If you did not provide your email
                            address or do not intend to register, please disregard this email. In that case, the
                            registration process will not be completed.
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