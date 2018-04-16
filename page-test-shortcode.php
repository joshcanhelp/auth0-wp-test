<?php
get_template_part('template-parts/block', 'header');
the_post();
?>

    <article>
      <?php get_template_part('template-parts/block', 'test-tpl-header') ?>

        <div class="the-content">
            <p>If this works properly, you should be redirected back to this page after successful login.</p>
            <p>You can use URL parameters to change valid shortcode attributes.</p>
            <br>
            <br>
          <?php
          $attrs = [
            'custom_css',
            'custom_js',
            'dict',
            'extra_conf',
            'form_title',
            'gravatar',
            'icon_url',
            'modal_trigger_name',
            'redirect_to',
            'show_as_modal',
            'social_big_buttons',
            'show_icon'
          ];

          $shortcode = '[auth0';
          foreach ( $attrs as $attr ) {
            if ( isset( $_GET[ $attr ] ) ) {
              $shortcode .= sprintf(' %s="%s"', $attr, urldecode( $_GET[ $attr ] ) );
            }
          }
          $shortcode .= ']';
          echo do_shortcode( $shortcode );
          ?>
        </div>
    </article>

<?php get_template_part('template-parts/block', 'footer'); ?>