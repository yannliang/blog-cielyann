<?php

class BWBPS_Layout{
	var $options;
	
	var $custFields;
	var $stdFields;
	var $attFields;
	var $psOptions;
	var $tabindex;
	var $moderateNonceCount = 0;
	
	var $layouts;
	
	var $ratings;
	
	//Constructor
	function BWBPS_Layout($options, $cfList){
		$this->psOptions = $options;
		$this->custFields = $cfList;
		$this->stdFields = $this->getStandardFields();
		$this->attFields = $this->getFieldsWithAtts();
	}
	
	function getStandardFields(){
		return array('caption'
			, 'url'
			, 'image'
			, 'linked_image'
			, 'image_id'
			, 'gallery_id'
			, 'thumbnail'
			, 'thumb'
			, 'user_name'
			, 'user_url'
			, 'user_link'
			, 'author_link'
			, 'contributor'
			, 'full_caption'
			, 'date_added'
			, 'file_name'
			, 'gallery_name'
			, 'eval'
			, 'post_id'
			, 'post_url'
			, 'ps_rating'
			, 'image_url'
			, 'caption_escaped'
			, 'thumb_image'
			, 'thumb_url'
			, 'thumb_linktoimage'
		);
	}
	
	function getFieldsWithAtts(){
		return array('caption'
			, 'eval'
			, 'image'
			, 'thumb'
			, 'thumbnail'
			, 'linked_image'
			, 'doc'
			, 'video'
			, 'youtube'
			, 'caption_escaped'
		);
	}
	
