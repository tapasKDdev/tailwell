</main>
	</div>
</div>

<footer class="site-footer">
	<div class="tw-container">
		<div class="tw-footer-grid">
			<div class="tw-footer-col">
				<p class="tw-footer-title"><?php bloginfo( 'name' ); ?></p>
				<p class="tw-footer-blurb"><?php esc_html_e( 'Made by pet parents, for pet parents — every pick researched, verified, and clearly disclosed.', 'tailwell' ); ?></p>
				<p class="tw-footer-note"><?php printf( esc_html__( 'As an Amazon Associate, %s earns from qualifying purchases. We also earn from select brand partnerships. See our', 'tailwell' ), esc_html( get_bloginfo( 'name' ) ) ); ?> <a href="<?php echo esc_url( home_url( '/affiliate-disclosure/' ) ); ?>"><?php esc_html_e( 'full affiliate disclosure', 'tailwell' ); ?></a>.</p>
			</div>
			<div class="tw-footer-col">
				<p class="tw-footer-title"><?php esc_html_e( 'Company', 'tailwell' ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-menu-list',
						'fallback_cb'    => 'tailwell_footer_menu_fallback',
						'depth'          => 1,
					)
				);
				?>
			</div>
		</div>
		<div class="tw-footer-bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'tailwell' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>