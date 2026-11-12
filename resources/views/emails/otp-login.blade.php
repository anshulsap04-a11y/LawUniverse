<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Instant Login OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f9fc; padding: 30px; margin: 0;">
    <table align="center" width="560" style="background-color: #ffffff; border-radius: 8px; border: 1px solid #e3e8ee; padding: 30px;">
        <tr>
            <td align="center" style="padding-bottom: 20px;">
                <h2 style="color: #111; margin: 0;">Law Universe</h2>
                <p style="color: #666; font-size: 14px; margin-top: 5px;">Instant Authentication System</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="font-size: 15px; color: #333;">Hello,</p>
                <p style="font-size: 15px; color: #333;">Your one-time password (OTP) for instant login to the Law Universe portal is:</p>
                <div style="text-align: center; margin: 25px 0;">
                    <span style="display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #0275d8; background: #eaf3fd; padding: 12px 24px; border-radius: 6px; border: 1px dashed #0275d8;">
                        {{ $otpCode }}
                    </span>
                </div>
                <p style="font-size: 13px; color: #777;">This OTP is valid for {{ $validityMinutes }} minutes. If you did not request this login, please ignore this email.</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
                <p style="font-size: 12px; color: #999; text-align: center;">© Law Universe Portal. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
