   <ul id="copyright">
    <li style="background:none;">
                <?php
                        global $wpdb;
                        $post_datetimes = $wpdb->get_row($wpdb->prepare("SELECT YEAR(min(post_date_gmt)) AS firstyear, YEAR(max(post_date_gmt)) AS lastyear FROM $wpdb->posts WHERE post_date_gmt > 1970"));
                        if ($post_datetimes) {
                                $firstpost_year = $post_datetimes->firstyear;
                                $lastpost_year = $post_datetimes->lastyear;

                                $copyright = __('Copyright &copy;&nbsp; ', 'monochrome') . $firstpost_year;
                                if($firstpost_year != $lastpost_year) {
                                        $copyright .= '-'. $lastpost_year;
                                }
                                $copyright .= ' ';

                                echo $copyright;
                        }
                ?>
    &nbsp;<a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></li>
    <li><a href="http://www.mono-lab.net/">Theme designed by mono-lab</a></li>
    <li><a href="http://wordpress.org/">Powerd by WordPress</a></li>
   </ul>
  </div>
 
</div><!-- #wrapper end -->

<?php $options = get_option('mc_options'); if ($options['pagetop']) : ?>
<div id="return_top">
 <a href="#wrapper"></a>
</div>
<?php endif; ?>

<script type="text/javascript">
	var menu=new menu.dd("menu");
	menu.init("menu","menuhover");
</script>
<?php wp_footer(); ?>

<!-- Added by Yann, code of Google Analytics -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-10785988-1");
pageTracker._trackPageview();
} catch(err) {}</script>

</body>
</html>
