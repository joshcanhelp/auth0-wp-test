<?php
/**
 * The template for author posts
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage WP_Auth0_Testing
 * @since 1.0
 * @version 1.0
 */

get_template_part( 'template-parts/block', 'header' );
the_post();
$opts     = WP_Auth0_Options::Instance();
$all_opts = $opts->get_options();
?>

<article>
	<?php get_template_part( 'template-parts/block', 'test-tpl-header' ); ?>
    <div class="the-content">
		<?php
		$current_user = get_user_by( 'slug', get_query_var( 'author_name' ) );
		if ( isset( $current_user->ID ) && current_user_can( 'edit_users' ) ) :
			?>
            <h2>WP user</h2>
            <pre><?php var_dump( $current_user->data ); ?></pre>
			<?php if ( function_exists( 'get_auth0userinfo' ) && $auth0_user = get_auth0userinfo( $current_user->ID ) ) : ?>
                <h2>Stored Auth0 user</h2>
                <pre><?php var_dump( $auth0_user ); ?></pre>
            <?php else : ?>
                <div class="alert alert-warning"><strong>No Auth0 user</strong></div>
            <?php endif; ?>
		<?php
        else :
            ?>
            <div class="alert alert-warning"><strong>Not authorized to view user data</strong></div>
		    <?php
        endif;
        ?>
    </div>

	<?php if ( have_posts() ) : ?>
        <h2>Posts</h2>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<?php endwhile; else : ?>
        <p><i>No posts for this user...</i></p>
	<?php endif; ?>
</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>
