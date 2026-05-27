<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * @var array  $settings
 * @var array  $template
 * @var string $template_id
 * @var bool   $preview
 * @var array  $errors
 */
$primary    = $settings['primary_color'] ? $settings['primary_color'] : '#1E40AF';
$action_url = esc_url( admin_url( 'admin-post.php' ) );
$doc_types  = lrp_get_document_types();
$channels   = lrp_get_channels();
$has_error  = function( $f ) use ( $errors ) { return in_array( $f, (array) $errors, true ); };
?>
<div class="lrp-form-wrap" style="--lrp-primary:<?php echo esc_attr( $primary ); ?>;">
	<div class="lrp-form-header">
		<?php if ( ! empty( $settings['logo_url'] ) ) : ?>
			<img class="lrp-logo" src="<?php echo esc_url( $settings['logo_url'] ); ?>" alt="" />
		<?php endif; ?>
		<div>
			<h2><?php esc_html_e( 'Libro de Reclamaciones', 'libro-reclamaciones-pro' ); ?></h2>
			<p class="lrp-business">
				<strong><?php echo esc_html( $settings['business_name'] ); ?></strong>
				<?php if ( $settings['ruc'] ) : ?> · RUC <?php echo esc_html( $settings['ruc'] ); ?><?php endif; ?>
				<?php if ( $settings['address'] ) : ?><br><span class="lrp-muted"><?php echo esc_html( $settings['address'] ); ?></span><?php endif; ?>
			</p>
		</div>
	</div>

	<?php if ( ! empty( $settings['legal_text'] ) ) : ?>
		<p class="lrp-legal"><?php echo esc_html( $settings['legal_text'] ); ?></p>
	<?php endif; ?>

	<?php if ( ! empty( $template['help'] ) ) : ?>
		<div class="lrp-notice lrp-warn"><?php echo esc_html( $template['help'] ); ?></div>
	<?php endif; ?>

	<?php if ( $has_error( 'email' ) || $errors ) : ?>
		<div class="lrp-notice lrp-error"><?php esc_html_e( 'Revisa los campos marcados.', 'libro-reclamaciones-pro' ); ?></div>
	<?php endif; ?>

	<form class="lrp-form" method="post" action="<?php echo $action_url; ?>" enctype="multipart/form-data" novalidate <?php echo $preview ? 'onsubmit="return false;"' : ''; ?>>
		<?php wp_nonce_field( 'lrp_submit' ); ?>
		<input type="hidden" name="action" value="lrp_submit" />
		<input type="hidden" name="template_id" value="<?php echo esc_attr( $template_id ); ?>" />
		<!-- Honeypot -->
		<input type="text" name="lrp_website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" />

		<fieldset>
			<legend>1. <?php esc_html_e( 'Identificación del consumidor', 'libro-reclamaciones-pro' ); ?></legend>
			<div class="lrp-grid">
				<label class="lrp-col-2"><?php esc_html_e( 'Nombres y apellidos', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<input type="text" name="consumer_name" required class="<?php echo $has_error( 'consumer_name' ) ? 'lrp-input-error' : ''; ?>" />
				</label>
				<label><?php esc_html_e( 'Tipo de documento', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<select name="document_type" required>
						<?php foreach ( $doc_types as $k => $l ) : ?>
							<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $l ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label><?php esc_html_e( 'Número de documento', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<input type="text" name="document_number" required />
				</label>
				<label><?php esc_html_e( 'Teléfono', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<input type="tel" name="phone" required />
				</label>
				<label><?php esc_html_e( 'Correo electrónico', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<input type="email" name="email" required />
				</label>
				<label class="lrp-col-2"><?php esc_html_e( 'Dirección', 'libro-reclamaciones-pro' ); ?>
					<input type="text" name="address" />
				</label>
				<label><?php esc_html_e( 'Departamento', 'libro-reclamaciones-pro' ); ?>
					<input type="text" name="department" />
				</label>
				<label><?php esc_html_e( 'Provincia', 'libro-reclamaciones-pro' ); ?>
					<input type="text" name="province" />
				</label>
				<label><?php esc_html_e( 'Distrito', 'libro-reclamaciones-pro' ); ?>
					<input type="text" name="district" />
				</label>
				<label class="lrp-checkbox"><input type="checkbox" name="is_minor" value="1" data-toggle="lrp-guardian" /> <?php esc_html_e( '¿Es menor de edad?', 'libro-reclamaciones-pro' ); ?></label>
			</div>
			<div class="lrp-grid lrp-guardian" style="display:none;">
				<label><?php esc_html_e( 'Nombre del padre/madre/apoderado', 'libro-reclamaciones-pro' ); ?>
					<input type="text" name="guardian_name" />
				</label>
				<label><?php esc_html_e( 'Documento del apoderado', 'libro-reclamaciones-pro' ); ?>
					<input type="text" name="guardian_document" />
				</label>
			</div>
		</fieldset>

		<fieldset>
			<legend>2. <?php esc_html_e( 'Identificación del bien contratado', 'libro-reclamaciones-pro' ); ?></legend>
			<div class="lrp-grid">
				<label><?php esc_html_e( 'Tipo', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<select name="good_type" required>
						<option value="producto"><?php esc_html_e( 'Producto', 'libro-reclamaciones-pro' ); ?></option>
						<option value="servicio"><?php esc_html_e( 'Servicio', 'libro-reclamaciones-pro' ); ?></option>
					</select>
				</label>
				<label class="lrp-col-2"><?php esc_html_e( 'Nombre del producto o servicio', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<input type="text" name="product_or_service" required />
				</label>
				<label><?php esc_html_e( 'Monto reclamado', 'libro-reclamaciones-pro' ); ?>
					<input type="number" step="0.01" min="0" name="amount" />
				</label>
				<label><?php esc_html_e( 'Número de comprobante', 'libro-reclamaciones-pro' ); ?>
					<input type="text" name="receipt_number" />
				</label>
				<label><?php esc_html_e( 'Fecha del consumo / compra', 'libro-reclamaciones-pro' ); ?>
					<input type="date" name="purchase_date" />
				</label>
				<label><?php esc_html_e( 'Canal de atención', 'libro-reclamaciones-pro' ); ?>
					<select name="channel">
						<option value="">—</option>
						<?php foreach ( $channels as $k => $l ) : ?>
							<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $l ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</div>
		</fieldset>

		<fieldset>
			<legend>3. <?php esc_html_e( 'Detalle del reclamo o queja', 'libro-reclamaciones-pro' ); ?></legend>
			<div class="lrp-grid">
				<label class="lrp-col-2"><?php esc_html_e( 'Tipo de registro', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<select name="claim_type" required>
						<option value="reclamo"><?php esc_html_e( 'Reclamo', 'libro-reclamaciones-pro' ); ?></option>
						<option value="queja"><?php esc_html_e( 'Queja', 'libro-reclamaciones-pro' ); ?></option>
					</select>
					<small class="lrp-help-text"><?php esc_html_e( 'Reclamo: disconformidad relacionada con el producto o servicio. Queja: disconformidad no relacionada directamente (atención, demora, etc.).', 'libro-reclamaciones-pro' ); ?></small>
				</label>
				<label class="lrp-col-2"><?php esc_html_e( 'Detalle de lo ocurrido', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<textarea name="claim_detail" rows="4" required></textarea>
				</label>
				<label class="lrp-col-2"><?php esc_html_e( 'Pedido concreto del consumidor', 'libro-reclamaciones-pro' ); ?> <span class="lrp-req">*</span>
					<textarea name="consumer_request" rows="3" required></textarea>
				</label>
				<?php if ( ! empty( $settings['allow_attachments'] ) ) : ?>
					<label class="lrp-col-2"><?php esc_html_e( 'Adjuntar archivos (PDF, JPG, PNG, DOC, DOCX, máx. 5MB)', 'libro-reclamaciones-pro' ); ?>
						<input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
					</label>
				<?php endif; ?>
			</div>
		</fieldset>

		<?php if ( ! empty( $template['fields'] ) ) : ?>
		<fieldset>
			<legend><?php echo esc_html( $template['name'] ); ?> — <?php esc_html_e( 'Campos adicionales', 'libro-reclamaciones-pro' ); ?></legend>
			<div class="lrp-grid">
				<?php foreach ( $template['fields'] as $f ) :
					$name = 'extra[' . esc_attr( $f['name'] ) . ']';
					$type = isset( $f['type'] ) ? $f['type'] : 'text';
				?>
					<label class="lrp-col-2"><?php echo esc_html( $f['label'] ); ?>
						<?php if ( 'textarea' === $type ) : ?>
							<textarea name="<?php echo $name; ?>" rows="3"></textarea>
						<?php elseif ( 'select' === $type ) : ?>
							<select name="<?php echo $name; ?>">
								<option value="">—</option>
								<?php foreach ( (array) $f['options'] as $k => $l ) : ?>
									<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $l ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php elseif ( 'date' === $type ) : ?>
							<input type="date" name="<?php echo $name; ?>" />
						<?php elseif ( 'number' === $type ) : ?>
							<input type="number" step="0.01" name="<?php echo $name; ?>" />
						<?php else : ?>
							<input type="text" name="<?php echo $name; ?>" />
						<?php endif; ?>
						<?php if ( ! empty( $f['help'] ) ) : ?><small class="lrp-help-text"><?php echo esc_html( $f['help'] ); ?></small><?php endif; ?>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>
		<?php endif; ?>

		<fieldset>
			<legend><?php esc_html_e( 'Aceptación', 'libro-reclamaciones-pro' ); ?></legend>
			<label class="lrp-checkbox"><input type="checkbox" name="accept_truth" value="1" required /> <?php esc_html_e( 'Declaro que la información proporcionada es veraz.', 'libro-reclamaciones-pro' ); ?></label>
			<label class="lrp-checkbox"><input type="checkbox" name="accept_privacy" value="1" required /> <?php esc_html_e( 'Acepto el tratamiento de mis datos personales para la gestión del reclamo.', 'libro-reclamaciones-pro' ); ?></label>
		</fieldset>

		<div class="lrp-actions">
			<button type="submit" class="lrp-submit" <?php disabled( $preview ); ?>><?php esc_html_e( 'Registrar reclamo', 'libro-reclamaciones-pro' ); ?></button>
			<?php if ( $preview ) : ?>
				<span class="lrp-muted"><?php esc_html_e( '(Vista previa — el envío está deshabilitado)', 'libro-reclamaciones-pro' ); ?></span>
			<?php endif; ?>
		</div>
	</form>
</div>
