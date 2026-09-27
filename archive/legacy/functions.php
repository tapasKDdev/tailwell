<?php
/**
 * TailWell GeneratePress Child Theme functions
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Enqueue parent + child stylesheets and Google Fonts.
 */
function tailwell_enqueue_styles() {
	wp_enqueue_style(
		'generatepress-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'GeneratePress' )->get( 'Version' )
	);

	wp_enqueue_style(
		'tailwell-child-style',
		get_stylesheet_uri(),
		array( 'generatepress-parent-style' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'tailwell-google-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&family=Inter:wght@400;500;600&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'tailwell_enqueue_styles' );

/**
 * Shortcode: [tw_silo_grid]
 * Renders the 5 content-silo cards on the homepage.
 * Usage: place in a Gutenberg "Shortcode" block or in a template.
 */
function tailwell_silo_grid_shortcode() {
	$silos = array(
		array(
			'title' => 'Wellness & health',
			'desc'  => 'Supplements, senior care',
			'icon'  => 'heart',
			'link'  => '/wellness-health/',
		),
		array(
			'title' => 'Food & nutrition',
			'desc'  => 'Fresh food, treats, diets',
			'icon'  => 'bone',
			'link'  => '/food-nutrition/',
		),
		array(
			'title' => 'Gear & tech',
			'desc'  => 'Trackers, feeders, cameras',
			'icon'  => 'device',
			'link'  => '/gear-tech/',
		),
		array(
			'title' => 'Training & behavior',
			'desc'  => 'Courses, tools, enrichment',
			'icon'  => 'paw',
			'link'  => '/training-behavior/',
		),
		array(
			'title' => 'Senior & special-needs care',
			'desc'  => 'Mobility, insurance, comfort',
			'icon'  => 'leaf',
			'link'  => '/senior-special-needs/',
		),
	);

	ob_start();
	echo '<div class="tw-silo-grid">';
	foreach ( $silos as $silo ) {
		echo '<a class="tw-silo-card" href="' . esc_url( $silo['link'] ) . '">';
		echo '<div class="tw-icon-badge"></div>';
		echo '<h3>' . esc_html( $silo['title'] ) . '</h3>';
		echo '<p>' . esc_html( $silo['desc'] ) . '</p>';
		echo '</a>';
	}
	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'tw_silo_grid', 'tailwell_silo_grid_shortcode' );

/**
 * Shortcode: [tw_product name="" why="" price="" url=""]
 * Renders a styled affiliate product recommendation box inside article content.
 * Usage in a post: [tw_product name="Product Name" why="Short reason this is recommended" price="$29.99" url="https://your-affiliate-link"]
 */
function tailwell_product_box_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'name'  => '',
			'why'   => '',
			'price' => '',
			'url'   => '#',
		),
		$atts,
		'tw_product'
	);

	ob_start();
	?>
	<div class="tw-product-box">
		<span class="tw-badge">Our pick</span>
		<h4><?php echo esc_html( $atts['name'] ); ?></h4>
		<p><?php echo esc_html( $atts['why'] ); ?></p>
		<?php if ( $atts['price'] ) : ?>
			<p style="font-weight:500;"><?php echo esc_html( $atts['price'] ); ?></p>
		<?php endif; ?>
		<a class="tw-button" href="<?php echo esc_url( $atts['url'] ); ?>" rel="nofollow sponsored" target="_blank">
			Check price <i class="ti ti-arrow-right"></i>
		</a>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tw_product', 'tailwell_product_box_shortcode' );

/**
 * Shortcode: [tw_disclosure]
 * Renders the affiliate disclosure note. Place near the top of any article
 * that contains affiliate links.
 */
function tailwell_disclosure_shortcode() {
	return '<div class="tw-disclosure">This article contains affiliate links. If you buy through them, TailWell may earn a small commission at no extra cost to you. <a href="/affiliate-disclosure/">Learn more</a>.</div>';
}
add_shortcode( 'tw_disclosure', 'tailwell_disclosure_shortcode' );

/**
 * Add theme support for custom logo (used in header).
 */
function tailwell_theme_setup() {
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	/*
	 * Register the two navigation menus the site structure requires:
	 *   - Primary: the 5 silo names + About
	 *   - Footer: About, Affiliate Disclosure, Privacy Policy, Contact
	 * Pages are wired to these via appearance > menus (see README).
	 */
	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'tailwell' ),
			'footer'  => __( 'Footer menu', 'tailwell' ),
		)
	);
}
add_action( 'after_setup_theme', 'tailwell_theme_setup' );

/**
 * Register the Gutenberg block patterns library (inc/patterns.php).
 */