	//  Build the Markup that gets inserted into the Content...$g == the gallery data
	function getGallery($g, $layoutName = false, $image=false, $useAlt=false)
	{
		global $post;
		global $wpdb;
				
		$admin = current_user_can('level_10');
		
		$uploads = wp_upload_dir();
		
		//Instantiate the Ratings class if needed
		if($g['poll_id']){
			
			$rate = true;	//Boolean for quick reference to add ratings
			
			if( !isset($this->ratings) ){
				require_once('bwbps-rating.php');
				$this->ratings = new BWBPS_Rating();
			}
			unset($rating);
			
			if(!$this->psOptions['rating_allow_anon'] && !is_user_logged_in()){
				$allow_anon = 0;
			} else {
				$allow_anon = 1;
			}
			
			$rating = array( 
				'image_id' => 0,
				'gallery_id' => $g['gallery_id'],
				'poll_id' => $g['poll_id'],
				'avg_rating' => 0,
				'rating_cnt' => 0,
				'vote_sum' => 0,
				'vote_cnt' => 0,
				'rating_position' => $g['rating_position'],
				'allow_rating' => $allow_anon
			);
		}

		if(!$image){
		
			//Determine if we need to bring back Custom Fields
			if($this->psOptions['use_customfields'] || 
				$this->psOptions['use_customform'] || $layoutName){
				
				$usecustomfields = true;
				
			} else { 
				
				$usecustomfields = false;
				
			}
		
			$images = $this->getGalleryImages($g, $usecustomfields);
			
		}else{
		
			$images[] = $image;
			
		}
	
		//Calculate Pagination variables
		$totRows = $wpdb->num_rows;	// Total # of images (total rows returned in query)
		if($post->photosmash == 'author'){
			$perma = $post->photosmash_link;
		} else {
			$perma = get_permalink($post->ID);	//The permalink for this post
		}
		
		$pagenum = $this->getPageNumbers();
		
		//Set to page 1 if not supplied in Get or Post

		if(!isset($pagenum[$g['gallery_id']]) || $pagenum[$g['gallery_id']] < 1){
			$pagenum[$g['gallery_id']] = 1;
		}	
		
		//Set up Attributes:  caption width, image class name, etc
		if(!$g['thumb_width'] || $g['thumb_width'] < 60){
			$g['captionwidth'] = "style='width: 80px'";
		} else {
			$g['captionwidth'] = "style='width: ".($g['thumb_width'] + 4)."px'";
		}
		//IMAGE CLASS
		if($g['img_class']){$g['imgclass'] = 
			" class='".$g['img_class']."'";} else {$g['imgclass']="";}
		
		//IMAGE REL
		if($g['img_rel']){
			
			$caprel = str_replace("[album]","[album_"
				.$g['gallery_id']."cap]",$g['img_rel']);
				
			$g['img_rel'] = str_replace("[album]","[album_"
				.$g['gallery_id']."]",$g['img_rel']);
			
			if( $caprel == $g['img_rel'] ){ $caprel .= 'cap'; }
			
			$g['url_attr']['imgrel'] = " rel='".$g['img_rel']."'";
			$g['url_attr']['caprel'] = " rel='".$caprel."'";
			
		} else {$g['url_attr']['imgrel']="";}
		
		//NO FOLLOW
		if($g['nofollow_caption']){
			$g['url_attr']['nofollow'] = " rel='external nofollow'";
		}else {$g['url_attr']['nofollow']='';}

		//CAPTION CLASS
		$g['url_attr']['captionclass']= ' class="bwbps_caption"';

		//IMAGES PER ROW
		if($g['img_perrow'] && $g['img_perrow']>0){
			$g['imgsPerRowHTML'] = " style='width: "
				.floor((1/((int)$g['img_perrow']))*100)."%;'";
		} else {
			$g['imgsPerRowHTML'] = " style='margin: 15px;'";
		}
		
		
		//Get the Custom Layout if in use
		if($layoutName){
			$layout = $this->getLayout(false, $layoutName);
		}
		if(!$layout && (int)$g['layout_id'] > -1){
			if((int)$g['layout_id'] == 0){ 
				//use the PhotoSmash Default Layout 
				if($this->psOptions['layout_id'] && $this->psOptions['layout_id'] > -1){
					$layout = $this->getLayout($this->psOptions['layout_id']);		
				} //else, just use the Standard Layout
			} else {
				$layout = $this->getLayout($g['layout_id']);
			}
		}
		
		if($useAlt){
			$imgNum = 1;
		} else {
			$imgNum = 0;
		}
		
		//get the pagination navigation
		if($totRows && $g['img_perpage']){
			
			//What image # do we begin page with?
			$lastImg = $pagenum[$g['gallery_id']] * $g['img_perpage'];
			$startImg = $lastImg - $g['img_perpage'] + 1;
				
			
		} 
		
		
		if($images){
			//Add SetID and Layout ID for use Insert Sets - PhotoSmash Extend
			$images[0]['pext_insert_setid'] = (int)$g['pext_insert_setid'];
			$images[0]['pext_layout_id'] = ($layout ? $layout->layout_id : false);
			$images[0]['pext_start_image'] = $startImg;
			$images[0]['pext_imgs_perpage'] = $g['img_perpage'];
			$images[0]['pext_page_number'] = $pagenum[$g['gallery_id']];
			$images = apply_filters('bwbps_gallery_loop', $images);
						
			$totRows = count($images);
			//get the pagination navigation
			if($totRows && $g['img_perpage']){
				$nav = $this->getPagingNavigation($perma, $pagenum, $totRows, $g, $layout);
			} else {
				$nav = "";
				$startImg = 0;
				$lastImg = $totRows + 1;
			}
			
		
			if($this->psOptions['img_targetnew']){
				$g['url_attr']['imagetargblank'] = " target='_blank' ";
			}
					
			foreach($images as $image){
			
				
				$imgNum++;
				//Pagination - not the most efficient, 
				//but there shouldn't be thousands of images in a gallery
				if($startImg > $imgNum || $lastImg < $imgNum){ continue;}
				
				
				//Handle PSmashExtend Inserts
				if( $image['pext_insert'] ){
				
					if(!$layout){
						$psTable .= $this->getStandardLayout($g, $image);
					} else {
						$psTable .= $image['pext_insert'];
					}
					continue;
				
				}
				
								
				//Handle Rating Code
				if($rate){
					$rating['image_id'] = $image['psimageID'];
					$rating['avg_rating'] = $image['avg_rating'];
					$rating['rating_cnt'] = $image['rating_cnt'];
					$rating['votes_sum'] = $image['votes_sum'];
					$rating['votes_cnt'] = $image['votes_cnt'];
										
					$image['ps_rating'] = $this->ratings->get_rating($rating);
			
				} else {
					$image['ps_rating'] = "";
				}
			
				if( $g['suppress_no_image'] && !$image['file_type'] 
					&& !$image['file_name'] && !$image['thumb_url'] ){
					continue;	
				}
				if( !$image['file_type'] 
					&& !$image['file_name'] && $g['default_image'] 
					&& !$image['thumb_url'] ){
					$image['file_name'] = $g['default_image'];
				} 
				
				if( !$image['thumb_url'] ){
				
					$image['thumb_url'] = PSTHUMBSURL.$image['file_name'];
					$image['medium_url'] = PSTHUMBSURL.$image['file_name'];
					$image['image_url'] = PSIMAGESURL.$image['file_name'];
				
				} else {
				
					// Add the Uploads base URL to the image urls.
					// This way if the user ever moves the blog, everything might still work ;-) 
					// set $uploads at top of function...only do it once
					$image['thumb_url'] = $uploads['baseurl'] . '/' . $image['thumb_url'];
					$image['medium_url'] = $uploads['baseurl'] . '/' . $image['medium_url'];
					$image['image_url'] = $uploads['baseurl'] . '/' . $image['image_url'];
				
				}
			
				$g['modMenu'] = "";
				switch ($image['status']) {
					case -1 :
						$g['modClass'] = 'ps-moderate';
						if($admin){
							$g['modMenu'] = 
								"<br/><span class='ps-modmenu' id='psmod_"
								.$image['psimageID']
								."'><input type='button' "
								."onclick='bwbpsModerateImage(\"approve\", "
								.$image['psimageID']
								.");' value='approve' class='ps-modbutton'/>"
								."<input type='button' onclick='bwbpsModerateImage(\"bury\", "
								.$image['psimageID']
								.");' value='bury' class='ps-modbutton'/></span>";
						}
						break;
	
					case -2 :
						$g['modClass'] = 'ps-buried';
						break;
					
					case -10 :	//special status that will be cleaned out periodically
						
						break;
					default :
						$g['modClass'] = '';
						break;
				}
				
				$image['imgtitle'] = str_replace("'","",$image['image_caption']);
				
				
				/*	***********		Set up the <a href....>		*************  */
				$this->calculateURLs($g, $image, $perma);
				
								
				//Get the Layout:  Standard or Custom
				if(!$layout){
					//Standard Layout
					$psTable .= $this->getStandardLayout($g, $image);
							
				} else {
					//Custom Layout
										
					if($imgNum % 2 == 0){
						$psTableRow .= $this->getCustomLayout($g, $image, $layout, true);	
					} else {
						$psTableRow .= $this->getCustomLayout($g, $image, $layout, false);	
					}
					
					if($layout->cells_perrow){
						if($imgNum % $layout->cells_perrow == 0){
							$psTable .="<tr>".$psTableRow."</tr>";
							$psTableRow = "";
							$imgNum = 0;
						}
					
					} else {
						$psTable .= $psTableRow;
						$psTableRow = "";
					}
				}
			}
			
		} else {
			if(!$layout){
				$psTable .= "<li class='psgal_".$g['gallery_id']
					."' style='height: ".($g['thumb_height'] + 15)
					."px; margin: 15px 0;'><img alt='' 	src='"
					.WP_PLUGIN_URL."/photosmash-galleries/images/"
					."ps_blank.gif' width='1' height='"
					.$g['thumb_height']."' /></li>";
			}
		}
		
		//If using Cells Per Row (for tables in Custom Forms..a setting Advanced)
		//then clean up any left over $psTableRows
		if($layout->cells_perrow && $psTableRow){
			
			$remaining =  $layout->cells_perrow - $imgNum;
			if($remaining > 0){
				for($i =0; $i < $remaining; $i++){
					$psTableRow .= "<td></td>";
				}
			}
			$psTable .="<tr>".$psTableRow."</tr>";
		}
		
		//Gallery Wrapper
		
		if($rate){
				$ratetoggle = "<span class='bwbps-rating-toggle'><a href='javascript: void(0);'"
					. " onclick='bwbpsToggleRatings(". $g['gallery_id'] 
					. "); return false;' title='Toggle image ratings'>Toggle ratings</a></span><div style='clear: both; margin: 0; padding: 0;'></div>";			
		}
		
		if(!$layout){
			//Standard Wrapper
			$ret = "<div class='bwbps_gallery_div' id='bwbps_galcont_".$g['gallery_id']."'>".$ratetoggle."
			<table><tr><td>";
			
			$ret .= "<ul id='bwbps_stdgal_".$g['gallery_id']."' class='bwbps_gallery'>".$psTable;
	
			$ret .= "</ul>
				</td></tr></table>
				".$nav."</div>
				";
		} else {
			//Custom Wrapper
				
			if($layout->wrapper){
				$ret = $layout->wrapper;
				$ret = str_replace('[gallery_id]',$g['gallery_id'], $ret);
				$ret = str_replace('[gallery_name]',$g['gallery_name'], $ret);
				
				$psTable = $ratetoggle . $psTable;
				
				if(strpos($layout->wrapper, '[gallery]')){
					$ret = str_replace('[gallery]',$psTable, $ret);
				}else {
					$ret .= $psTable;
				}
				
			} else {
				$ret = $psTable;
			}
			
			
			
			$ret .= $nav;
			
			//Add CSS
			if(trim($layout->css)){
				$ret = "<style type='text/css'>
				<!--
				".$layout->css."
				-->
				</style>".$ret;
			}
			
			//Need the insertion point to create a holder for adding new images.			
			$ret .= "<div id='bwbpsInsertBox_".$g['gallery_id']."' style='clear: both;'></div>";
		}
	
		
		return $ret;
	}
	
