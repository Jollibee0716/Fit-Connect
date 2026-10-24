<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Sign in | Gym Portal</title>
<style>
:root{--ink:#1a1208;--text:#f5f1ea;--muted:#a8a095;--radius:14px;--role-color:#ff9900;--ease:cubic-bezier(.16,1,.3,1)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;display:grid;place-items:center;padding:24px 16px;color:var(--text);font-family:Poppins,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;line-height:1.5;background:radial-gradient(60% 50% at 15% 10%,rgba(255,153,0,.22),transparent 70%),radial-gradient(50% 45% at 90% 85%,rgba(204,85,0,.22),transparent 70%),#0a0908}
button,input{font:inherit;color:inherit}button{cursor:pointer}
:focus-visible{outline:3px solid var(--role-color);outline-offset:3px}
.card{width:100%;max-width:460px;padding:40px 36px 34px;background:rgba(20,17,14,.78);-webkit-backdrop-filter:blur(18px);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:0 24px 60px rgba(0,0,0,.55);animation:rise .7s var(--ease) both}
@keyframes rise{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}
.card:has(#role-coach:checked){--role-color:#ffcc00}
.card:has(#role-admin:checked){--role-color:#ff5a1f}
.brand{display:flex;align-items:center;gap:10px;margin-bottom:26px;font-weight:700;letter-spacing:2px;text-transform:uppercase;font-size:14px;color:var(--muted)}
.brand svg{width:26px;height:26px;fill:var(--role-color)}
h1{font-size:clamp(26px,5vw,32px);line-height:1.15;letter-spacing:-.5px}
.sub{margin:6px 0 26px;color:var(--muted);font-size:15px}
.roles{position:relative;display:grid;grid-template-columns:repeat(3,1fr);padding:4px;margin-bottom:26px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:var(--radius)}
.roles::before{content:"";position:absolute;top:4px;bottom:4px;left:4px;width:calc((100% - 8px)/3);background:var(--role-color);border-radius:10px;transition:transform .4s var(--ease),background-color .4s}
.roles:has(#role-coach:checked)::before{transform:translateX(100%)}
.roles:has(#role-admin:checked)::before{transform:translateX(200%)}
.roles input{position:absolute;opacity:0;pointer-events:none}
.roles label{position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;gap:4px;padding:10px 6px;border-radius:10px;color:var(--muted);font-size:13px;font-weight:600;cursor:pointer;transition:color .3s}
.roles label svg{width:22px;height:22px;fill:currentColor}
.roles input:checked+label{color:var(--ink)}
.roles input:focus-visible+label{outline:3px solid var(--text);outline-offset:2px}
.field{margin-bottom:18px}
.field label{display:block;margin-bottom:7px;font-size:14px;font-weight:600}
.field input{width:100%;padding:14px 16px;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.12);border-radius:var(--radius);font-size:16px;transition:border-color .25s,box-shadow .25s}
.field input::placeholder{color:#8a8379}
.field input:focus{outline:none;border-color:var(--role-color);box-shadow:0 0 0 4px color-mix(in srgb,var(--role-color) 25%,transparent)}
.row{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin:4px 0 22px;font-size:14px}
.check{display:flex;align-items:center;gap:9px;color:var(--muted);cursor:pointer}
.check input{width:18px;height:18px;accent-color:var(--role-color)}
.row a{color:var(--role-color);text-decoration:none;font-weight:600}.row a:hover{text-decoration:underline}
.btn{display:flex;align-items:center;justify-content:center;width:100%;padding:15px 20px;background:var(--role-color);border:0;border-radius:var(--radius);color:var(--ink);font-size:16px;font-weight:700;transition:transform .2s,filter .2s}
.btn:hover{transform:translateY(-2px);filter:brightness(1.08)}

.view{display:none}
#login{display:block}
body:has(#register:target) #login{display:none}
#register:target{display:block}
.back{display:inline-flex;align-items:center;gap:6px;margin-bottom:22px;padding:8px 14px 8px 10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:999px;color:var(--text);font-size:14px;font-weight:600;text-decoration:none;transition:background-color .2s}
.back:hover{background:rgba(255,255,255,.12)}
.back svg{width:18px;height:18px;fill:currentColor}
.alt{margin-top:22px;text-align:center;font-size:14px;color:var(--muted)}
.alt a{color:var(--role-color);font-weight:600;text-decoration:none}.alt a:hover{text-decoration:underline}
.field input[type=email]{width:100%;padding:14px 16px;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.12);border-radius:var(--radius);font-size:16px}
.field input[type=email]:focus{outline:none;border-color:var(--role-color);box-shadow:0 0 0 4px color-mix(in srgb,var(--role-color) 25%,transparent)}
.field input[type=email]::placeholder{color:#8a8379}

#login:has(#role-admin:checked) .alt{display:none}
#login:has(#role-admin:checked) .forgot{display:none}
.roles.two{grid-template-columns:repeat(2,1fr)}
.roles.two::before{width:calc((100% - 8px)/2)}
#register:has(#reg-coach:checked){--role-color:#ffcc00}
.roles:has(#reg-coach:checked)::before{transform:translateX(100%)}

/* Gender selector */
.group-label{display:block;margin-bottom:7px;font-size:14px;font-weight:600}
.field .roles{margin-bottom:0}
.field .roles label{display:flex;margin-bottom:0}
.roles:has(#reg-female:checked)::before{transform:translateX(100%)}

/* Extra register fields */
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.field input[type=date]{color-scheme:dark}
.field textarea{width:100%;padding:14px 16px;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.12);border-radius:var(--radius);font:inherit;font-size:16px;color:inherit;resize:vertical}
.coach-only{display:none}
#register:has(#reg-coach:checked) .coach-only{display:block}
#register:has(#reg-coach:checked) .member-only{display:none}

/* Flash messages */
.flash{position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:50;width:calc(100% - 32px);max-width:460px;display:grid;gap:8px}
.flash p{padding:12px 16px;border-radius:12px;font-size:14px;font-weight:600;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px)}
.flash .ok{background:rgba(74,222,128,.12);border:1px solid rgba(74,222,128,.4);color:#4ade80}
.flash .err{background:rgba(255,85,85,.12);border:1px solid rgba(255,85,85,.4);color:#ff7a7a}

@media(max-width:480px){.card{padding:30px 22px 26px;border-radius:18px}.grid2{grid-template-columns:1fr}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}}
</style>
</head>
<body>

  @if ($errors->any() || session('success'))
    <div class="flash" role="status">
      @if (session('success'))
        <p class="ok">{{ session('success') }}</p>
      @endif
      @foreach ($errors->all() as $error)
        <p class="err">{{ $error }}</p>
      @endforeach
    </div>
  @endif

  {{-- Reopen the register form if registration failed validation --}}
  @if (old('_form') === 'register')
    <script>location.hash = '#register';</script>
  @endif

<!-- ===================== LOGIN ===================== -->
<main class="card view" id="login">
  <div class="brand">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 4 14h6l-1 8 9-12h-6z"/></svg>
    Gym Portal
  </div>
  <h1>Member login</h1>
  <p class="sub">Track your workouts and book classes.</p>

  <form action="{{ route('login.submit') }}" method="POST">
    @csrf

    @php $loginRole = old('_form') === 'login' ? old('role', 'member') : 'member'; @endphp

    <div class="roles" role="radiogroup" aria-label="Sign in as">
      <input type="radio" name="role" id="role-user" value="member" {{ $loginRole === 'member' ? 'checked' : '' }}>
      <label for="role-user">Member</label>

      <input type="radio" name="role" id="role-coach" value="gym_owner" {{ $loginRole === 'gym_owner' ? 'checked' : '' }}>
      <label for="role-coach">Gym Owner</label>

      <input type="radio" name="role" id="role-admin" value="admin" {{ $loginRole === 'admin' ? 'checked' : '' }}>
      <label for="role-admin">Admin</label>
    </div>

    <input type="hidden" name="_form" value="login">

    <div class="field">
      <label for="username">Email</label>
      <input type="email" id="username" name="email"
             value="{{ old('_form') === 'login' ? old('email') : '' }}"
             autocomplete="username" autocapitalize="none" spellcheck="false"
             placeholder="you@example.com" required>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password"
             autocomplete="current-password" placeholder="Enter your password" required>
    </div>

    <div class="row">
      <label class="check">
        <input type="checkbox" name="remember">
        Remember me
      </label>
      <a class="forgot" href="{{ route('password.request') }}">Forgot password?</a>
    </div>

    <button type="submit" class="btn">Sign in</button>
  </form>

  <p class="alt">New member? <a href="#register">Create account</a></p>
</main>

<!-- ===================== REGISTER ===================== -->
<main class="card view" id="register">
  <a class="back" href="#login"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11H7.8l5.6-5.6L12 4l-8 8 8 8 1.4-1.4L7.8 13H20z"/></svg>Back to sign in</a>

  <div class="brand">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 4 14h6l-1 8 9-12h-6z"/></svg>
    Gym Portal
  </div>
  <h1>Create account</h1>
  <p class="sub">Members sign in right away. Gym Owner accounts need admin approval first.</p>

  <form action="{{ route('register') }}" method="POST">
    @csrf
    <input type="hidden" name="_form" value="register">

    <div class="roles two" role="radiogroup" aria-label="Create account as">
      <input type="radio" name="role" id="reg-user" value="member" {{ old('role', 'member') === 'member' ? 'checked' : '' }}>
      <label for="reg-user"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4.4 0-8 2.2-8 5v3h16v-3c0-2.8-3.6-5-8-5z"/></svg>Member</label>
      <input type="radio" name="role" id="reg-coach" value="gym_owner" {{ old('role') === 'gym_owner' ? 'checked' : '' }}>
      <label for="reg-coach"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 4h7v2h-2.2l-3 12H19v2H5v-2h3.2l3-12H9V4z"/></svg>Gym Owner</label>
    </div>

    <div class="field">
      <label for="reg-name">Username</label>
      <input type="text" id="reg-name" name="name" value="{{ old('name') }}" autocomplete="username" autocapitalize="none" spellcheck="false" placeholder="e.g. juan123">
    </div>

    <div class="field">
      <label for="reg-email">Email</label>
      <input type="email" id="reg-email" name="email" value="{{ old('_form') === 'register' ? old('email') : '' }}" autocomplete="email" placeholder="you@example.com">
    </div>

    <!-- Personal details -->
    <div class="grid2">
      <div class="field">
        <label for="reg-first">First name</label>
        <input type="text" id="reg-first" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name">
      </div>
      <div class="field">
        <label for="reg-last">Last name</label>
        <input type="text" id="reg-last" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name">
      </div>
    </div>

    <div class="grid2">
      <div class="field">
        <label for="reg-contact">Contact number</label>
        <input type="text" id="reg-contact" name="contact_number" value="{{ old('contact_number') }}" autocomplete="tel">
      </div>
      <div class="field">
        <label for="reg-dob">Date of birth</label>
        <input type="date" id="reg-dob" name="date_of_birth" value="{{ old('date_of_birth') }}">
      </div>
    </div>

    <div class="field">
      <label for="reg-address">Address</label>
      <input type="text" id="reg-address" name="address" value="{{ old('address') }}" autocomplete="street-address">
    </div>

    <!-- Gender -->
    <div class="field">
      <span class="group-label" id="gender-label">Gender</span>
      <div class="roles two" role="radiogroup" aria-labelledby="gender-label">
        <input type="radio" name="gender" id="reg-male" value="Male" {{ old('gender', 'Male') === 'Male' ? 'checked' : '' }}>
        <label for="reg-male">Male</label>
        <input type="radio" name="gender" id="reg-female" value="Female" {{ old('gender') === 'Female' ? 'checked' : '' }}>
        <label for="reg-female">Female</label>
      </div>
    </div>

    <!-- Member only -->
    <div class="member-only">
      <div class="field">
        <label for="reg-ecn">Emergency contact name</label>
        <input type="text" id="reg-ecn" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
      </div>
      <div class="field">
        <label for="reg-ecnum">Emergency contact number</label>
        <input type="text" id="reg-ecnum" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}">
      </div>
      <div class="grid2">
        <div class="field">
          <label for="reg-height">Height (cm)</label>
          <input type="number" step="0.01" id="reg-height" name="height_cm" value="{{ old('height_cm') }}">
        </div>
        <div class="field">
          <label for="reg-weight">Weight (kg)</label>
          <input type="number" step="0.01" id="reg-weight" name="weight_kg" value="{{ old('weight_kg') }}">
        </div>
      </div>
      <div class="field">
        <label for="reg-goal">Fitness goal</label>
        <input type="text" id="reg-goal" name="fitness_goal" value="{{ old('fitness_goal') }}">
      </div>
    </div>

    <!-- Coach only -->
    <div class="coach-only">
      <div class="field">
        <label for="reg-spec">Specialty</label>
        <input type="text" id="reg-spec" name="specialty" value="{{ old('specialty') }}">
      </div>
      <div class="field">
        <label for="reg-cert">Certification</label>
        <input type="text" id="reg-cert" name="certification" value="{{ old('certification') }}">
      </div>
      <div class="field">
        <label for="reg-years">Years of experience</label>
        <input type="number" min="0" id="reg-years" name="years_experience" value="{{ old('years_experience', 0) }}">
      </div>
      <div class="field">
        <label for="reg-bio">Bio</label>
        <textarea id="reg-bio" name="bio" rows="3">{{ old('bio') }}</textarea>
      </div>
    </div>

    <div class="field">
      <label for="reg-pass">Password</label>
      <input type="password" id="reg-pass" name="password" autocomplete="new-password" placeholder="At least 8 characters">
    </div>
    <div class="field">
      <label for="reg-pass2">Confirm password</label>
      <input type="password" id="reg-pass2" name="password_confirmation" autocomplete="new-password" placeholder="Repeat your password">
    </div>

    <button type="submit" class="btn">Create account</button>
  </form>

  <p class="alt">Already have an account? <a href="#login">Sign in</a></p>
</main>
</body>
</html>