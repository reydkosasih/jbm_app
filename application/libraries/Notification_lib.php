<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Notification_lib
 *
 * Handles creating in-app notifications, sending emails, and optionally
 * WhatsApp messages via a gateway API.
 *
 * Usage:
 *   $this->load->library('notification_lib');
 *   $this->notification_lib->send($user_id, 'Title', 'Message', 'booking', '/my/booking/1');
 */
class Notification_lib
{
    /** @var CI_Controller */
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    // ─── In-app notifications ─────────────────────────────────────────────

    /**
     * Insert a notification for a single user.
     *
     * @param int    $id_user  Target user id
     * @param string $title
     * @param string $message
     * @param string $type   booking|payment|service|stock|system
     * @param string|null $url  Optional deep-link URL
     * @return int  Inserted notification id
     */
    public function send(int $id_user, string $title, string $message, string $type = 'system', ?string $url = null): int
    {
        $this->CI->db->insert('notifications', [
            'user_id'    => $id_user,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'url'        => $url,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return (int) $this->CI->db->insert_id();
    }

    /**
     * Insert a notification for all users with a given role (or array of roles).
     *
     * @param string|array $roles  e.g. 'admin' or ['admin','kasir']
     * @param string $title
     * @param string $message
     * @param string $type
     * @param string|null $url
     * @return int  Number of notifications inserted
     */
    public function send_to_role($roles, string $title, string $message, string $type = 'system', ?string $url = null): int
    {
        if (!is_array($roles)) $roles = [$roles];
        $users = $this->CI->db
            ->select('id_user')
            ->where('is_active', 1)
            ->where_in('role', $roles)
            ->get('users')->result();

        $count = 0;
        foreach ($users as $u) {
            $this->send((int) $u->id_user, $title, $message, $type, $url);
            $count++;
        }
        return $count;
    }

    /**
     * Convenience: send to all admin + kasir users.
     */
    public function send_to_admins(string $title, string $message, string $type = 'system', ?string $url = null): int
    {
        return $this->send_to_role(['admin', 'kasir'], $title, $message, $type, $url);
    }

    /**
     * Mark a notification as read.
     */
    public function mark_read(int $id_notif, int $id_user): void
    {
        $this->CI->db
            ->where('id', $id_notif)
            ->where('user_id', $id_user)
            ->update('notifications', ['is_read' => 1]);
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function mark_all_read(int $id_user): void
    {
        $this->CI->db
            ->where('user_id', $id_user)
            ->where('is_read', 0)
            ->update('notifications', ['is_read' => 1]);
    }

    /**
     * Get unread count for a user.
     */
    public function unread_count(int $id_user): int
    {
        return (int) $this->CI->db
            ->where('user_id', $id_user)
            ->where('is_read', 0)
            ->count_all_results('notifications');
    }

    /**
     * Get recent notifications for a user.
     */
    public function get_recent(int $id_user, int $limit = 15): array
    {
        return $this->CI->db
            ->where('user_id', $id_user)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get('notifications')->result();
    }

    // ─── Email ────────────────────────────────────────────────────────────

    /**
     * Send an HTML email.
     *
     * @param string $to       Recipient address
     * @param string $subject
     * @param string $body_html Full HTML body
     * @return bool  TRUE on success
     */
    public function send_email(string $to, string $subject, string $body_html): bool
    {
        $this->CI->load->library('email');

        $smtp_host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $smtp_user = getenv('SMTP_USER') ?: '';
        $smtp_pass = getenv('SMTP_PASS') ?: '';
        $smtp_port = (int)(getenv('SMTP_PORT') ?: 587);

        $config = [
            'protocol'  => $smtp_host ? 'smtp' : 'mail',
            'smtp_host' => $smtp_host,
            'smtp_user' => $smtp_user,
            'smtp_pass' => $smtp_pass,
            'smtp_port' => $smtp_port,
            'smtp_crypto' => $smtp_port === 465 ? 'ssl' : 'tls',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n",
        ];

        $workshop_name = $this->CI->db
            ->where('key', 'workshop_name')
            ->get('settings')->row_array()['value'] ?? 'JBM Bengkel Mobil';

        $this->CI->email->initialize($config);
        $this->CI->email->from($smtp_user ?: 'no-reply@jbm.id', $workshop_name);
        $this->CI->email->to($to);
        $this->CI->email->subject($subject);
        $this->CI->email->message($body_html);

        return (bool) $this->CI->email->send(false);
    }

    /**
     * Send a booking status change email to the customer.
     *
     * @param string $to         Customer email address
     * @param string $name       Customer name
     * @param string $booking_code
     * @param string $status     New status key
     */
    public function send_booking_update_email(string $to, string $name, string $booking_code, string $status): bool
    {
        $label_map = [
            'confirmed'   => 'Booking Dikonfirmasi',
            'in_progress' => 'Kendaraan Sedang Dikerjakan',
            'done'        => 'Servis Selesai',
            'cancelled'   => 'Booking Dibatalkan',
            'waiting_payment' => 'Menunggu Pembayaran',
        ];
        $label = $label_map[$status] ?? ucfirst(str_replace('_', ' ', $status));

        $body = '
        <div style="font-family:sans-serif;max-width:600px;margin:0 auto;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden">
          <div style="background:#1a73e8;padding:20px;text-align:center">
            <h1 style="color:#fff;margin:0">JBM Bengkel Mobil</h1>
          </div>
          <div style="padding:24px">
            <p>Halo <strong>' . htmlspecialchars($name) . '</strong>,</p>
            <p>Status booking Anda <strong>' . htmlspecialchars($booking_code) . '</strong> telah diperbarui menjadi:</p>
            <div style="background:#f5f5f5;border-left:4px solid #1a73e8;padding:12px 16px;margin:16px 0;border-radius:4px">
              <strong style="font-size:18px">' . htmlspecialchars($label) . '</strong>
            </div>
            <p>Silakan cek aplikasi untuk detail selengkapnya.</p>
            <p style="color:#888;font-size:12px">Email ini dikirim secara otomatis. Jangan balas email ini.</p>
          </div>
        </div>';

        return $this->send_email($to, "Update Booking " . $booking_code . " — " . $label, $body);
    }

    // ─── WhatsApp (optional gateway) ─────────────────────────────────────

    /**
     * Send a WhatsApp message via a configured gateway API.
     * Set WA_GATEWAY_URL and WA_GATEWAY_KEY in .env to enable.
     *
     * @param string $phone  Phone number with country code (e.g. 628123456789)
     * @param string $message Plain text message
     * @return bool
     */
    public function send_whatsapp(string $phone, string $message): bool
    {
        $gateway_url = getenv('WA_GATEWAY_URL');
        $gateway_key = getenv('WA_GATEWAY_KEY');

        if (!$gateway_url) return false; // Feature disabled

        // Normalize phone: strip non-digits, ensure starts with country code
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strncmp($phone, '0', 1) === 0) {
            $phone = '62' . substr($phone, 1);
        }

        $payload = json_encode(['phone' => $phone, 'message' => $message]);

        $ch = curl_init($gateway_url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $gateway_key,
            ],
        ]);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $http_code >= 200 && $http_code < 300;
    }
}
