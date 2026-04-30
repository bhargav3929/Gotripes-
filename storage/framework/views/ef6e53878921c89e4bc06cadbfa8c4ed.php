<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/index_files/logo.png')); ?>">

    <title>Join the Partner Program - GoTrips</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
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
            --radius-md: 12px;
            --radius-lg: 16px;
            --success: #22c55e;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--dark-bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Two-column layout wrapper */
        .register-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* Left brand panel */
        .brand-panel {
            width: 45%;
            background: var(--darker-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem 3.5rem;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.06) 0%, transparent 70%);
            top: -150px;
            left: -150px;
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.04) 0%, transparent 70%);
            bottom: -100px;
            right: -100px;
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-gold);
            margin-bottom: 3rem;
            letter-spacing: -0.02em;
            position: relative;
            z-index: 1;
        }

        .brand-logo span {
            color: var(--text-main);
        }

        .brand-logo-mark {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .brand-logo-mark img {
            height: 44px;
            width: 44px;
            object-fit: contain;
            display: block;
            flex-shrink: 0;
        }

        .brand-logo-mark span {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .brand-tagline {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: #ffffff;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .brand-tagline .accent {
            color: var(--primary-gold);
            display: block;
        }

        .brand-desc {
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 300;
            line-height: 1.7;
            margin-bottom: 3rem;
            position: relative;
            z-index: 1;
        }

        .benefit-list {
            list-style: none;
            position: relative;
            z-index: 1;
        }

        .benefit-list li {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
            color: var(--text-main);
        }

        .benefit-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid var(--border-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--primary-gold);
            font-size: 0.9rem;
        }

        .benefit-text strong {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            color: #ffffff;
            margin-bottom: 0.15rem;
        }

        .benefit-text span {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 300;
        }

        .brand-footer {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
            position: relative;
            z-index: 1;
        }

        .trust-badges {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .trust-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .trust-badge i {
            color: var(--primary-gold);
        }

        /* Right form panel */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            overflow-y: auto;
        }

        .form-container {
            width: 100%;
            max-width: 460px;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 0.4rem;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 400;
        }

        /* Alerts */
        .alert-success-custom {
            background: rgba(34, 197, 94, 0.08);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: var(--success);
            border-radius: var(--radius-sm);
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger);
            border-radius: var(--radius-sm);
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger-custom ul {
            margin: 0;
            padding-left: 1.25rem;
        }

        .alert-danger-custom ul li {
            color: var(--danger);
        }

        /* Form fields */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.125rem;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.8rem;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            background: var(--light-dark);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            -webkit-appearance: none;
        }

        .form-control:focus {
            outline: none;
            background: var(--light-dark);
            border-color: var(--primary-gold);
            color: var(--text-main);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .form-control::placeholder {
            color: rgba(138, 143, 152, 0.6);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .field-error {
            font-size: 0.72rem;
            color: var(--danger);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* Country select chevron */
        .country-wrap .country-chevron {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.7rem;
            pointer-events: none;
            transition: color 0.15s ease;
        }

        .country-wrap input:focus ~ .country-chevron {
            color: var(--primary-gold);
        }

        .country-wrap input {
            padding-right: 2.5rem;
        }

        /* Hide native datalist dropdown arrow on webkit while keeping list functionality */
        .country-wrap input::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            right: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* Password toggle */
        .password-wrap {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            font-size: 0.8rem;
            transition: color 0.15s ease;
        }

        .password-toggle:hover {
            color: var(--text-main);
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
            border: none;
            color: #000;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.9rem 1.5rem;
            border-radius: var(--radius-sm);
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: 0.01em;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(255, 215, 0, 0.2);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit i {
            margin-right: 0.5rem;
        }

        /* Sign in link */
        .signin-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .signin-link a {
            color: var(--primary-gold);
            text-decoration: none;
            font-weight: 500;
        }

        .signin-link a:hover {
            text-decoration: underline;
        }

        /* Terms note */
        .terms-note {
            text-align: center;
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 1rem;
            line-height: 1.5;
        }

        /* Divider */
        .section-divider {
            height: 1px;
            background: var(--border-color);
            margin: 1.25rem 0;
        }

        /* Mobile logo (shown only on mobile) */
        .mobile-logo {
            display: none;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-gold);
            margin-bottom: 1.5rem;
        }

        .mobile-logo span {
            color: var(--text-main);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .brand-panel {
                display: none;
            }

            .form-panel {
                padding: 2rem 1.25rem;
                align-items: flex-start;
                padding-top: 2.5rem;
            }

            .form-container {
                max-width: 100%;
            }

            .mobile-logo {
                display: block;
            }

            .brand-tagline {
                font-size: 2rem;
            }
        }

        @media (max-width: 575.98px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }
        }

        /* Text visibility */
        p, span, div, li, small {
            color: #e2e8f0;
        }
        .text-muted {
            color: #a0aec0 !important;
        }
        h1, h2, h3, h4, h5, h6, strong {
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="register-wrapper">

        <!-- Left Brand Panel -->
        <div class="brand-panel">
            <div class="brand-logo-mark">
                <img src="<?php echo e(asset('assets/index_files/logo.png')); ?>" alt="GoTrips">
                <span>GoTrips</span>
            </div>

            <h1 class="brand-tagline">
                Join the GoTrips
                <span class="accent">Partner Program</span>
            </h1>

            <p class="brand-desc">
                Turn your network into revenue. Earn commissions on every eSIM sale
                through your unique referral link — paid directly to your bank.
            </p>

            <ul class="benefit-list">
                <li>
                    <div class="benefit-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="benefit-text">
                        <strong>Earn on every eSIM sale</strong>
                        <span>Competitive commissions on each purchase your referrals make</span>
                    </div>
                </li>
                <li>
                    <div class="benefit-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="benefit-text">
                        <strong>Real-time earnings dashboard</strong>
                        <span>Track clicks, conversions, and commissions live, 24/7</span>
                    </div>
                </li>
                <li>
                    <div class="benefit-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="benefit-text">
                        <strong>Instant bank transfers</strong>
                        <span>Request payouts directly to your bank account, no delays</span>
                    </div>
                </li>
            </ul>

            <div class="brand-footer">
                <div class="trust-badges">
                    <div class="trust-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure &amp; Encrypted</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-globe"></i>
                        <span>190+ Countries</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-check-circle"></i>
                        <span>Trusted by 10,000+</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="form-panel">
            <div class="form-container">

                <div class="mobile-logo">
                    <img src="<?php echo e(asset('assets/index_files/logo.png')); ?>" alt="GoTrips" style="height:34px;width:34px;object-fit:contain;vertical-align:middle;">
                    <span style="font-size:1rem;color:#fff;font-weight:700;margin-left:0.5rem;">GoTrips</span>
                    <span style="font-size:0.8rem;color:var(--text-muted);font-weight:400;margin-left:0.4rem;">Partner</span>
                </div>

                <div class="form-header">
                    <h2>Create your account</h2>
                    <p>Start earning in under 2 minutes — no approval wait time.</p>
                </div>

                
                <?php if(session('success')): ?>
                <div class="alert-success-custom">
                    <i class="fas fa-check-circle" style="margin-top: 1px; flex-shrink: 0;"></i>
                    <span><?php echo e(session('success')); ?></span>
                </div>
                <?php endif; ?>

                
                <?php if($errors->any()): ?>
                <div class="alert-danger-custom">
                    <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <i class="fas fa-exclamation-circle" style="color: var(--danger); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 0.25rem; color: var(--danger);">Please fix the following:</div>
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(url('/partner/register')); ?>" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name <span style="color: var(--danger);">*</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-user input-icon"></i>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control <?php echo e($errors->has('name') ? 'is-invalid' : ''); ?>"
                                    value="<?php echo e(old('name')); ?>"
                                    placeholder="Jane Smith"
                                    required
                                    autofocus
                                >
                            </div>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number <span style="color: var(--danger);">*</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-phone input-icon"></i>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    class="form-control <?php echo e($errors->has('phone') ? 'is-invalid' : ''); ?>"
                                    value="<?php echo e(old('phone')); ?>"
                                    placeholder="+971 50 123 4567"
                                    required
                                >
                            </div>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address <span style="color: var(--danger);">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope input-icon"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control <?php echo e($errors->has('email') ? 'is-invalid' : ''); ?>"
                                value="<?php echo e(old('email')); ?>"
                                placeholder="you@example.com"
                                required
                            >
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="country">Country <span style="color: var(--danger);">*</span></label>
                        <div class="input-wrap country-wrap">
                            <i class="fas fa-globe input-icon"></i>
                            <input
                                type="text"
                                id="country"
                                name="country"
                                class="form-control <?php echo e($errors->has('country') ? 'is-invalid' : ''); ?>"
                                value="<?php echo e(old('country')); ?>"
                                placeholder="Start typing your country"
                                autocomplete="country-name"
                                list="country-options"
                                required
                            >
                            <i class="fas fa-chevron-down country-chevron"></i>
                        </div>
                        <datalist id="country-options"></datalist>
                        <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="section-divider"></div>

                    
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                            <i class="fas fa-university" style="color: var(--primary-gold);"></i>
                            <h3 style="font-size: 0.95rem; font-weight: 700; color:#fff; margin:0; letter-spacing:-0.01em;">Bank Details (for commission payouts)</h3>
                        </div>
                        <p style="font-size:0.75rem; color: var(--text-muted); margin: 0 0 0.85rem 0; line-height:1.5;">
                            We pay your earned commissions directly into this account. You can update these later from your dashboard.
                        </p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="bank_name">Bank Name <span style="color: var(--danger);">*</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-university input-icon"></i>
                                <input type="text" id="bank_name" name="bank_name"
                                    class="form-control <?php echo e($errors->has('bank_name') ? 'is-invalid' : ''); ?>"
                                    value="<?php echo e(old('bank_name')); ?>" placeholder="e.g. Emirates NBD" required>
                            </div>
                            <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="account_holder_name">Account Holder Name <span style="color: var(--danger);">*</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" id="account_holder_name" name="account_holder_name"
                                    class="form-control <?php echo e($errors->has('account_holder_name') ? 'is-invalid' : ''); ?>"
                                    value="<?php echo e(old('account_holder_name')); ?>" placeholder="As on bank record" required>
                            </div>
                            <?php $__errorArgs = ['account_holder_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="account_number">Account Number <span style="color: var(--danger);">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-hashtag input-icon"></i>
                            <input type="text" id="account_number" name="account_number"
                                class="form-control <?php echo e($errors->has('account_number') ? 'is-invalid' : ''); ?>"
                                value="<?php echo e(old('account_number')); ?>" placeholder="Account / IBAN number" required>
                        </div>
                        <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="iban">IBAN <span style="font-size:0.7rem; color:var(--text-muted); font-weight:400;">(optional)</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-file-invoice input-icon"></i>
                                <input type="text" id="iban" name="iban"
                                    class="form-control <?php echo e($errors->has('iban') ? 'is-invalid' : ''); ?>"
                                    value="<?php echo e(old('iban')); ?>" placeholder="AE07 0331 ...">
                            </div>
                            <?php $__errorArgs = ['iban'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="swift_code">SWIFT / BIC <span style="font-size:0.7rem; color:var(--text-muted); font-weight:400;">(optional)</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-code input-icon"></i>
                                <input type="text" id="swift_code" name="swift_code"
                                    class="form-control <?php echo e($errors->has('swift_code') ? 'is-invalid' : ''); ?>"
                                    value="<?php echo e(old('swift_code')); ?>" placeholder="EBILAEAD">
                            </div>
                            <?php $__errorArgs = ['swift_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password <span style="color: var(--danger);">*</span></label>
                        <div class="input-wrap password-wrap">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control <?php echo e($errors->has('password') ? 'is-invalid' : ''); ?>"
                                placeholder="Min. 8 characters"
                                required
                                style="padding-right: 3rem;"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password <span style="color: var(--danger);">*</span></label>
                        <div class="input-wrap password-wrap">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Repeat your password"
                                required
                                style="padding-right: 3rem;"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus"></i>Create Account
                    </button>
                </form>

                <p class="signin-link">
                    Already have an account?
                    <a href="<?php echo e(route('referral.login')); ?>">Sign in instead</a>
                </p>

                <p class="terms-note">
                    By creating an account, you agree to GoTrips' Partner Terms &amp; Conditions.
                </p>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Populate country datalist from REST Countries (no fixed list)
        (function () {
            const datalist = document.getElementById('country-options');
            if (!datalist) return;

            const FALLBACK = ['United Arab Emirates','Saudi Arabia','Qatar','Kuwait','Bahrain','Oman','India','Pakistan','Bangladesh','United Kingdom','United States','Canada','Australia','Germany','France','Italy','Spain','Egypt','Jordan','Lebanon','Turkey','Philippines','Indonesia','Malaysia','Singapore','Sri Lanka','Nepal','Nigeria','Kenya','South Africa','Brazil','Mexico','Argentina','Russia','China','Japan','South Korea'];

            function populate(list) {
                datalist.innerHTML = '';
                list.forEach(name => {
                    const opt = document.createElement('option');
                    opt.value = name;
                    datalist.appendChild(opt);
                });
            }

            fetch('https://restcountries.com/v3.1/all?fields=name')
                .then(r => r.ok ? r.json() : Promise.reject(r.status))
                .then(data => {
                    const names = data
                        .map(c => c && c.name && c.name.common)
                        .filter(Boolean)
                        .sort((a, b) => a.localeCompare(b));
                    populate(names);
                })
                .catch(() => populate(FALLBACK.slice().sort((a, b) => a.localeCompare(b))));
        })();
    </script>
</body>
</html>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/referral/auth/register.blade.php ENDPATH**/ ?>