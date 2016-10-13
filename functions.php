<?php

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		} );
	return;
}

Timber::$dirname = array('templates', 'views');

class StarterSite extends TimberSite {

	function __construct() {
		add_theme_support( 'custom-logo' );
		add_theme_support( 'html5', array(
			'comment-list',
			'comment-form',
			'search-form',
			'gallery',
			'caption'
		) );
		add_theme_support( 'title-tag' );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'theme_cleanup' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'register_menus' ) );
		add_action( 'init', array( $this, 'register_widgets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 100 );
		parent::__construct();
	}

	function theme_cleanup() {
		// remove EditURI/RSD
		remove_action ('wp_head', 'rsd_link');
		// remove Windows Live Writer Manifest
		remove_action( 'wp_head', 'wlwmanifest_link');
		// remove Wordpress Page/Post Shortlinks
		remove_action( 'wp_head', 'wp_shortlink_wp_head');
		// remove Wordpress Generator
		remove_action('wp_head', 'wp_generator');
		// all actions related to emojis
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		// filter to remove TinyMCE emojis
		add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojicons_tinymce' ) );
	}
	function disable_emojicons_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

	function register_assets() {
		// Styles
		wp_enqueue_style( 'theme/css', get_template_directory_uri() . '/static/css/main.css' );

		// Scripts
		if (is_single() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
		wp_enqueue_script( 'theme/js', get_template_directory_uri() . '/static/js/site.js', [], null, true );
	}

	function register_post_types() {
		require('lib/post-types.php');
	}

	function register_taxonomies() {
		require('lib/taxonomies.php');
	}

	function register_menus() {
		require('lib/menus.php');
	}

	function register_widgets() {
		require('lib/widgets.php');
	}

	function add_to_context( $context ) {
		$context['main_menu'] = new TimberMenu( 'main-nav' );
		$context['site'] = $this;
		$context['logo'] = get_custom_logo();
		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own fuctions to twig */
		// $twig->addExtension( new Twig_Extension_StringLoader() );
		// $twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
		return $twig;
	}

}

new StarterSite();
