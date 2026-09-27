<?php
/**
 * Template Part: Silo banner
 *
 * Reusable banner for the top of a category page. Content can be edited by
 * calling this part with $args, or via the [tw_silo_banner] shortcode.
 *
 * Usage inside a template:
 *   get_template_part( 'template-parts/silo', 'banner', array( 'silo' => 'senior' ) );
 *
 * @var array $args Passed via get_template_part 3rd argument.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$silo_key = isset( $args['silo'] ) ? $args['silo'] : 'wellness';
$silo     = tailwell_silo_by_key( $silo_key );

if ( ! $silo ) {
	return;
}

$desc = isset( $args['desc'] ) && $args['desc'] ? $args['desc'] : $silo['desc'];
?>
<div class="tw-silo-banner">
	<span class="tw-icon-badge" aria-hidden="true"><?php echo tailwell_icon_svg( $silo['icon'] ); // phpcs:ignore ?></span>
	<div>
		<h1><?php echo esc_html( $silo['title'] ); ?></h1>
		<p><?php echo esc_html( $desc ); ?></p>
	</div>
</div>
