<?php
/**
 * Gestión de configuración general del negocio.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Settings {

	const OPTION = 'lrp_settings';

	public static function defaults() {
		return array(
			'business_name'      => '',
			'trade_name'         => '',
			'ruc'                => '',
			'address'            => '',
			'phone'              => '',
			'email'              => get_option( 'admin_email' ),
			'website'            => home_url(),
			'logo_url'           => '',
			'industry'           => 'general',
			'template_id'        => 'general',
			'legal_text'         => __( 'Conforme al Código de Protección y Defensa del Consumidor, este establecimiento cuenta con un Libro de Reclamaciones a disposición del consumidor.', 'libro-reclamaciones-pro' ),
			'primary_color'      => '#1E40AF',
			'capture_ip'         => 1,
			'allow_attachments'  => 1,
			'send_consumer_copy' => 1,
			'response_days'      => 15,
			'book_page'          => '',
			'success_message'    => __( 'Tu reclamo ha sido registrado correctamente. Recibirás una copia en tu correo electrónico.', 'libro-reclamaciones-pro' ),
			'country'            => 'PE',
		);
	}

	public static function get( $key = null, $default = null ) {
		$opts = wp_parse_args( get_option( self::OPTION, array() ), self::defaults() );
		if ( null === $key ) {
			return $opts;
		}
		return isset( $opts[ $key ] ) ? $opts[ $key ] : $default;
	}

	public static function update( array $values ) {
		$current = self::get();
		$new     = array_merge( $current, $values );
		update_option( self::OPTION, $new );
		return $new;
	}

	/**
	 * Sanitiza el formulario completo de configuración.
	 */
	public static function sanitize( array $input ) {
		$rules = array(
			'business_name'      => 'text',
			'trade_name'         => 'text',
			'ruc'                => 'text',
			'address'            => 'textarea',
			'phone'              => 'text',
			'email'              => 'email',
			'website'            => 'url',
			'logo_url'           => 'url',
			'industry'           => 'key',
			'template_id'        => 'key',
			'legal_text'         => 'textarea',
			'primary_color'      => 'text',
			'capture_ip'         => 'bool',
			'allow_attachments'  => 'bool',
			'send_consumer_copy' => 'bool',
			'response_days'      => 'int',
			'book_page'          => 'url',
			'success_message'    => 'textarea',
			'country'            => 'text',
		);
		return LRP_Security::sanitize_array( $input, $rules );
	}
}
