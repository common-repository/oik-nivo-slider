<?php // (C) Copyright Bobbing Wide 2012-2020, 2023

/**
 * Format the Nivo output for posts which have attached images 
 * 
 * When the thumbnail size is "full" then this will only work when there is an attached image.
 * It won't find the featured image. This is a limitation of bw_thumbnail().
 *
 * Note: This routine only supports link=y or link=n.  
 *
 * It doesn't support link=file nor link=full 
 * as it's intended for creating links to the particular post not to the attached image.
 *
 * @param post $post - a post object
 * @param array $atts - array of shortcode parameters 
 */
function bw_format_nivo( $post, $atts ) {
  $alt = null;
  if ( bw_validate_torf( $atts['caption'] ) ) {
    $title = get_the_title( $post->ID );
    if ( $format = bw_array_get( $atts, "format", false ) ) {
      $alt =  $title;
      $title = "#" . $atts['slider-id'] . "-" . $post->ID;
    } else {
    }
  } else {
    $title = null;
  }   
  $atts['title'] = $title;  
  $atts['thumbnail'] = bw_array_get( $atts, "thumbnail", "full" );
  $thumbnail = bw_thumbnail( $post->ID, $atts );
  $permalink = get_permalink( $post->ID );
  if ( bw_validate_torf( $atts['link'] ) ) {
    BW_::alink( null, $permalink , $thumbnail, $alt );  
  } else {
    e( $thumbnail );
  }    
}

/**
 * Determine if now is closer to wanted than the best so far
 *
 * 9 = perfect - this is the exact value
 * 3 = better - the new value is better than best so far
 * 2 = it's a start - well at least it's not 0
 * 1 = same - the new value is the same as the best so far, but it's not the wanted value
 * 0 = worse - the new value is not as good as the best so far
 * 
 * x is the EXACT value that we want - the $wanted variable
 * 
 * In the table below: z > y > x > w > 0
 *
 * $best | $now   |  result
 * ----- | -----  |  ------------
 * 0     | w/y/z  |  2 = it's a start
 * 0     | x      |  9 = perfect
 * w     | w      |  1 = same
 * w     | x      |  9 = perfect
 * w     | y/z    |  3 = better
 * x     | w      |  0 = worse         
 * x     | x      |  9 = perfect       
 * x     | y/z    |  0 = worse
 * y     | w      |  0 = worse
 * y     | x      |  9 = perfect
 * y     | y      |  1 = same
 * y     | z      |  0 = worse
 * z     | w      |  0 = worse
 * z     | x      |  9 = perfect
 * z     | y      |  3 = better
 * z     | z      |  1 = same
 *
 * 
 * @param integer $wanted - the exact value we want to match
 * @param integer $best - the best value so far
 * @param integer $now - the current value
 * @return integer $result - one of the above
 */
function bw_closer( $wanted, $best, $now ) {
  if ( $wanted == $best  ) {
    if ( $now == $best ) {
      $result = 9;
    } else {
      $result = 0;
    }  
  } elseif ( $now == $wanted ) {
      $result = 9;
  } elseif ( $best == 0 ) {
      $result = 2;  
  } elseif ( ( $best < $wanted ) && ( $now > $wanted ) ) {
      $result = 3;    
  } elseif ( ( $now < $best ) && ( $now > $wanted) ) {
      $result = 3;
  } elseif ( $now == $best ) {
      $result = 1;
  } else {
    $result = 0;
  }
  return( $result );
}        

/**
 * Determine if this size is a better match than before? 
 * 
 * Note: Best width and height start at 0
 * 
 * @param integer $sw - required width
 * @param integer $sh - required height
 * @param integer $best_width - best width so far
 * @param integer $best_height - best height so far
 * @param integer $width - current width
 * @param integer $height - current height
 * @param integer $best_score - "score" for best so far
 * @return integer $result - new score if better or 0 if not
 *
 */
function bw_better_fit( $sw, $sh, $best_width, $best_height, $width, $height, $best_score ) {
  $closer_w = bw_closer( $sw, $best_width, $width );
  $closer_h = bw_closer( $sh, $best_height, $height );
  $score = ( $closer_w * 10 )  + $closer_h;
   
  if ( $score >= $best_score )
    $result = $score;
  else
    $result = 0;
  bw_trace2( $score, "score", true, BW_TRACE_VERBOSE );
  return( $result );    
}

