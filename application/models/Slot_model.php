<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Slot_model extends CI_Model
{

    protected $table = 'time_slots';

    public function get_active()
    {
        return $this->db->where('is_active', 1)
            ->order_by('start_time', 'ASC')
            ->get($this->table)->result();
    }

    /**
     * Get slots with booking count for a given date.
     * Returns each slot with 'booked_count' and 'is_full' flag.
     */
    public function get_with_availability($date)
    {
        $this->db->select('ts.*, COALESCE(bc.cnt, 0) AS booked_count,
                           CASE WHEN COALESCE(bc.cnt, 0) >= ts.max_bookings THEN 1 ELSE 0 END AS is_full')
            ->from($this->table . ' ts')
            ->join(
                '(SELECT slot_id, COUNT(*) AS cnt FROM bookings
                          WHERE booking_date = ' . $this->db->escape($date) . '
                          AND status NOT IN (\'cancelled\')
                          GROUP BY slot_id) bc',
                'bc.slot_id = ts.id',
                'left'
            )
            ->where('ts.is_active', 1)
            ->order_by('ts.start_time', 'ASC');
        return $this->db->get()->result();
    }

    public function count_bookings($slot_id, $date)
    {
        return $this->db->where('slot_id', $slot_id)
            ->where('booking_date', $date)
            ->where_not_in('status', ['cancelled'])
            ->count_all_results('bookings');
    }

    public function is_available($slot_id, $date)
    {
        $slot = $this->db->where('id', $slot_id)->where('is_active', 1)->get($this->table)->row();
        if (!$slot) return false;
        $count = $this->count_bookings($slot_id, $date);
        return $count < $slot->max_bookings;
    }

    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }
}
