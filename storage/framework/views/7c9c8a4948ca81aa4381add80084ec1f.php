<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/index_files/logo.png')); ?>">
    <title>Become a Freelancer — GoTrips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gold: #FFD700;
            --secondary-gold: #FFA500;
            --dark-bg: #0a0a0b;
            --darker-bg: #060608;
            --light-dark: #131316;
            --card-bg: #16161a;
            --border-color: rgba(255, 255, 255, 0.06);
            --border-gold: rgba(255, 215, 0, 0.15);
            --text-main: #e2e8f0;
            --text-muted: #8a8f98;
            --radius-sm: 8px;
            --success: #22c55e;
            --danger: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--dark-bg); color: var(--text-main);
            min-height: 100vh;
        }
        .register-wrapper { min-height: 100vh; display: flex; }
        .brand-panel {
            width: 45%; background: var(--darker-bg);
            border-right: 1px solid var(--border-color);
            display: flex; flex-direction: column; justify-content: center;
            padding: 4rem 3.5rem; position: relative; overflow: hidden;
        }
        .brand-panel::before {
            content: ''; position: absolute;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(255,215,0,0.06) 0%, transparent 70%);
            top: -150px; left: -150px;
        }
        .brand-logo-mark { display: inline-flex; align-items: center; gap: 0.65rem; margin-bottom: 2rem; position: relative; z-index: 1; }
        .brand-logo-mark img { height: 44px; width: 44px; object-fit: contain; }
        .brand-logo-mark span { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; }
        .brand-tagline { font-size: 2.75rem; font-weight: 800; line-height: 1.1; letter-spacing: -0.03em; color: #fff; margin-bottom: 1.5rem; position: relative; z-index: 1; }
        .brand-tagline .accent { color: var(--primary-gold); display: block; }
        .brand-desc { color: var(--text-muted); font-size: 1rem; font-weight: 300; line-height: 1.7; margin-bottom: 3rem; position: relative; z-index: 1; }
        .benefit-list { list-style: none; position: relative; z-index: 1; }
        .benefit-list li { display: flex; gap: 1rem; margin-bottom: 1.25rem; }
        .benefit-icon { width: 40px; height: 40px; background: rgba(255,215,0,0.1); border: 1px solid var(--border-gold); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary-gold); flex-shrink: 0; }
        .benefit-text strong { display: block; font-weight: 600; font-size: 0.9rem; color: #fff; margin-bottom: 0.15rem; }
        .benefit-text span { font-size: 0.78rem; color: var(--text-muted); font-weight: 300; }
        .form-panel { flex: 1; display: flex; align-items: center; justify-content: center; padding: 3rem 2.5rem; overflow-y: auto; }
        .form-container { width: 100%; max-width: 480px; }
        .form-header { margin-bottom: 2rem; }
        .form-header h2 { font-size: 1.75rem; font-weight: 700; letter-spacing: -0.02em; color: #fff; margin-bottom: 0.4rem; }
        .form-header p { color: var(--text-muted); font-size: 0.875rem; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-group { margin-bottom: 1.125rem; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 500; color: var(--text-muted); margin-bottom: 0.4rem; }
        .form-control, textarea.form-control {
            width: 100%; background: var(--light-dark);
            border: 1px solid var(--border-color);
            color: var(--text-main); padding: 0.75rem 1rem;
            border-radius: var(--radius-sm); font-size: 0.9rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus, textarea.form-control:focus { outline: none; border-color: var(--primary-gold); box-shadow: 0 0 0 3px rgba(255,215,0,0.1); }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .alert-danger-custom { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: var(--danger); border-radius: 8px; padding: 0.875rem 1rem; font-size: 0.875rem; margin-bottom: 1.5rem; }
        .alert-danger-custom ul { margin: 0; padding-left: 1.25rem; }
        .btn-submit { width: 100%; background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%); border: none; color: #000; font-weight: 700; font-size: 0.95rem; padding: 0.9rem 1.5rem; border-radius: 8px; cursor: pointer; transition: all 0.2s; margin-top: 0.5rem; }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(255,215,0,0.2); }
        .signin-link { text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--text-muted); }
        .signin-link a { color: var(--primary-gold); text-decoration: none; font-weight: 500; }
        .section-divider { height: 1px; background: var(--border-color); margin: 1.5rem 0 1rem; }
        .section-title { font-size: 0.95rem; font-weight: 700; color: #fff; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem; }
        .section-title i { color: var(--primary-gold); }
        .section-sub { font-size: 0.75rem; color: var(--text-muted); margin: 0 0 0.85rem 0; line-height: 1.5; }
        @media (max-width: 991.98px) {
            .brand-panel { display: none; }
            .form-panel { padding: 2rem 1.25rem; align-items: flex-start; padding-top: 2.5rem; }
        }
        @media (max-width: 575.98px) {
            .form-row { grid-template-columns: 1fr; }
            .form-header h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="brand-panel">
            <div class="brand-logo-mark">
                <img src="<?php echo e(asset('assets/index_files/logo.png')); ?>" alt="GoTrips">
                <span>GoTrips</span>
            </div>
            <h1 class="brand-tagline">
                Sell travel.
                <span class="accent">Earn from anywhere.</span>
            </h1>
            <p class="brand-desc">
                Become a GoTrips freelancer in minutes. Sell UAE activities, visas, eSIMs and tour packages from your own storefront — and earn commissions on every booking.
            </p>
            <ul class="benefit-list">
                <li>
                    <div class="benefit-icon"><i class="fas fa-link"></i></div>
                    <div class="benefit-text"><strong>Personal storefront link</strong><span>Share on WhatsApp, Instagram or anywhere</span></div>
                </li>
                <li>
                    <div class="benefit-icon"><i class="fas fa-coins"></i></div>
                    <div class="benefit-text"><strong>Earn on every sale</strong><span>Live commission tracking, 24/7 dashboard</span></div>
                </li>
                <li>
                    <div class="benefit-icon"><i class="fas fa-bolt"></i></div>
                    <div class="benefit-text"><strong>Direct bank payouts</strong><span>Withdraw to your bank when you're ready</span></div>
                </li>
            </ul>
        </div>

        <div class="form-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2>Become a freelancer</h2>
                    <p>Set up your travel-selling storefront in under 2 minutes.</p>
                </div>

                <?php if($errors->any()): ?>
                <div class="alert-danger-custom">
                    <ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('freelancer.register.submit')); ?>" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" placeholder="Jane Smith" required autofocus>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone *</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo e(old('phone')); ?>" placeholder="+971 50 ..." required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Country *</label>
                        <input type="text" name="country" class="form-control" value="<?php echo e(old('country')); ?>" placeholder="United Arab Emirates" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Profile Headline <span style="color: var(--text-muted); font-size: 0.7rem;">(optional)</span></label>
                        <input type="text" name="profile_headline" class="form-control" value="<?php echo e(old('profile_headline')); ?>" placeholder="e.g. UAE travel advisor · Hajj specialist">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Services You Want to Sell <span style="color: var(--text-muted); font-size: 0.7rem;">(optional)</span></label>
                        <textarea name="services_offered" class="form-control" placeholder="e.g. Activities in Dubai & Abu Dhabi, eSIMs, UAE visas"><?php echo e(old('services_offered')); ?></textarea>
                    </div>

                    <div class="section-divider"></div>

                    <div class="section-title"><i class="fas fa-university"></i>Bank Details (for payouts)</div>
                    <p class="section-sub">We pay your earned commissions directly into this account. You can update later from your dashboard.</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Bank Name *</label>
                            <input type="text" name="bank_name" class="form-control" value="<?php echo e(old('bank_name')); ?>" placeholder="e.g. Emirates NBD" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Account Holder *</label>
                            <input type="text" name="account_holder_name" class="form-control" value="<?php echo e(old('account_holder_name')); ?>" placeholder="As on bank record" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Account Number *</label>
                        <input type="text" name="account_number" class="form-control" value="<?php echo e(old('account_number')); ?>" placeholder="Account / IBAN number" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">IBAN <span style="color: var(--text-muted); font-size: 0.7rem;">(optional)</span></label>
                            <input type="text" name="iban" class="form-control" value="<?php echo e(old('iban')); ?>" placeholder="AE07 0331 ...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SWIFT/BIC <span style="color: var(--text-muted); font-size: 0.7rem;">(optional)</span></label>
                            <input type="text" name="swift_code" class="form-control" value="<?php echo e(old('swift_code')); ?>" placeholder="EBILAEAD">
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit"><i class="fas fa-rocket" style="margin-right:8px;"></i>Create my storefront</button>
                </form>

                <p class="signin-link">
                    Already have an account? <a href="<?php echo e(route('referral.login')); ?>">Sign in instead</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/freelancer/register.blade.php ENDPATH**/ ?>