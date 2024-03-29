<?php

class PhotoSmash_Widget extends WP_Widget {

	function PhotoSmash_Widget(){
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'PhotoSmash', 'description' => 'Displays a PhotoSmash Gallery (standard gallery or random images, etc)' );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'photosmash-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'photosmash-widget', 'PhotoSmash Widget', $widget_ops, $control_ops );

	
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$gallery_name = $instance['gallery_name'];
		$layout = $instance['layout'];
		
		if( $instance['gallery_type'] ){
			
			$gallery_type = 'gallery_type='.$instance['gallery_type'];
			
		}
		$gallery_id = (int)$instance['gallery_id'];
		$images = (int)$instance['images'];
		$where_gallery = (int)$instance['where_gallery'];
		$thumb_height = (int)$instance['thumb_height'];
		$thumb_width = (int)$instance['thumb_width'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display Gallery. */
		if ( $gallery_type || $gallery_id ){
			if($gallery_id){ $gid = 'id=' . $gallery_id; }
			
			if(!$layout){$layout = 'Std_Widget'; }
			
			$sc = "[photosmash $gid $gallery_type images=$images where_gallery=$where_gallery layout='$layout' thumb_height=$thumb_height thumb_width=$thumb_width ]";
			
			echo do_shortcode($sc);
		
		}

		
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['gallery_type'] = strip_tags( $new_instance['gallery_type'] );
		$instance['layout'] = strip_tags( $new_instance['layout'] );
		$instance['gallery_id'] = (int)( $new_instance['gallery_id'] );
		$instance['images'] = (int)( $new_instance['images'] );
		$instance['where_gallery'] = (int)( $new_instance['where_gallery'] );
		$instance['thumb_height'] = (int)( $new_instance['thumb_height'] );
		$instance['thumb_width'] = (int)( $new_instance['thumb_width'] );
		
		return $instance;
	}
	
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
			'title' => 'Random Images',
			'gallery_type' => 'random',
			'layout' => 'Std_Widget',
			'id' => 0,
			'images' => 8,
			'where_gallery' => 0,
			'thumb_height' => 60,
			'thumb_width' => 60
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br/>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"   />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'gallery_type' ); ?>">Gallery type:</label><br/>
		
			<select id="<?php echo $this->get_field_id( 'gallery_type' ); ?>" name="<?php echo $this->get_field_name( 'gallery_type' ); ?>"  >
				<option <?php if ( 'random' == $instance['gallery_type'] ) echo 'selected="selected"'; ?> value='random'>Random</option>
				<option <?php if ( 'recent' == $instance['gallery_type'] ) echo 'selected="selected"'; ?> value='recent'>Recent</option>
				<option <?php if ( "0" === $instance['gallery_type'] ) echo 'selected="selected"'; ?> value=0>normal</option>
				
			</select>
		</p>
		
		<p>
					
		
			<label for="<?php echo $this->get_field_id( 'layout' ); ?>">Layout:</label><br/>
			
			<select id="<?php echo $this->get_field_id( 'layout' ); ?>" name="<?php echo $this->get_field_name( 'layout' ); ?>"  >
			
				<?php echo $this->getLayoutsDDL($instance['layout']); ?>	
				
			</select>
			
		</p>
		
		<p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'gallery_id' ); ?>">Main Gallery ID :</label><br/>
			<input id="<?php echo $this->get_field_id( 'gallery_id' ); ?>" name="<?php echo $this->get_field_name( 'gallery_id' ); ?>" value="<?php echo $instance['gallery_id']; ?>" />
			<br/>
			<span style='font-size: 9px;'>Required for normal galleries</span>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'where_gallery' ); ?>">Image Gallery ID (for random / recent) :</label><br/>
			<input id="<?php echo $this->get_field_id( 'where_gallery' ); ?>" name="<?php echo $this->get_field_name( 'where_gallery' ); ?>" value="<?php echo (int)$instance['where_gallery']; ?>"   /> 
			<br/>
			<span style='font-size: 9px;'>Optionally limit selection to specific gallery.</span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'images' ); ?>"># images to show:</label><br/>
			<input id="<?php echo $this->get_field_id( 'images' ); ?>" name="<?php echo $this->get_field_name( 'images' ); ?>" value="<?php echo (int)$instance['images']; ?>"   />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'thumb_height' ); ?>">Width x Height:</label><br/>
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id( 'thumb_width' ); ?>" name="<?php echo $this->get_field_name( 'thumb_width' ); ?>" value="<?php echo (int)$instance['thumb_width']; ?>"   /> x 			
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id( 'thumb_height' ); ?>" name="<?php echo $this->get_field_name( 'thumb_height' ); ?>" value="<?php echo (int)$instance['thumb_height']; ?>"   /> (px)<br/>(optional)
		</p>
		
		<?php
	}
	
	
	//Get Layouts DDL
	function getLayoutsDDL($selected_layout,$psDefault){
		
 		global $wpdb;
 		 		
 		if($selected_layout == 'std'){$sel = "selected='selected'";}else{$sel = "";}
		$ret .= "<option value='std' ".$sel.">Standard display</option>";
		
		if(!$psDefault){
			if($selected_layout == 0){$sel = "selected='selected'";}else{$sel = "";}
			$ret .= "<option value='0' ".$sel.">&lt;Default layout&gt;</option>";
		}
		
		$query = $wpdb->get_results("SELECT layout_id, layout_name FROM "
			.PSLAYOUTSTABLE." ORDER BY layout_name;");
		
		if($query){
			foreach($query as $row){
		
				if($selected_layout == $row->layout_name){$sel = "selected='selected'";}else{$sel = "";}
				$ret .= "<option value='".$row->layout_name."' ".$sel.">".$row->layout_name."</option>";
						
			}
		}
		return $ret;
	}


}

?>