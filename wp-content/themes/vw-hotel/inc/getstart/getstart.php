<?php
//about theme info
add_action( 'admin_menu', 'vw_hotel_gettingstarted' );
function vw_hotel_gettingstarted() {    	
	add_theme_page( esc_html__('About VW Hotel', 'vw-hotel'), esc_html__('About VW Hotel', 'vw-hotel'), 'edit_theme_options', 'vw_hotel_guide', 'vw_hotel_mostrar_guide');   
}

// Add a Custom CSS file to WP Admin Area
function vw_hotel_admin_theme_style() {
   wp_enqueue_style('vw-hotel-custom-admin-style', esc_url(get_template_directory_uri()) . '/inc/getstart/getstart.css');
   wp_enqueue_script('vw-hotel-tabs', esc_url(get_template_directory_uri()) . '/inc/getstart/js/tab.js');
   wp_enqueue_style( 'font-awesome-css', esc_url(get_template_directory_uri()).'/css/fontawesome-all.css' );
}
add_action('admin_enqueue_scripts', 'vw_hotel_admin_theme_style');

//guidline for about theme
function vw_hotel_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( 'vw-hotel' );
?>

<div class="wrapper-info">
    <div class="col-left">
    	<h2><?php esc_html_e( 'Welcome to VW Hotel Theme', 'vw-hotel' ); ?> <span class="version">Version: <?php echo esc_html($theme['Version']);?></span></h2>
    	<p><?php esc_html_e('All our WordPress themes are modern, minimalist, 100% responsive, seo-friendly,feature-rich, and multipurpose that best suit designers, bloggers and other professionals who are working in the creative fields.','vw-hotel'); ?></p>
    </div>
    <div class="col-right">
    	<div class="logo">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/final-logo.png" alt="" />
		</div>
		<div class="update-now">
			<h4><?php esc_html_e('Buy VW Hotel at 20% Discount','vw-hotel'); ?></h4>
			<h4><?php esc_html_e('Use Coupon','vw-hotel'); ?> ( <span><?php esc_html_e('vwpro20','vw-hotel'); ?></span> ) </h4> 
			<div class="info-link">
				<a href="<?php echo esc_url( VW_HOTEL_BUY_NOW ); ?>" target="_blank"> <?php esc_html_e( 'Upgrade to Pro', 'vw-hotel' ); ?></a>
			</div>
		</div>
    </div>

    <div class="tab-sec">
		<div class="tab">
			<button class="tablinks" onclick="vw_hotel_open_tab(event, 'lite_theme')"><?php esc_html_e( 'Setup With Customizer', 'vw-hotel' ); ?></button>
			<button class="tablinks" onclick="vw_hotel_open_tab(event, 'block_pattern')"><?php esc_html_e( 'Setup With Block Pattern', 'vw-hotel' ); ?></button>
			<button class="tablinks" onclick="vw_hotel_open_tab(event, 'gutenberg_editor')"><?php esc_html_e( 'Setup With Gutunberg Block', 'vw-hotel' ); ?></button>	
			<button class="tablinks" onclick="vw_hotel_open_tab(event, 'product_addons_editor')"><?php esc_html_e( 'Woocommerce Product Addons', 'vw-hotel' ); ?></button>
		  	<button class="tablinks" onclick="vw_hotel_open_tab(event, 'hotel_pro')"><?php esc_html_e( 'Get Premium', 'vw-hotel' ); ?></button>
		  	<button class="tablinks" onclick="vw_hotel_open_tab(event, 'free_pro')"><?php esc_html_e( 'Support', 'vw-hotel' ); ?></button>
		</div>

		<?php
			$vw_hotel_plugin_custom_css = '';
			if(class_exists('Ibtana_Visual_Editor_Menu_Class')){
				$vw_hotel_plugin_custom_css ='display: block';
			}
		?>
		<div id="lite_theme" class="tabcontent open">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
				$plugin_ins = VW_Hotel_Plugin_Activation_Settings::get_instance();
				$vw_hotel_actions = $plugin_ins->recommended_actions;
				?>
				<div class="vw-hotel-recommended-plugins">
				    <div class="vw-hotel-action-list">
				        <?php if ($vw_hotel_actions): foreach ($vw_hotel_actions as $key => $vw_hotel_actionValue): ?>
				                <div class="vw-hotel-action" id="<?php echo esc_attr($vw_hotel_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($vw_hotel_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($vw_hotel_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($vw_hotel_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" get-start-tab-id="lite-theme-tab" href="javascript:void(0);"><?php esc_html_e('Skip','vw-hotel'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="lite-theme-tab" style="<?php echo esc_attr($vw_hotel_plugin_custom_css); ?>">
				<h3><?php esc_html_e( 'Lite Theme Information', 'vw-hotel' ); ?></h3>
				<hr class="h3hr">
			  	<p><?php esc_html_e(' VW Hotel is a refreshing, attractive and modern WordPress theme for hotel, restaurant, eatery, food joint, bakery, barbeque and grill house, cafe and similar food businesses. It can serve itself for resorts, holiday homes, accommodations, guest houses, lodges and hospitality business as well. A peppy design full of complimenting colors and fonts is all it has to build up a great hotel theme. The theme is undoubtedly responsive and cross-browser compatible to look beautiful on mobiles, tablets, iPads, desktops and across all browsers. Customization is offered to change each and every part of the theme according to your will. It has multiple slides that can be used in banners and other places to display amazing offers and delicacies to leverage people into opting your services. The theme is SEO-friendly to dominate the search results. It is light-weight and hence loads fast. It is built on Bootstrap framework to ease the process of using it for developers and novice user. It uses social media icons to get maximum user attention. You can display your most popular dishes, other exclusive services and hotel ambience through gallery. We have made provision to share some cooking and recipe tips in blogs. It has a testimonial section where users can share their valuable feedback.','vw-hotel'); ?></p>
			  	<div class="col-left-inner">
			  		<h4><?php esc_html_e( 'Theme Documentation', 'vw-hotel' ); ?></h4>
					<p><?php esc_html_e( 'If you need any assistance regarding setting up and configuring the Theme, our documentation is there.', 'vw-hotel' ); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_HOTEL_FREE_THEME_DOC ); ?>" target="_blank"> <?php esc_html_e( 'Documentation', 'vw-hotel' ); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Theme Customizer', 'vw-hotel'); ?></h4>
					<p> <?php esc_html_e('To begin customizing your website, start by clicking "Customize".', 'vw-hotel'); ?></p>
					<div class="info-link">
						<a target="_blank" href="<?php echo esc_url( admin_url('customize.php') ); ?>"><?php esc_html_e('Customizing', 'vw-hotel'); ?></a>
					</div>
					<hr>				
					<h4><?php esc_html_e('Having Trouble, Need Support?', 'vw-hotel'); ?></h4>
					<p> <?php esc_html_e('Our dedicated team is well prepared to help you out in case of queries and doubts regarding our theme.', 'vw-hotel'); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_HOTEL_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support Forum', 'vw-hotel'); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Reviews & Testimonials', 'vw-hotel'); ?></h4>
					<p> <?php esc_html_e('All the features and aspects of this WordPress Theme are phenomenal. I\'d recommend this theme to all.', 'vw-hotel'); ?>  </p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_HOTEL_REVIEW ); ?>" target="_blank"><?php esc_html_e('Reviews', 'vw-hotel'); ?></a>
					</div>
			  		<div class="link-customizer">
						<h3><?php esc_html_e( 'Link to customizer', 'vw-hotel' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','vw-hotel'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-slides"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_slidersettings') ); ?>" target="_blank"><?php esc_html_e('Slider Settings','vw-hotel'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-editor-table"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_aboutus_section') ); ?>" target="_blank"><?php esc_html_e('About Us Section','vw-hotel'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-admin-customizer"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=vw_hotel_typography') ); ?>" target="_blank"><?php esc_html_e('Typography','vw-hotel'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','vw-hotel'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','vw-hotel'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','vw-hotel'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','vw-hotel'); ?></a>
								</div> 
							</div>
							
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','vw-hotel'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','vw-hotel'); ?></a>
								</div>
							</div>
						</div>
					</div>
			  	</div>
				<div class="col-right-inner">
					<h3 class="page-template"><?php esc_html_e('How to set up Home Page Template','vw-hotel'); ?></h3>
				  	<hr class="h3hr">
					<p><?php esc_html_e('Follow these instructions to setup Home page.','vw-hotel'); ?></p>
	                <ul>
	                  	<p><span class="strong"><?php esc_html_e('1. Create a new page :','vw-hotel'); ?></span><?php esc_html_e(' Go to ','vw-hotel'); ?>
					  	<b><?php esc_html_e(' Dashboard >> Pages >> Add New Page','vw-hotel'); ?></b></p>

	                  	<p><?php esc_html_e('Name it as "Home" then select the template "Custom Home Page".','vw-hotel'); ?></p>
	                  	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/home-page-template.png" alt="" />
	                  	<p><span class="strong"><?php esc_html_e('2. Set the front page:','vw-hotel'); ?></span><?php esc_html_e(' Go to ','vw-hotel'); ?>
					  	<b><?php esc_html_e(' Settings >> Reading ','vw-hotel'); ?></b></p>
					  	<p><?php esc_html_e('Select the option of Static Page, now select the page you created to be the homepage, while another page to be your default page.','vw-hotel'); ?></p>
	                  	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/set-front-page.png" alt="" />
	                  	<p><?php esc_html_e(' Once you are done with this, then follow the','vw-hotel'); ?> <a class="doc-links" href="https://vwthemesdemo.com/docs/free-vw-hotel/" target="_blank"><?php esc_html_e('Documentation','vw-hotel'); ?></a></p>
	                </ul>
			  	</div>
			</div>
		</div>

		<div id="block_pattern" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = VW_Hotel_Plugin_Activation_Settings::get_instance();
			$vw_hotel_actions = $plugin_ins->recommended_actions;
			?>
				<div class="vw-hotel-recommended-plugins">
				    <div class="vw-hotel-action-list">
				        <?php if ($vw_hotel_actions): foreach ($vw_hotel_actions as $key => $vw_hotel_actionValue): ?>
				                <div class="vw-hotel-action" id="<?php echo esc_attr($vw_hotel_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($vw_hotel_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($vw_hotel_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($vw_hotel_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" href="javascript:void(0);" get-start-tab-id="gutenberg-editor-tab"><?php esc_html_e('Skip','vw-hotel'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="gutenberg-editor-tab" style="<?php echo esc_attr($vw_hotel_plugin_custom_css); ?>">
				<div class="block-pattern-img">
				  	<h3><?php esc_html_e( 'Block Patterns', 'vw-hotel' ); ?></h3>
					<hr class="h3hr">
					<p><?php esc_html_e('Follow the below instructions to setup Home page with Block Patterns.','vw-hotel'); ?></p>
	              	<p><b><?php esc_html_e('Click on Below Add new page button >> Click on "+" Icon >> Click Pattern Tab >> Click on homepage sections >> Publish.','vw-hotel'); ?></span></b></p>
	              	<div class="vw-hotel-pattern-page">
				    	<a href="javascript:void(0)" class="vw-pattern-page-btn button-primary button"><?php esc_html_e('Add New Page','vw-hotel'); ?></a>
				    </div>
	              	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/block-pattern.png" alt="" />	
	            </div>

	            <div class="block-pattern-link-customizer">
	              	<div class="link-customizer-with-block-pattern">
							<h3><?php esc_html_e( 'Link to customizer', 'vw-hotel' ); ?></h3>
							<hr class="h3hr">
							<div class="first-row">
								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','vw-hotel'); ?></a>
									</div>
									<div class="row-box2">
										<span class="dashicons dashicons-networking"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_social_icon_settings') ); ?>" target="_blank"><?php esc_html_e('Social Icons','vw-hotel'); ?></a>
									</div>
								</div>
								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','vw-hotel'); ?></a>
									</div>
									
									<div class="row-box2">
										<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','vw-hotel'); ?></a>
									</div>
								</div>

								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','vw-hotel'); ?></a>
									</div>
									 <div class="row-box2">
										<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','vw-hotel'); ?></a>
									</div> 
								</div>
								
								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','vw-hotel'); ?></a>
									</div>
									 <div class="row-box2">
										<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','vw-hotel'); ?></a>
									</div> 
								</div>
							</div>
					</div>	
				</div>			
	        </div>
		</div>

		<div id="gutenberg_editor" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = VW_Hotel_Plugin_Activation_Settings::get_instance();
			$vw_hotel_actions = $plugin_ins->recommended_actions;
			?>
				<div class="vw-hotel-recommended-plugins">
				    <div class="vw-hotel-action-list">
				        <?php if ($vw_hotel_actions): foreach ($vw_hotel_actions as $key => $vw_hotel_actionValue): ?>
				                <div class="vw-hotel-action" id="<?php echo esc_attr($vw_hotel_actionValue['id']);?>">
			                        <div class="action-inner plugin-activation-redirect">
			                            <h3 class="action-title"><?php echo esc_html($vw_hotel_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($vw_hotel_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($vw_hotel_actionValue['link']); ?>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php }else{ ?>
				<h3><?php esc_html_e( 'Gutunberg Blocks', 'vw-hotel' ); ?></h3>
				<hr class="h3hr">
				<div class="vw-hotel-pattern-page">
			    	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-templates' ) ); ?>" class="vw-pattern-page-btn ibtana-dashboard-page-btn button-primary button"><?php esc_html_e('Ibtana Settings','vw-hotel'); ?></a>
			    </div>

			    <div class="link-customizer-with-guternberg-ibtana">
					<h3><?php esc_html_e( 'Link to customizer', 'vw-hotel' ); ?></h3>
					<hr class="h3hr">
					<div class="first-row">
						<div class="row-box">
							<div class="row-box1">
								<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','vw-hotel'); ?></a>
							</div>
							<div class="row-box2">
								<span class="dashicons dashicons-networking"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_social_icon_settings') ); ?>" target="_blank"><?php esc_html_e('Social Icons','vw-hotel'); ?></a>
							</div>
						</div>
						<div class="row-box">
							<div class="row-box1">
								<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','vw-hotel'); ?></a>
							</div>
							
							<div class="row-box2">
								<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','vw-hotel'); ?></a>
							</div>
						</div>

						<div class="row-box">
							<div class="row-box1">
								<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','vw-hotel'); ?></a>
							</div>
							 <div class="row-box2">
								<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','vw-hotel'); ?></a>
							</div> 
						</div>
						
						<div class="row-box">
							<div class="row-box1">
								<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hotel_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','vw-hotel'); ?></a>
							</div>
							 <div class="row-box2">
								<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','vw-hotel'); ?></a>
							</div> 
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<div id="product_addons_editor" class="tabcontent product_woo_tab">
			<?php if(!is_plugin_active('ibtana-ecommerce-product-addons/plugin.php')){ 
				$plugin_ins = VW_Hotel_Plugin_Activation_Woo_Products::get_instance();
				$vw_hotel_actions = $plugin_ins->recommended_actions;
				?>
				<div class="vw-hotel-recommended-plugins">
			    	<div class="vw-hotel-action-list">
                	<div class="vw-hotel-action" id="<?php echo esc_attr($vw_hotel_actions['id']);?>">
                     <div class="action-inner plugin-activation-redirect">
                        <h3 class="action-title"><?php echo esc_html($vw_hotel_actions['title']); ?></h3>
                        <div class="action-desc"><?php echo esc_html($vw_hotel_actions['desc']); ?>
                        </div>
                        <?php echo wp_kses_post($vw_hotel_actions['link']); ?>
                     </div>
                	</div>
			    	</div>
				</div>
			<?php }else{ ?>
				<h3><?php esc_html_e( 'Woocommerce Products Blocks', 'vw-hotel' ); ?></h3>
				<hr class="h3hr">
              	<div class="vw-hotel-pattern-page">
			    	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-templates&woo=true' ) ); ?>" class="vw-pattern-page-btn ibtana-dashboard-page-btn button-primary button"><?php esc_html_e('Woocommerce Templates','vw-hotel'); ?></a>
			    </div>
			<?php } ?>
		</div>	

		<div id="hotel_pro" class="tabcontent">
		  	<h3><?php esc_html_e( 'Premium Theme Information', 'vw-hotel' ); ?></h3>
			<hr class="h3hr">
		    <div class="col-left-pro">
		    	<p><?php esc_html_e('This premium WordPress hotel theme is inviting, eye-catching and modern with an appeal to the visitors. The multipurpose theme can be used for variety of food businesses like hotels, restaurants, barbeques, grill houses, cafes, bakery, food joints and other eateries. It can cater websites of lodges, holiday homes, guest houses, inn and other room reservation services. The premium theme offers numerous features and functionality to craft out a highly efficient site for your business. It is made clean and user-friendly. It maintains WordPress standards of coding for a bug-free site. With customization allowed on an array of elements, the theme can be tweaked to get a great design. Another way of changing the feel and look of the theme is by trying combinations of colour options and Google fonts. Banners and sliders are used to enhance the look of your site. With regular theme updates and prompt customer support, it becomes all the more handy to use','vw-hotel'); ?></p>
		    	<div class="pro-links">
			    	<a href="<?php echo esc_url( VW_HOTEL_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'vw-hotel'); ?></a>
					<a href="<?php echo esc_url( VW_HOTEL_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Buy Pro', 'vw-hotel'); ?></a>
					<a href="<?php echo esc_url( VW_HOTEL_PRO_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Documentation', 'vw-hotel'); ?></a>
				</div>
		    </div>
		    <div class="col-right-pro">
		    	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/responsive.png" alt="" />
		    </div>
		    <div class="featurebox">
			    <h3><?php esc_html_e( 'Theme Features', 'vw-hotel' ); ?></h3>
				<hr class="h3hr">
				<div class="table-image">
					<table class="tablebox">
						<thead>
							<tr>
								<th></th>
								<th><?php esc_html_e('Free Themes', 'vw-hotel'); ?></th>
								<th><?php esc_html_e('Premium Themes', 'vw-hotel'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e('Theme Customization', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Responsive Design', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Logo Upload', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Social Media Links', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Slider Settings', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Number of Slides', 'vw-hotel'); ?></td>
								<td class="table-img"><?php esc_html_e('4', 'vw-hotel'); ?></td>
								<td class="table-img"><?php esc_html_e('Unlimited', 'vw-hotel'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Template Pages', 'vw-hotel'); ?></td>
								<td class="table-img"><?php esc_html_e('3', 'vw-hotel'); ?></td>
								<td class="table-img"><?php esc_html_e('6', 'vw-hotel'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Home Page Template', 'vw-hotel'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'vw-hotel'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'vw-hotel'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Theme sections', 'vw-hotel'); ?></td>
								<td class="table-img"><?php esc_html_e('2', 'vw-hotel'); ?></td>
								<td class="table-img"><?php esc_html_e('12', 'vw-hotel'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Contact us Page Template', 'vw-hotel'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('1', 'vw-hotel'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Blog Templates & Layout', 'vw-hotel'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('3(Full width/Left/Right Sidebar)', 'vw-hotel'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Page Templates & Layout', 'vw-hotel'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('2(Left/Right Sidebar)', 'vw-hotel'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Color Pallete For Particular Sections', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Global Color Option', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Reordering', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Demo Importer', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Allow To Set Site Title, Tagline, Logo', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Enable Disable Options On All Sections, Logo', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Full Documentation', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Latest WordPress Compatibility', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Woo-Commerce Compatibility', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Support 3rd Party Plugins', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Secure and Optimized Code', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Exclusive Functionalities', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Enable / Disable', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Section Google Font Choices', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Gallery', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Simple & Mega Menu Option', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Support to add custom CSS / JS ', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Shortcodes', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Background, Colors, Header, Logo & Menu', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Premium Membership', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Budget Friendly Value', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Priority Error Fixing', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Feature Addition', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('All Access Theme Pass', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Seamless Customer Support', 'vw-hotel'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td></td>
								<td class="table-img"></td>
								<td class="update-link"><a href="<?php echo esc_url( VW_HOTEL_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Upgrade to Pro', 'vw-hotel'); ?></a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="free_pro" class="tabcontent">
		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-star-filled"></span><?php esc_html_e('Pro Version', 'vw-hotel'); ?></h4>
				<p> <?php esc_html_e('To gain access to extra theme options and more interesting features, upgrade to pro version.', 'vw-hotel'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_HOTEL_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Get Pro', 'vw-hotel'); ?></a>
				</div>
		  	</div>
		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-cart"></span><?php esc_html_e('Pre-purchase Queries', 'vw-hotel'); ?></h4>
				<p> <?php esc_html_e('If you have any pre-sale query, we are prepared to resolve it.', 'vw-hotel'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_HOTEL_CONTACT ); ?>" target="_blank"><?php esc_html_e('Question', 'vw-hotel'); ?></a>
				</div>
		  	</div>
		  	<div class="col-3">		  		
		  		<h4><span class="dashicons dashicons-admin-customizer"></span><?php esc_html_e('Child Theme', 'vw-hotel'); ?></h4>
				<p> <?php esc_html_e('For theme file customizations, make modifications in the child theme and not in the main theme file.', 'vw-hotel'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_HOTEL_CHILD_THEME ); ?>" target="_blank"><?php esc_html_e('About Child Theme', 'vw-hotel'); ?></a>
				</div>
		  	</div>

		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e('Frequently Asked Questions', 'vw-hotel'); ?></h4>
				<p> <?php esc_html_e('We have gathered top most, frequently asked questions and answered them for your easy understanding. We will list down more as we get new challenging queries. Check back often.', 'vw-hotel'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_HOTEL_FAQ ); ?>" target="_blank"><?php esc_html_e('View FAQ','vw-hotel'); ?></a>
				</div>
		  	</div>

		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-sos"></span><?php esc_html_e('Support Queries', 'vw-hotel'); ?></h4>
				<p> <?php esc_html_e('If you have any queries after purchase, you can contact us. We are eveready to help you out.', 'vw-hotel'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( VW_HOTEL_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Contact Us', 'vw-hotel'); ?></a>
				</div>
		  	</div>
		</div>
	</div>
</div>
<?php } ?>