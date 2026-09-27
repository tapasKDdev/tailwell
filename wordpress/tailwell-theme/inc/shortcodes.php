<?php

function tailwell_silos() {
	return array(
		'wellness-health'     => array(
			'name'    => 'Wellness & Health',
			'tagline' => 'Supplements, checkups, and everyday care for a thriving pet.',
			'icon'    => 'heart',
		),
		'food-nutrition'      => array(
			'name'    => 'Food & Nutrition',
			'tagline' => 'Fresh, balanced meals, treats, and feeding that fits their life.',
			'icon'    => 'bowl',
		),
		'gear-tech'           => array(
			'name'    => 'Gear & Tech',
			'tagline' => 'Trackers, smart feeders, and practical gear for modern pet parents.',
			'icon'    => 'tracker',
		),
		'training-behavior'   => array(
			'name'    => 'Training & Behavior',
			'tagline' => 'Positive methods and tools that build trust and a calmer home.',
			'icon'    => 'paw',
		),
		'senior-special-needs' => array(
			'name'    => 'Senior & Special-Needs Pet Care',
			'tagline' => 'Mobility, comfort, and tailored care for aging and sensitive pets.',
			'icon'    => 'shield',
		),
	);
}

function tailwell_icon( $name ) {
	$icons = array(
		'heart'   => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3C14.74 3 13.5 3.5 12 5c-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/>',
		'bowl'    => '<path d="M4 14a8 8 0 0 1 16 0H4Z"/><path d="M5 14c0-4.5 2.67-7.5 7-7.5s7 3 7 7.5"/><path d="M12 6.5V4"/>',
		'tracker' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
		'paw'     => '<circle cx="6.5" cy="13.5" r="1.8"/><circle cx="12" cy="16.5" r="2.2"/><circle cx="17.5" cy="13.5" r="1.8"/><path d="M12 9.5C13.6 7.1 15.7 6 17.3 6c2 0 3.2 2 1.9 3.9"/><path d="M12 9.5C10.4 7.1 8.3 6 6.7 6c-2 0-3.2 2-1.9 3.9"/>',
		'shield'  => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1 1 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1Z"/><path d="m9 12 2 2 4-4"/>',
	);

	$svg = isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['heart'];

	return '<span class="tw-icon-badge" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">' . $svg . '</svg></span>';
}

function tailwell_silo_link( $slug ) {
	$term = get_category_by_slug( $slug );
	if ( $term ) {
		return get_category_link( $term );
	}
	return home_url( '/category/' . $slug . '/' );
}

function tailwell_life_stage_terms( $post_id ) {
	return wp_get_object_terms( $post_id, 'life_stage' );
}

function tailwell_life_stage_badge_html( $post_id ) {
	$terms = tailwell_life_stage_terms( $post_id );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}
	$labels = tailwell_life_stages();
	$parts  = array();
	foreach ( $terms as $term ) {
		$label   = isset( $labels[ $term->slug ] ) ? $labels[ $term->slug ] : $term->name;
		$parts[] = '<span class="tw-badge tw-badge--life-stage">' . esc_html( $label ) . '</span>';
	}
	return implode( ' ', $parts );
}

add_shortcode( 'tw_silo_grid', 'tailwell_silo_grid' );
function tailwell_silo_grid() {
	$out = '<section class="tw-silo-grid" aria-label="Browse by topic">';
	foreach ( tailwell_silos() as $slug => $silo ) {
		$out .= sprintf(
			'<a class="tw-silo-card" href="%s">%s<h3 class="tw-silo-title">%s</h3><p class="tw-silo-tagline">%s</p><span class="tw-silo-explore">%s</span></a>',
			esc_url( tailwell_silo_link( $slug ) ),
			tailwell_icon( $silo['icon'] ),
			esc_html( $silo['name'] ),
			esc_html( $silo['tagline'] ),
			esc_html__( 'Explore', 'tailwell' )
		);
	}
	$out .= '</section>';
	return $out;
}

function tailwell_product_category_labels() {
	return array(
		'joint-supplement' => 'Joint supplement',
		'fresh-food'      => 'Fresh food',
		'cbd-wellness'    => 'CBD & wellness',
		'insurance'       => 'Pet insurance',
		'gear-tracker'    => 'GPS tracker',
		'gear-feeder'     => 'Smart feeder',
		'dental'          => 'Dental care',
		'odor-control'    => 'Odor control',
	);
}

