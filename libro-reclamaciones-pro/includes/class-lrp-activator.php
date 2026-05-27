<?php
/**
 * Activación del plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Activator {

	public static function activate() {
		LRP_Database::install();
		update_option( 'lrp_db_version', LRP_DB_VERSION );

		// Configuración inicial si no existe.
		if ( false === get_option( 'lrp_settings' ) ) {
			add_option( 'lrp_settings', LRP_Settings::defaults() );
		}
		if ( false === get_option( 'lrp_emails' ) ) {
			add_option( 'lrp_emails', LRP_Emails::defaults() );
		}

		// Directorio de uploads para adjuntos.
		$uploads = wp_upload_dir();
		$dir     = trailingslashit( $uploads['basedir'] ) . 'lrp-attachments';
		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
			// Bloqueo de listado por seguridad.
			@file_put_contents( $dir . '/index.html', '' );
			@file_put_contents( $dir . '/.htaccess', "Options -Indexes\n" );
		}

		flush_rewrite_rules();
	}
}
