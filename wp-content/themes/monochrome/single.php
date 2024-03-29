<?php get_header(); ?>
  <div id="contents" class="clearfix">

   <div id="left_col">
<?php $options = get_option('mc_options'); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="post clearfix" id="single_post">
     <div class="post_content_wrapper">
      <h2><span><?php the_title(); ?></span></h2>
      <div class="post_content">
       <?php the_content(__('Read more', 'monochrome')); ?>
      </div>
     </div>
     <dl class="post_meta">
        <dt class="meta_date"><?php the_time('Y') ?></dt>
         <dd class="post_date"><?php the_time('m') ?><span>/<?php the_time('d') ?></span></dd>
        <?php if ($options['author']) : ?>
        <dt><?php _e('POSTED BY','monochrome'); ?></dt>
         <dd><?php the_author_posts_link(); ?></dd>
        <?php endif; ?>
       <dt><?php _e('CATEGORY','monochrome'); ?></dt>
        <dd><?php the_category('<br />'); ?></dd>
        <?php if ($options['tag']) : ?>
         <?php the_tags(__('<dt>TAGS</dt><dd>','monochrome'),'<br />','</dd>'); ?>
        <?php endif; ?>
       <dt class="meta_comment"><a href="#respond"><?php _e('Write comment','monochrome'); ?></a></dt>
        <?php edit_post_link(__('[ EDIT ]', 'monochrome'), '<dd>', '</dd>' ); ?>
     </dl>
    </div>

<?php endwhile; else: ?>
    <div class="post_odd">
     <div class="post clearfix">
      <div class="post_content_wrapper">
       <?php _e("Sorry, but you are looking for something that isn't here.","monochrome"); ?>
      </div>
      <div class="post_meta">
      </div>
     </div>
    </div>
<?php endif; ?>

<div id="comments_wrapper">
<?php if (function_exists('wp_list_comments')) { comments_template('', true); } else { comments_template(); } ?>
</div>

   </div><!-- #left_col end -->

   <?php get_sidebar(); ?>

  </div><!-- #contents end -->

  <div id="footer">
<?php get_footer(); ?>