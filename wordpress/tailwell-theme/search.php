<?php
get_header();
do_action( 'generate_before_main_content' );

printf(
	'<header class="tw-page-head"><h1 class="page-title">%s</h1></header>',
	esc_html( sprintf( __( 'Search results for “%s”', 'tailwell' ), get_search_query() ) )
);

if ( have_posts() ) {
	echo '<div class="tw-article-grid">';
	while ( have_posts() ) {
		the_post();
		tailwell_article_card( get_post() );
	}
	echo '</div>';
	the_posts_pagination();
} else {
	echo '<p class="tw-latest-empty">' . esc_html__( 'Nothing found for that search — try another term.', 'tailwell' ) . '</p>';
	get_search_form();
}

do_action( 'generate_after_main_content' );
get_footer();