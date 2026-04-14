<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?php echo base_url('persons'); ?>" class="brand-link">
    <span class="brand-text font-weight-light"><strong>Suspect</strong></span>
  </a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image"><i class="fas fa-user fa-2x ml-1 text-white"></i></div>
      <div class="info"><a href="#" class="d-block"><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'User'; ?></a></div>
    </div>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
          <a href="<?php echo base_url('persons'); ?>" class="nav-link <?php echo (isset($active) && $active === 'persons') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Persons</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
