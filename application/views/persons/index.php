<div class="page-header">
    <h1><i class="fas fa-users mr-2"></i>Persons</h1>
    <span class="badge badge-secondary badge-sm">
        <?php echo number_format($total); ?> record<?php echo ($total != 1) ? 's' : ''; ?> found
    </span>
</div>

<!-- ================================================================
     Filter Card
     ================================================================ -->
<div class="card filter-card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fas fa-filter mr-1"></i> Search &amp; Filters</span>
        <button type="button" id="toggleAdvancedFilters" class="btn btn-sm btn-outline-secondary">
            Advanced <i class="fas fa-chevron-down ml-1"></i>
        </button>
    </div>
    <div class="card-body">
        <?php echo form_open('persons', array('method' => 'get', 'id' => 'filterForm')); ?>

        <!-- Advanced filters (hidden by default) -->
        <div id="advancedFilters" style="display:none;">
            <hr class="mt-1 mb-2">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Gender</label>
                    <select name="gender" class="custom-select custom-select-sm">
                        <option value="">All Genders</option>
                        <option value="1" <?php echo ($filters['gender'] === '1') ? 'selected' : ''; ?>>Male</option>
                        <option value="2" <?php echo ($filters['gender'] === '2') ? 'selected' : ''; ?>>Female</option>
                        <option value="3" <?php echo ($filters['gender'] === '3') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Province</label>
                    <select name="province" class="custom-select custom-select-sm">
                        <option value="">All Provinces</option>
                        <?php foreach ($provinces as $prov): ?>
                            <option value="<?php echo htmlspecialchars($prov, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo ($filters['province'] === $prov) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prov, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">District</label>
                    <input type="text" name="district" class="form-control form-control-sm"
                           placeholder="District…"
                           value="<?php echo htmlspecialchars($filters['district'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Category</label>
                    <select name="category" class="custom-select custom-select-sm">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int) $cat['id']; ?>"
                                <?php echo ($filters['category'] !== '' && $filters['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">CNIC</label>
                    <input type="text" name="cnic" class="form-control form-control-sm"
                           placeholder="CNIC number…"
                           value="<?php echo htmlspecialchars($filters['cnic'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Mobile No.</label>
                    <input type="text" name="mobile" class="form-control form-control-sm"
                           placeholder="Mobile number…"
                           value="<?php echo htmlspecialchars($filters['mobile'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Affiliation</label>
                    <input type="text" name="affiliation" class="form-control form-control-sm"
                           placeholder="Organisation / group…"
                           value="<?php echo htmlspecialchars($filters['affiliation'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Date Range</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="from_date" class="form-control"
                               value="<?php echo htmlspecialchars($filters['from_date'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="input-group-prepend input-group-append">
                            <span class="input-group-text">–</span>
                        </div>
                        <input type="date" name="to_date" class="form-control"
                               value="<?php echo htmlspecialchars($filters['to_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter mr-1"></i>Apply Filters
                    </button>
                    <a href="<?php echo base_url('persons'); ?>" class="btn btn-secondary btn-sm ml-1">
                        <i class="fas fa-times mr-1"></i>Reset All
                    </a>
                </div>
            </div>
        </div><!-- /#advancedFilters -->

        <?php echo form_close(); ?>
    </div>
</div>

<!-- ================================================================
     Results Table
     ================================================================ -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($persons)): ?>
            <div class="p-4 text-center text-muted">
                <i class="fas fa-search fa-2x mb-2 d-block"></i>
                No persons found matching your criteria.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Father's Name</th>
                            <th>CNIC</th>
                            <th>Gender</th>
                            <th>Province</th>
                            <th>District</th>
                            <th>Category</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_num = ($page - 1) * $limit + 1;
                        foreach ($persons as $p):
                            // Encrypt person ID for the profile URL
                            $enc_id = $this->Person_model->encrypt_person_id($p->person_id);
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo $row_num++; ?></td>
                            <td>
                                <a href="<?php echo base_url('personprofile/person_profile?id=' . urlencode($enc_id)); ?>"
                                   class="font-weight-bold text-primary">
                                    <?php echo htmlspecialchars($p->name ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($p->father_name ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($p->cnic ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($p->gender ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($p->province ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($p->district ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if ( ! empty($p->category)): ?>
                                    <span class="badge badge-info badge-sm">
                                        <?php echo htmlspecialchars($p->category, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo base_url('personprofile/person_profile?id=' . urlencode($enc_id)); ?>"
                                   class="btn btn-xs btn-outline-primary"
                                   title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div><!-- /.card-body -->

    <?php if ($total_pages > 1): ?>
    <div class="card-footer">
        <?php
        // Build pagination preserving filter query params
        $pager_filters = $filters;
        unset($pager_filters['page']);
        $qs_base = http_build_query(array_filter($pager_filters, function($v){ return $v !== '' && $v !== null; }));
        ?>
        <nav>
            <ul class="pagination pagination-sm justify-content-center mb-0">

                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link"
                       href="<?php echo base_url('persons?' . $qs_base . ($qs_base ? '&' : '') . 'page=' . ($page - 1)); ?>">
                        &laquo; Prev
                    </a>
                </li>

                <?php
                $window = 2;
                for ($i = max(1, $page - $window); $i <= min($total_pages, $page + $window); $i++):
                ?>
                    <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                        <a class="page-link"
                           href="<?php echo base_url('persons?' . $qs_base . ($qs_base ? '&' : '') . 'page=' . $i); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link"
                       href="<?php echo base_url('persons?' . $qs_base . ($qs_base ? '&' : '') . 'page=' . ($page + 1)); ?>">
                        Next &raquo;
                    </a>
                </li>

            </ul>
        </nav>
        <p class="text-center text-muted small mt-1 mb-0">
            Showing page <?php echo $page; ?> of <?php echo $total_pages; ?>
            (<?php echo number_format($total); ?> total records)
        </p>
    </div>
    <?php endif; ?>
</div>