function tailwell_product_raw( $value ) {
	return html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
}

add_shortcode( 'tw_product', 'tailwell_product' );
function tailwell_product( $atts ) {
	$atts = shortcode_atts(
		array(
			'name'       => '',
			'why'        => '',
			'price'      => '',
			'url'        => '',
			'brand'      => '',
			'image'      => '',
			'category'   => '',
			'best_for'   => '',
			'partner'    => '',
			'vet'        => '',
			'limitation' => '',
		),
		$atts,
		'tw_product'
	);

	if ( empty( $atts['name'] ) || empty( $atts['url'] ) ) {
		return '';
	}

	$name  = tailwell_product_raw( $atts['name'] );
	$price = tailwell_product_raw( $atts['price'] );

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Product',
		'name'     => wp_strip_all_tags( $name ),
		'url'      => esc_url_raw( $atts['url'] ),
	);
	if ( ! empty( $atts['brand'] ) ) {
		$schema['brand'] = array( '@type' => 'Brand', 'name' => wp_strip_all_tags( tailwell_product_raw( $atts['brand'] ) ) );
	}
	if ( ! empty( $atts['image'] ) ) {
		$schema['image'] = esc_url_raw( $atts['image'] );
	}
	if ( '' !== $price ) {
		$schema['offers'] = array(
			'@type'         => 'Offer',
			'url'           => esc_url_raw( $atts['url'] ),
			'price'         => wp_strip_all_tags( $price ),
			'priceCurrency' => 'USD',
		);
	}

	$is_vet  = in_array( strtolower( trim( tailwell_product_raw( $atts['vet'] ) ) ), array( '1', 'true', 'yes' ), true );
	$labels  = tailwell_product_category_labels();
	$slug    = sanitize_key( $atts['category'] );
	$type    = isset( $labels[ $slug ] ) ? $labels[ $slug ] : '';
	$has_price = '' !== $price;
	$cta     = $has_price ? __( 'Check price', 'tailwell' ) : __( 'View product', 'tailwell' );

	$out  = '<article class="tw-product">';

	$out .= '<div class="tw-product-media">';
	if ( ! empty( $atts['image'] ) ) {
		$out .= '<img src="' . esc_url( $atts['image'] ) . '" alt="' . esc_attr( $name ) . '" width="240" height="240" loading="lazy" decoding="async">';
	} else {
		$out .= tailwell_icon( 'paw' );
	}
	$out .= '</div>';

	$out .= '<div class="tw-product-body">';
	$out .= '<div class="tw-product-head">';
	$out .= '<h3 class="tw-product-name">' . esc_html( $name ) . '</h3>';
	if ( ! empty( $atts['brand'] ) ) {
		$out .= '<span class="tw-product-brand">' . esc_html( tailwell_product_raw( $atts['brand'] ) ) . '</span>';
	}
	if ( $is_vet ) {
		$out .= '<span class="tw-badge tw-badge--vet">' . esc_html__( 'Safety-reviewed', 'tailwell' ) . '</span>';
	}
	$out .= '</div>';

	if ( $type || ! empty( $atts['best_for'] ) ) {
		$out .= '<p class="tw-product-tags">';
		if ( $type ) {
			$out .= '<span class="tw-product-type">' . esc_html( $type ) . '</span>';
		}
		if ( ! empty( $atts['best_for'] ) ) {
			$out .= '<span class="tw-product-bestfor"><span class="tw-product-bestfor-label">' . esc_html__( 'Best for', 'tailwell' ) . '</span>' . esc_html( tailwell_product_raw( $atts['best_for'] ) ) . '</span>';
		}
		$out .= '</p>';
	}

	if ( ! empty( $atts['why'] ) ) {
		$out .= '<p class="tw-product-why"><span class="tw-product-why-label">' . esc_html__( 'Why we picked it:', 'tailwell' ) . '</span> ' . esc_html( tailwell_product_raw( $atts['why'] ) ) . '</p>';
	}
	if ( ! empty( $atts['limitation'] ) ) {
		$out .= '<p class="tw-product-note"><span class="tw-product-note-label">' . esc_html__( 'Keep in mind:', 'tailwell' ) . '</span> ' . esc_html( tailwell_product_raw( $atts['limitation'] ) ) . '</p>';
	}
	$out .= '</div>';

	$out .= '<div class="tw-product-action">';
	if ( $has_price ) {
		$out .= '<span class="tw-product-price">' . esc_html( $price ) . '</span>';
	}
	$out .= '<a class="tw-product-link" href="' . esc_url( $atts['url'] ) . '" target="_blank" rel="noopener nofollow sponsored">' . esc_html( $cta ) . '</a>';
	if ( ! empty( $atts['partner'] ) ) {
		$out .= '<span class="tw-product-partner">' . esc_html( sprintf( __( 'via %s', 'tailwell' ), tailwell_product_raw( $atts['partner'] ) ) ) . '</span>';
	}
	$out .= '</div>';

	$out .= '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
	$out .= '</article>';
	return $out;
}

