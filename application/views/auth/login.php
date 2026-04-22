<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; Suspect</title>
    <!-- Vendor CSS — self-hosted to avoid CDN blocking on restricted networks -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/fontawesome/css/all.min.css'); ?>">
    <!-- Suspect admin theme -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/admin.css'); ?>">
</head>
<body class="login-body">

<!-- ============================================================
     Left branding panel
     ============================================================ -->
<div class="login-brand">
    <div class="brand-icon"><i class="fas fa-user-secret"></i></div>
    <h1>Suspect</h1>
    <div class="divider"></div>
    <p>Person Intelligence Portal<br>Authorised access only</p>
</div>

<!-- ============================================================
     Right form panel
     ============================================================ -->
<div class="login-form-panel">
    <div class="login-card">
        <p class="login-card-title">
            <i class="fas fa-lock"></i>Sign In
        </p>

        <?php if ( ! empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php echo form_open('auth/login'); ?>
            <div class="form-group">
                <label for="login">Username or Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" id="login" name="login"
                           class="form-control"
                           value="<?php echo set_value('login'); ?>"
                           placeholder="Enter your username or email"
                           autocomplete="username" autofocus required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                    </div>
                    <input type="password" id="password" name="password"
                           class="form-control"
                           placeholder="Enter your password"
                           autocomplete="current-password" required>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt mr-2"></i>Sign In
            </button>
        <?php echo form_close(); ?>

        <p class="login-footer">Access is restricted to authorised users only.</p>
    </div>
</div>

</body>
</html>
