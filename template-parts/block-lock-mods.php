<p>Use URL parameters to adjust how the form looks and operates. Available parameters:</p>
<p><?php
	array_map(
		function ( $item ) {
			printf( '<code>%s</code> ', $item );
		},
		auth0_theme_get_lock_mod_opts()
	) ?></p>
<br>
<br>
