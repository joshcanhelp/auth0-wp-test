<?php

get_template_part( 'template-parts/block', 'header' );
the_post();
?>

	<article>
		<h1><?php the_title(); ?></h1>
		<div class="the-content"><?php the_content(); ?></div>
		<div class="the-comments"><?php comments_template(); ?></div>
	</article>

<?php get_sidebar(); ?>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>