/**
 * Find the best fitting file for the chosen size 
 *
 * Choose the best fitting image from the available selection
 * `
            [sizes] => Array
                (
                    [thumbnail] => Array
                        (
                            [file] => nggallery-example1-150x150.jpg
                            [width] => 150
                            [height] => 150
                        )

                    [medium] => Array
                        (
                            [file] => nggallery-example1-256x130.jpg
                            [width] => 256
                            [height] => 130
                        )
                    [full] => Array built from the information for the full size file    

                )
 * `
 * Note: Since we're not going to scale the file then we just try to find something that's a reasonable size.
 * We ignore the aspect ratio.
 *
 *
 * @param array - array of files and their sizes
 * @param array - size array - size[0]= width, size[1] = height 
 * @return string chose file name or 
 */
function bw_get_by_size( $sizes, $size ) {
  $sw = $size[0];
  $sh = $size[1];

  $best_width = 0;
  $best_height = 0;
  $best_file = null;
  $best_score = 0;
  foreach ( $sizes as $fileinfo ) {
     
    $file = bw_array_get( $fileinfo, "file", null );
    $width = bw_array_get( $fileinfo, "width", 0 );
    $height = bw_array_get( $fileinfo, "height", 0 );
    if (( $sw == $width ) && ( $sh == $height )) {
      $best_file = $file;
      bw_trace2( $best_file, "best file", false );
      break;
    } 
    if ( $fit = bw_better_fit( $sw, $sh, $best_width, $best_height, $width, $height, $best_score ) ) {
      $best_file = $file;
      $best_width = $width;
      $best_height = $height; 
      $best_score = $fit;
    }
    
  }
  bw_trace2( $best_file, "best file now", false, BW_TRACE_DEBUG );  
  if ( !$best_file ) {
    $best_file = $file;
  }  
  bw_trace2( $best_file, "I chose", true, BW_TRACE_DEBUG );
  return( $best_file );
}

/**
 * Get best fitting image file name WITHOUT resizing
 * 
 * @param ID - post id of the image
 * @param mixed - size - either as a named size or array
 * @return string - path for the chosed attached image (within wp-uploads)
 */
function bw_get_best_fit( $post_id, $size ) {
  $image = null;
  $metadata = get_post_meta( $post_id, "_wp_attachment_metadata", false );
  bw_trace2( $metadata, "metadata" );
  if ( $metadata ) {
    $first = bw_array_get( $metadata, 0, null );
    if ( $first ) {
      $file = bw_array_get( $first, "file", null );
      if ( $file ) { 
        $sizes = bw_array_get( $first, "sizes", null );
        //bw_trace2( $sizes, "sizes", false );
        if ( $sizes ) {
          if ( !is_array( $size ) ) {
            $images = bw_array_get( $sizes, $size, null );
            //bw_trace2( $images, "images", false );
            if ( $images ) { 
              $image = bw_array_get( $images, "file", null );
            } else {
              $image = pathinfo( $file, PATHINFO_BASENAME );
            }  
          } else {
            $sizes[ 'full' ] = array( "file" => pathinfo( $file, PATHINFO_BASENAME )
                                  , "width" => bw_array_get( $first, "width", 0 )
                                  , "height" => bw_array_get( $first, "height", 0 )
                                  );
            $image = bw_get_by_size( $sizes, $size );
            //bw_trace2( $image, "image by_size", false );
          }
        } else {
          $image = pathinfo( $file, PATHINFO_BASENAME );
        }  
               
        if ( $image ) {
          $image = pathinfo( $file, PATHINFO_DIRNAME ) . '/' . $image;
          //bw_trace2( $image, "image with PATHINFO", false );
        }
      }
    }  
  }  
  return( $image );      
}

