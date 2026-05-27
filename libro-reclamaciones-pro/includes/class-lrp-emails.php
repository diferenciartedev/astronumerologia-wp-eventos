<?php
/**
 * Notificaciones por correo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Emails {

	const OPTION = 'lrp_emails';

	public static function defaults() {
		return array(
			'recipients'         => get_option( 'admin_email' ),
			'subject_business'   => __( 'Nuevo reclamo recibido [{{claim_code}}]', 'libro-reclamaciones-pro' ),
			'body_business'      => self::default_body_business(),
			'subject_consumer'   => __( 'Confirmación de tu reclamo [{{claim_code}}] - {{business_name}}', 'libro-reclamaciones-pro' ),
			'body_consumer'      => self::default_body_consumer(),
			'send_business_copy' => 1,
			'send_consumer_copy' => 1,
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

	public static function sanitize( array $input ) {
		$rules = array(
			'recipients'         => 'text',
			'subject_business'   => 'text',
			'body_business'      => 'html',
			'subject_consumer'   => 'text',
			'body_consumer'      => 'html',
			'send_business_copy' => 'bool',
			'send_consumer_copy' => 'bool',
		);
		return LRP_Security::sanitize_array( $input, $rules );
	}

	/**
	 * Envía las notificaciones para un reclamo recién creado.
	 */
	public static function notify_new_claim( array $claim ) {
		$emails   = self::get();
		$settings = LRP_Settings::get();
		$vars     = self::build_vars( $claim, $settings );

		// Correo a la empresa.
		if ( ! empty( $emails['send_business_copy'] ) ) {
			$recipients = self::parse_recipients( $emails['recipients'] );
			if ( $recipients ) {
				$subject = self::replace_vars( $emails['subject_business'], $vars );
				$body    = self::replace_vars( $emails['body_business'], $vars );
				self::send( $recipients, $subject, $body );
			}
		}

		// Correo al consumidor.
		if ( ! empty( $emails['send_consumer_copy'] ) && ! empty( $settings['send_consumer_copy'] ) && ! empty( $claim['email'] ) ) {
			$subject = self::replace_vars( $emails['subject_consumer'], $vars );
			$body    = self::replace_vars( $emails['body_consumer'], $vars );
			self::send( $claim['email'], $subject, $body );
		}
	}

	public static function send( $to, $subject, $body ) {
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		return wp_mail( $to, $subject, $body, $headers );
	}

	public static function parse_recipients( $string ) {
		$parts = array_filter( array_map( 'trim', explode( ',', (string) $string ) ) );
		$valid = array();
		foreach ( $parts as $p ) {
			if ( is_email( $p ) ) {
				$valid[] = $p;
			}
		}
		return $valid;
	}

	public static function build_vars( array $claim, array $settings ) {
		return array(
			'{{claim_code}}'       => $claim['claim_code'],
			'{{consumer_name}}'    => $claim['consumer_name'],
			'{{document_number}}'  => $claim['document_number'],
			'{{claim_type}}'       => ucfirst( $claim['claim_type'] ),
			'{{claim_detail}}'     => nl2br( esc_html( $claim['claim_detail'] ) ),
			'{{consumer_request}}' => nl2br( esc_html( $claim['consumer_request'] ) ),
			'{{created_at}}'       => $claim['created_at'],
			'{{deadline_at}}'      => $claim['deadline_at'],
			'{{business_name}}'    => $settings['business_name'] ? $settings['business_name'] : $settings['trade_name'],
			'{{status}}'           => lrp_status_label( $claim['status'] ),
			'{{admin_url}}'        => lrp_admin_claim_url( $claim['id'] ),
			'{{phone}}'            => $claim['phone'],
			'{{email}}'            => $claim['email'],
		);
	}

	public static function replace_vars( $tpl, array $vars ) {
		return strtr( $tpl, $vars );
	}

	protected static function default_body_business() {
		return '<h2>Nuevo reclamo registrado</h2>
<p><strong>Código:</strong> {{claim_code}}<br>
<strong>Fecha:</strong> {{created_at}}<br>
<strong>Tipo:</strong> {{claim_type}}<br>
<strong>Estado:</strong> {{status}}<br>
<strong>Fecha límite:</strong> {{deadline_at}}</p>
<h3>Consumidor</h3>
<p><strong>Nombre:</strong> {{consumer_name}}<br>
<strong>Documento:</strong> {{document_number}}<br>
<strong>Teléfono:</strong> {{phone}}<br>
<strong>Correo:</strong> {{email}}</p>
<h3>Detalle</h3>
<p>{{claim_detail}}</p>
<h3>Pedido del consumidor</h3>
<p>{{consumer_request}}</p>
<p><a href="{{admin_url}}">Ver en el panel administrativo</a></p>';
	}

	protected static function default_body_consumer() {
		return '<h2>Hemos recibido tu reclamo</h2>
<p>Hola {{consumer_name}}, registramos tu reclamo correctamente.</p>
<p><strong>Código:</strong> {{claim_code}}<br>
<strong>Fecha de registro:</strong> {{created_at}}<br>
<strong>Tipo:</strong> {{claim_type}}</p>
<h3>Resumen</h3>
<p>{{claim_detail}}</p>
<p>Te responderemos dentro del plazo legal correspondiente. Si necesitas hacer seguimiento, comunícate con nosotros referenciando el código <strong>{{claim_code}}</strong>.</p>
<p>{{business_name}}</p>';
	}
}
