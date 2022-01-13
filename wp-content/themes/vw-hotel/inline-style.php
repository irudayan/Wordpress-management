<?php
	
	/*---------------------------First highlight color-------------------*/

	$vw_hotel_first_color = get_theme_mod('vw_hotel_first_color');

	$vw_hotel_custom_css = '';

	if($vw_hotel_first_color != false){
		$vw_hotel_custom_css .='.search-box i, #slider .carousel-control-prev-icon, #slider .carousel-control-next-icon, .more-btn a, .overlay-bttn a, input[type="submit"], .footer .tagcloud a:hover, .footer-2, .scrollup i, .sidebar input[type="submit"], .sidebar .tagcloud a:hover, .blogbutton-small, .pagination span, .pagination a, nav.woocommerce-MyAccount-navigation ul li, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce span.onsale, #comments a.comment-reply-link, .toggle-nav i, a.button, .sidebar .widget_price_filter .ui-slider .ui-slider-range, .sidebar .widget_price_filter .ui-slider .ui-slider-handle, .sidebar .woocommerce-product-search button, .footer .widget_price_filter .ui-slider .ui-slider-range, .footer .widget_price_filter .ui-slider .ui-slider-handle, .footer .woocommerce-product-search button, .footer a.custom_read_more, .sidebar a.custom_read_more, .footer .custom-social-icons i:hover, .sidebar .custom-social-icons i:hover, .nav-previous a, .nav-next a, .woocommerce nav.woocommerce-pagination ul li a, #preloader{';
			$vw_hotel_custom_css .='background-color: '.esc_attr($vw_hotel_first_color).';';
		$vw_hotel_custom_css .='}';
	}
	if($vw_hotel_first_color != false){
		$vw_hotel_custom_css .='#comments input[type="submit"].submit, .sidebar ul li::before, .sidebar ul.cart_list li::before, .sidebar ul.product_list_widget li::before{';
			$vw_hotel_custom_css .='background-color: '.esc_attr($vw_hotel_first_color).'!important;';
		$vw_hotel_custom_css .='}';
	}
	if($vw_hotel_first_color != false){
		$vw_hotel_custom_css .='a, .more-btn a:hover, .overlay-bttn a:hover, .footer h3, .post-info i, .post-navigation a:hover .post-title, .post-navigation a:focus .post-title, .woocommerce-info::before,.logo h1 a,p.site-description,.footer li a:hover, .sidebar ul li a:hover, .main-navigation ul.sub-menu a:hover, .main-navigation a:hover, .entry-content a, .sidebar .textwidget p a, .textwidget p a, #comments p a, .slider .inner_carousel p a, .logo h1 a, .logo p.site-title a, .footer .custom-social-icons i, .sidebar .custom-social-icons i, .post-main-box:hover h2 a, .post-main-box:hover .post-info a, .single-post .post-info:hover a, .about-category h4 a:hover, #about-hotel h3 a:hover, #slider .inner_carousel h1 a:hover{';
			$vw_hotel_custom_css .='color: '.esc_attr($vw_hotel_first_color).';';
		$vw_hotel_custom_css .='}';
	}
	if($vw_hotel_first_color != false){
		$vw_hotel_custom_css .='.footer .custom-social-icons i, .sidebar .custom-social-icons i, .footer .custom-social-icons i:hover, .sidebar .custom-social-icons i:hover{';
			$vw_hotel_custom_css .='border-color: '.esc_attr($vw_hotel_first_color).';';
		$vw_hotel_custom_css .='}';
	}
	if($vw_hotel_first_color != false){
		$vw_hotel_custom_css .='hr.hrclass, .woocommerce-info, .main-navigation ul ul{';
			$vw_hotel_custom_css .='border-top-color: '.esc_attr($vw_hotel_first_color).';';
		$vw_hotel_custom_css .='}';
	}
	if($vw_hotel_first_color != false){
		$vw_hotel_custom_css .='.main-navigation ul ul, .header-fixed{';
			$vw_hotel_custom_css .='border-bottom-color: '.esc_attr($vw_hotel_first_color).';';
		$vw_hotel_custom_css .='}';
	}
	if($vw_hotel_first_color != false){
		$vw_hotel_custom_css .='.post-main-box:hover{
		box-shadow: 0 0 10px 1px '.esc_attr($vw_hotel_first_color).';
		}';
	}

	/*---------------- Width Layout -------------------*/

	$vw_hotel_theme_lay = get_theme_mod( 'vw_hotel_width_option','Full Width');
    if($vw_hotel_theme_lay == 'Boxed'){
		$vw_hotel_custom_css .='body{';
			$vw_hotel_custom_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
		$vw_hotel_custom_css .='}';
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='right: 100px;';
		$vw_hotel_custom_css .='}';
		$vw_hotel_custom_css .='.scrollup.left i{';
			$vw_hotel_custom_css .='left: 100px;';
		$vw_hotel_custom_css .='}';
	}else if($vw_hotel_theme_lay == 'Wide Width'){
		$vw_hotel_custom_css .='body{';
			$vw_hotel_custom_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
		$vw_hotel_custom_css .='}';
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='right: 30px;';
		$vw_hotel_custom_css .='}';
		$vw_hotel_custom_css .='.scrollup.left i{';
			$vw_hotel_custom_css .='left: 30px;';
		$vw_hotel_custom_css .='}';
	}else if($vw_hotel_theme_lay == 'Full Width'){
		$vw_hotel_custom_css .='body{';
			$vw_hotel_custom_css .='max-width: 100%;';
		$vw_hotel_custom_css .='}';
	}

	/*--------------- Slider Opacity -------------------*/

	$vw_hotel_theme_lay = get_theme_mod( 'vw_hotel_slider_opacity_color','0.5');
	if($vw_hotel_theme_lay == '0'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.1'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.1';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.2'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.2';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.3'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.3';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.4'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.4';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.5'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.5';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.6'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.6';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.7'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.7';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.8'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.8';
		$vw_hotel_custom_css .='}';
		}else if($vw_hotel_theme_lay == '0.9'){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='opacity:0.9';
		$vw_hotel_custom_css .='}';
		}

	/*---------------------------Slider Content Layout -------------------*/

	$vw_hotel_theme_lay = get_theme_mod( 'vw_hotel_slider_content_option','Center');
    if($vw_hotel_theme_lay == 'Left'){
		$vw_hotel_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_hotel_custom_css .='text-align:left; left:15%; right:45%;';
		$vw_hotel_custom_css .='}';
	}else if($vw_hotel_theme_lay == 'Center'){
		$vw_hotel_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_hotel_custom_css .='text-align:center; left:20%; right:20%;';
		$vw_hotel_custom_css .='}';
	}else if($vw_hotel_theme_lay == 'Right'){
		$vw_hotel_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_hotel_custom_css .='text-align:right; left:45%; right:15%;';
		$vw_hotel_custom_css .='}';
	}

	/*---------------------------Slider Height ------------*/

	$vw_hotel_slider_height = get_theme_mod('vw_hotel_slider_height');
	if($vw_hotel_slider_height != false){
		$vw_hotel_custom_css .='#slider img{';
			$vw_hotel_custom_css .='height: '.esc_attr($vw_hotel_slider_height).';';
		$vw_hotel_custom_css .='}';
	}

	/*--------------------------- Slider -------------------*/

	$vw_hotel_slider = get_theme_mod('vw_hotel_slider_hide_show');
	if($vw_hotel_slider == false){
		$vw_hotel_custom_css .='.page-template-custom-home-page .home-page-header{';
			$vw_hotel_custom_css .='position: static; background: #212121;';
		$vw_hotel_custom_css .='}';
		$vw_hotel_custom_css .='#about-hotel{';
			$vw_hotel_custom_css .='margin-top: 5em;';
		$vw_hotel_custom_css .='}';
	}

	/*---------------------------Blog Layout -------------------*/

	$vw_hotel_theme_lay = get_theme_mod( 'vw_hotel_blog_layout_option','Default');
    if($vw_hotel_theme_lay == 'Default'){
		$vw_hotel_custom_css .='.post-main-box{';
			$vw_hotel_custom_css .='';
		$vw_hotel_custom_css .='}';
	}else if($vw_hotel_theme_lay == 'Center'){
		$vw_hotel_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p{';
			$vw_hotel_custom_css .='text-align:center;';
		$vw_hotel_custom_css .='}';
		$vw_hotel_custom_css .='.post-info{';
			$vw_hotel_custom_css .='margin-top:10px;';
		$vw_hotel_custom_css .='}';
	}else if($vw_hotel_theme_lay == 'Left'){
		$vw_hotel_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p{';
			$vw_hotel_custom_css .='text-align:Left;';
		$vw_hotel_custom_css .='}';
	}

	/*------------------------------Responsive Media -----------------------*/

	$vw_hotel_resp_stickyheader = get_theme_mod( 'vw_hotel_stickyheader_hide_show',false);
	if($vw_hotel_resp_stickyheader == true && get_theme_mod( 'vw_hotel_sticky_header',false) != true){
    	$vw_hotel_custom_css .='.header-fixed{';
			$vw_hotel_custom_css .='position:static;';
		$vw_hotel_custom_css .='} ';
	}
    if($vw_hotel_resp_stickyheader == true){
    	$vw_hotel_custom_css .='@media screen and (max-width:575px) {';
		$vw_hotel_custom_css .='.header-fixed{';
			$vw_hotel_custom_css .='position:fixed;';
		$vw_hotel_custom_css .='} }';
	}else if($vw_hotel_resp_stickyheader == false){
		$vw_hotel_custom_css .='@media screen and (max-width:575px){';
		$vw_hotel_custom_css .='.header-fixed{';
			$vw_hotel_custom_css .='position:static;';
		$vw_hotel_custom_css .='} }';
	}

	$vw_hotel_resp_slider = get_theme_mod( 'vw_hotel_resp_slider_hide_show',false);
	if($vw_hotel_resp_slider == true && get_theme_mod( 'vw_hotel_slider_hide_show', false) == false){
    	$vw_hotel_custom_css .='#slider{';
			$vw_hotel_custom_css .='display:none;';
		$vw_hotel_custom_css .='} ';
	}
    if($vw_hotel_resp_slider == true){
    	$vw_hotel_custom_css .='@media screen and (max-width:575px) {';
		$vw_hotel_custom_css .='#slider{';
			$vw_hotel_custom_css .='display:block;';
		$vw_hotel_custom_css .='} }';
	}else if($vw_hotel_resp_slider == false){
		$vw_hotel_custom_css .='@media screen and (max-width:575px) {';
		$vw_hotel_custom_css .='#slider{';
			$vw_hotel_custom_css .='display:none;';
		$vw_hotel_custom_css .='} }';
	}

	$vw_hotel_resp_sidebar = get_theme_mod( 'vw_hotel_sidebar_hide_show',true);
    if($vw_hotel_resp_sidebar == true){
    	$vw_hotel_custom_css .='@media screen and (max-width:575px) {';
		$vw_hotel_custom_css .='.sidebar{';
			$vw_hotel_custom_css .='display:block;';
		$vw_hotel_custom_css .='} }';
	}else if($vw_hotel_resp_sidebar == false){
		$vw_hotel_custom_css .='@media screen and (max-width:575px) {';
		$vw_hotel_custom_css .='.sidebar{';
			$vw_hotel_custom_css .='display:none;';
		$vw_hotel_custom_css .='} }';
	}

	$vw_hotel_resp_scroll_top = get_theme_mod( 'vw_hotel_resp_scroll_top_hide_show',true);
	if($vw_hotel_resp_scroll_top == true && get_theme_mod( 'vw_hotel_hide_show_scroll',true) != true){
    	$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='visibility:hidden !important;';
		$vw_hotel_custom_css .='} ';
	}
    if($vw_hotel_resp_scroll_top == true){
    	$vw_hotel_custom_css .='@media screen and (max-width:575px) {';
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='visibility:visible !important;';
		$vw_hotel_custom_css .='} }';
	}else if($vw_hotel_resp_scroll_top == false){
		$vw_hotel_custom_css .='@media screen and (max-width:575px){';
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='visibility:hidden !important;';
		$vw_hotel_custom_css .='} }';
	}

	/*-------------- Sticky Header Padding ----------------*/

	$vw_hotel_sticky_header_padding = get_theme_mod('vw_hotel_sticky_header_padding');
	if($vw_hotel_sticky_header_padding != false){
		$vw_hotel_custom_css .='.header-fixed{';
			$vw_hotel_custom_css .='padding: '.esc_attr($vw_hotel_sticky_header_padding).';';
		$vw_hotel_custom_css .='}';
	}

	/*------------------ Search Settings -----------------*/
	
	$vw_hotel_search_padding_top_bottom = get_theme_mod('vw_hotel_search_padding_top_bottom');
	$vw_hotel_search_padding_left_right = get_theme_mod('vw_hotel_search_padding_left_right');
	$vw_hotel_search_font_size = get_theme_mod('vw_hotel_search_font_size');
	$vw_hotel_search_border_radius = get_theme_mod('vw_hotel_search_border_radius');
	if($vw_hotel_search_padding_top_bottom != false || $vw_hotel_search_padding_left_right != false || $vw_hotel_search_font_size != false || $vw_hotel_search_border_radius != false){
		$vw_hotel_custom_css .='.search-box i{';
			$vw_hotel_custom_css .='padding-top: '.esc_attr($vw_hotel_search_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_hotel_search_padding_top_bottom).';padding-left: '.esc_attr($vw_hotel_search_padding_left_right).';padding-right: '.esc_attr($vw_hotel_search_padding_left_right).';font-size: '.esc_attr($vw_hotel_search_font_size).';border-radius: '.esc_attr($vw_hotel_search_border_radius).'px;';
		$vw_hotel_custom_css .='}';
	}

	/*---------------- Button Settings ------------------*/

	$vw_hotel_button_padding_top_bottom = get_theme_mod('vw_hotel_button_padding_top_bottom');
	$vw_hotel_button_padding_left_right = get_theme_mod('vw_hotel_button_padding_left_right');
	if($vw_hotel_button_padding_top_bottom != false || $vw_hotel_button_padding_left_right != false){
		$vw_hotel_custom_css .='.blogbutton-small{';
			$vw_hotel_custom_css .='padding-top: '.esc_attr($vw_hotel_button_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_hotel_button_padding_top_bottom).';padding-left: '.esc_attr($vw_hotel_button_padding_left_right).';padding-right: '.esc_attr($vw_hotel_button_padding_left_right).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_button_border_radius = get_theme_mod('vw_hotel_button_border_radius');
	if($vw_hotel_button_border_radius != false){
		$vw_hotel_custom_css .='.blogbutton-small, .hvr-sweep-to-right:before{';
			$vw_hotel_custom_css .='border-radius: '.esc_attr($vw_hotel_button_border_radius).'px;';
		$vw_hotel_custom_css .='}';
	}

	/*------------- Single Blog Page------------------*/

	$vw_hotel_single_blog_post_navigation_show_hide = get_theme_mod('vw_hotel_single_blog_post_navigation_show_hide',true);
	if($vw_hotel_single_blog_post_navigation_show_hide != true){
		$vw_hotel_custom_css .='.post-navigation{';
			$vw_hotel_custom_css .='display: none;';
		$vw_hotel_custom_css .='}';
	}

	/*-------------- Copyright Alignment ----------------*/

	$vw_hotel_copyright_alingment = get_theme_mod('vw_hotel_copyright_alingment');
	if($vw_hotel_copyright_alingment != false){
		$vw_hotel_custom_css .='.copyright p{';
			$vw_hotel_custom_css .='text-align: '.esc_attr($vw_hotel_copyright_alingment).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_copyright_padding_top_bottom = get_theme_mod('vw_hotel_copyright_padding_top_bottom');
	if($vw_hotel_copyright_padding_top_bottom != false){
		$vw_hotel_custom_css .='.footer-2{';
			$vw_hotel_custom_css .='padding-top: '.esc_attr($vw_hotel_copyright_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_hotel_copyright_padding_top_bottom).';';
		$vw_hotel_custom_css .='}';
	}

	/*----------------Sroll to top Settings ------------------*/

	$vw_hotel_scroll_to_top_font_size = get_theme_mod('vw_hotel_scroll_to_top_font_size');
	if($vw_hotel_scroll_to_top_font_size != false){
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='font-size: '.esc_attr($vw_hotel_scroll_to_top_font_size).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_scroll_to_top_padding = get_theme_mod('vw_hotel_scroll_to_top_padding');
	$vw_hotel_scroll_to_top_padding = get_theme_mod('vw_hotel_scroll_to_top_padding');
	if($vw_hotel_scroll_to_top_padding != false){
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='padding-top: '.esc_attr($vw_hotel_scroll_to_top_padding).';padding-bottom: '.esc_attr($vw_hotel_scroll_to_top_padding).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_scroll_to_top_width = get_theme_mod('vw_hotel_scroll_to_top_width');
	if($vw_hotel_scroll_to_top_width != false){
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='width: '.esc_attr($vw_hotel_scroll_to_top_width).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_scroll_to_top_height = get_theme_mod('vw_hotel_scroll_to_top_height');
	if($vw_hotel_scroll_to_top_height != false){
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='height: '.esc_attr($vw_hotel_scroll_to_top_height).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_scroll_to_top_border_radius = get_theme_mod('vw_hotel_scroll_to_top_border_radius');
	if($vw_hotel_scroll_to_top_border_radius != false){
		$vw_hotel_custom_css .='.scrollup i{';
			$vw_hotel_custom_css .='border-radius: '.esc_attr($vw_hotel_scroll_to_top_border_radius).'px;';
		$vw_hotel_custom_css .='}';
	}

	/*----------------Social Icons Settings ------------------*/

	$vw_hotel_social_icon_font_size = get_theme_mod('vw_hotel_social_icon_font_size');
	if($vw_hotel_social_icon_font_size != false){
		$vw_hotel_custom_css .='.sidebar .custom-social-icons i, .footer .custom-social-icons i{';
			$vw_hotel_custom_css .='font-size: '.esc_attr($vw_hotel_social_icon_font_size).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_social_icon_padding = get_theme_mod('vw_hotel_social_icon_padding');
	if($vw_hotel_social_icon_padding != false){
		$vw_hotel_custom_css .='.sidebar .custom-social-icons i, .footer .custom-social-icons i{';
			$vw_hotel_custom_css .='padding: '.esc_attr($vw_hotel_social_icon_padding).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_social_icon_width = get_theme_mod('vw_hotel_social_icon_width');
	if($vw_hotel_social_icon_width != false){
		$vw_hotel_custom_css .='.sidebar .custom-social-icons i, .footer .custom-social-icons i{';
			$vw_hotel_custom_css .='width: '.esc_attr($vw_hotel_social_icon_width).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_social_icon_height = get_theme_mod('vw_hotel_social_icon_height');
	if($vw_hotel_social_icon_height != false){
		$vw_hotel_custom_css .='.sidebar .custom-social-icons i, .footer .custom-social-icons i{';
			$vw_hotel_custom_css .='height: '.esc_attr($vw_hotel_social_icon_height).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_social_icon_border_radius = get_theme_mod('vw_hotel_social_icon_border_radius');
	if($vw_hotel_social_icon_border_radius != false){
		$vw_hotel_custom_css .='.sidebar .custom-social-icons i, .footer .custom-social-icons i{';
			$vw_hotel_custom_css .='border-radius: '.esc_attr($vw_hotel_social_icon_border_radius).'px;';
		$vw_hotel_custom_css .='}';
	}

	/*----------------Woocommerce Products Settings ------------------*/

	$vw_hotel_products_padding_top_bottom = get_theme_mod('vw_hotel_products_padding_top_bottom');
	if($vw_hotel_products_padding_top_bottom != false){
		$vw_hotel_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_hotel_custom_css .='padding-top: '.esc_attr($vw_hotel_products_padding_top_bottom).'!important; padding-bottom: '.esc_attr($vw_hotel_products_padding_top_bottom).'!important;';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_products_padding_left_right = get_theme_mod('vw_hotel_products_padding_left_right');
	if($vw_hotel_products_padding_left_right != false){
		$vw_hotel_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_hotel_custom_css .='padding-left: '.esc_attr($vw_hotel_products_padding_left_right).'!important; padding-right: '.esc_attr($vw_hotel_products_padding_left_right).'!important;';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_products_box_shadow = get_theme_mod('vw_hotel_products_box_shadow');
	if($vw_hotel_products_box_shadow != false){
		$vw_hotel_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
				$vw_hotel_custom_css .='box-shadow: '.esc_attr($vw_hotel_products_box_shadow).'px '.esc_attr($vw_hotel_products_box_shadow).'px '.esc_attr($vw_hotel_products_box_shadow).'px #ddd;';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_products_border_radius = get_theme_mod('vw_hotel_products_border_radius');
	if($vw_hotel_products_border_radius != false){
		$vw_hotel_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_hotel_custom_css .='border-radius: '.esc_attr($vw_hotel_products_border_radius).'px;';
		$vw_hotel_custom_css .='}';
	}

	/*------------------ Preloader Background Color  -------------------*/

	$vw_hotel_preloader_bg_color = get_theme_mod('vw_hotel_preloader_bg_color');
	if($vw_hotel_preloader_bg_color != false){
		$vw_hotel_custom_css .='#preloader{';
			$vw_hotel_custom_css .='background-color: '.esc_attr($vw_hotel_preloader_bg_color).';';
		$vw_hotel_custom_css .='}';
	}

	$vw_hotel_preloader_border_color = get_theme_mod('vw_hotel_preloader_border_color');
	if($vw_hotel_preloader_border_color != false){
		$vw_hotel_custom_css .='.loader-line{';
			$vw_hotel_custom_css .='border-color: '.esc_attr($vw_hotel_preloader_border_color).'!important;';
		$vw_hotel_custom_css .='}';
	}