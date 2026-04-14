<div class="login-box">
  <div class="login-logo"><b>Suspect</b> Portal</div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      <?php echo form_open('auth/login'); ?>
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <input type="hidden" name="return" value="<?php echo htmlspecialchars(service('request')->getGet('return') ?? ''); ?>">
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
        </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
