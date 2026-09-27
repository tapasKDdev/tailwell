<?php
define( 'TAILWELL_VERSION', '1.0.0' );

function tailwell_enqueue_styles() {
	wp_enqueue_style( 'generate-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'tailwell-style', get_stylesheet_uri(), array( 'generate-style' ), TAILWELL_VERSION );
}

add_action( 'wp_enqueue_scripts', 'tailwell_enqueue_styles' );

function tailwell_enqueue_fonts() {
	wp_enqueue_style(
		'tailwell-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Public+Sans:wght@400;500;600;700&display=swap',
		array(),
		null
	);
}

add_action( 'wp_enqueue_scripts', 'tailwell_enqueue_fonts' );

function tailwell_enqueue_scripts() {
	wp_enqueue_script(
		'tailwell-nav',
		get_stylesheet_directory_uri() . '/assets/mobile-nav.js',
		array(),
		TAILWELL_VERSION,
		true
	);
}

add_action( 'wp_enqueue_scripts', 'tailwell_enqueue_scripts' );

add_action( 'init', 'tailwell_register_life_stage_taxonomy' );
function tailwell_register_life_stage_taxonomy() {
	register_taxonomy(
		'life_stage',
		'post',
		array(
			'labels'            => array(
				'name'          => __( 'Life Stages', 'tailwell' ),
				'singular_name' => __( 'Life Stage', 'tailwell' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rest_base'         => 'life_stage',
		)
	);
}

function tailwell_life_stages() {
	return array(
		'puppy'  => __( 'Puppy', 'tailwell' ),
		'adult'  => __( 'Adult', 'tailwell' ),
		'senior' => __( 'Senior', 'tailwell' ),
	);
}

add_action( 'after_switch_theme', 'tailwell_seed_life_stage_terms' );
function tailwell_seed_life_stage_terms() {
	foreach ( array_keys( tailwell_life_stages() ) as $slug ) {
		if ( ! term_exists( $slug, 'life_stage' ) ) {
			wp_insert_term( $slug, 'life_stage' );
		}
	}
}

add_filter( 'query_vars', 'tailwell_register_life_stage_query_var' );
function tailwell_register_life_stage_query_var( $vars ) {
	$vars[] = 'life_stage';
	return $vars;
}

add_action( 'pre_get_posts', 'tailwell_filter_archive_by_life_stage' );
function tailwell_filter_archive_by_life_stage( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_category() ) {
		return;
	}
	$slug = sanitize_key( get_query_var( 'life_stage' ) );
	if ( '' === $slug || ! array_key_exists( $slug, tailwell_life_stages() ) ) {
		return;
	}
	$query->set(
		'tax_query',
		array(
			array(
				'taxonomy' => 'life_stage',
				'field'    => 'slug',
				'terms'    => $slug,
			),
		)
	);
}

require_once get_stylesheet_directory() . '/inc/shortcodes.php';

add_action( 'after_setup_theme', 'tailwell_theme_setup' );
function tailwell_theme_setup() {
	add_image_size( 'tw-card', 640, 420, true );
	register_nav_menus(
		array(
			'primary' => __( 'Primary', 'tailwell' ),
			'footer'  => __( 'Footer', 'tailwell' ),
		)
	);
}

function tailwell_primary_menu_fallback() {
	$short = array(
		'wellness-health'      => __( 'Wellness', 'tailwell' ),
		'food-nutrition'       => __( 'Food', 'tailwell' ),
		'gear-tech'            => __( 'Gear', 'tailwell' ),
		'training-behavior'    => __( 'Training', 'tailwell' ),
		'senior-special-needs' => __( 'Senior Care', 'tailwell' ),
	);
	$items = '';
	foreach ( tailwell_silos() as $slug => $silo ) {
		$label = isset( $short[ $slug ] ) ? $short[ $slug ] : $silo['name'];
		$items .= '<li><a href="' . esc_url( tailwell_silo_link( $slug ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	$items .= '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">' . esc_html__( 'About', 'tailwell' ) . '</a></li>';
	echo '<ul id="tw-primary-menu" class="site-nav-menu">' . $items . '</ul>';
}

function tailwell_footer_menu_fallback() {
	$pages = array(
		'/about/'                => __( 'About', 'tailwell' ),
		'/review-methodology/'   => __( 'How we research', 'tailwell' ),
		'/affiliate-disclosure/' => __( 'Affiliate Disclosure', 'tailwell' ),
		'/privacy-policy/'       => __( 'Privacy Policy', 'tailwell' ),
		'/cookie-policy/'        => __( 'Cookie Policy', 'tailwell' ),
		'/terms-of-use/'         => __( 'Terms of Use', 'tailwell' ),
		'/contact/'              => __( 'Contact', 'tailwell' ),
	);
	$items = '';
	foreach ( $pages as $path => $label ) {
		$items .= '<li><a href="' . esc_url( home_url( $path ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '<ul class="footer-menu-list">' . $items . '</ul>';
}

add_action( 'generate_before_main_content', 'tailwell_breadcrumbs', 20 );
function tailwell_breadcrumbs() {
	if ( ! ( is_single() || is_category() ) ) {
		return;
	}

	$crumbs = array( home_url( '/' ) => __( 'Home', 'tailwell' ) );

	if ( is_category() ) {
		$name             = single_cat_title( '', false );
		$crumbs[ get_category_link( get_queried_object_id() ) ] = $name;
	} elseif ( is_single() ) {
		$cats = get_the_category();
		if ( ! empty( $cats ) ) {
			$crumbs[ get_category_link( $cats[0]->term_id ) ] = $cats[0]->name;
		}
		$crumbs[ get_permalink() ] = get_the_title();
	}

	$total = count( $crumbs );
	$i     = 0;

	echo '<nav class="tw-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'tailwell' ) . '"><ol>';
	foreach ( $crumbs as $url => $label ) {
		$i++;
		echo '<li>';
		if ( $i === $total ) {
			echo '<span aria-current="page">' . esc_html( wp_strip_all_tags( $label ) ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $url ) . '">' . esc_html( wp_strip_all_tags( $label ) ) . '</a>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';

	if ( ! defined( 'WPSEO_VERSION' ) ) {
		$items = array();
		$pos   = 1;
		foreach ( $crumbs as $url => $label ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => wp_strip_all_tags( $label ),
				'item'     => esc_url_raw( $url ),
			);
		}
		echo '<script type="application/ld+json">'
			. wp_json_encode(
				array(
					'@context'        => 'https://schema.org',
					'@type'           => 'BreadcrumbList',
					'itemListElement' => $items,
				),
				JSON_UNESCAPED_SLASHES
			)
			. '</script>';
	}
}