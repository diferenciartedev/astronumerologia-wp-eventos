<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $result, $args */
$statuses  = lrp_get_statuses();
$templates = LRP_Templates::all();
?>
<div class="wrap lrp-wrap">
	<h1><?php esc_html_e( 'Reclamos', 'libro-reclamaciones-pro' ); ?></h1>

	<?php if ( ! empty( $_GET['deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Reclamo eliminado.', 'libro-reclamaciones-pro' ); ?></p></div>
	<?php endif; ?>

	<form method="get" class="lrp-filters">
		<input type="hidden" name="page" value="lrp-claims" />
		<select name="status">
			<option value=""><?php esc_html_e( 'Todos los estados', 'libro-reclamaciones-pro' ); ?></option>
			<?php foreach ( $statuses as $k => $l ) : ?>
				<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $args['status'], $k ); ?>><?php echo esc_html( $l ); ?></option>
			<?php endforeach; ?>
		</select>
		<select name="type">
			<option value=""><?php esc_html_e( 'Tipo', 'libro-reclamaciones-pro' ); ?></option>
			<option value="reclamo" <?php selected( $args['type'], 'reclamo' ); ?>><?php esc_html_e( 'Reclamo', 'libro-reclamaciones-pro' ); ?></option>
			<option value="queja" <?php selected( $args['type'], 'queja' ); ?>><?php esc_html_e( 'Queja', 'libro-reclamaciones-pro' ); ?></option>
		</select>
		<select name="template_id">
			<option value=""><?php esc_html_e( 'Plantilla', 'libro-reclamaciones-pro' ); ?></option>
			<?php foreach ( $templates as $t ) : ?>
				<option value="<?php echo esc_attr( $t['id'] ); ?>" <?php selected( $args['template_id'], $t['id'] ); ?>><?php echo esc_html( $t['name'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="date" name="date_from" value="<?php echo esc_attr( $args['date_from'] ); ?>" />
		<input type="date" name="date_to" value="<?php echo esc_attr( $args['date_to'] ); ?>" />
		<input type="search" name="s" value="<?php echo esc_attr( $args['search'] ); ?>" placeholder="<?php esc_attr_e( 'Buscar código, nombre o documento', 'libro-reclamaciones-pro' ); ?>" />
		<button class="button button-primary"><?php esc_html_e( 'Filtrar', 'libro-reclamaciones-pro' ); ?></button>
		<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=lrp-claims' ) ); ?>"><?php esc_html_e( 'Limpiar', 'libro-reclamaciones-pro' ); ?></a>
	</form>

	<p><?php printf( esc_html__( 'Mostrando %1$d de %2$d reclamos.', 'libro-reclamaciones-pro' ), count( $result['items'] ), (int) $result['total'] ); ?></p>

	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Código', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Fecha', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Cliente', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Documento', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Tipo', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Plantilla', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Estado', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Fecha límite', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Acción', 'libro-reclamaciones-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( empty( $result['items'] ) ) : ?>
			<tr><td colspan="9"><?php esc_html_e( 'No hay reclamos para los filtros aplicados.', 'libro-reclamaciones-pro' ); ?></td></tr>
		<?php else : foreach ( $result['items'] as $c ) : ?>
			<tr>
				<td><strong><a href="<?php echo esc_url( lrp_admin_claim_url( $c['id'] ) ); ?>"><?php echo esc_html( $c['claim_code'] ); ?></a></strong></td>
				<td><?php echo esc_html( $c['created_at'] ); ?></td>
				<td><?php echo esc_html( $c['consumer_name'] ); ?></td>
				<td><?php echo esc_html( $c['document_type'] . ' ' . $c['document_number'] ); ?></td>
				<td><?php echo esc_html( ucfirst( $c['claim_type'] ) ); ?></td>
				<td><?php echo esc_html( $c['template_id'] ); ?></td>
				<td><span class="lrp-badge" style="background:<?php echo esc_attr( lrp_status_color( $c['status'] ) ); ?>"><?php echo esc_html( lrp_status_label( $c['status'] ) ); ?></span></td>
				<td><?php echo esc_html( $c['deadline_at'] ); ?></td>
				<td>
					<a class="button button-small" href="<?php echo esc_url( lrp_admin_claim_url( $c['id'] ) ); ?>"><?php esc_html_e( 'Ver', 'libro-reclamaciones-pro' ); ?></a>
					<?php if ( current_user_can( 'manage_options' ) ) : ?>
						<a class="button button-small button-link-delete" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=lrp_delete_claim&id=' . $c['id'] ), 'lrp_delete_claim' ) ); ?>" onclick="return confirm('<?php esc_attr_e( '¿Eliminar este reclamo?', 'libro-reclamaciones-pro' ); ?>')"><?php esc_html_e( 'Eliminar', 'libro-reclamaciones-pro' ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; endif; ?>
		</tbody>
	</table>

	<?php if ( (int) $result['pages'] > 1 ) :
		$base = remove_query_arg( 'paged' );
	?>
		<div class="tablenav"><div class="tablenav-pages">
			<?php for ( $p = 1; $p <= (int) $result['pages']; $p++ ) : ?>
				<a class="button <?php echo $p === (int) $result['paged'] ? 'button-primary' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'paged', $p, $base ) ); ?>"><?php echo (int) $p; ?></a>
			<?php endfor; ?>
		</div></div>
	<?php endif; ?>
</div>