function tailwell_register_patterns() {
	require_once get_stylesheet_directory() . '/inc/patterns.php';
}
add_action( 'init', 'tailwell_register_patterns' );

/**
 * Shared silo definitions used by multiple shortcodes.
 * Do not edit the two existing shortcodes below; this helper only feeds new ones.
 */
function tailwell_silo_definitions() {
	return array(
		'wellness' => array(
			'title' => 'Wellness & health',
			'desc'  => 'Supplements, senior care, and everyday wellbeing.',
			'icon'  => 'heart',
			'link'  => '/wellness-health/',
		),
		'food' => array(
			'title' => 'Food & nutrition',
			'desc'  => 'Fresh food, treats, and age-appropriate diets.',
			'icon'  => 'bone',
			'link'  => '/food-nutrition/',
		),
		'gear' => array(
			'title' => 'Gear & tech',
			'desc'  => 'Trackers, feeders, and cameras that earn their place.',
			'icon'  => 'device',
			'link'  => '/gear-tech/',
		),
		'training' => array(
			'title' => 'Training & behavior',
			'desc'  => 'Courses, tools, and enrichment at any age.',
			'icon'  => 'paw',
			'link'  => '/training-behavior/',
		),
		'senior' => array(
			'title' => 'Senior & special-needs care',
			'desc'  => 'Mobility, insurance, comfort, and gentle care.',
			'icon'  => 'leaf',
			'link'  => '/senior-special-needs/',
		),
	);
}

/**
 * Resolve a silo key ('wellness', 'food', 'gear', 'training', 'senior')
 * to its definition, or return null.
 */
function tailwell_silo_by_key( $key ) {
	$silos = tailwell_silo_definitions();
	return isset( $silos[ $key ] ) ? $silos[ $key ] : null;
}

/**
 * Printable single-line icon for a silo badge.
 * Simple outline-style SVGs (not filled, per the brief).
 */
function tailwell_icon_svg( $icon ) {
	$paths = array(
		'heart'  => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
		'bone'   => '<path d="M6 6.5c2.6-1.1 4.4.4 5.3 3 1-2.6 2.7-4.1 5.3-3 1 2.6-.4 4.4-3 5.3 2.6 1 4.1 2.7 3 5.3-2.6 1.1-4.4-.4-5.3-3-1 2.6-2.7 4.1-5.3 3-1-2.6.4-4.4 3-5.3-2.6-1-4.1-2.7-3-5.3Z"/>',
		'device' => '<rect x="4.5" y="7.5" width="15" height="9.5" rx="2.5"/><path d="M9 4.5h6"/><circle cx="17" cy="12.2" r="1.1"/>',
		'paw'    => '<circle cx="9" cy="9.5" r="1.6"/><circle cx="15" cy="9.5" r="1.6"/><circle cx="12" cy="7" r="1.6"/><path d="M12 13c3.3 0 4.9 2.4 4.7 4.2-.2 1.8-2 3.3-4.7 3.3s-4.5-1.5-4.7-3.3C7.1 15.4 8.7 13 12 13Z"/>',
		'leaf'   => '<path d="M4.5 19.5C4.5 11 11 4.5 19.5 4.5c.5 8-4.5 15-12.5 15"/><path d="M7 17.5 17 7.5"/>',
	);
	$path = isset( $paths[ $icon ] ) ? $paths[ $icon ] : $paths['heart'];
	return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

/**
 * Shortcode: [tw_hero heading="" sub="" cta="" url=""]
 * Homepage hero banner — cream/sage background, single terracotta CTA.
 * Optional paw + leaf motif on the right side (no photography).
 */
function tailwell_hero_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'heading' => 'Care choices you can feel good about.',
			'sub'     => 'Buying guides, comparisons, and honest reviews for pet families — researched for the pets who have been with us the longest.',
			'cta'     => 'Browse the guides',
			'url'     => '/wellness-health/',
			'icon'    => 1,
		),
		$atts,
		'tw_hero'
	);

	ob_start();
	?>
	<div class="tw-hero">
		<div class="tw-hero__content">
			<h1><?php echo esc_html( $atts['heading'] ); ?></h1>
			<p class="tw-hero__sub"><?php echo esc_html( $atts['sub'] ); ?></p>
			<div class="tw-hero__actions">
				<a class="tw-button" href="<?php echo esc_url( $atts['url'] ); ?>"><?php echo esc_html( $atts['cta'] ); ?></a>
			</div>
		</div>
		<?php if ( $atts['icon'] ) : ?>
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
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tw_hero', 'tailwell_hero_shortcode' );