/** 
 * Return the 'correct' URL for the attached image
 *
 * We cannot rely on $post->guid since the uploaded file may have been edited in the media dialog and given a new name. 
 * We need to obtain the file name from the metadata and then fully qualify it for the browser

 * `
    [0] => Array
        (
            [width] => 350
            [height] => 178
            [hwstring_small] => height='65' width='128'
            [file] => 2012/03/nggallery-example1.jpg
            [sizes] => Array
                (
                    [thumbnail] => Array
                        (
                            [file] => nggallery-example1-150x150.jpg
                            [width] => 150
                            [height] => 150
                        )

                    [medium] => Array
                        (
                            [file] => nggallery-example1-256x130.jpg
                            [width] => 256
                            [height] => 130
                        )

                )

            [image_meta] => Array
                (
                    [aperture] => 0
                    [credit] => 
                    [camera] => 
                    [caption] => 
                    [created_timestamp] => 0
                    [copyright] => 
                    [focal_length] => 0
                    [iso] => 0
                    [shutter_speed] => 0
                    [title] => 
                )

        )
 * `
 *        
 * When the file is "thumbnail", "medium" or "large" - or any other combination then we need to add the directory information
 * from the "file" to the selected file.
 * When the thumbnail parameter is a numeric [size] array we need to search the [sizes] array
 * to try to find the best match for the specified size.
 * 
 * @param integer $post_id - the ID of the attachment post
 * @param array $atts - attributes containing the specified thumbnail size.
 * @return string $image - the URL for the attached image
 */ 
function bw_get_attached_file_name( $post_id, $atts ) {
  $size = bw_array_get( $atts, "thumbnail", "full" );
  bw_trace2( $size, "size" );
  $image = null;
  switch ( $size ) { 
    case 'full':
      $images = get_post_custom_values( "_wp_attached_file", $post_id );  
      //bw_trace2( $images ); 
      if ( $images ) {
         $image = $images[0];
      } else { 
        $image = null;
      }
    break; 
    
    default:
      $size = bw_get_thumbnail_size( $atts );
      $image = bw_get_best_fit( $post_id, $size );
      bw_trace2( $image, "got_image", false ); 
    break;
  }
  if ( $image ) {
    $upload_dir = wp_upload_dir();
    $image = $upload_dir['baseurl'] . '/' . $image;
  }
  // bw_trace2( $image, "attached_image", false ); 
  return( $image );
}

/**
 * Format the HTML captions for the nivo slider
 *
 * Uses oik's format= parameter - from [bw_pages] to display whatever you like in the content.
 * Note: For attachments the Caption is stored as the post_excerpt and the Description in the post_content
 *
 * @param array $posts - array of posts
 * @param array $atts - array of shortcode parameters
 */
function bw_format_nivo_html_captions( $posts, $atts ) {
  $format = bw_array_get( $atts, "format", null );
  if ( $format ) {
    oik_require( "shortcodes/oik-pages.php" );
    $bw_post_formatter = bw_query_post_formatter( $atts );
    foreach ( $posts as $post ) {
      // bw_trace2( $post );
      sdiv( "nivo-html-caption", $atts['slider-id'] . "-" . $post->ID );
      $bw_post_formatter( $post, $atts );
      ediv();
    }
  }
}

/**
 * Format the Nivo output for attached images
 *
 * Extract from Nivo's documentation
 * - To add a caption to an image you simply need to add a title attribute to the image. 
 * - To add an HTML Caption simply set the title attribute to the ID of a element that contains your caption (prefixed with a hash). 
 * - Note that the HTML element that contains your caption must have the CSS class nivo-html-caption applied and must be outside of the slider div.
 *
 * The HTML caption logic was implemented in version 1.12, using oik's format= parameter.
 * Since there can be multiple sliders on a page the unique ID in the title attribute needs to take into account the slider instance (slider-id).
 * The hyphen between the slider-id and post id helps ensure uniqueness.
 *
 * Added support for link=full, in addition to link=file. 
 * link=full will open the link to the original image
 * link=file opens the links to the image of the selected thumbnail size
 * @TODO Add support for other file sizes if deemed really necessary.
 *
 * @param post $post - the post to be displayed
 * @param array $atts - array of parameters; name value pairs
 *
 */ 
