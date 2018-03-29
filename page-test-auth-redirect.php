<?php
if ( ! is_user_logged_in() ) {
    auth_redirect();
}

get_template_part( 'template-parts/block', 'header' );
the_post();

$opts = WP_Auth0_Options::Instance();
?>

    <article>
      <?php get_template_part( 'template-parts/block', 'test-tpl-header' ) ?>

        <div class="the-content">
          <p>If you see this, it worked!</p>
        </div>

    </article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>