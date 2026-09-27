<?php
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'tailwell' ); ?></a>

<header class="site-header">
	<div class="tw-container tw-header">
		<div class="tw-brand">
			<a class="tw-brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<span class="tw-brand-mark" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22"><circle cx="6.5" cy="13.5" r="1.8"/><circle cx="12" cy="16.5" r="2.2"/><circle cx="17.5" cy="13.5" r="1.8"/><path d="M12 9.5C13.6 7.1 15.7 6 17.3 6c2 0 3.2 2 1.9 3.9"/><path d="M12 9.5C10.4 7.1 8.3 6 6.7 6c-2 0-3.2 2-1.9 3.9"/></svg>
				</span>
				<span class="tw-brand-name"><?php bloginfo( 'name' ); ?></span>
			</a>
		</div>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'tailwell' ); ?>">
			<button type="button" id="tw-nav-toggle" class="site-nav-toggle" aria-expanded="false" aria-controls="tw-primary-menu"><?php esc_html_e( 'Menu', 'tailwell' ); ?></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav-menu',
					'menu_id'        => 'tw-primary-menu',
					'fallback_cb'    => 'tailwell_primary_menu_fallback',
					'depth'          => 1,
				)
			);
			?>
		</nav>
	</div>
</header>

<div id="content" class="site-content">
	<div class="tw-container">
		<main id="primary" class="site-main tw-main">