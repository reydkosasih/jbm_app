<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment_model extends CI_Model
{

    protected $table    = 'payments';
    protected $items_t  = 'payment_items';

    // ─── Create ──────────────────────────────────────────────────────────

    public function create($booking_id, $items = [], $method = 'cash')
    {
        // Calculate total from items
        $total = 0;
        foreach ($items as $item) {
            $total += (float)$item['unit_price'] * (int)($item['quantity'] ?? 1);
        }

        $invoice_number = $this->generate_invoice_number();

        $this->db->insert($this->table, [
            'booking_id'     => $booking_id,
            'invoice_number' => $invoice_number,
            'total_amount'   => $total,
            'method'         => $method,
            'status'         => 'unpaid',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
        $payment_id = $this->db->insert_id();

        foreach ($items as $item) {
            $this->db->insert($this->items_t, [
                'payment_id'  => $payment_id,
                'description' => $item['description'],
                'type'        => $item['type'],        // 'jasa' | 'parts'
                'quantity'    => $item['quantity']  ?? 1,
                'unit_price'  => $item['unit_price'],
            ]);
        }

        return $payment_id;
    }

    // ─── Get ─────────────────────────────────────────────────────────────

    public function get_by_booking($booking_id)
    {
        return $this->db->where('booking_id', $booking_id)->get($this->table)->row();
    }

    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function get_items($payment_id)
    {
        return $this->db->where('payment_id', $payment_id)->get($this->items_t)->result();
    }

    public function get_all($filters = [])
    {
        $this->db->select('p.*, b.booking_code, b.booking_date, u.name AS customer_name, u.phone AS customer_phone,
                           v.plate_number, s.name AS service_name')
            ->from($this->table . ' p')
            ->join('bookings b',  'b.id = p.booking_id')
            ->join('users u',     'u.id_user = b.user_id')
            ->join('vehicles v',  'v.id = b.vehicle_id')
            ->join('services s',  's.id_service = b.service_id');

        if (!empty($filters['status']))     $this->db->where('p.status', $filters['status']);
        if (!empty($filters['date_from']))  $this->db->where('DATE(p.created_at) >=', $filters['date_from']);
        if (!empty($filters['date_to']))    $this->db->where('DATE(p.created_at) <=', $filters['date_to']);
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('p.invoice_number', $filters['search'])
                ->or_like('b.booking_code', $filters['search'])
                ->or_like('u.name', $filters['search'])
                ->group_end();
        }
        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ─── Update ──────────────────────────────────────────────────────────

    public function confirm($id, $actor_id)
    {
        $this->db->where('id', $id)->update($this->table, [
            'status'      => 'paid',
            'paid_at'     => date('Y-m-d H:i:s'),
            'confirmed_by' => $actor_id,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        // Update booking status to completed
        $payment = $this->get_by_id($id);
        if ($payment) {
            $this->db->where('id', $payment->booking_id)->update('bookings', [
                'status'     => 'completed',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $this->db->insert('booking_status_logs', [
                'booking_id' => $payment->booking_id,
                'status'     => 'completed',
                'actor_id'   => $actor_id,
                'note'       => 'Pembayaran dikonfirmasi. Servis selesai.',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return true;
    }

    public function reject($id, $note = '')
    {
        return $this->db->where('id', $id)->update($this->table, [
            'status'     => 'unpaid',
            'proof_file' => null,
            'reject_note' => $note,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ─── Stats ───────────────────────────────────────────────────────────

    public function total_revenue($date_from = null, $date_to = null)
    {
        $this->db->where('status', 'paid');
        if ($date_from) $this->db->where('DATE(paid_at) >=', $date_from);
        if ($date_to)   $this->db->where('DATE(paid_at) <=', $date_to);
        return (float)$this->db->select_sum('total_amount')->get($this->table)->row()->total_amount;
    }

    public function monthly_revenue($year)
    {
        return $this->db->select('MONTH(paid_at) AS month, SUM(total_amount) AS revenue, COUNT(*) AS count')
            ->where('status', 'paid')
            ->where('YEAR(paid_at)', $year)
            ->group_by('MONTH(paid_at)')
            ->order_by('month', 'ASC')
            ->get($this->table)->result();
    }

    // ─── Helper ──────────────────────────────────────────────────────────

    public function generate_invoice_number()
    {
        $prefix = 'INV-' . date('Ym');
        do {
            $number = $prefix . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while ($this->db->where('invoice_number', $number)->count_all_results($this->table) > 0);
        return $number;
    }
}
