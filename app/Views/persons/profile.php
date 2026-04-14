<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">
            <?php echo htmlspecialchars($person->first_name . ' ' . $person->last_name); ?>
            <small class="text-muted"><?php echo htmlspecialchars($person->cnic ?? ''); ?></small>
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo base_url('persons'); ?>">Persons</a></li>
            <li class="breadcrumb-item active">Profile</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <!-- Info row -->
      <div class="row mb-3">
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-user"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Gender</span>
              <span class="info-box-number"><?php echo htmlspecialchars($person->gender ?? 'N/A'); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-birthday-cake"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Date of Birth</span>
              <span class="info-box-number"><?php echo htmlspecialchars($person->dob ?? 'N/A'); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-map-marker-alt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">District</span>
              <span class="info-box-number"><?php echo htmlspecialchars($person->district ?? 'N/A'); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-tag"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Category</span>
              <span class="info-box-number"><?php echo htmlspecialchars($person->category_id ?? 'N/A'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs card -->
      <div class="card card-primary card-outline">
        <div class="card-header p-0 border-bottom-0">
          <ul class="nav nav-tabs" id="person-tabs" role="tablist">
            <?php
            $tabs = [
                'basicinfo'             => ['Basic Info',              'fas fa-id-card'],
                'detailinfo'            => ['Detailed Info',           'fas fa-info-circle'],
                'identities'            => ['Identities',              'fas fa-fingerprint'],
                'education'             => ['Education',               'fas fa-graduation-cap'],
                'sourceofincome'        => ['Income Sources',          'fas fa-money-bill-wave'],
                'banksdetails'          => ['Banks Details',           'fas fa-university'],
                'assetsdetails'         => ['Asset Details',           'fas fa-home'],
                'mobiles'               => ['Mobiles',                 'fas fa-mobile-alt'],
                'relations'             => ['Relations',               'fas fa-people-arrows'],
                'criminalrecord'        => ['Criminal Record',         'fas fa-gavel'],
                'affiliations'          => ['Affiliations/Trainings',  'fas fa-handshake'],
                'linkedprojects'        => ['Link with Projects',      'fas fa-project-diagram'],
                'categorychangehistory' => ['Category Change History', 'fas fa-history'],
                'reports'               => ['Person Reports',          'fas fa-file-alt'],
            ];
            foreach ($tabs as $key => [$label, $icon]):
                $isActive = ($active_tab === $key);
            ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $isActive ? 'active' : ''; ?>"
                 id="tab-<?php echo $key; ?>"
                 data-toggle="tab"
                 href="#<?php echo $key; ?>"
                 role="tab"
                 data-pid="<?php echo $pid; ?>"
                 data-loaded="false">
                <i class="<?php echo $icon; ?> mr-1"></i><?php echo $label; ?>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content" id="person-tabs-content">
            <?php foreach ($tabs as $key => [$label, $icon]):
                $isActive = ($active_tab === $key); ?>
            <div class="tab-pane fade <?php echo $isActive ? 'show active' : ''; ?>" id="<?php echo $key; ?>" role="tabpanel">
              <div id="<?php echo $key; ?>-content">
                <?php if ($isActive): ?>
                  <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>
                <?php else: ?>
                  <div class="text-center py-4 text-muted"><i class="fas fa-clock mr-1"></i>Click tab to load</div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<script>
var PERSON_PID = <?php echo (int)$pid; ?>;
var API_BASE   = '<?php echo base_url('api/persons/'); ?>';

var TAB_ENDPOINTS = {
    basicinfo:              'basic',
    detailinfo:             'detailed',
    identities:             'identities',
    education:              'education',
    sourceofincome:         'income',
    banksdetails:           'banks',
    assetsdetails:          'assets',
    mobiles:                'mobiles',
    relations:              'relations',
    criminalrecord:         'criminal',
    affiliations:           'affiliations',
    linkedprojects:         'projects',
    categorychangehistory:  'category_history',
    reports:                'reports'
};

function loadTab(tabKey) {
    var endpoint = TAB_ENDPOINTS[tabKey];
    if (!endpoint) return;
    var $content = $('#' + tabKey + '-content');
    $content.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
    $.get(API_BASE + PERSON_PID + '/' + endpoint)
        .done(function(res) {
            if (res.status === 'success') {
                $content.html(renderData(tabKey, res.data));
            } else {
                $content.html('<div class="alert alert-warning">No data available.</div>');
            }
        })
        .fail(function() {
            $content.html('<div class="alert alert-danger">Failed to load data. Please try again.</div>');
        });
}

function renderData(tab, data) {
    if (!data || (Array.isArray(data) && data.length === 0) || (typeof data === 'object' && Object.keys(data).length === 0)) {
        return '<div class="alert alert-info mt-2"><i class="fas fa-info-circle mr-1"></i>No data found for this section.</div>';
    }
    if (tab === 'basicinfo' && !Array.isArray(data)) {
        return renderObjectTable(data);
    }
    if (Array.isArray(data)) {
        return renderArrayTable(data);
    }
    return renderObjectTable(data);
}

function renderObjectTable(obj) {
    var html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
    for (var k in obj) {
        if (!obj.hasOwnProperty(k)) continue;
        var val = obj[k] !== null && obj[k] !== undefined ? obj[k] : '—';
        html += '<tr><th class="bg-light" style="width:30%">' + escapeHtml(k.replace(/_/g,' ')) + '</th><td>' + escapeHtml(String(val)) + '</td></tr>';
    }
    html += '</table></div>';
    return html;
}

function renderArrayTable(arr) {
    if (!arr.length) return '<div class="alert alert-info mt-2">No records.</div>';
    var keys = Object.keys(arr[0]);
    var html = '<div class="table-responsive"><table class="table table-bordered table-hover table-sm"><thead class="bg-primary text-white"><tr>';
    keys.forEach(function(k){ html += '<th>' + escapeHtml(k.replace(/_/g,' ')) + '</th>'; });
    html += '</tr></thead><tbody>';
    arr.forEach(function(row){
        html += '<tr>';
        keys.forEach(function(k){ html += '<td>' + escapeHtml(String(row[k] !== null && row[k] !== undefined ? row[k] : '—')) + '</td>'; });
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

function escapeHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Load active tab immediately
$(function(){
    var activeTab = '<?php echo $active_tab; ?>';
    loadTab(activeTab);

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        var tabKey = $(e.target).attr('href').substring(1);
        loadTab(tabKey);
    });
});
</script>
