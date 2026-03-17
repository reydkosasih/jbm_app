<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Api Controller — JSON endpoints for AJAX polling, slot availability, etc.
 * All methods return JSON; no view rendering.
 */
class Api extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    // ─── Notifications ────────────────────────────────────────────────────

    /**
     * GET api/notifications
     * Returns unread notification count + last 10 unread notifications for the logged-in user.
     */
    public function notifications()
    {
        if (!$this->is_logged_in()) {
            return $this->json_response(['unread_count' => 0, 'notifications' => []]);
        }
        $user_id = $this->session->userdata('user_id');

        $items = $this->db->where('user_id', $user_id)
            ->where('is_read', 0)
            ->order_by('created_at', 'DESC')
            ->limit(10)
            ->get('notifications')->result();

        $count = $this->db->where('user_id', $user_id)->where('is_read', 0)->count_all_results('notifications');

        return $this->json_response(['unread_count' => $count, 'notifications' => $items]);
    }

    /**
     * POST api/notifications/read/{id}
     */
    public function read_notification($id)
    {
        if (!$this->is_logged_in()) return $this->json_response(['success' => false], 401);
        $this->_verify_csrf_header();

        $user_id = $this->session->userdata('user_id');
        $this->db->where('id', $id)->where('user_id', $user_id)->update('notifications', ['is_read' => 1]);
        return $this->json_response(['success' => true]);
    }

    /**
     * POST api/notifications/read-all
     */
    public function read_all_notifications()
    {
        if (!$this->is_logged_in()) return $this->json_response(['success' => false], 401);
        $this->_verify_csrf_header();

        $user_id = $this->session->userdata('user_id');
        $this->db->where('user_id', $user_id)->where('is_read', 0)->update('notifications', ['is_read' => 1]);
        return $this->json_response(['success' => true]);
    }

    // ─── Slot availability ────────────────────────────────────────────────

    /**
     * GET api/slots?date=YYYY-MM-DD  OR  api/booking/check-slots?date=...
     * Returns available time slots for a given date.
     */
    public function slots()
    {
        $date = $this->input->get('date');
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->json_response(['success' => false, 'message' => 'Tanggal tidak valid.'], 422);
        }
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            return $this->json_response(['success' => false, 'message' => 'Tidak dapat booking tanggal lalu.'], 422);
        }
        if (date('N', strtotime($date)) == 7) {
            return $this->json_response(['success' => false, 'message' => 'Bengkel tutup setiap hari Minggu.', 'slots' => []]);
        }

        $this->load->model('Slot_model');
        $slots = $this->Slot_model->get_with_availability($date);
        return $this->json_response(['success' => true, 'slots' => $slots]);
    }

    /** Alias: GET api/booking/check-slots  */
    public function check_slots()
    {
        return $this->slots();
    }
    // ─── Booking status (public tracker) ─────────────────────────────────

    /**
     * GET api/booking-status/{code}
     * Public endpoint — returns booking status by booking code.
     */
    public function booking_status($code = '')
    {
        $code = strtoupper(preg_replace('/[^A-Z0-9\-]/', '', $code));
        if (empty($code)) return $this->json_response(['success' => false, 'message' => 'Kode booking diperlukan.'], 422);

        $this->load->model('Booking_model');
        $booking = $this->Booking_model->get_by_code($code);
        if (!$booking) return $this->json_response(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);

        $logs = $this->Booking_model->get_status_logs($booking->id);

        return $this->json_response([
            'success' => true,
            'booking' => [
                'code'         => $booking->booking_code,
                'status'       => $booking->status,
                'service_name' => $booking->service_name,
                'plate_number' => $booking->plate_number,
                'booking_date' => $booking->booking_date,
                'slot_label'   => $booking->slot_label,
            ],
            'logs' => $logs,
        ]);
    }

    // ─── Low stock alert ─────────────────────────────────────────────────

    /**
     * GET api/low-stock   (admin only)
     */
    public function low_stock()
    {
        if (!$this->is_logged_in()) return $this->json_response(['success' => false], 401);
        $role = $this->session->userdata('user_role');
        if (!in_array($role, ['admin', 'kasir', 'mekanik'])) return $this->json_response(['success' => false], 403);

        $parts = $this->db->select('id, name, stock, min_stock, unit')
            ->where('stock <= min_stock')
            ->where('is_active', 1)
            ->order_by('stock', 'ASC')
            ->get('spare_parts')->result();

        return $this->json_response(['success' => true, 'count' => count($parts), 'parts' => $parts]);
    }

    // ─── Services dropdown ────────────────────────────────────────────────

    /**
     * GET api/services  — returns active services (for booking wizard step 2)
     */
    public function services()
    {
        $this->load->model('Service_model');
        $services = $this->Service_model->get_active();
        return $this->json_response(['success' => true, 'services' => $services]);
    }

    // ─── Settings (public, safe subset) ──────────────────────────────────

    /**
     * GET api/settings
     */
    public function settings()
    {
        $allowed = ['workshop_name', 'workshop_address', 'workshop_phone', 'workshop_email', 'workshop_hours'];
        $rows    = $this->db->where_in('key', $allowed)->get('settings')->result();
        $out     = [];
        foreach ($rows as $r) $out[$r->key] = $r->value;
        return $this->json_response(['success' => true, 'settings' => $out]);
    }

    // ─── Invoice download ─────────────────────────────────────────────────

    /**
     * GET api/invoice/{id}  — redirect to admin invoice pdf for logged-in users
     */
    public function download_invoice($id)
    {
        if (!$this->is_logged_in()) {
            redirect('login');
            return;
        }
        $this->load->model('Payment_model');
        $payment = $this->Payment_model->get_by_id($id);
        if (!$payment) show_404();

        $role    = $this->session->userdata('user_role');
        $user_id = $this->session->userdata('user_id');

        // Customers can only download their own invoices
        if ($role === 'customer') {
            $this->load->model('Booking_model');
            $booking = $this->Booking_model->get_by_id($payment->booking_id, $user_id);
            if (!$booking) show_404();
        }

        redirect(site_url('admin/invoice/' . $id . '/pdf'));
    }
}
