<?php
/**
 * TailWell Gutenberg block patterns.
 *
 * Every banner is delivered as a Gutenberg block pattern so it can be
 * inserted, edited, and reused without a designer. Patterns lean on the
 * theme's shortcodes ([tw_hero], [tw_silo_banner], [tw_inline_banner],
 * [tw_article_list], [tw_related_articles]) which render styled, rightsized
 * markup — so there are no inline styles that a caching plugin could strip.
 *
 * Register your own patterns too: use register_block_pattern() from a
 * separate plugin or theme if you'd rather not edit this file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'register_block_pattern' ) ) {
	return; // Block patterns require WordPress 5.5+.
}

/* Register pattern categories. */
if ( function_exists( 'register_block_pattern_category' ) ) {
	register_block_pattern_category(
		'tailwell',
		array( 'label' => __( 'TailWell', 'tailwell' ) )
	);
}

/* ------------------------------------------------------------------ */
/* Homepage hero banner                                               */
/* ------------------------------------------------------------------ */
register_block_pattern(
	'tailwell/home-hero',
	array(
		'title'       => __( 'TailWell: Homepage hero', 'tailwell' ),
		'description' => __( 'Full-width brand hero with heading, subtext, CTA, and paw+leaf motif.', 'tailwell' ),
		'categories'  => array( 'tailwell' ),
		'keywords'    => array( 'hero', 'home', 'banner' ),
		'content'     => '<!-- wp:shortcode -->[tw_hero]<!-- /wp:shortcode -->',
	)
);

/* ------------------------------------------------------------------ */
/* Silo banners (one per silo)                                        */
/* ------------------------------------------------------------------ */
function tailwell_silo_pattern( $key, $title, $desc ) {
	register_block_pattern(
		"tailwell/silo-{$key}",
		array(
			'title'       => sprintf( __( 'TailWell: %s banner', 'tailwell' ), $title ),
			'description' => __( 'Silo banner for the top of a category page.', 'tailwell' ),
			'categories'  => array( 'tailwell' ),
			'keywords'    => array( 'silo', 'category', 'banner' ),
			'content'     => '<!-- wp:shortcode -->[tw_silo_banner silo="' . esc_attr( $key ) . '" desc="' . esc_attr( $desc ) . '"]<!-- /wp:shortcode -->',
		)
	);
}

tailwell_silo_pattern( 'wellness', __( 'Wellness & Health', 'tailwell' ), __( 'Supplements, senior care, and everyday wellbeing — the wellness guides.', 'tailwell' ) );
tailwell_silo_pattern( 'food', __( 'Food & Nutrition', 'tailwell' ), __( 'Fresh food, treats, and age-appropriate diets researched calmly.', 'tailwell' ) );
tailwell_silo_pattern( 'gear', __( 'Gear & Tech', 'tailwell' ), __( 'Trackers, feeders, and cameras that earn their place.', 'tailwell' ) );
tailwell_silo_pattern( 'training', __( 'Training & Behavior', 'tailwell' ), __( 'Courses, tools, and enrichment at any age.', 'tailwell' ) );
tailwell_silo_pattern( 'senior', __( 'Senior & Special-Needs', 'tailwell' ), __( 'Mobility, insurance, comfort, and gentle care.', 'tailwell' ) );

/* ------------------------------------------------------------------ */
/* Silo banner + blank article-list pattern for each silo hub         */
/* ------------------------------------------------------------------ */
function tailwell_silo_hub_pattern( $key, $title, $desc, $category ) {
	register_block_pattern(
		"tailwell/silo-hub-{$key}",
		array(
			'title'       => sprintf( __( 'TailWell: %s silo hub', 'tailwell' ), $title ),
			'description' => __( 'Complete silo page: banner, intro paragraph, and article list.', 'tailwell' ),
			'categories'  => array( 'tailwell' ),
			'keywords'    => array( 'silo', 'hub', 'category', 'articles' ),
			'content'     => '<!-- wp:shortcode -->[tw_silo_banner silo="' . esc_attr( $key ) . '" desc="' . esc_attr( $desc ) . '"]<!-- /wp:shortcode -->'
				. '<!-- wp:paragraph --><p>' . esc_html__( 'Every choice matters more as our pets age. Below are the guides we have published in this topic — each one researched independently and updated on a regular schedule.', 'tailwell' ) . '</p><!-- /wp:paragraph -->'
				. '<!-- wp:shortcode -->[tw_article_list count="6" category="' . esc_attr( $category ) . '"]<!-- /wp:shortcode -->',
		)
	);
}

