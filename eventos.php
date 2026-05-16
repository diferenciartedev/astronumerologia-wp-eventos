<?php
/**
 * Plugin Name:       Eventos · Astronumerología
 * Plugin URI:        https://astronumerologia.com/
 * Description:       Calendario tipo lista de eventos para Astronumerología. Gestiona eventos de este mes, próximos y anteriores, con modalidad, costo, aforo, cupo y contacto por WhatsApp. Incluye shortcode y widget de Elementor.
 * Version:           1.0.0
 * Author:            Astronumerología · Carlos Berrospi
 * Text Domain:       eventos-astro
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * License:           GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EVT_VERSION', '1.0.0' );
define( 'EVT_FILE', __FILE__ );
define( 'EVT_DIR', plugin_dir_path( __FILE__ ) );
define( 'EVT_URL', plugin_dir_url( __FILE__ ) );
define( 'EVT_CPT', 'evento' );

require_once EVT_DIR . 'includes/helpers.php';
require_once EVT_DIR . 'includes/class-astro-components.php';
require_once EVT_DIR . 'includes/class-evt-cpt.php';
require_once EVT_DIR . 'includes/class-evt-metabox.php';
require_once EVT_DIR . 'includes/class-evt-admin.php';
require_once EVT_DIR . 'includes/class-evt-settings.php';
require_once EVT_DIR . 'includes/class-evt-shortcode.php';
require_once EVT_DIR . 'includes/class-evt-elementor.php';

/**
 * Arranca el plugin una vez que WordPress ha cargado todos los plugins.
 */
function evt_init() {
	Astro_Components::init();
	EVT_CPT_Registrar::init();
	EVT_Metabox::init();
	EVT_Admin::init();
	EVT_Settings::init();
	EVT_Shortcode::init();
	EVT_Elementor::init();
}
add_action( 'plugins_loaded', 'evt_init' );

/**
 * Activación: registra el CPT y refresca los enlaces permanentes.
 */
function evt_activate() {
	EVT_CPT_Registrar::register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'evt_activate' );

/**
 * Desactivación: refresca los enlaces permanentes.
 */
function evt_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'evt_deactivate' );
