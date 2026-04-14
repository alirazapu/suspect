<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($title) ? htmlspecialchars($title) . ' | Suspect' : 'Suspect'; ?></title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap4-theme/1.0.0/select2-bootstrap4.min.css">
  <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body class="<?php echo isset($body_class) ? htmlspecialchars($body_class) : 'hold-transition sidebar-mini layout-fixed'; ?>"
      style="<?php echo (isset($body_class) && strpos($body_class, 'login-page') !== false) ? 'margin:0;padding:0;overflow-x:hidden;' : ''; ?>">
<?php if (!isset($body_class) || strpos($body_class, 'login-page') === false): ?>
<div class="wrapper">
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
    <li class="nav-item d-none d-sm-inline-block"><a href="<?php echo base_url('persons'); ?>" class="nav-link">Home</a></li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <span class="nav-link"><i class="fas fa-user-circle mr-1"></i><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?></span>
    </li>
    <li class="nav-item">
      <a href="<?php echo base_url('auth/logout'); ?>" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </li>
  </ul>
</nav>
<?php endif; ?>
