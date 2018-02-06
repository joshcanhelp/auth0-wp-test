<?php
get_template_part( 'template-parts/block', 'header' );
the_post();
?>

	<article>
		A Page!
	</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>