<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coach account approved</title>
</head>
<body style="margin:0;padding:24px;background:#0a0908;font-family:Arial,sans-serif;">
    <div style="max-width:520px;margin:0 auto;background:#14110e;border-radius:16px;padding:36px;color:#f5f1ea;">
        <h1 style="margin:0 0 12px;font-size:24px;">You're approved!</h1>

        <p style="color:#a8a095;line-height:1.6;">
            Hi {{ $coach->first_name ?: $coach->name }},
        </p>

        <p style="color:#a8a095;line-height:1.6;">
            Good news: an admin has approved your coach account on Gym Portal.
            You can now sign in and start using your coach dashboard.
        </p>

        <p style="margin:28px 0;">
            <a href="{{ route('login') }}"
               style="display:inline-block;padding:14px 26px;background:#ff9900;color:#1a1208;
                      font-weight:bold;text-decoration:none;border-radius:10px;">
                Sign in as Coach
            </a>
        </p>

        <p style="color:#a8a095;font-size:13px;line-height:1.6;">
            On the sign-in page, choose <b>Coach</b> before entering your email and password.
        </p>
    </div>
</body>
</html>