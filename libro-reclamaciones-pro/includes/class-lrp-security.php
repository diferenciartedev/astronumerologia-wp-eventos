<?php
/**
 * Capa de seguridad del plugin.
 *
 * - Verificación de nonces.
 * - Sanitización masiva.
 * - Validación de archivos adjuntos.
 * - Rate limit simple por IP.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Security {

	const RATE_LIMIT_WINDOW   = 600;  // 10 minutos.
	const RATE_LIMIT_MAX_HITS = 5;    // 5 envíos por IP por ventana.

	/**
	 * Verifica un nonce o termina la ejecución.
	 */
	public static function verify_nonce( $nonce, $action ) {
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			wp_die(
				esc_html__( 'Token de seguridad inválido o expirado.', 'libro-reclamaciones-pro' ),
				esc_html__( 'Error de seguridad', 'libro-reclamaciones-pro' ),
				array( 'response' => 403 )
			);
		}
		return true;
	}

	/**
	 * Sanitiza un array de inputs según definición.
	 *
	 * @param array $input Datos de entrada.
	 * @param array $rules Mapa campo => tipo (text, textarea, email, int, float, date, url, key, html).
	 */
	public static function sanitize_array( array $input, array $rules ) {
		$out = array();
		foreach ( $rules as $field => $type ) {
			$value = isset( $input[ $field ] ) ? $input[ $field ] : '';
			$out[ $field ] = self::sanitize_value( $value, $type );
		}
		return $out;
	}

	public static function sanitize_value( $value, $type ) {
		if ( is_array( $value ) ) {
			return array_map( function( $v ) use ( $type ) {
				return self::sanitize_value( $v, $type );
			}, $value );
		}
		switch ( $type ) {
			case 'textarea':
				return sanitize_textarea_field( wp_unslash( $value ) );
			case 'email':
				return sanitize_email( wp_unslash( $value ) );
			case 'int':
				return (int) $value;
			case 'float':
				return (float) $value;
			case 'date':
				$d = sanitize_text_field( wp_unslash( $value ) );
				return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $d ) ? $d : '';
			case 'url':
				return esc_url_raw( wp_unslash( $value ) );
			case 'key':
				return sanitize_key( wp_unslash( $value ) );
			case 'bool':
				return (int) ( ! empty( $value ) );
			case 'html':
				return wp_kses_post( wp_unslash( $value ) );
			case 'text':
			default:
				return sanitize_text_field( wp_unslash( $value ) );
		}
	}

	/**
	 * Tipos MIME y extensiones permitidas para adjuntos.
	 */
	public static function allowed_mimes() {
		return apply_filters( 'lrp_allowed_mimes', array(
			'pdf'  => 'application/pdf',
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		) );
	}

	/**
	 * Tamaño máximo por archivo (5 MB por defecto).
	 */
	public static function max_file_size() {
		return apply_filters( 'lrp_max_file_size', 5 * 1024 * 1024 );
	}

	/**
	 * Valida un archivo subido.
	 *
	 * @return true|WP_Error
	 */
	public static function validate_upload( array $file ) {
		if ( empty( $file['name'] ) ) {
			return new WP_Error( 'lrp_empty_file', __( 'Archivo vacío.', 'libro-reclamaciones-pro' ) );
		}
		if ( ! empty( $file['error'] ) && UPLOAD_ERR_OK !== (int) $file['error'] ) {
			return new WP_Error( 'lrp_upload_error', __( 'Error al subir el archivo.', 'libro-reclamaciones-pro' ) );
		}
		if ( $file['size'] > self::max_file_size() ) {
			return new WP_Error( 'lrp_file_too_big', __( 'El archivo excede el tamaño máximo permitido.', 'libro-reclamaciones-pro' ) );
		}
		$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], self::allowed_mimes() );
		if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
			return new WP_Error( 'lrp_invalid_type', __( 'Tipo de archivo no permitido.', 'libro-reclamaciones-pro' ) );
		}
		return true;
	}

	/**
	 * Rate limit simple por IP (transient).
	 *
	 * @return bool true si pasa el límite (puede continuar).
	 */
	public static function rate_limit_check( $bucket = 'submit' ) {
		$ip = lrp_get_client_ip();
		if ( ! $ip ) {
			return true;
		}
		$key  = 'lrp_rl_' . $bucket . '_' . md5( $ip );
		$hits = (int) get_transient( $key );
		if ( $hits >= self::RATE_LIMIT_MAX_HITS ) {
			return false;
		}
		set_transient( $key, $hits + 1, self::RATE_LIMIT_WINDOW );
		return true;
	}

	/**
	 * Verifica honeypot anti-spam.
	 */
	public static function honeypot_passed( $field = 'lrp_website' ) {
		return empty( $_POST[ $field ] );
	}
}
