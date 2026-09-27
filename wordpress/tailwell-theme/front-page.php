<?php
get_header();
do_action( 'generate_before_main_content' );

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'tw-article tw-home-article' ); ?>>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
<?php
endwhile;

do_action( 'generate_after_main_content' );
get_footer();