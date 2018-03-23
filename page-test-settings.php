<?php
get_template_part( 'template-parts/block', 'header' );
the_post();

$opts = WP_Auth0_Options::Instance();
$all_opts = $opts->get_options();
ksort( $all_opts );
?>

    <article>
        <p><a href="<?php echo home_url() ?>"><small>&larr; Back to home</small></a></p>
        <h1>TPL Override: Auth0 Settings</h1>

        <div class="the-content">
          <?php if ( ! class_exists( 'WP_Auth0_Options' ) ) : ?>
              <div class="alert alert-warning"><strong>Auth0 is not installed</strong></div>
          <?php else : ?>
              <?php wp_a0_opts(); ?>
          <?php endif; ?>
        </div>
    </article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>