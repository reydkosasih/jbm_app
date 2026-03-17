<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Controller — Base application controller for JBM Bengkel Mobil
 * All controllers extend this class.
 */
class MY_Controller extends CI_Controller
{
    /** @var array Current logged-in user data */
    protected $current_user = [];

    /** @var array Site-wide settings from DB */
    protected $settings = [];

    public function __construct()
    {
        parent::__construct();

        // Load .env variables if available
        $this->_load_env();

        // Load site settings
        $this->settings = $this->_get_settings();

        // Make settings available to all views
        $this->load->vars([
            'settings'     => $this->settings,
            'current_user' => $this->get_user_array(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Environment helpers
    // -------------------------------------------------------------------------

    private function _load_env()
    {
        $env_file = FCPATH . '.env';
        if (file_exists($env_file)) {
            $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key   = trim($key);
                    $value = trim($value);
                    if (!getenv($key)) {
                        putenv("{$key}={$value}");
                        $_ENV[$key]    = $value;
                        $_SERVER[$key] = $value;
                    }
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // Settings
    // -------------------------------------------------------------------------

    private function _get_settings(): array
    {
        try {
            $rows = $this->db->get('settings')->result_array();
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['key']] = $row['value'];
            }
            return $settings;
        } catch (Exception $e) {
            return [];
        }
    }

    // -------------------------------------------------------------------------
    // Auth helpers
    // -------------------------------------------------------------------------

    /**
     * Check if a user is currently authenticated.
     */
    public function is_logged_in(): bool
    {
        return (bool) $this->session->userdata('user_id');
    }

    /**
     * Return the current authenticated user array, or empty array.
     */
    protected function get_user_array(): array
    {
        if ($this->is_logged_in()) {
            $user_id = (int) $this->session->userdata('user_id');
            $row = $this->db->where('id_user', $user_id)->get('users')->row_array();

            return [
                'id'       => $user_id,
                'id_user'  => $user_id,
                'name'     => $row['name'] ?? $this->session->userdata('user_name'),
                'email'    => $row['email'] ?? $this->session->userdata('user_email'),
                'role'     => $row['role'] ?? $this->session->userdata('user_role'),
                'avatar'   => $row['avatar'] ?? $this->session->userdata('user_avatar'),
                'phone'    => $row['phone'] ?? null,
                'address'  => $row['address'] ?? null,
                'is_active' => isset($row['is_active']) ? (int) $row['is_active'] : 1,
            ];
        }
        return [];
    }

    /**
     * Return the current authenticated user as an object for controller/view consumption.
     */
    public function get_user(): object
    {
        return (object) $this->get_user_array();
    }

    /**
     * Redirect to login page if not authenticated.
     */
    public function require_login()
    {
        if (!$this->is_logged_in()) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
        }
    }

    /**
     * Check that the current user has the required role.
     * Redirects to 403 page if unauthorized.
     *
     * @param string|array $role  Allowed role(s)
     */
    public function require_role($role)
    {
        $this->require_login();

        $user_role  = $this->session->userdata('user_role');
        $allowed    = is_array($role) ? $role : [$role];

        if (!in_array($user_role, $allowed, true)) {
            redirect('errors/error_403');
        }
    }

    // -------------------------------------------------------------------------
    // Flash notification
    // -------------------------------------------------------------------------

    /**
     * Set a SweetAlert2 flash notification.
     *
     * @param string $type    success | error | warning | info
     * @param string $message Notification text
     */
    public function set_notification(string $type, string $message)
    {
        $this->session->set_flashdata('swal_type',    $type);
        $this->session->set_flashdata('swal_message', $message);
    }

    // -------------------------------------------------------------------------
    // JSON response helper
    // -------------------------------------------------------------------------

    /**
     * Output a JSON response and stop execution.
     * Always includes fresh csrf_hash so the JS layer can sync after every call.
     *
     * @param mixed $data
     * @param int   $status HTTP status code
     */
    public function json_response($data, int $status = 200)
    {
        if (is_array($data)) {
            $data['csrf_hash'] = $this->security->get_csrf_hash();
            $data['csrf_name'] = $this->security->get_csrf_token_name();
        }
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data))
            ->_display();
        exit;
    }

    /**
     * Verify CSRF token from either AJAX header or posted form field.
     */
    protected function _verify_csrf_header()
    {
        $csrf_token_name = $this->security->get_csrf_token_name();
        $csrf_hash       = $this->security->get_csrf_hash();

        $header = $this->input->get_request_header('X-CSRF-Token', true);
        $post   = $this->input->post($csrf_token_name, true);

        if ($header !== $csrf_hash && $post !== $csrf_hash) {
            $this->json_response(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
        }
    }

    // -------------------------------------------------------------------------
    // View renderer with layout
    // -------------------------------------------------------------------------

    /**
     * Render a view inside a layout.
     *
     * @param string $view    View file path (relative to views/)
     * @param array  $data    Data to pass to the view
     * @param string $layout  Layout to use (public|customer|admin)
     */
    protected function render(string $view, array $data = [], string $layout = 'public')
    {
        $data['content'] = $this->load->view($view, $data, true);
        $this->load->view('layouts/' . $layout, $data);
    }
}


// =============================================================================
// Customer_Controller — enforces customer role
// =============================================================================
class Customer_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role('customer');
    }
}


// =============================================================================
// Admin_Controller — enforces admin or kasir role
// =============================================================================
class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role(['admin', 'kasir']);
    }
}
