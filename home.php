<?php get_template_part( 'template-parts/block', 'header' ); ?>

	<article>
		<?php
		$opts = WP_Auth0_Options::Instance();
		?>
		<h2>Current user</h2>

		<?php if ( get_current_user_id() ) : ?>
			<h3>WP user</h3>
			<pre><?php var_dump( get_user_by( 'id', get_current_user_id() )->data ); ?></pre>
			<?php if ( $auth0_user = get_auth0userinfo( get_current_user_id() ) ) : ?>
				<h3>Auth0 user</h3>
				<pre><?php var_dump( $auth0_user ); ?></pre>
			<?php else : ?>
				<div class="alert alert-warning"><strong>No Auth0 user</strong></div>
			<?php endif; ?>
		<?php else : ?>
			<div class="alert alert-warning"><strong>No current WP session</strong></div>
		<?php endif; ?>

		<h2>Posts</h2>

		<p>Tests avatar overrides. A yellow border means no Auth0 information for the user so no Auth0 avatar; a red border means an Auth0 avatar is being used.</p>

		<hr>

		<?php
		$post_q = new WP_Query( [ 'post_type' => 'post', 'posts_per_page' => 1, 'ignore_sticky_posts' => true ] );
		if ( $post_q->have_posts() ) {
			while ( $post_q->have_posts() ) {
				$post_q->the_post();
				printf( '<h4><a href="%s">%s</a></h4>', get_permalink(), get_the_title() );
				get_template_part( 'template-parts/block', 'avatar-test' );
			}
		} else {
			printf(
				'<p><i>No posts found. Add a post to test avatar overrides</i></p>'
			);
		}
		?>

		<hr>

		<h2>Current Auth0 settings</h2>
		<?php if ( ! class_exists( 'WP_Auth0_Options' ) ) : ?>
			<div class="alert alert-warning"><strong>Auth0 is not installed</strong></div>
		<?php else : ?>
			<pre><?php var_dump( $opts->get_options() ); ?></pre>
		<?php endif; ?>

		<h2>Auth0 Client settings</h2>

		<p><strong>Note:</strong> If you're seeing a client ID below but no client information, go to APIs in your <a
				href="https://manage.auth0.com/#/apis" target="_blank">Auth0 dashboard</a>, edit the <strong>Auth0 Management API</strong>, click the <strong>Non Interactive Clients</strong> tab, expand the row for <strong><?php echo get_auth0_curatedBlogName() ?></strong>, and add the <code>read:clients</code> scope. This is not required for the plugin to operate properly, only for the testing display below.</p>

		<hr>

		<?php if ( ! class_exists( 'WP_Auth0_Api_Client' ) ) : ?>
			<div class="alert alert-warning"><strong>Auth0 is not installed</strong></div>
		<?php elseif ( ! $opts->get( 'client_id' ) ) : ?>
			<div class="alert alert-warning"><strong>Client ID is not saved</strong></div>
		<?php else : ?>
			<h3><strong>Client ID:</strong> <?php echo $opts->get( 'client_id' ) ?></h3>
			<pre><?php
				var_dump( WP_Auth0_Api_Client::get_client(
					WP_Auth0_Api_Client::get_client_token(),
					$opts->get( 'client_id' )
				) );
				?></pre>
		<?php endif; ?>
	</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>