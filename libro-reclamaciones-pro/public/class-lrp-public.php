<?php
/**
 * Frontend: shortcode, render del formulario, manejo del envío.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Public {

	public static function init() {
		add_shortcode( 'libro_reclamaciones_pro', array( __CLASS__, 'shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_post_nopriv_lrp_submit', array( __CLASS__, 'handle_submit' ) );
		add_action( 'admin_post_lrp_submit', array( __CLASS__, 'handle_submit' ) );
	}

	public static function enqueue_assets() {
		wp_register_style( 'lrp-public', LRP_URL . 'public/css/public.css', array(), LRP_VERSION );
		wp_register_script( 'lrp-public', LRP_URL . 'public/js/public.js', array(), LRP_VERSION, true );
	}

	public static function shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'template_id' => '',
		), $atts, 'libro_reclamaciones_pro' );

		wp_enqueue_style( 'lrp-public' );
		wp_enqueue_script( 'lrp-public' );

		// Manejar mensajes post-envío.
		if ( isset( $_GET['lrp_success'] ) && ! empty( $_GET['lrp_code'] ) ) {
			return self::render_success( sanitize_text_field( wp_unslash( $_GET['lrp_code'] ) ) );
		}

		return self::render_form( array(
			'template_id' => $atts['template_id'] ?: LRP_Settings::get( 'template_id' ),
			'preview'     => false,
			'errors'      => isset( $_GET['lrp_errors'] ) ? array_map( 'sanitize_text_field', (array) $_GET['lrp_errors'] ) : array(),
		) );
	}

	/**
	 * Renderiza el formulario público.
	 *
	 * @param array $args { template_id, preview, errors }
	 */
	public static function render_form( array $args = array() ) {
		$settings    = LRP_Settings::get();
		$template_id = ! empty( $args['template_id'] ) ? $args['template_id'] : $settings['template_id'];
		$preview     = ! empty( $args['preview'] );
		$errors      = isset( $args['errors'] ) ? (array) $args['errors'] : array();

		$template = LRP_Templates::get( $template_id );
		if ( ! $template ) {
			$template_id = 'general';
			$template    = LRP_Templates::get( 'general' );
		}

		ob_start();
		include LRP_DIR . 'public/views/claim-form.php';
		return ob_get_clean();
	}

	public static function render_success( $code ) {
		$settings = LRP_Settings::get();
		ob_start();
		include LRP_DIR . 'public/views/success.php';
		return ob_get_clean();
	}

	/**
	 * Maneja el envío del formulario.
	 */
	public static function handle_submit() {
		// Nonce.
		LRP_Security::verify_nonce( $_POST['_wpnonce'] ?? '', 'lrp_submit' );

		// Honeypot.
		if ( ! LRP_Security::honeypot_passed() ) {
			wp_die( esc_html__( 'Solicitud bloqueada.', 'libro-reclamaciones-pro' ), 400 );
		}

		// Rate limit.
		if ( ! LRP_Security::rate_limit_check( 'submit' ) ) {
			wp_die( esc_html__( 'Demasiados intentos. Inténtalo nuevamente en unos minutos.', 'libro-reclamaciones-pro' ), 429 );
		}

		$settings    = LRP_Settings::get();
		$template_id = isset( $_POST['template_id'] ) ? sanitize_key( $_POST['template_id'] ) : $settings['template_id'];
		if ( ! LRP_Templates::exists( $template_id ) ) {
			$template_id = $settings['template_id'];
		}

		$rules = array(
			'consumer_name'     => 'text',
			'document_type'     => 'key',
			'document_number'   => 'text',
			'phone'             => 'text',
			'email'             => 'email',
			'address'           => 'textarea',
			'department'        => 'text',
			'province'          => 'text',
			'district'          => 'text',
			'is_minor'          => 'bool',
			'guardian_name'     => 'text',
			'guardian_document' => 'text',
			'good_type'         => 'key',
			'product_or_service'=> 'text',
			'amount'            => 'float',
			'receipt_number'    => 'text',
			'purchase_date'     => 'date',
			'channel'           => 'key',
			'claim_type'        => 'key',
			'claim_detail'      => 'textarea',
			'consumer_request'  => 'textarea',
		);
		$data = LRP_Security::sanitize_array( $_POST, $rules );

		// Validación mínima.
		$errors = array();
		foreach ( array( 'consumer_name', 'document_type', 'document_number', 'phone', 'email', 'good_type', 'product_or_service', 'claim_type', 'claim_detail', 'consumer_request' ) as $req ) {
			if ( empty( $data[ $req ] ) ) {
				$errors[] = $req;
			}
		}
		if ( ! is_email( $data['email'] ) ) {
			$errors[] = 'email';
		}
		if ( empty( $_POST['accept_truth'] ) || empty( $_POST['accept_privacy'] ) ) {
			$errors[] = 'accept';
		}

		if ( $errors ) {
			$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
			$redirect = add_query_arg( array( 'lrp_errors' => $errors ), $redirect );
			wp_safe_redirect( $redirect );
			exit;
		}

		// Datos adicionales según plantilla.
		$additional = array();
		$template   = LRP_Templates::get( $template_id );
		if ( $template && ! empty( $template['fields'] ) ) {
			foreach ( $template['fields'] as $f ) {
				$name = $f['name'];
				if ( isset( $_POST['extra'][ $name ] ) ) {
					$type = isset( $f['type'] ) ? $f['type'] : 'text';
					$map  = array(
						'textarea' => 'textarea',
						'number'   => 'float',
						'date'     => 'date',
						'select'   => 'text',
					);
					$rule = isset( $map[ $type ] ) ? $map[ $type ] : 'text';
					$additional[ $name ] = LRP_Security::sanitize_value( $_POST['extra'][ $name ], $rule );
				}
			}
		}
		$data['template_id']     = $template_id;
		$data['additional_data'] = $additional;

		$claim_id = LRP_Claims::create( $data );
		if ( is_wp_error( $claim_id ) ) {
			wp_die( esc_html( $claim_id->get_error_message() ), 500 );
		}

		// Adjuntos.
		if ( ! empty( $settings['allow_attachments'] ) && ! empty( $_FILES['attachments']['name'] ) ) {
			self::handle_attachments( $claim_id, $_FILES['attachments'] );
		}

		// Notificaciones.
		$claim = LRP_Claims::get( $claim_id );
		LRP_Emails::notify_new_claim( $claim );

		// Redirección a éxito.
		$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
		$redirect = remove_query_arg( array( 'lrp_errors' ), $redirect );
		$redirect = add_query_arg( array(
			'lrp_success' => 1,
			'lrp_code'    => $claim['claim_code'],
		), $redirect );
		wp_safe_redirect( $redirect );
		exit;
	}

	protected static function handle_attachments( $claim_id, $files ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$count = count( $files['name'] );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( empty( $files['name'][ $i ] ) ) {
				continue;
			}
			$single = array(
				'name'     => $files['name'][ $i ],
				'type'     => $files['type'][ $i ],
				'tmp_name' => $files['tmp_name'][ $i ],
				'error'    => $files['error'][ $i ],
				'size'     => $files['size'][ $i ],
			);

			$valid = LRP_Security::validate_upload( $single );
			if ( is_wp_error( $valid ) ) {
				continue;
			}

			$overrides = array(
				'test_form' => false,
				'mimes'     => LRP_Security::allowed_mimes(),
				'unique_filename_callback' => function( $dir, $name, $ext ) use ( $claim_id ) {
					return 'lrp-' . $claim_id . '-' . wp_generate_password( 6, false ) . $ext;
				},
			);

			// Subdirectorio dedicado.
			add_filter( 'upload_dir', array( __CLASS__, 'attachments_upload_dir' ) );
			$moved = wp_handle_upload( $single, $overrides );
			remove_filter( 'upload_dir', array( __CLASS__, 'attachments_upload_dir' ) );

			if ( ! empty( $moved['file'] ) && empty( $moved['error'] ) ) {
				LRP_Claims::save_attachment( $claim_id, array(
					'file' => $moved['file'],
					'url'  => $moved['url'],
					'name' => $single['name'],
					'type' => $moved['type'],
					'size' => $single['size'],
				) );
			}
		}
	}

	public static function attachments_upload_dir( $dirs ) {
		$dirs['subdir'] = '/lrp-attachments';
		$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
		$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
		return $dirs;
	}
}
