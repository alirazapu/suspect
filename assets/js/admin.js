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
    var LOOKUPS = null;   // cached lookup data from api/lookups
    var basicInfoData  = null;   // cached basic info API response (for Edit button)
    var detailInfoData = null;   // cached detail info API response (for Edit button)

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
    // Read-only views for Basic Info and Detailed Info tabs
    // (shown by default; Edit button switches to editable form)
    // ----------------------------------------------------------------
    function renderBasicReadOnly(d) {
        if ( ! d) return '<p class="text-muted p-3">No data available.</p>';
        var html = '<div class="d-flex justify-content-end mb-2">'
            + '<button type="button" class="btn btn-sm btn-outline-primary" id="btnEditBasic">'
            + '<i class="fas fa-edit mr-1"></i>Edit</button></div>';
        html += kvTable(d, {
            name:            'Full Name',
            cnic:            'CNIC',
            father_name:     "Father's Name",
            dob:             'Date of Birth',
            gender_label:    'Gender',
            address:         'Permanent Address',
            district:        'District',
            region:          'Region',
            category:        'Category',
            nationality_label: 'Nationality',
            religion_label:  'Religion',
            sect_label:      'Sect',
            caste_label:     'Caste',
            marital_status_label: 'Marital Status',
            place_of_birth:  'Place of Birth',
            alias:           'Alias',
            language_read_write: 'Languages (R/W)',
            language_speak:  'Languages (Spoken)',
            physical_appearance: 'Physical Appearance'
        });
        return html;
    }

    function renderDetailReadOnly(d) {
        if ( ! d) return '<p class="text-muted p-3">No data available.</p>';
        var html = '<div class="d-flex justify-content-end mb-2">'
            + '<button type="button" class="btn btn-sm btn-outline-primary" id="btnEditDetail">'
            + '<i class="fas fa-edit mr-1"></i>Edit</button></div>';
        html += kvTable(d, {
            alias:           'Alias',
            dob:             'Date of Birth',
            gender:          'Gender',
            marital_status:  'Marital Status',
            religion_label:  'Religion',
            sect_label:      'Sect',
            caste_label:     'Caste',
            nationality_label: 'Nationality',
            place_of_birth:  'Place of Birth',
            mother_tongue:   'Mother Tongue',
            language_read_write: 'Languages (R/W)',
            language_speak:  'Languages (Spoken)',
            language_accent: 'Language Accent',
            physical_appearance: 'Physical Appearance',
            other_details:   'Other Details',
            temporary_address: 'Temp Address',
            is_sensitive_department: 'Sensitive Dept.'
        });
        return html;
    }

    // ----------------------------------------------------------------
    // Load lookup data (regions, religions, etc.) from server — cached
    // ----------------------------------------------------------------
    function loadLookups(callback) {
        if (LOOKUPS) { callback(LOOKUPS); return; }
        $.ajax({
            url: BASE + 'api/lookups',
            method: 'GET',
            dataType: 'json',
            success: function (resp) {
                if (resp && resp.status === 'ok') {
                    LOOKUPS = resp.data;
                    callback(LOOKUPS);
                } else {
                    callback(null);
                }
            },
            error: function () { callback(null); }
        });
    }

    // ----------------------------------------------------------------
    // Build <option> HTML for a select from an array of objects
    // ----------------------------------------------------------------
    function buildOptions(items, idKey, labelKey, selectedVal) {
        var html = '<option value="">— Select —</option>';
        if ( ! items) return html;
        $.each(items, function (_, item) {
            var sel = (item[idKey] !== undefined && String(item[idKey]) === String(selectedVal)) ? ' selected' : '';
            html += '<option value="' + escHtml(item[idKey]) + '"' + sel + '>' + escHtml(item[labelKey]) + '</option>';
        });
        return html;
    }

    // ----------------------------------------------------------------
    // Form row helper
    // ----------------------------------------------------------------
    function fRow(label, inputHtml) {
        return '<div class="form-group row mb-2">'
            + '<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">' + label + '</label>'
            + '<div class="col-sm-9">' + inputHtml + '</div>'
            + '</div>';
    }

    function fInput(name, val, type) {
        return '<input type="' + (type || 'text') + '" class="form-control form-control-sm" name="' + escHtml(name) + '" value="' + escHtml(val) + '">';
    }

    function fTextarea(name, val, rows) {
        return '<textarea class="form-control form-control-sm" name="' + escHtml(name) + '" rows="' + (rows || 2) + '">' + escHtml(val) + '</textarea>';
    }

    function fSelect(name, optionsHtml) {
        return '<select class="form-control form-control-sm" name="' + escHtml(name) + '">' + optionsHtml + '</select>';
    }

    // ----------------------------------------------------------------
    // Build BASIC INFO editable form
    // ----------------------------------------------------------------
    function buildBasicInfoForm(d, lk) {
        if ( ! d) return '<p class="text-muted p-3">No data available.</p>';
        lk = lk || {};

        var regionOpts   = buildOptions(lk.regions, 'region_id', 'name', d.region_id);
        var districtOpts = '<option value="">— Select Region First —</option>';
        if (d.district_id) {
            // Pre-populate district option so it shows the saved value before cascade loads
            districtOpts = '<option value="' + escHtml(d.district_id) + '" selected>'
                + escHtml(d.district || 'District #' + d.district_id) + '</option>';
        }
        var psOpts = '<option value="">— Select District First —</option>';
        if (d.police_station_id) {
            psOpts = '<option value="' + escHtml(d.police_station_id) + '" selected>'
                + 'Police Station #' + escHtml(d.police_station_id) + '</option>';
        }

        var html = '<form id="basicinfoForm" class="mt-3">';
        html += '<input type="hidden" name="pid" value="' + escHtml(PID) + '">';
        html += fRow('First Name',   fInput('first_name',  d.first_name));
        html += fRow('Middle Name',  fInput('middle_name', d.middle_name));
        html += fRow('Last Name',    fInput('last_name',   d.last_name));
        html += fRow("Father's Name", fInput('father_name', d.father_name));
        html += fRow('Permanent Address', fTextarea('address', d.address, 2));
        html += fRow('Region', fSelect('region_id', regionOpts));
        html += fRow('District', '<select class="form-control form-control-sm" name="district_id" id="basicDistrictSel">' + districtOpts + '</select>');
        html += fRow('Police Station', '<select class="form-control form-control-sm" name="police_station_id" id="basicPsSel">' + psOpts + '</select>');
        html += '<div class="mt-3">'
            + '<button type="submit" class="btn btn-primary btn-sm mr-2"><i class="fas fa-save mr-1"></i>Save Changes</button>'
            + '<button type="button" class="btn btn-secondary btn-sm" id="btnCancelBasic"><i class="fas fa-times mr-1"></i>Cancel</button>'
            + '</div>';
        html += '</form>';
        return html;
    }

    // ----------------------------------------------------------------
    // Build DETAILED INFO editable form
    // ----------------------------------------------------------------
    function buildDetailedInfoForm(d, lk) {
        if ( ! d) return '<p class="text-muted p-3">No data available.</p>';
        lk = lk || {};

        var genderOpts = '<option value="">— Select —</option>'
            + '<option value="1"' + (String(d.gender) === '1' ? ' selected' : '') + '>Male</option>'
            + '<option value="2"' + (String(d.gender) === '2' ? ' selected' : '') + '>Female</option>'
            + '<option value="3"' + (String(d.gender) === '3' ? ' selected' : '') + '>Other</option>';

        var sensOpts = '<option value="">— Select —</option>'
            + '<option value="0"' + (String(d.is_sensitive_department) === '0' ? ' selected' : '') + '>No</option>'
            + '<option value="1"' + (String(d.is_sensitive_department) === '1' ? ' selected' : '') + '>Yes</option>';

        var religionOpts  = buildOptions(lk.religions,        'id', 'religion',      d.religion);
        var sectOpts      = '<option value="">— Select Religion First —</option>';
        if (d.sect) {
            // Pre-populate current sect so it shows before cascade loads
            sectOpts = '<option value="' + escHtml(d.sect) + '" selected>'
                + escHtml(d.sect_label || 'Sect #' + d.sect) + '</option>';
        }
        var maritalOpts   = buildOptions(lk.marital_statuses, 'id', 'marital_status', d.marital_status_id || d.marital_status);
        var casteOpts     = buildOptions(lk.castes,           'id', 'caste',          d.caste);
        var countryOpts   = buildOptions(lk.countries,        'id', 'nicename',       d.nationality);

        var html = '<form id="detailInfoForm" class="mt-3">';
        html += '<input type="hidden" name="pid" value="' + escHtml(PID) + '">';
        html += '<div class="row">';
        html += '<div class="col-md-6">';
        html += fRow('Alias',          fInput('alias', d.alias));
        html += fRow('Date of Birth',  fInput('dob',   d.dob, 'date'));
        html += fRow('Gender',         fSelect('gender', genderOpts));
        html += fRow('Marital Status', fSelect('marital_status', maritalOpts));
        html += fRow('Religion',       fSelect('religion', religionOpts));
        html += fRow('Sect',           '<select class="form-control form-control-sm" name="sect" id="detailSectSel">' + sectOpts + '</select>');
        html += fRow('Caste',          fSelect('caste', casteOpts));
        html += fRow('Nationality',    fSelect('nationality', countryOpts));
        html += fRow('Place of Birth', fInput('place_of_birth', d.place_of_birth));
        html += '</div>';
        html += '<div class="col-md-6">';
        html += fRow('Mother Tongue',  fInput('mother_tongue',    d.mother_tongue));
        html += fRow('Languages (R/W)',fInput('language_read_write', d.language_read_write));
        html += fRow('Languages (Spoken)', fInput('language_speak', d.language_speak));
        html += fRow('Language Accent',fInput('language_accent',  d.language_accent));
        html += fRow('Physical Appearance', fTextarea('physical_appearance', d.physical_appearance, 2));
        html += fRow('Other Details',  fTextarea('other_details', d.other_details, 2));
        html += fRow('Temp Address',   fTextarea('temporary_address', d.temporary_address, 2));
        html += fRow('Sensitive Dept', fSelect('is_sensitive_department', sensOpts));
        html += '</div>';
        html += '</div>';
        html += '<div class="mt-3">'
            + '<button type="submit" class="btn btn-primary btn-sm mr-2"><i class="fas fa-save mr-1"></i>Save Changes</button>'
            + '<button type="button" class="btn btn-secondary btn-sm" id="btnCancelDetail"><i class="fas fa-times mr-1"></i>Cancel</button>'
            + '</div>';
        html += '</form>';
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
                if (tabLoaded[target]) return;
                tabLoaded[target] = true;
                var $paneBasic = $(target);
                $paneBasic.html('<div class="p-3"><div class="spinner-border text-primary" role="status"></div> Loading…</div>');
                $.when(
                    $.ajax({ url: BASE + 'api/persons/' + PID + '/basic',  method: 'GET', dataType: 'json' }),
                    $.ajax({ url: BASE + 'api/lookups',                     method: 'GET', dataType: 'json' })
                ).done(function (basicResp, lookupResp) {
                    basicInfoData = (basicResp[0]  && basicResp[0].status  === 'ok') ? basicResp[0].data  : null;
                    if (lookupResp[0] && lookupResp[0].status === 'ok') LOOKUPS = lookupResp[0].data;
                    $paneBasic.html(renderBasicReadOnly(basicInfoData));
                }).fail(function () {
                    $paneBasic.html('<p class="text-danger p-3"><i class="fas fa-exclamation-circle mr-1"></i>Failed to load basic info.</p>');
                });
                break;

            // ----------------------------------------------------------
            case '#tab-detailed':
                if (tabLoaded[target]) return;
                tabLoaded[target] = true;
                var $paneDetail = $(target);
                $paneDetail.html('<div class="p-3"><div class="spinner-border text-primary" role="status"></div> Loading…</div>');
                $.when(
                    $.ajax({ url: BASE + 'api/persons/' + PID + '/detailed', method: 'GET', dataType: 'json' }),
                    $.ajax({ url: BASE + 'api/lookups',                       method: 'GET', dataType: 'json' })
                ).done(function (detailResp, lookupResp) {
                    detailInfoData = (detailResp[0] && detailResp[0].status === 'ok') ? detailResp[0].data : null;
                    if (lookupResp[0] && lookupResp[0].status === 'ok') LOOKUPS = lookupResp[0].data;
                    $paneDetail.html(renderDetailReadOnly(detailInfoData));
                }).fail(function () {
                    $paneDetail.html('<p class="text-danger p-3"><i class="fas fa-exclamation-circle mr-1"></i>Failed to load detailed info.</p>');
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
                if (tabLoaded[target]) return;
                tabLoaded[target] = true;
                var $paneAff = $(target);
                $paneAff.html('<div class="p-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading…</div>');
                $.when(
                    $.ajax({ url: BASE + 'api/persons/' + PID + '/affiliations', method: 'GET', dataType: 'json' }),
                    $.ajax({ url: BASE + 'api/persons/' + PID + '/trainings',    method: 'GET', dataType: 'json' })
                ).done(function (affResp, trainResp) {
                    var affData   = (affResp[0]   && affResp[0].status   === 'ok') ? affResp[0].data   : [];
                    var trainData = (trainResp[0]  && trainResp[0].status === 'ok') ? trainResp[0].data : [];

                    var addAffBtn = '<button class="btn btn-sm btn-success btn-add-row mr-2" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Affiliation</button>';
                    var addTrainBtn = '<button class="btn btn-sm btn-info btn-add-training" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Training</button>';

                    var affHtml = dataTable(affData, [
                        {key: 'organization_id',   label: 'Organization ID'},
                        {key: 'ideological_stance',label: 'Stance'},
                        {key: 'designation',       label: 'Designation'},
                        {key: 'is_trained',        label: 'Trained',
                            render: function (r) { return r.is_trained ? 'Yes' : 'No'; }},
                        {key: 'remarks',           label: 'Details'}
                    ], { tab: target, actions: {edit: true, del: true} });

                    var trainHtml = dataTable(trainData, [
                        {key: 'training_camp',     label: 'Camp'},
                        {key: 'training_site',     label: 'Site'},
                        {key: 'training_year',     label: 'Year'},
                        {key: 'training_duration', label: 'Duration'},
                        {key: 'training_purpose',  label: 'Purpose'}
                    ], { tab: '#tab-trainings', actions: {edit: true, del: true} });

                    var html = '<div class="mb-2">' + addAffBtn + addTrainBtn + '</div>';
                    html += '<h6 class="mt-3 mb-1 text-secondary">Affiliations</h6>' + affHtml;
                    html += '<h6 class="mt-4 mb-1 text-secondary">Trainings</h6>' + trainHtml;
                    $paneAff.html(html);
                }).fail(function () {
                    $paneAff.html('<p class="text-danger p-3"><i class="fas fa-exclamation-circle mr-1"></i>Failed to load affiliations/trainings.</p>');
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
            '#tab-trainings':  'delete_training',
            '#tab-reports':    'deletereport'
        };
        var action = urlMap[tab];
        if ( ! action) return;
        // For training records, reload affiliations tab (which renders both sections)
        var tabToReload = (tab === '#tab-trainings') ? '#tab-affiliations' : tab;
        deleteRecord(BASE + 'personprofile/' + action, {pid: PID, id: id}, tabToReload);
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

    // Add Training button — opens blank training modal, reloads affiliations tab on save
    $(document).on('click', '.btn-add-training', function () {
        _openEditModal('#tab-trainings', {});
        // Patch modal save to reload affiliations tab (which contains both sections)
        $('#suspectModal').one('hide.bs.modal', function () {
            delete tabLoaded['#tab-affiliations'];
            reloadTab('#tab-affiliations');
        });
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
            // Training records live inside the affiliations tab
            var reloadTabId = (tab === '#tab-trainings') ? '#tab-affiliations' : tab;
            saveRecord(BASE + 'personprofile/' + config.saveAction, data, reloadTabId);
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
            '#tab-trainings': {
                label: 'Training',
                saveAction: 'update_trainings',
                fields: [
                    {name: 'organization_id',   label: 'Organization ID', type: 'number'},
                    {name: 'training_camp',     label: 'Camp'},
                    {name: 'training_site',     label: 'Site'},
                    {name: 'training_year',     label: 'Year', type: 'number'},
                    {name: 'training_duration', label: 'Duration'},
                    {name: 'training_purpose',  label: 'Purpose'},
                    {name: 'material_taught',   label: 'Material Taught', type: 'textarea'},
                    {name: 'other_details',     label: 'Other Details', type: 'textarea'}
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
    // Basic info / Detailed info — Edit / Cancel / Save handlers
    // ================================================================

    // Edit button → switch basic info tab to editable form
    $(document).on('click', '#btnEditBasic', function () {
        var lk = LOOKUPS || {};
        $('#tab-basic').html(buildBasicInfoForm(basicInfoData, lk));
        if (basicInfoData && basicInfoData.region_id) {
            loadDistrictCascade('#basicDistrictSel', basicInfoData.region_id, basicInfoData.district_id, function () {
                if (basicInfoData && basicInfoData.district_id) {
                    loadPoliceCascade('#basicPsSel', basicInfoData.district_id, basicInfoData.police_station_id);
                }
            });
        }
    });

    // Cancel → restore read-only view without a server round-trip
    $(document).on('click', '#btnCancelBasic', function () {
        $('#tab-basic').html(renderBasicReadOnly(basicInfoData));
    });

    // Edit button → switch detailed info tab to editable form
    $(document).on('click', '#btnEditDetail', function () {
        var lk = LOOKUPS || {};
        $('#tab-detailed').html(buildDetailedInfoForm(detailInfoData, lk));
        if (detailInfoData && detailInfoData.religion) {
            loadSectCascade('#detailSectSel', detailInfoData.religion, detailInfoData.sect);
        }
    });

    // Cancel → restore read-only view without a server round-trip
    $(document).on('click', '#btnCancelDetail', function () {
        $('#tab-detailed').html(renderDetailReadOnly(detailInfoData));
    });

    // Save basic info form → POST, on success reload tab (shows fresh read-only view)
    $(document).on('submit', '#basicinfoForm', function (e) {
        e.preventDefault();
        var data = $(this).serializeArray().reduce(function (obj, item) {
            obj[item.name] = item.value; return obj;
        }, {});
        saveRecord(BASE + 'personprofile/update_basic_info', data, '#tab-basic', 'Basic info updated.');
    });

    // Save detailed info form → POST, on success reload tab
    $(document).on('submit', '#detailInfoForm', function (e) {
        e.preventDefault();
        var data = $(this).serializeArray().reduce(function (obj, item) {
            obj[item.name] = item.value; return obj;
        }, {});
        saveRecord(BASE + 'personprofile/update_detail_info', data, '#tab-detailed', 'Detail info updated.');
    });

    // ================================================================
    // Cascade: Region → District → Police Station
    // ================================================================

    function loadDistrictCascade(selId, regionId, selectedDistrictId, done) {
        $(selId).html('<option>Loading…</option>').prop('disabled', true);
        $.ajax({
            url: BASE + 'personprofile/get_district',
            method: 'POST',
            data: { region_id: regionId },
            dataType: 'json',
            success: function (items) {
                var opts = '<option value="">— Select District —</option>';
                if (items && items.length) {
                    $.each(items, function (_, d) {
                        var sel = (String(d.district_id) === String(selectedDistrictId)) ? ' selected' : '';
                        opts += '<option value="' + escHtml(d.district_id) + '"' + sel + '>' + escHtml(d.name) + '</option>';
                    });
                }
                $(selId).html(opts).prop('disabled', false);
                if (done) done();
            },
            error: function () {
                $(selId).html('<option value="">— Load failed —</option>').prop('disabled', false);
                if (done) done();
            }
        });
    }

    function loadPoliceCascade(selId, districtId, selectedPsId) {
        $(selId).html('<option>Loading…</option>').prop('disabled', true);
        $.ajax({
            url: BASE + 'personprofile/get_police_station',
            method: 'POST',
            data: { district_id: districtId },
            dataType: 'json',
            success: function (items) {
                var opts = '<option value="">— Select Police Station —</option>';
                if (items && items.length) {
                    $.each(items, function (_, p) {
                        var sel = (String(p.ps_id) === String(selectedPsId)) ? ' selected' : '';
                        opts += '<option value="' + escHtml(p.ps_id) + '"' + sel + '>' + escHtml(p.ps_name) + '</option>';
                    });
                }
                $(selId).html(opts).prop('disabled', false);
            },
            error: function () {
                $(selId).html('<option value="">— Load failed —</option>').prop('disabled', false);
            }
        });
    }

    function loadSectCascade(selId, religionId, selectedSectId) {
        $(selId).html('<option>Loading…</option>').prop('disabled', true);
        $.ajax({
            url: BASE + 'personprofile/get_sect',
            method: 'POST',
            data: { religion_id: religionId },
            dataType: 'json',
            success: function (items) {
                var opts = '<option value="">— Select Sect —</option>';
                if (items && items.length) {
                    $.each(items, function (_, s) {
                        var sel = (String(s.id) === String(selectedSectId)) ? ' selected' : '';
                        opts += '<option value="' + escHtml(s.id) + '"' + sel + '>' + escHtml(s.sect) + '</option>';
                    });
                }
                $(selId).html(opts).prop('disabled', false);
            },
            error: function () {
                $(selId).html('<option value="">— Load failed —</option>').prop('disabled', false);
            }
        });
    }

    // Cascade: when Region changes on basic info form → reload districts
    $(document).on('change', '#basicinfoForm [name="region_id"]', function () {
        var regionId = $(this).val();
        loadDistrictCascade('#basicDistrictSel', regionId, '', function () {
            $('#basicPsSel').html('<option value="">— Select District First —</option>');
        });
    });

    // Cascade: when District changes on basic info form → reload police stations
    $(document).on('change', '#basicinfoForm #basicDistrictSel', function () {
        var districtId = $(this).val();
        if (districtId) {
            loadPoliceCascade('#basicPsSel', districtId, '');
        } else {
            $('#basicPsSel').html('<option value="">— Select District First —</option>');
        }
    });

    // Cascade: when Religion changes on detail info form → reload sects
    $(document).on('change', '#detailInfoForm [name="religion"]', function () {
        var religionId = $(this).val();
        if (religionId) {
            loadSectCascade('#detailSectSel', religionId, '');
        } else {
            $('#detailSectSel').html('<option value="">— Select Religion First —</option>');
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
