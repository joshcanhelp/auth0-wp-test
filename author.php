<?php get_template_part( 'template-parts/block', 'header' ); ?>
	<article>
		<?php if ( ! class_exists( 'WP_Auth0' ) ) : ?>
			<div class="alert alert-warning"><strong>Auth0 is not installed or inactive</strong></div>
		<?php else :
			$current_user = get_user_by( 'slug', get_query_var( 'author_name' ) );
			if ( $current_user ) : ?>
				<h3>WP user</h3>
				<pre><?php var_dump( $current_user->data ); ?></pre>
				<?php if ( function_exists( 'get_auth0userinfo' ) && $auth0_user = get_auth0userinfo( $current_user->ID ) ) : ?>
					<h3>Auth0 user</h3>
					<pre><?php var_dump( $auth0_user ); ?></pre>
				<?php else : ?>
					<div class="alert alert-warning"><strong>No Auth0 user</strong></div>
				<?php endif; ?>
			<?php else : ?>
				<div class="alert alert-warning"><strong>No current WP session</strong></div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( have_posts() ) : ?>
			<h2>Posts</h2>
			<?php while ( have_posts() ) : the_post(); ?>
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<?php endwhile; else : ?>
			<p><i>No posts for this user...</i></p>
		<?php endif; ?>
	</article>
<?php get_template_part( 'template-parts/block', 'footer' ); ?>