	/**
	 * Get an Array of Page numbers -> uses gallery ID as key
	 * 
	 * @param none
	 */
	function getPageNumbers(){
		if(is_array($_REQUEST)){
			foreach( $_REQUEST as $key => $option ){
			
				if( strpos($key, 'bwbps_page_' ) !== false ){
					
					$pg_gal = ( str_replace('bwbps_page_',"", $key ));
					$pagenum[$pg_gal] = (int)$option;
				
				}
			
			}
		}
				
		if(!is_array($pagenum)){ $pagenum = array(); }
	
		return $pagenum;
	}
	
	
	
	function calculateURLs(&$g, &$image, $perma)
	{
			
		//Deal with cases where they only want links to images on Post Pages
		$filetype = (int)$image['file_type'];
		
		if($filetype == 0 || $filetype == 1 || $filetype == 4 )
		{
			//Set anchor class ... clear it for special cases below
			if($g['anchor_class']){
				$anchor_class = " class='".$g['anchor_class']."'";
			}
			$image['the_image_link'] = "<a href='"
						.$image['image_url']."'"
						.$g['url_attr']['imgrel']." title='".$image['imgtitle']."' "
						.$g['url_attr']['imagetargblank'].$anchor_class.">";
						
			$image['cap_image_link'] = "<a href='"
						.$image['image_url']."'"
						.$g['url_attr']['caprel']." title='".$image['imgtitle']."' "
						.$g['url_attr']['imagetargblank'].">";
			
			// URL when setting for Front/Cat/Archive pages to link thumbnails to the Post
			if( !is_single() && $this->psOptions['imglinks_postpages_only'])
			{
				$image['imgurl'] = "<a href='".$perma."'>";
				$image['imgurl_close'] = "</a>";
			} else {
			// Normal URLs
											
				
				//Deal with special cases where the caption style changes 
				//the thumbnail link.
				if($g['show_imgcaption'] == 8 || $g['show_imgcaption'] == 9 || $g['show_imgcaption'] == 12)
				{

					
					if($this->validURL($image['url'])){
						$theurl = $image['url'];
						$anchor_class = "";

					} else {
						if($this->validURL($image['user_url'])){
							$theurl = $image['user_url'];
							$anchor_class = "";
						} else {
							
							$theurl = $image['image_url'];
							$image['special_url'] = false;
						}
					}
				
					$image['imgurl'] = "<a href='".$theurl."'"
						.$g['url_attr']['imgrel']." title='".$image['imgtitle']."' "
						.$g['url_attr']['imagetargblank'].$anchor_class.">";
						
					$image['capurl'] = "<a href='".$theurl."'"
						.$g['url_attr']['caprel']." title='".$image['imgtitle']."' "
						.$g['url_attr']['imagetargblank'].">";
															
				} else {
			
					$image['imgurl'] = $image['the_image_link'];
					$image['capurl'] = $image['cap_image_link'];
				}
				
				$image['imgurl_close'] = "</a>";
				$image['capurl_close'] = $image['imgurl_close'];
			}
		} else {
			$image['imgurl'] = "";
			$image['imgurl_close'] = "";
			$image['capurl'] = "";
			$image['capurl_close'] = "";
		}
		
	}
	
	
	/**
	 * Get Standard Layout
	 * @return (str) containing a single images block of code, using an LI wrapper
	 *
	 * @param (object) $g - gallery definition array; (object) $image - an image object
	 */
	function getStandardLayout($g, $image){
		
		if( $image['pext_insert'] ){
		
			$insertclass = ' pext_insert';
		
		}
		
		
		$ret = "<li class='psgal_".$g['gallery_id']." "
					.$g['modClass']. $insertclass 
					. "' id='psimg_".$image['psimageID']."'"
					.$g['imgsPerRowHTML'].">
					<div id='psimage_".$image['psimageID']."' "
					.$g['captionwidth']." class='bwbps_image_div'>";
					
		//Handle PSmashExtend Insert
		if( $image['pext_insert'] ){
			
			$ret .= $image['pext_insert'];
			$ret .= "</div>".$g['modMenu']."</li>";					
				
			return $ret;
		
		}
		
		$scaption =  $this->getCaption($g, $image);
		// Get File Field
		$fileField = $this->getFileField($g, $image);	
		if($fileField)
		{
			//Add Rating as an Overlay of Image if $g['rating_position'] == FALSE
			if(!$g['rating_position'] ){
				$ret .= $image['ps_rating'].$image['imgurl'] . $fileField . $image['imgurl_close'];
			} else {
				$ret .= $image['imgurl'] . $fileField . $image['imgurl_close'];
			}
		}
		
		// Get Caption
		//$scaption =  $this->getCaption($g, $image);
		if($scaption) 
		{
			if( $fileField ) { $ret .= "<br/>"; }
			$ret .= $image['capurl'] . $scaption . $image['capurl_close'];
			
		}
		
		//Add Rating After Caption if $g['rating_position'] == TRUE
		if($image['ps_rating'] && $g['rating_position']){
			$ret .= $image['ps_rating'];
		}
				
		$ret .= "</div>".$g['modMenu']."</li>";					
				
		return $ret;
	}
	
	
	
	/**
	 * Get Custom Layout
	 * @return (str) containing a single images block of code, using custom layout def
	 *
	 * @param (object) $g - gallery definition array; (object) $image - an image object
	 * @param (object) $layout - custom layout definition; 
	 * @param (bool) $alt - alternating image or regular (even or odd)
	 */
	function getCustomLayout($g, $image, $layout, $alt){
		
		if($alt){
			//Use Alternate layout
			if(trim($layout->alt_layout)){
				$ret = $layout->alt_layout;
			} else {
				$ret = $layout->layout;
			}
		}else{
		
			$ret = $layout->layout;
		}
		
		//Replace Standard Fields with values
		foreach($this->stdFields as $fld){
			if(!strpos($ret, $fld) === false){
				unset($atts);
				unset($replace);
				if(in_array($fld, $this->attFields)){
					$atts = $this->getFieldAtts($ret, $fld);
										
					$replace = $this->getCFFieldHTML("[".$fld."]", $image, $g, $atts);
				
					$fld = $atts['bwbps_match'];

				} else {
				
					$replace = $this->getCFFieldHTML("[".$fld."]", $image, $g, $atts);
					$fld = "[".$fld."]";
					
				}
				
				$ret = str_replace($fld, $replace, $ret);	

			}
		}
		
		
		//Replace Custom Fields with values
		if($this->psOptions['use_customfields']){
		
		  foreach($this->custFields as $fld){
		
			if(!strpos($ret, '['.$fld->field_name.']') === false){
			
				//Format Date if it's a date
				if( $image[$fld->field_name] && $fld->type == 5){
				
					if($image[$fld->field_name] <> "0000-00-00 00:00:00"){
					
						$val = date($this->getDateFormat()
							,strtotime ($image[$fld->field_name]));
					
					}

				} else {

					$val = $image[$fld->field_name];

				}
				
				$ret = str_replace('['.$fld->field_name.']', $val, $ret);
			}
		  }
		}
		
		return $ret;
	}
	
