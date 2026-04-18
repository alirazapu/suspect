<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; Suspect</title>
    <style>
        /* Minimal, clean login page — replace with your theme's CSS if desired */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a2e;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,.35);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
        }
        .card h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.6rem;
            color: #1a1a2e;
            letter-spacing: 1px;
        }
        .alert {
            background: #ffe0e0;
            color: #c0392b;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: .75rem 1rem;
            margin-bottom: 1rem;
            font-size: .9rem;
        }
        .form-group { margin-bottom: 1rem; }
        label {
            display: block;
            margin-bottom: .3rem;
            font-size: .85rem;
            font-weight: 600;
            color: #444;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: .6rem .8rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color .2s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4a90e2;
        }
        .btn {
            width: 100%;
            padding: .75rem;
            background: #4a90e2;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: .5rem;
            transition: background .2s;
        }
        .btn:hover { background: #357abd; }
        .footer-note {
            text-align: center;
            margin-top: 1.2rem;
            font-size: .8rem;
            color: #888;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>&#128274; Suspect</h1>

    <?php if ( ! empty($error)): ?>
        <div class="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php echo form_open('auth/login'); ?>
        <div class="form-group">
            <label for="login">Username or Email</label>
            <input type="text" id="login" name="login"
                   value="<?php echo set_value('login'); ?>"
                   autocomplete="username" autofocus required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn">Sign In</button>
    <?php echo form_close(); ?>

    <p class="footer-note">Access is restricted to authorised users only.</p>
</div>
</body>
</html>
