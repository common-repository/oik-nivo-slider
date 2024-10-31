<?php // (C) Copyright Bobbing Wide 2012-2017

/**
 * Define Nivo slider settings fields and page
 */
function oik_nivo_lazy_admin_menu() {
  register_setting( 'oik_nivo_options', 'bw_nivo_slider', 'oik_plugins_validate' ); // No validation for oik-nivo-slider
  add_submenu_page( 'oik_menu', __( 'oik nivo slider settings', "oik-nivo-slider" ), __( "Nivo settings", "oik-nivo-slider" ), 'manage_options', 'oik_nivo', "oik_nivo_options_do_page" );
}

/**
 * Nivo slider settings page
 */
function oik_nivo_options_do_page() {
  BW_::oik_menu_header( __( "Nivo slider options", "oik-nivo-slider" ), "w50pc" );
  BW_::oik_box( NULL, NULL, __( "Default slider options", "oik-nivo-slider" ), "oik_nivo_slider_options" );
  ecolumn();
  scolumn( "w50pc" );
  BW_::oik_box( NULL, NULL, __( "Usage notes", "oik-nivo-slider" ), "oik_nivo_slider_usage" );
  oik_menu_footer();
  bw_flush();
}

/**
 * Settings for the jQuery Nivo slider  
 *
 * Extract from jQuery Nivo slider below
 * The setting selection field has been set for each line marked with an 'x' 

      x	effect: 'random',
      x	slices: 15,
      x	boxCols: 8,
      x	boxRows: 4,
      x animSpeed: 500,
      x	pauseTime: 3000,
      	startSlide: 0,
      x	directionNav: true,
      x	directionNavHide: true,
      x	controlNav: true,
      x	controlNavThumbs: false,
        controlNavThumbsFromRel: false,
      	controlNavThumbsSearch: '.jpg',
      	controlNavThumbsReplace: '_thumb.jpg',
      	keyboardNav: true,
      x	pauseOnHover: true,
      x	manualAdvance: false,
      x	captionOpacity: 0.8,
      	prevText: 'Prev',
      	nextText: 'Next',
      	randomStart: false,
      	beforeChange: function(){},
      	afterChange: function(){},
      	slideshowEnd: function(){},
        lastSlide: function(){},
        afterLoad: function(){}
        
*/
function oik_nivo_slider_options() {
  bw_form( "options.php" );
  $option = "bw_nivo_slider"; 
  $options = get_option( $option );     
  stag( 'table class="form-table"' );
  bw_flush();
  settings_fields('oik_nivo_options');
  $effect_options = array( 'random', 'sliceDownRight','sliceDownLeft','sliceUpRight','sliceUpLeft','sliceUpDown','sliceUpDownLeft','fold','fade',
                'boxRandom','boxRain','boxRainReverse','boxRainGrow','boxRainGrowReverse', 'slideInLeft', 'slideInRight' );
  $effect_options_assoc = bw_assoc( $effect_options );                
                
  BW_::bw_select_arr( $option, __( "Effect", "oik-nivo-slider" ) . ' (effect=<i>' . __( "effect", "oik-nivo-slider" ) . '</i>)', $options, 'effect', array( "#options" => $effect_options_assoc ) );
  
  BW_::bw_textfield_arr( $option, __( "Slices", "oik-nivo-slider" ), $options, 'slices', 2 );
  BW_::bw_textfield_arr( $option, __( "Box cols", "oik-nivo-slider" ), $options, 'boxCols', 2 );
  BW_::bw_textfield_arr( $option, __( "Box rows", "oik-nivo-slider" ), $options, 'boxRows', 2 );
  BW_::bw_textfield_arr( $option, __( "Anim speed", "oik-nivo-slider" ), $options, 'animSpeed', 4 );
  BW_::bw_textfield_arr( $option, __( "Pause time", "oik-nivo-slider" ) . ' (pause=<i>nnnn</i>)', $options, 'pauseTime', 4 );
  
  bw_checkbox_arr( $option, __( "Control nav", "oik-nivo-slider" ) . ' (nav=n|y)', $options, 'controlNav' );
  bw_checkbox_arr( $option, __( "Control nav thumbs", "oik-nivo-slider" ) . ' (thumbs=n|y)', $options, 'controlNavThumbs' );
  bw_checkbox_arr( $option, __( "Direction nav", "oik-nivo-slider" ), $options, 'directionNav' );
  bw_checkbox_arr( $option, __( "Direction nav hide", "oik-nivo-slider" ), $options, 'directionNavHide' );
  bw_checkbox_arr( $option, __( "Pause on hover", "oik-nivo-slider" ), $options, 'pauseOnHover' );
  bw_checkbox_arr( $option, __( "Manual advance", "oik-nivo-slider" ) . ' (manual=n|y)', $options, 'manualAdvance' );
  
  BW_::bw_textfield_arr( $option, __( "Caption opacity", "oik-nivo-slider" ), $options, 'captionOpacity', 4 );
  
  etag( "table" ); 
  e( isubmit( "ok", __( "Save changes", "oik-nivo-slider" ), null, "button-primary" ) );
  			
  etag( "form" );
  bw_flush();
}  

/**
 * Display some usage notes for the oik-nivo-slider with an example
 */
function oik_nivo_slider_usage() {
  BW_::p( __( "These options for the jQuery Nivo slider control the default behavior for each instance of the slider.", "oik-nivo-slider" ) );
  oik_require( "nivo.php", "oik-nivo-slider" );
	oik_require_lib( "oik-sc-help" );
  bw_flush();
  nivo__example();
  sediv("cleared"); 
}


