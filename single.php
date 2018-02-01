<?php
get_template_part( 'template-parts/block', 'header' );
the_post();
?>

	<article>
		<h2>Avatar test</h2>
		<?php get_template_part( 'template-parts/block', 'avatar-test' ); ?>
		<hr>
		<h2>Shortcode test</h2>
		<?php if ( !is_user_logged_in() ) : ?>
			<?php echo do_shortcode( '[auth0]' ) ?>
		<?php else : ?>
			<p>User logged in, no Auth0 form to show</p>
		<?php endif; ?>
		<hr>
		<?php the_content(); ?>
	</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>