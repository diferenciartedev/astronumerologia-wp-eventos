<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $templates */
$statuses = lrp_get_statuses();
?>
<div class="wrap lrp-wrap">
	<h1><?php esc_html_e( 'Exportar reclamos', 'libro-reclamaciones-pro' ); ?></h1>
	<p><?php esc_html_e( 'Descarga un CSV con los reclamos según filtros.', 'libro-reclamaciones-pro' ); ?></p>

	<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="lrp_export_csv" />
		<?php wp_nonce_field( 'lrp_export' ); ?>

		<table class="form-table">
			<tr><th><label><?php esc_html_e( 'Estado', 'libro-reclamaciones-pro' ); ?></label></th><td>
				<select name="status">
					<option value=""><?php esc_html_e( 'Todos', 'libro-reclamaciones-pro' ); ?></option>
					<?php foreach ( $statuses as $k => $l ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $l ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><label><?php esc_html_e( 'Tipo', 'libro-reclamaciones-pro' ); ?></label></th><td>
				<select name="type"><option value="">—</option><option value="reclamo"><?php esc_html_e( 'Reclamo', 'libro-reclamaciones-pro' ); ?></option><option value="queja"><?php esc_html_e( 'Queja', 'libro-reclamaciones-pro' ); ?></option></select>
			</td></tr>
			<tr><th><label><?php esc_html_e( 'Plantilla', 'libro-reclamaciones-pro' ); ?></label></th><td>
				<select name="template_id">
					<option value="">—</option>
					<?php foreach ( $templates as $t ) : ?>
						<option value="<?php echo esc_attr( $t['id'] ); ?>"><?php echo esc_html( $t['name'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><label><?php esc_html_e( 'Desde', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="date" name="date_from" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Hasta', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="date" name="date_to" /></td></tr>
			<tr><th><label><?php esc_html_e( 'Buscar', 'libro-reclamaciones-pro' ); ?></label></th><td><input type="search" name="search" class="regular-text" /></td></tr>
		</table>

		<p><button class="button button-primary"><?php esc_html_e( 'Descargar CSV', 'libro-reclamaciones-pro' ); ?></button></p>
	</form>
</div>
