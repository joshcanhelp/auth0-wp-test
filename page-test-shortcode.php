<?php
get_template_part( 'template-parts/block', 'header' );
the_post();
?>

    <article>
      <?php get_template_part( 'template-parts/block', 'test-tpl-header' ) ?>

        <div class="the-content">
          <?php
          /*
           * modal_trigger_name
           * form_title
           * social_big_buttons
           * gravatar
           * show_icon
           * icon_url
           * dict
           * extra_conf
           * custom_css
           * custom_js
           * redirect_to
          */
          echo do_shortcode( '[auth0 form_title="SHORTCODE!" social_big_buttons="true"]' ) ?>
        </div>
    </article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>