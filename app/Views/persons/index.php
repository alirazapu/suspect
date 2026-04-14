<!-- Content Wrapper -->
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Persons</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active">Persons</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <!-- Search Filters -->
      <div class="card card-primary card-outline collapsed-card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-search mr-2"></i>Search &amp; Filters</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
          </div>
        </div>
        <div class="card-body">
          <form action="<?php echo base_url('persons'); ?>" method="get" id="filter-form">
          <div class="row">
            <div class="col-md-3 form-group">
              <label>Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($filters['name'] ?? ''); ?>" placeholder="First / Last / Father name">
            </div>
            <div class="col-md-2 form-group">
              <label>CNIC</label>
              <input type="text" name="cnic" class="form-control" value="<?php echo htmlspecialchars($filters['cnic'] ?? ''); ?>" placeholder="e.g. 35201-1234567-1">
            </div>
            <div class="col-md-2 form-group">
              <label>Mobile</label>
              <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($filters['mobile'] ?? ''); ?>" placeholder="03xx-xxxxxxx">
            </div>
            <div class="col-md-2 form-group">
              <label>Category</label>
              <select name="category" class="form-control select2">
                <option value="">— Any —</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat->id; ?>" <?php if (($filters['category'] ?? '') == $cat->id) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cat->name); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 form-group">
              <label>District</label>
              <select name="district" class="form-control select2">
                <option value="">— Any —</option>
                <?php foreach ($districts as $d): ?>
                  <option value="<?php echo htmlspecialchars($d->district); ?>" <?php if (($filters['district'] ?? '') == $d->district) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($d->district); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-1 form-group d-flex align-items-end">
              <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i></button>
            </div>
          </div>
          </form>
        </div>
      </div>

      <!-- Results Card -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            Results <span class="badge badge-primary ml-2"><?php echo number_format($total); ?></span>
          </h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover table-striped table-sm">
            <thead class="bg-primary">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Father Name</th>
                <th>CNIC</th>
                <th>DOB</th>
                <th>District</th>
                <th>Category</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($persons)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No records found.</td></tr>
              <?php else: ?>
                <?php $i = ($page - 1) * $limit + 1; foreach ($persons as $p): ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo htmlspecialchars($p->first_name . ' ' . $p->last_name); ?></td>
                  <td><?php echo htmlspecialchars($p->father_name ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($p->cnic ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($p->dob ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($p->district ?? ''); ?></td>
                  <td><span class="badge badge-info"><?php echo htmlspecialchars($p->category_name ?? ''); ?></span></td>
                  <td>
                    <a href="<?php echo base_url('persons/profile?id=' . urlencode(pid_encrypt($p->id))); ?>"
                       class="btn btn-xs btn-primary" title="View Profile">
                      <i class="fas fa-user-circle"></i> Profile
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if ($total > $limit): ?>
        <div class="card-footer clearfix">
          <?php
            $total_pages = ceil($total / $limit);
            $qs = array_filter($filters);
            $qs_str = http_build_query($qs);
          ?>
          <ul class="pagination pagination-sm m-0 float-right">
            <?php for ($pg = 1; $pg <= min($total_pages, 20); $pg++): ?>
              <li class="page-item <?php echo $pg === $page ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo base_url('persons?' . ($qs_str ? $qs_str . '&' : '') . 'page=' . $pg); ?>"><?php echo $pg; ?></a>
              </li>
            <?php endfor; ?>
            <?php if ($total_pages > 20): ?>
              <li class="page-item disabled"><span class="page-link">…<?php echo $total_pages; ?></span></li>
            <?php endif; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </section>
</div>
<script>
$(function(){ $('.select2').select2({theme:'bootstrap4'}); });
</script>
