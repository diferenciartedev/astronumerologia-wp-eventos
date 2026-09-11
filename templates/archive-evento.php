<?php
/**
 * Plantilla del archivo del CPT «evento»: pinta el calendario tipo lista
 * dentro del layout del tema activo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="evt-archive-main" class="evt-archive-page">
	<div class="evt-archive-page__inner">
		<?php echo do_shortcode( '[eventos_calendario]' ); ?>
	</div>
</main>
<?php
get_footer();
