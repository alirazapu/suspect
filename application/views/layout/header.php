<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — Suspect' : 'Suspect — Person Intelligence Portal'; ?></title>

    <!--
        Vendor assets — for production, download and place locally:
          assets/vendor/bootstrap/css/bootstrap.min.css
          assets/vendor/fontawesome/css/all.min.css
          assets/vendor/jquery/jquery.min.js
          assets/vendor/bootstrap/js/bootstrap.bundle.min.js
        Until then, CDN fallbacks are used via the admin.css @import directives.
    -->

    <!-- Suspect admin theme (includes Bootstrap 4 + FontAwesome 5 via CDN @import fallback) -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/admin.css'); ?>">
    <!-- Select2 — searchable dropdowns -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body class="admin-body">
<div class="wrapper">

    <!-- ============================================================== -->
    <!-- Top Navigation Bar                                              -->
    <!-- ============================================================== -->
    <nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
        <a class="navbar-brand" href="<?php echo base_url(); ?>">
            <i class="fas fa-user-secret mr-2"></i>Suspect
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTop">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTop">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link text-light">
                        <i class="fas fa-user-circle mr-1"></i>
                        <?php echo htmlspecialchars($this->session->userdata('name') ?: $this->session->userdata('username') ?: 'User', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('auth/logout'); ?>">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="admin-container">

        <!-- ============================================================== -->
        <!-- Sidebar                                                         -->
        <!-- ============================================================== -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <i class="fas fa-shield-alt"></i> SUSPECT
            </div>
            <ul class="sidebar-menu">
                <li class="<?php echo ($this->uri->segment(1) === '' || $this->uri->segment(1) === 'welcome') ? 'active' : ''; ?>">
                    <a href="<?php echo base_url(); ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="<?php echo ($this->uri->segment(1) === 'persons') ? 'active' : ''; ?>">
                    <a href="<?php echo base_url('persons'); ?>">
                        <i class="fas fa-users"></i> Persons
                    </a>
                </li>
                <li class="sidebar-divider"></li>
                <li>
                    <a href="<?php echo base_url('auth/logout'); ?>">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- ============================================================== -->
        <!-- Main Content Area                                               -->
        <!-- ============================================================== -->
        <main class="admin-content">
