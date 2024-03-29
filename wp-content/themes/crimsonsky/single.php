<?php get_header(); ?>
<div class="cs-contentLayout">
<div class="cs-content">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php
$prev_link = get_previous_post_link('&laquo; %link');
$next_link = get_next_post_link('%link &raquo;');
?>
<?php if ($prev_link || $next_link): ?>
<div class="cs-Post">
    <div class="cs-Post-body">
<div class="cs-Post-inner cs-csicle">

<div class="cs-PostContent">

<div class="navigation">
	<div class="alignleft"><?php echo $prev_link; ?></div>
	<div class="alignright"><?php echo $next_link; ?></div>
</div>

</div>
<div class="cleared"></div>


</div>

    </div>
</div>

<?php endif; ?>
<div class="cs-Post">
    <div class="cs-Post-body">
<div class="cs-Post-inner cs-csicle">
<h2 class="cs-PostHeaderIcon-wrapper">
<span class="cs-PostHeader"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'kubrick'), the_title_attribute('echo=0')); ?>">
<?php the_title(); ?>
</a></span>
</h2>
<?php $icons = array(); ?>
<?php if (!is_page()): ?><?php ob_start(); ?><?php the_time(__('F jS, Y', 'kubrick')) ?>
<?php $icons[] = ob_get_clean(); ?><?php endif; ?><?php if (!is_page()): ?><?php ob_start(); ?><?php _e('Author', 'kubrick'); ?>: <a href="#" title="<?php _e('Author', 'kubrick'); ?>"><?php the_author() ?></a>
<?php $icons[] = ob_get_clean(); ?><?php endif; ?><?php if (current_user_can('edit_post', $post->ID)): ?><?php ob_start(); ?><?php edit_post_link(__('Edit', 'kubrick'), ''); ?>
<?php $icons[] = ob_get_clean(); ?><?php endif; ?><?php if (0 != count($icons)): ?>
<div class="cs-PostHeaderIcons cs-metadata-icons">
<?php echo implode(' | ', $icons); ?>

</div>
<?php endif; ?>
<div class="cs-PostContent">
<?php if (is_search()) the_excerpt(); else the_content(__('Read the rest of this entry &raquo;', 'kubrick')); ?>
<?php wp_link_pages(); ?>
</div>
<div class="cleared"></div>
<?php $icons = array(); ?>
<?php if (!is_page()): ?><?php ob_start(); ?><?php printf(__('Posted in %s', 'kubrick'), get_the_category_list(', ')); ?>
<?php $icons[] = ob_get_clean(); ?><?php endif; ?><?php if (!is_page() && get_the_tags()): ?><?php ob_start(); ?><?php the_tags(__('Tags:', 'kubrick') . ' ', ', ', ' '); ?>
<?php $icons[] = ob_get_clean(); ?><?php endif; ?><?php if (!is_page() && !is_single()): ?><?php ob_start(); ?><?php comments_popup_link(__('No Comments &#187;', 'kubrick'), __('1 Comment &#187;', 'kubrick'), __('% Comments &#187;', 'kubrick'), '', __('Comments Closed', 'kubrick') ); ?>
<?php $icons[] = ob_get_clean(); ?><?php endif; ?><?php if (0 != count($icons)): ?>
<div class="cs-PostFooterIcons cs-metadata-icons">
<?php echo implode(' | ', $icons); ?>

</div>
<?php endif; ?>

</div>

    </div>
</div>

<?php comments_template(); ?>
<?php endwhile; ?>
<?php else: ?>
<h2 class="center"><?php _e('Sorry, no posts matched your criteria.', 'kubrick'); ?></h2>
<?php endif; ?>

</div>
<?php include (TEMPLATEPATH . '/sidebar1.php'); ?><?php include (TEMPLATEPATH . '/sidebar2.php'); ?>
</div>
<div class="cleared"></div>

<?php get_footer(); ?>