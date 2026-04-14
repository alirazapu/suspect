<style>
  html, body { height: 100%; margin: 0; padding: 0; }
  .ctd-login-wrap {
    display: flex;
    flex-direction: row;
    min-height: 100vh;
    width: 100%;
  }
  .ctd-left {
    flex: 0 0 58%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 3rem 3.5rem;
    background: linear-gradient(150deg, #0d1b35 0%, #1a3260 55%, #1e4a8a 100%);
    color: #fff;
  }
  .ctd-right {
    flex: 0 0 42%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: #f4f6fb;
    padding: 3rem 2rem;
  }
  @media (max-width: 768px) {
    .ctd-login-wrap { flex-direction: column; }
    .ctd-left { flex: none; padding: 2.5rem 1.5rem; }
    .ctd-right { flex: none; }
  }
</style>

<div class="ctd-login-wrap">

  <!-- ===== LEFT PANEL — branding ===== -->
  <div class="ctd-left">

    <div style="text-align:center; margin-bottom:1.5rem;">
      <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.12);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
        <i class="fas fa-shield-alt" style="font-size:2.4rem;color:#f0c040;"></i>
      </div>
      <h1 style="font-size:1.9rem;font-weight:700;letter-spacing:.5px;margin:0;">Suspect Portal</h1>
      <p style="font-size:.85rem;opacity:.7;margin:.4rem 0 0;letter-spacing:1px;text-transform:uppercase;">Counter-Terrorism Department · KPK</p>
    </div>

    <p style="font-size:1rem;opacity:.85;max-width:400px;text-align:center;line-height:1.8;margin-bottom:2rem;">
      Criminal Tracking &amp; Intelligence System — a centralised platform for suspect profiling, financial intelligence, and investigative case management.
    </p>

    <ul style="list-style:none;padding:0;margin:0 0 2.5rem;max-width:380px;font-size:.94rem;opacity:.9;width:100%;">
      <li style="padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.1);">
        <i class="fas fa-user-secret" style="color:#f0c040;width:22px;margin-right:.6rem;"></i>Comprehensive suspect &amp; person profiles
      </li>
      <li style="padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.1);">
        <i class="fas fa-money-bill-wave" style="color:#f0c040;width:22px;margin-right:.6rem;"></i>Financial, asset &amp; bank intelligence
      </li>
      <li style="padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.1);">
        <i class="fas fa-sitemap" style="color:#f0c040;width:22px;margin-right:.6rem;"></i>Criminal history &amp; affiliation tracking
      </li>
      <li style="padding:.55rem 0;">
        <i class="fas fa-lock" style="color:#f0c040;width:22px;margin-right:.6rem;"></i>Secure, role-based access control
      </li>
    </ul>

    <p style="font-size:.75rem;opacity:.4;margin:0;">&copy; <?php echo date('Y'); ?> Counter-Terrorism Department, KPK &mdash; Confidential</p>
  </div>

  <!-- ===== RIGHT PANEL — login form ===== -->
  <div class="ctd-right">
    <div style="width:100%;max-width:370px;">

      <div style="text-align:center;margin-bottom:2rem;">
        <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#1a3260,#1e4a8a);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;box-shadow:0 4px 16px rgba(30,74,138,.3);">
          <i class="fas fa-shield-alt" style="font-size:1.6rem;color:#fff;"></i>
        </div>
        <h4 style="font-weight:700;color:#1a2a4a;margin:0;">Sign In</h4>
        <p style="color:#888;font-size:.875rem;margin:.35rem 0 0;">CTD Suspect Portal &mdash; Authorised Access Only</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible" style="border-radius:8px;font-size:.875rem;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
          <i class="fas fa-exclamation-triangle mr-1"></i><?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <?php echo form_open(site_url('auth/login'), ['style' => 'margin:0;']); ?>

        <div class="form-group" style="margin-bottom:1.2rem;">
          <label for="login_username" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#555;display:block;margin-bottom:.4rem;">Username</label>
          <div class="input-group" style="border:1.5px solid #dde2ee;border-radius:8px;overflow:hidden;background:#fff;">
            <div class="input-group-prepend" style="border:none;">
              <span class="input-group-text" style="background:#fff;border:none;color:#aaa;padding-right:.5rem;"><i class="fas fa-user"></i></span>
            </div>
            <input type="text" id="login_username" name="username" class="form-control"
                   placeholder="Enter your username" required autofocus autocomplete="username"
                   style="border:none;box-shadow:none;font-size:.95rem;padding-left:0;">
          </div>
        </div>

        <div class="form-group" style="margin-bottom:1.5rem;">
          <label for="login_password" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#555;display:block;margin-bottom:.4rem;">Password</label>
          <div class="input-group" style="border:1.5px solid #dde2ee;border-radius:8px;overflow:hidden;background:#fff;">
            <div class="input-group-prepend" style="border:none;">
              <span class="input-group-text" style="background:#fff;border:none;color:#aaa;padding-right:.5rem;"><i class="fas fa-lock"></i></span>
            </div>
            <input type="password" id="login_password" name="password" class="form-control"
                   placeholder="Enter your password" required autocomplete="current-password"
                   style="border:none;box-shadow:none;font-size:.95rem;padding-left:0;">
          </div>
        </div>

        <input type="hidden" name="return" value="<?php echo htmlspecialchars(service('request')->getGet('return') ?? ''); ?>">

        <button type="submit" class="btn btn-block text-white"
                style="background:linear-gradient(135deg,#1a3260,#1e4a8a);border:none;border-radius:8px;padding:.8rem;font-size:1rem;font-weight:600;letter-spacing:.4px;box-shadow:0 4px 14px rgba(30,74,138,.35);">
          <i class="fas fa-sign-in-alt mr-2"></i>Sign In
        </button>

      <?php echo form_close(); ?>

      <div style="text-align:center;margin-top:1.75rem;">
        <small style="color:#aaa;font-size:.78rem;">
          <i class="fas fa-lock mr-1"></i>Restricted access &mdash; authorised personnel only
        </small>
      </div>

    </div>
  </div>

</div>

