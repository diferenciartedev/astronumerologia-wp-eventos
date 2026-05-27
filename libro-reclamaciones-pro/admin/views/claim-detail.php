<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $claim, $logs, $attachments */
$statuses = lrp_get_statuses();
$additional = ! empty( $claim['additional_data'] ) ? json_decode( $claim['additional_data'], true ) : array();
?>
<div class="wrap lrp-wrap">
	<h1><?php echo esc_html( $claim['claim_code'] ); ?> <span class="lrp-badge" style="background:<?php echo esc_attr( lrp_status_color( $claim['status'] ) ); ?>"><?php echo esc_html( lrp_status_label( $claim['status'] ) ); ?></span></h1>

	<?php if ( ! empty( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Cambios guardados.', 'libro-reclamaciones-pro' ); ?></p></div>
	<?php endif; ?>

	<p>
		<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=lrp-claims' ) ); ?>">&larr; <?php esc_html_e( 'Volver al listado', 'libro-reclamaciones-pro' ); ?></a>
		<a class="button" target="_blank" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=lrp_view_pdf&id=' . $claim['id'] ), 'lrp_view_pdf' ) ); ?>"><?php esc_html_e( 'Vista imprimible / PDF', 'libro-reclamaciones-pro' ); ?></a>
	</p>

	<div class="lrp-grid-2">
		<div class="lrp-panel">
			<h2><?php esc_html_e( 'Consumidor', 'libro-reclamaciones-pro' ); ?></h2>
			<p><strong><?php esc_html_e( 'Nombre:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['consumer_name'] ); ?></p>
			<p><strong><?php esc_html_e( 'Documento:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['document_type'] . ' ' . $claim['document_number'] ); ?></p>
			<p><strong><?php esc_html_e( 'Correo:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['email'] ); ?></p>
			<p><strong><?php esc_html_e( 'Teléfono:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['phone'] ); ?></p>
			<p><strong><?php esc_html_e( 'Dirección:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['address'] ); ?></p>
			<p><strong><?php esc_html_e( 'Ubicación:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['department'] . ' / ' . $claim['province'] . ' / ' . $claim['district'] ); ?></p>
			<?php if ( $claim['is_minor'] ) : ?>
				<p><strong><?php esc_html_e( 'Menor de edad. Apoderado:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['guardian_name'] . ' (' . $claim['guardian_document'] . ')' ); ?></p>
			<?php endif; ?>
		</div>

		<div class="lrp-panel">
			<h2><?php esc_html_e( 'Bien contratado', 'libro-reclamaciones-pro' ); ?></h2>
			<p><strong><?php esc_html_e( 'Tipo:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( ucfirst( $claim['good_type'] ) ); ?></p>
			<p><strong><?php esc_html_e( 'Producto / servicio:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['product_or_service'] ); ?></p>
			<p><strong><?php esc_html_e( 'Monto:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['amount'] ); ?></p>
			<p><strong><?php esc_html_e( 'Comprobante:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['receipt_number'] ); ?></p>
			<p><strong><?php esc_html_e( 'Fecha de compra:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['purchase_date'] ); ?></p>
			<p><strong><?php esc_html_e( 'Canal:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['channel'] ); ?></p>
		</div>
	</div>

	<div class="lrp-panel">
		<h2><?php esc_html_e( 'Detalle del reclamo', 'libro-reclamaciones-pro' ); ?></h2>
		<p><strong><?php esc_html_e( 'Tipo:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( ucfirst( $claim['claim_type'] ) ); ?></p>
		<p><strong><?php esc_html_e( 'Detalle:', 'libro-reclamaciones-pro' ); ?></strong><br><?php echo nl2br( esc_html( $claim['claim_detail'] ) ); ?></p>
		<p><strong><?php esc_html_e( 'Pedido del consumidor:', 'libro-reclamaciones-pro' ); ?></strong><br><?php echo nl2br( esc_html( $claim['consumer_request'] ) ); ?></p>
	</div>

	<?php if ( ! empty( $additional ) ) : ?>
	<div class="lrp-panel">
		<h2><?php esc_html_e( 'Campos adicionales', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="widefat striped">
			<?php foreach ( $additional as $k => $v ) : ?>
				<tr><th><?php echo esc_html( $k ); ?></th><td><?php echo esc_html( is_scalar( $v ) ? $v : wp_json_encode( $v ) ); ?></td></tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $attachments ) ) : ?>
	<div class="lrp-panel">
		<h2><?php esc_html_e( 'Archivos adjuntos', 'libro-reclamaciones-pro' ); ?></h2>
		<ul>
		<?php foreach ( $attachments as $a ) : ?>
			<li><a href="<?php echo esc_url( $a['file_url'] ); ?>" target="_blank"><?php echo esc_html( $a['file_name'] ); ?></a> <span class="muted">(<?php echo esc_html( size_format( $a['size'] ) ); ?>)</span></li>
		<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>

	<div class="lrp-grid-2">
		<div class="lrp-panel">
			<h2><?php esc_html_e( 'Cambio de estado', 'libro-reclamaciones-pro' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'lrp_update_status' ); ?>
				<input type="hidden" name="action" value="lrp_update_status" />
				<input type="hidden" name="claim_id" value="<?php echo (int) $claim['id']; ?>" />
				<select name="new_status">
					<?php foreach ( $statuses as $k => $l ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $claim['status'], $k ); ?>><?php echo esc_html( $l ); ?></option>
					<?php endforeach; ?>
				</select>
				<button class="button button-primary"><?php esc_html_e( 'Actualizar estado', 'libro-reclamaciones-pro' ); ?></button>
			</form>
			<p class="muted" style="margin-top:8px;"><?php esc_html_e( 'Fecha límite:', 'libro-reclamaciones-pro' ); ?> <strong><?php echo esc_html( $claim['deadline_at'] ); ?></strong></p>
		</div>

		<div class="lrp-panel">
			<h2><?php esc_html_e( 'Trazabilidad', 'libro-reclamaciones-pro' ); ?></h2>
			<p><strong>IP:</strong> <?php echo esc_html( $claim['ip_address'] ); ?></p>
			<p><strong>User Agent:</strong> <code><?php echo esc_html( $claim['user_agent'] ); ?></code></p>
			<p><strong><?php esc_html_e( 'Registrado:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['created_at'] ); ?></p>
			<p><strong><?php esc_html_e( 'Respondido:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['answered_at'] ); ?></p>
		</div>
	</div>

	<div class="lrp-panel">
		<h2><?php esc_html_e( 'Historial de cambios', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Fecha', 'libro-reclamaciones-pro' ); ?></th><th><?php esc_html_e( 'Acción', 'libro-reclamaciones-pro' ); ?></th><th><?php esc_html_e( 'Anterior', 'libro-reclamaciones-pro' ); ?></th><th><?php esc_html_e( 'Nuevo', 'libro-reclamaciones-pro' ); ?></th><th><?php esc_html_e( 'Usuario', 'libro-reclamaciones-pro' ); ?></th><th>IP</th></tr></thead>
			<tbody>
			<?php if ( empty( $logs ) ) : ?>
				<tr><td colspan="6"><?php esc_html_e( 'Sin historial.', 'libro-reclamaciones-pro' ); ?></td></tr>
			<?php else : foreach ( $logs as $l ) :
				$user = $l['user_id'] ? get_userdata( $l['user_id'] ) : null;
			?>
				<tr>
					<td><?php echo esc_html( $l['created_at'] ); ?></td>
					<td><?php echo esc_html( $l['action'] ); ?></td>
					<td><?php echo esc_html( lrp_status_label( $l['previous_status'] ) ); ?></td>
					<td><?php echo esc_html( lrp_status_label( $l['new_status'] ) ); ?></td>
					<td><?php echo $user ? esc_html( $user->display_name ) : '—'; ?></td>
					<td><?php echo esc_html( $l['ip_address'] ); ?></td>
				</tr>
			<?php endforeach; endif; ?>
			</tbody>
		</table>
	</div>
</div>
