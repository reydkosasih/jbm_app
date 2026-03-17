<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Booking_model extends CI_Model
{

    protected $table = 'bookings';

    // ─── Create & Update ─────────────────────────────────────────────────

    public function create($data)
    {
        $data['booking_code'] = $this->generate_booking_code();
        $data['status']       = 'pending';
        $data['created_at']   = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        $id = $this->db->insert_id();

        // Create initial status log
        $this->_log($id, 'pending', null, 'Booking baru dibuat oleh customer.');

        return $id;
    }

    public function update_status($id, $new_status, $old_status = null, $note = '', $actor_id = null)
    {
        $this->db->where('id', $id)->update($this->table, [
            'status'     => $new_status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->_log($id, $new_status, $actor_id, $note);
        return true;
    }

    public function cancel($id, $user_id)
    {
        $booking = $this->get_by_id($id);
        if (!$booking) return false;
        if ((int)$booking->user_id !== (int)$user_id) return false;
        if (!in_array($booking->status, ['pending', 'confirmed'])) return false;

        return $this->update_status($id, 'cancelled', $booking->status, 'Dibatalkan oleh customer.', $user_id);
    }

    // ─── Get ─────────────────────────────────────────────────────────────

    public function get_by_id($id, $user_id = null)
    {
        $this->db->select('b.*, u.name AS customer_name, u.phone AS customer_phone, u.email AS customer_email,
                           v.plate_number, v.brand, v.model AS vehicle_model, v.year, v.color,
                           s.name AS service_name, s.base_price AS service_price, s.duration_min AS service_duration,
                           ts.label AS slot_label, ts.start_time, ts.end_time')
            ->from($this->table . ' b')
            ->join('users u',      'u.id_user = b.user_id')
            ->join('vehicles v',   'v.id = b.vehicle_id')
            ->join('services s',   's.id_service = b.service_id')
            ->join('time_slots ts', 'ts.id = b.slot_id')
            ->where('b.id', $id);
        if ($user_id) $this->db->where('b.user_id', $user_id);
        return $this->db->get()->row();
    }

    public function get_by_code($code)
    {
        $this->db->select('b.*, u.name AS customer_name,
                           v.plate_number, v.brand, v.model AS vehicle_model,
                           s.name AS service_name, s.base_price AS service_price,
                           ts.label AS slot_label, ts.start_time, ts.end_time')
            ->from($this->table . ' b')
            ->join('users u',      'u.id_user = b.user_id')
            ->join('vehicles v',   'v.id = b.vehicle_id')
            ->join('services s',   's.id_service = b.service_id')
            ->join('time_slots ts', 'ts.id = b.slot_id')
            ->where('b.booking_code', $code);
        return $this->db->get()->row();
    }

    public function get_by_user($user_id, $filters = [])
    {
        $this->db->select('b.*, s.name AS service_name, v.plate_number, v.brand, v.model AS vehicle_model,
                           ts.label AS slot_label')
            ->from($this->table . ' b')
            ->join('services s',   's.id_service = b.service_id')
            ->join('vehicles v',   'v.id = b.vehicle_id')
            ->join('time_slots ts', 'ts.id = b.slot_id')
            ->where('b.user_id', $user_id);
        if (!empty($filters['status'])) $this->db->where('b.status', $filters['status']);
        $this->db->order_by('b.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_all_with_filter($filters = [])
    {
        $this->db->select('b.*, u.name AS customer_name, u.phone AS customer_phone,
                           v.plate_number, v.brand, v.model AS vehicle_model,
                           s.name AS service_name, ts.label AS slot_label')
            ->from($this->table . ' b')
            ->join('users u',      'u.id_user = b.user_id')
            ->join('vehicles v',   'v.id = b.vehicle_id')
            ->join('services s',   's.id_service = b.service_id')
            ->join('time_slots ts', 'ts.id = b.slot_id');

        if (!empty($filters['status']))       $this->db->where('b.status', $filters['status']);
        if (!empty($filters['date']))         $this->db->where('b.booking_date', $filters['date']);
        if (!empty($filters['mechanic_id']))  $this->db->where('b.mechanic_id', $filters['mechanic_id']);
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('b.booking_code', $filters['search'])
                ->or_like('u.name', $filters['search'])
                ->or_like('v.plate_number', $filters['search'])
                ->group_end();
        }
        $this->db->order_by('b.booking_date', 'DESC');
        $this->db->order_by('ts.start_time',  'ASC');
        return $this->db->get()->result();
    }

    public function get_active_by_date($date)
    {
        return $this->db->where('booking_date', $date)
            ->where_not_in('status', ['cancelled'])
            ->get($this->table)->result();
    }

    public function get_status_logs($booking_id)
    {
        return $this->db->select('bsl.*, u.name AS actor_name')
            ->from('booking_status_logs bsl')
            ->join('users u', 'u.id_user = bsl.actor_id', 'left')
            ->where('bsl.booking_id', $booking_id)
            ->order_by('bsl.created_at', 'ASC')
            ->get()->result();
    }

    // ─── Stats ───────────────────────────────────────────────────────────

    public function count_by_status($status, $date = null)
    {
        if ($date) $this->db->where('booking_date', $date);
        return $this->db->where('status', $status)->count_all_results($this->table);
    }

    public function today_queue()
    {
        return $this->db->select('b.*, u.name AS customer_name, v.plate_number, v.brand, v.model AS vehicle_model,
                                  s.name AS service_name, ts.label AS slot_label, ts.start_time')
            ->from($this->table . ' b')
            ->join('users u',      'u.id_user = b.user_id')
            ->join('vehicles v',   'v.id = b.vehicle_id')
            ->join('services s',   's.id_service = b.service_id')
            ->join('time_slots ts', 'ts.id = b.slot_id')
            ->where('b.booking_date', date('Y-m-d'))
            ->where_in('b.status', ['confirmed', 'in_progress'])
            ->order_by('ts.start_time', 'ASC')
            ->get()->result();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    public function generate_booking_code()
    {
        $prefix = 'JBM-' . date('Ym');
        do {
            $code = $prefix . strtoupper(substr(uniqid(), -4));
        } while ($this->db->where('booking_code', $code)->count_all_results($this->table) > 0);
        return $code;
    }

    private function _log($booking_id, $status, $actor_id, $note)
    {
        $this->db->insert('booking_status_logs', [
            'booking_id' => $booking_id,
            'status'     => $status,
            'actor_id'   => $actor_id,
            'note'       => $note,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ─── Revenue / report helpers ─────────────────────────────────────────

    public function get_monthly_stats($year)
    {
        return $this->db->select('MONTH(booking_date) AS month, COUNT(*) AS total,
                                  SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) AS completed,
                                  SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) AS cancelled')
            ->where('YEAR(booking_date)', $year)
            ->group_by('MONTH(booking_date)')
            ->order_by('month', 'ASC')
            ->get($this->table)->result();
    }
}
