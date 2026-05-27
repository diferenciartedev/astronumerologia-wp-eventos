<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$features = array(
	__( 'Plantillas avanzadas por rubro', 'libro-reclamaciones-pro' ),
	__( 'Multi-sede', 'libro-reclamaciones-pro' ),
	__( 'Múltiples responsables por sede', 'libro-reclamaciones-pro' ),
	__( 'Alertas automáticas de vencimiento', 'libro-reclamaciones-pro' ),
	__( 'Cálculo de días hábiles (Perú)', 'libro-reclamaciones-pro' ),
	__( 'Generación avanzada de PDF (TCPDF/Dompdf)', 'libro-reclamaciones-pro' ),
	__( 'Validación pública con código QR', 'libro-reclamaciones-pro' ),
	__( 'Respuesta desde el panel', 'libro-reclamaciones-pro' ),
	__( 'Integración con WhatsApp', 'libro-reclamaciones-pro' ),
	__( 'Integración con CRM', 'libro-reclamaciones-pro' ),
	__( 'Webhooks', 'libro-reclamaciones-pro' ),
	__( 'Reportes avanzados', 'libro-reclamaciones-pro' ),
	__( 'Firma digital simple', 'libro-reclamaciones-pro' ),
	__( 'Marca blanca para agencias', 'libro-reclamaciones-pro' ),
	__( 'Multiempresa', 'libro-reclamaciones-pro' ),
	__( 'API REST', 'libro-reclamaciones-pro' ),
	__( 'Recordatorios automáticos', 'libro-reclamaciones-pro' ),
	__( 'Exportación avanzada Excel/PDF', 'libro-reclamaciones-pro' ),
	__( 'Campos condicionales', 'libro-reclamaciones-pro' ),
	__( 'ReCAPTCHA / Cloudflare Turnstile', 'libro-reclamaciones-pro' ),
);
?>
<div class="wrap lrp-wrap">
	<h1><?php esc_html_e( 'Opciones PRO', 'libro-reclamaciones-pro' ); ?></h1>
	<p><?php esc_html_e( 'Funcionalidades en preparación para la versión PRO del plugin. Si necesitas alguna en particular, escríbenos a MachaData.com.', 'libro-reclamaciones-pro' ); ?></p>
	<div class="lrp-pro-grid">
		<?php foreach ( $features as $f ) : ?>
			<div class="lrp-pro-card"><span class="lrp-pro-tag">PRO</span><h3><?php echo esc_html( $f ); ?></h3></div>
		<?php endforeach; ?>
	</div>
</div>