/**
 * Shortcode: [tw_silo_banner silo="wellness" desc="..."]
 * Silo banner for the top of each category page.
 * Uses the existing .tw-icon-badge style from the theme.
 */
function tailwell_silo_banner_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'silo' => 'wellness',
			'desc' => '',
		),
		$atts,
		'tw_silo_banner'
	);

	$silo = tailwell_silo_by_key( $atts['silo'] );
	if ( ! $silo ) {
		return '';
	}

	$desc = $atts['desc'] ? $atts['desc'] : $silo['desc'];

	ob_start();
	?>
	<div class="tw-silo-banner">
		<span class="tw-icon-badge"><?php echo tailwell_icon_svg( $silo['icon'] ); // phpcs:ignore ?></span>
		<div>
			<h1><?php echo esc_html( $silo['title'] ); ?></h1>
			<p><?php echo esc_html( $desc ); ?></p>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tw_silo_banner', 'tailwell_silo_banner_shortcode' );

/**
 * Shortcode: [tw_inline_banner text="..." url="..." label="..."]
 * Low-key mid-article banner for internal linking / session depth.
 * Same visual weight as .tw-disclosure — deliberately not ad-like.
 */
function tailwell_inline_banner_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'text'  => 'Looking for more senior dog care guides?',
			'url'   => '/senior-special-needs/',
			'label' => 'Browse the Senior & Special-Needs silo',
		),
		$atts,
		'tw_inline_banner'
	);

	ob_start();
	?>
	<div class="tw-inline-banner">
		<strong><?php echo esc_html( $atts['text'] ); ?></strong>
		<a href="<?php echo esc_url( $atts['url'] ); ?>"><?php echo esc_html( $atts['label'] ); ?></a>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tw_inline_banner', 'tailwell_inline_banner_shortcode' );

/**
 * Article collection helper: used by [tw_article_list] and [tw_related_articles].
 * Pulls real posts from the blog. Falls back to a curated placeholder list when
 * no posts exist yet (fresh install).
 */
function tailwell_get_articles( $count = 6, $category = '' ) {
	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => (int) $count,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => false,
	);
	if ( $category ) {
		$args['category_name'] = sanitize_title( $category );
	}

	$posts = get_posts( $args );

	if ( ! empty( $posts ) ) {
		return $posts;
	}

	// Placeholder articles so silo/article pages render before real content exists.
	$placeholders = array();
	$titles       = array(
		'The Best Joint Supplements for Senior Dogs (2026)',
		'Fresh Food vs. Kibble for Older Dogs',
		'The Best Dog GPS Trackers for Peace of Mind',
		'How to Teach an Older Dog New Things, Gently',
		'Understanding CKD in Cats: Food and Supplements That Help',
		'Orthopedic Beds for Arthritic Pets: What Actually Works',
	);
	$excerpts = array(
		'What the science actually supports, what is just marketing, and the formulas we would give our own aging dogs.',
		'A calm look at the evidence on hydration, protein, and palatability as dogs age.',
		'Real battery-life tests on the top collars — no affiliate sales pressure, just the facts.',
		'Positive methods that respect stiff joints and slower processing — no force, no rush.',
		'What the research says about renal diets, phosphate binders, and when supplements matter.',
		'Support foam, pressure relief, and washability tested side-by-side over six months.',
	);
	$cats = array( 'wellness-health', 'food-nutrition', 'gear-tech', 'training-behavior', 'senior-special-needs', 'senior-special-needs' );

	for ( $i = 0; $i < $count && $i < count( $titles ); $i++ ) {
		$placeholders[] = (object) array(
			'ID'          => 0,
			'post_title'  => $titles[ $i ],
			'post_excerpt'=> $excerpts[ $i ],
			'post_date'   => gmdate( 'Y-m-d', strtotime( "-{$i} weeks" ) ),
			'category'    => $cats[ $i ],
			'permalink'   => home_url( '/' ),
		);
	}
	return $placeholders;
}

/**
 * Render a single list row for articles (shared by shortcodes).
 */