add_filter( 'the_content', 'tailwell_wrap_product_runs', 20 );
function tailwell_wrap_product_runs( $content ) {
	if ( false === strpos( $content, 'class="tw-product"' ) ) {
		return $content;
	}

	// Release cards wpautop wrapped in their own paragraph(s)...
	$content = preg_replace( '#<p>\s*((?:<article class="tw-product">.*?</article>\s*)+)</p>#is', '$1', $content );
	$content = preg_replace( '#<p>([^<>]*?)((?:\s*<article class="tw-product">.*?</article>)+)\s*([^<>]*?)</p>#is', '$1$2$3', $content );

	// ...then group every consecutive run into a list (compare rows when 2+).
	return preg_replace_callback(
		'#(?:<article class="tw-product">.*?</article>\s*)+#is',
		function ( $matches ) {
			$run   = rtrim( $matches[0] );
			$count = substr_count( $run, '<article class="tw-product">' );
			$class = $count > 1 ? 'tw-product-list tw-product-list--compare' : 'tw-product-list';
			return '<div class="' . $class . '">' . $run . '</div>';
		},
		$content
	);
}

add_shortcode( 'tw_disclosure', 'tailwell_disclosure' );
function tailwell_disclosure() {
	$path = apply_filters( 'tailwell_disclosure_page', 'affiliate-disclosure' );
	$page = get_page_by_path( $path );
	$url  = $page ? get_permalink( $page ) : home_url( '/' . $path . '/' );

	$text = 'As an Amazon Associate and partner of select brands, TailWell earns from qualifying purchases made through links on this page.';

	return sprintf(
		'<p class="tw-disclosure">%1$s <a href="%2$s">Read the full affiliate disclosure.</a></p>',
		esc_html( $text ),
		esc_url( $url )
	);
}

function tailwell_article_card( $post ) {
	$thumb = get_the_post_thumbnail( $post, 'tw-card' );
	$out   = '<article class="tw-article-card">';
	$out  .= '<a class="tw-article-link" href="' . esc_url( get_permalink( $post ) ) . '">';
	if ( $thumb ) {
		$out .= '<div class="tw-article-thumb">' . $thumb . '</div>';
	}
	$out .= '<h3 class="tw-article-title">' . esc_html( get_the_title( $post ) ) . '</h3>';
	$out .= '</a></article>';
	return $out;
}

add_shortcode( 'tw_latest_posts', 'tailwell_latest_posts' );
function tailwell_latest_posts( $atts ) {
	$atts  = shortcode_atts( array( 'count' => 6 ), $atts, 'tw_latest_posts' );
	$posts = get_posts(
		array(
			'numberposts' => max( 1, (int) $atts['count'] ),
			'post_status' => 'publish',
		)
	);

	if ( empty( $posts ) ) {
		return '<p class="tw-latest-empty">New articles are on the way — check back soon.</p>';
	}

	$out = '<section class="tw-latest" aria-label="Latest articles"><div class="tw-latest-grid">';
	foreach ( $posts as $post ) {
		$out .= tailwell_article_card( $post );
	}
	$out .= '</div></section>';
	return $out;
}

function tailwell_get_articles( $count = 6, $category = '', $life_stage = '' ) {
	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => max( 1, (int) $count ),
		'post_status'    => 'publish',
	);
	if ( $category ) {
		$args['category_name'] = sanitize_title( $category );
	}
	if ( $life_stage ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'life_stage',
				'field'    => 'slug',
				'terms'    => sanitize_title( $life_stage ),
			),
		);
	}
	return get_posts( $args );
}

