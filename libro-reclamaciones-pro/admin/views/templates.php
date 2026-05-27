<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $templates */
$active = LRP_Settings::get( 'template_id' );
?>
<div class="wrap lrp-wrap">
	<h1><?php esc_html_e( 'Plantillas por rubro', 'libro-reclamaciones-pro' ); ?></h1>
	<p><?php esc_html_e( 'Selecciona la plantilla que se aplicará al formulario público desde la pantalla Configuración.', 'libro-reclamaciones-pro' ); ?></p>

	<div class="lrp-template-grid">
		<?php foreach ( $templates as $t ) : ?>
			<div class="lrp-template-card<?php echo $active === $t['id'] ? ' lrp-active' : ''; ?>">
				<h3><?php echo esc_html( $t['name'] ); ?> <?php if ( 'pro' === $t['type'] ) : ?><span class="lrp-pro-tag">PRO</span><?php endif; ?></h3>
				<p class="muted"><?php echo esc_html( $t['description'] ); ?></p>
				<p><strong><?php esc_html_e( 'Campos extra:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo (int) count( $t['fields'] ); ?></p>
				<?php if ( ! empty( $t['help'] ) ) : ?>
					<p class="lrp-help"><?php echo esc_html( $t['help'] ); ?></p>
				<?php endif; ?>
				<p>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=lrp-preview&template_id=' . $t['id'] ) ); ?>"><?php esc_html_e( 'Vista previa', 'libro-reclamaciones-pro' ); ?></a>
					<?php if ( $active === $t['id'] ) : ?>
						<span class="lrp-badge" style="background:#16A34A"><?php esc_html_e( 'Activa', 'libro-reclamaciones-pro' ); ?></span>
					<?php endif; ?>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
