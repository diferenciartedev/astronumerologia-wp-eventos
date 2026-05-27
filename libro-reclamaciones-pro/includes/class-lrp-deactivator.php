<?php
/**
 * Desactivación del plugin (no destructiva).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Deactivator {

	public static function deactivate() {
		// No se eliminan tablas ni opciones; sólo limpieza de cron/rewrites si los hubiera.
		flush_rewrite_rules();
	}
}
