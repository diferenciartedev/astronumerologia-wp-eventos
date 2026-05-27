<?php
/**
 * Helpers globales del plugin Libro de Reclamaciones PRO.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Devuelve los estados disponibles y su etiqueta legible.
 */
function lrp_get_statuses() {
	return array(
		'new'       => __( 'Nuevo', 'libro-reclamaciones-pro' ),
		'in_review' => __( 'En revisión', 'libro-reclamaciones-pro' ),
		'assigned'  => __( 'Derivado', 'libro-reclamaciones-pro' ),
		'answered'  => __( 'Respondido', 'libro-reclamaciones-pro' ),
		'closed'    => __( 'Cerrado', 'libro-reclamaciones-pro' ),
		'expired'   => __( 'Vencido', 'libro-reclamaciones-pro' ),
		'cancelled' => __( 'Anulado', 'libro-reclamaciones-pro' ),
	);
}

function lrp_status_label( $status ) {
	$statuses = lrp_get_statuses();
	return isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status;
}

function lrp_status_color( $status ) {
	$map = array(
		'new'       => '#1E40AF',
		'in_review' => '#F59E0B',
		'assigned'  => '#7C3AED',
		'answered'  => '#16A34A',
		'closed'    => '#374151',
		'expired'   => '#DC2626',
		'cancelled' => '#6B7280',
	);
	return isset( $map[ $status ] ) ? $map[ $status ] : '#374151';
}

/**
 * Devuelve los tipos de documento.
 */
function lrp_get_document_types() {
	return array(
		'DNI'       => __( 'DNI', 'libro-reclamaciones-pro' ),
		'CE'        => __( 'Carné de extranjería', 'libro-reclamaciones-pro' ),
		'PASAPORTE' => __( 'Pasaporte', 'libro-reclamaciones-pro' ),
		'RUC'       => __( 'RUC', 'libro-reclamaciones-pro' ),
	);
}

function lrp_get_channels() {
	return array(
		'presencial' => __( 'Presencial', 'libro-reclamaciones-pro' ),
		'web'        => __( 'Web', 'libro-reclamaciones-pro' ),
		'whatsapp'   => __( 'WhatsApp', 'libro-reclamaciones-pro' ),
		'telefono'   => __( 'Teléfono', 'libro-reclamaciones-pro' ),
		'app'        => __( 'App', 'libro-reclamaciones-pro' ),
		'otro'       => __( 'Otro', 'libro-reclamaciones-pro' ),
	);
}

/**
 * Genera el código único de reclamo: LR-AAAA-NNNNNN.
 */
function lrp_generate_claim_code( $number, $year = null ) {
	$year = $year ? (int) $year : (int) date_i18n( 'Y' );
	return sprintf( 'LR-%04d-%06d', $year, (int) $number );
}

/**
 * Capability requerida para gestionar reclamos.
 */
function lrp_manage_cap() {
	return apply_filters( 'lrp_manage_capability', 'manage_options' );
}

/**
 * Devuelve la URL admin de un reclamo.
 */
function lrp_admin_claim_url( $claim_id ) {
	return admin_url( 'admin.php?page=lrp-claims&action=view&id=' . absint( $claim_id ) );
}

/**
 * Calcula fecha límite de respuesta (días hábiles aproximados, MVP).
 */
function lrp_calculate_deadline( $days = 15, $from = null ) {
	$from = $from ? strtotime( $from ) : current_time( 'timestamp' );
	$count = 0;
	$ts    = $from;
	while ( $count < $days ) {
		$ts = strtotime( '+1 day', $ts );
		$dow = (int) date( 'N', $ts );
		if ( $dow < 6 ) {
			$count++;
		}
	}
	return date( 'Y-m-d H:i:s', $ts );
}

/**
 * Obtiene IP del cliente con cuidado de proxies.
 */
function lrp_get_client_ip() {
	$candidates = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
	foreach ( $candidates as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
			if ( strpos( $ip, ',' ) !== false ) {
				$parts = explode( ',', $ip );
				$ip    = trim( $parts[0] );
			}
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
	}
	return '';
}

/**
 * Obtiene user agent saneado.
 */
function lrp_get_user_agent() {
	return isset( $_SERVER['HTTP_USER_AGENT'] )
		? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 500 )
		: '';
}
