<?php

$csThemeSettings = array(
	'menu.showSubmenus' => true,
	'menu.homeCaption' => "Home",
	'menu.showHome' => true,
	'menu.topItemBegin' => "<span class=\"l\"></span><span class=\"r\"></span><span class=\"t\">",
	'menu.topItemEnd' => "</span>"
);

load_theme_textdomain('kubrick');

$themename = "CrimsonSky";
$shortname = "crimsky";
$default_footer_content = "Copyright &copy; 2009 ".get_bloginfo('name').". All Rights Reserved.";
$options = array (
                array(  "name" => "HTML",
                        "desc" => sprintf(__('<strong>XHTML:</strong> You can use these tags: <code>%s</code>', 'kubrick'), 'a, abbr, acronym, em, b, i, strike, strong, span'),
                        "id" => "cs_footer_content",
                        "std" => $default_footer_content,
                        "type" => "textarea")
          );
       
	
function cs_update_option($key, $value){
	update_option($key, (get_magic_quotes_gpc()) ? stripslashes($value) : $value);
}

function cs_add_admin() {



    global $themename, $shortname, $options;

    if ( $_GET['page'] == basename(__FILE__) ) {
   
        if ('save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    if($value['type'] != 'multicheck'){
                        cs_update_option( $value['id'], $_REQUEST[ $value['id'] ] );
                    }else{
                        foreach($value['options'] as $mc_key => $mc_value){
                            $up_opt = $value['id'].'_'.$mc_key;
                            cs_update_option($up_opt, $_REQUEST[$up_opt] );
                        }
                    }
                }
                foreach ($options as $value) {
                    if($value['type'] != 'multicheck'){
                        if( isset( $_REQUEST[ $value['id'] ] ) ) { cs_update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); }
                    }else{
                        foreach($value['options'] as $mc_key => $mc_value){
                            $up_opt = $value['id'].'_'.$mc_key;
                            if( isset( $_REQUEST[ $up_opt ] ) ) { cs_update_option( $up_opt, $_REQUEST[ $up_opt ]  ); } else { delete_option( $up_opt ); }
                        }
                    }
                }
                header("Location: themes.php?page=functions.php&saved=true");
                die;
        } 
    }

    add_theme_page("Footer", "Footer", 'edit_themes', basename(__FILE__), 'cs_admin');

}

function cs_admin() {
    global $themename, $shortname, $options;
    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
?>
<div class="wrap">
	<h2>Footer</h2>

	<form method="post">

		<table class="optiontable" style="width:100%;">

<?php foreach ($options as $value) {
   
    switch ( $value['type'] ) {
        case 'text':
        option_wrapper_header($value);
        ?>
                <input style="width:100%;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" />
        <?php
        option_wrapper_footer($value);
        break;
       
        case 'select':
        option_wrapper_header($value);
        ?>
                <select style="width:70%;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                    <?php foreach ($value['options'] as $option) { ?>
                    <option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
                    <?php } ?>
                </select>
        <?php
        option_wrapper_footer($value);
        break;
       
        case 'textarea':
        $ta_options = $value['options'];
        option_wrapper_header($value);
        ?>
                <textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="width:100%;height:100px;"><?php
                if( get_settings($value['id']) !== false) {
                        echo get_settings($value['id']);
                    }else{
                        echo $value['std'];
                }?></textarea>
        <?php
        option_wrapper_footer($value);
        break;

        case "radio":
        option_wrapper_header($value);
       
        foreach ($value['options'] as $key=>$option) {
                $radio_setting = get_settings($value['id']);
                if($radio_setting != ''){
                    if ($key == get_settings($value['id']) ) {
                        $checked = "checked=\"checked\"";
                        } else {
                            $checked = "";
                        }
                }else{
                    if($key == $value['std']){
                        $checked = "checked=\"checked\"";
                    }else{
                        $checked = "";
                    }
                }?>
                <input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><?php echo $option; ?><br />
        <?php
        }
        
        option_wrapper_footer($value);
        break;
       
        case "checkbox":
        option_wrapper_header($value);
                        if(get_settings($value['id'])){
                            $checked = "checked=\"checked\"";
                        }else{
                            $checked = "";
                        }
                    ?>
                    <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
        <?php
        option_wrapper_footer($value);
        break;

        case "multicheck":
        option_wrapper_header($value);
       
        foreach ($value['options'] as $key=>$option) {
                 $pn_key = $value['id'] . '_' . $key;
                $checkbox_setting = get_settings($pn_key);
                if($checkbox_setting != ''){
                    if (get_settings($pn_key) ) {
                        $checked = "checked=\"checked\"";
                        } else {
                            $checked = "";
                        }
                }else{
                    if($key == $value['std']){
                        $checked = "checked=\"checked\"";
                    }else{
                        $checked = "";
                    }
                }?>
                <input type="checkbox" name="<?php echo $pn_key; ?>" id="<?php echo $pn_key; ?>" value="true" <?php echo $checked; ?> /><label for="<?php echo $pn_key; ?>"><?php echo $option; ?></label><br />
        <?php
        }
        
        option_wrapper_footer($value);
        break;
       
        case "heading":
        ?>
        <tr valign="top">
            <td colspan="2" style="text-align: center;"><h3><?php echo $value['name']; ?></h3></td>
        </tr>
        <?php
        break;
       
        default:

        break;
    }
}
?>

		</table>

		<p class="submit">
			<input name="save" type="submit" value="Save changes" />
			<input type="hidden" name="action" value="save" />
		</p>
	</form>
