<?php
/**
 * Carga las plantillas propias del plugin para el archivo y el detalle de
 * evento, con posibilidad de sobrescritura desde el tema activo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EVT_Templates {

	/**
	 * Engancha el filtro de plantillas y el flush automático de rewrite
	 * cuando cambia la versión del plugin.
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'load' ), 20 );
		add_action( 'admin_init', array( __CLASS__, 'flush_on_version_change' ) );
	}

	/**
	 * Selecciona la plantilla del archivo o del detalle de evento.
	 *
	 * @param string $template Plantilla que iba a cargar WordPress.
	 * @return string
	 */
	public static function load( $template ) {
		if ( is_singular( EVT_CPT ) ) {
			$tpl = locate_template( array( 'single-evento.php' ) );
			if ( $tpl ) {
				return $tpl;
			}
			$plugin_tpl = EVT_DIR . 'templates/single-evento.php';
			if ( file_exists( $plugin_tpl ) ) {
				return $plugin_tpl;
			}
		}

		if ( is_post_type_archive( EVT_CPT ) ) {
			$tpl = locate_template( array( 'archive-evento.php' ) );
			if ( $tpl ) {
				return $tpl;
			}
			$plugin_tpl = EVT_DIR . 'templates/archive-evento.php';
			if ( file_exists( $plugin_tpl ) ) {
				return $plugin_tpl;
			}
		}

		return $template;
	}

	/**
	 * Refresca los rewrite rules una vez cuando cambia la versión del
	 * plugin, evitando pedir al usuario que guarde los enlaces permanentes.
	 */
	public static function flush_on_version_change() {
		if ( get_option( 'evt_version' ) === EVT_VERSION ) {
			return;
		}
		EVT_CPT_Registrar::register();
		flush_rewrite_rules();
		update_option( 'evt_version', EVT_VERSION );
	}
}
