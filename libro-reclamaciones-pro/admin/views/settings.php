<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $settings, $templates */
?>
<div class="wrap lrp-wrap">
	<h1><?php esc_html_e( 'Configuración general', 'libro-reclamaciones-pro' ); ?></h1>
	<?php if ( ! empty( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Configuración guardada.', 'libro-reclamaciones-pro' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'lrp_save_settings' ); ?>
		<input type="hidden" name="action" value="lrp_save_settings" />

		<h2><?php esc_html_e( 'Datos del negocio', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="form-table">
			<tr><th><label><?php esc_html_e( 'Razón social', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="text" name="lrp[business_name]" value="<?php echo esc_attr( $settings['business_name'] ); ?>" class="regular-text" required /></td></tr>
			<tr><th><label><?php esc_html_e( 'Nombre comercial', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="text" name="lrp[trade_name]" value="<?php echo esc_attr( $settings['trade_name'] ); ?>" class="regular-text" /></td></tr>
			<tr><th><label>RUC</label></th><td><input type="text" name="lrp[ruc]" value="<?php echo esc_attr( $settings['ruc'] ); ?>" class="regular-text" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Dirección fiscal', 'libro-reclamaciones-pro' ); ?></label></th><td><textarea name="lrp[address]" class="large-text" rows="2"><?php echo esc_textarea( $settings['address'] ); ?></textarea></td></tr>
			<tr><th><label><?php esc_html_e( 'Teléfono', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="text" name="lrp[phone]" value="<?php echo esc_attr( $settings['phone'] ); ?>" class="regular-text" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Correo principal', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="email" name="lrp[email]" value="<?php echo esc_attr( $settings['email'] ); ?>" class="regular-text" required /></td></tr>
			<tr><th><label><?php esc_html_e( 'Página web', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="url" name="lrp[website]" value="<?php echo esc_attr( $settings['website'] ); ?>" class="regular-text" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Logo (URL)', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="url" name="lrp[logo_url]" value="<?php echo esc_attr( $settings['logo_url'] ); ?>" class="regular-text" placeholder="https://..." /></td></tr>
		</table>

		<h2><?php esc_html_e( 'Plantilla y rubro', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="form-table">
			<tr><th><label><?php esc_html_e( 'Rubro del negocio', 'libro-reclamaciones-pro' ); ?></label></th><td>
				<select name="lrp[industry]">
					<?php foreach ( $templates as $t ) : ?>
						<option value="<?php echo esc_attr( $t['id'] ); ?>" <?php selected( $settings['industry'], $t['id'] ); ?>><?php echo esc_html( $t['name'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><label><?php esc_html_e( 'Plantilla activa', 'libro-reclamaciones-pro' ); ?></label></th><td>
				<select name="lrp[template_id]">
					<?php foreach ( $templates as $t ) : ?>
						<option value="<?php echo esc_attr( $t['id'] ); ?>" <?php selected( $settings['template_id'], $t['id'] ); ?>><?php echo esc_html( $t['name'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
		</table>

		<h2><?php esc_html_e( 'Personalización', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="form-table">
			<tr><th><label><?php esc_html_e( 'Texto legal', 'libro-reclamaciones-pro' ); ?></label></th><td><textarea name="lrp[legal_text]" class="large-text" rows="4"><?php echo esc_textarea( $settings['legal_text'] ); ?></textarea></td></tr>
			<tr><th><label><?php esc_html_e( 'Color principal', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="text" name="lrp[primary_color]" value="<?php echo esc_attr( $settings['primary_color'] ); ?>" class="regular-text" placeholder="#1E40AF" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Página del libro', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="url" name="lrp[book_page]" value="<?php echo esc_attr( $settings['book_page'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( home_url( '/libro-de-reclamaciones' ) ); ?>" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Mensaje final de confirmación', 'libro-reclamaciones-pro' ); ?></label></th><td><textarea name="lrp[success_message]" class="large-text" rows="3"><?php echo esc_textarea( $settings['success_message'] ); ?></textarea></td></tr>
		</table>

		<h2><?php esc_html_e( 'Opciones operativas', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="form-table">
			<tr><th><?php esc_html_e( 'Capturar IP', 'libro-reclamaciones-pro' ); ?></th><td><label><input type="checkbox" name="lrp[capture_ip]" value="1" <?php checked( 1, (int) $settings['capture_ip'] ); ?> /> <?php esc_html_e( 'Registrar IP del usuario', 'libro-reclamaciones-pro' ); ?></label></td></tr>
			<tr><th><?php esc_html_e( 'Permitir adjuntos', 'libro-reclamaciones-pro' ); ?></th><td><label><input type="checkbox" name="lrp[allow_attachments]" value="1" <?php checked( 1, (int) $settings['allow_attachments'] ); ?> /> <?php esc_html_e( 'PDF, JPG, PNG, DOC, DOCX (máx. 5MB)', 'libro-reclamaciones-pro' ); ?></label></td></tr>
			<tr><th><?php esc_html_e( 'Copia al consumidor', 'libro-reclamaciones-pro' ); ?></th><td><label><input type="checkbox" name="lrp[send_consumer_copy]" value="1" <?php checked( 1, (int) $settings['send_consumer_copy'] ); ?> /> <?php esc_html_e( 'Enviar correo de confirmación al consumidor', 'libro-reclamaciones-pro' ); ?></label></td></tr>
			<tr><th><label><?php esc_html_e( 'Días máximos de respuesta', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="number" min="1" max="365" name="lrp[response_days]" value="<?php echo esc_attr( $settings['response_days'] ); ?>" /> <span class="muted"><?php esc_html_e( 'días hábiles', 'libro-reclamaciones-pro' ); ?></span></td></tr>
			<tr><th><label><?php esc_html_e( 'País', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="text" maxlength="3" name="lrp[country]" value="<?php echo esc_attr( $settings['country'] ); ?>" /> <span class="muted"><?php esc_html_e( 'Código ISO. Por defecto PE.', 'libro-reclamaciones-pro' ); ?></span></td></tr>
		</table>

		<p><button class="button button-primary"><?php esc_html_e( 'Guardar configuración', 'libro-reclamaciones-pro' ); ?></button></p>
	</form>
</div>
