<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — Suspect' : 'Suspect — Person Intelligence Portal'; ?></title>

    <!-- Vendor CSS — self-hosted to avoid CDN blocking on restricted networks -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/fontawesome/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/select2/css/select2.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/select2-bootstrap4/select2-bootstrap4.min.css'); ?>">
    <!-- Suspect admin theme -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/admin.css'); ?>">
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
