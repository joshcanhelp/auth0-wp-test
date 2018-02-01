<?php
$this_author = $post->post_author;
$avatar_size = 80;

$a_comment = null;
if ( get_comments_number() ) {
	$a_comment = current( get_comments( [ 'post_id' => get_the_ID(), 'number' => 1 ] ) );
}

printf(
	'<p>%s - Avatar from email</p>
	<p>%s - Avatar from user ID</p>
	<p>%s - Avatar from <code>WP_Post</code> object</p>
	<p>%s - Avatar from <code>WP_User</code> object</p>
	<p>%s</p>',
	get_avatar( $this_author, $avatar_size ),
	get_avatar( get_the_author_meta( 'email', $this_author ), $avatar_size ),
	get_avatar( $post, $avatar_size ),
	get_avatar( new WP_User( $this_author ), $avatar_size ),
	$a_comment
		? get_avatar( $a_comment, $avatar_size ) . ' - Avatar from <code>WP_Comment</code> object'
		: 'Add a comment to test comment avatars'
);