<?php
get_header();
do_action( 'generate_before_main_content' );

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'tw-article' ); ?>>
		<header class="entry-header tw-page-head">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
<?php
endwhile;

do_action( 'generate_after_main_content' );
get_footer();