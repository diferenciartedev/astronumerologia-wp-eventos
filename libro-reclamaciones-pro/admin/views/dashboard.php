<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * @var array $stats
 * @var array $recent
 */
?>
<div class="wrap lrp-wrap">
	<h1><?php esc_html_e( 'Libro de Reclamaciones · Dashboard', 'libro-reclamaciones-pro' ); ?></h1>

	<div class="lrp-cards">
		<div class="lrp-card"><span class="lrp-card-label"><?php esc_html_e( 'Total', 'libro-reclamaciones-pro' ); ?></span><span class="lrp-card-value"><?php echo esc_html( $stats['total'] ); ?></span></div>
		<div class="lrp-card"><span class="lrp-card-label"><?php esc_html_e( 'Nuevos', 'libro-reclamaciones-pro' ); ?></span><span class="lrp-card-value"><?php echo esc_html( $stats['new'] ); ?></span></div>
		<div class="lrp-card"><span class="lrp-card-label"><?php esc_html_e( 'En revisión', 'libro-reclamaciones-pro' ); ?></span><span class="lrp-card-value"><?php echo esc_html( $stats['in_review'] ); ?></span></div>
		<div class="lrp-card"><span class="lrp-card-label"><?php esc_html_e( 'Respondidos', 'libro-reclamaciones-pro' ); ?></span><span class="lrp-card-value"><?php echo esc_html( $stats['answered'] ); ?></span></div>
		<div class="lrp-card lrp-card-warn"><span class="lrp-card-label"><?php esc_html_e( 'Vencidos', 'libro-reclamaciones-pro' ); ?></span><span class="lrp-card-value"><?php echo esc_html( $stats['expired'] ); ?></span></div>
		<div class="lrp-card"><span class="lrp-card-label"><?php esc_html_e( 'Del mes', 'libro-reclamaciones-pro' ); ?></span><span class="lrp-card-value"><?php echo esc_html( $stats['month'] ); ?></span></div>
		<div class="lrp-card"><span class="lrp-card-label"><?php esc_html_e( 'Tiempo promedio (h)', 'libro-reclamaciones-pro' ); ?></span><span class="lrp-card-value"><?php echo esc_html( $stats['avg_hours'] ); ?></span></div>
	</div>

	<?php if ( ! empty( $stats['by_template'] ) ) : ?>
		<h2><?php esc_html_e( 'Reclamos por plantilla', 'libro-reclamaciones-pro' ); ?></h2>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Plantilla', 'libro-reclamaciones-pro' ); ?></th><th><?php esc_html_e( 'Total', 'libro-reclamaciones-pro' ); ?></th></tr></thead>
			<tbody>
			<?php foreach ( $stats['by_template'] as $row ) : ?>
				<tr><td><?php echo esc_html( $row['template_id'] ); ?></td><td><?php echo esc_html( $row['total'] ); ?></td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<h2 style="margin-top:24px;"><?php esc_html_e( 'Últimos reclamos', 'libro-reclamaciones-pro' ); ?></h2>
	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Código', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Fecha', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Cliente', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Tipo', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Estado', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Días restantes', 'libro-reclamaciones-pro' ); ?></th>
				<th><?php esc_html_e( 'Acción', 'libro-reclamaciones-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( empty( $recent['items'] ) ) : ?>
			<tr><td colspan="7"><?php esc_html_e( 'Aún no hay reclamos.', 'libro-reclamaciones-pro' ); ?></td></tr>
		<?php else : foreach ( $recent['items'] as $c ) :
			$days_left = $c['deadline_at'] ? (int) floor( ( strtotime( $c['deadline_at'] ) - current_time( 'timestamp' ) ) / DAY_IN_SECONDS ) : '-';
		?>
			<tr>
				<td><strong><?php echo esc_html( $c['claim_code'] ); ?></strong></td>
				<td><?php echo esc_html( $c['created_at'] ); ?></td>
				<td><?php echo esc_html( $c['consumer_name'] ); ?></td>
				<td><?php echo esc_html( ucfirst( $c['claim_type'] ) ); ?></td>
				<td><span class="lrp-badge" style="background:<?php echo esc_attr( lrp_status_color( $c['status'] ) ); ?>"><?php echo esc_html( lrp_status_label( $c['status'] ) ); ?></span></td>
				<td><?php echo esc_html( $days_left ); ?></td>
				<td><a class="button button-small" href="<?php echo esc_url( lrp_admin_claim_url( $c['id'] ) ); ?>"><?php esc_html_e( 'Ver', 'libro-reclamaciones-pro' ); ?></a></td>
			</tr>
		<?php endforeach; endif; ?>
		</tbody>
	</table>
</div>
