<?php
get_header();
do_action( 'generate_before_main_content' );

if ( ! is_category() ) {
	the_archive_title( '<header class="tw-page-head"><h1 class="page-title">', '</h1></header>' );
}

if ( have_posts() ) {
	echo '<div class="tw-article-grid">';
	while ( have_posts() ) {
		the_post();
		tailwell_article_card( get_post() );
	}
	echo '</div>';
	the_posts_pagination();
} else {
	echo '<p class="tw-latest-empty">' . esc_html__( 'No articles here yet — check back soon.', 'tailwell' ) . '</p>';
}

do_action( 'generate_after_main_content' );
get_footer();