tailwell_silo_hub_pattern( 'wellness', __( 'Wellness & Health', 'tailwell' ), __( 'Supplements, senior care, and everyday wellbeing.', 'tailwell' ), 'wellness-health' );
tailwell_silo_hub_pattern( 'food', __( 'Food & Nutrition', 'tailwell' ), __( 'Fresh food, treats, and age-appropriate diets.', 'tailwell' ), 'food-nutrition' );
tailwell_silo_hub_pattern( 'gear', __( 'Gear & Tech', 'tailwell' ), __( 'Trackers, feeders, and cameras that earn their place.', 'tailwell' ), 'gear-tech' );
tailwell_silo_hub_pattern( 'training', __( 'Training & Behavior', 'tailwell' ), __( 'Courses, tools, and enrichment at any age.', 'tailwell' ), 'training-behavior' );
tailwell_silo_hub_pattern( 'senior', __( 'Senior & Special-Needs', 'tailwell' ), __( 'Mobility, insurance, comfort, and gentle care.', 'tailwell' ), 'senior-special-needs' );

/* ------------------------------------------------------------------ */
/* Article inline banner (mid-article internal linking)               */
/* ------------------------------------------------------------------ */
register_block_pattern(
	'tailwell/inline-banner',
	array(
		'title'       => __( 'TailWell: Inline silo banner', 'tailwell' ),
		'description' => __( 'Low-key mid-article banner linking to another silo.', 'tailwell' ),
		'categories'  => array( 'tailwell' ),
		'keywords'    => array( 'inline', 'banner', 'related', 'silo' ),
		'content'     => '<!-- wp:shortcode -->[tw_inline_banner]<!-- /wp:shortcode -->',
	)
);

/* ------------------------------------------------------------------ */
/* Article footer block: disclosure + related articles                 */
/* ------------------------------------------------------------------ */
register_block_pattern(
	'tailwell/article-footer',
	array(
		'title'       => __( 'TailWell: Article footer', 'tailwell' ),
		'description' => __( 'Disclosure note and related-articles block for the end of a guide.', 'tailwell' ),
		'categories'  => array( 'tailwell' ),
		'keywords'    => array( 'disclosure', 'related', 'footer', 'article' ),
		'content'     => '<!-- wp:shortcode -->[tw_disclosure]<!-- /wp:shortcode -->'
			. '<!-- wp:shortcode -->[tw_related_articles count="3"]<!-- /wp:shortcode -->',
	)
);

/* ------------------------------------------------------------------ */
/* Contact section                                                     */
/* ------------------------------------------------------------------ */
register_block_pattern(
	'tailwell/contact',
	array(
		'title'       => __( 'TailWell: Contact', 'tailwell' ),
		'description' => __( 'Simple contact section stub; replace with your form plugin shortcode.', 'tailwell' ),
		'categories'  => array( 'tailwell' ),
		'keywords'    => array( 'contact', 'form' ),
		'content'     => '<!-- wp:heading {"level":2} --><h2>' . esc_html__( 'Get in touch', 'tailwell' ) . '</h2><!-- /wp:heading -->'
			. '<!-- wp:paragraph --><p>' . esc_html__( 'A correction, a question about a guide, or a product you think we got wrong — we read every message. Replace this block with your form plugin\'s shortcode or block.', 'tailwell' ) . '</p><!-- /wp:paragraph -->',
	)
);