<?php get_template_part( 'template-parts/block', 'header' ); ?>
    <article>
      <?php if ( ! class_exists( 'WP_Auth0' ) ) : ?>
          <div class="alert alert-warning"><strong>Auth0 is not installed or inactive</strong></div>
      <?php elseif ( ! WP_Auth0::ready() ) : ?>
          <div class="alert alert-danger"><strong>Auth0 needs to be configured</strong></div>
      <?php else : ?>
      <div class="the-content">
          <p><strong>Note:</strong> You'll need to create the following pages for the test fixtures to display.</p>
          <ul>
            <?php foreach ( [ 'settings', 'client', 'user', 'avatars', 'shortcode', 'widget' ] as $slug ) : ?>
                <li><a href="<?php echo home_url( 'auth-zero/test-' ) . $slug ?>">
                        Test <?php echo ucfirst($slug) ?>
                    </a></li>
            <?php endforeach; ?>
          </ul>
      </div>
      <?php endif; ?>
    </article>
<?php get_template_part( 'template-parts/block', 'footer' ); ?>