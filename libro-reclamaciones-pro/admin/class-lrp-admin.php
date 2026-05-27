<?php
/**
 * Admin: menús, vistas y manejo de acciones POST/GET del panel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		add_action( 'admin_post_lrp_save_settings', array( __CLASS__, 'save_settings' ) );
		add_action( 'admin_post_lrp_save_emails', array( __CLASS__, 'save_emails' ) );
		add_action( 'admin_post_lrp_update_status', array( __CLASS__, 'update_status' ) );
		add_action( 'admin_post_lrp_delete_claim', array( __CLASS__, 'delete_claim' ) );
		add_action( 'admin_post_lrp_send_test', array( __CLASS__, 'send_test' ) );
		add_action( 'admin_post_lrp_view_pdf', array( __CLASS__, 'view_pdf' ) );
	}

	public static function register_menu() {
		$cap = lrp_manage_cap();

		add_menu_page(
			__( 'Libro de Reclamaciones', 'libro-reclamaciones-pro' ),
			__( 'Libro de Reclamaciones', 'libro-reclamaciones-pro' ),
			$cap,
			'lrp-dashboard',
			array( __CLASS__, 'render_dashboard' ),
			'dashicons-book-alt',
			26
		);

		add_submenu_page( 'lrp-dashboard', __( 'Dashboard', 'libro-reclamaciones-pro' ), __( 'Dashboard', 'libro-reclamaciones-pro' ), $cap, 'lrp-dashboard', array( __CLASS__, 'render_dashboard' ) );
		add_submenu_page( 'lrp-dashboard', __( 'Reclamos', 'libro-reclamaciones-pro' ), __( 'Reclamos', 'libro-reclamaciones-pro' ), $cap, 'lrp-claims', array( __CLASS__, 'render_claims' ) );
		add_submenu_page( 'lrp-dashboard', __( 'Plantillas', 'libro-reclamaciones-pro' ), __( 'Plantillas', 'libro-reclamaciones-pro' ), $cap, 'lrp-templates', array( __CLASS__, 'render_templates' ) );
		add_submenu_page( 'lrp-dashboard', __( 'Configuración', 'libro-reclamaciones-pro' ), __( 'Configuración', 'libro-reclamaciones-pro' ), $cap, 'lrp-settings', array( __CLASS__, 'render_settings' ) );
		add_submenu_page( 'lrp-dashboard', __( 'Correos y notificaciones', 'libro-reclamaciones-pro' ), __( 'Correos', 'libro-reclamaciones-pro' ), $cap, 'lrp-emails', array( __CLASS__, 'render_emails' ) );
		add_submenu_page( 'lrp-dashboard', __( 'Vista previa', 'libro-reclamaciones-pro' ), __( 'Vista previa', 'libro-reclamaciones-pro' ), $cap, 'lrp-preview', array( __CLASS__, 'render_preview' ) );
		add_submenu_page( 'lrp-dashboard', __( 'Exportar', 'libro-reclamaciones-pro' ), __( 'Exportar', 'libro-reclamaciones-pro' ), $cap, 'lrp-export', array( __CLASS__, 'render_export' ) );
		add_submenu_page( 'lrp-dashboard', __( 'Opciones PRO', 'libro-reclamaciones-pro' ), __( 'Opciones PRO', 'libro-reclamaciones-pro' ), $cap, 'lrp-pro', array( __CLASS__, 'render_pro' ) );
	}

	public static function enqueue_assets( $hook ) {
		if ( false === strpos( (string) $hook, 'lrp-' ) && false === strpos( (string) $hook, 'libro-reclamaciones' ) ) {
			return;
		}
		wp_enqueue_style( 'lrp-admin', LRP_URL . 'admin/css/admin.css', array(), LRP_VERSION );
		wp_enqueue_script( 'lrp-admin', LRP_URL . 'admin/js/admin.js', array( 'jquery' ), LRP_VERSION, true );
	}

	// ===== Renderers =====

	public static function render_dashboard() {
		if ( ! current_user_can( lrp_manage_cap() ) ) {
			return;
		}
		$stats   = LRP_Claims::stats();
		$recent  = LRP_Claims::query( array( 'per_page' => 10 ) );
		include LRP_DIR . 'admin/views/dashboard.php';
	}

	public static function render_claims() {
		if ( ! current_user_can( lrp_manage_cap() ) ) {
			return;
		}
		$action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : '';
		if ( 'view' === $action && ! empty( $_GET['id'] ) ) {
			$claim = LRP_Claims::get( absint( $_GET['id'] ) );
			if ( ! $claim ) {
				echo '<div class="wrap"><h1>' . esc_html__( 'Reclamo no encontrado', 'libro-reclamaciones-pro' ) . '</h1></div>';
				return;
			}
			$logs        = LRP_Claims::get_logs( $claim['id'] );
			$attachments = LRP_Claims::get_attachments( $claim['id'] );
			include LRP_DIR . 'admin/views/claim-detail.php';
			return;
		}

		$args = array(
			'status'      => isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '',
			'type'        => isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : '',
			'template_id' => isset( $_GET['template_id'] ) ? sanitize_key( $_GET['template_id'] ) : '',
			'date_from'   => isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '',
			'date_to'     => isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '',
			'search'      => isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '',
			'paged'       => isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1,
		);
		$result = LRP_Claims::query( $args );
		include LRP_DIR . 'admin/views/claims-list.php';
	}

	public static function render_templates() {
		if ( ! current_user_can( lrp_manage_cap() ) ) { return; }
		$templates = LRP_Templates::all();
		include LRP_DIR . 'admin/views/templates.php';
	}

	public static function render_settings() {
		if ( ! current_user_can( lrp_manage_cap() ) ) { return; }
		$settings  = LRP_Settings::get();
		$templates = LRP_Templates::all();
		include LRP_DIR . 'admin/views/settings.php';
	}

	public static function render_emails() {
		if ( ! current_user_can( lrp_manage_cap() ) ) { return; }
		$emails = LRP_Emails::get();
		include LRP_DIR . 'admin/views/emails.php';
	}

	public static function render_preview() {
		if ( ! current_user_can( lrp_manage_cap() ) ) { return; }
		$settings  = LRP_Settings::get();
		$emails    = LRP_Emails::get();
		$templates = LRP_Templates::all();
		$template_id = isset( $_GET['template_id'] ) ? sanitize_key( $_GET['template_id'] ) : $settings['template_id'];
		include LRP_DIR . 'admin/views/preview.php';
	}

	public static function render_export() {
		if ( ! current_user_can( lrp_manage_cap() ) ) { return; }
		$templates = LRP_Templates::all();
		include LRP_DIR . 'admin/views/export.php';
	}

	public static function render_pro() {
		if ( ! current_user_can( lrp_manage_cap() ) ) { return; }
		include LRP_DIR . 'admin/views/pro.php';
	}

	// ===== Acciones POST =====

	public static function save_settings() {
		if ( ! current_user_can( lrp_manage_cap() ) ) {
			wp_die( esc_html__( 'No autorizado.', 'libro-reclamaciones-pro' ), 403 );
		}
		LRP_Security::verify_nonce( $_POST['_wpnonce'] ?? '', 'lrp_save_settings' );

		$data = LRP_Settings::sanitize( $_POST['lrp'] ?? array() );
		LRP_Settings::update( $data );

		wp_safe_redirect( add_query_arg( array( 'page' => 'lrp-settings', 'updated' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function save_emails() {
		if ( ! current_user_can( lrp_manage_cap() ) ) {
			wp_die( esc_html__( 'No autorizado.', 'libro-reclamaciones-pro' ), 403 );
		}
		LRP_Security::verify_nonce( $_POST['_wpnonce'] ?? '', 'lrp_save_emails' );

		$data = LRP_Emails::sanitize( $_POST['lrp'] ?? array() );
		LRP_Emails::update( $data );

		wp_safe_redirect( add_query_arg( array( 'page' => 'lrp-emails', 'updated' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function update_status() {
		if ( ! current_user_can( lrp_manage_cap() ) ) {
			wp_die( esc_html__( 'No autorizado.', 'libro-reclamaciones-pro' ), 403 );
		}
		LRP_Security::verify_nonce( $_POST['_wpnonce'] ?? '', 'lrp_update_status' );

		$id     = absint( $_POST['claim_id'] ?? 0 );
		$status = sanitize_key( $_POST['new_status'] ?? '' );
		LRP_Claims::update_status( $id, $status, get_current_user_id() );

		wp_safe_redirect( add_query_arg( array( 'page' => 'lrp-claims', 'action' => 'view', 'id' => $id, 'updated' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function delete_claim() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sólo los administradores pueden eliminar reclamos.', 'libro-reclamaciones-pro' ), 403 );
		}
		LRP_Security::verify_nonce( $_GET['_wpnonce'] ?? '', 'lrp_delete_claim' );

		global $wpdb;
		$id = absint( $_GET['id'] ?? 0 );
		if ( $id ) {
			$wpdb->delete( LRP_Database::table( 'claims' ), array( 'id' => $id ), array( '%d' ) );
			$wpdb->delete( LRP_Database::table( 'claim_attachments' ), array( 'claim_id' => $id ), array( '%d' ) );
			$wpdb->delete( LRP_Database::table( 'claim_logs' ), array( 'claim_id' => $id ), array( '%d' ) );
			$wpdb->delete( LRP_Database::table( 'claim_responses' ), array( 'claim_id' => $id ), array( '%d' ) );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'lrp-claims', 'deleted' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function send_test() {
		if ( ! current_user_can( lrp_manage_cap() ) ) {
			wp_die( esc_html__( 'No autorizado.', 'libro-reclamaciones-pro' ), 403 );
		}
		LRP_Security::verify_nonce( $_POST['_wpnonce'] ?? '', 'lrp_send_test' );

		$settings = LRP_Settings::get();
		$dummy = array(
			'id'                 => 0,
			'claim_code'         => 'LR-' . date( 'Y' ) . '-TEST01',
			'claim_type'         => 'reclamo',
			'consumer_name'      => 'Consumidor de Prueba',
			'document_number'    => '00000000',
			'email'              => sanitize_email( $_POST['test_email'] ?? get_option( 'admin_email' ) ),
			'phone'              => '999-999-999',
			'claim_detail'       => 'Este es un correo de prueba enviado desde el plugin Libro de Reclamaciones PRO.',
			'consumer_request'   => 'Verificar que llegue correctamente.',
			'created_at'         => current_time( 'mysql' ),
			'deadline_at'        => lrp_calculate_deadline( (int) $settings['response_days'] ),
			'status'             => 'new',
		);
		LRP_Emails::notify_new_claim( $dummy );

		wp_safe_redirect( add_query_arg( array( 'page' => 'lrp-preview', 'test_sent' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function view_pdf() {
		if ( ! current_user_can( lrp_manage_cap() ) ) {
			wp_die( esc_html__( 'No autorizado.', 'libro-reclamaciones-pro' ), 403 );
		}
		LRP_Security::verify_nonce( $_GET['_wpnonce'] ?? '', 'lrp_view_pdf' );

		$id    = absint( $_GET['id'] ?? 0 );
		$claim = LRP_Claims::get( $id );
		if ( ! $claim ) {
			wp_die( esc_html__( 'Reclamo no encontrado.', 'libro-reclamaciones-pro' ), 404 );
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo LRP_PDF::render( $claim );
		exit;
	}
}