	function getDateFormat(){
		if(!trim($this->psOptions['date_format'])){
			return "m/d/Y";
		} else {
			return $this->psOptions['date_format'];
		}	
	}
	
	
	/**
	 * Partial Layouts
	 * Usage - in posts, use a shortcode like [psmash id=230 layout=address]
	 *		 - will return the address code based on the values from image id 230
	 * @return (str) containing a block of code based on a layout
	 *
	 * @param (object) $g - gallery definition array; (object) $image - an image object
	 */
	function getPartialLayout($g, $image, $layoutName, $alt=false){
		$g['suppress_no_image'] = false;
		return $this->getGallery($g, $layoutName, $image, $alt);
	
	}
	
	function getFileField($g, $image, $is_thumb=true){
		$ftype = (int)$image['file_type'];
		
		if($is_thumb){
			$psg_imagesurl = $image['thumb_url'];
			
			//Set up thumb size
			if((int)$g['thumb_height'] ){
				$imagesize = " height=" . (int)$g['thumb_height'];
			}
			if( (int)$g['thumb_width'] ){
				$imagesize .= " width=" . (int)$g['thumb_width'];
			}		
		} else {
			$psg_imagesurl = $image['image_url'];
		}
			
		
		switch ( true ) {
		 
			case ( $ftype == 0 || $ftype == 1 ) :	//image
			
				if($image['image_url']){
				$ret = "<img src='".$psg_imagesurl."'".$g['imgclass']
					." alt='".$image['img_alt']."' $imagesize />";
				} else { $ret = ""; }
			
				break;
			
			case ( $ftype == 2 ) :	//direct link
							
				if( $this->psValidateURL($image['file_url']) )
				{
					$ret = "<img src='".$image['file_url']."'".$g['imgclass']
					." alt='".$image['img_alt']."' " . $imagesize . " />";
				} else { $ret = ""; }
				
				
				break;
				
			case ( $ftype == 3 ) :	//youtube
			
				$thumbheight = "";
				$thumbwidth = "";
				if($g['thumb_width']){ $width = (int)$g['thumb_width'];}
				if($g['thumb_height']){ $height = (int)$g['thumb_height'];}
				
				$width = $width ? $width : 320;
				$height = $height ? $height : 265;
				
				if( $image['file_url'] ){
					$ret = '<span class="youtube"><object width="'.$width.'" height="'.$height.'"><param name="movie" value="http://www.youtube-nocookie.com/v/FAlWxZK-ps4&hl=en&fs=1&rel=0&color1=0xe1600f&color2=0xfebd01"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="'
					. htmlspecialchars("http://www.youtube-nocookie.com/v/" 
					. $image['file_url'] 
					. "&hl=en&fs=1&rel=0&color1=0xe1600f&color2=0xfebd01") 
					. '" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$width.'" height="'.$height.'"></embed></object></span>';
				} else {
				 	$ret = "";
				}
				
				break;
			
			case ( $ftype == 4 ) :	//video
				
				/*  Doesn't Work
				
				$thumbheight = "";
				$thumbwidth = "";
				if($g['thumb_width']){ $width = (int)$g['thumb_width'];}
				if($g['thumb_height']){ $height = (int)$g['thumb_height'];}
				
				$width = $width ? $width : 320;
				$height = $height ? $height : 265;
				
				if( $image['file_url'] ){
					if($image['file_name']){
											
						$ret = "<img src='".$psg_imagesurl.$image['file_name']."'".$g['imgclass']
							." alt='".$image['img_alt']."' />";
						
					} else {
			
						$ret = "<img src='".BWBPSPLUGINURL."/images/no_image.gif'".$g['imgclass']
							." alt='".$image['img_alt']."' />";
					
					}
					
				} 
				
				*/
				
				break;
			
			default :
				
				break;
		}
		
		return $ret; 
	}
	
	/**
	 * Get TaggedField
	 * @return (str) containing a single images block of code, using an LI wrapper
	 *
	 * @param (object) $g - gallery definition array; (object) $image - an image object
	 */
	 
