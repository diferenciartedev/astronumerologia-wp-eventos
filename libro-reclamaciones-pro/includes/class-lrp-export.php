<?php
/**
 * Exportación CSV de reclamos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Export {

	public static function init() {
		add_action( 'admin_post_lrp_export_csv', array( __CLASS__, 'handle' ) );
	}

	public static function handle() {
		if ( ! current_user_can( lrp_manage_cap() ) ) {
			wp_die( esc_html__( 'No tienes permisos para exportar.', 'libro-reclamaciones-pro' ), 403 );
		}
		LRP_Security::verify_nonce( isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '', 'lrp_export' );

		$args = array(
			'status'      => isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '',
			'type'        => isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : '',
			'template_id' => isset( $_GET['template_id'] ) ? sanitize_key( $_GET['template_id'] ) : '',
			'date_from'   => isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '',
			'date_to'     => isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '',
			'search'      => isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '',
			'per_page'    => 5000,
			'paged'       => 1,
		);

		$res = LRP_Claims::query( $args );

		$filename = 'reclamos-' . date( 'Ymd-His' ) . '.csv';

		nocache_headers();
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		$out = fopen( 'php://output', 'w' );
		// BOM para Excel.
		fwrite( $out, "\xEF\xBB\xBF" );

		$cols = array(
			'Código', 'Fecha', 'Nombre', 'Documento', 'Correo', 'Teléfono',
			'Tipo', 'Estado', 'Plantilla', 'Detalle', 'Pedido',
			'Fecha límite', 'Fecha de respuesta',
		);
		fputcsv( $out, $cols );

		foreach ( $res['items'] as $c ) {
			fputcsv( $out, array(
				$c['claim_code'],
				$c['created_at'],
				$c['consumer_name'],
				$c['document_type'] . ' ' . $c['document_number'],
				$c['email'],
				$c['phone'],
				$c['claim_type'],
				lrp_status_label( $c['status'] ),
				$c['template_id'],
				$c['claim_detail'],
				$c['consumer_request'],
				$c['deadline_at'],
				$c['answered_at'],
			) );
		}

		fclose( $out );
		exit;
	}
}
