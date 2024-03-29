<?php
 /* Mystique/digitalnature */
 get_header();
?>

  <!-- main content: primary + sidebar(s) -->
  <div id="main">
   <div id="main-inside" class="clearfix">
    <!-- primary content -->
    <div id="primary-content">

      <?php if (have_posts()) : while (have_posts()): the_post(); ?>
        <div class="page-navigation clearfix">
          <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
          <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
        </div>

        <!-- post -->
        <div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

          <?php
           if(function_exists('the_post_thumbnail'))
            if (has_post_thumbnail()) $post_thumb = true; else $post_thumb = false;
           ?>

          <?php if (!get_post_meta($post->ID, 'hide_title', true)): ?><h1 class="title"><?php the_title(); ?></h1><?php endif; ?>

          <div class="post-content clearfix">
          <?php if($post_thumb): ?>
          <div class="post-thumb alignleft"><?php the_post_thumbnail(); ?></div>
          <?php endif; ?>
          <?php the_content(__('More &gt;', 'mystique')); ?>
          <?php if(function_exists('wp_print')): ?><div class="alignright"><?php print_link(); ?></div><?php endif; ?>          
          </div>
          <?php wp_link_pages(array('before' => '<div class="page-navigation"><p><strong>'.__("Pages:","mystique").' </strong> ', 'after' => '</p></div>', 'next_or_number' => 'number')); ?>
          <?php
          $posttags = get_the_tags();
          if ($posttags): ?>
          <div class="post-tags"> <?php the_tags(__('Tags:','mystique').' ', ', ', ''); ?></div>
          <?php endif; ?>
          <div class="post-meta clearfix">
                <?php
                 if(get_mystique_option('sharethis') && get_mystique_option('jquery')) $share = true;
                 if ($share) shareThis();
                ?>
                <div class="details<?php if($share):?> share<?php endif; ?>">
                <?php
                printf(__('This entry was posted by %1$s on %2$s at %3$s, and is filled under %4$s. Follow any responses to this post through %5$s.', 'mystique'), '<a href="'. get_author_posts_url(get_the_author_ID()) .'" title="'. sprintf(__("Posts by %s","mystique"), attribute_escape(get_the_author())).' ">'. get_the_author() .'</a>', get_the_time(get_option('date_format')),get_the_time(get_option('time_format')), get_the_category_list(', '), '<a href="'.get_post_comments_feed_link($post->ID).'" title="RSS 2.0">RSS 2.0</a>');echo ' ';

                if (('open' == $post-> comment_status) && ('open' == $post->ping_status)):
                  // Both Comments and Pings are open
                  printf(__('You can <a%1$s>leave a response</a> or <a%2$s>trackback</a> from your own site.', 'mystique'), ' href="#respond"',' href="'.trackback_url('',false).'" rel="trackback"');

                  elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)):
                  // Only Pings are Open
                  printf(__('Responses are currently closed, but you can <a%1$s>trackback</a> from your own site.', 'mystique'), ' href="'.trackback_url('',false).'" rel="trackback"');

                  elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)):
                  // Comments are open, Pings are not
                  _e('You can skip to the end and leave a response. Pinging is currently not allowed.','mystique');

                  elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)):
                  // Neither Comments, nor Pings are open
                  _e('Both comments and pings are currently closed.','mystique');
                endif; ?>
                <?php edit_post_link(__('Edit this entry', 'mystique')); ?>
                </div>
		  </div>
        </div>
        <!-- /post -->

       <?php endwhile; endif; ?>

       <?php comments_template(); ?>

    </div>
    <!-- /primary content -->

    <?php get_sidebar(); ?>

   </div>
  </div>
  <!-- /main content -->

<?php get_footer(); ?>