	function getCFFieldHTML($fld, $image, $g, $atts){
		
		//Set up thumb size
		if((int)$g['thumb_height'] ){
			$thumbsize = " height=" . (int)$g['thumb_height'];
		}
		if( (int)$g['thumb_width'] ){
			$thumbsize .= " width=" . (int)$g['thumb_width'];
		}
		
		/* Set up image size */
		if($g['enforce_sizes']){
			if((int)$g['image_height'] ){
				$imagesize = " height=" . (int)$g['image_height'];
			}
			if( (int)$g['image_width'] ){
				$imagesize .= " width=" . (int)$g['image_width'];
			}
		}
		
		//Clean up URLs
		$image['user_url'] = esc_url($image['user_url']);
		$image['url'] = esc_url($image['url']);
			
		$ftype = (int)$image['file_type'];
		
		if($ftype == 3 && ($fld == '[thumb]' || $fld == '[thumbnail]' )) {
			$fld = '[youtube]';
		}
		if($ftype == 4 && ($fld == '[thumb]' || $fld == '[thumbnail]' )) {
			$fld = '[video]';
		}
	
		//Fix up the image Alt for images
		if( is_array($atts)){
		
			//Work with the ALT attribute in Images
			if(array_key_exists( 'alt_field', $atts ) ){
				if ( $atts['alt_field'] && $image[$atts['alt_field']] ){
					$image['img_alt'] = $image[$atts['alt_field']];
					if( $atts['before_alt'] ){
						$image['img_alt'] = $atts['before_alt'] . $image['img_alt'];
					}
					if( $atts['after_alt'] ){
						$image['img_alt'] = $atts['after_alt'] . $image['img_alt'];
					}
					$image['img_alt'] = str_replace("'","",$image['img_alt']);
				}
			} else {
				$image['img_alt'] = $image['imgtitle'];
			}
			
			//Work with the Link in Image, Thumbs, etc
			if(array_key_exists('link_to', $atts)){
				switch ($atts['link_to'] ){
				
					case "none" :
						$image['imgurl'] = "";
						$image['imgurl_close'] = "";
						break;
						
					case "post_url" :
						$image['imgurl'] = $this->getCustomFormURL($g, $image);
						break;
				
					default :
						break;
				}
				
			}
			
		} else {
			$image['img_alt'] = $image['imgtitle'];
		}
		
		switch ($fld){
			case '[image]' :
				$ret = $this->getFileField($g, $image, false);
				break;
			
			case '[image_url]' :
				if($image['thumb_url']){
				  
					$ret = $image['image_url'];
						
				} else { $ret = ""; }
				break;
				
			case '[doc]' :
				$ret = $this->getFileField($g, $image, false);
				break;
				
			case '[youtube]' :
				$ret = $this->getFileField($g, $image);
				break;
				
			case '[video]' :
				$ret = $this->getFileField($g, $image);
				break;
				
			case '[linked_image]' :
				if($image['thumb_url']){
				  
					$ret = $image['imgurl']."
						<img src='".$image['image_url']."'".$g['imgclass']
						. " alt='".$image['img_alt']."' $imagesize />"
						. $image['imgurl_close'];
						
				} else { $ret = ""; }
				break;
				
			case '[thumbnail]' :
				if($image['thumb_url']){
				
					$ret = $image['imgurl']."
						<img src='".$image['thumb_url']."'".$g['imgclass']
						." alt='".$image['img_alt']."' $thumbsize />"
						.$image['imgurl_close'];
					
				} else { $ret = ""; }
				
				break;
			
			case '[thumb]' :
				if($image['thumb_url']){
				
					$ret = $image['imgurl']."
						<img src='".$image['thumb_url']."'".$g['imgclass']
						." alt='".$image['img_alt']."' $thumbsize />"
						.$image['imgurl_close'];
					
				} else { $ret = ""; }
				
				break;
				
			case '[thumb_linktoimage]' :
				if($image['thumb_url']){
				
					$ret = $image['the_image_link']."
						<img src='".$image['thumb_url']."'".$g['imgclass']
						." alt='".$image['img_alt']."' $thumbsize />"
						.$image['imgurl_close'];
					
				} else { $ret = ""; }
				
				break;
				
				
			case '[thumb_image]' :
				if($image['thumb_url']){
				
					$ret = "
						<img src='".$image['thumb_url']."'".$g['imgclass']
						." alt='".$image['img_alt']."' $thumbsize />";
					
				} else { $ret = ""; }
				
				break;
			
			case '[thumb_url]' :
				if($image['thumb_url']){
				
					$ret = $image['thumb_url'];
					
				} else { $ret = ""; }
				
				break;
			
			case '[medium]' :
				if($image['thumb_url']){
				
					//Set up medium size
					if($g['enforce_sizes']){
						if((int)$g['medium_height'] ){
							$mediumsize = " height=" . (int)$g['medium_height'];
						}
						if( (int)$g['medium_width'] ){
							$mediumsize .= " width=" . (int)$g['medium_width'];
						}
					}
				
					$ret = $image['imgurl']."
						<img src='".$image['medium_url']."'".$g['imgclass']
						." alt='".$image['img_alt']."' $mediumsize />"
						.$image['imgurl_close'];
					
				} else { $ret = ""; }
				
				break;
				
			case '[image_id]' :
				$ret = $image['psimageID'];
				break;
			
			case '[gallery_id]' :
				$ret = $g['gallery_id'];
				break;
				
			case '[gallery_name]' :
				$ret = $g['gallery_name'];
				break;
			
			case '[caption]' :
				
				$ret = $image['image_caption'];
								
				if( is_array($atts) && ((int)$atts['length'] || ((int)$atts['nonpost_length'] && !is_single()) ) ){
					$len = (int)$atts['nonpost_length'] ? (int)$atts['nonpost_length'] :
						(int)$atts['length'];
					
					if( strlen($ret) > $len) {
					
						$ret = substr($ret, 0, $len);
						if($atts['more_link']){
							$more = $atts['more_link'];
							if((int)$image['post_id']){
								$post_perma = get_permalink((int)$image['post_id']);
							} else {
								$post_perma = get_permalink((int)$g['post_id']);
							}
							if($post_perma){
								$more = "<a href='".$post_perma."' title='View post'>"
									.$more."</a>";
							}
							$ret .= $more;
						} else {
							if($atts['more_text']){
								$ret .= $atts['more_text'];
							}
						}

					}
									
				}
				
				break;
			
			case '[caption_escaped]' :
				$ret = $image['image_caption'];
								
				if( is_array($atts) && ((int)$atts['length'] || ((int)$atts['nonpost_length'] 
					&& !is_single()) ) )
				{
					$len = (int)$atts['nonpost_length'] ? (int)$atts['nonpost_length'] :
						(int)$atts['length'];
					
					if( strlen($ret) > $len) {
					
						$ret = substr($ret, 0, $len);
					}
				}
				$ret = esc_attr__($ret);
				break;
			
			case '[post_url]' :
				if((int)trim($image['post_id'])){
					$ret = get_permalink((int)$image['post_id']);
				} else {
					if((int)$g['post_id']){
						$ret = get_permalink((int)$g['post_id']);
					} else {
						$ret = $image['imgurl'];
					}
				}
				
				break;
				
			case '[post_id]' :
				if((int)trim($image['post_id'])){
					$ret = (int)$image['post_id'];
				} else {
					$ret = (int)$g['post_id'];
				}
				break;
			
			case '[file_name]' :
				$ret = $image['image_name'];
				break;
			
			case '[date_added]' :
				$ret = date($this->getDateFormat(4)
						,strtotime ($image['created_date']));
				
				break;
			
			case '[full_caption]' :
				$ret = $this->getCaption($g, $image);
				break;
			
			case '[user_name]' :
				
				$ret = $this->calcUserName($image['user_login'], $image['user_nicename'], $image['display_name']);
				
				break;
				
			case '[contributor]' :
				
				$ret = $this->calcUserName($image['user_login'], $image['user_nicename'], $image['display_name']);
				
				break;
						
			case '[user_link]' :
				$ret = "";
				if($this->calcUserName($image['user_login'], $image['user_nicename'], $image['display_name'])){
					if($image['user_url'] && $this->validURL($image['user_url'])){
						$ret = "<a href='".$image['user_url']."' title=''>"
							.$this->calcUserName($image['user_login'], $image['user_nicename'], $image['display_name'])."</a>";
					} else {
						$ret = $this->calcUserName($image['user_login'], $image['user_nicename'], $image['display_name']);
					}
				} else {
					$ret = "anonymous";
				}
				break;
				
			case '[user_url]' :
				$ret = "";
				if($image['user_url'] && $this->validURL($image['user_url'])){
					$ret =  $image['user_url'];
				}
				
				break;
			
			case '[author_link]' :

				if( (int)$image['user_id'] ){
									
					$ret = get_author_posts_url($image['user_id']);
					
					if($ret){
						$name = $this->calcUserName($image['user_login'], $image['user_nicename'], $image['display_name']);
						$ret = "<a href='".$ret."' title='View all images by contributor'>".$name."</a";
					}
				
				} else {
				
					$ret = "";
				
				}
								
				break;
				
				
			/*
			case '[contributor_url]' :
				$ret = "";
				if($this->calcUserName($image['user_login'], $image['user_nicename'], $image['display_name'])){
					if($image['user_url'] && $this->validURL($image['user_url'])){
						$ret = "<a href='".esc_url($image['user_url'])."' title=''>"
							.$this->calcUserName($image['user_login'], $image['user_nicename'], $image['display_name'])."</a>";
					}
				}
				break;
			*/
			
			case '[url]' :
				$ret = $image['url'];
				break;

			case '[ps_rating]' :
				$ret = $image['ps_rating'];
				break;
			
			default :
				break;
		}
		return $ret;
	}
	
