<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage WP_Auth0_Testing
 * @since 1.0
 * @version 1.0
 */

get_template_part( 'template-parts/block', 'header' );
?>
	<article>
		<h1>404</h1>
	<?php if ( current_user_can( 'publish_posts' ) && $_SERVER['REQUEST_URI'] ) : ?>
	<?php if ( empty( $_SERVER['REQUEST_URI'] ) ) : ?>
				<div class="alert alert-warning">
					<strong>No <code>REQUEST_URI</code> in <code>$_SERVER</code></strong>
				</div>
	<?php else : ?>
				<?php

				$uri_parts = explode( '/', $_SERVER['REQUEST_URI'] );
				echo '<pre>' . print_r( $uri_parts, true ) . '</pre>';
				die();
				?>
	<?php endif ?>

	<?php endif ?>
	</article>
<?php get_template_part( 'template-parts/block', 'footer' ); ?>
