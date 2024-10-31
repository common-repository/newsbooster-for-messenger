<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://chatbooster.pl
 * @since      1.0.0
 *
 * @package    Chatbooster
 * @subpackage Chatbooster/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Chatbooster
 * @subpackage Chatbooster/includes
 * @author     newsBooster <tomasz.zewlakow@chatbooster.pl>
 */
class Chatbooster {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Chatbooster_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'newsBooster';
		$this->version = '1.3.1';
        $this->newsBooster_options = get_option($this->plugin_name);

		$this->load_dependencies();
		$this->set_locale();
		$this->define_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_wp_schedule();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Chatbooster_Loader. Orchestrates the hooks of the plugin.
	 * - Chatbooster_i18n. Defines internationalization functionality.
	 * - Newsbooster_Admin. Defines all hooks for the admin area.
	 * - Chatbooster_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-newsBooster-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-newsBooster-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-newsBooster-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-newsBooster-public.php';

		$this->loader = new Chatbooster_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Chatbooster_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Chatbooster_i18n();
        $plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
        if(!is_admin()) return;
		$plugin_admin = new Newsbooster_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notice__success' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notice__error' );

		// Save/Update our plugin options
        $this->loader->add_action( 'admin_init', $plugin_admin, 'options_update');
        // Add menu item
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        // Add Settings link to the plugin
        $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
        $this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
        //Admin Customizations
        $this->loader->add_action( 'login_enqueue_scripts', $plugin_admin, 'wp_cbf_login_css' );

        //Ajax hooks
        $this->loader->add_action( 'wp_ajax_load_pages', $plugin_admin , 'load_pages' );
        $this->loader->add_action( 'wp_ajax_nopriv_load_pages', $plugin_admin , 'load_pages' );

        $this->loader->add_action( 'wp_ajax_activate_page', $plugin_admin , 'activate_page' );
        $this->loader->add_action( 'wp_ajax_nopriv_activate_page', $plugin_admin , 'activate_page' );

        $this->loader->add_action( 'wp_ajax_send_test', $plugin_admin , 'send_test' );
        $this->loader->add_action( 'wp_ajax_nopriv_send_test', $plugin_admin , 'send_test' );

        $this->loader->add_action( 'wp_ajax_refresh_count', $plugin_admin , 'refresh_count' );
        $this->loader->add_action( 'wp_ajax_nopriv_refresh_count', $plugin_admin , 'refresh_count' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
        if(is_admin()) return;
		$plugin_public = new Chatbooster_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'generateUserRef' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        //Actions
        //$this->loader->add_action( 'init', $plugin_public, 'newsBooster_init' );
        //$this->loader->add_action( 'wp_loaded', $plugin_public, 'wp_cbf_remove_comments_inline_styles' );
        //$this->loader->add_action( 'wp_loaded', $plugin_public, 'wp_cbf_remove_gallery_styles' );
        //$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'wp_cbf_cdn_jquery', PHP_INT_MAX);
        //Filters
        //$this->loader->add_filter('wp_enqueue_scripts', $plugin_public, 'show_fixed_send_to_messenger_plugin');
        $this->loader->add_filter('the_content', $plugin_public, 'show_after_post_send_to_messenger_plugin');

        //$this->loader->add_filter('wp_headers', $plugin_public, 'wp_cbf_remove_x_pingback');
        //$this->loader->add_filter( 'body_class', $plugin_public, 'wp_cbf_body_class_slug' );

	}

	/**
	 * Register all of the hooks related to the plugin functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_hooks() {
		$plugin_public = new Chatbooster_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_shortcode( 'newsBooster', $plugin_public, 'shortcode_display' );
	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_wp_schedule() {
		$plugin_admin = new Newsbooster_Admin( $this->get_plugin_name(), $this->get_version() );
        //$this->loader->add_action( 'init', $plugin_admin, 'newsBooster_trigger' );

		if($this->newsBooster_options['license_active'] && $this->newsBooster_options['active_fbpage_id']){
            if (! wp_next_scheduled ('wp_schedule_newsBooster_event'))
                $plugin_admin->setup_wp_schedule('daily');

            $this->loader->add_action( 'wp_schedule_newsBooster_event', $plugin_admin, 'newsBooster_trigger' );
        }else{
            $plugin_admin->clear_wp_schedule();
        }
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
        //remove_all_filters( 'sanitize_option_'.$this->plugin_name, 10 );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Chatbooster_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