</div>
<?php
}

function option_wrapper_header($values){
    ?>
    <tr valign="top">
        <th scope="row" style="width:1%;white-space: nowrap;"><?php echo $values['name']; ?>:</th>
        <td>
    <?php
}

function option_wrapper_footer($values){
    ?>
        </td>
    </tr>
    <tr valign="top">
        <td>&nbsp;</td><td><small><?php echo $values['desc']; ?></small></td>
    </tr>
    <?php
}


add_action('admin_menu', 'cs_add_admin'); 

if (!function_exists('get_search_form')) {
	function get_search_form()
	{
		include (TEMPLATEPATH . "/searchform.php");
	}
}

if (!function_exists('get_previous_posts_link')) {
	function get_previous_posts_link($label)
	{
		ob_start();
		previous_posts_link($label);
		return ob_get_clean();
	}
}

if (!function_exists('get_next_posts_link')) {
	function get_next_posts_link($label)
	{
		ob_start();
		next_posts_link($label);
		return ob_get_clean();
	}
}

if (!function_exists('get_previous_post_link')) {
	function get_previous_post_link($label)
	{
		ob_start();
		previous_post_link($label);
		return ob_get_clean();
	}
}

if (!function_exists('get_next_post_link')) {
	function get_next_post_link($label)
	{
		ob_start();
		next_post_link($label);
		return ob_get_clean();
	}
}

function cs_comment($comment, $args, $depth)
{
	 $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
<div class="cs-Post">
         <div class="cs-Post-body">
     <div class="cs-Post-inner cs-csicle">
     
<div class="cs-PostContent">
     
      <div class="comment-author vcard">
         <?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>
         <cite class="fn"><?php comment_author_link(); ?>:</cite>
      </div>
      <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>

      <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link('('.__('Edit').')','  ','') ?></div>

      <?php comment_text() ?>

      <div class="reply">
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>

      </div>
      <div class="cleared"></div>
      

      </div>
      
          </div>
      </div>
      
     </div>
<?php
}


if (function_exists('register_sidebars')) {
	register_sidebars(2, array(
		'before_widget' => '<div id="%1$s" class="widget %2$s">'.'<!--- BEGIN Widget --->',
		'before_title' => '<!--- BEGIN WidgetTitle --->',
		'after_title' => '<!--- END WidgetTitle --->',
		'after_widget' => '<!--- END Widget --->'.'</div>'
	));
}

