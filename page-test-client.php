<?php
get_template_part( 'template-parts/block', 'header' );
the_post();

$opts = WP_Auth0_Options::Instance();
?>

    <article>
      <?php get_template_part( 'template-parts/block', 'test-tpl-header' ) ?>

        <div class="the-content">
            <p><strong>Note:</strong> If you're seeing a client ID below but no client information, go to APIs in your
                <a href="https://manage.auth0.com/#/apis" target="_blank">Auth0 dashboard</a>, edit the <strong>Auth0 Management API</strong>, click the <strong>Non Interactive Clients</strong> tab, expand the row for <strong><?php echo get_auth0_curatedBlogName() ?></strong>, and add the <code>read:clients</code> scope. This is not required for the plugin to operate properly, only for the testing display below.</p>

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
        </div>


    </article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>