	function getCustomFormURL($g, $image){
			
			
			if((int)trim($image['post_id'])){

				$imglink = get_permalink((int)$image['post_id']);
			} else {
				if((int)$g['post_id']){
					$imglink = get_permalink((int)$g['post_id']);
				} 
			}
			
			if($imglink){
				$url = "<a href='".$imglink."'"
					.$g['url_attr']['imgrel']." title='".$image['imgtitle']."' "
					.$g['url_attr']['imagetargblank'].">";	
			} else {
				$url = $image['imgurl'];
			}
			
		return $url;
	}
	
		
	/**
	 * Get Caption
	 * @return (str) containing html for an image's caption based on settings
	 *
	 * @param (object) $g - gallery definition array; (object) $image - an image object
	 */
	function getCaption($g, &$image){
		// Clean up URLs
		$image['user_url'] = esc_url($image['user_url']);
		$image['url'] = esc_url($image['url']);
		
				
		//Build caption
			if($this->psOptions['caption_targetnew']){
				$captiontargblank = " target='_blank' ";
			}
			
			$nicename = $this->calcUserName($image['user_login']
				, $image['user_nicename'], $image['display_name']);

			$nicename = $nicename ? $nicename : "anonymous";
			
			

							
			switch ($g['show_imgcaption']){
				case 0:	//no caption
					$image['capurl'] = "";
					$image['imgurl_close'] = "";
					
					break;
				case 1: //caption - link to image
					
					if($image['image_caption']){
					
						$scaption = "<span ".$g['url_attr']['captionclass'] .">"
							. $image['image_caption']."</span>";
					
						$image['capurl_close'] = $image['imgurl_close'];
						
					} else {
						$image['capurl'] = "";
					}
					break;
					
				case 2: //contributor's name - link to image
				
					$scaption = "<span ".$g['url_attr']['captionclass'] .">"
						. $nicename. "</span>";
					break;
					
				case 3: //contributor's name - link to website
					if($this->validURL($image['user_url'])){
					
						$theurl = $image['user_url'];
						$captionurl = "
						<a href='".$theurl."'"
							." title='".str_replace("'","",$image['image_caption'])
							."' ".$g['url_attr']['nofollow']." $captiontargblank>";
						$closeUserURL = "</a>
						";
						$image['capurl'] = "";
						$image['capurl_close'] = "";
												
					}else{
						$captionurl = "";
						$closeUserURL = "";					
					}
					
					$scaption = "<span ".$g['url_attr']['captionclass'].">"
						. $captionurl
						. $nicename . $closeUserURL . "</span>";
						
					break;
					
				case 4: //caption [by] contributor's name - link to website

					if($this->validURL($image['user_url'])){
						
						$theurl = $image['user_url'];
						$captionurl = "
						<a href='".$theurl."'"
							." title='".str_replace("'","",$image['image_caption'])
							."' ".$g['url_attr']['nofollow']." $captiontargblank>";
						$closeUserURL = "</a>
						";
						$image['capurl'] = "";
						$image['capurl_close'] = "";				
						
					}else{
					
						$captionurl = "";
						$closeUserURL = "";
						
					}
					
					$scaption = "<span ".$g['url_attr']['captionclass'] .">"
						. $captionurl
						. $image['image_caption']." by "
						. $nicename . $closeUserURL . "</span>";
					
					break;
					
				case 5: //caption [by] contributor's name - link to image
				
					$scaption = "<span ".$g['url_attr']['captionclass'] .">"
						. $image['image_caption']." by "
						. $nicename 
						. "</span>";
											
					break;
					
				case 6: //caption [by] contributor's name - link to user submitted url
				
					$goturl = false;					
					if($this->validURL($image['url'])){
						
						$theurl = $image['user_url'];
						$captionurl = "
						<a href='".$theurl."'"
							." title='".str_replace("'","",$image['image_caption'])
							."' ".$g['url_attr']['nofollow']." $captiontargblank>";
						$closeUserURL = "</a>
						";
											
						$theurl = $image['url'];
						$goturl = true;
						
					} else {
					
						if($this->validURL($image['user_url'])){
						
							$theurl = $image['user_url'];
							$goturl = true;
							
						}
					}
					
					if($goturl){
					
						$captionurl = "<a href='".$theurl."'"
							." title='".str_replace("'","",$image['image_caption'])
							."' ".$g['url_attr']['nofollow']."  $captiontargblank>";
							
						$closeUserURL = "</a>";
						
						$image['capurl'] = "";
						$image['capurl_close'] = "";
						
					}else{
					
						$captionurl = "";
						$closeUserURL = "";					
					}
					
					$scaption = "<span ".$g['url_attr']['captionclass'] .">"
						. $captionurl
						. $image['image_caption']." by "
						. $nicename . $closeUserURL . "</span>";
					
					break;
					
				case 7: //caption - link to user submitted url
					if( $image['image_caption'] ){
					
						$goturl = false;
						if($this->validURL($image['url'])){
							$theurl = $image['url'];
							$goturl = true;
						} else {
							if($this->validURL($image['user_url'])){
								$theurl = $image['user_url'];
								$goturl = true;
							}
						}
						
						if($goturl){
													
							$captionurl = "<a href='".$theurl."'"
								." title='".str_replace("'","",$image['image_caption'])
								."' ".$g['url_attr']['nofollow']."  $captiontargblank>";
								
							$closeUserURL = "</a>";
							
							$image['capurl'] = "";
							$image['capurl_close'] = "";
							
						}else{
							$captionurl = "";
							$closeUserURL = "";
						}
						
						$scaption = "<span ".$g['url_attr']['captionclass'] .">"
							. $captionurl
							. $image['image_caption']
							. $closeUserURL . "</span>";
					} else {
						$image['capurl'] = "";
						$image['capurl_close'] = "";
					}
						
					break;
				
				case 8:	//no caption - Thumbnail links to User Submitted URL
					
					$image['capurl'] = "";
					$image['capurl_close'] = "";
					$scaption = "";	//Close out the link from above
					
					break;
					
				case 9: //caption - Thumbnail & Caption link to User Submitted URL
				
					if( $image['image_caption'] ) 
					{
						$scaption = "<span ".$g['url_attr']['captionclass'] .">"
							. $image['image_caption']
							. "</span>";
					} else {
						$image['capurl'] = "";
						$image['capurl_close'] = "";
					}
															
					break;
				
				case 10: // by Contributor (link to WP author page)				
					
					if($image['user_id']){
					
						$theurl = get_author_posts_url($image['user_id']);
						$captionurl = "
						<a href='".$theurl."'"
							." title='View all images by contributor'"
							.$g['url_attr']['nofollow']." $captiontargblank>";
						$closeUserURL = "</a>
						";
						$image['capurl'] = "";
						$image['capurl_close'] = "";
												
					}else{
						$captionurl = "";
						$closeUserURL = "";					
					}
					
					$scaption = "<span ".$g['url_attr']['captionclass'].">by "
						. $captionurl
						. $nicename . $closeUserURL . "</span>";
												
					break;	
					
				case 11: // Caption by Contributor (link to WP author page)
					
					$goturl = false;					
					if($image['user_id']){
					
						$theurl = get_author_posts_url($image['user_id']);
						$goturl = true;
						
					} else {
					
						if($this->validURL($image['user_url'])){
						
							$theurl = $image['user_url'];
							$goturl = true;
							
						}
					}
					
					if($goturl){
					
						$captionurl = "<a href='".$theurl."'"
							." title='".str_replace("'","",$image['image_caption'])
							."' ".$g['url_attr']['nofollow']."  $captiontargblank>";
							
						$closeUserURL = "</a>";
						
						$image['capurl'] = "";
						$image['capurl_close'] = "";
						
					}else{
					
						$captionurl = "";
						$closeUserURL = "";					
					}
					
					$scaption = "<span ".$g['url_attr']['captionclass'] .">"
						. $captionurl
						. $image['image_caption']." by "
						. $nicename . $closeUserURL . "</span>";
					
					break;
				
				case 12: //No caption - Thumbnail links to Post
				
					$image['capurl'] = "";
					$image['capurl_close'] = "";
					$scaption = "";	//Close out the link from above
										
					if((int)$image['post_id']){
						$post_perma = get_permalink((int)$image['post_id']);
					} else {
						$post_perma = get_permalink((int)$image['gal_post_id']);
					}
					
					if($post_perma){
						$perma = "<a href='".$post_perma."' title='View post'>";
					}
				
									
					if($perma){
						$image['imgurl'] = $perma;
					}
																			
					break;	
			}
			
			return $scaption;
	}
	
