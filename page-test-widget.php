<?php
get_template_part('template-parts/block', 'header');
the_post();

$opts = WP_Auth0_Options::Instance();
?>

    <article>
      <?php get_template_part('template-parts/block', 'test-tpl-header') ?>

        <div class="the-content">
          <?php
          the_widget(
            'WP_Auth0_Embed_Widget',
            array(
              'show_as_modal' => false,
              'modal_trigger_name' => '',
              'form_title' => 'WIDGET!!',
              'social_big_buttons' => false,
              'gravatar' => true,
              'show_icon' => '',
              'icon_url' => includes_url('images/w-logo-blue.png'),
              'dict' => '',
              'extra_conf' => '',
              'custom_css' => '',
              'custom_js' => '',
              'redirect_to' => '',
            )
          );
          ?>
        </div>

    </article>

<?php get_template_part('template-parts/block', 'footer'); ?>