function bw_format_nivo_attachment( $post, $atts ) {
  $alt = null;
  if ( bw_validate_torf( $atts['caption'] ) ) {
    $title = get_the_title( $post->ID );
    if ( $format = bw_array_get( $atts, "format", false ) ) {
      $alt =  $title;
      $title = "#" . $atts['slider-id'] . "-" . $post->ID;
    } else {
    }
  } else {
    $title = null;
  }   
  $image = bw_get_attached_file_name( $post->ID, $atts );
  bw_trace2( $image, "image", true, BW_TRACE_DEBUG );
  if ( $image ) {
    $thumbnail = retimage( null, $image , $title, null, null, kv("data-thumb", $image) );
		if ( $atts['count'] ) {
			$thumbnail = str_replace( "/>", ' style="display:none" />', $thumbnail );
		}
    if ( bw_validate_torf( $atts['link'] ) ) {
      $permalink = bw_get_image_link( $post->ID );
      BW_::alink( "iframe", $permalink , $thumbnail, null );  
    } elseif ( $atts['link'] == "file" ) {
      BW_::alink( null, $image, $thumbnail, null, null, kv( "rel", $atts["slider-id"] )); 
		} elseif ( $atts['link'] == "full" ) {
			$full_image = bw_get_attached_file_name( $post->ID, array( "thumbnail" => "full" ) );
      BW_::alink( null, $full_image, $thumbnail, null, null, kv( "rel", $atts["slider-id"] )); 
    } else { 
      e( $thumbnail );
    }    
  } else {
    bw_trace2();
  } 
}

/**
 * Get special post types
 * 
 * @param array $atts - array of shortcode parameters
 * @return array $posts - array of "posts" 
 *
 * Initially the only special post_type supported was "screenshot"
 * This allowed oik-nivo-slider to display its own screen shot files: screenshot-n.png
 * Now we're looking at supporting NextGEN: ngg_pictures, ngg_gallery or ngg_album
 *
 */
function bw_get_special_post_type( $atts ) {
  $explode = explode( ':', $atts['post_type'] );
  $type = bw_array_get( $explode, 0, "screenshot" );
  $funcname = "bw_get_spt_$type"; 
  //$funcname = bw_funcname( "bw_get_spt_", $type );
  if ( function_exists( $funcname ) ) {
    $posts = $funcname( $atts ); 
  } else { 
    e( sprintf( __( 'Unsupported special post_type: %1$s', "oik-nivo-slider" ), $type ) );
    $posts = null;
  }
  return( $posts );
}

/** 
 * Load screenshot images for the selected plugin (or other plugin files)
 * 
 * e.g [nivo post_type='screenshot:oik-nivo-slider'] or [nivo post_type='screenshot:oik'] 
 * This solution assumes that the screenshots are part of the plugin 
 * It does not load the files from the wordpress.org repository - where they may be stored in the assets folder
 * 
 * @param array $atts - array of shortcode parameters
 * @return array 
 */    
function bw_get_spt_screenshot( $atts ) { 
  $explode = explode( ':', $atts['post_type'] );
  $type = bw_array_get( $explode, 0, "screenshot" );
  $plugin = bw_array_get( $explode, 1, "oik-nivo-slider" );
  $path = oik_path( $type, $plugin );
  $files = glob( $path . "*" );
  $urls = bw_file_to_url( $files, $atts );
  return( $urls );
}

/**
 * Get posts for the NextGen gallery
 *
 * Developed using NextGEN version 1.9.2
 * 
 * @param array $atts shortcode attributes
 * @return array images returned from NextGen 
 */
function bw_get_spt_nggallery( $atts ) {
  $explode = explode( ':', $atts['post_type'] );
  $type = bw_array_get( $explode, 0, "nggallery" );
  $gallery = bw_array_get( $explode, 1, 1 );
  if ( class_exists( "nggdb" ) ) {
    $images = nggdb::get_gallery( $gallery );
    bw_trace2( $images );
  } else { 
    $images = null;
    e( __( "NextGEN does not appear to be activated", "oik-nivo-slider" ) ); 
  }
  return( $images ); 
}

