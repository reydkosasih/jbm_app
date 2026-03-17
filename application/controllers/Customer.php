<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends Customer_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Booking_model', 'Vehicle_model', 'Service_model', 'Slot_model', 'Payment_model', 'User_model']);
    }

    // ─── Dashboard ────────────────────────────────────────────────────────

    public function index()
    {
        $user = $this->get_user();
        $data = [
            'title'          => 'Dashboard',
            'user'           => $user,
            'active_bookings' => $this->Booking_model->get_by_user($user->id, ['status' => 'in_progress']),
            'pending'        => $this->Booking_model->count_by_status('pending'),
            'recent'         => array_slice($this->Booking_model->get_by_user($user->id), 0, 5),
            'vehicles'       => $this->Vehicle_model->get_by_user($user->id),
        ];
        $this->render('customer/dashboard', $data, 'customer');
    }

    // ─── Booking ──────────────────────────────────────────────────────────

    public function booking()
    {
        $user    = $this->get_user();
        $vehicles = $this->Vehicle_model->get_by_user($user->id);
        $services = $this->Service_model->get_active();

        $data = [
            'title'    => 'Booking Servis Baru',
            'vehicles' => $vehicles,
            'services' => $services,
        ];
        $this->render('customer/booking', $data, 'customer');
    }

    public function store_booking()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $user = $this->get_user();

        // Validate vehicle ownership
        $vehicle_id = (int)$this->input->post('vehicle_id');
        $vehicle    = $this->Vehicle_model->get_by_id($vehicle_id, $user->id);
        if (!$vehicle) return $this->json_response(['success' => false, 'message' => 'Kendaraan tidak ditemukan.'], 422);

        // Validate service
        $service_id = (int)$this->input->post('service_id');
        $service    = $this->Service_model->get_by_id($service_id);
        if (!$service || empty($service['is_active'])) return $this->json_response(['success' => false, 'message' => 'Layanan tidak tersedia.'], 422);

        // Validate date (not past, not Sundays)
        $booking_date = $this->input->post('booking_date');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $booking_date) || strtotime($booking_date) < strtotime(date('Y-m-d'))) {
            return $this->json_response(['success' => false, 'message' => 'Tanggal tidak valid.'], 422);
        }
        if (date('N', strtotime($booking_date)) == 7) {
            return $this->json_response(['success' => false, 'message' => 'Bengkel tutup setiap hari Minggu.'], 422);
        }

        // Validate slot
        $slot_id = (int)$this->input->post('slot_id');
        if (!$this->Slot_model->is_available($slot_id, $booking_date)) {
            return $this->json_response(['success' => false, 'message' => 'Slot waktu sudah penuh. Pilih slot lain.'], 422);
        }

        // Check duplicate booking (same user, date, slot)
        $dup = $this->db->where('user_id', $user->id)
            ->where('booking_date', $booking_date)
            ->where('slot_id', $slot_id)
            ->where_not_in('status', ['cancelled'])
            ->count_all_results('bookings');
        if ($dup > 0) {
            return $this->json_response(['success' => false, 'message' => 'Anda sudah memiliki booking di slot waktu yang sama.'], 422);
        }

        $notes = strip_tags($this->input->post('notes') ?? '');

        $id = $this->Booking_model->create([
            'user_id'      => $user->id,
            'vehicle_id'   => $vehicle_id,
            'service_id'   => $service_id,
            'slot_id'      => $slot_id,
            'booking_date' => $booking_date,
            'notes'        => $notes,
        ]);

        // Create notification for the customer
        $this->_create_notification(
            $user->id,
            'Booking Berhasil Dibuat',
            'Booking Anda untuk layanan ' . $service['name'] . ' pada ' . date('d/m/Y', strtotime($booking_date)) . ' sedang menunggu konfirmasi.',
            'booking',
            $id
        );

        $booking = $this->Booking_model->get_by_id($id);
        return $this->json_response([
            'success'      => true,
            'message'      => 'Booking berhasil dibuat! Kode booking: ' . $booking->booking_code,
            'redirect'     => base_url('my/booking/' . $id),
            'booking_code' => $booking->booking_code,
        ]);
    }

    public function booking_detail($id)
    {
        $user    = $this->get_user();
        $booking = $this->Booking_model->get_by_id($id, $user->id);
        if (!$booking) show_404();

        $logs = $this->Booking_model->get_status_logs($id);

        // Service orders & parts
        $service_orders = $this->db->select('so.*, sp.name AS part_name')
            ->from('service_orders so')
            ->join('spare_parts sp', 'sp.id = so.spare_part_id', 'left')
            ->where('so.booking_id', $id)
            ->get()->result();

        // Payment info
        $payment = $this->db->where('booking_id', $id)->get('payments')->row();

        $data = [
            'title'          => 'Detail Booking — ' . $booking->booking_code,
            'booking'        => $booking,
            'logs'           => $logs,
            'service_orders' => $service_orders,
            'payment'        => $payment,
        ];
        $this->render('customer/booking_detail', $data, 'customer');
    }

    public function cancel_booking($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $user   = $this->get_user();
        $result = $this->Booking_model->cancel($id, $user->id);

        if (!$result) {
            return $this->json_response(['success' => false, 'message' => 'Booking tidak dapat dibatalkan.'], 422);
        }
        return $this->json_response(['success' => true, 'message' => 'Booking berhasil dibatalkan.']);
    }

    // ─── Status tracker ───────────────────────────────────────────────────

    public function service_status()
    {
        $user = $this->get_user();
        $bookings = $this->Booking_model->get_by_user($user->id, ['status' => 'in_progress']);
        $data = [
            'title'    => 'Status Servis',
            'bookings' => $bookings,
        ];
        $this->render('customer/service_status', $data, 'customer');
    }

    // ─── History ──────────────────────────────────────────────────────────

    public function history()
    {
        $user   = $this->get_user();
        $status = $this->input->get('status');
        $bookings = $this->Booking_model->get_by_user($user->id, $status ? ['status' => $status] : []);

        $data = [
            'title'    => 'Riwayat Servis',
            'bookings' => $bookings,
            'status'   => $status,
        ];
        $this->render('customer/history', $data, 'customer');
    }

    // ─── Payment ──────────────────────────────────────────────────────────

    public function payment($id)
    {
        $user    = $this->get_user();
        $booking = $this->Booking_model->get_by_id($id, $user->id);
        if (!$booking) show_404();

        if (!in_array($booking->status, ['in_progress', 'completed'])) {
            $this->set_notification('warning', 'Belum ada tagihan untuk booking ini.');
            redirect('my/booking/' . $id);
        }

        $payment = $this->db->where('booking_id', $id)->get('payments')->row();
        $items   = $payment ? $this->db->where('payment_id', $payment->id)->get('payment_items')->result() : [];

        $data = [
            'title'   => 'Pembayaran — ' . $booking->booking_code,
            'booking' => $booking,
            'payment' => $payment,
            'items'   => $items,
        ];
        $this->render('customer/payment', $data, 'customer');
    }

    public function upload_proof($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $user    = $this->get_user();
        $booking = $this->Booking_model->get_by_id($id, $user->id);
        if (!$booking) return $this->json_response(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);

        $payment = $this->db->where('booking_id', $id)->get('payments')->row();
        if (!$payment) return $this->json_response(['success' => false, 'message' => 'Tagihan belum dibuat.'], 422);
        if ($payment->method !== 'transfer') {
            return $this->json_response(['success' => false, 'message' => 'Hanya untuk pembayaran transfer.'], 422);
        }

        // File upload
        $upload_path = FCPATH . 'uploads/payment_proofs/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

        $this->upload->initialize([
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|pdf',
            'max_size'      => 2048,
            'encrypt_name'  => true,
        ]);

        if (!$this->upload->do_upload('proof_file')) {
            return $this->json_response(['success' => false, 'message' => $this->upload->display_errors('', '')], 422);
        }

        $file = $this->upload->data('file_name');
        $this->db->where('id', $payment->id)->update('payments', [
            'proof_file'  => $file,
            'status'      => 'waiting_confirmation',
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->json_response(['success' => true, 'message' => 'Bukti transfer berhasil diupload. Menunggu konfirmasi admin.']);
    }

    // ─── Vehicles ─────────────────────────────────────────────────────────

    public function vehicles()
    {
        $user     = $this->get_user();
        $vehicles = $this->Vehicle_model->get_by_user($user->id);
        $data = [
            'title'    => 'Kendaraan Saya',
            'vehicles' => $vehicles,
        ];
        $this->render('customer/vehicles', $data, 'customer');
    }

    public function store_vehicle()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $user = $this->get_user();

        $this->form_validation->set_rules('plate_number', 'Plat Nomor', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('brand',        'Merek',      'required|trim|max_length[50]');
        $this->form_validation->set_rules('model',        'Model',      'required|trim|max_length[50]');
        $this->form_validation->set_rules('year',         'Tahun',      'required|integer|greater_than[1990]');
        $this->form_validation->set_rules('color',        'Warna',      'trim|max_length[30]');

        if (!$this->form_validation->run()) {
            return $this->json_response(['success' => false, 'message' => validation_errors('', ' | ')], 422);
        }

        // Check duplicate plate for this user
        $plate = strtoupper(preg_replace('/\s+/', '', $this->input->post('plate_number')));
        $dup   = $this->db->where('user_id', $user->id)->where('plate_number', $plate)->where('is_deleted', 0)
            ->count_all_results('vehicles');
        if ($dup > 0) return $this->json_response(['success' => false, 'message' => 'Plat nomor sudah terdaftar.'], 422);

        $id = $this->Vehicle_model->create([
            'user_id'      => $user->id,
            'plate_number' => $plate,
            'brand'        => $this->input->post('brand'),
            'model'        => $this->input->post('model'),
            'year'         => (int)$this->input->post('year'),
            'color'        => $this->input->post('color') ?? '',
            'engine_no'    => $this->input->post('engine_no') ?? '',
            'chassis_no'   => $this->input->post('chassis_no') ?? '',
        ]);

        return $this->json_response(['success' => true, 'message' => 'Kendaraan berhasil ditambahkan.', 'id' => $id]);
    }

    public function edit_vehicle($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $user    = $this->get_user();
        $vehicle = $this->Vehicle_model->get_by_id($id, $user->id);
        if (!$vehicle) return $this->json_response(['success' => false, 'message' => 'Kendaraan tidak ditemukan.'], 404);

        $this->form_validation->set_rules('brand', 'Merek',  'required|trim|max_length[50]');
        $this->form_validation->set_rules('model', 'Model',  'required|trim|max_length[50]');
        $this->form_validation->set_rules('year',  'Tahun',  'required|integer|greater_than[1990]');

        if (!$this->form_validation->run()) {
            return $this->json_response(['success' => false, 'message' => validation_errors('', ' | ')], 422);
        }

        $this->Vehicle_model->update($id, $user->id, [
            'brand'      => $this->input->post('brand'),
            'model'      => $this->input->post('model'),
            'year'       => (int)$this->input->post('year'),
            'color'      => $this->input->post('color') ?? '',
            'engine_no'  => $this->input->post('engine_no') ?? '',
            'chassis_no' => $this->input->post('chassis_no') ?? '',
        ]);

        return $this->json_response(['success' => true, 'message' => 'Kendaraan berhasil diperbarui.']);
    }

    public function delete_vehicle($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $user   = $this->get_user();
        $result = $this->Vehicle_model->delete($id, $user->id);

        if (!$result) {
            return $this->json_response(['success' => false, 'message' => 'Kendaraan tidak dapat dihapus karena masih ada booking aktif.'], 422);
        }
        return $this->json_response(['success' => true, 'message' => 'Kendaraan berhasil dihapus.']);
    }

    // ─── Profile ──────────────────────────────────────────────────────────

    public function profile()
    {
        $user = $this->get_user();
        $data = [
            'title' => 'Profil Saya',
            'user'  => $user,
        ];
        $this->render('customer/profile', $data, 'customer');
    }

    public function update_profile()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $user = $this->get_user();

        $this->form_validation->set_rules('name',  'Nama', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('phone', 'No. HP', 'trim|max_length[20]');

        if (!$this->form_validation->run()) {
            return $this->json_response(['success' => false, 'message' => validation_errors('', ' | ')], 422);
        }

        $update = [
            'name'       => $this->input->post('name'),
            'phone'      => $this->input->post('phone'),
            'address'    => strip_tags($this->input->post('address') ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $upload_path = FCPATH . 'uploads/avatars/';
            if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

            $this->upload->initialize([
                'upload_path'   => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|gif|webp',
                'max_size'      => 1024,
                'max_width'     => 800,
                'max_height'    => 800,
                'encrypt_name'  => true,
            ]);

            if ($this->upload->do_upload('avatar')) {
                // Remove old avatar
                if (!empty($user->avatar) && file_exists($upload_path . $user->avatar)) {
                    unlink($upload_path . $user->avatar);
                }
                $update['avatar'] = $this->upload->data('file_name');
            }
        }

        $this->load->model('User_model');
        $this->User_model->update($user->id, $update);

        // Refresh session
        $fresh = $this->User_model->get_by_id($user->id);
        $this->session->set_userdata([
            'user_name'   => $fresh['name'] ?? $this->session->userdata('user_name'),
            'user_avatar' => $fresh['avatar'] ?? '',
        ]);

        return $this->json_response(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
    }

    public function change_password()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $user = $this->get_user();
        $db_user = $this->User_model->get_by_id($user->id);

        $current  = $this->input->post('current_password');
        $new_pass = $this->input->post('new_password');
        $confirm  = $this->input->post('confirm_password');

        if (!$db_user || !password_verify($current, $db_user['password'])) {
            return $this->json_response(['success' => false, 'message' => 'Password saat ini tidak sesuai.'], 422);
        }
        if (strlen($new_pass) < 8) {
            return $this->json_response(['success' => false, 'message' => 'Password baru minimal 8 karakter.'], 422);
        }
        if ($new_pass !== $confirm) {
            return $this->json_response(['success' => false, 'message' => 'Konfirmasi password tidak cocok.'], 422);
        }

        $this->User_model->update_password($user->id, password_hash($new_pass, PASSWORD_BCRYPT, ['cost' => 12]));
        return $this->json_response(['success' => true, 'message' => 'Password berhasil diubah.']);
    }

    // ─── Private helpers ─────────────────────────────────────────────────

    private function _create_notification($user_id, $title, $message, $type, $ref_id = null)
    {
        $this->db->insert('notifications', [
            'user_id'    => $user_id,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'ref_id'     => $ref_id,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
