<?php get_template_part('template-parts/block', 'header'); ?>
    <article>
        <h1>Home</h1>
      <?php if (!class_exists('WP_Auth0')) : ?>
          <div class="alert alert-warning"><strong>Auth0 is not installed or inactive</strong></div>
      <?php elseif (!WP_Auth0_Options::Instance()->get('client_id')) : ?>
          <div class="alert alert-danger"><strong>Auth0 needs to be configured</strong></div>
      <?php else : ?>
          <div class="the-content">
              <p><strong>Note:</strong> You'll need to create the following pages for the test fixtures to display.</p>
              <ul>
                <?php
                $slugs = ['settings', 'client', 'user', 'avatars', 'shortcode', 'widget', 'auth-redirect'];
                foreach ($slugs as $slug) : ?>
                    <li><a href="<?php echo home_url('auth-zero/test-') . $slug ?>">
                            Test <?php echo implode(' ', array_map('ucfirst', explode('-', $slug))) ?>
                        </a></li>
                <?php endforeach; ?>
              </ul>
          </div>
      <?php endif; ?>
    </article>
<?php get_template_part('template-parts/block', 'footer'); ?>