function cs_normalize_widget_style_tokens($content) {
	$bw = '<!--- BEGIN Widget --->';
	$bwt = '<!--- BEGIN WidgetTitle --->';
	$ewt = '<!--- END WidgetTitle --->';
	$bwc = '<!--- BEGIN WidgetContent --->';
	$ewc = '<!--- END WidgetContent --->';
	$ew = '<!--- END Widget --->';
	$result = '';
	$startBlock = 0;
	$endBlock = 0;
	while (true) {
		$startBlock = strpos($content, $bw, $endBlock);
		if (false === $startBlock) {
			$result .= substr($content, $endBlock);
			break;
		}
		$result .= substr($content, $endBlock, $startBlock - $endBlock);
		$endBlock = strpos($content, $ew, $startBlock);
		if (false === $endBlock) {
			$result .= substr($content, $endBlock);
			break;
		}
		$endBlock += strlen($ew);
		$widgetContent = substr($content, $startBlock, $endBlock - $startBlock);
		$beginTitlePos = strpos($widgetContent, $bwt);
		$endTitlePos = strpos($widgetContent, $ewt);
		if ((false == $beginTitlePos) xor (false == $endTitlePos)) {
			$widgetContent = str_replace($bwt, '', $widgetContent);
			$widgetContent = str_replace($ewt, '', $widgetContent);
		} else {
			$beginTitleText = $beginTitlePos + strlen($bwt);
			$titleContent = substr($widgetContent, $beginTitleText, $endTitlePos - $beginTitleText);
			if ('&nbsp;' == $titleContent) {
				$widgetContent = substr($widgetContent, 0, $beginTitlePos)
					. substr($widgetContent, $endTitlePos + strlen($ewt));
			}
		}
		if (false === strpos($widgetContent, $bwt)) {
			$widgetContent = str_replace($bw, $bw . $bwc, $widgetContent);
		} else {
			$widgetContent = str_replace($ewt, $ewt . $bwc, $widgetContent);
		}
		$result .= str_replace($ew, $ewc . $ew, $widgetContent);
	}
	return $result;
}

function cs_sidebar($index = 1)
{
	if (!function_exists('dynamic_sidebar')) return false;
	ob_start();
	$success = dynamic_sidebar($index);
	$content = ob_get_clean();
	if (!$success) return false;
	$content = cs_normalize_widget_style_tokens($content);
	$replaces = array(
		'<!--- BEGIN Widget --->' => "<div class=\"cs-Block\">\r\n    <div class=\"cs-Block-tl\"></div>\r\n    <div class=\"cs-Block-tr\"></div>\r\n    <div class=\"cs-Block-bl\"></div>\r\n    <div class=\"cs-Block-br\"></div>\r\n    <div class=\"cs-Block-tc\"></div>\r\n    <div class=\"cs-Block-bc\"></div>\r\n    <div class=\"cs-Block-cl\"></div>\r\n    <div class=\"cs-Block-cr\"></div>\r\n    <div class=\"cs-Block-cc\"></div>\r\n    <div class=\"cs-Block-body\">\r\n",
		'<!--- BEGIN WidgetTitle --->' => "<div class=\"cs-BlockHeader\">\r\n    <div class=\"cs-header-tag-icon\">\r\n        <div class=\"t\">",
		'<!--- END WidgetTitle --->' => "</div>\r\n    </div>\r\n</div>",
		'<!--- BEGIN WidgetContent --->' => "<div class=\"cs-BlockContent\">\r\n    <div class=\"cs-BlockContent-body\">\r\n",
		'<!--- END WidgetContent --->' => "\r\n    </div>\r\n</div>\r\n",
		'<!--- END Widget --->' => "\r\n    </div>\r\n</div>\r\n"
	);
	$bwt = '<!--- BEGIN WidgetTitle --->';
	$ewt = '<!--- END WidgetTitle --->';
	if ('' == $replaces[$bwt] && '' == $replaces[$ewt]) {
		$startTitle = 0;
		$endTitle = 0;
		$result = '';
		while (true) {
			$startTitle = strpos($content, $bwt, $endTitle);
			if (false == $startTitle) {
				$result .= substr($content, $endTitle);
				break;
			}
			$result .= substr($content, $endTitle, $startTitle - $endTitle);
			$endTitle = strpos($content, $ewt, $startTitle);
			if (false == $endTitle) {
				$result .= substr($content, $startTitle);
				break;
			}
			$endTitle += strlen($ewt);
		}
		$content = $result;
	}
	$content = str_replace(array_keys($replaces), array_values($replaces), $content);
	echo $content;
	return true;
}

function cs_activeID($pages){
	$result = null;
	foreach ($pages as $index => $page){
		if (is_page($page->ID)) { 
			$result = $page;
			break;
		}
	}
	while($result && $result->post_parent) {
		foreach ($pages as $parent){
			if ($parent->ID == $result->post_parent) {
				$result = $parent;
				breack;
			}
		}
	}
	return ($result ? $result->ID : null);
}