function tailwell_render_article_row( $post ) {
	$title = isset( $post->post_title ) ? $post->post_title : '';
	$excerpt = isset( $post->post_excerpt ) ? $post->post_excerpt : '';
	$excerpt = ( $excerpt ) ? $excerpt : 'A well-researched TailWell guide.';

	if ( isset( $post->permalink ) ) {
		$url = $post->permalink;
	} else {
		$url = get_permalink( $post->ID );
	}

	$date = isset( $post->post_date ) ? $post->post_date : '';
	$read = 0;
	if ( $excerpt ) {
		$words = str_word_count( strip_tags( $excerpt ) );
		$read  = max( 1, (int) round( $words / 200 ) );
	}
	?>
	<a class="tw-list-item" href="<?php echo esc_url( $url ); ?>">
		<div>
			<h3><?php echo esc_html( $title ); ?></h3>
			<p><?php echo esc_html( $excerpt ); ?></p>
			<div class="tw-meta">
				<?php if ( $date ) : ?>
					<span><?php echo esc_html( mysql2date( 'M j, Y', $date ) ); ?></span>
				<?php endif; ?>
				<span><?php echo esc_html( $read ); ?> min read</span>
			</div>
		</div>
		<span class="tw-list-item__arrow" aria-hidden="true">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
		</span>
	</a>
	<?php
}

/**
 * Shortcode: [tw_article_list count="6" category="wellness-health"]
 * Article list for silo pages (posts from the matching category).
 */
function tailwell_article_list_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'count'    => 6,
			'category' => '',
		),
		$atts,
		'tw_article_list'
	);

	$posts = tailwell_get_articles( (int) $atts['count'], $atts['category'] );

	if ( empty( $posts ) ) {
		return '<p>No articles here yet — check back soon.</p>';
	}

	ob_start();
	echo '<div class="tw-article-list">';
	foreach ( $posts as $post ) {
		tailwell_render_article_row( $post );
	}
	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'tw_article_list', 'tailwell_article_list_shortcode' );

/**
 * Shortcode: [tw_related_articles count="3"]
 * Related-articles block for the bottom of article pages.
 * Renders the current post's category plus a couple of most-recent posts.
 */
function tailwell_related_articles_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'count'   => 3,
			'heading' => 'Keep reading',
		),
		$atts,
		'tw_related_articles'
	);

	$posts = tailwell_get_articles( (int) $atts['count'] );

	if ( empty( $posts ) ) {
		return '';
	}

	ob_start();
	?>
	<section class="tw-related" aria-label="<?php echo esc_attr( $atts['heading'] ); ?>">
		<h2 class="tw-related__title"><?php echo esc_html( $atts['heading'] ); ?></h2>
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
	<?php
	return ob_get_clean();
}
add_shortcode( 'tw_related_articles', 'tailwell_related_articles_shortcode' );

/**
 * Lazy-loading: add native loading="lazy" to images below the fold and
 * width/height placeholders to reduce layout shift (Core Web Vitals).
 */
function tailwell_add_image_attributes( $attr, $attachment, $size ) {
	$attr['loading'] = 'lazy';
	$attr['decoding'] = 'async';

	// Avoid layout shift: supply intrinsic dimensions where available.
	$meta = wp_get_attachment_metadata( $attachment->ID );
	if ( isset( $meta['width'] ) && isset( $meta['height'] ) ) {
		$attr['width']  = $meta['width'];
		$attr['height'] = $meta['height'];
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'tailwell_add_image_attributes', 10, 3 );

/**
 * Add lazy-loading to content images in posts/pages too.
 */
function tailwell_content_images_lazyload( $content ) {
	if ( is_feed() || is_admin() ) {
		return $content;
	}
	$content = preg_replace_callback(
		'/<img([^>]+?)>/i',
		function ( $matches ) {
			$tag = $matches[1];
			if ( preg_match( '/loading\s*=\s*["\']lazy["\']/i', $tag ) ) {
				return $matches[0]; // already lazy
			}
			if ( ! preg_match( '/loading\s*=/i', $tag ) ) {
				$tag .= ' loading="lazy"';
			}
			if ( ! preg_match( '/decoding\s*=/i', $tag ) ) {
				$tag .= ' decoding="async"';
			}
			return '<img' . $tag . '>';
		},
		$content
	);
	return $content;
}
add_filter( 'the_content', 'tailwell_content_images_lazyload' );

/**
 * Skip-to-content link generated by GeneratePress; we mirror the focus
 * styling here and ensure headings have ids for the TOC anchors.
 */
function tailwell_auto_heading_ids( $content ) {
	if ( is_admin() || is_feed() ) {
		return $content;
	}
	$count = 0;
	return preg_replace_callback(
		'/<h([2-4])([^>]*)>(.*?)<\/h\1>/i',
		function ( $m ) use ( &$count ) {
			$tag_level = $m[1];
			$attrs     = $m[2];
			if ( preg_match( '/id\s*=/i', $attrs ) ) {
				return $m[0]; // id already present
			}
			$count++;
			$id = 'section_' . $count;
			return '<h' . $tag_level . $attrs . ' id="' . esc_attr( $id ) . '">' . $m[3] . '</h' . $tag_level . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'tailwell_auto_heading_ids' );
