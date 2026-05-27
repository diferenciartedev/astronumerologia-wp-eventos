<?php
/**
 * Plugin Name:       Libro de Reclamaciones PRO
 * Plugin URI:        https://machadata.com/libro-reclamaciones-pro
 * Description:       Libro de Reclamaciones Digital profesional para WordPress. Configurable por rubro, con plantillas, notificaciones por correo, panel de gestión y exportación CSV. Preparado para Perú y modular para otros países.
 * Version:           1.0.0
 * Author:            MachaData · Carlos Macha
 * Author URI:        https://machadata.com/
 * Text Domain:       libro-reclamaciones-pro
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LRP_VERSION', '1.0.0' );
define( 'LRP_FILE', __FILE__ );
define( 'LRP_BASENAME', plugin_basename( __FILE__ ) );
define( 'LRP_DIR', plugin_dir_path( __FILE__ ) );
define( 'LRP_URL', plugin_dir_url( __FILE__ ) );
define( 'LRP_SLUG', 'libro-reclamaciones-pro' );
define( 'LRP_TEXTDOMAIN', 'libro-reclamaciones-pro' );
define( 'LRP_DB_VERSION', '1.0.0' );

// Includes núcleo.
require_once LRP_DIR . 'includes/helpers.php';
require_once LRP_DIR . 'includes/class-lrp-security.php';
require_once LRP_DIR . 'includes/class-lrp-database.php';
require_once LRP_DIR . 'includes/class-lrp-templates.php';
require_once LRP_DIR . 'includes/class-lrp-settings.php';
require_once LRP_DIR . 'includes/class-lrp-claims.php';
require_once LRP_DIR . 'includes/class-lrp-emails.php';
require_once LRP_DIR . 'includes/class-lrp-export.php';
require_once LRP_DIR . 'includes/class-lrp-pdf.php';
require_once LRP_DIR . 'includes/class-lrp-activator.php';
require_once LRP_DIR . 'includes/class-lrp-deactivator.php';

// Admin y público.
require_once LRP_DIR . 'admin/class-lrp-admin.php';
require_once LRP_DIR . 'public/class-lrp-public.php';

/**
 * Carga del text domain para traducciones.
 */
function lrp_load_textdomain() {
	load_plugin_textdomain( LRP_TEXTDOMAIN, false, dirname( LRP_BASENAME ) . '/languages' );
}
add_action( 'plugins_loaded', 'lrp_load_textdomain' );

/**
 * Arranque del plugin.
 */
function lrp_boot() {
	LRP_Admin::init();
	LRP_Public::init();
	LRP_Claims::init();
	LRP_Export::init();
}
add_action( 'plugins_loaded', 'lrp_boot', 20 );

/**
 * Activación: crea tablas y opciones iniciales.
 */
register_activation_hook( __FILE__, array( 'LRP_Activator', 'activate' ) );

/**
 * Desactivación: limpieza segura.
 */
register_deactivation_hook( __FILE__, array( 'LRP_Deactivator', 'deactivate' ) );

/**
 * Verifica versión de DB en cada carga del admin (idempotente).
 */
function lrp_check_db_version() {
	if ( get_option( 'lrp_db_version' ) !== LRP_DB_VERSION ) {
		LRP_Database::install();
		update_option( 'lrp_db_version', LRP_DB_VERSION );
	}
}
add_action( 'admin_init', 'lrp_check_db_version' );
