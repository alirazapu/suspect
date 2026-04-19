<?php
/**
 * Person Profile View
 *
 * Mirrors the dramslive person_profile page.
 * Tab data is loaded on-demand via AJAX (see assets/js/admin.js).
 *
 * Tabs:
 *   1  Basic Info
 *   2  Detailed Info
 *   3  Identities
 *   4  Education
 *   5  Income Sources
 *   6  Banks Details
 *   7  Asset Details
 *   8  Mobiles
 *   9  Relations
 *   10 Criminal Record
 *   11 Affiliations/Trainings
 *   12 Link with Projects
 *   13 Category Change History
 *   14 Person Reports
 */
?>

<!-- Pass person_id to JavaScript for AJAX calls -->
<script>document.body.setAttribute('data-person-id', '<?php echo (int) $person_id; ?>');</script>

<!-- ================================================================
     Profile Header Card
     ================================================================ -->
<div class="profile-header">
    <div class="d-flex align-items-start">
        <div class="mr-3">
            <div style="width:72px;height:72px;background:rgba(255,255,255,.2);border-radius:50%;
                        display:flex;align-items:center;justify-content:center;font-size:2rem;">
                <i class="fas fa-user"></i>
            </div>
        </div>
        <div class="flex-grow-1">
            <h2><?php echo htmlspecialchars($person->name ?? 'Unknown Person', ENT_QUOTES, 'UTF-8'); ?></h2>
            <p>
                <?php if ( ! empty($person->father_name)): ?>
                    <i class="fas fa-male mr-1"></i>
                    <?php echo htmlspecialchars($person->father_name, ENT_QUOTES, 'UTF-8'); ?> &nbsp;|&nbsp;
                <?php endif; ?>
                <?php if ( ! empty($person->cnic)): ?>
                    <i class="fas fa-id-card mr-1"></i>
                    CNIC: <?php echo htmlspecialchars($person->cnic, ENT_QUOTES, 'UTF-8'); ?> &nbsp;|&nbsp;
                <?php endif; ?>
                <?php if ( ! empty($person->category)): ?>
                    <i class="fas fa-tag mr-1"></i>
                    <?php echo htmlspecialchars($person->category, ENT_QUOTES, 'UTF-8'); ?> &nbsp;|&nbsp;
                <?php endif; ?>
                <?php if ( ! empty($person->province)): ?>
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    <?php echo htmlspecialchars($person->province, ENT_QUOTES, 'UTF-8'); ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="text-right">
            <a href="<?php echo base_url('persons'); ?>" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left mr-1"></i>Back to List
            </a>
        </div>
    </div>
</div>

<!-- ================================================================
     Tab Navigation
     ================================================================ -->
<div class="card">
    <div class="card-body p-0">
        <ul class="nav nav-tabs px-3 pt-3" id="profileTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-basic-link" data-toggle="tab" href="#tab-basic" role="tab">
                    <i class="fas fa-info-circle mr-1"></i>Basic Info
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-detailed-link" data-toggle="tab" href="#tab-detailed" role="tab">
                    <i class="fas fa-list-alt mr-1"></i>Detailed Info
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-identities-link" data-toggle="tab" href="#tab-identities" role="tab">
                    <i class="fas fa-id-badge mr-1"></i>Identities
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-education-link" data-toggle="tab" href="#tab-education" role="tab">
                    <i class="fas fa-graduation-cap mr-1"></i>Education
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-income-link" data-toggle="tab" href="#tab-income" role="tab">
                    <i class="fas fa-money-bill-wave mr-1"></i>Income Sources
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-banks-link" data-toggle="tab" href="#tab-banks" role="tab">
                    <i class="fas fa-university mr-1"></i>Banks Details
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-assets-link" data-toggle="tab" href="#tab-assets" role="tab">
                    <i class="fas fa-home mr-1"></i>Asset Details
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-mobiles-link" data-toggle="tab" href="#tab-mobiles" role="tab">
                    <i class="fas fa-mobile-alt mr-1"></i>Mobiles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-relations-link" data-toggle="tab" href="#tab-relations" role="tab">
                    <i class="fas fa-sitemap mr-1"></i>Relations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-criminal-link" data-toggle="tab" href="#tab-criminal" role="tab">
                    <i class="fas fa-gavel mr-1"></i>Criminal Record
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-affiliations-link" data-toggle="tab" href="#tab-affiliations" role="tab">
                    <i class="fas fa-network-wired mr-1"></i>Affiliations/Trainings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-projects-link" data-toggle="tab" href="#tab-projects" role="tab">
                    <i class="fas fa-project-diagram mr-1"></i>Link with Projects
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-category-link" data-toggle="tab" href="#tab-category" role="tab">
                    <i class="fas fa-history mr-1"></i>Category Change History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-reports-link" data-toggle="tab" href="#tab-reports" role="tab">
                    <i class="fas fa-file-alt mr-1"></i>Person Reports
                </a>
            </li>
        </ul>

        <!-- ============================================================
             Tab Panes
             ============================================================ -->
        <div class="tab-content px-3 pb-3" id="profileTabContent">

            <!-- 1. Basic Info -->
            <div class="tab-pane fade show active" id="tab-basic" role="tabpanel">
                <div class="tab-loading">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading…</p>
                </div>
            </div>

            <!-- 2. Detailed Info -->
            <div class="tab-pane fade" id="tab-detailed" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 3. Identities -->
            <div class="tab-pane fade" id="tab-identities" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 4. Education -->
            <div class="tab-pane fade" id="tab-education" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 5. Income Sources -->
            <div class="tab-pane fade" id="tab-income" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 6. Banks Details -->
            <div class="tab-pane fade" id="tab-banks" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 7. Asset Details -->
            <div class="tab-pane fade" id="tab-assets" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 8. Mobiles -->
            <div class="tab-pane fade" id="tab-mobiles" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 9. Relations -->
            <div class="tab-pane fade" id="tab-relations" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 10. Criminal Record -->
            <div class="tab-pane fade" id="tab-criminal" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 11. Affiliations/Trainings -->
            <div class="tab-pane fade" id="tab-affiliations" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 12. Link with Projects -->
            <div class="tab-pane fade" id="tab-projects" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 13. Category Change History -->
            <div class="tab-pane fade" id="tab-category" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

            <!-- 14. Person Reports -->
            <div class="tab-pane fade" id="tab-reports" role="tabpanel">
                <div class="tab-loading text-muted"><i class="fas fa-clock"></i> Click the tab to load.</div>
            </div>

        </div><!-- /.tab-content -->
    </div><!-- /.card-body -->
</div><!-- /.card -->

<script>
// Make base URL available to admin.js AJAX calls
window.SUSPECT_BASE_URL = '<?php echo base_url(); ?>';
</script>
