<?php
if ( post_password_required() ) {
	return;
}

if ( have_comments() ) :
	?>
	<section id="comments" class="tw-comments">
		<h2 class="tw-comments-title">
			<?php
			printf(
				esc_html( _n( 'One comment', '%s comments', get_comments_number(), 'tailwell' ) ),
				esc_html( number_format_i18n( get_comments_number() ) )
			);
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	</section>
	<?php
endif;

comment_form();