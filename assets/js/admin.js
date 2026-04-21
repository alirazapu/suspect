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
        var btnLabel = d
            ? '<i class="fas fa-edit mr-1"></i>Edit'
            : '<i class="fas fa-plus mr-1"></i>Add Details';
        var btnClass = d ? 'btn-outline-primary' : 'btn-success';
        var html = '<div class="d-flex justify-content-end mb-2">'
            + '<button type="button" class="btn btn-sm ' + btnClass + '" id="btnEditDetail">'
            + btnLabel + '</button></div>';
        if ( ! d) {
            html += '<p class="text-muted p-3">No details recorded yet. Click <strong>Add Details</strong> to enter information.</p>';
            return html;
        }
        // Pre-process values for human-readable display
        var genders = {1: 'Male', 2: 'Female', 3: 'Other'};
        var display = $.extend({}, d);
        display.gender_label            = genders[d.gender] || (d.gender !== undefined && d.gender !== '' ? d.gender : '—');
        display.is_sensitive_dept_label = d.is_sensitive_department === 1 || d.is_sensitive_department === '1' ? 'Yes' : (d.is_sensitive_department === 0 || d.is_sensitive_department === '0' ? 'No' : '—');
        html += kvTable(display, {
            alias:                    'Alias',
            dob:                      'Date of Birth',
            gender_label:             'Gender',
            marital_status:           'Marital Status',
            religion_label:           'Religion',
            sect_label:               'Sect',
            caste_label:              'Caste',
            nationality_label:        'Nationality',
            place_of_birth:           'Place of Birth',
            mother_tongue:            'Mother Tongue',
            language_read_write:      'Languages (R/W)',
            language_speak:           'Languages (Spoken)',
            language_accent:          'Language Accent',
            physical_appearance:      'Physical Appearance',
            other_details:            'Other Details',
            temporary_address:        'Temporary Address',
            is_sensitive_dept_label:  'Belongs to Sensitive Dept.'
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
                + escHtml(d.police_station_name || 'Station #' + d.police_station_id) + '</option>';
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
        d  = d  || {};   // allow adding details when no row exists yet
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
            sectOpts = '<option value="' + escHtml(d.sect) + '" selected>'
                + escHtml(d.sect_label || 'Sect #' + d.sect) + '</option>';
        }
        var maritalOpts   = buildOptions(lk.marital_statuses, 'id', 'marital_status', d.marital_status_id || d.marital_status);
        var casteOpts     = buildOptions(lk.castes,           'id', 'caste',          d.caste);
        var countryOpts   = buildOptions(lk.countries,        'id', 'nicename',       d.nationality);

        // Mother Tongue — static list
        var motherTongues = ['Urdu', 'Punjabi', 'Sindhi', 'Pashto', 'Balochi', 'Saraiki',
                             'Hindko', 'Brahui', 'Kashmiri', 'Shina', 'Balti', 'English', 'Arabic', 'Other'];
        var motherTongueOpts = '<option value="">— Select —</option>';
        $.each(motherTongues, function (_, lang) {
            var sel = (d.mother_tongue === lang) ? ' selected' : '';
            motherTongueOpts += '<option value="' + escHtml(lang) + '"' + sel + '>' + escHtml(lang) + '</option>';
        });

        // Temporary address — region/district/police cascades
        var tempRegionOpts = buildOptions(lk.regions, 'region_id', 'name', d.region_id);
        var tempDistrictOpts = '<option value="">— Select Region First —</option>';
        if (d.district_id) {
            tempDistrictOpts = '<option value="' + escHtml(d.district_id) + '" selected>'
                + escHtml(d.district || 'District #' + d.district_id) + '</option>';
        }
        var tempPsOpts = '<option value="">— Select District First —</option>';
        if (d.police_station_id) {
            tempPsOpts = '<option value="' + escHtml(d.police_station_id) + '" selected>'
                + escHtml(d.police_station_name || 'Station #' + d.police_station_id) + '</option>';
        }

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
        html += fRow('Mother Tongue',  '<select class="form-control form-control-sm" name="mother_tongue">' + motherTongueOpts + '</select>');
        html += '</div>';
        html += '<div class="col-md-6">';
        html += fRow('Languages (R/W)',    fInput('language_read_write', d.language_read_write));
        html += fRow('Languages (Spoken)', fInput('language_speak', d.language_speak));
        html += fRow('Language Accent',    fInput('language_accent', d.language_accent));
        html += fRow('Physical Appearance', fTextarea('physical_appearance', d.physical_appearance, 2));
        html += fRow('Other Details',       fTextarea('other_details', d.other_details, 2));
        html += fRow('Belongs to Sensitive Dept.', fSelect('is_sensitive_department', sensOpts));
        html += '</div>';
        html += '</div>';
        // Temporary address section
        html += '<hr><h6 class="text-secondary mt-2 mb-2"><i class="fas fa-map-marker-alt mr-1"></i>Temporary Address</h6>';
        html += '<div class="row">';
        html += '<div class="col-md-6">';
        html += fRow('Address Text', fTextarea('temporary_address', d.temporary_address, 2));
        html += fRow('Region', '<select class="form-control form-control-sm" name="region_id" id="detailTempRegionSel">' + tempRegionOpts + '</select>');
        html += '</div>';
        html += '<div class="col-md-6">';
        html += fRow('District', '<select class="form-control form-control-sm" name="district_id" id="detailTempDistrictSel">' + tempDistrictOpts + '</select>');
        html += fRow('Police Station', '<select class="form-control form-control-sm" name="police_station_id" id="detailTempPsSel">' + tempPsOpts + '</select>');
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
        if ( ! rows || ! rows.length) return '<p class="text-muted py-4 text-center"><i class="fas fa-inbox mr-1"></i>No records found.</p>';
        var html = '<div class="table-responsive"><table class="table table-sm table-striped table-bordered"><thead><tr>';
        $.each(cols, function (_, col) { html += '<th>' + escHtml(col.label) + '</th>'; });
        if (opts.actions) { html += '<th class="text-center" style="width:75px">Actions</th>'; }
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
                html += '<td class="text-center" style="white-space:nowrap">';
                if (opts.actions.edit) {
                    html += '<button class="btn btn-xs btn-outline-primary btn-edit-row" '
                        + 'data-row="' + rowJson + '" data-tab="' + opts.tab + '" title="Edit">'
                        + '<i class="fas fa-edit"></i></button>';
                }
                if (opts.actions.del) {
                    html += ' <button class="btn btn-xs btn-outline-danger btn-del-row" '
                        + 'data-id="' + escHtml(row.id) + '" data-tab="' + opts.tab + '" title="Delete">'
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
    // Build a hidden inline-form wrapper for a given tab
    // ----------------------------------------------------------------
    function _makeFormWrap(tabId, colorClass) {
        colorClass = colorClass || 'info';
        return '<div class="tab-inline-form-wrap" data-tab="' + tabId + '" style="display:none">'
            + '<div class="card border-' + colorClass + ' mb-3">'
            + '<div class="card-header bg-' + colorClass + ' text-white d-flex justify-content-between align-items-center py-2">'
            + '<strong class="tab-form-title">Add Record</strong>'
            + '<button type="button" class="btn btn-sm btn-light btn-inline-cancel" data-tab="' + tabId + '">'
            + '<i class="fas fa-times mr-1"></i>Cancel</button>'
            + '</div>'
            + '<div class="card-body inline-form-body"></div>'
            + '</div></div>';
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
                    var formWrap = _makeFormWrap(tabId, 'info');
                    var addBtn = '';
                    if (opts && opts.addBtn) {
                        addBtn = '<div class="mb-2 d-flex justify-content-end tab-add-btn-wrap" data-tab="' + tabId + '">'
                            + opts.addBtn + '</div>';
                    }
                    $pane.html(formWrap + addBtn + render(resp.data));
                    initSelect2($pane[0]);
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
    // Initialise Select2 on every <select> inside a context element
    // ----------------------------------------------------------------
    function initSelect2(ctx) {
        if (typeof $.fn.select2 !== 'function') { return; }
        $(ctx || document).find('select').not('.no-select2').each(function () {
            // Destroy existing Select2 instance first to avoid duplicate binding
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true,
                placeholder: '— Select —'
            });
        });
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
                        {key: 'identity_type', label: 'Identity Type'},
                        {key: 'identity_no',   label: 'Identity Number'}
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
                        {key: 'edu_type',      label: 'Education Type',
                            render: function (r) { return r.edu_type == 0 ? 'Religious' : 'Non-Religious'; }},
                        {key: 'degree',        label: 'Degree/Certificate'},
                        {key: 'institution',   label: 'Institution'},
                        {key: 'passing_year',  label: 'Passing Year'},
                        {key: 'education_level_label', label: 'Education Level'}
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
                        {key: 'duration_label', label: 'Duration'},
                        {key: 'file_link',      label: 'Document', render: function (r) {
                            return r.file_link
                                ? '<a href="' + escHtml(BASE + r.file_link) + '" target="_blank"><i class="fas fa-file mr-1"></i>View</a>'
                                : '<em class="text-muted">—</em>';
                        }}
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
                        {key: 'bank_display',   label: 'Bank'},
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
                        {key: 'asset_acquired_how', label: 'Acquired How'},
                        {key: 'file_link', label: 'Document', render: function (r) {
                            return r.file_link
                                ? '<a href="' + escHtml(BASE + r.file_link) + '" target="_blank"><i class="fas fa-file mr-1"></i>View</a>'
                                : '<em class="text-muted">—</em>';
                        }}
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
                        {key: 'mobile_number',        label: 'Number'},
                        {key: 'operator',             label: 'Operator'},
                        {key: 'sim_owner',            label: 'SIM Owner'},
                        {key: 'contact_type_label',   label: 'Contact Type'},
                        {key: 'status_label',         label: 'Status'},
                        {key: 'connection_type_label',label: 'Connection'},
                        {key: 'sim_activated_at',     label: 'Activated At'},
                        {key: 'sim_last_used_at',     label: 'Last Used At'}
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
                        {key: 'rel_from_name', label: 'Relation From',
                            render: function (r) {
                                var n = (r.rel_from_name || '').trim() || '#' + r.rel_from_id;
                                var c = r.rel_from_cnic ? ' <small class="text-muted">(' + escHtml(r.rel_from_cnic) + ')</small>' : '';
                                return escHtml(n) + c;
                            }},
                        {key: 'relation_type',  label: 'Relation Type'},
                        {key: 'rel_to_name',    label: 'Relation With',
                            render: function (r) {
                                var n = (r.rel_to_name || '').trim() || '#' + r.rel_to_id;
                                return escHtml(n);
                            }},
                        {key: 'cnic',           label: 'CNIC'},
                        {key: 'country',        label: 'Country'},
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
                        {key: 'fir_number',             label: 'FIR No.'},
                        {key: 'police_station',         label: 'Police Station'},
                        {key: 'district',               label: 'District'},
                        {key: 'case_date',              label: 'FIR Date'},
                        {key: 'section',                label: 'Section(s)'},
                        {key: 'status',                 label: 'Case Status'},
                        {key: 'accused_position_label', label: 'Accused As'}
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

                    var formWrapAff   = _makeFormWrap('#tab-affiliations', 'info');
                    var formWrapTrain = _makeFormWrap('#tab-trainings', 'success');

                    var addAffBtn   = '<div class="tab-add-btn-wrap d-inline-block mr-2" data-tab="#tab-affiliations">'
                        + '<button class="btn btn-sm btn-success btn-add-row" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Affiliation</button></div>';
                    var addTrainBtn = '<div class="tab-add-btn-wrap d-inline-block" data-tab="#tab-trainings">'
                        + '<button class="btn btn-sm btn-info btn-add-training" data-tab="' + target + '">'
                        + '<i class="fas fa-plus mr-1"></i>Add Training</button></div>';

                    var affHtml = dataTable(affData, [
                        {key: 'organization_name', label: 'Organization',
                            render: function (r) {
                                var n = r.organization_name || '';
                                return n ? escHtml(n) : (r.organization_id ? '#' + escHtml(r.organization_id) : '<em class="text-muted">—</em>');
                            }},
                        {key: 'ideological_stance',label: 'Ideological Stance'},
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

                    var html = formWrapAff + formWrapTrain + '<div class="mb-2 d-flex justify-content-end">' + addAffBtn + addTrainBtn + '</div>';
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
                        {key: 'summary',           label: 'Details'},
                        {key: 'file_link', label: 'Attachment', render: function (r) {
                            return r.file_link
                                ? '<a href="' + escHtml(BASE + r.file_link) + '" target="_blank"><i class="fas fa-paperclip mr-1"></i>View</a>'
                                : '<em class="text-muted">—</em>';
                        }}
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
        _openInlineEditForm(tab, row);
    });

    // Add button — open a blank modal
    $(document).on('click', '.btn-add-row', function () {
        var $btn = $(this);
        var tab  = $btn.data('tab');
        _openInlineEditForm(tab, {});
    });

    // Add Training button — opens blank training inline form inside affiliations pane
    $(document).on('click', '.btn-add-training', function () {
        _openInlineEditForm('#tab-trainings', {});
    });

    function _openInlineEditForm(tab, row) {
        var config = _tabEditConfig(tab);
        if ( ! config) { showToast('warning', 'Edit not supported for this tab yet.'); return; }

        // Training records live inside the affiliations tab pane
        var paneId     = (tab === '#tab-trainings') ? '#tab-affiliations' : tab;
        var reloadTabId = (tab === '#tab-trainings') ? '#tab-affiliations' : tab;
        var isEdit     = !! row.id;

        var $wrap = $(paneId).find('.tab-inline-form-wrap[data-tab="' + tab + '"]');
        if ( ! $wrap.length) return;

        loadLookups(function (lk) {
            var formHtml = '<form id="inlineEditForm" enctype="multipart/form-data"'
                + ' data-save-action="' + escHtml(config.saveAction) + '"'
                + ' data-reload-tab="' + escHtml(reloadTabId) + '"'
                + ' data-has-file="' + (config.hasFileUpload ? '1' : '0') + '">';
            formHtml += '<input type="hidden" name="pid" value="' + escHtml(PID) + '">';
            if (row.id) {
                formHtml += '<input type="hidden" name="id" value="' + escHtml(row.id) + '">';
            }
            formHtml += '<div class="row">';
            $.each(config.fields, function (_, f) {
                // dataKey allows the form field name to differ from the row-data key
                var dataKey = f.dataKey || f.name;
                var val = (row[dataKey] !== undefined && row[dataKey] !== null) ? row[dataKey] : '';
                var colClass = (f.type === 'textarea' || f.type === 'person-lookup'
                    || f.type === 'criminal-ps-cascade') ? 'col-12' : 'col-md-6';
                formHtml += '<div class="form-group ' + colClass + ' mb-2">';
                formHtml += '<label class="font-weight-bold small mb-1">' + escHtml(f.label) + '</label>';

                if (f.type === 'textarea') {
                    formHtml += '<textarea class="form-control form-control-sm" name="' + escHtml(f.name) + '" rows="3">' + escHtml(val) + '</textarea>';
                } else if (f.type === 'select') {
                    var opts = f.options || [];
                    if (f.lookupKey && lk && lk[f.lookupKey] && lk[f.lookupKey].length) {
                        opts = $.map(lk[f.lookupKey], function (item) {
                            return {value: item[f.lookupId], label: item[f.lookupLabel]};
                        });
                    }
                    formHtml += '<select class="form-control form-control-sm" name="' + escHtml(f.name) + '">';
                    formHtml += '<option value="">— Select —</option>';
                    $.each(opts, function (_, opt) {
                        var sel = (String(val) === String(opt.value)) ? ' selected' : '';
                        formHtml += '<option value="' + escHtml(opt.value) + '"' + sel + '>' + escHtml(opt.label) + '</option>';
                    });
                    formHtml += '</select>';
                } else if (f.type === 'file') {
                    // Show existing file link if any
                    if (row.file_link) {
                        formHtml += '<div class="mb-1 small text-muted">'
                            + 'Current: <a href="' + escHtml(BASE + row.file_link) + '" target="_blank">'
                            + '<i class="fas fa-file mr-1"></i>View Document</a></div>';
                    }
                    formHtml += '<input type="file" class="form-control-file form-control-sm" name="document">';
                } else if (f.type === 'readonly') {
                    formHtml += '<input type="text" class="form-control form-control-sm bg-light" readonly value="' + escHtml(val) + '">';
                    formHtml += '<input type="hidden" name="' + escHtml(f.name) + '" value="' + escHtml(val) + '">';
                } else if (f.type === 'person-lookup') {
                    // Related person search: text search → fills person_id, shows name+cnic readonly
                    var currentName = (row.name || '').trim() + (row.cnic ? ' (' + row.cnic + ')' : '');
                    formHtml += '<div class="input-group input-group-sm mb-1">'
                        + '<input type="number" id="relPersonId" class="form-control form-control-sm" name="relation_with"'
                        + ' placeholder="Enter person ID" value="' + escHtml(row.relation_with || '') + '" min="1">'
                        + '<div class="input-group-append">'
                        + '<button type="button" class="btn btn-outline-secondary btn-sm" id="btnLookupPerson">'
                        + '<i class="fas fa-search"></i></button>'
                        + '</div></div>'
                        + '<div id="relPersonDisplay" class="form-control form-control-sm bg-light text-muted small"'
                        + ' style="min-height:32px">'
                        + escHtml(currentName || 'Enter ID and click Search')
                        + '</div>';
                } else if (f.type === 'criminal-ps-cascade') {
                    // Region → District → Police Station cascade for criminal form
                    var regionOpts = buildOptions(lk.regions || [], 'region_id', 'name', row.region_id);
                    formHtml += '<div class="row no-gutters">'
                        + '<div class="col-md-4 pr-1">'
                        + '<label class="small font-weight-bold">Region</label>'
                        + '<select class="form-control form-control-sm" id="crimRegionSel">' + regionOpts + '</select>'
                        + '</div>'
                        + '<div class="col-md-4 pr-1">'
                        + '<label class="small font-weight-bold">District</label>'
                        + '<select class="form-control form-control-sm" id="crimDistrictSel" name="_district_id">'
                        + '<option value="">— Select Region First —</option>'
                        + '</select>'
                        + '</div>'
                        + '<div class="col-md-4">'
                        + '<label class="small font-weight-bold">Police Station</label>'
                        + '<select class="form-control form-control-sm" name="police_station_id" id="crimPsSel">'
                        + '<option value="">— Select District First —</option>'
                        + '</select>'
                        + '</div></div>';
                } else {
                    formHtml += '<input type="' + (f.type || 'text') + '" class="form-control form-control-sm" name="'
                        + escHtml(f.name) + '" value="' + escHtml(val) + '">';
                }
                formHtml += '</div>';
            });
            formHtml += '</div>';
            formHtml += '<div class="mt-2">'
                + '<button type="submit" class="btn btn-primary btn-sm mr-2"><i class="fas fa-save mr-1"></i>Save</button>'
                + '</div>';
            formHtml += '</form>';

            $wrap.find('.tab-form-title').text((isEdit ? 'Edit ' : 'Add ') + config.label);
            $wrap.find('.inline-form-body').html(formHtml);
            $wrap.slideDown(200);
            $wrap[0].scrollIntoView({behavior: 'smooth', block: 'start'});
            // Hide the + Add button while the inline form is open
            $(paneId).find('.tab-add-btn-wrap[data-tab="' + tab + '"]').hide();
            // Init Select2 on any selects in the form
            initSelect2($wrap[0]);

            // For criminal form: pre-populate police-station cascade from saved row data
            if (tab === '#tab-criminal' && row.region_id) {
                loadDistrictCascade('#crimDistrictSel', row.region_id, row.district_id, function () {
                    if (row.district_id) {
                        loadPoliceCascade('#crimPsSel', row.district_id, row.police_station_id);
                    }
                });
                initSelect2($wrap[0]);
            }

            // For relation form: if we already have a name from the row data show it
            if (tab === '#tab-relations' && row.relation_with) {
                // name/cnic already shown; do a live lookup to confirm
                $.getJSON(BASE + 'api/persons/' + row.relation_with + '/name_cnic', function (resp) {
                    if (resp && resp.status === 'ok' && resp.data) {
                        var d = resp.data;
                        var display = d.name + (d.cnic ? ' (' + d.cnic + ')' : '');
                        $('#relPersonDisplay').text(display).removeClass('text-muted').addClass('text-dark');
                    }
                });
            }
        });
    }

    function _tabEditConfig(tab) {
        var configs = {
            '#tab-identities': {
                label: 'Identity',
                saveAction: 'update_identity',
                fields: [
                    {name: 'identity_id', label: 'Identity Type', type: 'select',
                        lookupKey: 'identity_types', lookupId: 'id', lookupLabel: 'identity',
                        options: [
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
                    {name: 'edu_type', label: 'Education Type', type: 'select', options: [
                        {value: 0, label: 'Religious'}, {value: 1, label: 'Non-Religious'}
                    ]},
                    {name: 'degree_name',    label: 'Degree/Certificate'},
                    {name: 'institute_name', label: 'Institution'},
                    {name: 'complete_year',  label: 'Passing Year', type: 'number'},
                    {name: 'education_level', label: 'Education Level', type: 'select',
                        lookupKey: 'education_levels', lookupId: 'id', lookupLabel: 'education_level',
                        options: [
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
                hasFileUpload: true,
                fields: [
                    {name: 'income_source_name', label: 'Source Name'},
                    {name: 'details',            label: 'Description', type: 'textarea'},
                    {name: 'income_amount',      label: 'Amount (PKR)', type: 'number'},
                    {name: 'income_source_duration', label: 'Duration', type: 'select', options: [
                        {value: 1, label: 'Daily'}, {value: 2, label: 'Monthly'}, {value: 3, label: 'Yearly'}
                    ]},
                    {name: 'document', label: 'Supporting Document', type: 'file'}
                ]
            },
            '#tab-banks': {
                label: 'Bank Account',
                saveAction: 'update_banks',
                fields: [
                    {name: 'bank_name', dataKey: 'bank_id', label: 'Bank', type: 'select',
                        lookupKey: 'banks', lookupId: 'id', lookupLabel: 'name',
                        options: []},
                    {name: 'account_number',     label: 'Account No.'},
                    {name: 'atm_number',         label: 'ATM No.'},
                    {name: 'branch_name',        label: 'Branch', dataKey: 'branch'},
                    {name: 'is_internet_banking', label: 'Internet Banking', type: 'select', options: [
                        {value: 0, label: 'No'}, {value: 1, label: 'Yes'}
                    ]}
                ]
            },
            '#tab-assets': {
                label: 'Asset',
                saveAction: 'update_personassets',
                hasFileUpload: true,
                fields: [
                    {name: 'asset_name',          label: 'Asset Name'},
                    {name: 'details',             label: 'Description', type: 'textarea'},
                    {name: 'moveable_immovable',  label: 'Type', type: 'select', options: [
                        {value: 0, label: 'Unknown'}, {value: 1, label: 'Moveable'}, {value: 2, label: 'Immovable'}
                    ]},
                    {name: 'asset_value',         label: 'Value (PKR)', type: 'number'},
                    {name: 'since_year',          label: 'Since (Year)', type: 'number'},
                    {name: 'asset_acquired_how',  label: 'Acquired How'},
                    {name: 'document',            label: 'Supporting Document', type: 'file'}
                ]
            },
            '#tab-mobiles': {
                label: 'Mobile Number',
                saveAction: 'update_mobiles',
                fields: [
                    {name: 'phone_number',    label: 'Phone Number'},
                    {name: 'sim_owner',       label: 'SIM Owner'},
                    {name: 'contact_type',    label: 'Contact Type', type: 'select', options: [
                        {value: 1, label: 'Personal'}, {value: 2, label: 'WhatsApp'},
                        {value: 3, label: 'Official'}, {value: 4, label: 'Other'}
                    ]},
                    {name: 'status',          label: 'Status', type: 'select', options: [
                        {value: 0, label: 'Inactive'}, {value: 1, label: 'Active'}
                    ]},
                    {name: 'connection_type', label: 'Connection', type: 'select', options: [
                        {value: 0, label: 'Post-Paid'}, {value: 1, label: 'Pre-Paid'}
                    ]},
                    {name: 'sim_activated_at', label: 'Activated At', type: 'date'},
                    {name: 'sim_last_used_at', label: 'Last Used At', type: 'date'}
                ]
            },
            '#tab-relations': {
                label: 'Relation',
                saveAction: 'update_relations',
                fields: [
                    {name: 'relation_with', label: 'Related Person (ID + Search)', type: 'person-lookup'},
                    {name: 'person_relation_type', label: 'Relation Type', type: 'select',
                        lookupKey: 'relation_types', lookupId: 'id', lookupLabel: 'relation_name',
                        options: [
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
                    {name: 'police_station_id', label: 'Police Station', type: 'criminal-ps-cascade'},
                    {name: 'sections_applied',  label: 'Sections Applied'},
                    {name: 'case_position', label: 'Case Status', type: 'select', options: [
                        {value: 1, label: 'Under Investigation'}, {value: 2, label: 'Under Trial'},
                        {value: 3, label: 'Convicted'}, {value: 4, label: 'Discharged'}
                    ]},
                    {name: 'accused_position',  label: 'Accused Position', type: 'select', options: [
                        {value: 1, label: 'Main Accused'},  {value: 2, label: 'Co-Accused'},
                        {value: 3, label: 'Absconder'},     {value: 4, label: 'Suspect'},
                        {value: 5, label: 'Witness'},       {value: 6, label: 'Arrested'}
                    ]}
                ]
            },
            '#tab-affiliations': {
                label: 'Affiliation',
                saveAction: 'update_affiliations',
                fields: [
                    {name: 'organization_id', dataKey: 'organization_id', label: 'Organization', type: 'select',
                        lookupKey: 'organizations', lookupId: 'org_id', lookupLabel: 'org_name',
                        options: []},
                    {name: 'ideological_stance',       label: 'Ideological Stance', type: 'select', options: [
                        {value: 'Jihadi',       label: 'Jihadi'},
                        {value: 'Sectarian',    label: 'Sectarian'},
                        {value: 'Nationalist',  label: 'Nationalist'},
                        {value: 'Political',    label: 'Political'},
                        {value: 'Religious',    label: 'Religious'},
                        {value: 'Ethnic',       label: 'Ethnic'},
                        {value: 'Criminal',     label: 'Criminal'},
                        {value: 'Other',        label: 'Other'}
                    ]},
                    {name: 'designation',              label: 'Designation', type: 'select', options: [
                        {value: 'Founder',         label: 'Founder'},
                        {value: 'Chief',           label: 'Chief'},
                        {value: 'Commander',       label: 'Commander'},
                        {value: 'Deputy',          label: 'Deputy'},
                        {value: 'Facilitator',     label: 'Facilitator'},
                        {value: 'Member',          label: 'Member'},
                        {value: 'Sympathiser',     label: 'Sympathiser'},
                        {value: 'Financier',       label: 'Financier'},
                        {value: 'Recruiter',       label: 'Recruiter'},
                        {value: 'Trainer',         label: 'Trainer'},
                        {value: 'Other',           label: 'Other'}
                    ]},
                    {name: 'is_trained',               label: 'Is Trained', type: 'select', options: [
                        {value: 0, label: 'No'}, {value: 1, label: 'Yes'}
                    ]},
                    {name: 'details',                  label: 'Details', type: 'textarea'},
                    {name: 'self_recruitment_details', label: 'Self-Recruitment Details (Did You Recruit? How?)', type: 'textarea'}
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
                hasFileUpload: true,
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
                    {name: 'report_details',      label: 'Details', type: 'textarea'},
                    {name: 'document',            label: 'Attachment', type: 'file'}
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
        initSelect2($('#tab-basic')[0]);
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
        initSelect2($('#tab-detailed')[0]);
        if (detailInfoData && detailInfoData.religion) {
            loadSectCascade('#detailSectSel', detailInfoData.religion, detailInfoData.sect);
        }
        // Pre-populate temporary address cascades
        if (detailInfoData && detailInfoData.region_id) {
            loadDistrictCascade('#detailTempDistrictSel', detailInfoData.region_id, detailInfoData.district_id, function () {
                if (detailInfoData && detailInfoData.district_id) {
                    loadPoliceCascade('#detailTempPsSel', detailInfoData.district_id, detailInfoData.police_station_id);
                }
            });
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

    // Inline form save (tab child-record tabs)
    $(document).on('submit', '#inlineEditForm', function (e) {
        e.preventDefault();
        var $form       = $(this);
        var saveAction  = $form.data('save-action');
        var reloadTabId = $form.data('reload-tab');
        var hasFile     = $form.data('has-file') === 1;
        var $fileInput  = $form.find('input[type="file"][name="document"]');
        var hasActualFile = hasFile && $fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0;

        if (hasActualFile) {
            // Use FormData to support file uploads
            var fd = new FormData($form[0]);
            // Remove the file from main save; we do a two-step: save record first, then upload
            fd.delete('document');
            var $btn = $form.find('[type="submit"]');
            $btn.prop('disabled', true);
            $.ajax({
                url: BASE + 'personprofile/' + saveAction,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (resp) {
                    if (resp && resp.status === 'ok') {
                        // Now upload the file — tab name is everything after '#tab-'
                        var uploadTabName = reloadTabId.replace('#tab-', '');
                        var uploadFd = new FormData();
                        uploadFd.append('pid', $form.find('[name="pid"]').val());
                        uploadFd.append('tab', uploadTabName);
                        if (resp.insert_id) uploadFd.append('id', resp.insert_id);
                        else if ($form.find('[name="id"]').val()) uploadFd.append('id', $form.find('[name="id"]').val());
                        uploadFd.append('document', $fileInput[0].files[0]);
                        $.ajax({
                            url: BASE + 'personprofile/upload_doc',
                            method: 'POST',
                            data: uploadFd,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            complete: function () {
                                showToast('success', resp.message || 'Saved.');
                                delete tabLoaded[reloadTabId];
                                reloadTab(reloadTabId);
                                $btn.prop('disabled', false);
                            }
                        });
                    } else {
                        showToast('danger', (resp && resp.message) ? resp.message : 'Save failed.');
                        $btn.prop('disabled', false);
                    }
                },
                error: function () {
                    showToast('danger', 'Network error. Please try again.');
                    $btn.prop('disabled', false);
                }
            });
        } else {
            // Standard save without file
            var data = $form.serializeArray().reduce(function (obj, item) {
                obj[item.name] = item.value; return obj;
            }, {});
            saveRecord(BASE + 'personprofile/' + saveAction, data, reloadTabId);
        }
    });

    // Cancel button on any inline form
    $(document).on('click', '.btn-inline-cancel', function () {
        var $wrap  = $(this).closest('.tab-inline-form-wrap');
        var tab    = $wrap.data('tab');
        var paneId = (tab === '#tab-trainings') ? '#tab-affiliations' : tab;
        $wrap.slideUp(200);
        // Restore the + Add button
        $(paneId).find('.tab-add-btn-wrap[data-tab="' + tab + '"]').show();
    });

    // ================================================================
    // Person lookup — Relations tab: search by ID → show name+CNIC readonly
    // ================================================================
    $(document).on('click', '#btnLookupPerson', function () {
        var pid = parseInt($('#relPersonId').val(), 10);
        if ( ! pid || pid < 1) {
            $('#relPersonDisplay').text('Please enter a valid person ID.').removeClass('text-dark').addClass('text-danger');
            return;
        }
        $('#relPersonDisplay').text('Searching…').removeClass('text-dark text-danger').addClass('text-muted');
        $.getJSON(BASE + 'api/persons/' + pid + '/name_cnic', function (resp) {
            if (resp && resp.status === 'ok' && resp.data) {
                var d = resp.data;
                var display = d.name + (d.cnic ? ' (' + d.cnic + ')' : '');
                $('#relPersonDisplay').text(display).removeClass('text-muted text-danger').addClass('text-dark');
            } else {
                $('#relPersonDisplay').text('Person not found (ID: ' + pid + ').').removeClass('text-muted text-dark').addClass('text-danger');
            }
        }).fail(function () {
            $('#relPersonDisplay').text('Lookup failed. Check ID.').removeClass('text-muted text-dark').addClass('text-danger');
        });
    });

    // Also trigger lookup when user presses Enter or Tab in the ID field
    $(document).on('keydown', '#relPersonId', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#btnLookupPerson').trigger('click'); }
    });

    // ================================================================
    // Cascade: Criminal Record — Region → District → Police Station
    // ================================================================
    $(document).on('change', '#crimRegionSel', function () {
        var regionId = $(this).val();
        if (regionId) {
            loadDistrictCascade('#crimDistrictSel', regionId, '', function () {
                $('#crimPsSel').html('<option value="">— Select District First —</option>');
            });
        } else {
            $('#crimDistrictSel').html('<option value="">— Select Region First —</option>');
            $('#crimPsSel').html('<option value="">— Select District First —</option>');
        }
    });

    $(document).on('change', '#crimDistrictSel', function () {
        var districtId = $(this).val();
        if (districtId) {
            loadPoliceCascade('#crimPsSel', districtId, '');
        } else {
            $('#crimPsSel').html('<option value="">— Select District First —</option>');
        }
    });

    // ================================================================
    // Date picker — clicking anywhere in a date input opens the calendar
    // ================================================================
    $(document).on('click', 'input[type="date"]', function () {
        if (typeof this.showPicker === 'function') {
            try { this.showPicker(); } catch (ex) { /* ignore – not all browsers support */ }
        }
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

    // Cascade: when Temp Region changes on detail info form → reload districts
    $(document).on('change', '#detailInfoForm [name="region_id"]', function () {
        var regionId = $(this).val();
        loadDistrictCascade('#detailTempDistrictSel', regionId, '', function () {
            $('#detailTempPsSel').html('<option value="">— Select District First —</option>');
        });
    });

    // Cascade: when Temp District changes on detail info form → reload police stations
    $(document).on('change', '#detailInfoForm #detailTempDistrictSel', function () {
        var districtId = $(this).val();
        if (districtId) {
            loadPoliceCascade('#detailTempPsSel', districtId, '');
        } else {
            $('#detailTempPsSel').html('<option value="">— Select District First —</option>');
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
