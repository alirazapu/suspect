<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Dashboard / Welcome page for the Suspect admin theme -->

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</h1>
    <small class="text-muted">Welcome, <?php echo htmlspecialchars(
        $this->session->userdata('name') ?: $this->session->userdata('username') ?: 'User',
        ENT_QUOTES, 'UTF-8'
    ); ?></small>
</div>

<!-- Quick-nav cards -->
<div class="row mb-4">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left-color:#3498db;">
            <div class="stat-value"><i class="fas fa-users" style="font-size:1.5rem;"></i></div>
            <div class="stat-label">Persons Database</div>
            <a href="<?php echo base_url('persons'); ?>" class="btn btn-sm btn-primary mt-2">
                <i class="fas fa-list mr-1"></i>Browse Persons
            </a>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left-color:#27ae60;">
            <div class="stat-value"><i class="fas fa-user-circle" style="font-size:1.5rem;"></i></div>
            <div class="stat-label">Person Profiles</div>
            <a href="<?php echo base_url('persons'); ?>" class="btn btn-sm btn-success mt-2">
                <i class="fas fa-search mr-1"></i>Search &amp; View
            </a>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left-color:#e74c3c;">
            <div class="stat-value"><i class="fas fa-sign-out-alt" style="font-size:1.5rem;"></i></div>
            <div class="stat-label">Session</div>
            <a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-sm btn-danger mt-2">
                <i class="fas fa-sign-out-alt mr-1"></i>Logout
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-info-circle mr-1"></i> Quick Reference
    </div>
    <div class="card-body">
        <p class="mb-2">Use the <strong>Persons</strong> link in the sidebar to browse and search the person
        intelligence database with advanced filters.</p>
        <p class="mb-2">To view a person's full profile — including Basic Info, Identities, Education,
        Bank Details, Criminal Records, and all other tabs — click any name in the persons list or
        navigate directly to:</p>
        <code>/personprofile/person_profile?id=&lt;encrypted_person_id&gt;</code>
        <p class="mt-2 mb-0 text-muted small">
            Access tokens from <strong>ctd.drams.com</strong> are automatically validated.
            Append <code>?accesstoken=&lt;token&gt;</code> to any URL to authenticate.
        </p>
    </div>
</div>
