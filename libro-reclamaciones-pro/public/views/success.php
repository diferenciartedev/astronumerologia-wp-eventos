<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * @var array  $settings
 * @var string $code
 */
$primary = $settings['primary_color'] ? $settings['primary_color'] : '#1E40AF';
?>
<div class="lrp-success-wrap" style="--lrp-primary:<?php echo esc_attr( $primary ); ?>;">
	<div class="lrp-success-card">
		<div class="lrp-check">✓</div>
		<h2><?php esc_html_e( '¡Reclamo registrado!', 'libro-reclamaciones-pro' ); ?></h2>
		<p><?php echo esc_html( $settings['success_message'] ); ?></p>
		<p class="lrp-code"><?php esc_html_e( 'Código de seguimiento:', 'libro-reclamaciones-pro' ); ?> <strong><?php echo esc_html( $code ); ?></strong></p>
		<p class="lrp-muted"><?php esc_html_e( 'Guarda este código para futuras consultas.', 'libro-reclamaciones-pro' ); ?></p>
	</div>
</div>
