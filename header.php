<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content-main">
 *
 * @package WordPress
 * @subpackage Graphene
 * @since graphene 1.0
 */
global $graphene_settings;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title><?php graphene_title(); ?></title>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" /> 
    <!--[if lte IE 6]>
        <style>#container{background:none !important;}</style>
        <script>
        sfHover = function() {
            var sfEls = document.getElementById("menu").getElementsByTagName("LI");
            for (var i=0; i<sfEls.length; i++) {
                sfEls[i].onmouseover=function() {
                    this.className+=" sfhover";
                }
                sfEls[i].onmouseout=function() {
                    this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
                }
            }
        }
        if (window.attachEvent) window.attachEvent("onload", sfHover);
              </script>
    <![endif]-->
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <?php
    /* We add some JavaScript to pages with the comment form
     * to support sites with threaded comments (when in use).
     */
    if (is_singular() && get_option('thread_comments'))
        wp_enqueue_script('comment-reply');

    /* Always have wp_head() just before the closing </head>
     * tag of your theme, or you will break many plugins, which
     * generally use this hook to add elements to <head> such
     * as styles, scripts, and meta tags.
     */
    wp_head();
    ?>
</head><?php flush(); ?>
<body <?php body_class(); ?>>

<?php if (!get_theme_mod('background_image', false) && !get_theme_mod('background_color', false)) : ?>
<div class="bg-gradient">
<?php endif; ?>

<div id="container">
    
    <?php if ($graphene_settings['hide_top_bar'] != true) : ?>
        <div id="top-bar">

            <?php if ($graphene_settings['hide_feed_icon'] != true) : ?>
                <div id="rss">
                    <?php $custom_feed_url = ($graphene_settings['custom_feed_url']) ? $graphene_settings['custom_feed_url'] : get_bloginfo('rss2_url'); ?>
                    <a href="<?php echo $custom_feed_url; ?>" title="<?php esc_attr_e('Subscribe to RSS feed', 'graphene'); ?>" class="rss_link"><span><?php _e('Subscribe to RSS feed', 'graphene'); ?></span></a>
                    <?php do_action('graphene_feed_icon'); ?>
                </div>
            <?php endif; ?>

            <?php
            /**
             * Retrieves our custom search form.
             */
            ?>
            <?php if (($search_box_location = $graphene_settings['search_box_location']) && $search_box_location == 'top_bar' || $search_box_location == '') : ?>
                <div id="top_search">
                    <?php get_search_form(); ?>
                    <?php do_action('graphene_top_search'); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
        if ($post)
            $post_id = $post->ID;
        else
            $post_id = false;
        $header_img = graphene_get_header_image($post_id);

        /*
         * Check if the page uses SSL and change HTTP to HTTPS if true 
         * 
         * Experimental. Let me know if there's any problem.
         */
        if (is_ssl() && stripos($header_img, 'https') === false) {
            $header_img = str_replace('http', 'https', $header_img);
        }

        // Gets the colour for header texts, or if we should display them at all
        if ('blank' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) || '' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR))
            $style = ' style="display:none;"';
        else
            $style = ' style="color:#' . get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) . ';"';
    ?>
    <div id="header" style="background-image:url(<?php echo $header_img; ?>);">
        <?php if ($graphene_settings['link_header_img']) : ?>
        <a href="<?php echo home_url(); ?>" id="header_img_link" title="<?php esc_attr_e('Go back to the front page', 'graphene'); ?>">&nbsp;</a>
        <?php endif; ?>
        
        <?php /* Header widget area */
		if ($graphene_settings['enable_header_widget'] && is_active_sidebar('header-widget-area')) {
			dynamic_sidebar('header-widget-area');
		}
		?>

        <h1 <?php echo $style; ?> class="header_title"><a <?php echo $style; ?> href="<?php echo home_url(); ?>" title="<?php esc_attr_e('Go back to the front page', 'graphene'); ?>"><font color="white"><?php bloginfo('name'); ?></font></a></h1>
        <h2 <?php echo $style; ?> class="header_desc"><font color="white"><?php bloginfo('description'); ?></font></h2>
        <?php do_action('graphene_header'); ?>
    </div>
    <div id="nav">
        <?php /* The navigation menu */ ?>
        <?php
        /* Header menu */
        $args = array(
            'container' => '',
            'menu_id' => 'header-menu',
            'menu_class' => 'menu clearfix',
            'fallback_cb' => 'graphene_default_menu',
            'depth' => 5,
            'theme_location' => 'Header Menu',
        );
        wp_nav_menu(apply_filters('graphene_header_menu_args', $args));

        /* Secondary menu */
        $args = array(
            'container' => '',
            'menu_id' => 'secondary-menu',
            'menu_class' => 'menu clearfix',
            'fallback_cb' => 'none',
            'depth' => 5,
            'theme_location' => 'secondary-menu',
        );
        wp_nav_menu(apply_filters('graphene_secondary_menu_args', $args));
        ?>


        <?php do_action('graphene_top_menu'); ?>

        <?php if (($search_box_location = $graphene_settings['search_box_location']) && $search_box_location == 'nav_bar') : ?>
            <div id="top_search">
                <?php get_search_form(); ?>
                <?php do_action('graphene_top_search'); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php do_action('graphene_before_content'); ?>

    <div id="content" class="clearfix">
        <?php do_action('graphene_before_content-main'); ?>
        
        <?php
        
            /* Sidebar1 on the left side? */            
            if ( in_array(graphene_column_mode(), array('two-col-right', 'three-col-right', 'three-col-center')) ){
                get_sidebar();                
            }
            /* Sidebar2 on the left side? */
            if ( graphene_column_mode() == 'three-col-right' ){
                get_sidebar('two');
            }            
        
        ?>
        
        <div id="content-main" class="clearfix">
        <?php do_action('graphene_top_content'); ?>
