<?php // Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
		die ('Please do not load this page directly. Thanks!');
	}

	if ( post_password_required() ) {
		echo '<p class="nocomments">This post is password protected. Enter the password to view comments.</p>';
		return;
	}
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>
	<h2 class="commentheading" id="comments"><?php comments_number('No Comments', '1  Comment', '% Comments' );?></h2>
	<ol class="commentlist">
		<?php wp_list_comments(); ?>
	</ol>
	<div class="navigation commentnavigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>

<?php endif; ?>

<div id="respond">
<h2 class="commentheading"><?php comment_form_title(); ?></h2>

<?php if ('open' == $post->comment_status) : ?>

<div id="cancel-comment-reply"><small><?php cancel_comment_reply_link() ?></small></div>


<?php if ( get_option('comment_registration') && !$user_ID ) : ?>

<p class="commentsclosed">You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>

<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p class="loggedinAs">Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(); ?>" title="Log out of this account">Log out &raquo;</a></p>

<?php else : ?>

<p class="loggedinAs">
<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
<label for="author"><small>Name <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
<label for="email"><small>Mail (will not be published) <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<label for="url"><small>Website</small></label></p>

<?php endif; ?>

<?php comment_id_fields(); ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->

<p class="commentWrapper"><textarea name="comment" id="comment" rows="10" tabindex="4" cols="30"></textarea></p>

<div><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" /></div>

<?php  do_action('comment_form', $post->ID); ?>

</form>
</div><!-- end of respond id -->

<?php endif; // If registration required and not logged in ?>

<?php else: // If comments are closed ?>
	<p class="commentsclosed">Comments are closed.</p>
        </div>
<?php endif; // if you delete this the sky will fall on your head ?>

