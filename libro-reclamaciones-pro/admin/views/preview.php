<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $settings, $emails, $templates, string $template_id */
?>
<div class="wrap lrp-wrap">
	<h1><?php esc_html_e( 'Vista previa', 'libro-reclamaciones-pro' ); ?></h1>

	<?php if ( ! empty( $_GET['test_sent'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Correo de prueba enviado.', 'libro-reclamaciones-pro' ); ?></p></div>
	<?php endif; ?>

	<form method="get">
		<input type="hidden" name="page" value="lrp-preview" />
		<label><?php esc_html_e( 'Plantilla:', 'libro-reclamaciones-pro' ); ?>
			<select name="template_id">
				<?php foreach ( $templates as $t ) : ?>
					<option value="<?php echo esc_attr( $t['id'] ); ?>" <?php selected( $template_id, $t['id'] ); ?>><?php echo esc_html( $t['name'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<button class="button"><?php esc_html_e( 'Ver', 'libro-reclamaciones-pro' ); ?></button>
	</form>

	<h2><?php esc_html_e( 'Formulario público', 'libro-reclamaciones-pro' ); ?></h2>
	<div class="lrp-preview-tabs">
		<button class="button lrp-tab active" data-w="desktop"><?php esc_html_e( 'Desktop', 'libro-reclamaciones-pro' ); ?></button>
		<button class="button lrp-tab" data-w="mobile"><?php esc_html_e( 'Mobile', 'libro-reclamaciones-pro' ); ?></button>
	</div>
	<div class="lrp-preview-frame lrp-w-desktop">
		<?php echo LRP_Public::render_form( array( 'template_id' => $template_id, 'preview' => true ) ); // phpcs:ignore ?>
	</div>

	<h2 style="margin-top:32px;"><?php esc_html_e( 'Correo a la empresa', 'libro-reclamaciones-pro' ); ?></h2>
	<div class="lrp-panel"><strong><?php esc_html_e( 'Asunto:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $emails['subject_business'] ); ?>
		<hr><?php echo wp_kses_post( $emails['body_business'] ); ?>
	</div>

	<h2><?php esc_html_e( 'Correo al consumidor', 'libro-reclamaciones-pro' ); ?></h2>
	<div class="lrp-panel"><strong><?php esc_html_e( 'Asunto:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $emails['subject_consumer'] ); ?>
		<hr><?php echo wp_kses_post( $emails['body_consumer'] ); ?>
	</div>

	<h2><?php esc_html_e( 'Mensaje final de confirmación', 'libro-reclamaciones-pro' ); ?></h2>
	<div class="lrp-panel"><?php echo esc_html( $settings['success_message'] ); ?></div>

	<h2><?php esc_html_e( 'Enviar reclamo de prueba (correo)', 'libro-reclamaciones-pro' ); ?></h2>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'lrp_send_test' ); ?>
		<input type="hidden" name="action" value="lrp_send_test" />
		<input type="email" name="test_email" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" class="regular-text" />
		<button class="button button-primary"><?php esc_html_e( 'Enviar prueba', 'libro-reclamaciones-pro' ); ?></button>
	</form>
</div>