	/**
	 * Get Paging Navigation
	 * @return - (str) containing a block of html that navigates through pages in a gallery
	 *
	 * @param (str) $url - current page's url, (int) $page - current page #
	 * @param (int) $totalRows - total rows in images query
	 * @param (int) $rowsPerPage - rows per page - or # of images per page
	 */
	function getPagingNavigation($url, $pagenum, $totalRows, $g, $layout=false){
		$rowsPerPage = $g['img_perpage'];
		
		$page = (int)$pagenum[$g['gallery_id']];
		
		if((int)$rowsPerPage < 1){return false;}
				
		$total_pages = ceil($totalRows / $rowsPerPage);
		
		//use split on ? to get the url broken between ? and rest
		
		$arrURL = split("\?",$url);
		if(count($arrURL)> 1){
			$url .= "&";			
		} else {
			$url .= "?";
		}
		
		$othergals = $this->getPagingForOtherGalleries($pagenum, (int)$g['gallery_id']);
		
		if($othergals){ $othergals = "&amp;".$othergals; }
		
		$page_numstop = $total_pages;
		
		$page_numstart = 1;
		
		//Build PREVIOUS link
		if($page > 3 && $total_pages > 5){
			$nav[] = "<a href='".$url."bwbps_page_".$g['gallery_id']."=1".$othergals."'>first</a>";
			$frontellip = "&#8230;";
			
			
			if($page > ($total_pages - 3)){
				$page_numstart = $total_pages - 4;
			} else {
				$page_numstart = $page - 2;
			}
					
		}
		
		if($total_pages > 5 && $page < $total_pages - 2){
		
			if( $page > 3 ){
				if( $page + 2 < $total_pages){
					$backellip = "&#8230;";
					$page_numstop = $page + 2;
				}					
					
			} else {
				$page_numstop = 5;
				$backellip = "&#8230;";

			}
		}
		
		if($page > 1){
			$nav[] = "<a href='".$url."bwbps_page_".$g['gallery_id']."=".($page-1).$othergals."'>&#9668;</a>";
			
		}
		
		if($frontellip){ $nav[] = $frontellip; }
		
		if($total_pages > 1){
			
			$icnt = 0;
			for($page_num = $page_numstart; $page_num <= $page_numstop; $page_num++){
				if($page == $page_num){ 
					$nav[] = "<span>".$page."</span>";
				}else{
					$nav[] = "<a href='".$url."bwbps_page_".$g['gallery_id']."=".$page_num.$othergals."'>".$page_num."</a>";
				}
			}
			
		}
		
		if($backellip){
			$nav[] = $backellip;
		}
		
		//Build NEXT LINK
		if($page < $total_pages){
			$nav[] = "<a href='".$url."bwbps_page_".$g['gallery_id']."=".($page+1).$othergals."'>&#9658;</a>";
		}
		
		if($total_pages > 5 && $page < ($total_pages - 2)){
			$nav[] = "<a href='".$url."bwbps_page_".$g['gallery_id']."=".($total_pages).$othergals."'>last</a>";
		}
		
		$snav = "";
		if(is_array($nav)){
			$snav = implode("",$nav);
		}
		
		if($layout && $layout->pagination_class){
			$pgnclass = $layout->pagination_class;
		} else {
			$pgnclass = "bwbps_pagination";
		}
		
		$ret = "<div class='$pgnclass'>". $snav."</div>";
		
		return $ret;
		
	}
	
	function getPagingForOtherGalleries($pagenum, $this_gal_id){
	
		if(is_array($pagenum)){
			foreach( $pagenum as $gal_id => $page ){
			
				if( $gal_id <> $this_gal_id ){
					$gal_pages[] = 'bwbps_page_'.(int)$gal_id.'='.(int)$page;
				}
			
			}
		}
		
		if(is_array($gal_pages)){
			$gp = implode("&amp;",$gal_pages);
		}
		return $gp;
	
	}
	
	/**
	 * Get Layout
	 * returns the custom layout from the database
	 *
	 * @param (int) $layout_id
	 */
	function getLayout($layout_id, $layout_name=false){
		global $wpdb;
		
		if($layout_name){ $layoutName = $layout_name;} else { $layoutName = "psid-".$layout_id;}
		if(is_array($this->layouts)){
			if(array_key_exists($layoutName, $this->layouts)){
				return $this->layouts[$layoutName];
			}
		}
		
		if(!$layout_id){
			$sql = $wpdb->prepare('SELECT * FROM '.PSLAYOUTSTABLE
			.' WHERE layout_name = %s ', $layout_name);
		} else {
			$sql = $wpdb->prepare('SELECT * FROM '.PSLAYOUTSTABLE
			.' WHERE layout_id = %d ', $layout_id);
		}
		$query = $wpdb->get_row($sql);
		
		$this->layouts[$layoutName];	//Cache layouts to prevent future DB calls
		
		return $query;
	}
	
