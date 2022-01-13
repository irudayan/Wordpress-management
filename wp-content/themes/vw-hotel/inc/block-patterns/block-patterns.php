<?php
/**
 * VW Hotel: Block Patterns
 *
 * @package VW Hotel
 * @since   1.0.0
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {

	register_block_pattern_category(
		'vw-hotel',
		array( 'label' => __( 'VW Hotel', 'vw-hotel' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {
	register_block_pattern(
		'vw-hotel/banner-section',
		array(
			'title'      => __( 'Banner Section', 'vw-hotel' ),
			'categories' => array( 'vw-hotel' ),
			'content'    => "<!-- wp:cover {\"url\":\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/banner.png\",\"id\":308,\"align\":\"full\",\"className\":\"banner-section\"} -->\n<div class=\"wp-block-cover alignfull has-background-dim banner-section\" style=\"background-image:url(" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/banner.png)\"><div class=\"wp-block-cover__inner-container\"><!-- wp:columns {\"align\":\"full\"} -->\n<div class=\"wp-block-columns alignfull\"><!-- wp:column {\"width\":\"25%\"} -->\n<div class=\"wp-block-column\" style=\"flex-basis:25%\"></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"50%\",\"className\":\"ms-0\"} -->\n<div class=\"wp-block-column ms-0\" style=\"flex-basis:50%\"><!-- wp:heading {\"textAlign\":\"center\",\"level\":1,\"textColor\":\"white\",\"style\":{\"typography\":{\"fontSize\":40}}} -->\n<h1 class=\"has-text-align-center has-white-color has-text-color\" style=\"font-size:40px\">LOREM IPSUM&nbsp;IS SIMPLY DUMMY</h1>\n<!-- /wp:heading --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"25%\",\"className\":\"ms-0\"} -->\n<div class=\"wp-block-column ms-0\" style=\"flex-basis:25%\"></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:cover -->",
		)
	);

	register_block_pattern(
		'vw-hotel/about-section',
		array(
			'title'      => __( 'About Section', 'vw-hotel' ),
			'categories' => array( 'vw-hotel' ),
			'content'    => "<!-- wp:cover {\"overlayColor\":\"white\",\"align\":\"wide\",\"className\":\"about-section mx-0 p-4\"} -->\n<div class=\"wp-block-cover alignwide has-white-background-color has-background-dim about-section mx-0 p-4\"><div class=\"wp-block-cover__inner-container\"><!-- wp:columns {\"align\":\"wide\",\"className\":\"mx-0\"} -->\n<div class=\"wp-block-columns alignwide mx-0\"><!-- wp:column {\"width\":\"66.66%\"} -->\n<div class=\"wp-block-column\" style=\"flex-basis:66.66%\"><!-- wp:heading {\"className\":\"mb-3\",\"style\":{\"color\":{\"text\":\"#b19261\"},\"typography\":{\"fontSize\":18}}} -->\n<h2 class=\"mb-3 has-text-color\" style=\"color:#b19261;font-size:18px\">Lorem Ipsum&nbsp;is simply dummy</h2>\n<!-- /wp:heading -->\n\n<!-- wp:heading {\"level\":3,\"style\":{\"color\":{\"text\":\"#212121\"},\"typography\":{\"fontSize\":25}}} -->\n<h3 class=\"has-text-color\" style=\"color:#212121;font-size:25px\"><strong>ABOUT US</strong></h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"style\":{\"color\":{\"text\":\"#121212\"},\"typography\":{\"fontSize\":14}}} -->\n<p class=\"has-text-color\" style=\"color:#121212;font-size:14px\">Lorem Ipsum&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s when an unknown</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:columns {\"className\":\"mb-0\"} -->\n<div class=\"wp-block-columns mb-0\"><!-- wp:column {\"className\":\"col-6 px-md-0\"} -->\n<div class=\"wp-block-column col-6 px-md-0\"><!-- wp:image {\"id\":336,\"sizeSlug\":\"large\",\"linkDestination\":\"none\",\"className\":\"mb-3\"} -->\n<figure class=\"wp-block-image size-large mb-3\"><img src=\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/WiFi.png\" alt=\"\" class=\"wp-image-336\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:heading {\"level\":4,\"style\":{\"color\":{\"text\":\"#212121\"},\"typography\":{\"fontSize\":17}}} -->\n<h4 class=\"has-text-color\" style=\"color:#212121;font-size:17px\">ABOUT US TITLE 1</h4>\n<!-- /wp:heading --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"className\":\"col-6 px-md-0\"} -->\n<div class=\"wp-block-column col-6 px-md-0\"><!-- wp:image {\"id\":338,\"sizeSlug\":\"large\",\"linkDestination\":\"none\",\"className\":\"mb-3\"} -->\n<figure class=\"wp-block-image size-large mb-3\"><img src=\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/Taxi.png\" alt=\"\" class=\"wp-image-338\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:heading {\"level\":4,\"style\":{\"color\":{\"text\":\"#212121\"},\"typography\":{\"fontSize\":17}}} -->\n<h4 class=\"has-text-color\" style=\"color:#212121;font-size:17px\">ABOUT US TITLE 2</h4>\n<!-- /wp:heading --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->\n\n<!-- wp:columns {\"className\":\"mb-0\"} -->\n<div class=\"wp-block-columns mb-0\"><!-- wp:column {\"className\":\"col-6 px-md-0\"} -->\n<div class=\"wp-block-column col-6 px-md-0\"><!-- wp:image {\"id\":337,\"sizeSlug\":\"large\",\"linkDestination\":\"none\",\"className\":\"mb-3\"} -->\n<figure class=\"wp-block-image size-large mb-3\"><img src=\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/Alarm.png\" alt=\"\" class=\"wp-image-337\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:heading {\"level\":4,\"style\":{\"color\":{\"text\":\"#212121\"},\"typography\":{\"fontSize\":17}}} -->\n<h4 class=\"has-text-color\" style=\"color:#212121;font-size:17px\">ABOUT US TITLE 3</h4>\n<!-- /wp:heading --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"className\":\"col-6 px-md-0\"} -->\n<div class=\"wp-block-column col-6 px-md-0\"><!-- wp:image {\"id\":339,\"sizeSlug\":\"large\",\"linkDestination\":\"none\",\"className\":\"mb-3\"} -->\n<figure class=\"wp-block-image size-large mb-3\"><img src=\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/Beakfast.png\" alt=\"\" class=\"wp-image-339\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:heading {\"level\":4,\"style\":{\"color\":{\"text\":\"#212121\"},\"typography\":{\"fontSize\":17}}} -->\n<h4 class=\"has-text-color\" style=\"color:#212121;font-size:17px\">ABOUT US TITLE 4</h4>\n<!-- /wp:heading --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"33.33%\"} -->\n<div class=\"wp-block-column\" style=\"flex-basis:33.33%\"><!-- wp:cover {\"url\":\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/about.png\",\"id\":310,\"className\":\"about-img\"} -->\n<div class=\"wp-block-cover has-background-dim about-img\" style=\"background-image:url(" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/about.png)\"><div class=\"wp-block-cover__inner-container\"><!-- wp:buttons {\"className\":\"text-center about-btn\"} -->\n<div class=\"wp-block-buttons text-center about-btn\"><!-- wp:button {\"borderRadius\":0,\"style\":{\"color\":{\"background\":\"#f1b64a\"}},\"textColor\":\"white\"} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-white-color has-text-color has-background no-border-radius\" style=\"background-color:#f1b64a\">LEARN MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div></div>\n<!-- /wp:cover --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:cover -->",
		)
	);
}