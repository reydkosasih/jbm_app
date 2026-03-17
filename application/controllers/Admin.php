<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'Booking_model',
            'Vehicle_model',
            'Service_model',
            'Slot_model',
            'Payment_model',
            'User_model',
            'Spare_part_model'
        ]);
    }

    // ─── Dashboard ────────────────────────────────────────────────────────

    public function index()
    {
        $today = date('Y-m-d');
        $data  = [
            'title'            => 'Dashboard Admin',
            'today_bookings'   => $this->Booking_model->count_by_status('confirmed', $today)
                + $this->Booking_model->count_by_status('in_progress', $today),
            'pending_count'    => $this->Booking_model->count_by_status('pending'),
            'revenue_today'    => $this->Payment_model->total_revenue($today, $today),
            'revenue_month'    => $this->Payment_model->total_revenue(date('Y-m-01'), date('Y-m-d')),
            'pending_payments' => $this->db->where('status', 'waiting_confirmation')->count_all_results('payments'),
            'low_stock_count'  => $this->db->where('stock <= min_stock')->where('is_active', 1)->count_all_results('spare_parts'),
            'today_queue'      => $this->Booking_model->today_queue(),
            'monthly_revenue'  => $this->Payment_model->monthly_revenue(date('Y')),
            'monthly_bookings' => $this->Booking_model->get_monthly_stats(date('Y')),
            'total_customers'  => $this->User_model->count_by_role('customer'),
        ];
        $this->render('admin/dashboard', $data, 'admin');
    }

    // ─── Queue / Bookings Management ─────────────────────────────────────

    public function queue()
    {
        $data = [
            'title'   => 'Antrean Hari Ini',
            'queue'   => $this->Booking_model->today_queue(),
            'date'    => date('Y-m-d'),
        ];
        $this->render('admin/queue', $data, 'admin');
    }

    public function bookings()
    {
        $filters = [
            'status'  => $this->input->get('status'),
            'date'    => $this->input->get('date'),
            'search'  => $this->input->get('q'),
        ];
        $data = [
            'title'    => 'Semua Booking',
            'bookings' => $this->Booking_model->get_all_with_filter($filters),
            'filters'  => $filters,
        ];
        $this->render('admin/bookings', $data, 'admin');
    }

    public function booking_detail($id)
    {
        $booking = $this->Booking_model->get_by_id($id);
        if (!$booking) show_404();

        $logs           = $this->Booking_model->get_status_logs($id);
        $service_orders = $this->db->select('so.*, sp.name AS part_name, sp.unit')
            ->from('service_orders so')
            ->join('spare_parts sp', 'sp.id = so.spare_part_id', 'left')
            ->where('so.booking_id', $id)
            ->get()->result();
        $payment    = $this->Payment_model->get_by_booking($id);
        $mechanics  = $this->User_model->get_all(['role' => 'mekanik']);
        $spare_parts = $this->Spare_part_model->get_active();

        $data = [
            'title'          => 'Detail Booking — ' . $booking->booking_code,
            'booking'        => $booking,
            'status_logs'    => $logs,
            'service_orders' => $service_orders,
            'payment'        => $payment,
            'mechanics'      => $mechanics,
            'spare_parts'    => $spare_parts,
        ];
        $this->render('admin/booking_detail', $data, 'admin');
    }

    public function update_booking_status($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $booking    = $this->Booking_model->get_by_id($id);
        if (!$booking) return $this->json_response(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);

        $new_status  = $this->input->post('status');
        $note        = strip_tags($this->input->post('note') ?? '');
        $mechanic_id = $this->input->post('mechanic_id') ? (int)$this->input->post('mechanic_id') : null;

        $allowed_transitions = [
            'pending'         => ['confirmed', 'cancelled'],
            'confirmed'       => ['in_progress', 'cancelled'],
            'in_progress'     => ['waiting_payment'],
            'waiting_payment' => ['completed'],
        ];

        if (!in_array($new_status, $allowed_transitions[$booking->status] ?? [])) {
            return $this->json_response(['success' => false, 'message' => 'Transisi status tidak valid.'], 422);
        }

        $update = ['status' => $new_status, 'updated_at' => date('Y-m-d H:i:s')];
        if ($mechanic_id) $update['mechanic_id'] = $mechanic_id;

        $this->db->where('id', $id)->update('bookings', $update);
        $this->Booking_model->update_status($id, $new_status, $booking->status, $note, $this->session->userdata('user_id'));

        // Notify customer
        $this->_notify_customer($booking->user_id, $booking->booking_code, $new_status);

        return $this->json_response(['success' => true, 'message' => 'Status booking diperbarui.']);
    }

    public function add_mechanic_note($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $note = strip_tags((string) ($this->input->post('mechanic_notes') ?? $this->input->post('note') ?? ''));
        $this->db->where('id', $id)->update('bookings', ['mechanic_notes' => $note, 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->json_response(['success' => true, 'message' => 'Catatan mekanik disimpan.']);
    }

    // ─── Service Orders (parts & labor) ───────────────────────────────────

    public function add_service_order($booking_id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $type        = $this->input->post('type');
        $type        = $type === 'sparepart' ? 'parts' : $type; // normalize payload from admin view
        $description = strip_tags((string) $this->input->post('description'));
        $quantity    = max(1, (int)($this->input->post('quantity') ?? $this->input->post('qty')));
        $unit_price  = (float)$this->input->post('unit_price');

        if ($type === 'parts') {
            $part_id = (int)$this->input->post('spare_part_id');
            $part    = $this->Spare_part_model->get_by_id($part_id);
            if (!$part || $part->stock < $quantity) {
                return $this->json_response(['success' => false, 'message' => 'Stok tidak mencukupi.'], 422);
            }
            // Deduct stock
            $this->Spare_part_model->deduct_stock($part_id, $quantity, $booking_id);
            $description = $description ?: $part->name;
            $unit_price  = $unit_price  ?: $part->selling_price;
        }

        $this->db->insert('service_orders', [
            'booking_id'    => $booking_id,
            'spare_part_id' => ($type === 'parts') ? $part_id : null,
            'type'          => $type,
            'description'   => $description,
            'quantity'      => $quantity,
            'unit_price'    => $unit_price,
            'unit'          => $this->input->post('unit') ?? 'pcs',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return $this->json_response(['success' => true, 'message' => 'Item ditambahkan.']);
    }

    public function delete_service_order($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $so = $this->db->where('id', $id)->get('service_orders')->row();
        if (!$so) return $this->json_response(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);

        // Return stock if it was a parts deduction
        if ($so->type === 'parts' && $so->spare_part_id) {
            $this->Spare_part_model->add_stock($so->spare_part_id, $so->quantity, 'return', 'Item dibatalkan dari booking.');
        }

        $this->db->where('id', $id)->delete('service_orders');
        return $this->json_response(['success' => true, 'message' => 'Item dihapus.']);
    }

    public function generate_invoice($booking_id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $booking = $this->Booking_model->get_by_id($booking_id);
        if (!$booking) return $this->json_response(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);

        // Check no duplicate
        $existing = $this->Payment_model->get_by_booking($booking_id);
        if ($existing) return $this->json_response(['success' => false, 'message' => 'Invoice sudah ada.'], 422);

        // Gather service orders
        $orders = $this->db->where('booking_id', $booking_id)->get('service_orders')->result();
        $items  = [];
        foreach ($orders as $o) {
            $items[] = [
                'description' => $o->description,
                'type'        => $o->type,
                'quantity'    => $o->quantity,
                'unit_price'  => $o->unit_price,
            ];
        }

        if (empty($items)) {
            // Use service base price as default
            $items[] = [
                'description' => $booking->service_name,
                'type'        => 'jasa',
                'quantity'    => 1,
                'unit_price'  => $booking->service_price,
            ];
        }

        $payment_id = $this->Payment_model->create($booking_id, $items);

        // Update booking status
        $this->Booking_model->update_status(
            $booking_id,
            'waiting_payment',
            $booking->status,
            'Invoice dibuat oleh admin.',
            $this->session->userdata('user_id')
        );
        $this->db->where('id', $booking_id)->update('bookings', ['status' => 'waiting_payment', 'updated_at' => date('Y-m-d H:i:s')]);

        $this->_notify_customer($booking->user_id, $booking->booking_code, 'waiting_payment');

        return $this->json_response(['success' => true, 'message' => 'Invoice berhasil dibuat.', 'payment_id' => $payment_id]);
    }

    // ─── Payments Management ─────────────────────────────────────────────

    public function payments()
    {
        $filters = [
            'status'    => $this->input->get('status'),
            'date_from' => $this->input->get('date_from'),
            'date_to'   => $this->input->get('date_to'),
            'search'    => $this->input->get('q'),
        ];
        $data = [
            'title'    => 'Manajemen Pembayaran',
            'payments' => $this->Payment_model->get_all($filters),
            'filters'  => $filters,
            'pending_count' => $this->db->where('status', 'waiting_confirmation')->count_all_results('payments'),
        ];
        $this->render('admin/payments', $data, 'admin');
    }

    public function confirm_payment($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $result = $this->Payment_model->confirm($id, $this->session->userdata('user_id'));
        $payment = $this->Payment_model->get_by_id($id);
        if ($payment) {
            $booking = $this->Booking_model->get_by_id($payment->booking_id);
            if ($booking) $this->_notify_customer($booking->user_id, $booking->booking_code, 'completed');
        }
        return $this->json_response(['success' => true, 'message' => 'Pembayaran dikonfirmasi. Servis ditandai selesai.']);
    }

    public function reject_payment($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $note = strip_tags($this->input->post('note') ?? 'Bukti tidak sesuai.');
        $this->Payment_model->reject($id, $note);
        return $this->json_response(['success' => true, 'message' => 'Pembayaran ditolak.']);
    }

    // ─── Spare Parts / Stock ─────────────────────────────────────────────

    public function spare_parts()
    {
        $data = [
            'title'       => 'Manajemen Suku Cadang',
            'parts'       => $this->Spare_part_model->get_all(),
            'low_stock'   => $this->Spare_part_model->get_low_stock(),
        ];
        $this->render('admin/spare_parts', $data, 'admin');
    }

    public function store_part()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $this->form_validation->set_rules('name',          'Nama',     'required|trim|max_length[150]');
        $this->form_validation->set_rules('sku',           'SKU',      'trim|max_length[50]');
        $this->form_validation->set_rules('purchase_price', 'H. Beli',  'required|numeric');
        $this->form_validation->set_rules('selling_price', 'H. Jual',  'required|numeric');
        $this->form_validation->set_rules('stock',         'Stok',     'required|integer');
        $this->form_validation->set_rules('min_stock',     'Min. Stok', 'required|integer');

        if (!$this->form_validation->run()) {
            return $this->json_response(['success' => false, 'message' => validation_errors('', ' | ')], 422);
        }

        $id = $this->Spare_part_model->create([
            'name'           => $this->input->post('name'),
            'sku'            => $this->input->post('sku') ?? '',
            'category'       => $this->input->post('category') ?? '',
            'unit'           => $this->input->post('unit') ?? 'pcs',
            'purchase_price' => (float)$this->input->post('purchase_price'),
            'selling_price'  => (float)$this->input->post('selling_price'),
            'stock'          => (int)$this->input->post('stock'),
            'min_stock'      => (int)$this->input->post('min_stock'),
            'description'    => strip_tags($this->input->post('description') ?? ''),
        ]);

        return $this->json_response(['success' => true, 'message' => 'Suku cadang berhasil ditambahkan.', 'id' => $id]);
    }

    public function update_part($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $part = $this->Spare_part_model->get_by_id($id);
        if (!$part) return $this->json_response(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);

        $this->Spare_part_model->update($id, [
            'name'           => $this->input->post('name'),
            'sku'            => $this->input->post('sku') ?? '',
            'category'       => $this->input->post('category') ?? '',
            'unit'           => $this->input->post('unit') ?? 'pcs',
            'purchase_price' => (float)$this->input->post('purchase_price'),
            'selling_price'  => (float)$this->input->post('selling_price'),
            'min_stock'      => (int)$this->input->post('min_stock'),
            'description'    => strip_tags($this->input->post('description') ?? ''),
            'is_active'      => (int)$this->input->post('is_active'),
        ]);

        return $this->json_response(['success' => true, 'message' => 'Suku cadang berhasil diperbarui.']);
    }

    public function adjust_stock($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $qty  = (int)($this->input->post('quantity') ?? $this->input->post('qty'));
        $type = $this->input->post('mutation_type') ?? $this->input->post('type'); // 'in' | 'out' | 'adjustment'
        $note = strip_tags($this->input->post('note') ?? '');

        if ($qty <= 0) return $this->json_response(['success' => false, 'message' => 'Jumlah harus lebih dari 0.'], 422);

        $part = $this->Spare_part_model->get_by_id($id);
        if (!$part) return $this->json_response(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);

        if ($type === 'out' && $part->stock < $qty) {
            return $this->json_response(['success' => false, 'message' => 'Stok tidak mencukupi.'], 422);
        }

        if ($type === 'in') {
            $this->Spare_part_model->add_stock($id, $qty, 'purchase', $note);
        } elseif ($type === 'out') {
            $this->Spare_part_model->deduct_stock($id, $qty, null, $note);
        } else {
            // Adjustment: set absolute value
            $this->db->where('id', $id)->update('spare_parts', ['stock' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);
            $this->db->insert('stock_mutations', [
                'spare_part_id' => $id,
                'type'          => 'adjustment',
                'quantity'      => $qty - $part->stock,
                'stock_before'  => $part->stock,
                'stock_after'   => $qty,
                'note'          => $note,
                'created_by'    => $this->session->userdata('user_id'),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->json_response(['success' => true, 'message' => 'Stok berhasil diperbarui.']);
    }

    // ─── Users Management ─────────────────────────────────────────────────

    public function users()
    {
        $filters = [
            'role'   => $this->input->get('role'),
            'search' => $this->input->get('q'),
        ];
        $users = array_map(static function ($row) {
            return (object) $row;
        }, $this->User_model->get_all($filters));
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $users,
            'filters' => $filters,
        ];
        $this->render('admin/users', $data, 'admin');
    }

    public function store_user()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $this->form_validation->set_rules('name',     'Nama',     'required|trim|max_length[100]');
        $this->form_validation->set_rules('email',    'Email',    'required|trim|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('role',     'Role',     'required|in_list[admin,kasir,mekanik,customer]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');

        if (!$this->form_validation->run()) {
            return $this->json_response(['success' => false, 'message' => validation_errors('', ' | ')], 422);
        }

        $id = $this->User_model->create([
            'name'     => $this->input->post('name'),
            'email'    => $this->input->post('email'),
            'phone'    => $this->input->post('phone') ?? '',
            'role'     => $this->input->post('role'),
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT, ['cost' => 12]),
            'is_active' => 1,
        ]);

        return $this->json_response(['success' => true, 'message' => 'Pengguna berhasil ditambahkan.', 'id' => $id]);
    }

    public function toggle_user($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        if ($id == $this->session->userdata('user_id')) {
            return $this->json_response(['success' => false, 'message' => 'Tidak dapat menonaktifkan diri sendiri.'], 422);
        }
        $user = $this->User_model->get_by_id($id);
        if (!$user) return $this->json_response(['success' => false, 'message' => 'User tidak ditemukan.'], 404);

        $new_status = !empty($user['is_active']) ? 0 : 1;
        $this->User_model->update($id, ['is_active' => $new_status]);
        return $this->json_response(['success' => true, 'message' => 'Status pengguna diperbarui.', 'is_active' => $new_status]);
    }

    /** AJAX: user detail modal data */
    public function user_detail($id)
    {
        $user = $this->User_model->get_by_id($id);
        if (!$user) return $this->json_response(['success' => false, 'message' => 'User tidak ditemukan.'], 404);

        $bookings = $this->db
            ->select('b.booking_code, b.booking_date, s.name AS service_name, b.status')
            ->from('bookings b')
            ->join('services s', 's.id_service = b.service_id', 'left')
            ->where('b.user_id', $id)
            ->order_by('b.created_at', 'DESC')
            ->limit(10)
            ->get()->result();

        $total_spent = $this->db
            ->select_sum('p.total_amount')
            ->from('payments p')
            ->join('bookings b', 'b.id = p.booking_id')
            ->where('b.user_id', $id)
            ->where('p.status', 'paid')
            ->get()->row_array();

        $vehicles = $this->db->where('user_id', $id)->get('vehicles')->result();

        return $this->json_response([
            'success'     => true,
            'user'        => $user,
            'bookings'    => $bookings,
            'vehicles'    => $vehicles,
            'total_spent' => $total_spent['total_amount'] ?? 0,
        ]);
    }



    public function services()
    {
        $data = [
            'title'    => 'Manajemen Layanan',
            'services' => $this->Service_model->get_all(),
        ];
        $this->render('admin/services', $data, 'admin');
    }

    public function store_service()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $this->form_validation->set_rules('name',     'Nama Layanan', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('base_price',   'Harga',  'required|numeric');
        $this->form_validation->set_rules('duration_min', 'Durasi', 'required|integer');

        if (!$this->form_validation->run()) {
            return $this->json_response(['success' => false, 'message' => validation_errors('', ' | ')], 422);
        }

        // Handle icon upload
        $icon = '';
        if (!empty($_FILES['icon']['name'])) {
            $this->upload->initialize([
                'upload_path'   => FCPATH . 'uploads/services/',
                'allowed_types' => 'jpg|jpeg|png|svg|webp',
                'max_size'      => 500,
                'encrypt_name'  => true,
            ]);
            if ($this->upload->do_upload('icon')) {
                $icon = $this->upload->data('file_name');
            }
        }

        $id = $this->Service_model->create([
            'name'        => $this->input->post('name'),
            'description' => strip_tags($this->input->post('description') ?? ''),
            'base_price'  => (float)$this->input->post('base_price'),
            'duration_min' => (int)$this->input->post('duration_min'),
            'icon'        => $icon,
            'is_active'   => 1,
        ]);

        return $this->json_response(['success' => true, 'message' => 'Layanan berhasil ditambahkan.', 'id' => $id]);
    }

    public function update_service($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $this->Service_model->update($id, [
            'name'        => $this->input->post('name'),
            'description' => strip_tags($this->input->post('description') ?? ''),
            'base_price'  => (float)$this->input->post('base_price'),
            'duration_min' => (int)$this->input->post('duration_min'),
            'is_active'   => (int)$this->input->post('is_active'),
        ]);

        return $this->json_response(['success' => true, 'message' => 'Layanan berhasil diperbarui.']);
    }

    // ─── Stock aliases (route: admin/stock/*) ─────────────────────────────

    /** Alias so route admin/stock maps here */
    public function stock()
    {
        return $this->spare_parts();
    }

    /** Alias so route admin/queue/update-status maps here */
    public function update_status()
    {
        $id = (int)$this->input->post('booking_id');
        return $this->update_booking_status($id);
    }

    /** Alias so route admin/settings/update maps here */
    public function update_settings()
    {
        return $this->save_settings();
    }

    /** Spare part detail (for stock modal/page) */
    public function stock_detail($id)
    {
        $part = $this->Spare_part_model->get_by_id($id);
        if (!$part) {
            return $this->json_response(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
        }
        $mutations = $this->db
            ->where('spare_part_id', $id)
            ->order_by('created_at', 'DESC')
            ->limit(50)
            ->get('stock_mutations')
            ->result();
        return $this->json_response(['success' => true, 'part' => $part, 'mutations' => $mutations]);
    }

    /** Stock mutation list for a part */
    public function stock_mutasi($id)
    {
        $part = $this->Spare_part_model->get_by_id($id);
        if (!$part) show_404();
        $mutations = $this->db
            ->select('sm.*, u.name AS actor_name')
            ->from('stock_mutations sm')
            ->join('users u', 'u.id_user = sm.created_by', 'left')
            ->where('sm.spare_part_id', $id)
            ->order_by('sm.created_at', 'DESC')
            ->limit(100)
            ->get()->result();
        return $this->json_response(['success' => true, 'part' => $part->name, 'mutations' => $mutations]);
    }

    // ─── Reports ──────────────────────────────────────────────────────────

    public function reports()
    {
        $year   = (int)($this->input->get('year') ?? date('Y'));
        $month  = $this->input->get('month') ?? null;

        $data = [
            'title'           => 'Laporan',
            'year'            => $year,
            'month'           => $month,
            'monthly_revenue' => $this->Payment_model->monthly_revenue($year),
            'monthly_bookings' => $this->Booking_model->get_monthly_stats($year),
            'total_revenue'   => $this->Payment_model->total_revenue(date('Y-01-01'), date('Y-12-31')),
            'total_bookings'  => $this->db->where('YEAR(booking_date)', $year)->count_all_results('bookings'),
        ];
        $this->render('admin/reports', $data, 'admin');
    }

    /** AJAX: daily report data */
    public function report_daily()
    {
        $date = $this->input->get('date') ?: date('Y-m-d');
        $rows = $this->db
            ->select('p.invoice_number, u.name AS customer_name, s.name AS service_name,
                      p.method AS payment_method, p.total_amount, p.status, p.paid_at')
            ->from('payments p')
            ->join('bookings b', 'b.id = p.booking_id', 'left')
            ->join('users u',    'u.id_user = b.user_id',       'left')
            ->join('services s', 's.id_service = b.service_id', 'left')
            ->where('DATE(p.created_at)', $date)
            ->where('p.status', 'paid')
            ->order_by('p.created_at', 'DESC')
            ->get()->result();

        $cash     = array_sum(array_column(array_filter((array)$rows, fn($r) => $r->payment_method === 'cash'),     'total_amount'));
        $transfer = array_sum(array_column(array_filter((array)$rows, fn($r) => $r->payment_method === 'transfer'), 'total_amount'));

        return $this->json_response([
            'success'  => true,
            'date'     => $date,
            'rows'     => $rows,
            'total_cash'     => $cash,
            'total_transfer' => $transfer,
            'grand_total'    => $cash + $transfer,
        ]);
    }

    /** AJAX: monthly report data */
    public function report_monthly()
    {
        $year  = (int)($this->input->get('year')  ?: date('Y'));
        $month = (int)($this->input->get('month') ?: date('m'));

        $rows = $this->db
            ->select('DATE(p.paid_at) AS day, SUM(p.total_amount) AS revenue, COUNT(p.id) AS transactions')
            ->from('payments p')
            ->where('YEAR(p.paid_at)',  $year)
            ->where('MONTH(p.paid_at)', $month)
            ->where('p.status',         'paid')
            ->group_by('DATE(p.paid_at)')
            ->order_by('day', 'ASC')
            ->get()->result();

        $prev_month  = $month == 1 ? 12 : $month - 1;
        $prev_year   = $month == 1 ? $year - 1 : $year;
        $prev_total  = $this->Payment_model->total_revenue(
            "$prev_year-" . str_pad($prev_month, 2, '0', STR_PAD_LEFT) . "-01",
            date('Y-m-t', mktime(0, 0, 0, $prev_month, 1, $prev_year))
        );
        $curr_total  = array_sum(array_column((array)$rows, 'revenue'));
        $growth      = $prev_total > 0 ? round((($curr_total - $prev_total) / $prev_total) * 100, 1) : null;

        return $this->json_response([
            'success'    => true,
            'year'       => $year,
            'month'      => $month,
            'rows'       => $rows,
            'total'      => $curr_total,
            'prev_total' => $prev_total,
            'growth'     => $growth,
        ]);
    }

    /** AJAX: yearly report data */
    public function report_yearly()
    {
        $year = (int)($this->input->get('year') ?: date('Y'));
        $monthly = $this->Payment_model->monthly_revenue($year);
        $total   = array_sum(array_column((array)$monthly, 'revenue'));
        return $this->json_response([
            'success' => true,
            'year'    => $year,
            'rows'    => $monthly,
            'total'   => $total,
        ]);
    }

    /** Export report as CSV */
    public function report_export()
    {
        $type  = $this->input->get('type') ?: 'daily';
        $date  = $this->input->get('date')  ?: date('Y-m-d');
        $year  = (int)($this->input->get('year')  ?: date('Y'));
        $month = (int)($this->input->get('month') ?: date('m'));

        $rows     = [];
        $filename = 'laporan-' . $type . '-' . date('Ymd') . '.csv';

        if ($type === 'daily') {
            $rows = $this->db
                ->select('p.invoice_number, u.name AS customer, s.name AS layanan, p.method, p.total_amount AS total, p.paid_at')
                ->from('payments p')
                ->join('bookings b', 'b.id = p.booking_id', 'left')
                ->join('users u',    'u.id_user = b.user_id',       'left')
                ->join('services s', 's.id_service = b.service_id', 'left')
                ->where('DATE(p.created_at)', $date)
                ->where('p.status', 'paid')
                ->get()->result_array();
        } elseif ($type === 'monthly') {
            $rows = $this->db
                ->select('DATE(p.paid_at) AS tanggal, COUNT(*) AS transaksi, SUM(p.total_amount) AS total')
                ->from('payments p')
                ->where('YEAR(p.paid_at)',  $year)
                ->where('MONTH(p.paid_at)', $month)
                ->where('p.status', 'paid')
                ->group_by('DATE(p.paid_at)')
                ->get()->result_array();
        } else {
            $rows = $this->db
                ->select('MONTH(p.paid_at) AS bulan, COUNT(*) AS transaksi, SUM(p.total_amount) AS total')
                ->from('payments p')
                ->where('YEAR(p.paid_at)', $year)
                ->where('p.status', 'paid')
                ->group_by('MONTH(p.paid_at)')
                ->get()->result_array();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        if (!empty($rows)) {
            fputcsv($out, array_keys($rows[0]));
            foreach ($rows as $row) fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }



    public function settings()
    {
        $data = [
            'title'    => 'Pengaturan Bengkel',
            'settings' => $this->settings,
        ];
        $this->render('admin/settings', $data, 'admin');
    }

    public function save_settings()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $allowed_keys = [
            'workshop_name',
            'workshop_tagline',
            'workshop_phone',
            'workshop_whatsapp',
            'workshop_address',
            'workshop_city',
            'workshop_email',
            'workshop_lat',
            'workshop_lng',
            'bank_name',
            'bank_account',
            'bank_holder',
            'workshop_hours_weekday',
            'workshop_hours_saturday',
            'max_booking_per_day',
            'reminder_days_before_service',
        ];

        $payload = json_decode((string) $this->input->post('settings'), true);
        if (!is_array($payload)) {
            $payload = $this->input->post(null, true) ?: [];
        }

        foreach ($allowed_keys as $key) {
            $value = $payload[$key] ?? null;
            if ($value !== null) {
                $exists = $this->db->where('key', $key)->count_all_results('settings');
                if ($exists) {
                    $this->db->where('key', $key)->update('settings', ['value' => $value]);
                } else {
                    $this->db->insert('settings', ['key' => $key, 'value' => $value]);
                }
            }
        }

        return $this->json_response(['success' => true, 'message' => 'Pengaturan berhasil disimpan.']);
    }

    // ─── Gallery management (admin) ───────────────────────────────────────

    public function gallery()
    {
        $this->load->model('Gallery_model');
        $data = [
            'title'   => 'Galeri',
            'gallery' => $this->Gallery_model->get_all(),
        ];
        $this->render('admin/gallery', $data, 'admin');
    }

    public function upload_gallery()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $upload_path = FCPATH . 'uploads/gallery/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

        $this->upload->initialize([
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 3072,
            'encrypt_name'  => true,
        ]);

        if (!$this->upload->do_upload('image')) {
            return $this->json_response(['success' => false, 'message' => $this->upload->display_errors('', '')], 422);
        }

        $file = $this->upload->data('file_name');
        $this->load->model('Gallery_model');
        $id = $this->Gallery_model->create([
            'title'       => strip_tags($this->input->post('title') ?? ''),
            'description' => strip_tags($this->input->post('description') ?? ''),
            'image'       => $file,
            'is_active'   => 1,
        ]);

        return $this->json_response(['success' => true, 'message' => 'Gambar berhasil diupload.', 'id' => $id]);
    }

    public function delete_gallery($id)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->_verify_csrf_header();

        $this->load->model('Gallery_model');
        $item = $this->Gallery_model->get_by_id($id);
        if (!$item) return $this->json_response(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);

        // Delete physical file
        $file_path = FCPATH . 'uploads/gallery/' . $item->image;
        if (file_exists($file_path)) @unlink($file_path);

        $this->Gallery_model->delete($id);
        return $this->json_response(['success' => true, 'message' => 'Gambar berhasil dihapus.']);
    }

    // ─── Invoice HTML print view ──────────────────────────────────────────

    public function invoice_print($payment_id)
    {
        $payment = $this->Payment_model->get_by_id($payment_id);
        if (!$payment) show_404();

        $booking = $this->Booking_model->get_by_id($payment->booking_id);
        $items   = $this->Payment_model->get_items($payment_id);
        $data    = [
            'payment'  => $payment,
            'booking'  => $booking,
            'items'    => $items,
            'settings' => $this->settings,
        ];
        $this->load->view('admin/invoice_print', $data);
    }

    // ─── Invoice PDF ──────────────────────────────────────────────────────

    public function invoice_pdf($payment_id)
    {
        $payment = $this->Payment_model->get_by_id($payment_id);
        if (!$payment) show_404();

        $booking = $this->Booking_model->get_by_id($payment->booking_id);
        $items   = $this->Payment_model->get_items($payment_id);

        // Try to generate PDF using dompdf if available
        $pdf_path = APPPATH . '../vendor/dompdf/dompdf/src/Dompdf.php';
        if (file_exists($pdf_path)) {
            $this->_generate_pdf($payment, $booking, $items);
        } else {
            // Fallback: print-friendly HTML view
            $data = ['payment' => $payment, 'booking' => $booking, 'items' => $items, 'settings' => $this->settings];
            $this->load->view('admin/invoice_print', $data);
        }
    }

    private function _generate_pdf($payment, $booking, $items)
    {
        require_once APPPATH . '../vendor/autoload.php';

        $html  = $this->load->view('admin/invoice_pdf', ['payment' => $payment, 'booking' => $booking, 'items' => $items, 'settings' => $this->settings], true);
        $dompdf = new \Dompdf\Dompdf(['default_paper_size' => 'A4']);
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream('invoice-' . $payment->invoice_number . '.pdf', ['Attachment' => false]);
    }

    // ─── Private ─────────────────────────────────────────────────────────

    private function _notify_customer($user_id, $booking_code, $status)
    {
        $label_map = [
            'confirmed'       => 'Booking dikonfirmasi',
            'in_progress'     => 'Kendaraan mulai diservis',
            'waiting_payment' => 'Servis selesai, menunggu pembayaran',
            'completed'       => 'Pembayaran diterima, terima kasih!',
            'cancelled'       => 'Booking dibatalkan',
        ];
        $label   = $label_map[$status] ?? 'Status booking diperbarui';
        $message = 'Booking ' . $booking_code . ': ' . $label;

        $this->db->insert('notifications', [
            'user_id'    => $user_id,
            'title'      => 'Update Booking',
            'message'    => $message,
            'type'       => 'booking',
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
