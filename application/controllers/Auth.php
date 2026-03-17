<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{
    // Max failed login attempts before temporary block
    private const MAX_ATTEMPTS   = 5;
    private const BLOCK_DURATION = 60; // 10 minutes in seconds

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library(['form_validation']);
    }

    // -------------------------------------------------------------------------
    // Login
    // -------------------------------------------------------------------------

    public function login()
    {
        // Redirect already logged-in users
        if ($this->is_logged_in()) {
            $this->_redirect_by_role();
        }

        if ($this->input->method() === 'post') {
            $this->_handle_login_post();
            return;
        }

        $this->render('auth/login', ['page_title' => 'Masuk'], 'public');
    }

    private function _handle_login_post()
    {
        // Verify CSRF (handled by CI3 automatically, but also check header for AJAX)
        $this->_verify_csrf_header();

        $email    = $this->input->post('email', true);
        $password = $this->input->post('password', false);

        // Rate limiting
        $block_key = 'login_block_' . md5($email);
        $attempts_key = 'login_attempts_' . md5($email);
        $block_until  = $this->session->userdata($block_key);

        if ($block_until && time() < $block_until) {
            $remaining = ceil(($block_until - time()) / 60);
            $this->json_response([
                'success' => false,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$remaining} menit.",
            ], 429);
        }

        // Validate input
        if (empty($email) || empty($password)) {
            $this->json_response(['success' => false, 'message' => 'Email dan password wajib diisi.'], 422);
        }

        // Sanitize email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json_response(['success' => false, 'message' => 'Format email tidak valid.'], 422);
        }

        $user = $this->User_model->get_by_email($email);

        if (!$user || !password_verify($password, $user['password'])) {
            // Increment failed attempts
            $attempts = (int)($this->session->userdata($attempts_key) ?? 0) + 1;
            $this->session->set_userdata($attempts_key, $attempts);

            if ($attempts >= self::MAX_ATTEMPTS) {
                $this->session->set_userdata($block_key, time() + self::BLOCK_DURATION);
                $this->session->unset_userdata($attempts_key);
                $this->json_response([
                    'success' => false,
                    'message' => 'Akun diblokir sementara karena terlalu banyak percobaan. Coba lagi dalam 10 menit.',
                ], 429);
            }

            $this->json_response([
                'success' => false,
                'message' => 'Email atau password salah. (' . $attempts . '/' . self::MAX_ATTEMPTS . ' percobaan)',
            ], 401);
        }

        // Clear failed attempts
        $this->session->unset_userdata([$attempts_key, $block_key]);

        // Set session
        $this->session->set_userdata([
            'user_id'     => $user['id_user'],
            'user_name'   => $user['name'],
            'user_email'  => $user['email'],
            'user_role'   => $user['role'],
            'user_avatar' => $user['avatar'],
        ]);

        $redirect = $this->_get_redirect_url($user['role']);

        $this->json_response(['success' => true, 'redirect' => $redirect]);
    }

    // -------------------------------------------------------------------------
    // Register
    // -------------------------------------------------------------------------

    public function register()
    {
        if ($this->is_logged_in()) {
            $this->_redirect_by_role();
        }

        if ($this->input->method() === 'post') {
            $this->_handle_register_post();
            return;
        }

        $this->render('auth/register', ['page_title' => 'Daftar Akun'], 'public');
    }

    private function _handle_register_post()
    {
        $this->_verify_csrf_header();

        $this->form_validation->set_rules('name',     'Nama Lengkap', 'required|trim|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('email',    'Email',        'required|trim|valid_email|max_length[150]');
        $this->form_validation->set_rules('phone',    'No. HP',       'trim|max_length[20]');
        $this->form_validation->set_rules('password', 'Password',     'required|min_length[8]|max_length[72]');
        $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|matches[password]');

        if (!$this->form_validation->run()) {
            $this->json_response([
                'success' => false,
                'message' => strip_tags(validation_errors()),
                'errors'  => $this->form_validation->error_array(),
            ], 422);
        }

        $email = trim($this->input->post('email', true));

        if ($this->User_model->email_exists($email)) {
            $this->json_response(['success' => false, 'message' => 'Email sudah terdaftar. Gunakan email lain.'], 409);
        }

        $id = $this->User_model->create([
            'name'     => trim($this->input->post('name', true)),
            'email'    => $email,
            'phone'    => trim($this->input->post('phone', true)),
            'password' => password_hash($this->input->post('password', false), PASSWORD_BCRYPT, ['cost' => 12]),
            'role'     => 'customer',
            'is_active' => 1,
        ]);

        if (!$id) {
            $this->json_response(['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.'], 500);
        }

        // Auto-login after registration
        $user = $this->User_model->get_by_id($id);
        $this->session->set_userdata([
            'user_id'     => $user['id_user'],
            'user_name'   => $user['name'],
            'user_email'  => $user['email'],
            'user_role'   => $user['role'],
            'user_avatar' => $user['avatar'],
        ]);

        $this->json_response([
            'success'  => true,
            'message'  => 'Akun berhasil dibuat! Selamat datang di JBM Bengkel.',
            'redirect' => base_url('my/dashboard'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }

    // -------------------------------------------------------------------------
    // Forgot Password
    // -------------------------------------------------------------------------

    public function forgot_password()
    {
        if ($this->input->method() === 'post') {
            $this->_handle_forgot_post();
            return;
        }
        $this->render('auth/forgot', ['page_title' => 'Lupa Password'], 'public');
    }

    private function _handle_forgot_post()
    {
        $this->_verify_csrf_header();

        $email = trim(filter_var($this->input->post('email', true), FILTER_SANITIZE_EMAIL));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json_response(['success' => false, 'message' => 'Format email tidak valid.'], 422);
        }

        $user = $this->User_model->get_by_email($email);

        // Always return success to prevent email enumeration
        if (!$user) {
            $this->json_response([
                'success' => true,
                'message' => 'Jika email terdaftar, link reset password telah dikirim.',
            ]);
        }

        $token  = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + 3600);

        $this->User_model->set_reset_token($user['id_user'], $token, $expiry);

        // Send email
        $this->_send_reset_email($user, $token);

        $this->json_response([
            'success' => true,
            'message' => 'Link reset password telah dikirim ke email Anda.',
        ]);
    }

    private function _send_reset_email(array $user, string $token)
    {
        $reset_url = base_url('reset-password/' . $token);

        $this->email->initialize([
            'protocol'  => 'smtp',
            'smtp_host' => getenv('SMTP_HOST') ?: 'localhost',
            'smtp_port' => getenv('SMTP_PORT') ?: 25,
            'smtp_user' => getenv('SMTP_USER') ?: '',
            'smtp_pass' => getenv('SMTP_PASS') ?: '',
        ]);

        $this->email->from(
            $this->settings['workshop_email'] ?? 'noreply@jbmbengkel.com',
            $this->settings['workshop_name']  ?? 'JBM Bengkel Mobil'
        );
        $this->email->to($user['email']);
        $this->email->subject('Reset Password — ' . ($this->settings['workshop_name'] ?? 'JBM Bengkel'));
        $this->email->message(
            "<p>Halo {$user['name']},</p>" .
                "<p>Klik link berikut untuk reset password Anda (berlaku 1 jam):</p>" .
                "<p><a href='{$reset_url}'>{$reset_url}</a></p>" .
                "<p>Abaikan email ini jika Anda tidak meminta reset password.</p>"
        );

        $this->email->send();
    }

    // -------------------------------------------------------------------------
    // Reset Password
    // -------------------------------------------------------------------------

    public function reset_password(string $token = '')
    {
        if (empty($token)) {
            show_404();
        }

        // Sanitize token (hex only)
        $token = preg_replace('/[^a-f0-9]/', '', $token);

        $user = $this->User_model->get_by_reset_token($token);

        if (!$user) {
            $this->set_notification('error', 'Link reset password tidak valid atau sudah kadaluarsa.');
            redirect('forgot-password');
        }

        if ($this->input->method() === 'post') {
            $this->_handle_reset_post($user, $token);
            return;
        }

        $this->render('auth/reset', ['page_title' => 'Reset Password', 'token' => $token], 'public');
    }

    private function _handle_reset_post(array $user, string $token)
    {
        $this->_verify_csrf_header();

        $password = $this->input->post('password', false);
        $confirm  = $this->input->post('password_confirm', false);

        if (empty($password) || strlen($password) < 8) {
            $this->json_response(['success' => false, 'message' => 'Password minimal 8 karakter.'], 422);
        }

        if ($password !== $confirm) {
            $this->json_response(['success' => false, 'message' => 'Konfirmasi password tidak cocok.'], 422);
        }

        $this->User_model->update_password(
            $user['id_user'],
            password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])
        );

        $this->json_response([
            'success'  => true,
            'message'  => 'Password berhasil direset. Silakan login.',
            'redirect' => base_url('login'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function _get_redirect_url(string $role): string
    {
        switch ($role) {
            case 'admin':
            case 'kasir':
                return base_url('admin');
            case 'mekanik':
                return base_url('admin/queue');
            default:
                return base_url('my/dashboard');
        }
    }

    private function _redirect_by_role()
    {
        redirect($this->_get_redirect_url($this->session->userdata('user_role') ?? 'customer'));
    }

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
}
