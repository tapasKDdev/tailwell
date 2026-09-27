<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="tw-search-field"><?php esc_html_e( 'Search TailWell', 'tailwell' ); ?></label>
	<input type="search" id="tw-search-field" class="search-field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search articles...', 'tailwell' ); ?>">
	<button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'tailwell' ); ?></button>
</form>