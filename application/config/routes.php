<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Landing';
$route['404_override'] = 'Errors/error_404';
$route['translate_uri_dashes'] = FALSE;

// ── Public routes ───────────────────────────────────────────
$route['about']    = 'Landing/about';
$route['services'] = 'Landing/services';

// ── Auth routes ─────────────────────────────────────────────
$route['login']                    = 'Auth/login';
$route['register']                 = 'Auth/register';
$route['logout']                   = 'Auth/logout';
$route['forgot-password']          = 'Auth/forgot_password';
$route['reset-password/(:any)']    = 'Auth/reset_password/$1';

// ── Customer routes (prefix: my/) ───────────────────────────
$route['my/dashboard']             = 'Customer/index';
$route['my/booking']               = 'Customer/booking';
$route['my/booking/store']         = 'Customer/store_booking';
$route['my/booking/(:num)']        = 'Customer/booking_detail/$1';
$route['my/booking/cancel/(:num)'] = 'Customer/cancel_booking/$1';
$route['my/status']                = 'Customer/service_status';
$route['my/payment/(:num)']        = 'Customer/payment/$1';
$route['my/payment/upload/(:num)'] = 'Customer/upload_proof/$1';
$route['my/history']               = 'Customer/history';
$route['my/profile']               = 'Customer/profile';
$route['my/profile/update']        = 'Customer/update_profile';
$route['my/vehicles']              = 'Customer/vehicles';
$route['my/vehicles/store']        = 'Customer/store_vehicle';
$route['my/vehicles/edit/(:num)']   = 'Customer/edit_vehicle/$1';
$route['my/vehicles/update/(:num)'] = 'Customer/update_vehicle/$1';
$route['my/vehicles/delete/(:num)'] = 'Customer/delete_vehicle/$1';
$route['my/profile/change-password'] = 'Customer/change_password';

// ── Admin routes (prefix: admin/) ───────────────────────────
$route['admin']                          = 'Admin/index';
$route['admin/queue']                    = 'Admin/queue';
$route['admin/queue/update-status']      = 'Admin/update_status';
$route['admin/bookings']                 = 'Admin/bookings';
$route['admin/bookings/(:num)']          = 'Admin/booking_detail/$1';
$route['admin/payments']                 = 'Admin/payments';
$route['admin/payments/confirm/(:num)']  = 'Admin/confirm_payment/$1';
$route['admin/payments/reject/(:num)']   = 'Admin/reject_payment/$1';
$route['admin/stock']                    = 'Admin/stock';
$route['admin/stock/store']              = 'Admin/store_part';
$route['admin/stock/(:num)']             = 'Admin/stock_detail/$1';
$route['admin/stock/mutasi/(:num)']      = 'Admin/stock_mutasi/$1';
$route['admin/reports']                  = 'Admin/reports';
$route['admin/reports/daily']            = 'Admin/report_daily';
$route['admin/reports/monthly']          = 'Admin/report_monthly';
$route['admin/reports/yearly']           = 'Admin/report_yearly';
$route['admin/reports/export']           = 'Admin/report_export';
$route['admin/users']                    = 'Admin/users';
$route['admin/users/store']              = 'Admin/store_user';
$route['admin/users/(:num)']             = 'Admin/user_detail/$1';
$route['admin/settings']                 = 'Admin/settings';
$route['admin/settings/update']          = 'Admin/update_settings';

// ── Admin booking actions ────────────────────────────────────
$route['admin/booking/(:num)']                   = 'Admin/booking_detail/$1';
$route['admin/booking/(:num)/status']            = 'Admin/update_booking_status/$1';
$route['admin/booking/(:num)/mechanic-note']     = 'Admin/add_mechanic_note/$1';
$route['admin/booking/(:num)/add-order']         = 'Admin/add_service_order/$1';
$route['admin/booking/(:num)/generate-invoice']  = 'Admin/generate_invoice/$1';
$route['admin/service-order/(:num)/delete']      = 'Admin/delete_service_order/$1';
$route['admin/payment/(:num)/confirm']           = 'Admin/confirm_payment/$1';
$route['admin/payment/(:num)/reject']            = 'Admin/reject_payment/$1';

// ── Admin spare parts (slug: spare-parts) ───────────────────
$route['admin/spare-parts']                      = 'Admin/spare_parts';
$route['admin/spare-parts/store']                = 'Admin/store_part';
$route['admin/spare-parts/(:num)/update']        = 'Admin/update_part/$1';
$route['admin/spare-parts/(:num)/adjust-stock']  = 'Admin/adjust_stock/$1';

// ── Admin users actions ──────────────────────────────────────
$route['admin/users/(:num)/toggle']              = 'Admin/toggle_user/$1';
$route['admin/users/(:num)']                     = 'Admin/user_detail/$1';

// ── Admin services ────────────────────────────────────────────
$route['admin/services']                         = 'Admin/services';
$route['admin/services/store']                   = 'Admin/store_service';
$route['admin/services/(:num)/update']           = 'Admin/update_service/$1';

// ── Admin gallery ─────────────────────────────────────────────
$route['admin/gallery']                          = 'Admin/gallery';
$route['admin/gallery/upload']                   = 'Admin/upload_gallery';
$route['admin/gallery/(:num)/delete']            = 'Admin/delete_gallery/$1';

// ── Admin reports (AJAX data endpoints) — already defined above, no duplicates

// ── Admin settings save ───────────────────────────────────────
$route['admin/settings/save']                    = 'Admin/save_settings';

// ── Admin invoice ─────────────────────────────────────────────
$route['admin/invoice/(:num)/print']             = 'Admin/invoice_print/$1';
$route['admin/invoice/(:num)/pdf']               = 'Admin/invoice_pdf/$1';

// ── Alias routes for backward compat (admin/stock → spare_parts) ─────────────────────────
$route['admin/stock/(:num)/adjust']              = 'Admin/adjust_stock/$1';

// ── AJAX / API routes ───────────────────────────────────────
$route['api/notifications']              = 'Api/notifications';
$route['api/notifications/read/(:num)']  = 'Api/read_notification/$1';
$route['api/notifications/read-all']     = 'Api/read_all_notifications';
$route['api/booking/check-slots']        = 'Api/check_slots';
$route['api/booking/status/(:any)']      = 'Api/booking_status/$1';
$route['api/stock/low']                  = 'Api/low_stock';
$route['api/invoice/(:num)']             = 'Api/download_invoice/$1';
