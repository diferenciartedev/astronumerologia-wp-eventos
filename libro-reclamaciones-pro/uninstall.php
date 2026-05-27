<?php
/**
 * Desinstalación del plugin Libro de Reclamaciones PRO.
 * Elimina opciones. Las tablas se mantienen para preservar datos legales,
 * salvo que el administrador active la opción de borrado total.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$full_delete = get_option( 'lrp_uninstall_delete_data', 0 );

delete_option( 'lrp_settings' );
delete_option( 'lrp_emails' );
delete_option( 'lrp_db_version' );
delete_option( 'lrp_correlative' );
delete_option( 'lrp_uninstall_delete_data' );

if ( $full_delete ) {
	$tables = array(
		$wpdb->prefix . 'lrp_books',
		$wpdb->prefix . 'lrp_claims',
		$wpdb->prefix . 'lrp_claim_responses',
		$wpdb->prefix . 'lrp_claim_attachments',
		$wpdb->prefix . 'lrp_claim_logs',
	);
	foreach ( $tables as $table ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
	}
}
