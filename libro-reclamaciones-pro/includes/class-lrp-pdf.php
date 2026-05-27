<?php
/**
 * Estructura preparada para PDF.
 *
 * MVP: genera una vista HTML imprimible. La generación de PDF real
 * (TCPDF/Dompdf) se contempla en Opciones PRO.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_PDF {

	public static function render( array $claim ) {
		$settings = LRP_Settings::get();
		ob_start();
		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="utf-8">
			<title><?php echo esc_html( $claim['claim_code'] ); ?></title>
			<style>
				body { font-family: Arial, sans-serif; color: #111; padding: 24px; }
				h1 { font-size: 18px; margin: 0 0 4px; }
				h2 { font-size: 14px; margin: 16px 0 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
				p { margin: 4px 0; font-size: 13px; }
				.muted { color: #6b7280; }
				.box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; margin-bottom: 12px; }
				.row { display:flex; gap:24px; flex-wrap:wrap; }
				.row > div { flex:1 1 220px; }
				@media print { .no-print { display:none; } }
			</style>
		</head>
		<body>
			<h1><?php esc_html_e( 'Libro de Reclamaciones', 'libro-reclamaciones-pro' ); ?></h1>
			<p class="muted"><?php echo esc_html( $settings['business_name'] ); ?> — RUC <?php echo esc_html( $settings['ruc'] ); ?></p>

			<div class="box">
				<p><strong><?php esc_html_e( 'Código:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['claim_code'] ); ?></p>
				<p><strong><?php esc_html_e( 'Fecha:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['created_at'] ); ?></p>
				<p><strong><?php esc_html_e( 'Tipo:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( ucfirst( $claim['claim_type'] ) ); ?></p>
				<p><strong><?php esc_html_e( 'Estado:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( lrp_status_label( $claim['status'] ) ); ?></p>
			</div>

			<h2><?php esc_html_e( 'Consumidor', 'libro-reclamaciones-pro' ); ?></h2>
			<div class="row">
				<div><p><strong><?php esc_html_e( 'Nombre:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['consumer_name'] ); ?></p></div>
				<div><p><strong><?php esc_html_e( 'Documento:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['document_type'] . ' ' . $claim['document_number'] ); ?></p></div>
				<div><p><strong><?php esc_html_e( 'Correo:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['email'] ); ?></p></div>
				<div><p><strong><?php esc_html_e( 'Teléfono:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['phone'] ); ?></p></div>
			</div>

			<h2><?php esc_html_e( 'Bien contratado', 'libro-reclamaciones-pro' ); ?></h2>
			<p><strong><?php esc_html_e( 'Tipo:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['good_type'] ); ?></p>
			<p><strong><?php esc_html_e( 'Producto / servicio:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['product_or_service'] ); ?></p>
			<p><strong><?php esc_html_e( 'Comprobante:', 'libro-reclamaciones-pro' ); ?></strong> <?php echo esc_html( $claim['receipt_number'] ); ?></p>

			<h2><?php esc_html_e( 'Detalle', 'libro-reclamaciones-pro' ); ?></h2>
			<p><?php echo nl2br( esc_html( $claim['claim_detail'] ) ); ?></p>

			<h2><?php esc_html_e( 'Pedido del consumidor', 'libro-reclamaciones-pro' ); ?></h2>
			<p><?php echo nl2br( esc_html( $claim['consumer_request'] ) ); ?></p>

			<p class="muted" style="margin-top:24px;font-size:11px;">
				<?php esc_html_e( 'Generado por Libro de Reclamaciones PRO', 'libro-reclamaciones-pro' ); ?> · <?php echo esc_html( date_i18n( 'Y-m-d H:i' ) ); ?>
			</p>

			<p class="no-print" style="margin-top:16px;">
				<button onclick="window.print()"><?php esc_html_e( 'Imprimir', 'libro-reclamaciones-pro' ); ?></button>
			</p>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}
}
