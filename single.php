<?php
get_template_part( 'template-parts/block', 'header' );
the_post();
?>

	<article>
		<h2>Avatar test</h2>
		<?php get_template_part( 'template-parts/block', 'avatar-test' ); ?>
		<hr>
		<?php the_content(); ?>
	</article>

	<?php get_sidebar(); ?>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>