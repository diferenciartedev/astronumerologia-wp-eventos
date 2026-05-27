<?php
/**
 * Lógica central de reclamos: crear, listar, leer, actualizar estado.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Claims {

	public static function init() {
		// Reservado para hooks futuros (cron de vencimientos, etc.).
	}

	/**
	 * Crea un nuevo reclamo. Recibe datos ya sanitizados.
	 *
	 * @param array $data
	 * @return int|WP_Error  ID del reclamo o error.
	 */
	public static function create( array $data ) {
		global $wpdb;

		$correlative = LRP_Database::next_correlative();
		$year        = (int) date_i18n( 'Y' );
		$code        = lrp_generate_claim_code( $correlative, $year );

		$settings = LRP_Settings::get();
		$deadline = lrp_calculate_deadline( (int) $settings['response_days'] );

		$now = current_time( 'mysql' );

		$row = array(
			'book_id'            => 0,
			'claim_code'         => $code,
			'claim_number'       => $correlative,
			'claim_type'         => isset( $data['claim_type'] ) ? $data['claim_type'] : 'reclamo',
			'template_id'        => isset( $data['template_id'] ) ? $data['template_id'] : $settings['template_id'],
			'consumer_name'      => isset( $data['consumer_name'] ) ? $data['consumer_name'] : '',
			'document_type'      => isset( $data['document_type'] ) ? $data['document_type'] : '',
			'document_number'    => isset( $data['document_number'] ) ? $data['document_number'] : '',
			'email'              => isset( $data['email'] ) ? $data['email'] : '',
			'phone'              => isset( $data['phone'] ) ? $data['phone'] : '',
			'address'            => isset( $data['address'] ) ? $data['address'] : '',
			'department'         => isset( $data['department'] ) ? $data['department'] : '',
			'province'           => isset( $data['province'] ) ? $data['province'] : '',
			'district'           => isset( $data['district'] ) ? $data['district'] : '',
			'is_minor'           => ! empty( $data['is_minor'] ) ? 1 : 0,
			'guardian_name'      => isset( $data['guardian_name'] ) ? $data['guardian_name'] : '',
			'guardian_document'  => isset( $data['guardian_document'] ) ? $data['guardian_document'] : '',
			'product_or_service' => isset( $data['product_or_service'] ) ? $data['product_or_service'] : '',
			'good_type'          => isset( $data['good_type'] ) ? $data['good_type'] : '',
			'amount'             => isset( $data['amount'] ) ? (float) $data['amount'] : 0,
			'receipt_number'     => isset( $data['receipt_number'] ) ? $data['receipt_number'] : '',
			'purchase_date'      => ! empty( $data['purchase_date'] ) ? $data['purchase_date'] : null,
			'channel'            => isset( $data['channel'] ) ? $data['channel'] : '',
			'claim_detail'       => isset( $data['claim_detail'] ) ? $data['claim_detail'] : '',
			'consumer_request'   => isset( $data['consumer_request'] ) ? $data['consumer_request'] : '',
			'additional_data'    => isset( $data['additional_data'] ) ? wp_json_encode( $data['additional_data'] ) : '',
			'status'             => 'new',
			'deadline_at'        => $deadline,
			'ip_address'         => ! empty( $settings['capture_ip'] ) ? lrp_get_client_ip() : '',
			'user_agent'         => lrp_get_user_agent(),
			'created_at'         => $now,
			'updated_at'         => $now,
		);

		$formats = array(
			'%d', '%s', '%d', '%s', '%s',
			'%s', '%s', '%s', '%s', '%s',
			'%s', '%s', '%s', '%s', '%d',
			'%s', '%s', '%s', '%s', '%f',
			'%s', '%s', '%s', '%s', '%s',
			'%s', '%s', '%s', '%s', '%s',
			'%s', '%s', '%s',
		);

		$ok = $wpdb->insert( LRP_Database::table( 'claims' ), $row, $formats );
		if ( false === $ok ) {
			return new WP_Error( 'lrp_db_error', __( 'No se pudo guardar el reclamo.', 'libro-reclamaciones-pro' ) );
		}
		$claim_id = (int) $wpdb->insert_id;

		self::log( $claim_id, 'created', '', 'new', 0 );

		return $claim_id;
	}

	public static function get( $id ) {
		global $wpdb;
		$id    = absint( $id );
		$table = LRP_Database::table( 'claims' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
	}

	public static function get_by_code( $code ) {
		global $wpdb;
		$table = LRP_Database::table( 'claims' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE claim_code = %s", $code ), ARRAY_A );
	}

	/**
	 * Lista de reclamos con filtros y paginación.
	 */
	public static function query( array $args = array() ) {
		global $wpdb;
		$table = LRP_Database::table( 'claims' );

		$defaults = array(
			'status'      => '',
			'type'        => '',
			'template_id' => '',
			'date_from'   => '',
			'date_to'     => '',
			'search'      => '',
			'orderby'     => 'created_at',
			'order'       => 'DESC',
			'per_page'    => 20,
			'paged'       => 1,
		);
		$args = wp_parse_args( $args, $defaults );

		$where = ' WHERE 1=1';
		$params = array();

		if ( $args['status'] ) {
			$where  .= ' AND status = %s';
			$params[] = $args['status'];
		}
		if ( $args['type'] ) {
			$where  .= ' AND claim_type = %s';
			$params[] = $args['type'];
		}
		if ( $args['template_id'] ) {
			$where  .= ' AND template_id = %s';
			$params[] = $args['template_id'];
		}
		if ( $args['date_from'] ) {
			$where  .= ' AND created_at >= %s';
			$params[] = $args['date_from'] . ' 00:00:00';
		}
		if ( $args['date_to'] ) {
			$where  .= ' AND created_at <= %s';
			$params[] = $args['date_to'] . ' 23:59:59';
		}
		if ( $args['search'] ) {
			$like    = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$where  .= ' AND ( claim_code LIKE %s OR consumer_name LIKE %s OR document_number LIKE %s OR email LIKE %s )';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		$orderby = in_array( $args['orderby'], array( 'created_at', 'id', 'claim_code', 'status' ), true ) ? $args['orderby'] : 'created_at';
		$order   = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
		$per     = max( 1, (int) $args['per_page'] );
		$page    = max( 1, (int) $args['paged'] );
		$offset  = ( $page - 1 ) * $per;

		// Total.
		$count_sql = "SELECT COUNT(*) FROM {$table}" . $where;
		$total     = $params ? (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) ) : (int) $wpdb->get_var( $count_sql );

		// Items.
		$sql = "SELECT * FROM {$table}" . $where . " ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
		$sql_params = array_merge( $params, array( $per, $offset ) );
		$items = $wpdb->get_results( $wpdb->prepare( $sql, $sql_params ), ARRAY_A );

		return array(
			'items'    => $items ? $items : array(),
			'total'    => $total,
			'per_page' => $per,
			'paged'    => $page,
			'pages'    => (int) ceil( $total / $per ),
		);
	}

	/**
	 * Cambia el estado de un reclamo con log.
	 */
	public static function update_status( $id, $new_status, $user_id = 0 ) {
		global $wpdb;
		$claim = self::get( $id );
		if ( ! $claim ) {
			return new WP_Error( 'lrp_not_found', __( 'Reclamo no encontrado.', 'libro-reclamaciones-pro' ) );
		}
		$statuses = lrp_get_statuses();
		if ( ! isset( $statuses[ $new_status ] ) ) {
			return new WP_Error( 'lrp_invalid_status', __( 'Estado inválido.', 'libro-reclamaciones-pro' ) );
		}
		if ( $claim['status'] === $new_status ) {
			return true;
		}
		$update = array(
			'status'     => $new_status,
			'updated_at' => current_time( 'mysql' ),
		);
		if ( 'answered' === $new_status ) {
			$update['answered_at'] = current_time( 'mysql' );
		}
		$wpdb->update( LRP_Database::table( 'claims' ), $update, array( 'id' => (int) $id ), null, array( '%d' ) );

		self::log( $id, 'status_change', $claim['status'], $new_status, $user_id );

		return true;
	}

	/**
	 * Guarda un registro en wp_lrp_claim_logs.
	 */
	public static function log( $claim_id, $action, $previous = '', $new = '', $user_id = 0, $data = array() ) {
		global $wpdb;
		$wpdb->insert(
			LRP_Database::table( 'claim_logs' ),
			array(
				'claim_id'        => (int) $claim_id,
				'action'          => sanitize_text_field( $action ),
				'previous_status' => sanitize_key( $previous ),
				'new_status'      => sanitize_key( $new ),
				'user_id'         => (int) $user_id,
				'ip_address'      => lrp_get_client_ip(),
				'data'            => $data ? wp_json_encode( $data ) : '',
				'created_at'      => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
		);
	}

	public static function get_logs( $claim_id ) {
		global $wpdb;
		$table = LRP_Database::table( 'claim_logs' );
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE claim_id = %d ORDER BY created_at DESC", (int) $claim_id ), ARRAY_A );
	}

	public static function get_attachments( $claim_id ) {
		global $wpdb;
		$table = LRP_Database::table( 'claim_attachments' );
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE claim_id = %d ORDER BY created_at ASC", (int) $claim_id ), ARRAY_A );
	}

	public static function save_attachment( $claim_id, array $file_data ) {
		global $wpdb;
		$wpdb->insert(
			LRP_Database::table( 'claim_attachments' ),
			array(
				'claim_id'   => (int) $claim_id,
				'file_path'  => $file_data['file'],
				'file_url'   => $file_data['url'],
				'file_name'  => $file_data['name'],
				'mime_type'  => $file_data['type'],
				'size'       => (int) $file_data['size'],
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%d', '%s' )
		);
		return (int) $wpdb->insert_id;
	}

	/**
	 * Estadísticas para el dashboard.
	 */
	public static function stats() {
		global $wpdb;
		$table = LRP_Database::table( 'claims' );

		$total     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		$new       = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'new'" );
		$review    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'in_review'" );
		$answered  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'answered'" );
		$expired   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'expired' OR ( deadline_at < NOW() AND status NOT IN ('answered','closed','cancelled') )" );

		$month_start = date_i18n( 'Y-m-01 00:00:00' );
		$month       = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s", $month_start ) );

		// Tiempo promedio de respuesta (en horas).
		$avg_hours = (float) $wpdb->get_var( "SELECT AVG( TIMESTAMPDIFF( HOUR, created_at, answered_at ) ) FROM {$table} WHERE answered_at IS NOT NULL" );

		$by_template = $wpdb->get_results( "SELECT template_id, COUNT(*) AS total FROM {$table} GROUP BY template_id", ARRAY_A );

		return array(
			'total'       => $total,
			'new'         => $new,
			'in_review'   => $review,
			'answered'    => $answered,
			'expired'     => $expired,
			'month'       => $month,
			'avg_hours'   => round( $avg_hours, 1 ),
			'by_template' => $by_template ? $by_template : array(),
		);
	}
}