add_shortcode( 'tw_article_list', 'tailwell_article_list' );
function tailwell_article_list( $atts ) {
	$atts = shortcode_atts(
		array(
			'count'      => 6,
			'category'   => '',
			'life_stage' => '',
			'heading'    => '',
		),
		$atts,
		'tw_article_list'
	);

	$posts = tailwell_get_articles( (int) $atts['count'], $atts['category'], $atts['life_stage'] );

	if ( empty( $posts ) ) {
		return '<p class="tw-latest-empty">' . esc_html__( 'No articles here yet — check back soon.', 'tailwell' ) . '</p>';
	}

	$out = '';
	if ( $atts['heading'] ) {
		$out .= '<h2 class="tw-related-title">' . esc_html( $atts['heading'] ) . '</h2>';
	}
	$out .= '<div class="tw-article-grid">';
	foreach ( $posts as $post ) {
		$out .= tailwell_article_card( $post );
	}
	$out .= '</div>';
	return $out;
}

add_shortcode( 'tw_related_articles', 'tailwell_related_articles' );
function tailwell_related_articles( $atts ) {
	$atts = shortcode_atts(
		array(
			'count'      => 3,
			'heading'    => 'Keep reading',
			'category'   => '',
			'life_stage' => '',
			'exclude'    => 0,
		),
		$atts,
		'tw_related_articles'
	);

	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => max( 1, (int) $atts['count'] ),
		'post_status'    => 'publish',
	);
	if ( $atts['category'] ) {
		$args['category_name'] = sanitize_title( $atts['category'] );
	}
	if ( $atts['life_stage'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'life_stage',
				'field'    => 'slug',
				'terms'    => sanitize_title( $atts['life_stage'] ),
			),
		);
	}
	$exclude = absint( $atts['exclude'] );
	if ( ! $exclude ) {
		$exclude = get_the_ID();
	}
	if ( $exclude ) {
		$args['post__not_in'] = array( $exclude );
	}

	$posts = get_posts( $args );

	if ( empty( $posts ) ) {
		return '';
	}

	$out  = '<section class="tw-related" aria-label="' . esc_attr( $atts['heading'] ) . '">';
	$out .= '<h2 class="tw-related-title">' . esc_html( $atts['heading'] ) . '</h2>';
	$out .= '<div class="tw-latest-grid">';
	foreach ( $posts as $post ) {
		$out .= tailwell_article_card( $post );
	}
	$out .= '</div></section>';
	return $out;
}

add_action( 'generate_before_main_content', 'tailwell_category_banner' );
function tailwell_category_banner() {
	if ( ! is_category() ) {
		return;
	}

	$slug  = get_query_var( 'category_name' );
	$silos = tailwell_silos();

	if ( $slug && isset( $silos[ $slug ] ) ) {
		$silo = $silos[ $slug ];
		echo '<header class="tw-silo-hero">',
			tailwell_icon( $silo['icon'] ),
			'<div class="tw-silo-hero-copy">',
			'<h1 class="tw-silo-hero-title">', esc_html( $silo['name'] ), '</h1>',
			'<p class="tw-silo-hero-tagline">', esc_html( $silo['tagline'] ), '</p>',
			'</div></header>';
	}
}

add_action( 'generate_before_main_content', 'tailwell_life_stage_filter', 25 );
function tailwell_life_stage_filter() {
	if ( ! is_category() ) {
		return;
	}

	$slug  = get_query_var( 'category_name' );
	$silos = tailwell_silos();
	if ( ! $slug || ! isset( $silos[ $slug ] ) || 'senior-special-needs' === $slug ) {
		return;
	}

	$base    = get_category_link( get_queried_object_id() );
	$current = sanitize_key( get_query_var( 'life_stage' ) );
	$labels  = tailwell_life_stages();

	echo '<nav class="tw-life-stage-filter" aria-label="' . esc_attr__( 'Filter by life stage', 'tailwell' ) . '">';
	$all_classes = 'tw-badge' . ( '' === $current ? ' is-active' : '' );
	echo '<a class="' . esc_attr( $all_classes ) . '" href="' . esc_url( $base ) . '">' . esc_html__( 'All', 'tailwell' ) . '</a>';
	foreach ( $labels as $ls_slug => $label ) {
		$url = add_query_arg( 'life_stage', $ls_slug, $base );
		$cls = 'tw-badge' . ( $ls_slug === $current ? ' is-active' : '' );
		echo '<a class="' . esc_attr( $cls ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
	echo '</nav>';
}