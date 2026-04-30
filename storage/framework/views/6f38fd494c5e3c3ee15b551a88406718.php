<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin · GoTrips</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/index_files/logo.png')); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #050505;
            --card: #0d0d0d;
            --border: rgba(255, 215, 0, 0.18);
            --gold: #FFD700;
            --gold-deep: #D4AF37;
            --text: #f0f0f0;
            --muted: #8a8f98;
            --danger: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 700px 400px at 80% 10%, rgba(255,215,0,0.08), transparent 60%),
                radial-gradient(ellipse 600px 400px at 10% 90%, rgba(212,175,55,0.05), transparent 60%);
            pointer-events: none;
            z-index: 0;
        }
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 44px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
        }
        .brand {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 36px;
        }
        .brand img {
            width: 40px; height: 40px;
            object-fit: contain;
        }
        .brand-text {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .brand-text .gold { color: var(--gold); }
        .brand-badge {
            margin-left: auto;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: var(--gold);
            background: rgba(255,215,0,0.08);
            border: 1px solid rgba(255,215,0,0.25);
            padding: 5px 10px;
            border-radius: 99px;
            text-transform: uppercase;
        }
        h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.025em;
            line-height: 1.15;
            margin-bottom: 8px;
        }
        .lede {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 32px;
            line-height: 1.55;
        }
        .field { margin-bottom: 18px; }
        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 14px;
            pointer-events: none;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            background: #131313;
            border: 1px solid #222;
            color: var(--text);
            padding: 13px 16px 13px 44px;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Outfit', sans-serif;
            transition: border-color 0.15s, box-shadow 0.15s;
            -webkit-appearance: none;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(255,215,0,0.12);
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            cursor: pointer;
            user-select: none;
        }
        .checkbox input { accent-color: var(--gold); }
        .submit {
            width: 100%;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-deep) 100%);
            color: #0a0a0a;
            border: none;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.5px;
            padding: 14px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
        }
        .submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 32px rgba(255,215,0,0.3);
        }
        .alert {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.25);
            color: #fca5a5;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.5;
        }
        .footer-note {
            text-align: center;
            margin-top: 28px;
            font-size: 12px;
            color: var(--muted);
        }
        .footer-note a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <img src="<?php echo e(asset('assets/index_files/logo.png')); ?>" alt="GoTrips">
            <div class="brand-text"><span class="gold">GoTrips</span></div>
            <span class="brand-badge"><i class="fas fa-shield-halved" style="margin-right:4px;"></i>Super Admin</span>
        </div>

        <h1>Restricted access</h1>
        <p class="lede">Sign in with your super-admin credentials to manage tenants, agents, and platform settings.</p>

        <?php if($errors->any()): ?>
            <div class="alert">
                <i class="fas fa-circle-exclamation" style="margin-top:2px;"></i>
                <div><?php echo e($errors->first()); ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('superadmin.login.submit')); ?>" novalidate>
            <?php echo csrf_field(); ?>
            <div class="field">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>"
                           placeholder="admin@gotrips.ai" required autofocus autocomplete="email">
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password"
                           placeholder="Enter your password" required autocomplete="current-password">
                </div>
            </div>

            <div class="row">
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1">
                    Remember me
                </label>
            </div>

            <button type="submit" class="submit">
                <i class="fas fa-key" style="margin-right:6px;"></i>Sign in
            </button>
        </form>

        <p class="footer-note">
            Not a super admin? <a href="/">Go to homepage</a>
        </p>
    </div>
</body>
</html>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/superadmin/auth/login.blade.php ENDPATH**/ ?>