/**
 * Format the Nivo output for a screenshot-n.png file
 * 
 * @param string $fileurl - the file name of the screenshot
 * @param array $atts - array of shortcode parameters
*/ 
function bw_format_nivo_screenshot( $fileurl, $atts ) {
  if ( bw_validate_torf( $atts['caption'] ) ) {
    $caption = $fileurl;
  } else {
    $caption = null;
  }    
  $image = retimage( null, $fileurl, $caption, null, null, kv("data-thumb", $fileurl) );
	
	if ( $atts['count'] ) {
		$image = str_replace( "/>", ' style="display:none" />', $image );
	}
  e( $image );
}

/**
 * Format a NextGEN gallery image for the nivo slider
 * 
 * The nggImage object already contains the fields we need in order to display an image: imageURL and description
 * Don't yet know the requirements for using other data. So currently just display without any links
 *
 * `
    [14] => nggImage Object
        (
            [errmsg] => 
            [error] => 
            [imageURL] => http://qw/wordpress/wp-content/gallery/nextgen-gallery/RCGC-7th-green-cherry-blossom-972x300.jpg
            [thumbURL] => http://qw/wordpress/wp-content/gallery/nextgen-gallery/thumbs/thumbs_RCGC-7th-green-cherry-blossom-972x300.jpg
            [imagePath] => C:/apache/htdocs/wordpress/wp-content/gallery/nextgen-gallery/RCGC-7th-green-cherry-blossom-972x300.jpg
            [thumbPath] => C:/apache/htdocs/wordpress/wp-content/gallery/nextgen-gallery/thumbs/thumbs_RCGC-7th-green-cherry-blossom-972x300.jpg
            [href] => 
<a href="http://qw/wordpress/wp-content/gallery/nextgen-gallery/RCGC-7th-green-cherry-blossom-972x300.jpg" title="Here is a description for the cherry blossom" class="thickbox" rel="nextgen-gallery">
	<img alt="RCGC-7th-green-cherry-blossom-972x300" src="http://qw/wordpress/wp-content/gallery/nextgen-gallery/thumbs/thumbs_RCGC-7th-green-cherry-blossom-972x300.jpg"/>
</a>

            [thumbPrefix] => thumbs_
            [thumbFolder] => /thumbs/
            [galleryid] => 1
            [pid] => 14
            [filename] => RCGC-7th-green-cherry-blossom-972x300.jpg
            [description] => Here is a description for the cherry blossom
            [alttext] => RCGC-7th-green-cherry-blossom-972x300
            [imagedate] => 2011-04-20 01:01:42
            [exclude] => 0
            [thumbcode] => class="thickbox" rel="nextgen-gallery"
            [name] => nextgen-gallery
            [path] => wp-content/gallery/nextgen-gallery
            [title] => NextGen gallery
            [pageid] => 3091
            [previewpic] => 18
            [permalink] => 
            [image_slug] => rcgc-7th-green-cherry-blossom-972x300-2
            [post_id] => 0
            [sortorder] => 0
            [meta_data] => Array
                (
                    [0] => 
                    [aperture] => F 9
                    [credit] => 
                    [camera] => DSC-W80
                    [caption] => 
                    [created_timestamp] => April 20, 2011 1:01 am
                    [copyright] => 
                    [focal_length] => 6.6 mm
                    [iso] => 125
                    [shutter_speed] => 1/130 sec
                    [flash] => Not fired
                    [title] =>                                
                    [keywords] => 
                    [width] => 972
                    [height] => 300
                    [saved] => 1
                    [thumbnail] => Array
                        (
                            [width] => 100
                            [height] => 75
                        )

                )

            [gid] => 1
            [slug] => nextgen-gallery
            [galdesc] => 
            [author] => 1
            [imageHTML] => 
<a href="http://qw/wordpress/wp-content/gallery/nextgen-gallery/RCGC-7th-green-cherry-blossom-972x300.jpg" title="Here is a description for the cherry blossom" class="thickbox" rel="nextgen-gallery">
	<img alt="RCGC-7th-green-cherry-blossom-972x300" src="http://qw/wordpress/wp-content/gallery/nextgen-gallery/RCGC-7th-green-cherry-blossom-972x300.jpg"/>
</a>

            [thumbHTML] => 
<a href="http://qw/wordpress/wp-content/gallery/nextgen-gallery/RCGC-7th-green-cherry-blossom-972x300.jpg" title="Here is a description for the cherry blossom" class="thickbox" rel="nextgen-gallery">
	<img alt="RCGC-7th-green-cherry-blossom-972x300" src="http://qw/wordpress/wp-content/gallery/nextgen-gallery/thumbs/thumbs_RCGC-7th-green-cherry-blossom-972x300.jpg"/>
</a>

        )
 * `				
 *
 * @param object $nggImage - a NextGEN gallery nggImage object
 * @param array $atts - array of shortcode parameters
 *
 */
