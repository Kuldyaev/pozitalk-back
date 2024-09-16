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
                            Confirmation of Registration in <br> VBALANCE.Community Account
                        </h1>
                        <p
                                style="font-weight: 400; font-size: 14px; color: #465461; line-height: 21.7px; margin: 20px 0 0;">
                            Please enter this confirmation code into the <br> appropriate field on the registration
                            page:
                        </p>
                        <p
                                style="background-color: #fff; width: 100%; height: 40px; border-radius: 20px; color: #5EB1BF; font-weight: 700; font-size: 20px; text-align: center; border: none; outline: none;">
                            {{ $code }}
                        </p>
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