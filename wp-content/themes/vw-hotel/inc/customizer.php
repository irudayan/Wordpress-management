<?php
/**
 * VW Hotel Theme Customizer
 *
 * @package VW Hotel
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function vw_hotel_custom_controls() {

    load_template( trailingslashit( get_template_directory() ) . '/inc/custom-controls.php' );
}
add_action( 'customize_register', 'vw_hotel_custom_controls' );

function vw_hotel_customize_register( $wp_customize ) {

	load_template( trailingslashit( get_template_directory() ) . '/inc/icon-picker.php' );

	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage'; 
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial( 'blogname', array( 
		'selector' => '.logo .site-title a', 
	 	'render_callback' => 'vw_hotel_customize_partial_blogname', 
	)); 

	$wp_customize->selective_refresh->add_partial( 'blogdescription', array( 
		'selector' => 'p.site-description', 
		'render_callback' => 'vw_hotel_customize_partial_blogdescription', 
	));

	//add home page setting pannel
	$VWHotelParentPanel = new VW_Hotel_WP_Customize_Panel( $wp_customize, 'vw_hotel_panel_id', array(
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => esc_html__( 'VW Settings', 'vw-hotel' ),
		'priority' => 10,
	));

	$wp_customize->add_section( 'vw_hotel_left_right', array(
    	'title'      => esc_html__( 'General Settings', 'vw-hotel' ),
		'panel' => 'vw_hotel_panel_id'
	) );

	$wp_customize->add_setting('vw_hotel_width_option',array(
        'default' => 'Full Width',
        'sanitize_callback' => 'vw_hotel_sanitize_choices'
	));
	$wp_customize->add_control(new VW_Hotel_Image_Radio_Control($wp_customize, 'vw_hotel_width_option', array(
        'type' => 'select',
        'label' => __('Width Layouts','vw-hotel'),
        'description' => __('Here you can change the width layout of Website.','vw-hotel'),
        'section' => 'vw_hotel_left_right',
        'choices' => array(
            'Full Width' => esc_url(get_template_directory_uri()).'/images/full-width.png',
            'Wide Width' => esc_url(get_template_directory_uri()).'/images/wide-width.png',
            'Boxed' => esc_url(get_template_directory_uri()).'/images/boxed-width.png',
    ))));

	// Add Settings and Controls for Layout
	$wp_customize->add_setting('vw_hotel_theme_options',array(
        'default' => 'Right Sidebar',
        'sanitize_callback' => 'vw_hotel_sanitize_choices'	        
	) );
	$wp_customize->add_control('vw_hotel_theme_options', array(
        'type' => 'select',
        'label' => __('Post Sidebar Layout','vw-hotel'),
        'description' => __('Here you can change the sidebar layout for posts. ','vw-hotel'),
        'section' => 'vw_hotel_left_right',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','vw-hotel'),
            'Right Sidebar' => __('Right Sidebar','vw-hotel'),
            'One Column' => __('One Column','vw-hotel'),
            'Three Columns' => __('Three Columns','vw-hotel'),
            'Four Columns' => __('Four Columns','vw-hotel'),
            'Grid Layout' => __('Grid Layout','vw-hotel')
        ),
	));

	$wp_customize->add_setting('vw_hotel_page_layout',array(
        'default' => 'One Column',
        'sanitize_callback' => 'vw_hotel_sanitize_choices'
	));
	$wp_customize->add_control('vw_hotel_page_layout',array(
        'type' => 'select',
        'label' => __('Page Sidebar Layout','vw-hotel'),
        'description' => __('Here you can change the sidebar layout for pages. ','vw-hotel'),
        'section' => 'vw_hotel_left_right',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','vw-hotel'),
            'Right Sidebar' => __('Right Sidebar','vw-hotel'),
            'One Column' => __('One Column','vw-hotel')
        ),
	) );

	$wp_customize->add_setting( 'vw_hotel_search_hide_show', array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_search_hide_show',
       array(
		'label' => esc_html__( 'Show / Hide Search','vw-hotel' ),
		'section' => 'vw_hotel_left_right'
    )));

    $wp_customize->add_setting('vw_hotel_search_icon',array(
		'default'	=> 'fas fa-search',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new VW_Hotel_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hotel_search_icon',array(
		'label'	=> __('Add Search Icon','vw-hotel'),
		'transport' => 'refresh',
		'section'	=> 'vw_hotel_left_right',
		'setting'	=> 'vw_hotel_search_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('vw_hotel_search_close_icon',array(
		'default'	=> 'fa fa-window-close',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new VW_Hotel_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hotel_search_close_icon',array(
		'label'	=> __('Add Search Close Icon','vw-hotel'),
		'transport' => 'refresh',
		'section'	=> 'vw_hotel_left_right',
		'setting'	=> 'vw_hotel_search_close_icon',
		'type'		=> 'icon'
	)));

    $wp_customize->add_setting('vw_hotel_search_font_size',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_search_font_size',array(
		'label'	=> __('Search Font Size','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_left_right',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_search_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_search_padding_top_bottom',array(
		'label'	=> __('Search Padding Top Bottom','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_left_right',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_search_padding_left_right',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_search_padding_left_right',array(
		'label'	=> __('Search Padding Left Right','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_left_right',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'vw_hotel_search_border_radius', array(
		'default'              => "",
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_search_border_radius', array(
		'label'       => esc_html__( 'Search Border Radius','vw-hotel' ),
		'section'     => 'vw_hotel_left_right',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	//Sticky Header
	$wp_customize->add_setting( 'vw_hotel_sticky_header',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_sticky_header',array(
        'label' => esc_html__( 'Sticky Header','vw-hotel' ),
        'section' => 'vw_hotel_left_right'
    )));

    $wp_customize->add_setting('vw_hotel_sticky_header_padding',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_sticky_header_padding',array(
		'label'	=> __('Sticky Header Padding','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_left_right',
		'type'=> 'text'
	));

	//Pre-Loader
	$wp_customize->add_setting( 'vw_hotel_loader_enable',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_loader_enable',array(
        'label' => esc_html__( 'Pre-Loader','vw-hotel' ),
        'section' => 'vw_hotel_left_right'
    )));

	$wp_customize->add_setting('vw_hotel_preloader_bg_color', array(
		'default'           => '#f1b64a',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hotel_preloader_bg_color', array(
		'label'    => __('Pre-Loader Background Color', 'vw-hotel'),
		'section'  => 'vw_hotel_left_right',
	)));

	$wp_customize->add_setting('vw_hotel_preloader_border_color', array(
		'default'           => '#ffffff',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hotel_preloader_border_color', array(
		'label'    => __('Pre-Loader Border Color', 'vw-hotel'),
		'section'  => 'vw_hotel_left_right',
	)));    
    
	//Slider
	$wp_customize->add_section( 'vw_hotel_slidersettings' , array(
    	'title'      => __( 'Slider Settings', 'vw-hotel' ),
		'priority'   => null,
		'panel' => 'vw_hotel_panel_id'
	) );

	$wp_customize->add_setting( 'vw_hotel_slider_hide_show',
       array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_slider_hide_show',
       array(
      'label' => esc_html__( 'Show / Hide Slider','vw-hotel' ),
      'section' => 'vw_hotel_slidersettings'
    )));

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('vw_hotel_slider_hide_show',array(
		'selector'        => '#slider .inner_carousel h1',
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_slider_hide_show',
	));

	for ( $count = 1; $count <= 4; $count++ ) {
		// Add color scheme setting and control.
		$wp_customize->add_setting( 'vw_hotel_slider_page' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'vw_hotel_sanitize_dropdown_pages'
		) );
		$wp_customize->add_control( 'vw_hotel_slider_page' . $count, array(
			'label'    => __( 'Select Slide Image Page', 'vw-hotel' ),
			'description' => __('Slider image size (1500 x 665)','vw-hotel'),
			'section'  => 'vw_hotel_slidersettings',
			'type'     => 'dropdown-pages'
		) );
	}

	$wp_customize->add_setting('vw_hotel_slider_button_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_slider_button_text',array(
		'label'	=> __('Add Slider Button Text','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( 'READ MORE', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_slidersettings',
		'type'=> 'text'
	));

	//content layout
	$wp_customize->add_setting('vw_hotel_slider_content_option',array(
        'default' => 'Center',
        'sanitize_callback' => 'vw_hotel_sanitize_choices'
	));
	$wp_customize->add_control(new VW_Hotel_Image_Radio_Control($wp_customize, 'vw_hotel_slider_content_option', array(
        'type' => 'select',
        'label' => __('Slider Content Layouts','vw-hotel'),
        'section' => 'vw_hotel_slidersettings',
        'choices' => array(
            'Left' => esc_url(get_template_directory_uri()).'/images/slider-content1.png',
            'Center' => esc_url(get_template_directory_uri()).'/images/slider-content2.png',
            'Right' => esc_url(get_template_directory_uri()).'/images/slider-content3.png',
    ))));

    //Slider excerpt
	$wp_customize->add_setting( 'vw_hotel_slider_excerpt_number', array(
		'default'              => 30,
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_slider_excerpt_number', array(
		'label'       => esc_html__( 'Slider Excerpt length','vw-hotel' ),
		'section'     => 'vw_hotel_slidersettings',
		'type'        => 'range',
		'settings'    => 'vw_hotel_slider_excerpt_number',
		'input_attrs' => array(
			'step'             => 5,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	//Opacity
	$wp_customize->add_setting('vw_hotel_slider_opacity_color',array(
      'default'              => 0.5,
      'sanitize_callback' => 'vw_hotel_sanitize_choices'
	));

	$wp_customize->add_control( 'vw_hotel_slider_opacity_color', array(
	'label'       => esc_html__( 'Slider Image Opacity','vw-hotel' ),
	'section'     => 'vw_hotel_slidersettings',
	'type'        => 'select',
	'settings'    => 'vw_hotel_slider_opacity_color',
	'choices' => array(
      '0' =>  esc_attr('0','vw-hotel'),
      '0.1' =>  esc_attr('0.1','vw-hotel'),
      '0.2' =>  esc_attr('0.2','vw-hotel'),
      '0.3' =>  esc_attr('0.3','vw-hotel'),
      '0.4' =>  esc_attr('0.4','vw-hotel'),
      '0.5' =>  esc_attr('0.5','vw-hotel'),
      '0.6' =>  esc_attr('0.6','vw-hotel'),
      '0.7' =>  esc_attr('0.7','vw-hotel'),
      '0.8' =>  esc_attr('0.8','vw-hotel'),
      '0.9' =>  esc_attr('0.9','vw-hotel')
	),
	));

	//Slider height
	$wp_customize->add_setting('vw_hotel_slider_height',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_slider_height',array(
		'label'	=> __('Slider Height','vw-hotel'),
		'description'	=> __('Specify the slider height (px).','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '500px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_slidersettings',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'vw_hotel_slider_speed', array(
		'default'  => 4000,
		'sanitize_callback'	=> 'vw_hotel_sanitize_float'
	) );
	$wp_customize->add_control( 'vw_hotel_slider_speed', array(
		'label' => esc_html__('Slider Transition Speed','vw-hotel'),
		'section' => 'vw_hotel_slidersettings',
		'type'  => 'number',
	) );

	// About
	$wp_customize->add_section('vw_hotel_aboutus_section',array(
		'title'	=> __('About Section','vw-hotel'),
		'description'	=> __('Add About sections below.','vw-hotel'),
		'panel' => 'vw_hotel_panel_id',
	));

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial( 'vw_hotel_section_title', array( 
		'selector' => '#about-hotel h3 a', 
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_section_title',
	));

	$wp_customize->add_setting('vw_hotel_section_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control('vw_hotel_section_title',array(
		'label'	=> __('Section Title','vw-hotel'),
		'section'=> 'vw_hotel_aboutus_section',
		'setting'=> 'vw_hotel_section_title',
		'type'=> 'text'
	));

	for ( $count = 1; $count <= 1; $count++ ) {
		// Add color scheme setting and control.
		$wp_customize->add_setting( 'vw_hotel_about_section' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'vw_hotel_sanitize_dropdown_pages'
		) );
		$wp_customize->add_control( 'vw_hotel_about_section' . $count, array(
			'label'    => __( 'Select Page', 'vw-hotel' ),
			'section'  => 'vw_hotel_aboutus_section',
			'type'     => 'dropdown-pages'
		) );
	}

	$args = array('numberposts' => -1);
	$post_list = get_posts($args);
	$i = 0;
	$pst[]='Select'; 
	foreach($post_list as $post){
		$pst[$post->post_title] = $post->post_title;
	}
	$wp_customize->add_setting('vw_hotel_offer_image',array(
		'sanitize_callback' => 'vw_hotel_sanitize_choices',
	));
	$wp_customize->add_control('vw_hotel_offer_image',array(
		'type'    => 'select',
		'choices' => $pst,
		'label' => __('Select post','vw-hotel'),		
		'description' => __('Image size (350 x 400)','vw-hotel'),
		'section' => 'vw_hotel_aboutus_section',
	));

	$wp_customize->add_setting('vw_hotel_about_button_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_about_button_text',array(
		'label'	=> __('Add About Button Text','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( 'LEARN MORE', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_aboutus_section',
		'type'=> 'text'
	));

	$categories = get_categories();
	$cats = array();
	$i = 0;
	$cat_post[]= 'select';
	foreach($categories as $category){
	if($i==0){
	$default = $category->slug;
	$i++;
	}
	$cat_post[$category->slug] = $category->name;
	}

	$wp_customize->add_setting('vw_hotel_service_category',array(
		'default'	=> 'select',
		'sanitize_callback' => 'vw_hotel_sanitize_choices',
	));
	$wp_customize->add_control('vw_hotel_service_category',array(
		'type'    => 'select',
		'choices' => $cat_post,
		'label' => __('Select Category to display Services','vw-hotel'),
		'description' => __('Image size (60 x 60)','vw-hotel'),
		'section' => 'vw_hotel_aboutus_section',
	));

	//About excerpt
	$wp_customize->add_setting( 'vw_hotel_about_excerpt_number', array(
		'default'              => 30,
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_about_excerpt_number', array(
		'label'       => esc_html__( 'About Excerpt length','vw-hotel' ),
		'section'     => 'vw_hotel_aboutus_section',
		'type'        => 'range',
		'settings'    => 'vw_hotel_about_excerpt_number',
		'input_attrs' => array(
			'step'             => 5,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	//Blog Post
	$wp_customize->add_panel( $VWHotelParentPanel );

	$BlogPostParentPanel = new VW_Hotel_WP_Customize_Panel( $wp_customize, 'blog_post_parent_panel', array(
		'title' => __( 'Blog Post Settings', 'vw-hotel' ),
		'panel' => 'vw_hotel_panel_id',
	));

	$wp_customize->add_panel( $BlogPostParentPanel );

	// Add example section and controls to the middle (second) panel
	$wp_customize->add_section( 'vw_hotel_post_settings', array(
		'title' => __( 'Post Settings', 'vw-hotel' ),
		'panel' => 'blog_post_parent_panel',
	));

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('vw_hotel_toggle_postdate', array( 
		'selector' => '.post-main-box h2 a', 
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_toggle_postdate', 
	));

	$wp_customize->add_setting( 'vw_hotel_toggle_postdate',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_toggle_postdate',array(
        'label' => esc_html__( 'Post Date','vw-hotel' ),
        'section' => 'vw_hotel_post_settings'
    )));

    $wp_customize->add_setting( 'vw_hotel_toggle_author',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_toggle_author',array(
		'label' => esc_html__( 'Author','vw-hotel' ),
		'section' => 'vw_hotel_post_settings'
    )));

    $wp_customize->add_setting( 'vw_hotel_toggle_comments',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_toggle_comments',array(
		'label' => esc_html__( 'Comments','vw-hotel' ),
		'section' => 'vw_hotel_post_settings'
    )));

    $wp_customize->add_setting( 'vw_hotel_toggle_time',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_toggle_time',array(
		'label' => esc_html__( 'Time','vw-hotel' ),
		'section' => 'vw_hotel_post_settings'
    )));

    $wp_customize->add_setting( 'vw_hotel_excerpt_number', array(
		'default'              => 30,
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_excerpt_number', array(
		'label'       => esc_html__( 'Excerpt length','vw-hotel' ),
		'section'     => 'vw_hotel_post_settings',
		'type'        => 'range',
		'settings'    => 'vw_hotel_excerpt_number',
		'input_attrs' => array(
			'step'             => 5,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	//Blog layout
    $wp_customize->add_setting('vw_hotel_blog_layout_option',array(
        'default' => 'Default',
        'sanitize_callback' => 'vw_hotel_sanitize_choices'
    ));
    $wp_customize->add_control(new VW_Hotel_Image_Radio_Control($wp_customize, 'vw_hotel_blog_layout_option', array(
        'type' => 'select',
        'label' => __('Blog Layouts','vw-hotel'),
        'section' => 'vw_hotel_post_settings',
        'choices' => array(
            'Default' => esc_url(get_template_directory_uri()).'/images/blog-layout1.png',
            'Center' => esc_url(get_template_directory_uri()).'/images/blog-layout2.png',
            'Left' => esc_url(get_template_directory_uri()).'/images/blog-layout3.png',
    ))));

    $wp_customize->add_setting('vw_hotel_excerpt_settings',array(
        'default' => 'Excerpt',
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hotel_sanitize_choices'
	));
	$wp_customize->add_control('vw_hotel_excerpt_settings',array(
        'type' => 'select',
        'label' => __('Post Content','vw-hotel'),
        'section' => 'vw_hotel_post_settings',
        'choices' => array(
        	'Content' => __('Content','vw-hotel'),
            'Excerpt' => __('Excerpt','vw-hotel'),
            'No Content' => __('No Content','vw-hotel')
        ),
	) );

	$wp_customize->add_setting('vw_hotel_excerpt_suffix',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_excerpt_suffix',array(
		'label'	=> __('Add Excerpt Suffix','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '[...]', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_post_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'vw_hotel_blog_pagination_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_blog_pagination_hide_show',array(
      'label' => esc_html__( 'Show / Hide Blog Pagination','vw-hotel' ),
      'section' => 'vw_hotel_post_settings'
    )));

	$wp_customize->add_setting( 'vw_hotel_blog_pagination_type', array(
        'default'			=> 'blog-page-numbers',
        'sanitize_callback'	=> 'vw_hotel_sanitize_choices'
    ));
    $wp_customize->add_control( 'vw_hotel_blog_pagination_type', array(
        'section' => 'vw_hotel_post_settings',
        'type' => 'select',
        'label' => __( 'Blog Pagination', 'vw-hotel' ),
        'choices'		=> array(
            'blog-page-numbers'  => __( 'Numeric', 'vw-hotel' ),
            'next-prev' => __( 'Older Posts/Newer Posts', 'vw-hotel' ),
    )));

    // Button Settings
	$wp_customize->add_section( 'vw_hotel_button_settings', array(
		'title' => __( 'Button Settings', 'vw-hotel' ),
		'panel' => 'blog_post_parent_panel',
	));

	$wp_customize->add_setting('vw_hotel_button_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_button_padding_top_bottom',array(
		'label'	=> __('Padding Top Bottom','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_button_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_button_padding_left_right',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_button_padding_left_right',array(
		'label'	=> __('Padding Left Right','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_button_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'vw_hotel_button_border_radius', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_button_border_radius', array(
		'label'       => esc_html__( 'Button Border Radius','vw-hotel' ),
		'section'     => 'vw_hotel_button_settings',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('vw_hotel_button_text', array(
		'selector' => '.post-main-box .content-bttn a', 
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_button_text', 
	));

	$wp_customize->add_setting('vw_hotel_button_text',array(
		'default'=> esc_html__( 'Read More', 'vw-hotel' ),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_button_text',array(
		'label'	=> __('Add Button Text','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( 'Read More', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_button_settings',
		'type'=> 'text'
	));

	// Related Post Settings
	$wp_customize->add_section( 'vw_hotel_related_posts_settings', array(
		'title' => __( 'Related Posts Settings', 'vw-hotel' ),
		'panel' => 'blog_post_parent_panel',
	));

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('vw_hotel_related_post_title', array( 
		'selector' => '.related-post h3', 
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_related_post_title', 
	));

    $wp_customize->add_setting( 'vw_hotel_related_post',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_related_post',array(
		'label' => esc_html__( 'Related Post','vw-hotel' ),
		'section' => 'vw_hotel_related_posts_settings'
    )));

    $wp_customize->add_setting('vw_hotel_related_post_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_related_post_title',array(
		'label'	=> __('Add Related Post Title','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( 'Related Post', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_related_posts_settings',
		'type'=> 'text'
	));

   	$wp_customize->add_setting('vw_hotel_related_posts_count',array(
		'default'=> 3,
		'sanitize_callback'	=> 'vw_hotel_sanitize_float'
	));
	$wp_customize->add_control('vw_hotel_related_posts_count',array(
		'label'	=> __('Add Related Post Count','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '3', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_related_posts_settings',
		'type'=> 'number'
	));

	// Single Posts Settings
	$wp_customize->add_section( 'vw_hotel_single_blog_settings', array(
		'title' => __( 'Single Post Settings', 'vw-hotel' ),
		'panel' => 'blog_post_parent_panel',
	));

	$wp_customize->add_setting( 'vw_hotel_toggle_tags',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
	));
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_toggle_tags', array(
		'label' => esc_html__( 'Tags','vw-hotel' ),
		'section' => 'vw_hotel_single_blog_settings'
    )));

	$wp_customize->add_setting( 'vw_hotel_single_blog_post_navigation_show_hide',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
	));
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_single_blog_post_navigation_show_hide', array(
		'label' => esc_html__( 'Post Navigation','vw-hotel' ),
		'section' => 'vw_hotel_single_blog_settings'
    )));

	//navigation text
	$wp_customize->add_setting('vw_hotel_single_blog_prev_navigation_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_single_blog_prev_navigation_text',array(
		'label'	=> __('Post Navigation Text','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( 'PREVIOUS', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_single_blog_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_single_blog_next_navigation_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_single_blog_next_navigation_text',array(
		'label'	=> __('Post Navigation Text','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( 'NEXT', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_single_blog_settings',
		'type'=> 'text'
	));

    //404 Page Setting
	$wp_customize->add_section('vw_hotel_404_page',array(
		'title'	=> __('404 Page Settings','vw-hotel'),
		'panel' => 'vw_hotel_panel_id',
	));	

	$wp_customize->add_setting('vw_hotel_404_page_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));

	$wp_customize->add_control('vw_hotel_404_page_title',array(
		'label'	=> __('Add Title','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '404 Not Found', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_404_page',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_404_page_content',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));

	$wp_customize->add_control('vw_hotel_404_page_content',array(
		'label'	=> __('Add Text','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( 'Looks like you have taken a wrong turn, Dont worry, it happens to the best of us.', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_404_page',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_404_page_button_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_404_page_button_text',array(
		'label'	=> __('Add Button Text','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( 'Return to the home page', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_404_page',
		'type'=> 'text'
	));

	//Social Icon Setting
	$wp_customize->add_section('vw_hotel_social_icon_settings',array(
		'title'	=> __('Social Icons Settings','vw-hotel'),
		'panel' => 'vw_hotel_panel_id',
	));	

	$wp_customize->add_setting('vw_hotel_social_icon_font_size',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_social_icon_font_size',array(
		'label'	=> __('Icon Font Size','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_social_icon_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_social_icon_padding',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_social_icon_padding',array(
		'label'	=> __('Icon Padding','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_social_icon_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_social_icon_width',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_social_icon_width',array(
		'label'	=> __('Icon Width','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_social_icon_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_social_icon_height',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_social_icon_height',array(
		'label'	=> __('Icon Height','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_social_icon_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'vw_hotel_social_icon_border_radius', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_social_icon_border_radius', array(
		'label'       => esc_html__( 'Icon Border Radius','vw-hotel' ),
		'section'     => 'vw_hotel_social_icon_settings',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	//Responsive Media Settings
	$wp_customize->add_section('vw_hotel_responsive_media',array(
		'title'	=> __('Responsive Media','vw-hotel'),
		'panel' => 'vw_hotel_panel_id',
	));

    $wp_customize->add_setting( 'vw_hotel_stickyheader_hide_show',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_stickyheader_hide_show',array(
      'label' => esc_html__( 'Sticky Header','vw-hotel' ),
      'section' => 'vw_hotel_responsive_media'
    )));

    $wp_customize->add_setting( 'vw_hotel_resp_slider_hide_show',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_resp_slider_hide_show',array(
      'label' => esc_html__( 'Show / Hide Slider','vw-hotel' ),
      'section' => 'vw_hotel_responsive_media'
    )));

    $wp_customize->add_setting( 'vw_hotel_sidebar_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_sidebar_hide_show',array(
      'label' => esc_html__( 'Show / Hide Sidebar','vw-hotel' ),
      'section' => 'vw_hotel_responsive_media'
    )));

    $wp_customize->add_setting( 'vw_hotel_resp_scroll_top_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_resp_scroll_top_hide_show',array(
      'label' => esc_html__( 'Show / Hide Scroll To Top','vw-hotel' ),
      'section' => 'vw_hotel_responsive_media'
    )));

    $wp_customize->add_setting('vw_hotel_res_open_menu_icon',array(
		'default'	=> 'fas fa-bars',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new VW_Hotel_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hotel_res_open_menu_icon',array(
		'label'	=> __('Add Open Menu Icon','vw-hotel'),
		'transport' => 'refresh',
		'section'	=> 'vw_hotel_responsive_media',
		'setting'	=> 'vw_hotel_res_open_menu_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('vw_hotel_res_close_menus_icon',array(
		'default'	=> 'fas fa-times',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new VW_Hotel_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hotel_res_close_menus_icon',array(
		'label'	=> __('Add Close Menu Icon','vw-hotel'),
		'transport' => 'refresh',
		'section'	=> 'vw_hotel_responsive_media',
		'setting'	=> 'vw_hotel_res_close_menus_icon',
		'type'		=> 'icon'
	)));

	//Footer Text
	$wp_customize->add_section('vw_hotel_footer',array(
		'title'	=> __('Footer','vw-hotel'),
		'description'=> __('This section will appear in the footer','vw-hotel'),
		'panel' => 'vw_hotel_panel_id',
	));	

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('vw_hotel_footer_text', array( 
		'selector' => '.copyright p', 
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_footer_text', 
	));
	
	$wp_customize->add_setting('vw_hotel_footer_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control('vw_hotel_footer_text',array(
		'label'	=> __('Copyright Text','vw-hotel'),
		'section'=> 'vw_hotel_footer',
		'setting'=> 'vw_hotel_footer_text',
		'type'=> 'text'
	));	

	$wp_customize->add_setting('vw_hotel_copyright_alingment',array(
        'default' => 'center',
        'sanitize_callback' => 'vw_hotel_sanitize_choices'
	));
	$wp_customize->add_control(new VW_Hotel_Image_Radio_Control($wp_customize, 'vw_hotel_copyright_alingment', array(
        'type' => 'select',
        'label' => __('Copyright Alignment','vw-hotel'),
        'section' => 'vw_hotel_footer',
        'settings' => 'vw_hotel_copyright_alingment',
        'choices' => array(
            'left' => esc_url(get_template_directory_uri()).'/images/copyright1.png',
            'center' => esc_url(get_template_directory_uri()).'/images/copyright2.png',
            'right' => esc_url(get_template_directory_uri()).'/images/copyright3.png'
    ))));

    $wp_customize->add_setting('vw_hotel_copyright_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_copyright_padding_top_bottom',array(
		'label'	=> __('Copyright Padding Top Bottom','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'vw_hotel_hide_show_scroll',array(
    	'default' => 1,
      	'transport' => 'refresh',
      	'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_hide_show_scroll',array(
      	'label' => esc_html__( 'Show / Hide Scroll To Top','vw-hotel' ),
      	'section' => 'vw_hotel_footer'
    )));

     //Selective Refresh
	$wp_customize->selective_refresh->add_partial('vw_hotel_scroll_top_icon', array( 
		'selector' => '.scrollup i', 
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_scroll_top_icon', 
	));

    $wp_customize->add_setting('vw_hotel_scroll_top_icon',array(
		'default'	=> 'fas fa-long-arrow-alt-up',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new VW_Hotel_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hotel_scroll_top_icon',array(
		'label'	=> __('Add Scroll to Top Icon','vw-hotel'),
		'transport' => 'refresh',
		'section'	=> 'vw_hotel_footer',
		'setting'	=> 'vw_hotel_scroll_top_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('vw_hotel_scroll_to_top_font_size',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_scroll_to_top_font_size',array(
		'label'	=> __('Icon Font Size','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_scroll_to_top_padding',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_scroll_to_top_padding',array(
		'label'	=> __('Icon Top Bottom Padding','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_scroll_to_top_width',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_scroll_to_top_width',array(
		'label'	=> __('Icon Width','vw-hotel'),
		'description'	=> __('Enter a value in pixels Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_scroll_to_top_height',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_scroll_to_top_height',array(
		'label'	=> __('Icon Height','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'vw_hotel_scroll_to_top_border_radius', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_scroll_to_top_border_radius', array(
		'label'       => esc_html__( 'Icon Border Radius','vw-hotel' ),
		'section'     => 'vw_hotel_footer',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	$wp_customize->add_setting('vw_hotel_scroll_top_alignment',array(
        'default' => 'Right',
        'sanitize_callback' => 'vw_hotel_sanitize_choices'
	));
	$wp_customize->add_control(new VW_Hotel_Image_Radio_Control($wp_customize, 'vw_hotel_scroll_top_alignment', array(
        'type' => 'select',
        'label' => __('Scroll To Top','vw-hotel'),
        'section' => 'vw_hotel_footer',
        'settings' => 'vw_hotel_scroll_top_alignment',
        'choices' => array(
            'Left' => esc_url(get_template_directory_uri()).'/images/layout1.png',
            'Center' => esc_url(get_template_directory_uri()).'/images/layout2.png',
            'Right' => esc_url(get_template_directory_uri()).'/images/layout3.png'
    ))));

    //Woocommerce settings
	$wp_customize->add_section('vw_hotel_woocommerce_section', array(
		'title'    => __('WooCommerce Layout', 'vw-hotel'),
		'priority' => null,
		'panel'    => 'woocommerce',
	));

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial( 'vw_hotel_woocommerce_shop_page_sidebar', array( 'selector' => '.post-type-archive-product .sidebar', 
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_woocommerce_shop_page_sidebar', ) );

	//Woocommerce Shop Page Sidebar
	$wp_customize->add_setting( 'vw_hotel_woocommerce_shop_page_sidebar',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_woocommerce_shop_page_sidebar',array(
		'label' => esc_html__( 'Shop Page Sidebar','vw-hotel' ),
		'section' => 'vw_hotel_woocommerce_section'
    )));

     //Selective Refresh
	$wp_customize->selective_refresh->add_partial( 'vw_hotel_woocommerce_single_product_page_sidebar', array( 'selector' => '.single-product .sidebar', 
		'render_callback' => 'vw_hotel_customize_partial_vw_hotel_woocommerce_single_product_page_sidebar', ) );

    //Woocommerce Single Product page Sidebar
	$wp_customize->add_setting( 'vw_hotel_woocommerce_single_product_page_sidebar',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hotel_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hotel_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hotel_woocommerce_single_product_page_sidebar',array(
		'label' => esc_html__( 'Single Product Sidebar','vw-hotel' ),
		'section' => 'vw_hotel_woocommerce_section'
    )));

    //Products per page
    $wp_customize->add_setting('vw_hotel_products_per_page',array(
		'default'=> 9,
		'sanitize_callback'	=> 'vw_hotel_sanitize_float'
	));
	$wp_customize->add_control('vw_hotel_products_per_page',array(
		'label'	=> __('Products Per Page','vw-hotel'),
		'description' => __('Display on shop page','vw-hotel'),
		'input_attrs' => array(
            'step'             => 1,
			'min'              => 0,
			'max'              => 50,
        ),
		'section'=> 'vw_hotel_woocommerce_section',
		'type'=> 'number',
	));

    //Products per row
    $wp_customize->add_setting('vw_hotel_products_per_row',array(
		'default'=> 3,
		'sanitize_callback'	=> 'vw_hotel_sanitize_choices'
	));
	$wp_customize->add_control('vw_hotel_products_per_row',array(
		'label'	=> __('Products Per Row','vw-hotel'),
		'description' => __('Display on shop page','vw-hotel'),
		'choices' => array(
            2 => 2,
			3 => 3,
			4 => 4,
        ),
		'section'=> 'vw_hotel_woocommerce_section',
		'type'=> 'select',
	));

	//Products padding
	$wp_customize->add_setting('vw_hotel_products_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_products_padding_top_bottom',array(
		'label'	=> __('Products Padding Top Bottom','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_woocommerce_section',
		'type'=> 'text'
	));

	$wp_customize->add_setting('vw_hotel_products_padding_left_right',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hotel_products_padding_left_right',array(
		'label'	=> __('Products Padding Left Right','vw-hotel'),
		'description'	=> __('Enter a value in pixels. Example:20px','vw-hotel'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hotel' ),
        ),
		'section'=> 'vw_hotel_woocommerce_section',
		'type'=> 'text'
	));

	//Products box shadow
	$wp_customize->add_setting( 'vw_hotel_products_box_shadow', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_products_box_shadow', array(
		'label'       => esc_html__( 'Products Box Shadow','vw-hotel' ),
		'section'     => 'vw_hotel_woocommerce_section',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	//Products border radius
    $wp_customize->add_setting( 'vw_hotel_products_border_radius', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'vw_hotel_sanitize_number_range'
	) );
	$wp_customize->add_control( 'vw_hotel_products_border_radius', array(
		'label'       => esc_html__( 'Products Border Radius','vw-hotel' ),
		'section'     => 'vw_hotel_woocommerce_section',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

    // Has to be at the top
	$wp_customize->register_panel_type( 'VW_Hotel_WP_Customize_Panel' );
	$wp_customize->register_section_type( 'VW_Hotel_WP_Customize_Section' );
}

add_action( 'customize_register', 'vw_hotel_customize_register' );

load_template( trailingslashit( get_template_directory() ) . '/inc/logo/logo-resizer.php' );

if ( class_exists( 'WP_Customize_Panel' ) ) {
  	class VW_Hotel_WP_Customize_Panel extends WP_Customize_Panel {
	    public $panel;
	    public $type = 'vw_hotel_panel';
	    public function json() {

	      $array = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'type', 'panel', ) );
	      $array['title'] = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
	      $array['content'] = $this->get_content();
	      $array['active'] = $this->active();
	      $array['instanceNumber'] = $this->instance_number;
	      return $array;
    	}
  	}
}

if ( class_exists( 'WP_Customize_Section' ) ) {
  	class VW_Hotel_WP_Customize_Section extends WP_Customize_Section {
	    public $section;
	    public $type = 'vw_hotel_section';
	    public function json() {

	      $array = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'panel', 'type', 'description_hidden', 'section', ) );
	      $array['title'] = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
	      $array['content'] = $this->get_content();
	      $array['active'] = $this->active();
	      $array['instanceNumber'] = $this->instance_number;

	      if ( $this->panel ) {
	        $array['customizeAction'] = sprintf( 'Customizing &#9656; %s', esc_html( $this->manager->get_panel( $this->panel )->title ) );
	      } else {
	        $array['customizeAction'] = 'Customizing';
	      }
	      return $array;
    	}
  	}
}

// Enqueue our scripts and styles
function vw_hotel_customize_controls_scripts() {
  wp_enqueue_script( 'customizer-controls', get_theme_file_uri( '/js/customizer-controls.js' ), array(), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'vw_hotel_customize_controls_scripts' );

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class VW_Hotel_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'VW_Hotel_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(new VW_Hotel_Customize_Section_Pro($manager,'vw_hotel_upgrade_pro_link',array(
			'priority'   => 1,
			'title'    => esc_html__( 'Hotel Pro Theme', 'vw-hotel' ),
			'pro_text' => esc_html__( 'Upgrade Pro', 'vw-hotel' ),
			'pro_url'  => esc_url('https://www.vwthemes.com/themes/wordpress-hotel-theme/'),
		)));

		$manager->add_section(new VW_Hotel_Customize_Section_Pro($manager,'vw_hotel_get_started_link',array(
			'priority'   => 1,
			'title'    => esc_html__( 'Documentation', 'vw-hotel' ),
			'pro_text' => esc_html__( 'Docs', 'vw-hotel' ),
			'pro_url'  => admin_url('themes.php?page=vw_hotel_guide'),
		)));
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'vw-hotel-customize-controls', trailingslashit( esc_url(get_template_directory_uri()) ) . '/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'vw-hotel-customize-controls', trailingslashit( esc_url(get_template_directory_uri()) ) . '/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
VW_Hotel_Customize::get_instance();