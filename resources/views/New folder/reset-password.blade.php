<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Gym Portal</title>

    <style>
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #0a0908;
            color: #f5f1ea;
            font-family: Arial, sans-serif;
        }
        .card {
            width: 100%;
            max-width: 460px;
            padding: 40px;
            background: #14110e;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,.5);
        }
        h1 { margin-bottom: 10px; }
        .sub { color: #a8a095; margin-bottom: 25px; }
        .field { margin-bottom: 20px; }
        label { display: block; margin-bottom: 7px; font-weight: bold; }
        input {
            width: 100%;
            padding: 14px;
            box-sizing: border-box;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 10px;
            color: white;
            font-size: 16px;
        }
        input.input-error {
            border-color: #ff5555;
            background: rgba(255, 85, 85, .08);
        }
        .field-error { color: #ff5555; font-size: 14px; margin-top: 6px; }
        .btn {
            width: 100%;
            padding: 15px;
            background: #ff9900;
            border: none;
            border-radius: 10px;
            color: #1a1208;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .back {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #ff9900;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Reset Password</h1>
    <p class="sub">Enter your new password below.</p>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $email) }}"
                class="{{ $errors->has('email') ? 'input-error' : '' }}"
                required
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password">New Password</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                class="{{ $errors->has('password') ? 'input-error' : '' }}"
                required
            >
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                minlength="8"
                required
            >
        </div>

        <button type="submit" class="btn">Reset Password</button>
    </form>

    <a href="{{ route('login') }}" class="back">← Back to Sign In</a>
</div>

</body>
</html>