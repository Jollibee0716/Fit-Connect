<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verify sign in</title>
<style>
:root{--panel:#18130f;--ink:#fff;--mute:#a8a29e;--line:rgba(255,255,255,.12);--accent:#ff9900}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;place-items:center;padding:20px;font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:var(--ink);background:radial-gradient(60% 50% at 15% 10%,rgba(255,153,0,.22),transparent 70%),#0a0908}
.card{width:100%;max-width:400px;background:var(--panel);border:1px solid var(--line);border-radius:14px;padding:28px}
h1{margin:0 0 6px;font-size:22px}
p{margin:0 0 18px;color:var(--mute);font-size:14px;line-height:1.5}
input{width:100%;background:var(--panel);color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:12px;font:inherit;font-size:24px;letter-spacing:8px;text-align:center}
input:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
.btn{width:100%;margin-top:14px;background:var(--accent);color:#1a1208;border:0;border-radius:8px;padding:11px 16px;font:inherit;font-weight:600;cursor:pointer}
.link{background:none;border:0;color:var(--accent);font:inherit;font-size:13px;cursor:pointer;padding:0}
.link:hover{text-decoration:underline}
.err{color:#ff8a65;font-size:13px;margin-top:8px;display:block}
.ok{color:#4ade80;font-size:13px;margin-bottom:12px;display:block}
.row{display:flex;justify-content:space-between;margin-top:16px}
</style>
</head>
<body>
<div class="card">
  <h1>Check your email</h1>
  <p>We sent a 6-digit code to your email address. Enter it below to finish signing in.</p>

  @if (session('status'))<span class="ok">{{ session('status') }}</span>@endif

  <form method="POST" action="{{ route('two-factor.verify') }}">
    @csrf
    <input name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000" autofocus aria-label="6-digit code">
    @error('code')<span class="err">{{ $message }}</span>@enderror
    <button class="btn" type="submit">Verify and sign in</button>
  </form>

  <div class="row">
    <form method="POST" action="{{ route('two-factor.resend') }}">@csrf<button class="link" type="submit">Resend code</button></form>
    <a class="link" href="{{ route('login') }}">Back to sign in</a>
  </div>
</div>
</body>
</html>