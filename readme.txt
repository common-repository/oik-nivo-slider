=== oik-nivo-slider ===
Contributors: bobbingwide, vsgloik
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags:  [nivo], shortcode, slider, jQuery, oik
Requires at least: 4.9
Tested up to: 6.5
Stable tag: 1.16.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Gutenberg compatible: Yes

[nivo] shortcode for the responsive jQuery "Nivo slider" for posts, pages, attachments and custom post types using oik

== Description ==
[nivo] shortcode for the jQuery Nivo slider; "The Most Awesome jQuery Image Slider"; reputed to be the world's most popular jQuery image slider.

= New features in nivo v1.14.1 =

* Now supports theme=custom

= New feature in nivo v1.14.0 =

* Add support for link=full, to open the original full sized image rather than the selected thumbnail= size

= New feature in nivo v1.12 =

* Choose the contents of the caption area using the format= parameter

Note: The format parameter was first made available for the [bw_pages] shortcode and is also supported in [bw_cycle]; both part of the oik base plugin.  

= Features of jQuery Nivo 3.2 =

* Flexible slider
* 16 transition effects
* 4 responsive slider themes
* Built in directional and control navigation
* Thumbnail image navigation

See also [Nivo Slider](https://github.com/Codeinwp/Nivo-Slider-jQuery) for more information about the jQuery Nivo code.

= Features of the oik nivo slider WordPress plugin =

* [nivo] lazy smart shortcode
* Displays attached images
* Displays images attached to related content
* Profile for nivo slider settings
* Display slideshows with/without links
* Display slideshows with/without captions
* Displays slideshows with HTML captions, using the format= parameter
* Transition effect can be defined in the shortcode
* Supports custom links to your content
* Supports jQuery Nivo 3.2 for responsive sliders
* nivo slider can be put into any part of your website: content, header, footer and sidebar text widgets
* 1 additional responsive slider theme
* Supports jQuery Nivo 2.7.1 for backward compatibility
* 4 additional themes for jQuery Nivo 2.7.1
* Works with existing content, does not add its own custom post type
* Uses the oik plugin's shortcode API for *lazy smart* shortcodes
* The [nivo] shortcode is interchangeable with other oik shortcodes such as [bw_images], [bw_thumbs] or [bw_pages]
* Supports display of images from a NextGEN gallery, using a "special post type" of nggallery.
* Supports display of a plugin's screenshots
* Supports display of images with fancybox

Note: oik-nivo-slider is dependent upon the oik plugin. 
You can activate it but it will not function correctly unless the pre-requisite version of oik is also activated.
Download oik from 
[oik download](https://wordpress.org/plugins/oik/)

[Find out more](https://www.oik-plugins.com/oik-plugins/oik-nivo-slider/)

== Installation ==
1. Upload the contents of the oik-nivo-slider plugin to the `/wp-content/plugins/oik-nivo-slider' directory
1. Activate the oik-nivo-slider plugin through the 'Plugins' menu in WordPress
1. Whenever you want to produce a Nivo slider use the [nivo] shortcode.

Note: oik-nivo-slider is dependent upon the oik plugin. You can activate it but it will not work unless oik is also activated.
Download oik from 
[oik download](https://wordpress.org/plugins/oik/)

== Frequently Asked Questions ==
= Installation =

1. Upload the contents of the oik-nivo-slider plugin to the `/wp-content/plugins/oik-nivo-slider' directory
1. Activate the oik-nivo-slider plugin through the 'Plugins' menu in WordPress
1. Whenever you want to produce a Nivo slider use the [nivo] shortcode.

Note: oik-nivo-slider is dependent upon the oik plugin. 
You can activate it but it will not work unless oik is also activated.
Download oik from [oik download](https://wordpress.org/plugins/oik/)

= What is the simplest syntax for the [nivo] shortcode? = 
If you simply want to show all the attached images to a page, post or custom post type then use
`[nivo]`

= I typed [nivo] and got [nivo] back =
You need to activate both the oik-nivo-slider plugin and the oik base plugin. The [nivo] shortcode only becomes functional when [oik] is loaded.

= My images do not appear in the slider =
The most common fixes to this problem are:

* Don't insert the images that you want in the slideshow into the page; just upload media and save changes.
* The slider requires jQuery. Check that your theme files contain calls to wp_head() and wp_footer().


= What are the parameters to the [nivo] shortcode? =
The basic parameters that control the display of the Nivo slider are:

`[nivo
  theme="default|bar|dark|light|oik|orman|pascal|default271|oik271 - Theme for the slideshow"
  link="y|n|file|full - Link the images to the target post/page or media file"
  caption="y|n - Display the image title as the caption"
  ribbon="y|n - Display the ribbon, if the theme supports it (version 271 only)"
  thumbnail="full|thumbnail|medium|large|nnn|wxh - image size"
  class="|classes - CSS classes"
  thumbs="|n|y - thumbnail navigation"
  effect="random|sliceDownRight|sliceDownLeft|sliceUpRight|sliceUpLeft|sliceUpDown|sliceUpDownLeft|fold|fade|boxRandom|boxRain|boxRainReverse|boxRainGrow|boxRainGrowReverse|slideInLeft|slideInRight
]`

= How do I make the images link to my content? = 
There are three methods.

1. Use the oik custom link URL field
1. Build the slideshow from images attached to related content
1. Use the format= parameter 

= Use the oik custom link URL field =

If your slide show is created from attached images then use the oik custom image link URL field in the Add Media dialog to set the target for the link.

= Images attached to related content =

Alternatively build the slideshow dynamically from images attached to related content. 

Use the post_type parameter to specify the content type and
 
* either the post_parent parameter for hierarchical content types 
* or category for posts 
* or other selection criteria for other content types


`[nivo
  post_type="post_type - Post type to display"
  post_parent="|ID - Parent ID to use if not current post"
]`

= Use the format= parameter =
`[nivo format="L" 
]`

`[nivo format="T/C"
]`


= What is the FULL syntax for the [nivo] shortcode = 
`[nivo
  post_type="attachment|post_type|special:value - Post type to display"
  theme="default|custom|bar|dark|light|oik|orman|pascal|default271|oik271 - Theme for the slideshow"
  class="|classes - CSS classes"
  link="y|n|file|full - Link the images to the target post/page or media file"
  caption="y|n - Display the image title as the caption"
  ribbon="y|n - Display the ribbon, if the theme supports it"
  thumbnail="full|thumbnail|medium|large|nnn|wxh - image size"
  thumbs="|n| y - Thumbnail navigation"
  nav="|n| y - Control navigation"
  pause="|pause - Pause time in milli seconds"
  manual="|n| y - Manual advance"
  effect="random|sliceDownRight|sliceDownLeft|sliceUpRight|sliceUpLeft|sliceUpDown|sliceUpDownLeft|fold|fade|boxRandom|boxRain|boxRainReverse|boxRainGrow|boxRainGrowReverse|slideInLeft|slideInRight - transition effect"
  numberposts="5|numeric - number to return"
  offset="0|numeric - offset from which to start"
  category="|category-id - category IDs (comma separated)"
  category_name="|category-slug - category slugs (comma separated)"
  customcategoryname="|category-slug - custom category slug"
  orderby="date|ID|title|parent|rand|menu_order - Sort sequence"
  order="DESC|ASC - Sort order."
  include="|id1,id2 - IDs to include"
  exclude="|id1,id2 - IDs to exclude"
  meta_key="|meta key - post metadata key"
  meta_value="|meta value - post metadata value"
  post_mime_type="|image|application|text|video|mime type - Attached media MIME type"
  post_parent="|ID - Parent ID to use if not current post"
  post_status="publish|pending|draft|auto-draft|future|private|trash|any - Post status"
  id="|IDs - IDs of posts to display"
]`

= Do I have to remember all those parameters? =
NO. You can enable the oik TinyMCE shortcode or quicktag buttons. See oik options > Buttons.
When editing a post/page with TinyMCE or the HTML editor click on the shortcode button to see a list of ALL enabled shortcodes and get syntax help, where available.

= What's OIK and why do I need it? = 
The oik Nivo slider is developed using the OIK (Often Included Key Information) API (Application Programming Interface).
If you don't have the OIK plugin installed and activated then the Nivo slider shortcode won't work.

Specifically, the code is dependent upon bw_get_posts() to obtain the list of attachments, posts, pages or custom post types
which populate the slider and bw_thumbnail() to select the image to display when it's not an attached image. 

= I can't see some of the images in my slideshow =
The default image size is thumbnail=full.
At present the bw_thumbnail() function will only return an image for a particular post id
when there is an attached image; it won't find the featured image. As a workaround either specify the thumbnail parameter as small,medium,large or your preferred size (e.g. 150x100)
OR ensure that the image you want to display is attached to the post
OR exclude the post from the list ( exclude=id1,id2 )

= Do I need to make my images the same size? =
NO, not any more. 
If you use any one of the five themes associated with jQuery Nivo version 3.1 then the images can be different sizes.
Warning: you might not like the results though.

= Can I control the slideshow transitions? =
YES. Use the Nivo slider settings page.

= Can I provide my own themeing? =
This is planned for a future version.

= Can I put the [nivo] shortcode in my sidebar? =
YES. You can use the [nivo] shortcode in sidebars, headers and footers by using a text widget.
It's just like entering the shortcode into a post, page or custom post type.
Remember you may need to set the post_parent parameter to control which posts are loaded.

= Can I code the shortcode into header.php? =
Not directly. In order to get the shortcode to expand you need to wrap it in some php.
One way of achieving this is to code
`<?php echo do_shortcode('[nivo post_type=attachment post_parent=487 caption=n]'); ?>`

= Which version of the jQuery Nivo slider code is needed? =
The plugin includes multiple versions of the FREE jQuery Nivo slider from Dev7 Studios
* Version 3.2 is the latest version producing responsive slideshows.
* oik-nivo-slider continues to support the themes for version 2.7.1: default271, orman, pascal and oik271
 
= Does it support version 3.2 of the jQuery Nivo slider? =
YES... from oik-nivo-slider version 1.9

= Does it support thumbnail navigation? =
YES... from oik-nivo-slider version 1.7 with oik 1.17 or higher
Thumbnail navigation is supported from version 3.1 of the nivo jQuery code.

= Can I choose the effect per slider? =
YES... Use the effect= parameter e.g. [nivo effect=boxRain]

= What's the difference between this plugin and Nivo's WordPress plugin =
I have not tried Nivo's plugin.
 

= Does it support NextGEN galleries? =
YES. Basic support for images in NextGEN galleries has been added in version 1.18
You need to use a special post_type parameter 
e.g. 
[nivo post_type=nggallery:1]

* The special post_type is nggallery
* Then you need a colon (:)
* Then the ID of the gallery

= What other special post types are there? =
oik-nivo-slider has built in support for:

* nggallery:id - display the images from a NextGEN gallery 
* screenshot:plugin_name - display the screenshots for an installed plugin

= Can you tell me more? =
YES. See [oik-nivo-slider](https://www.oik-plugins.com/oik-plugins/oik-nivo-slider/)

== Screenshots ==
1. Nivo slider - default theme [nivo]
2. Nivo slider - bar theme [nivo theme=bar]
3. Nivo slider - dark theme [nivo theme=dark]
4. Nivo slider - light theme [nivo theme=light]
5. Nivo slider - oik theme - not hovered over [nivo theme=oik ] 
6. Nivo slider - oik theme - image hovered over
7. Nivo slider - oik theme - caption hovered over
8. Nivo slider - default271 theme [nivo theme=default271]
9. Nivo slider - orman theme [nivo theme=orman]
10. Nivo slider - pascal theme [nivo theme=pascal]
11. Nivo slider - oik271 theme [nivo theme=oik271]
12. oik Nivo slider options page

== Upgrade Notice ==
= 1.16.4 =
Implements Required Plugins from WordPress 6.5. Tested with PHP 8.3

== Changelog ==
= 1.16.4 =
* Changed: Support PHP 8.3 #13
* Tested: With WordPress 6.5 and WordPress Multisite
* Tested: With PHP 8.3
* Tested: With PHPUnit 9.6

== Further reading ==
If you want to read more about the oik plugins then please visit the
[oik plugin](https://www.oik-plugins.com/oik) 
**"the oik plugin - for often included key-information"**