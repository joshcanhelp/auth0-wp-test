<p>If this works properly, you should be redirected back to this page after successful login (login ).</p>
<p>Use URL parameters to adjust how the form looks and operates. Available parameters:</p>
<p><?php
	array_map(
		function ( $item ) {
			printf( '<code>%s</code> ', $item );
		},
		auth0_theme_get_lock_mod_opts()
	) ?></p>
<?php if ( is_user_logged_in() ) : ?>
<p><a href="<?php echo wp_logout_url( get_permalink() ) ?>" class="btn btn-success btn-sm">Logout to test</a></p>
<?php endif; ?>
<br>
<br>
