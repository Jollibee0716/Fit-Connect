<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Gym Portal</title>

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

        h1 {
            margin-bottom: 10px;
        }

        .sub {
            color: #a8a095;
            margin-bottom: 25px;
        }

        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

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

        .field-error {
            color: #ff5555;
            font-size: 14px;
            margin-top: 6px;
        }

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

        .success {
            color: #4ade80;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="card">

    <h1>Forgot Password?</h1>

    <p class="sub">
        Enter your email and we'll send you a password reset link.
    </p>

    <?php if(session('success')): ?>
        <div class="success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('password.email')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="field">
            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="you@example.com"
                value="<?php echo e(old('email')); ?>"
                class="<?php echo e($errors->has('email') ? 'input-error' : ''); ?>"
                required
            >

            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="field-error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <button type="submit" class="btn">
            Send Reset Link
        </button>
    </form>

    <a href="<?php echo e(route('login')); ?>" class="back">
        ← Back to Sign In
    </a>

</div>

</body>
</html><?php /**PATH C:\xampp\htdocs\dashboard\firstwebsite\resources\views\New folder\forgot-password.blade.php ENDPATH**/ ?>