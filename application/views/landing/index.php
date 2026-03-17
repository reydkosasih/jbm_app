<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('landing/_hero', ['settings' => $settings]); ?>
<?php $this->load->view('landing/_stats', ['stats' => $stats]); ?>
<div id="services">
    <?php $this->load->view('landing/_services', ['services' => $services, 'settings' => $settings]); ?>
</div>
<?php $this->load->view('landing/_why'); ?>
<div id="gallery">
    <?php $this->load->view('landing/_gallery', ['gallery' => $gallery]); ?>
</div>
<div id="testimonials">
    <?php $this->load->view('landing/_testimonials', ['testimonials' => $testimonials]); ?>
</div>
<div id="location">
    <?php $this->load->view('landing/_location', ['settings' => $settings]); ?>
</div>