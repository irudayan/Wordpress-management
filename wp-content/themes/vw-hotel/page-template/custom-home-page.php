<?php
/**
 * Template Name: Custom Home
 */

get_header(); ?>

<main id="maincontent" role="main">
  <?php do_action( 'vw_hotel_before_slider' ); ?>

  <?php if( get_theme_mod( 'vw_hotel_slider_hide_show', false) != '' || get_theme_mod( 'vw_hotel_resp_slider_hide_show', false) != '') { ?>
    <section id="slider">
      <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="<?php echo esc_attr(get_theme_mod( 'vw_hotel_slider_speed',4000)) ?>">
        <?php $vw_hotel_slider_page = array();
          for ( $count = 1; $count <= 4; $count++ ) {
            $mod = intval( get_theme_mod( 'vw_hotel_slider_page' . $count ));
            if ( 'page-none-selected' != $mod ) {
              $vw_hotel_slider_page[] = $mod;
            }
          }
          if( !empty($vw_hotel_slider_page) ) :
            $args = array(
              'post_type' => 'page',
              'post__in' => $vw_hotel_slider_page,
              'orderby' => 'post__in'
            );
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) :
              $i = 1;
        ?>     
        <div class="carousel-inner" role="listbox">
          <?php  while ( $query->have_posts() ) : $query->the_post(); ?>
            <div <?php if($i == 1){echo 'class="carousel-item active"';} else{ echo 'class="carousel-item"';}?>>
              <?php the_post_thumbnail(); ?>
              <div class="carousel-caption">
                <div class="inner_carousel">
                  <h1><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                  <p><?php $excerpt = get_the_excerpt(); echo esc_html( vw_hotel_string_limit_words( $excerpt, esc_attr(get_theme_mod('vw_hotel_slider_excerpt_number','30')))); ?></p>
                  <?php if( get_theme_mod('vw_hotel_slider_button_text','READ MORE') != ''){ ?>
                    <div class="more-btn">              
                      <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_theme_mod('vw_hotel_slider_button_text',__('READ MORE','vw-hotel')));?><span class="screen-reader-text"><?php echo esc_html(get_theme_mod('vw_hotel_slider_button_text',__('READ MORE','vw-hotel')));?></span></a>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          <?php $i++; endwhile; 
          wp_reset_postdata();?>
        </div>
        <?php else : ?>
            <div class="no-postfound"></div>
        <?php endif;
        endif;?>
        <a class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev" role="button">
          <span class="carousel-control-prev-icon w-auto h-auto" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
          <span class="screen-reader-text"><?php esc_html_e( 'Previous','vw-hotel' );?></span>
        </a>
        <a class="carousel-control-next" data-bs-target="#carouselExampleCaptions" data-bs-slide="next" role="button">
          <span class="carousel-control-next-icon w-auto h-auto" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
          <span class="screen-reader-text"><?php esc_html_e( 'Next','vw-hotel' );?></span>
        </a>
      </div>  
      <div class="clearfix"></div>
    </section> 
  <?php }?>

  <?php do_action( 'vw_hotel_after_slider' ); ?>

  <?php if( get_theme_mod('vw_hotel_section_title') != '' || get_theme_mod('vw_hotel_service_category') != '' || get_theme_mod('vw_hotel_offer_image') != ''){ ?>
    <section id="about-hotel">
      <div class="container">
        <div class="about-mainbox">
          <div class="row">
            <div class="col-lg-8 col-md-8">
              <?php if( get_theme_mod('vw_hotel_section_title') != ''){ ?>
                <h2><?php echo esc_html(get_theme_mod('vw_hotel_section_title','')); ?></h2>
              <?php }?>
              <?php $vw_hotel_about_page = array();
                for ( $count = 1; $count <= 1; $count++ ) {
                  $mod = intval( get_theme_mod( 'vw_hotel_about_section' . $count ));
                  if ( 'page-none-selected' != $mod ) {
                    $vw_hotel_about_page[] = $mod;
                  }
                }
                if( !empty($vw_hotel_about_page) ) :
                  $args = array(
                    'post_type' => 'page',
                    'post__in' => $vw_hotel_about_page,
                    'orderby' => 'post__in'
                  );
                  $query = new WP_Query( $args );
                  if ( $query->have_posts() ) :
                    $count = 1;
                    while ( $query->have_posts() ) : $query->the_post(); ?>
                      <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="screen-reader-text"><?php the_title(); ?></span></a></h3>
                      <hr class="hrclass">
                      <p><?php $excerpt = get_the_excerpt(); echo esc_html( vw_hotel_string_limit_words( $excerpt, esc_attr(get_theme_mod('vw_hotel_about_excerpt_number','30')))); ?></p>
                    <?php $count++; endwhile; 
                    wp_reset_postdata();?>
                  <?php else : ?>
                    <div class="no-postfound"></div>
                <?php endif;
              endif;?>
              <div class="about-category">       
                <div class="row">
                  <?php 
                  $catData1=  get_theme_mod('vw_hotel_service_category');
                  if($catData1){

                    $page_query = new WP_Query(array(  'category_name' => esc_html($catData1 ,'vw-hotel')));?>
                    <?php while( $page_query->have_posts() ) : $page_query->the_post(); ?>
                        <div class="col-lg-6 col-md-6">
                          <?php if(has_post_thumbnail()) { ?><?php the_post_thumbnail(); ?><?php } ?>
                          <div class="overlay-box">
                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="screen-reader-text"><?php the_title(); ?></span></a></h4>
                          </div>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata();
                    }
                  ?>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-4">
              <?php
              $vw_hotel_postData1=  get_theme_mod('vw_hotel_offer_image');
              if($vw_hotel_postData1){
                $args = array( 'name' => esc_html($vw_hotel_postData1 ,'vw-hotel'));
                $query = new WP_Query( $args );
                if ( $query->have_posts() ) :
                  while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="imagebox">
                      <?php the_post_thumbnail(); ?>
                      <?php if( get_theme_mod('vw_hotel_about_button_text','LEARN MORE') != ''){ ?>
                        <div class="overlay-bttn">
                          <a href="<?php echo esc_url( get_permalink() );?>"><?php echo esc_html(get_theme_mod('vw_hotel_about_button_text',__('LEARN MORE','vw-hotel')));?><span class="screen-reader-text"><?php echo esc_html(get_theme_mod('vw_hotel_about_button_text',__('LEARN MORE','vw-hotel')));?></span></a>
                        </div>
                      <?php } ?>
                    </div>
                  <?php endwhile; 
                  wp_reset_postdata();?>
                  <?php else : ?>
                    <div class="no-postfound"></div>
                  <?php
              endif; }?>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php }?>

  <?php do_action( 'vw_hotel_after_about' ); ?>

  <div class="content-vw">
    <div class="container">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
      <?php endwhile; // end of the loop. ?>
    </div>
  </div>
</main>

<?php get_footer(); ?>