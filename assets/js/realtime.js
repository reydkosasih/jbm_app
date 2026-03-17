/**
 * realtime.js — Polling-based real-time status updater for JBM Bengkel
 * Responsible for: booking status updates, service step progress display
 */

var RealTime = (function () {
    'use strict';

    var BASE_URL = window.BASE_URL || '/';

    var STATUS_CONFIG = {
        pending: { color: 'warning', label: 'Menunggu Konfirmasi', icon: 'fa-clock', step: 0 },
        confirmed: { color: 'primary', label: 'Dikonfirmasi', icon: 'fa-calendar-check', step: 1 },
        in_progress: { color: 'info', label: 'Sedang Diservis', icon: 'fa-wrench', step: 2 },
        waiting_payment: { color: 'warning', label: 'Menunggu Pembayaran', icon: 'fa-money-bill', step: 3 },
        waiting_confirmation: { color: 'warning', label: 'Konfirmasi Pembayaran', icon: 'fa-hourglass-half', step: 3 },
        completed: { color: 'success', label: 'Selesai', icon: 'fa-circle-check', step: 4 },
        cancelled: { color: 'danger', label: 'Dibatalkan', icon: 'fa-ban', step: -1 },
    };

    var STEP_LABELS = ['Booking', 'Konfirmasi', 'Diservis', 'Pembayaran', 'Selesai'];
    var STEP_ICONS = ['fa-calendar', 'fa-calendar-check', 'fa-wrench', 'fa-money-bill', 'fa-circle-check'];

    // localStorage key for last-known status (per booking code)
    function _storageKey(code) {
        return 'jbm_status_' + code;
    }

    function _getLastStatus(code) {
        try { return localStorage.getItem(_storageKey(code)); } catch (e) { return null; }
    }

    function _saveStatus(code, status) {
        try { localStorage.setItem(_storageKey(code), status); } catch (e) { }
    }

    /**
     * Poll booking status via API and update the card on the status page.
     * @param {string} code - Booking code (e.g. JBM-202401ABC)
     * @param {Element} card - DOM element of the status card
     */
    function checkStatus(code, card) {
        if (!code) return;

        $.get(BASE_URL + 'api/booking-status/' + code, function (res) {
            if (!res.success || !res.booking) return;

            var status = res.booking.status;
            var cfg = STATUS_CONFIG[status] || { color: 'secondary', label: status, icon: 'fa-circle', step: -1 };
            var lastStatus = _getLastStatus(code);

            // Notify if status changed
            if (lastStatus && lastStatus !== status) {
                _notify(res.booking, cfg);
            }
            _saveStatus(code, status);

            if (!card) return;

            // Update badge
            var badge = card.querySelector('.status-badge');
            if (badge) {
                badge.className = 'badge badge-light-' + cfg.color + ' fs-6 px-4 py-3 status-badge';
                badge.innerHTML = '<i class="fa-solid ' + cfg.icon + ' me-2"></i>' + cfg.label;
            }

            // Update last-update time
            var ts = card.querySelector('.last-update');
            if (ts) ts.textContent = _timeAgo(new Date());

            // Update progress steps
            _updateSteps(card, cfg.step);

            // If completed, show celebration toast
            if (status === 'completed' && lastStatus !== 'completed') {
                _celebrateCompleted(res.booking);
            }

            // If status is terminal (completed/cancelled), redirect to detail after a moment
            if (status === 'completed' || status === 'cancelled') {
                setTimeout(function () {
                    window.location.href = BASE_URL + 'my/history';
                }, 5000);
            }

        }).fail(function () {
            console.warn('[RealTime] Failed to fetch status for ' + code);
        });
    }

    function _updateSteps(card, currentStep) {
        var steps = card.querySelectorAll('.status-steps > div:not([style*="height"])');
        steps.forEach(function (el, i) {
            var circle = el.querySelector('div');
            if (!circle) return;

            circle.className = circle.className.replace(/bg-\w+|text-\w+|pulse/g, '').trim();

            if (i < currentStep) {
                circle.classList.add('bg-success', 'text-white');
            } else if (i === currentStep) {
                circle.classList.add('bg-info', 'text-white', 'pulse');
            } else {
                circle.classList.add('bg-light', 'text-muted');
            }

            var label = el.querySelector('span');
            if (label) {
                label.className = 'fs-9 text-center text-' + (i <= currentStep ? 'gray-800 fw-semibold' : 'muted');
            }
        });
    }

    function _notify(booking, cfg) {
        if (typeof Swal === 'undefined') return;
        Swal.mixin({
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 5000, timerProgressBar: true,
        }).fire({
            icon: cfg.color === 'success' ? 'success' : (cfg.color === 'danger' ? 'error' : 'info'),
            title: 'Status Diperbarui',
            text: booking.plate_number + ': ' + cfg.label,
        });
    }

    function _celebrateCompleted(booking) {
        if (typeof Swal === 'undefined') return;
        Swal.fire({
            icon: 'success',
            title: 'Servis Selesai!',
            html: 'Kendaraan <strong>' + booking.plate_number + '</strong> Anda sudah selesai diservis. Silakan lakukan pembayaran.',
            confirmButtonText: 'Lihat Detail',
        }).then(function () {
            window.location.href = BASE_URL + 'my/history?status=completed';
        });
    }

    function _timeAgo(date) {
        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    // Public API
    return {
        checkStatus: checkStatus,
        STATUS_CONFIG: STATUS_CONFIG,
    };

})();
