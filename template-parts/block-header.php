<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- start of wp_head() -->
	<?php wp_head(); ?>
	<!-- end of wp_head() -->

</head>
<body <?php body_class(); ?>>

<header class="jumbotron">
	<a href="<?php echo home_url(); ?>">
		<img src="<?php echo get_template_directory_uri() ?>/assets/img/logo-grey.png" alt="Auth0" width="500">
	</a>
	<h1><?php echo is_single() ? get_the_title() : get_bloginfo( 'name' ); ?></h1>
</header>