/*
 * jQuery oik nivo-caption click  
 * http://oik-plugins.com/
 *
 * Copyright 2012, 2013, 2020 Bobbing Wide
 *
 *
 * We find the nivo-caption div for this slider and attach a click function
 * that will find the currently visible image link and open it.
 * 
 * Attach it to your Nivo slider (with an id of 'slider') using
 * 
 * <script type="text/javascript">
 *  jQuery(window).load(function() {  jQuery('#slider').oikNCClick(); });
 * </script>
 *
 */
(function($) {

  jQuery.fn.oikNCClick = function(){
    var slider = $(this);
    var nc = $('.nivo-caption', slider);
    nc.on( 'mouseenter', function() { $(this).addClass( 'nivo-caption-hovered' ); } );
    nc.on( 'mouseleave', function() { $(this).removeClass( 'nivo-caption-hovered' ); } );
    nc.css( 'cursor', 'pointer' );
    slider.on('click', ".nivo-caption", function(){
      var visible = $('a.nivo-imageLink:visible', slider);
      window.location.href = visible.attr('href');
    });
  };

})(jQuery);

