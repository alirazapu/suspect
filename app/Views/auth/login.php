<div class="login-page d-flex align-items-stretch" style="min-height:100vh;">

  <!-- LEFT PANEL — branding & project message -->
  <div class="col-lg-7 d-none d-lg-flex flex-column justify-content-center align-items-center text-white px-5"
       style="background: linear-gradient(135deg, #1a2a4a 0%, #1e3c6e 50%, #2a5298 100%);">
    <div class="text-center mb-4">
      <i class="fas fa-shield-alt" style="font-size:4rem; color:#f0c040;"></i>
    </div>
    <h1 class="font-weight-bold mb-2" style="font-size:2.4rem; letter-spacing:1px;">
      Suspect Portal
    </h1>
    <p class="mb-4" style="font-size:1.1rem; opacity:.85; max-width:420px; text-align:center; line-height:1.7;">
      Counter-Terrorism Department — Criminal Tracking &amp; Intelligence System.<br>
      Centralised database for suspect profiling, financial intelligence, and investigative case management.
    </p>
    <ul class="list-unstyled text-left" style="max-width:380px; font-size:.97rem; opacity:.9;">
      <li class="mb-2"><i class="fas fa-check-circle mr-2" style="color:#f0c040;"></i>Comprehensive suspect &amp; person profiles</li>
      <li class="mb-2"><i class="fas fa-check-circle mr-2" style="color:#f0c040;"></i>Financial, asset &amp; bank intelligence</li>
      <li class="mb-2"><i class="fas fa-check-circle mr-2" style="color:#f0c040;"></i>Criminal history &amp; affiliation tracking</li>
      <li class="mb-2"><i class="fas fa-check-circle mr-2" style="color:#f0c040;"></i>Secure, role-based access control</li>
    </ul>
    <div class="mt-5" style="opacity:.5; font-size:.8rem;">
      &copy; <?php echo date('Y'); ?> Counter-Terrorism Department, KPK &mdash; Confidential
    </div>
  </div>

  <!-- RIGHT PANEL — login form -->
  <div class="col-12 col-lg-5 d-flex flex-column justify-content-center align-items-center bg-white px-4 py-5">
    <div style="width:100%; max-width:380px;">

      <!-- Logo / title -->
      <div class="text-center mb-4">
        <i class="fas fa-shield-alt" style="font-size:2.5rem; color:#1e3c6e;"></i>
        <h4 class="mt-2 font-weight-bold" style="color:#1a2a4a;">CTD &mdash; Suspect Portal</h4>
        <p class="text-muted" style="font-size:.875rem;">Sign in with your authorised credentials</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible mb-3">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <i class="fas fa-exclamation-triangle mr-1"></i>
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <?php echo form_open('auth/login'); ?>

        <div class="form-group">
          <label for="username" class="font-weight-bold small text-uppercase" style="color:#555; letter-spacing:.5px;">Username</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-light border-right-0"><i class="fas fa-user text-muted"></i></span>
            </div>
            <input type="text" id="username" name="username" class="form-control border-left-0"
                   placeholder="Enter your username" required autofocus autocomplete="username"
                   style="box-shadow:none;">
          </div>
        </div>

        <div class="form-group mt-3">
          <label for="password" class="font-weight-bold small text-uppercase" style="color:#555; letter-spacing:.5px;">Password</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
            </div>
            <input type="password" id="password" name="password" class="form-control border-left-0"
                   placeholder="Enter your password" required autocomplete="current-password"
                   style="box-shadow:none;">
          </div>
        </div>

        <input type="hidden" name="return" value="<?php echo htmlspecialchars(service('request')->getGet('return') ?? ''); ?>">

        <button type="submit" class="btn btn-block mt-4 text-white font-weight-bold"
                style="background:linear-gradient(135deg,#1e3c6e,#2a5298); letter-spacing:.5px; padding:.75rem; font-size:1rem;">
          <i class="fas fa-sign-in-alt mr-2"></i> Sign In
        </button>

      <?php echo form_close(); ?>

      <div class="text-center mt-4">
        <small class="text-muted">
          <i class="fas fa-lock mr-1"></i>Restricted access &mdash; authorised personnel only
        </small>
      </div>
    </div>
  </div>

</div>

