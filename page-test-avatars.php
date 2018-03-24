<?php
get_template_part( 'template-parts/block', 'header' );
the_post();
?>

    <article>
      <?php get_template_part( 'template-parts/block', 'test-tpl-header' ) ?>

        <div class="the-content">
            <p>Tests avatar overrides. A yellow border means no Auth0 information for the user so no Auth0 avatar;
                a red border means an Auth0 avatar is being used.</p>
        </div>

      <?php

      $post_q = new WP_Query( [ 'post_type' => 'post', 'posts_per_page' => 1, 'ignore_sticky_posts' => true ] );
      if ( $post_q->have_posts() ) {
        while ( $post_q->have_posts() ) {
          $post_q->the_post();
          printf(
            '<h4>Post used: <a href="%s">%s</a> [<a href="%s%d">edit</a>]</h4>',
            get_permalink(),
            get_the_title(),
            admin_url( 'post.php?action=edit&post=' ),
            get_the_ID()
          );
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
        }
      } else {
        printf(
          '<p><i>No posts found. Add a post to test avatar overrides</i></p>'
        );
      }
      ?>
    </article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>