	/**
	 * Get Images for Gallery
	 * returns a query object containing the images in a gallery + user info
	 * for users who uploaded images
	 *
	 * @param (object) $g - the gallery definition array
	 * @param (object) $customFields - whether to bring in custom data or not
	 */
	function getGalleryImages($g, $customFields=false){
		global $wpdb;
		global $user_ID;
				
		//Set up SQL for Custom Fields
		$custDataJoin = " LEFT OUTER JOIN ".PSCUSTOMDATATABLE
				." ON ".PSIMAGESTABLE.".image_id = "
				.PSCUSTOMDATATABLE.".image_id ";
			$custdata = ", ".PSCUSTOMDATATABLE.".* ";
			
		
		if($g['show_imgcaption'] == 12){
		
			$custDataJoin = " LEFT OUTER JOIN ". PSGALLERIESTABLE 
				. " ON ". PSIMAGESTABLE . ".gallery_id = "
				. PSGALLERIESTABLE . ".gallery_id " . $custDataJoin;
			
			$gallery_selections = ", ". PSGALLERIESTABLE . ".post_id AS gal_post_id ";
		
		}
		
		
		// Calculate ORDER BY
		$sorder = (int)$g['sort_order'] ? "DESC" : "ASC";
		
		
		switch ($g['gallery_type']){
			
			case 20:
				$sortby = 'RAND() ';
				if(!(int)$g['limit_images']){
					$g['limit_images'] = 8;
				}
				
				$sortby .= "LIMIT ". (int)$g['limit_images'];
			
				break;
			
			case 30:
				$sortby = PSIMAGESTABLE.'.created_date DESC ';
				if(!(int)$g['limit_images']){
					$g['limit_images'] = 8;
				}
				
				$sortby .= "LIMIT ". (int)$g['limit_images'];
			
				break;
			
			default :

				switch ( (int)$g['sort_field'] ){
				
					case 0 :	// When Uploaded
						$sortby = PSIMAGESTABLE.'.created_date ' . $sorder . ', '.PSIMAGESTABLE.'.seq';
						break;
					case 1 :	// Custom Sort
						$sortby = PSIMAGESTABLE.'.seq, '.PSIMAGESTABLE.'.created_date '. $sorder;
						break;
					case 2 :	// Custom Fields
						$sortby = PSIMAGESTABLE.'.created_date ' . $sorder . ', '.PSIMAGESTABLE.'.seq';
						break;
					default :	// When Uploaded
						$sortby = PSIMAGESTABLE.'.created_date ' 
							. $sorder . ', '.PSIMAGESTABLE.'.seq';
						break;
					
				}				
			
		}
		
		
				
		// Add the WHERE clause for the Smart Galleries
		if ( $g['smart_gallery'] ){
			
			if( is_array($g['smart_where'] ) ){
				$swhere[] = $this->getSmartWhereField( $g['smart_where'] );
				
				$sqlWhere = " WHERE " . implode( " AND ", $swhere );			
			} else {
			
				$sqlWhere = " WHERE 1=1 ";
			
			}
		
		} else {
			
			$sqlWhere = " WHERE gallery_id = " . (int)$g['gallery_id'];
		
		}
				
		//Admins can see all images
		if(current_user_can('level_10')){
			$sql = $wpdb->prepare('SELECT '.PSIMAGESTABLE.'.*, '
				.PSIMAGESTABLE.'.image_id as psimageID, '
				.$wpdb->users.'.user_nicename,'
				.$wpdb->users.'.display_name,'
				.$wpdb->users.'.user_login,'
				.$wpdb->users.'.user_url' . $gallery_selections
				.$custdata.' FROM '
				.PSIMAGESTABLE.' LEFT OUTER JOIN '.$wpdb->users.' ON '
				. $wpdb->users .'.ID = '. PSIMAGESTABLE. '.user_id'.$custDataJoin
				. $sqlWhere . ' ORDER BY '.$sortby);			
					
			
		} else {
			//Non-Admins can see their own images and Approved images
			$uid = $user_ID ? $user_ID : -1;
					
			$sql = $wpdb->prepare('SELECT '.PSIMAGESTABLE.'.*, '
				.PSIMAGESTABLE.'.image_id as psimageID, '
				.$wpdb->users.'.user_nicename,'
				.$wpdb->users.'.display_name,'
				.$wpdb->users.'.user_login,'
				.$wpdb->users.'.user_url' . $gallery_selections
				.$custdata.' FROM '
				.PSIMAGESTABLE.' LEFT OUTER JOIN '.$wpdb->users.' ON '
				. $wpdb->users .'.ID = '. PSIMAGESTABLE. '.user_id'.$custDataJoin
				. $sqlWhere . ' AND (status > 0 OR user_id = '
				.$uid.')  ORDER BY '.$sortby
				);			
				
		}
					
		$images = $wpdb->get_results($sql, ARRAY_A);
						
		return $images;
	}
	
	/**
	 * Smart Gallery Where Field
	 * validates a URL
	 *
	 * @param (str) $url
	 */
	function getSmartWhereField( $swhere ){
	
		
		if( is_array($swhere) ){
			//$key = key($swhere);
			//$val = $swhere[$key];
			
			foreach ($swhere as $key => $val){
			
			switch ($key) {
				case "user_id" :
					if( is_array($val) ){
					
						foreach ($val as $uid){
						
							$valid[] = (int)$uid;
							
						}
						$ret = PSIMAGESTABLE . ".user_id IN ( " . implode( "," , $valid) . " ) ";
						
					} else {
					
						$ret = PSIMAGESTABLE . ".user_id = " . (int)$val;
						
					}
					break;
				
				default :
					
					$ret = $key . " = '" . $val . "'" ;
					
					break;
			}
			
			}
		
		}
		
		return $ret;
	}

	/**
	 * Valid URL
	 * validates a URL
	 *
	 * @param (str) $url
	 */
	function validURL($url)
	{
		return ( ! preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) ? FALSE : TRUE;
	}
	
	function getFieldAtts($content, $fieldname){
				
		$pattern = '\[('.$fieldname.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\1\])?';
		
		preg_match_all('/'.$pattern.'/s', $content,  $matches );
		
		$attr = $this->field_parse_atts($matches[2][0]);

		$attr['bwbps_match'] = $matches[0][0];
		return $attr;
				
	}
		
	function field_parse_atts($text) {
		$atts = array();
		$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
		$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
		if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
			foreach ($match as $m) {
				if (!empty($m[1]))
					$atts[strtolower($m[1])] = stripcslashes($m[2]);
				elseif (!empty($m[3]))
					$atts[strtolower($m[3])] = stripcslashes($m[4]);
				elseif (!empty($m[5]))
					$atts[strtolower($m[5])] = stripcslashes($m[6]);
				elseif (isset($m[7]) and strlen($m[7]))
					$atts[] = stripcslashes($m[7]);
				elseif (isset($m[8]))
					$atts[] = stripcslashes($m[8]);
			}
		} else {
			$atts = ltrim($text);
		}	
		return $atts;
	}
	
	function calcUserName($loginname, $nicename = false, $displayname = false){
		if($displayname) return $displayname;
		if($nicename) return $nicename;
		return $loginname;
	}
	
	//Validate URL
	function psValidateURL($url)
	{
		return ( ! preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) ? FALSE : TRUE;
	}
}
?>