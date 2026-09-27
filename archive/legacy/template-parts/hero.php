<?php
/**
 * Template Part: Hero banner (homepage)
 *
 * Reusable homepage hero. Content can be edited here without a designer.
 *
 * Usage inside a template:
 *   get_template_part( 'template-parts/hero' );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = apply_filters( 'tailwell_hero_heading', 'Care choices you can feel good about.' );
$sub     = apply_filters( 'tailwell_hero_sub', 'Buying guides, comparisons, and honest reviews for pet families — researched for the pets who have been with us the longest.' );
$cta     = apply_filters( 'tailwell_hero_cta', 'Browse the guides' );
$url     = apply_filters( 'tailwell_hero_url', home_url( '/wellness-health/' ) );
?>
<div class="tw-hero">
	<div class="tw-hero__content">
		<h1><?php echo esc_html( $heading ); ?></h1>
		<p class="tw-hero__sub"><?php echo esc_html( $sub ); ?></p>
		<div class="tw-hero__actions">
			<a class="tw-button" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $cta ); ?></a>
		</div>
	</div>
	<div class="tw-hero__art" aria-hidden="true">
		<svg width="120" height="120" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
			<g>
				<path d="M14 20c0-6.5 2-9 4-9s4 2.5 4 9"/>
				<path d="M24 14c0-6.5 2-9 4-9s4 2.5 4 9"/>
				<path d="M34 20c0-6.5 2-9 4-9s4 2.5 4 9"/>
			</g>
			<path d="M29.5 28.5c2.8 5.6 7 8 7 13.1 0 5.6-4.9 8.7-8.6 8.7-5.2 0-9.3-4.5-8.7-10 .5-4.2 4.3-11.8 10.3-11.8z"/>
		</svg>
	</div>
</div>