function cs_blogID($pages){
	$result = null;
	if(!'page' == get_option('show_on_front')) return $result;
	$blogID = get_option('page_for_posts');
	if (!$blogID) return $result;
	foreach ($pages as $index => $page){
		if ($page->ID == $blogID) { 
			$result = $page;
			break;
		}
	}
	while($result && $result->post_parent) {
		foreach ($pages as $parent){
			if ($parent->ID == $result->post_parent) {
				$result = $parent;
				breack;
			}
		}
	}
	return ($result ? $result->ID : null);
}

function cs_process_front(&$pages){
	if ('page' != get_option('show_on_front')) return;
	$frontID = get_option('page_on_front');
	if (!$frontID) return;
	foreach ($pages as $index => $page)
		if($page->ID == $frontID) {
			unset($pages[$index]);
			$page->post_parent = '0';
			$page->menu_order = '0';
			array_unshift($pages, $page);
			break;
		}
}

function cs_topIDs($pages){
	$result = array();
	foreach ($pages as $index => $page)
		if (!$page->post_parent) $result[]=$page->ID;
	return $result;
}

function cs_remove_subitems(&$pages){
	foreach ($pages as $index => $page)
		if ($page->post_parent) unset($pages[$index]);
}

function cs_header_page_list_filter($pages)
{
	global $csThemeSettings;
	cs_process_front($pages);
	$csThemeSettings['menu.topItemIDs'] = cs_topIDs($pages);
	$csThemeSettings['menu.activeID'] = cs_activeID($pages);
	$csThemeSettings['menu.blogID'] = cs_blogID($pages);
	if (!$csThemeSettings['menu.showSubmenus']) cs_remove_subitems($pages);
	return $pages;
}

function cs_list_pages_filter($output)
{
	global $csThemeSettings;
	$pref ='page-item-';
	if($csThemeSettings['menu.topItemIDs'])
		foreach($csThemeSettings['menu.topItemIDs'] as $id){
			$output = preg_replace('~<li class="([^"]*)\b(' . $pref . $id . ')\b([^"]*)"><a ([^>]+)>([^<]*)</a>~',
				'<li class="$1$2$3"><a $4>' . $csThemeSettings['menu.topItemBegin']
					. '$5' . $csThemeSettings['menu.topItemEnd'] . '</a>', $output, 1);
		}
		
	$frontID = null;
	$blogID = null;
	if('page' == get_option('show_on_front')) {
		$frontID = get_option('page_on_front');
		$blogID = $csThemeSettings['menu.blogID'];
	}
	if ($frontID) 
		$output = preg_replace('~<li class="([^"]*)\b(' . $pref . $frontID . ')\b([^"]*)"><a href="([^"]*)" ~',
			'<li class="$1$2$3"><a href="'. get_option('home') .'" ', $output, 1); 

	if ((is_home() && $blogID) || $csThemeSettings['menu.activeID'])
		$output = preg_replace('~<li class="([^"]*)\b(' . $pref . (is_home() ? $blogID : $csThemeSettings['menu.activeID']) . ')\b([^"]*)"><a ~',
			'<li class="$1$2$3"><a class="active" ', $output, 1);
	return $output;
}

function cs_menu_items()
{
	global $csThemeSettings;
	if (true === $csThemeSettings['menu.showHome'] && 'page' != get_option('show_on_front'))
		echo '<li><a' . (is_home() ? ' class="active"' : '') . ' href="' . get_option('home') . '">'.$csThemeSettings['menu.topItemBegin']
			. $csThemeSettings['menu.homeCaption'] . $csThemeSettings['menu.topItemEnd'] . '</a></li>';
	add_action('get_pages', 'cs_header_page_list_filter');
	add_action('wp_list_pages', 'cs_list_pages_filter');
	wp_list_pages('title_li=');
	remove_action('wp_list_pages', 'cs_list_pages_filter');
	remove_action('get_pages', 'cs_header_page_list_filter');
}

add_filter('comments_template', 'legacy_comments');  
function legacy_comments($file) {  
    if(!function_exists('wp_list_comments')) : // WP 2.7-only check  
    $file = TEMPLATEPATH.'/legacy.comments.php';  
    endif;  
    return $file;  
}  