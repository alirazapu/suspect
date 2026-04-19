/*
 * Suspect Admin JS
 * Minimal JavaScript for the Suspect admin dashboard.
 */
(function ($) {
    'use strict';

    // ----------------------------------------------------------------
    // Auto-dismiss flash messages after 4 s
    // ----------------------------------------------------------------
    setTimeout(function () {
        $('.alert-dismissible').fadeOut(400);
    }, 4000);

    // ----------------------------------------------------------------
    // Person Profile tab loading via AJAX
    // Each tab calls the /api/ endpoint for its data.
    // ----------------------------------------------------------------
    var tabLoaded = {};   // cache: don't reload a tab already fetched

    /**
     * Generic tab loader.
     * @param {string} tabId     — the '#tab-xxx' pane to populate
     * @param {string} endpoint  — relative URL to fetch JSON from
     * @param {function} render  — function(data) → HTML string
     */
    function loadTab(tabId, endpoint, render) {
        var $pane = $(tabId);
        if (!$pane.length) return;
        if (tabLoaded[tabId]) return;   // already fetched
        tabLoaded[tabId] = true;

        $pane.html('<div class="tab-loading"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading…</p></div>');

        $.ajax({
            url: endpoint,
            method: 'GET',
            dataType: 'json',
            success: function (resp) {
                if (resp && resp.status === 'ok') {
                    $pane.html(render(resp.data));
                } else {
                    var msg = (resp && resp.message) ? resp.message : 'No data available.';
                    $pane.html('<p class="text-muted p-3">' + escHtml(msg) + '</p>');
                }
            },
            error: function () {
                $pane.html('<p class="text-danger p-3"><i class="fas fa-exclamation-circle mr-1"></i>Failed to load data. Please try again.</p>');
            }
        });
    }

    /** Minimal HTML-escape helper */
    function escHtml(s) {
        if (!s) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /** Build a simple key-value definition list from an object */
    function kvTable(obj, labels) {
        if (!obj) return '<p class="text-muted">No data.</p>';
        var html = '<table class="table table-sm table-bordered"><tbody>';
        $.each(labels, function (key, label) {
            var val = (obj[key] !== undefined && obj[key] !== null) ? escHtml(obj[key]) : '<em class="text-muted">—</em>';
            html += '<tr><th class="bg-light" style="width:35%">' + label + '</th><td>' + val + '</td></tr>';
        });
        html += '</tbody></table>';
        return html;
    }

    /** Build a table from an array of objects with given columns */
    function dataTable(rows, cols) {
        if (!rows || !rows.length) return '<p class="text-muted">No records found.</p>';
        var html = '<div class="table-responsive"><table class="table table-sm table-striped table-bordered"><thead><tr>';
        $.each(cols, function (_, col) { html += '<th>' + escHtml(col.label) + '</th>'; });
        html += '</tr></thead><tbody>';
        $.each(rows, function (_, row) {
            html += '<tr>';
            $.each(cols, function (_, col) {
                var val = (row[col.key] !== undefined && row[col.key] !== null) ? escHtml(row[col.key]) : '—';
                html += '<td>' + val + '</td>';
            });
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }

    // ----------------------------------------------------------------
    // Profile tab event listeners
    // ----------------------------------------------------------------
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function () {
        var target = $(this).attr('href');
        var pid    = $('body').data('person-id') || '';
        if (!pid) return;

        var base = window.SUSPECT_BASE_URL || '/';

        switch (target) {
            case '#tab-basic':
                loadTab(target, base + 'api/persons/' + pid + '/basic', function (d) {
                    return kvTable(d, {
                        name: 'Full Name', father_name: "Father's Name", mother_name: "Mother's Name",
                        dob: 'Date of Birth', gender: 'Gender', nationality: 'Nationality',
                        domicile: 'Domicile', religion: 'Religion', caste: 'Caste',
                        address: 'Address', city: 'City', province: 'Province'
                    });
                });
                break;

            case '#tab-detailed':
                loadTab(target, base + 'api/persons/' + pid + '/detailed', function (d) {
                    return kvTable(d, {
                        marital_status: 'Marital Status', spouse_name: 'Spouse Name',
                        children_count: 'No. of Children', occupation: 'Occupation',
                        designation: 'Designation', organization: 'Organization',
                        education_level: 'Education Level', email: 'Email',
                        website: 'Website', remarks: 'Remarks'
                    });
                });
                break;

            case '#tab-identities':
                loadTab(target, base + 'api/persons/' + pid + '/identities', function (d) {
                    return dataTable(d, [
                        {key:'identity_type', label:'Type'},
                        {key:'identity_number', label:'Number'},
                        {key:'issue_date', label:'Issue Date'},
                        {key:'expiry_date', label:'Expiry Date'},
                        {key:'issue_place', label:'Issue Place'},
                        {key:'status', label:'Status'}
                    ]);
                });
                break;

            case '#tab-education':
                loadTab(target, base + 'api/persons/' + pid + '/education', function (d) {
                    return dataTable(d, [
                        {key:'degree', label:'Degree'}, {key:'institution', label:'Institution'},
                        {key:'board_university', label:'Board / University'},
                        {key:'passing_year', label:'Year'}, {key:'grade', label:'Grade/Marks'}
                    ]);
                });
                break;

            case '#tab-income':
                loadTab(target, base + 'api/persons/' + pid + '/income', function (d) {
                    return dataTable(d, [
                        {key:'source_type', label:'Source'}, {key:'description', label:'Description'},
                        {key:'monthly_amount', label:'Monthly Income (PKR)'},
                        {key:'annual_amount', label:'Annual Income (PKR)'}, {key:'remarks', label:'Remarks'}
                    ]);
                });
                break;

            case '#tab-banks':
                loadTab(target, base + 'api/persons/' + pid + '/banks', function (d) {
                    return dataTable(d, [
                        {key:'bank_name', label:'Bank'}, {key:'branch', label:'Branch'},
                        {key:'account_number', label:'Account No.'},
                        {key:'account_type', label:'Account Type'}, {key:'iban', label:'IBAN'},
                        {key:'balance', label:'Balance (PKR)'}
                    ]);
                });
                break;

            case '#tab-assets':
                loadTab(target, base + 'api/persons/' + pid + '/assets', function (d) {
                    return dataTable(d, [
                        {key:'asset_type', label:'Type'}, {key:'description', label:'Description'},
                        {key:'location', label:'Location'}, {key:'value', label:'Est. Value (PKR)'},
                        {key:'registered_name', label:'Registered Name'}
                    ]);
                });
                break;

            case '#tab-mobiles':
                loadTab(target, base + 'api/persons/' + pid + '/mobiles', function (d) {
                    return dataTable(d, [
                        {key:'mobile_number', label:'Number'}, {key:'operator', label:'Operator'},
                        {key:'sim_owner', label:'SIM Owner'}, {key:'status', label:'Status'},
                        {key:'remarks', label:'Remarks'}
                    ]);
                });
                break;

            case '#tab-relations':
                loadTab(target, base + 'api/persons/' + pid + '/relations', function (d) {
                    return dataTable(d, [
                        {key:'relation_type', label:'Relation'}, {key:'name', label:'Name'},
                        {key:'father_name', label:"Father's Name"},
                        {key:'cnic', label:'CNIC'}, {key:'contact', label:'Contact'},
                        {key:'remarks', label:'Remarks'}
                    ]);
                });
                break;

            case '#tab-criminal':
                loadTab(target, base + 'api/persons/' + pid + '/criminal', function (d) {
                    return dataTable(d, [
                        {key:'fir_number', label:'FIR No.'}, {key:'police_station', label:'Police Station'},
                        {key:'district', label:'District'}, {key:'case_date', label:'Case Date'},
                        {key:'offence', label:'Offence'}, {key:'section', label:'Section'},
                        {key:'status', label:'Status'}
                    ]);
                });
                break;

            case '#tab-affiliations':
                loadTab(target, base + 'api/persons/' + pid + '/affiliations', function (d) {
                    return dataTable(d, [
                        {key:'affiliation_type', label:'Type'}, {key:'name', label:'Name / Organization'},
                        {key:'role', label:'Role'}, {key:'from_date', label:'From'},
                        {key:'to_date', label:'To'}, {key:'remarks', label:'Remarks'}
                    ]);
                });
                break;

            case '#tab-projects':
                loadTab(target, base + 'api/persons/' + pid + '/projects', function (d) {
                    return dataTable(d, [
                        {key:'project_name', label:'Project'}, {key:'project_type', label:'Type'},
                        {key:'role', label:'Role'}, {key:'start_date', label:'Start'},
                        {key:'end_date', label:'End'}, {key:'status', label:'Status'}
                    ]);
                });
                break;

            case '#tab-category':
                loadTab(target, base + 'api/persons/' + pid + '/category_history', function (d) {
                    return dataTable(d, [
                        {key:'old_category', label:'Old Category'}, {key:'new_category', label:'New Category'},
                        {key:'changed_by', label:'Changed By'}, {key:'changed_at', label:'Date'},
                        {key:'reason', label:'Reason'}
                    ]);
                });
                break;

            case '#tab-reports':
                loadTab(target, base + 'api/persons/' + pid + '/reports', function (d) {
                    return dataTable(d, [
                        {key:'report_type', label:'Report Type'}, {key:'report_date', label:'Date'},
                        {key:'reported_by', label:'Reported By'}, {key:'summary', label:'Summary'},
                        {key:'status', label:'Status'}
                    ]);
                });
                break;
        }
    });

    // ----------------------------------------------------------------
    // Trigger load for the initially active tab on page load
    // ----------------------------------------------------------------
    $(window).on('load', function () {
        var $activeTab = $('.nav-tabs .nav-link.active');
        if ($activeTab.length) {
            $activeTab.trigger('shown.bs.tab');
        }
    });

    // ----------------------------------------------------------------
    // Person listing: advanced filter toggle
    // ----------------------------------------------------------------
    $('#toggleAdvancedFilters').on('click', function () {
        $('#advancedFilters').slideToggle(200);
        var $icon = $(this).find('i');
        $icon.toggleClass('fa-chevron-down fa-chevron-up');
    });

    // ----------------------------------------------------------------
    // Person listing: live search (debounce 400 ms)
    // ----------------------------------------------------------------
    var searchTimer;
    $('#quick-search').on('keyup', function () {
        clearTimeout(searchTimer);
        var val = $(this).val();
        searchTimer = setTimeout(function () {
            var $form = $('#filterForm');
            $form.find('[name="q"]').val(val);
            $form.submit();
        }, 400);
    });

}(jQuery));
