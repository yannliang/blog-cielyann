<?php
/*
Template Name:No sidebar,No comment
*/
?>
<?php get_header(); ?>
  <div id="page_noside_header"></div>
  <div id="page_noside_contents" class="clearfix">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="post page" id="page_noside_post">
      <h2><span><?php the_title(); ?></span></h2>
      <div class="post_content">
       <?php the_content(__('Read more', 'monochrome')); ?>
      </div>
    </div>

<?php endwhile; else: ?>
    <div class="post page" id="page_noside_post">
     <div class="post_content">
      <?php _e("Sorry, but you are looking for something that isn't here.","monochrome"); ?>
     </div>
    </div>
<?php endif; ?>

  </div><!-- #page_noside_contents end -->

  <div id="footer_noside">
<?php get_footer(); ?>