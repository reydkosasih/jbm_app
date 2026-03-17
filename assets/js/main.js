/**
 * JBM Bengkel Mobil — Global JS Utilities
 * Requires: jQuery, SweetAlert2
 *
 * Usage:
 *   JBM.confirm('Hapus item?', 'Data tidak dapat dikembalikan.', () => { /* callback *\/ });
 *   JBM.toast('success', 'Disimpan!');
 *   JBM.loading(true);
 */

/* global Swal, BASE_URL, CSRF_NAME, CSRF_HASH */

const JBM = (function ($) {
    'use strict';

    // ──────────────────────────────────────────────────────────────────────
    // CSRF Token Management
    // ──────────────────────────────────────────────────────────────────────

    /** Return current CSRF hash from meta tag */
    function getCsrfHash() {
        return $('meta[name="csrf-token"]').attr('content') || '';
    }

    function getCsrfName() {
        return $('meta[name="csrf-name"]').attr('content') || 'jbm_csrf_token';
    }

    /** Sync CSRF hash from any JSON response that includes it */
    function syncCsrf(data) {
        if (data && data.csrf_hash) {
            $('meta[name="csrf-token"]').attr('content', data.csrf_hash);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // jQuery AJAX Global Setup
    // ──────────────────────────────────────────────────────────────────────

    /** Inject CSRF token header and sync updated hash on every AJAX call */
    $(document).ajaxSend(function (event, jqXHR) {
        jqXHR.setRequestHeader('X-CSRF-Token', getCsrfHash());
    });

    $(document).ajaxSuccess(function (event, jqXHR, settings, data) {
        syncCsrf(data);
    });

    $(document).ajaxError(function (event, jqXHR) {
        const status = jqXHR.status;
        if (status === 401) {
            Swal.fire({
                icon: 'warning',
                title: 'Sesi Habis',
                text: 'Silakan login kembali.',
                confirmButtonText: 'Login',
            }).then(() => { window.location.href = (typeof BASE_URL !== 'undefined' ? BASE_URL : '/') + 'login'; });
        } else if (status === 403) {
            Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: 'Anda tidak memiliki izin untuk tindakan ini.' });
        } else if (status === 422) {
            try {
                const resp = JSON.parse(jqXHR.responseText);
                Swal.fire({ icon: 'warning', title: 'Validasi Gagal', text: resp.message || 'Periksa kembali data yang diisi.' });
            } catch (e) {
                Swal.fire({ icon: 'warning', title: 'Validasi Gagal', text: 'Periksa kembali data yang diisi.' });
            }
        } else if (status >= 500) {
            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Server error, silakan coba lagi nanti. (' + status + ')' });
        }
    });

    // ──────────────────────────────────────────────────────────────────────
    // Loading Overlay
    // ──────────────────────────────────────────────────────────────────────

    let $overlay = null;

    function ensureOverlay() {
        if (!$overlay || !$overlay.length) {
            $overlay = $('<div id="jbm-loading-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">' +
                '<div style="background:#fff;padding:24px 36px;border-radius:12px;text-align:center;">' +
                '<div class="spinner-border text-primary mb-3" role="status"></div>' +
                '<p class="mb-0 fw-semibold">Memproses…</p>' +
                '</div></div>');
            $('body').append($overlay);
        }
    }

    /**
     * Show or hide the full-screen loading overlay.
     * @param {boolean} show
     */
    function loading(show) {
        ensureOverlay();
        if (show) {
            $overlay.css('display', 'flex');
        } else {
            $overlay.hide();
        }
    }

    // Auto-hide overlay after AJAX complete
    $(document).ajaxStop(function () { loading(false); });

    // ──────────────────────────────────────────────────────────────────────
    // SweetAlert2 Wrappers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Show a SweetAlert2 confirmation dialog then execute a callback.
     * @param {string}   title     Dialog title
     * @param {string}   text      Body text
     * @param {Function} callback  Called when user clicks "Ya"
     * @param {Object}   [options] Extra Swal.fire() options
     */
    function confirm(title, text, callback, options) {
        Swal.fire(Object.assign({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1a73e8',
            cancelButtonColor: '#6c757d',
        }, options || {})).then(function (result) {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    }

    /**
     * Show a non-blocking SweetAlert2 toast.
     * @param {'success'|'error'|'warning'|'info'} icon
     * @param {string} title   Short message text
     * @param {string} [message] Optional sub-text
     * @param {number} [timer]  Auto-dismiss ms (default 3000)
     */
    function toast(icon, title, message, timer) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: timer || 3000,
            timerProgressBar: true,
            didOpen: function (t) {
                t.addEventListener('mouseenter', Swal.stopTimer);
                t.addEventListener('mouseleave', Swal.resumeTimer);
            },
        });
        Toast.fire({ icon: icon, title: title, text: message || '' });
    }

    // ──────────────────────────────────────────────────────────────────────
    // Flash notification from CI3 session flashdata
    // Reads from <meta name="jbm-flash-*"> tags set by layout.
    // ──────────────────────────────────────────────────────────────────────

    function showFlash() {
        const type = $('meta[name="jbm-flash-type"]').attr('content');
        const message = $('meta[name="jbm-flash-message"]').attr('content');
        if (type && message) {
            const iconMap = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
            Swal.fire({ icon: iconMap[type] || 'info', title: message, timer: 3500, showConfirmButton: false, timerProgressBar: true });
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Form helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Serialize a jQuery form into a plain object (handles checkboxes correctly).
     * @param {jQuery} $form
     * @returns {Object}
     */
    function serializeForm($form) {
        const obj = {};
        $form.serializeArray().forEach(function (item) {
            if (obj.hasOwnProperty(item.name)) {
                if (!Array.isArray(obj[item.name])) obj[item.name] = [obj[item.name]];
                obj[item.name].push(item.value);
            } else {
                obj[item.name] = item.value;
            }
        });
        return obj;
    }

    /**
     * Submit a form via AJAX (JSON), showing loader and handling errors.
     * @param {jQuery}   $form
     * @param {string}   url
     * @param {Function} [onSuccess]  Called with response JSON on success
     * @param {Object}   [extraData]  Extra fields to merge into POST body
     */
    function ajaxSubmit($form, url, onSuccess, extraData) {
        const data = Object.assign(serializeForm($form), extraData || {});
        loading(true);
        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            dataType: 'json',
        }).done(function (resp) {
            loading(false);
            if (resp.success) {
                if (typeof onSuccess === 'function') onSuccess(resp);
            } else {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: resp.message || 'Terjadi kesalahan.' });
            }
        }).fail(function () {
            loading(false);
            // ajaxError handler will show the toast
        });
    }

    // ──────────────────────────────────────────────────────────────────────
    // Number / currency formatting
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Format a number as Indonesian Rupiah.
     * @param {number} num
     * @returns {string}  e.g. "Rp 150.000"
     */
    function rupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    // ──────────────────────────────────────────────────────────────────────
    // Init
    // ──────────────────────────────────────────────────────────────────────

    $(function () {
        showFlash();

        // Auto-inject CSRF into all serialized form POSTs
        $(document).on('submit', 'form[data-ajax]', function (e) {
            e.preventDefault();
            const $form = $(this);
            const url = $form.attr('action') || window.location.href;
            ajaxSubmit($form, url, function (resp) {
                toast('success', resp.message || 'Berhasil!');
                if (resp.redirect) {
                    setTimeout(function () { window.location.href = resp.redirect; }, 900);
                }
            });
        });

        // Global confirm-delete buttons  [data-confirm="Yakin hapus?"]
        $(document).on('click', '[data-confirm]', function (e) {
            e.preventDefault();
            const $el = $(this);
            const msg = $el.data('confirm') || 'Yakin melanjutkan?';
            const href = $el.attr('href') || $el.data('href');
            confirm('Konfirmasi', msg, function () {
                if (href) window.location.href = href;
            });
        });
    });

    // ──────────────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────────────

    return {
        confirm: confirm,
        toast: toast,
        loading: loading,
        ajaxSubmit: ajaxSubmit,
        serializeForm: serializeForm,
        syncCsrf: syncCsrf,
        getCsrfHash: getCsrfHash,
        rupiah: rupiah,
    };

}(jQuery));
