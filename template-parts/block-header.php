<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- start of wp_head() -->
  <?php wp_head(); ?>
    <!-- end of wp_head() -->

</head>
<body <?php body_class(); ?>>

<header class="jumbotron">
    <a href="<?php echo home_url(); ?>">
        <img src="<?php echo get_template_directory_uri() ?>/assets/img/logo-grey.png" alt="Auth0" width="400">
    </a>
    <h1><?php echo is_single() ? get_the_title() : get_bloginfo('name'); ?></h1>
    <p>
      <?php if (is_user_logged_in()) : ?>
          <a class="btn btn-primary btn-sm" href="<?php echo get_edit_profile_url() ?>">Profile</a>&nbsp;
          <a class="btn btn-primary btn-sm" href="<?php echo wp_logout_url() ?>">Logout</a>&nbsp;
        <?php if (current_user_can('manage_options')) : ?>
              <a class="btn btn-success btn-sm" href="<?php echo admin_url('admin.php?page=wpa0') ?>">Settings</a>&nbsp;
              <a class="btn btn-success btn-sm"
                 href="<?php echo admin_url('admin.php?page=wpa0-errors') ?>">Errors</a>&nbsp;
        <?php endif; ?>
      <?php else : ?>
          <a class="btn btn-primary btn-sm" href="<?php echo wp_login_url() ?>">Login</a>&nbsp;
          <a class="btn btn-primary btn-sm" href="<?php echo wp_login_url() ?>?wle">Login Override</a>&nbsp;
      <?php endif; ?>
    </p>
</header>