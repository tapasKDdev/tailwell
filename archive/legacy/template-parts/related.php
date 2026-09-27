<?php
/**
 * Template Part: Related articles
 *
 * Reusable related-articles block for the bottom of article pages.
 *
 * Usage inside a template:
 *   get_template_part( 'template-parts/related' );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts = tailwell_get_articles( 3 );

if ( empty( $posts ) ) {
	return;
}
?>
<section class="tw-related" aria-label="<?php esc_attr_e( 'Related articles', 'tailwell' ); ?>">
	<h2 class="tw-related__title"><?php esc_html_e( 'Keep reading', 'tailwell' ); ?></h2>
	<div class="tw-grid--related">
		<?php foreach ( $posts as $post ) : ?>
			<?php $url = isset( $post->permalink ) ? $post->permalink : get_permalink( $post->ID ); ?>
			<a class="tw-article-card" href="<?php echo esc_url( $url ); ?>">
				<h3><?php echo esc_html( isset( $post->post_title ) ? $post->post_title : 'TailWell guide' ); ?></h3>
				<p><?php echo esc_html( isset( $post->post_excerpt ) ? $post->post_excerpt : 'A well-researched TailWell guide.' ); ?></p>
				<span class="tw-meta">Browse guides</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
