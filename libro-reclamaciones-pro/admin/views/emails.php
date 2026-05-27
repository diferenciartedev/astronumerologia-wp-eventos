<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $emails */
?>
<div class="wrap lrp-wrap">
	<h1><?php esc_html_e( 'Correos y notificaciones', 'libro-reclamaciones-pro' ); ?></h1>
	<?php if ( ! empty( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Configuración de correos guardada.', 'libro-reclamaciones-pro' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'lrp_save_emails' ); ?>
		<input type="hidden" name="action" value="lrp_save_emails" />

		<table class="form-table">
			<tr><th><label><?php esc_html_e( 'Destinatarios (empresa)', 'libro-reclamaciones-pro' ); ?></label></th><td>
				<input type="text" name="lrp[recipients]" value="<?php echo esc_attr( $emails['recipients'] ); ?>" class="large-text" />
				<p class="description"><?php esc_html_e( 'Separa varios correos con coma.', 'libro-reclamaciones-pro' ); ?></p>
			</td></tr>
			<tr><th><?php esc_html_e( 'Notificación a la empresa', 'libro-reclamaciones-pro' ); ?></th><td><label><input type="checkbox" name="lrp[send_business_copy]" value="1" <?php checked( 1, (int) $emails['send_business_copy'] ); ?>> <?php esc_html_e( 'Activar', 'libro-reclamaciones-pro' ); ?></label></td></tr>
			<tr><th><?php esc_html_e( 'Copia al consumidor', 'libro-reclamaciones-pro' ); ?></th><td><label><input type="checkbox" name="lrp[send_consumer_copy]" value="1" <?php checked( 1, (int) $emails['send_consumer_copy'] ); ?>> <?php esc_html_e( 'Activar', 'libro-reclamaciones-pro' ); ?></label></td></tr>
		</table>

		<h2><?php esc_html_e( 'Correo a la empresa', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="form-table">
			<tr><th><label><?php esc_html_e( 'Asunto', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="text" name="lrp[subject_business]" value="<?php echo esc_attr( $emails['subject_business'] ); ?>" class="large-text" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Cuerpo', 'libro-reclamaciones-pro' ); ?></label></th><td><textarea name="lrp[body_business]" rows="10" class="large-text code"><?php echo esc_textarea( $emails['body_business'] ); ?></textarea></td></tr>
		</table>

		<h2><?php esc_html_e( 'Correo al consumidor', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="form-table">
			<tr><th><label><?php esc_html_e( 'Asunto', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="text" name="lrp[subject_consumer]" value="<?php echo esc_attr( $emails['subject_consumer'] ); ?>" class="large-text" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Cuerpo', 'libro-reclamaciones-pro' ); ?></label></th><td><textarea name="lrp[body_consumer]" rows="10" class="large-text code"><?php echo esc_textarea( $emails['body_consumer'] ); ?></textarea></td></tr>
		</table>

		<h3><?php esc_html_e( 'Variables disponibles', 'libro-reclamaciones-pro' ); ?></h3>
		<p><code>{{claim_code}}</code> <code>{{consumer_name}}</code> <code>{{document_number}}</code> <code>{{claim_type}}</code> <code>{{claim_detail}}</code> <code>{{consumer_request}}</code> <code>{{created_at}}</code> <code>{{deadline_at}}</code> <code>{{business_name}}</code> <code>{{status}}</code> <code>{{admin_url}}</code> <code>{{phone}}</code> <code>{{email}}</code></p>

		<p><button class="button button-primary"><?php esc_html_e( 'Guardar correos', 'libro-reclamaciones-pro' ); ?></button></p>
	</form>
</div>