function bw_format_nivo_nggallery( $nggImage, $atts ) {
  if ( bw_validate_torf( $atts['caption'] ) ) {
    $caption = $nggImage->description;
  } else {
    $caption = null;
  } 
  $fileurl = $nggImage->imageURL;
  $thumburl = $nggImage->thumbURL;   
  $image = retimage( null, $fileurl, $caption, null, null, kv("data-thumb", $thumburl ) );
  e( $image );
}

/**
 * Enqueue the debug script if needed otherwise enqueue the minified (packed) one
 * 
 * @param string $version - must be 32 or 271
*/
function bw_nivo_enqueue_script( $version ) {
  if ( defined('SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true) {
    $script = "jquery.nivo.slider-$version.js";
  } else {
    $script = "jquery.nivo.slider-$version.min.js";
  } 
  wp_enqueue_script( "nivo-slider-$version-js", oik_url( $script, "oik-nivo-slider" ), array( 'jquery') );
}

/**
 * Set the nivoSlider to start for objects with an id of slider-n
 * 
 * Note: The .css file allows for up to 10 sliders per page.
*/
function bw_slider_id() {
  global $bw_slider_count; 
  if ( empty( $bw_slider_count ) ) {
    $bw_slider_count = 0;
  }  
  $bw_slider_count++;
  return( $bw_slider_count );
}
 
/** 
 * Returns the required nivo slider jQuery version for the selected theme.
 * 
 * @param string $theme - theme name
 * 2.7.1 versions are: orman, pascal, default271 and oik271
 * 3.2 versions are: default, bar, dark, light, oik AND anything else
 */
function bw_nivo_version( $theme ) { 
  static $versions = array( "default271" => "271" 
                          , "oik271" => "271"
                          , "orman" => "271"
                          , "pascal" => "271" 
                          );
  $version = bw_array_get( $versions, $theme, "32" );
  return $version;
}

/**
 * Override the profile options with parameter settings with the same name
 * 
 * Use this routine to override options such as:
 * effect: fade, fold, slideInLeft, etc
 * 
 * @param array $options - the options array
 * @param string $options_field - the name of the options field to override
 * @param array $atts - the overriding parameter array
 * @param string $atts_field - the name of the field containing the override
 * @param callback $field_validation - a validation function for the overriding parameter
 * @return array - the overridden options array
 * 
 */
function bw_override_options( $options, $options_field, $atts, $atts_field, $field_validation=null ) {
  $value = bw_array_get( $atts, $atts_field, null ); 
  if ( $value ) {
    if ( is_callable( $field_validation ) ) {
      $value = $field_validation( $value );
    }  
  } 
  if ( $value !== null ) {
    $options[$options_field] = $value ;
  }    
  return( $options ); 
}
  
/**
 * Implement the [nivo] shortcode to display a Nivo slider for attachments or other post types
 *
 * The nivo slider consists of: 
 * jQuery, the Nivo slider jQuery and something to attach nivoSlider to the div containing the images
 * Nivo is a bit pernickety about the html you use in order to create links with captions;
 * finally dealt with in version 1.12 with the addition of bw_format_nivo_html_captions().
 * 
 * @param array $atts - array of shortcode parameters
 * @return string - the results of expanding the shortcode
 *
 */
function bw_nivo_slider( $atts=null ) {
  $atts[ 'post_type'] = bw_array_get( $atts, "post_type", "attachment" );
  if ( $atts['post_type'] == "attachment" ) {
    $def_post_mime_type = "image"; 
  } else {
    $def_post_mime_type = null;  
  }  
  $atts[ 'post_mime_type'] = bw_array_get( $atts, "post_mime_type", $def_post_mime_type );
  $theme = bw_array_get( $atts, "theme", "default" );
  $class = bw_array_get( $atts, "class", null );
  $format = bw_array_get( $atts, "format", null );
  if ( $format ) {
    $atts['link'] = bw_array_get( $atts, "link", "n" );
  } else {
    $atts['link'] = bw_array_get( $atts, "link", "y" );
  }  
  $atts['caption'] = bw_array_get($atts, "caption", "y" );
  $ribbon = bw_validate_torf( bw_array_get(  $atts, "ribbon", "y" ));
  $atts['thumbnail'] = bw_array_get( $atts, "thumbnail", "full" );
  oik_require( "shortcodes/oik-attachments.php" );
  $pos = strpos( $atts['post_type'], ':' ); 
  if ( $pos != FALSE ) {  
    $posts = bw_get_special_post_type( $atts );
    $atts['post_type'] = substr( $atts['post_type'], 0, $pos ); 
  } else {
    $posts = bw_get_posts( $atts );
  }  
  if ( $posts ) {
    wp_enqueue_style( "nivo{$theme}", oik_url( "themes/{$theme}/{$theme}.css", "oik-nivo-slider" ) ); 
    $version = bw_nivo_version( $theme );
    wp_enqueue_style( "nivoCSS-$version", oik_url( "nivo-slider-$version.css", "oik-nivo-slider" ) ); 
    bw_nivo_enqueue_script( $version );
    $slider_id = bw_slider_id();
    //e( '<script type="text/javascript">jQuery(window).load(function() {  jQuery(\'#slider-'. $slider_id. '\').nivoSlider(); });</script>' );
    if ( $version == "271" ) {
      bw_jquery( "#slider-$slider_id", "nivoSlider271", bw_jkv( get_option( 'bw_nivo_slider' ) ), true );
    } else { 
      //bw_jquery( "#slider-$slider_id", "nivoSlider", bw_jkv( get_option( 'bw_nivo_slider' ) ), true );
      $options = get_option( 'bw_nivo_slider' );
      // 2013/01/29 - Implemented ability to override "controlNav" with a "nav" parameter.
      // and "pauseTime" with a "pause" parameter
      $options = bw_override_options( $options, "controlNav", $atts, "nav", "bw_validate_torf" ); 
      $options = bw_override_options( $options, "pauseTime", $atts, "pause" ); 
      $options = bw_override_options( $options, "manualAdvance", $atts, "manual", "bw_validate_torf" ); 
      $options = bw_override_options( $options, "controlNavThumbs", $atts, "thumbs", "bw_validate_torf" ); 
      $options = bw_override_options( $options, "effect", $atts, "effect", "bw_filter_transition_effect" ); 
      //bw_trace2( $options, "options" );   
      bw_jquery( "#slider-$slider_id", "nivoSlider", bw_jkv( $options ), true );
    }  
    if ( bw_validate_torf( $atts['link'] ) ) {
      $oik_nc_script = 'jquery.oik-nc-click.js';  
      wp_enqueue_script( 'oik-nc-click.js', oik_url( $oik_nc_script, "oik-nivo-slider" ), array( "nivo-slider-$version-js" ) ); 
      //e( '<script type="text/javascript">jQuery(window).load(function() {  jQuery(\'#slider-'. $slider_id. '\').oikNCClick(); });</script>' );
      bw_jquery( "#slider-$slider_id", "oikNCClick", null, true );
    }  
    sdiv( $class );
    sdiv( "slider-wrapper theme-{$theme}" );
    if ( $ribbon )
      sediv( "ribbon" );
    //sediv( "nivoSlider-dummy" );
    sdiv( "nivoSlider", "slider-$slider_id" );
    $atts['slider-id'] = "slider-$slider_id";
    $funcname = bw_funcname( "bw_format_nivo", $atts['post_type'] );
		$count = 0;
    foreach ( $posts as $post ) {
			$atts['count'] = $count++;
      $funcname( $post, $atts );
    }
    ediv();
    
    // Now, add the formatted content if requested
    if ( $format ) {
      bw_format_nivo_html_captions( $posts, $atts );
    }
    ediv();  
    ediv( $class ); 
    bw_clear_processed_posts();
  }  
  return bw_ret();
}

/**
 * oik shortcode help for [nivo] 
 */ 
function nivo__help( $shortcode="nivo" ) {
  return( __( "Display the nivo slideshow for attachments or other post types.", "oik-nivo-slider" ) );
}

if ( !function_exists( '_sc_thumbnail_full' ) ) {  
function _sc_thumbnail_full() {   
  return( array( 'thumbnail'       => BW_::bw_skv( "full", "thumbnail|medium|large|nnn|wxh", __( "image size", "oik-nivo-slider" ) ) 
               ));
}
}

/**
 * oik shortcode syntax for [nivo]
 */ 
function nivo__syntax( $shortcode='nivo' ) {
  $nivo_syntax = array( "post_type" => BW_::bw_skv( "attachment", "<i>" . __( "post_type", "oik-nivo-slider" ) . "</i>", __( "Post type to display", "oik-nivo-slider" ) )
                 , "theme" => BW_::bw_skv( "default", "bar|custom|dark|light|orman|pascal|oik271|default271", __( "Theme for the slideshow", "oik-nivo-slider" ) )
                 , "class" => BW_::bw_skv( "", "<i>" . __( "classes", "oik-nivo-slider" ) . "</i>", __( "CSS classes", "oik-nivo-slider" ) )
                 , "link" => BW_::bw_skv( "y", "n|file|full", __( "Link the images to the target post/page or media file", "oik-nivo-slider" ) )
                 , "caption" => BW_::bw_skv( "y", "n", __( "Display the image title as the caption", "oik-nivo-slider" ) )
                 , "format" => BW_::bw_skv( null, "<i>" . __( "format", "oik-nivo-slider" ) . "</i>", __( "field format string", "oik-nivo-slider" ) )
                 , "ribbon" => BW_::bw_skv( "y", "n", __( "Display the ribbon, if the theme supports it", "oik-nivo-slider" ) )
                 );
  $syntax = array_merge( $nivo_syntax, _sc_thumbnail_full() );
  $syntax += array( "thumbs" => BW_::bw_skv( "", "n|y", __( "Thumbnail navigation", "oik-nivo-slider" ) ));
  $syntax += array( "nav" => BW_::bw_skv( "", "n|y", __( "Control navigation", "oik-nivo-slider" ) ));
  $syntax += array( "pause" => BW_::bw_skv( "", "<i>" .  __( "pause", "oik-nivo-slider" ) . "</i>", __( "Pause time in milli seconds", "oik-nivo-slider" ) ));
  $syntax += array( "manual" => BW_::bw_skv( "", "n|y", __( "Manual advance", "oik-nivo-slider" ) ));
  $syntax += array( "effect" => BW_::bw_skv( "", "random|sliceDownRight|sliceDownLeft|sliceUpRight|sliceUpLeft|sliceUpDown|sliceUpDownLeft |fold|fade|boxRandom|boxRain|boxRainReverse|boxRainGrow|boxRainGrowReverse|slideInLeft|slideInRight", __( "transition effect", "oik-nivo-slider" ) ) );
  $syntax = array_merge( $syntax, _sc_posts() );
  $syntax = array_merge( $syntax, $nivo_syntax ); // Re-apply the post_type values for this shortcode
  return( $syntax );
}

/**
 * Show an example of the Nivo shortcode
 *
 * Note: The default processing is to show attached images.
 * If there is no post (as on admin pages) then it shows unattached images.
 * An interesting side effect.
 */ 
function nivo__example( $shortcode='nivo' ) {
  $atts = 'theme=oik post_type="screenshot:oik-nivo-slider" caption=n link=n';
  $text = __( "Slideshow of the screenshots from oik-nivo-slider, using the oik theme and the current settings.", "oik-nivo-slider" );
  bw_invoke_shortcode( $shortcode, $atts, $text  );
  sediv("clear");
}

/**
 * Produce the code snippet for the nivo shortcode as used in the example
 */ 
function nivo__snippet( $shortcode='nivo', $atts=null ) {
  $atts = 'theme=oik post_type="screenshot:oik-nivo-slider" caption=n link=n';
  _sc__snippet( $shortcode, $atts );
}


