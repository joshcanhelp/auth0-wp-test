<?php get_template_part( 'template-parts/block', 'header' ); ?>
	<article>
		<h1>Home</h1>
	<?php if ( ! class_exists( 'WP_Auth0' ) ) : ?>
			<div class="alert alert-warning"><strong>Auth0 is not installed or inactive</strong></div>
	<?php elseif ( ! WP_Auth0_Options::Instance()->get( 'client_id' ) ) : ?>
			<div class="alert alert-danger"><strong>Auth0 needs to be configured</strong></div>
	<?php else : ?>
			<div class="the-content">
				<p><strong>Note:</strong> You'll need to create the following pages for the test fixtures to display.</p>
				<ul>
		<?php

		foreach ( auth0_wp_test_get_test_page_slugs() as $slug ) :
			?>
		 <li><a href="<?php echo home_url( 'auth-zero/test-' ) . $slug; ?>">
		   Test <?php echo implode( ' ', array_map( 'ucfirst', explode( '-', $slug ) ) ); ?>
		  </a></li>
		<?php endforeach; ?>
		  <li><a href="#" id="amp-test">Test Amplificator</a></li>
				</ul>
			</div>
	<?php endif; ?>
	</article>

	<article>
		<?php dynamic_sidebar(); ?>
	</article>

<script>
	var $ = jQuery;
	var ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
	var pageUrl = '<?php echo home_url(); ?>';
	$('#amp-test').click(function (e) {
		e.preventDefault();
		var postData = {
			action: 'auth0_amplificator',
			provider: 'twitter',
			page_url: pageUrl
		};

		$.post( ajaxUrl, postData )
			.done(function (data) {
				if ('-1' === data || -1 === data) {
					alert('Error - no data');
				} else {
					alert(data);
				}
			})
			.fail(function (data) {
				alert('Error - failed AJAX');
				console.log(data);
			});

	});
</script>
<?php get_template_part( 'template-parts/block', 'footer' ); ?>
