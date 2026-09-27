<?php
get_header();
do_action( 'generate_before_main_content' );
?>
<section class="tw-404">
	<h1 class="tw-404-title"><?php esc_html_e( 'Page not found', 'tailwell' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for has moved or no longer exists. Try a search instead.', 'tailwell' ); ?></p>
	<?php get_search_form(); ?>
</section>
<?php
do_action( 'generate_after_main_content' );
get_footer();