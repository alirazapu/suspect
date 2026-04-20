/*
 * Suspect Admin JS
 * JavaScript for the Suspect admin dashboard — profile tabs (read + edit).
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
    // Globals
    // ----------------------------------------------------------------
    var tabLoaded = {};   // cache: don't reload a tab already fetched
    var BASE   = window.SUSPECT_BASE_URL || '/';
    var PID    = ($('body').data('person-id') || '').toString();

    // ----------------------------------------------------------------
    // Minimal HTML-escape helper
    // ----------------------------------------------------------------
    function escHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // ----------------------------------------------------------------
    // Build a key-value definition table
    // ----------------------------------------------------------------
    function kvTable(obj, labels) {
        if ( ! obj) return '<p class="text-muted">No data.</p>';
        var html = '<table class="table table-sm table-bordered"><tbody>';
        $.each(labels, function (key, label) {
            var val = (obj[key] !== undefined && obj[key] !== null && obj[key] !== '')
                ? escHtml(obj[key])
                : '<em class="text-muted">—</em>';
            html += '<tr><th class="bg-light" style="width:35%">' + label + '</th><td>' + val + '</td></tr>';
        });
        html += '</tbody></table>';
        return html;
    }

    // ----------------------------------------------------------------
    // Build a data table with optional Edit/Delete column
    // ----------------------------------------------------------------
    function dataTable(rows, cols, opts) {
        opts = opts || {};
        if ( ! rows || ! rows.length) return '<p class="text-muted py-3">No records found.</p>';
        var html = '<div class="table-responsive"><table class="table table-sm table-striped table-bordered"><thead><tr>';
        $.each(cols, function (_, col) { html += '<th>' + escHtml(col.label) + '</th>'; });
        if (opts.actions) { html += '<th style="width:90px">Actions</th>'; }
        html += '</tr></thead><tbody>';
        $.each(rows, function (_, row) {
            html += '<tr>';
            $.each(cols, function (_, col) {
                var val = (row[col.key] !== undefined && row[col.key] !== null && row[col.key] !== '')
                    ? escHtml(col.render ? col.render(row) : row[col.key]) : '—';
                html += '<td>' + val + '</td>';
            });
            if (opts.actions) {
                var rowJson = escHtml(JSON.stringify(row));
                html += '<td>';
                if (opts.actions.edit) {
                    html += '<button class="btn btn-xs btn-outline-primary mr-1 btn-edit-row" '
                        + 'data-row="' + rowJson + '" data-tab="' + opts.tab + '">'
                        + '<i class="fas fa-edit"></i></button>';
                }
                if (opts.actions.del) {
                    html += '<button class="btn btn-xs btn-outline-danger btn-del-row" '
                        + 'data-id="' + escHtml(row.id) + '" data-tab="' + opts.tab + '">'
                        + '<i class="fas fa-trash"></i></button>';
                }
                html += '</td>';
            }
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }

    // ----------------------------------------------------------------
    // Generic AJAX tab loader with add-record button
    // ----------------------------------------------------------------
    function loadTab(tabId, endpoint, render, opts) {
        var $pane = $(tabId);
        if ( ! $pane.length) return;
        if (tabLoaded[tabId]) return;
        tabLoaded[tabId] = true;

        $pane.html('<div class="tab-loading"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading…</p></div>');

        $.ajax({
            url: endpoint,
            method: 'GET',
            dataType: 'json',
            success: function (resp) {
                if (resp && resp.status === 'ok') {
                    var addBtn = '';
                    if (opts && opts.addBtn) {
                        addBtn = '<div class="mb-2">' + opts.addBtn + '</div>';
                    }
                    $pane.html(addBtn + render(resp.data));
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

    // ----------------------------------------------------------------
    // Force-reload a tab (invalidate cache then re-trigger)
    // ----------------------------------------------------------------
    function reloadTab(tabId) {
        delete tabLoaded[tabId];
        var $link = $('a[href="' + tabId + '"]');
        if ($link.hasClass('active')) {
            $link.trigger('shown.bs.tab');
        } else {
            $link.tab('show');
        }
    }

    // ----------------------------------------------------------------
    // Save helper: POST JSON → server, show toast, optionally reload tab
    // ----------------------------------------------------------------
    function saveRecord(url, data, tabId, successMsg) {
        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function (resp) {
                if (resp && resp.status === 'ok') {
                    showToast('success', successMsg || resp.message || 'Saved.');
                    delete tabLoaded[tabId];
                    reloadTab(tabId);
                } else {
                    showToast('danger', (resp && resp.message) ? resp.message : 'Save failed.');
                }
            },
            error: function () {
                showToast('danger', 'Network error. Please try again.');
            }
        });
    }

    // ----------------------------------------------------------------
    // Delete helper
    // ----------------------------------------------------------------
    function deleteRecord(url, data, tabId, successMsg) {
        if ( ! confirm('Delete this record?')) return;
        saveRecord(url, data, tabId, successMsg || 'Deleted.');
    }

    // ----------------------------------------------------------------
    // Simple toast notification
    // ----------------------------------------------------------------
    function showToast(type, message) {
        var $toast = $('<div class="alert alert-' + type + ' alert-dismissible fade show" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:280px;">'
            + escHtml(message)
            + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        $('body').append($toast);
        setTimeout(function () { $toast.alert('close'); }, 4000);
    }

    // ----------------------------------------------------------------
    // Modal builder for edit forms
    // ----------------------------------------------------------------
    function showModal(title, bodyHtml, saveCallback) {
        $('#suspectModal').remove();
        var modal = '<div class="modal fade" id="suspectModal" tabindex="-1" role="dialog">'
            + '<div class="modal-dialog modal-lg" role="document"><div class="modal-content">'
            + '<div class="modal-header"><h5 class="modal-title">' + escHtml(title) + '</h5>'
            + '<button type="button" class="close" data-dismiss="modal">&times;</button></div>'
            + '<div class="modal-body">' + bodyHtml + '</div>'
            + '<div class="modal-footer">'
            + '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>'
            + '<button type="button" class="btn btn-primary" id="suspectModalSave">Save</button>'
            + '</div></div></div></div>';
        $('body').append(modal);
        $('#suspectModal').modal('show');
        $('#suspectModalSave').on('click', function () {
            saveCallback();
        });
    }

    // ----------------------------------------------------------------
    // Tab event listeners
    // ----------------------------------------------------------------
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function () {
        var target = $(this).attr('href');
        if ( ! PID) return;

        switch (target) {
            // ----------------------------------------------------------
            case '#tab-basic':
                loadTab(target, BASE + 'api/persons/' + PID + '/basic', function (d) {
                    return kvTable(d, {
                        name:            'Full Name',
                        cnic:            'CNIC',
                        father_name:     "Father's Name",
                        dob:             'Date of Birth',
                        gender_label:    'Gender',
                        nationality_label: 'Nationality',
                        religion_label:  'Religion',
                        sect_label:      'Sect',
                        caste_label:     'Caste',
                        marital_status_label: 'Marital Status',
                        place_of_birth:  'Place of Birth',
                        alias:           'Alias',
                        address:         'Permanent Address',
                        district:        'District',
                        region:          'Region',
                        category:        'Category',
                        language_read_write: 'Languages (R/W)',
                        language_speak:  'Languages (Spoken)',
                        physical_appearance: 'Physical Appearance'
                    });
                });
                break;

            // ----------------------------------------------------------
            case '#tab-detailed':
                loadTab(target, BASE + 'api/persons/' + PID + '/detailed', function (d) {
                    return kvTable(d, {
                        marital_status:  'Marital Status',
                        dob:             'Date of Birth',
                        place_of_birth:  'Place of Birth',
                        alias:           'Alias',
                        religion_label:  'Religion',
                        sect_label:      'Sect',
                        caste_label:     'Caste',
                        nationality_label: 'Nationality',
                        mother_tongue:   'Mother Tongue',
                        language_read_write: 'Languages (R/W)',
                        language_speak:  'Languages (Spoken)',
                        language_accent: 'Language Accent',
                        physical_appearance: 'Physical Appearance',
                        is_sensitive_department: 'Sensitive Dept.'
                    });
                });
                break;

            // ----------------------------------------------------------
            case '#tab-identities':
                loadTab(target, BASE + 'api/persons/' + PID + '/identities', function (d) {
                    return dataTable(d, [
                        {key: 'identity_type',   label: 'Type'},
                        {key: 'identity_number', label: 'Number'}
                    ], {
                        tab: target,
                        actions: {edit: true, del: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Identity</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-education':
                loadTab(target, BASE + 'api/persons/' + PID + '/education', function (d) {
                    return dataTable(d, [
                        {key: 'degree',        label: 'Degree'},
                        {key: 'institution',   label: 'Institution'},
                        {key: 'passing_year',  label: 'Year'},
                        {key: 'education_level_label', label: 'Level'}
                    ], {
                        tab: target,
                        actions: {edit: true, del: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Education</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-income':
                loadTab(target, BASE + 'api/persons/' + PID + '/income', function (d) {
                    return dataTable(d, [
                        {key: 'source_type',    label: 'Source'},
                        {key: 'description',    label: 'Description'},
                        {key: 'monthly_amount', label: 'Amount (PKR)'},
                        {key: 'duration_label', label: 'Duration'}
                    ], {
                        tab: target,
                        actions: {edit: true, del: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Income Source</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-banks':
                loadTab(target, BASE + 'api/persons/' + PID + '/banks', function (d) {
                    return dataTable(d, [
                        {key: 'bank_name',      label: 'Bank'},
                        {key: 'branch',         label: 'Branch'},
                        {key: 'account_number', label: 'Account No.'},
                        {key: 'atm_number',     label: 'ATM No.'},
                        {key: 'is_internet_banking', label: 'Internet Banking',
                            render: function (r) { return r.is_internet_banking ? 'Yes' : 'No'; }}
                    ], {
                        tab: target,
                        actions: {edit: true, del: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Bank</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-assets':
                loadTab(target, BASE + 'api/persons/' + PID + '/assets', function (d) {
                    return dataTable(d, [
                        {key: 'asset_type',     label: 'Asset Name'},
                        {key: 'description',    label: 'Description'},
                        {key: 'moveable_label', label: 'Type'},
                        {key: 'value',          label: 'Value (PKR)'},
                        {key: 'since_year',     label: 'Since'},
                        {key: 'asset_acquired_how', label: 'Acquired How'}
                    ], {
                        tab: target,
                        actions: {edit: true, del: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Asset</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-mobiles':
                loadTab(target, BASE + 'api/persons/' + PID + '/mobiles', function (d) {
                    return dataTable(d, [
                        {key: 'mobile_number', label: 'Number'},
                        {key: 'operator',      label: 'Operator'},
                        {key: 'sim_owner',     label: 'SIM Owner'},
                        {key: 'status_label',  label: 'Status'},
                        {key: 'connection_type_label', label: 'Connection'},
                        {key: 'sim_activated_at',  label: 'Activated'},
                        {key: 'sim_last_used_at',  label: 'Last Used'}
                    ], {
                        tab: target,
                        actions: {edit: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Mobile</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-relations':
                loadTab(target, BASE + 'api/persons/' + PID + '/relations', function (d) {
                    return dataTable(d, [
                        {key: 'relation_type', label: 'Relation'},
                        {key: 'name',          label: 'Name'},
                        {key: 'father_name',   label: "Father's Name"},
                        {key: 'cnic',          label: 'CNIC'},
                        {key: 'under_custodian', label: 'Under Custody',
                            render: function (r) { return r.under_custodian ? 'Yes' : 'No'; }}
                    ], {
                        tab: target,
                        actions: {edit: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Relation</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-criminal':
                loadTab(target, BASE + 'api/persons/' + PID + '/criminal', function (d) {
                    return dataTable(d, [
                        {key: 'fir_number',     label: 'FIR No.'},
                        {key: 'police_station', label: 'Police Station'},
                        {key: 'district',       label: 'District'},
                        {key: 'case_date',      label: 'FIR Date'},
                        {key: 'section',        label: 'Section(s)'},
                        {key: 'status',         label: 'Status'}
                    ], {
                        tab: target,
                        actions: {edit: true, del: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Criminal Record</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-affiliations':
                loadTab(target, BASE + 'api/persons/' + PID + '/affiliations', function (d) {
                    return dataTable(d, [
                        {key: 'organization_id',   label: 'Organization ID'},
                        {key: 'ideological_stance',label: 'Stance'},
                        {key: 'designation',       label: 'Designation'},
                        {key: 'is_trained',        label: 'Trained',
                            render: function (r) { return r.is_trained ? 'Yes' : 'No'; }},
                        {key: 'remarks',           label: 'Details'}
                    ], {
                        tab: target,
                        actions: {edit: true, del: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Affiliation</button>'
                });
                break;

            // ----------------------------------------------------------
            case '#tab-projects':
                loadTab(target, BASE + 'api/persons/' + PID + '/projects', function (d) {
                    return dataTable(d, [
                        {key: 'project_name',  label: 'Project'},
                        {key: 'status',        label: 'Status'},
                        {key: 'request_time',  label: 'Linked On'}
                    ]);
                });
                break;

            // ----------------------------------------------------------
            case '#tab-category':
                loadTab(target, BASE + 'api/persons/' + PID + '/category_history', function (d) {
                    return dataTable(d, [
                        {key: 'old_category', label: 'Old Category'},
                        {key: 'new_category', label: 'New Category'},
                        {key: 'changed_by',   label: 'Changed By'},
                        {key: 'changed_at',   label: 'Date'},
                        {key: 'reason',       label: 'Reason'}
                    ]);
                });
                break;

            // ----------------------------------------------------------
            case '#tab-reports':
                loadTab(target, BASE + 'api/persons/' + PID + '/reports', function (d) {
                    return dataTable(d, [
                        {key: 'report_type_label', label: 'Report Type'},
                        {key: 'report_reference_no', label: 'Ref No.'},
                        {key: 'report_date',       label: 'Date'},
                        {key: 'summary',           label: 'Details'}
                    ], {
                        tab: target,
                        actions: {edit: true, del: true}
                    });
                }, {
                    addBtn: '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Report</button>'
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

    // ================================================================
    // Edit / Delete row handlers (event delegation on document)
    // ================================================================

    // Delete button
    $(document).on('click', '.btn-del-row', function () {
        var $btn = $(this);
        var tab  = $btn.data('tab');
        var id   = $btn.data('id');
        var urlMap = {
            '#tab-identities': 'delete_identity',
            '#tab-education':  'delete_education',
            '#tab-income':     'deletesource',
            '#tab-banks':      'delete_bank',
            '#tab-assets':     'delete_asset',
            '#tab-criminal':   'delete_criminalrecord',
            '#tab-affiliations': 'delete_affiliations',
            '#tab-reports':    'deletereport'
        };
        var action = urlMap[tab];
        if ( ! action) return;
        deleteRecord(BASE + 'personprofile/' + action, {pid: PID, id: id}, tab);
    });

    // Edit button — open a modal with fields pre-filled
    $(document).on('click', '.btn-edit-row', function () {
        var $btn = $(this);
        var tab  = $btn.data('tab');
        var row  = JSON.parse($btn.attr('data-row') || '{}');
        _openEditModal(tab, row);
    });

    // Add button — open a blank modal
    $(document).on('click', '.btn-add-row', function () {
        var $btn = $(this);
        var tab  = $btn.data('tab');
        _openEditModal(tab, {});
    });

    function _openEditModal(tab, row) {
        var config = _tabEditConfig(tab);
        if ( ! config) { showToast('warning', 'Edit not supported for this tab yet.'); return; }

        var body = '<form id="editRowForm">';
        body += '<input type="hidden" name="pid" value="' + escHtml(PID) + '">';
        if (row.id) {
            body += '<input type="hidden" name="id" value="' + escHtml(row.id) + '">';
        }
        $.each(config.fields, function (_, f) {
            var val = escHtml(row[f.name] || '');
            body += '<div class="form-group row"><label class="col-sm-4 col-form-label">' + escHtml(f.label) + '</label><div class="col-sm-8">';
            if (f.type === 'textarea') {
                body += '<textarea class="form-control form-control-sm" name="' + escHtml(f.name) + '" rows="3">' + val + '</textarea>';
            } else if (f.type === 'select' && f.options) {
                body += '<select class="form-control form-control-sm" name="' + escHtml(f.name) + '">';
                body += '<option value="">— Select —</option>';
                $.each(f.options, function (_, opt) {
                    var sel = String(row[f.name]) === String(opt.value) ? ' selected' : '';
                    body += '<option value="' + escHtml(opt.value) + '"' + sel + '>' + escHtml(opt.label) + '</option>';
                });
                body += '</select>';
            } else {
                body += '<input type="' + (f.type || 'text') + '" class="form-control form-control-sm" name="' + escHtml(f.name) + '" value="' + val + '">';
            }
            body += '</div></div>';
        });
        body += '</form>';

        var title = (row.id ? 'Edit ' : 'Add ') + config.label;
        showModal(title, body, function () {
            var data = $('#editRowForm').serializeArray().reduce(function (obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            saveRecord(BASE + 'personprofile/' + config.saveAction, data, tab);
            $('#suspectModal').modal('hide');
        });
    }

    function _tabEditConfig(tab) {
        var configs = {
            '#tab-identities': {
                label: 'Identity',
                saveAction: 'update_identity',
                fields: [
                    {name: 'identity_id', label: 'Type', type: 'select', options: [
                        {value: 1, label: 'Armed Licence'}, {value: 2, label: 'Driving Licence'},
                        {value: 3, label: 'NTN'}, {value: 4, label: 'CNIC'},
                        {value: 5, label: 'Afghan Refugees Card'}, {value: 6, label: 'Passport'}
                    ]},
                    {name: 'identity_no', label: 'Number'}
                ]
            },
            '#tab-education': {
                label: 'Education',
                saveAction: 'update_education',
                fields: [
                    {name: 'edu_type', label: 'Type', type: 'select', options: [
                        {value: 0, label: 'Religious'}, {value: 1, label: 'Non-Religious'}
                    ]},
                    {name: 'degree_name',    label: 'Degree'},
                    {name: 'institute_name', label: 'Institution'},
                    {name: 'complete_year',  label: 'Year', type: 'number'},
                    {name: 'education_level', label: 'Level', type: 'select', options: [
                        {value: 1, label: 'Primary'}, {value: 2, label: 'Middle'},
                        {value: 3, label: 'Matric'}, {value: 4, label: 'Intermediate'},
                        {value: 5, label: 'Bachelor'}, {value: 6, label: 'Master'},
                        {value: 7, label: 'M.Phil'}, {value: 8, label: 'PhD'}
                    ]}
                ]
            },
            '#tab-income': {
                label: 'Income Source',
                saveAction: 'update_personincomesource',
                fields: [
                    {name: 'income_source_name', label: 'Source Name'},
                    {name: 'details',            label: 'Description', type: 'textarea'},
                    {name: 'income_amount',      label: 'Amount (PKR)', type: 'number'},
                    {name: 'income_source_duration', label: 'Duration', type: 'select', options: [
                        {value: 1, label: 'Daily'}, {value: 2, label: 'Monthly'}, {value: 3, label: 'Yearly'}
                    ]}
                ]
            },
            '#tab-banks': {
                label: 'Bank Account',
                saveAction: 'update_banks',
                fields: [
                    {name: 'account_number',     label: 'Account No.'},
                    {name: 'atm_number',         label: 'ATM No.'},
                    {name: 'branch_name',        label: 'Branch'},
                    {name: 'is_internet_banking', label: 'Internet Banking', type: 'select', options: [
                        {value: 0, label: 'No'}, {value: 1, label: 'Yes'}
                    ]}
                ]
            },
            '#tab-assets': {
                label: 'Asset',
                saveAction: 'update_personassets',
                fields: [
                    {name: 'asset_name',          label: 'Asset Name'},
                    {name: 'details',             label: 'Description', type: 'textarea'},
                    {name: 'moveable_immovable',  label: 'Type', type: 'select', options: [
                        {value: 0, label: 'Unknown'}, {value: 1, label: 'Moveable'}, {value: 2, label: 'Immovable'}
                    ]},
                    {name: 'asset_value',         label: 'Value (PKR)', type: 'number'},
                    {name: 'since_year',          label: 'Since (Year)', type: 'number'},
                    {name: 'asset_acquired_how',  label: 'Acquired How'}
                ]
            },
            '#tab-mobiles': {
                label: 'Mobile Number',
                saveAction: 'update_mobiles',
                fields: [
                    {name: 'phone_number',    label: 'Phone Number'},
                    {name: 'sim_owner',       label: 'SIM Owner'},
                    {name: 'status',          label: 'Status', type: 'select', options: [
                        {value: 0, label: 'Inactive'}, {value: 1, label: 'Active'}
                    ]},
                    {name: 'connection_type', label: 'Connection', type: 'select', options: [
                        {value: 0, label: 'Post-Paid'}, {value: 1, label: 'Pre-Paid'}
                    ]},
                    {name: 'sim_activated_at', label: 'Activated At'},
                    {name: 'sim_last_used_at', label: 'Last Used At'}
                ]
            },
            '#tab-relations': {
                label: 'Relation',
                saveAction: 'update_relations',
                fields: [
                    {name: 'relation_with',       label: 'Related Person ID', type: 'number'},
                    {name: 'person_relation_type', label: 'Relation Type', type: 'select', options: [
                        {value: 1, label: 'Father'}, {value: 2, label: 'Mother'},
                        {value: 3, label: 'Brother'}, {value: 4, label: 'Sister'},
                        {value: 5, label: 'Son'}, {value: 6, label: 'Daughter'},
                        {value: 7, label: 'Wife'}, {value: 8, label: 'Husband'},
                        {value: 9, label: 'Uncle'}, {value: 10, label: 'Aunt'},
                        {value: 11, label: 'Nephew'}, {value: 12, label: 'Niece'},
                        {value: 13, label: 'Grandfather'}, {value: 14, label: 'Grandmother'},
                        {value: 15, label: 'Friend'}, {value: 16, label: 'Colleague'},
                        {value: 17, label: 'Other'}
                    ]},
                    {name: 'under_custodian', label: 'Under Custody', type: 'select', options: [
                        {value: 0, label: 'No'}, {value: 1, label: 'Yes'}
                    ]}
                ]
            },
            '#tab-criminal': {
                label: 'Criminal Record',
                saveAction: 'update_criminalr',
                fields: [
                    {name: 'fir_number',        label: 'FIR Number'},
                    {name: 'fir_date',          label: 'FIR Date', type: 'date'},
                    {name: 'police_station_id', label: 'Police Station ID', type: 'number'},
                    {name: 'sections_applied',  label: 'Sections Applied'},
                    {name: 'case_position', label: 'Case Status', type: 'select', options: [
                        {value: 1, label: 'Under Investigation'}, {value: 2, label: 'Under Trial'},
                        {value: 3, label: 'Convicted'}, {value: 4, label: 'Discharged'}
                    ]},
                    {name: 'accused_position',  label: 'Accused Position', type: 'number'}
                ]
            },
            '#tab-affiliations': {
                label: 'Affiliation',
                saveAction: 'update_affiliations',
                fields: [
                    {name: 'organization_id',          label: 'Organization ID', type: 'number'},
                    {name: 'ideological_stance',       label: 'Ideological Stance'},
                    {name: 'designation',              label: 'Designation'},
                    {name: 'is_trained',               label: 'Trained', type: 'select', options: [
                        {value: 0, label: 'No'}, {value: 1, label: 'Yes'}
                    ]},
                    {name: 'details',                  label: 'Details', type: 'textarea'},
                    {name: 'self_recruitment_details', label: 'Self-Recruitment Details', type: 'textarea'}
                ]
            },
            '#tab-reports': {
                label: 'Report',
                saveAction: 'update_personreports',
                fields: [
                    {name: 'report_type', label: 'Report Type', type: 'select', options: [
                        {value: 1, label: 'Interrogation Report'},
                        {value: 2, label: 'Investigation Report'},
                        {value: 3, label: 'Special Report'},
                        {value: 4, label: 'Intelligence Report'},
                        {value: 5, label: 'Ground Check Report'},
                        {value: 6, label: 'FIR Copy'},
                        {value: 7, label: 'Recommendations/Remarks'},
                        {value: 8, label: 'Other'}
                    ]},
                    {name: 'report_reference_no', label: 'Reference No.'},
                    {name: 'report_date',         label: 'Date', type: 'date'},
                    {name: 'report_details',      label: 'Details', type: 'textarea'}
                ]
            }
        };
        return configs[tab] || null;
    }

    // ================================================================
    // Basic info / Detailed info inline save (forms already in the view)
    // ================================================================

    $(document).on('submit', '#basicinfoForm', function (e) {
        e.preventDefault();
        var data = $(this).serializeArray().reduce(function (obj, item) {
            obj[item.name] = item.value; return obj;
        }, {});
        saveRecord(BASE + 'personprofile/update_basic_info', data, '#tab-basic', 'Basic info updated.');
    });

    $(document).on('submit', '#detailInfoForm', function (e) {
        e.preventDefault();
        var data = $(this).serializeArray().reduce(function (obj, item) {
            obj[item.name] = item.value; return obj;
        }, {});
        saveRecord(BASE + 'personprofile/update_detail_info', data, '#tab-detailed', 'Detail info updated.');
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
