<?php
get_header();
do_action( 'generate_before_main_content' );

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'tw-article tw-article-single' ); ?>>
		<header class="entry-header tw-page-head">
			<div class="entry-meta">
				<span class="entry-meta-item"><?php esc_html_e( 'Filed under', 'tailwell' ); ?> <?php the_category( ' / ' ); ?></span>
				<?php
				$life_stage_badges = tailwell_life_stage_badge_html( get_the_ID() );
				if ( $life_stage_badges ) {
					echo '<span class="entry-meta-item">' . $life_stage_badges . '</span>';
				}
				?>
				<span class="entry-meta-item"><?php echo esc_html( get_the_date() ); ?></span>
			</div>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<div class="entry-content">
			<?php echo do_shortcode( '[tw_disclosure]' ); ?>
			<?php the_content(); ?>
		</div>

		<footer class="entry-footer">
			<?php the_tags( '<p class="entry-tags">', ' ', '</p>' ); ?>
		</footer>
	</article>
	<?php

	the_post_navigation(
		array(
			'prev_text' => __( '<span class="tw-nav-label">Previous</span> %title', 'tailwell' ),
			'next_text' => __( '<span class="tw-nav-label">Next</span> %title', 'tailwell' ),
		)
	);

	$related_cats = wp_get_post_categories( get_the_ID() );
	if ( $related_cats ) {
		$related = get_posts(
			array(
				'category__in'   => $related_cats,
				'post__not_in'   => array( get_the_ID() ),
				'posts_per_page' => 3,
				'post_status'    => 'publish',
			)
		);
		if ( $related ) {
			echo '<section class="tw-related">',
				'<h2 class="tw-related-title">' . esc_html__( 'More things your pet will love', 'tailwell' ) . '</h2>',
				'<div class="tw-latest-grid">';
			foreach ( $related as $related_post ) {
				echo tailwell_article_card( $related_post );
			}
			echo '</div></section>';
		}
	}

	comments_template();
endwhile;

do_action( 'generate_after_main_content' );
get_footer();