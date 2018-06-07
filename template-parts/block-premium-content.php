<?php
/**
 * Displays full editor content if the visitor is logged in, only an excerpt and a login form if logged out.
 * Include in a post, page, or CPT using get_template_part( 'template-parts/block', 'premium-content' ).
 *
 * @link https://developer.wordpress.org/reference/functions/get_template_part/
 */
?>
<div class="the-content">
<?php if ( is_user_logged_in() ) : ?>
  <?php the_content(); ?>
<?php else : ?>
  <?php the_excerpt(); ?>
  <?php do_shortcode( '[auth0]' ) ?>
<?php endif; ?>
</div>