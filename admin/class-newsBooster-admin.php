<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://chatbooster.pl
 * @since      1.0.0
 *
 * @package    Chatbooster
 * @subpackage Chatbooster/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chatbooster
 * @subpackage Chatbooster/admin
 * @author     newsBooster <tomasz.zewlakow@chatbooster.pl>
 */
class Newsbooster_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * newsBooster remote url for communication
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $newsBooster_remote_url
	 */
    private $newsBooster_remote_url = 'https://app.chatbooster.pl/api/wordpress';
    //private $newsBooster_remote_url = 'http://localhost/PhpStormProjects/chatBooster/public/api/wordpress';

    public $title_character_limit;
    public $subtitle_character_limit;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->title_character_limit = 80;
        $this->subtitle_character_limit = 80;
        $this->newsBooster_options = get_option($this->plugin_name);
	}

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Cbf_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Cbf_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if ( 'settings_page_newsBooster' == get_current_screen() -> id ) {
            wp_enqueue_style( $this->plugin_name.'-admin', plugin_dir_url( __FILE__ ) . 'css/newsBooster-admin.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../public/css/newsBooster-public.css', array(), $this->version, 'all' );
        }
    }
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Cbf_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Cbf_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if ( 'settings_page_newsBooster' == get_current_screen() -> id ) {
            wp_enqueue_media();
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/newsBooster-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );
            wp_localize_script( $this->plugin_name, 'newsBooster' , array(
                'message_thankyou' => $this->getMessageThankyou(),
                'locale' => get_locale(),
                'ajax_url' => admin_url( 'admin-ajax.php' )
            ) );
        }
    }

    /**
     * @since    1.0.0
     */
    public function admin_notice__success() {
        $class = 'notice notice-success';
        //$message = __( 'Plugin is ready to work!<br/>occurred.', $this->plugin_name );
        //$this->show_notice($class, $message, true);
    }

    /**
     * @since    1.0.0
     */
    public function setup_wp_schedule($recurrence) {
        wp_schedule_event(strtotime('noon '.get_option('timezone_string'), current_time('timestamp')), $recurrence, 'wp_schedule_newsBooster_event');
    }

    /**
     * @since    1.0.0
     */
    public function clear_wp_schedule() {
        $timestamp = wp_next_scheduled ('wp_schedule_newsBooster_event');
        wp_unschedule_event($timestamp, 'wp_schedule_newsBooster_event');
    }

    /**
     * @since    1.0.0
     */
    public function admin_notice__error() {
        $class = 'notice notice-error';

        if($this->newsBooster_options['license_active'] !== true){
            if(empty($this->newsBooster_options['license_key']))
                $message = __( '<strong>Thank you for using newsBooster!</strong> Start using plugin by providing license key here: <a href="'.admin_url().'options-general.php?page='.$this->plugin_name.'">Activate</a>', $this->plugin_name );
            else
                $message = __( '<strong>Thank you for using newsBooster!</strong> There is problem with your license key. Check it here: <a href="'.admin_url().'options-general.php?page='.$this->plugin_name.'">Activate</a>', $this->plugin_name );

            $this->show_notice($class, $message, false);
        }
    }

    /**
     * @since    1.0.0
     */
    public function show_notice($class, $message, $dismissible = false) {
        if($dismissible)
            $class .= " is-dismissible";

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ),  $message  );
    }

    /**
     * @since    1.1.0
     */
    public function get_observed_post_types(){
        if(!isset($this->newsBooster_options['post_types']))
            return array('post');
        else
            return $this->newsBooster_options['post_types'];
    }

    /**
     * @since    1.0.0
     */
    public function get_latest_posts($number = 3) {
        $posts = array();
        $the_query = new WP_Query( array('post_status' => 'publish', 'post_type'=> $this->get_observed_post_types(), 'posts_per_page' => $number) );
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ){
                $the_query->the_post();
                $post = new \stdClass();
                if (has_post_thumbnail( $post->ID ) ) {
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                    $post->image_url = $image[0];
                }
                $post->id = get_the_ID();
                $post->title = get_the_title();
                $post->subtitle = get_the_excerpt();
                $post->url = get_permalink();
                $posts[] = $post;
            }
            wp_reset_postdata();
        }
        return $posts;
    }

    /**
     * @since    1.0.0
     */
    public function get_new_posts_to_send($number = 9) {
        $options = get_option($this->plugin_name);

        $last_successful_execution_timestamp = null;
        if(isset($options['last_successful_execution_timestamp']))
            $last_successful_execution_timestamp = $options['last_successful_execution_timestamp'];

        $posts = array();
        $the_query = new WP_Query( array('post_type'=> $this->get_observed_post_types(), 'posts_per_page' => $number) );
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ){
                $the_query->the_post();
                //skip already sent posts
                if($last_successful_execution_timestamp != null && get_post_time('U',true) < $last_successful_execution_timestamp) continue;

                $post = new \stdClass();
                if (has_post_thumbnail( get_the_ID() ) ) {
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');
                    $post->image_url = $image[0];
                }
                $post->id = get_the_ID();
                $post->title = get_the_title();
                $post->subtitle = get_the_excerpt();
                $post->url = get_permalink();
                $posts[] = $post;
            }
            wp_reset_postdata();
        }
        return $posts;
    }

    /**
     * @since    1.0.0
     */
    public function send_test() {
        global $wp_version;
        $body_params = array(
            'action' => 'send_test',
            'posts' => $this->get_latest_posts(3),
            'home_url' => home_url()
        );

        $response = wp_remote_post($this->newsBooster_remote_url,$this->get_connection_args($body_params));

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
            die();
        }

        $header = $response['headers']; // array of http header lines
        $body = json_decode($response['body']);
        if($body == null){
            //debug
            print_r($response['body']);
        }else{
            echo json_encode($body);
            $this->write_to_log($body,$body_params);
            $this->updateMetaDataFromResponse($body);
        }
        wp_die();
    }

    /**
     * @since    1.3.0
     */
    public function refresh_count() {
        global $wp_version;
        $body_params = array(
            'action' => 'refresh_count',
            'home_url' => home_url()
        );

        $response = wp_remote_post($this->newsBooster_remote_url,$this->get_connection_args($body_params));

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
            die();
        }

        $header = $response['headers']; // array of http header lines
        $body = json_decode($response['body']);
        if($body == null){
            //debug
            print_r($response['body']);
        }else{
            echo json_encode($body);
            $this->write_to_log($body,$body_params);
            $this->updateMetaDataFromResponse($body);
        }
        wp_die();
    }

    /**
     * @since    1.0.0
     */
    public function newsBooster_trigger() {
        global $wp_version;
        $body_params = array(
            'action' => 'send_subscription',
            'posts' => $this->get_new_posts_to_send(),
            'home_url' => home_url()
        );
        if(!sizeof($body_params['posts'])) return;
        $response = wp_remote_post($this->newsBooster_remote_url,$this->get_connection_args($body_params));

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
            die();
        }

        $header = $response['headers']; // array of http header lines
        $body = json_decode($response['body']);
        if($body == null){
            //debug
            print_r($response['body']);
            $this->write_to_log($response['body'],$body_params,true);
        }else{
            $this->write_to_log($body,$body_params);
            if($body->success)
                $this->update_newsBooster_option('last_successful_execution_timestamp',time());
            $this->updateMetaDataFromResponse($body);
        }
        wp_die();
    }

    /**
     * @since   1.0.0
     */
    public function updateMetaDataFromResponse($response){
        if(isset($response->subscribers_count))
            $this->update_newsBooster_option('subscribers_count',$response->subscribers_count);
        if(isset($response->subscribers_limit))
            $this->update_newsBooster_option('subscribers_limit',$response->subscribers_limit);
    }

    /**
     * @since    1.0.0
     */
    public function write_to_log($response, $params, $error = false) {
        $options = get_option($this->plugin_name);
        if(!isset($options['log']))
            $options = array();
        else
            $options = $options['log'];

        $options[] = array(
            'timestamp' => time(),
            'date' => current_time('timestamp'),
            'date_readable' => current_time('mysql'),
            'error' => $error,
            'response_body' => json_encode($response),
            'message' => $response->message,
            'params' => $params
        );

        $this->update_newsBooster_option( 'log', $options );
    }

    /**
     * @since    1.0.0
     */
    public function update_newsBooster_option($option, $value) {
        $options = get_option($this->plugin_name);

        /*
        if(!isset($options[$option]))
            $options[$option] = '';
        */

        $options[$option] = $value;
        $options['skip_validation'] = true;
        update_option( $this->plugin_name, $options );
    }


    /**
     * @since    1.0.0
     */
    public function get_last_execution_readable() {
        if(empty($this->newsBooster_options['last_successful_execution_timestamp'])){
            return "Not executed yet";
        }
        $dt = new DateTime('now', new DateTimeZone(get_option('timezone_string'))); //first argument "must" be a string
        $dt->setTimestamp($this->newsBooster_options['last_successful_execution_timestamp']); //adjust the object to correct timestamp
        return $dt->format('d.m.Y, H:i:s');
    }

    /**
     * @since    1.0.0
     */
    public function get_next_schedule_readable() {
        //$schedule = wp_get_schedule( 'wp_schedule_newsBooster_event' );
        //var_dump($schedule);
        if(!wp_next_scheduled ( 'wp_schedule_newsBooster_event' )) return "Provide license key first";
        $dt = new DateTime('now', new DateTimeZone(get_option('timezone_string'))); //first argument "must" be a string
        $dt->setTimestamp(wp_next_scheduled ( 'wp_schedule_newsBooster_event' )); //adjust the object to correct timestamp
        return $dt->format('d.m.Y, H:i:s');
    }

    /**
     * @since    1.0.0
     */
    public function activate_page() {
        $fb_page_id = $_POST['fb_page_id'];

        $body_params = array(
            'action' => 'activate_fbpage',
            'fb_page_id' => $fb_page_id,
            'home_url' => home_url()
        );

        $response = wp_remote_post($this->newsBooster_remote_url,$this->get_connection_args($body_params));

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
            die();
        }

        $header = $response['headers']; // array of http header lines
        $body = json_decode($response['body']);
        if($body == null){
            //debug
            print_r($response['body']);
        }else{
            if(isset($body->fb_page_id))
                $this->update_newsBooster_option('active_fbpage_id',$body->fb_page_id);

            if(isset($body->fb_page_name))
                $this->update_newsBooster_option('active_fbpage_name',$body->fb_page_name);

            echo json_encode($body);
            $this->write_to_log($body,$body_params);
            $this->updateMetaDataFromResponse($body);
        }
        wp_die();
    }

    /**
     * @since    1.0.0
     */
    public function load_pages() {
        $license_key = $this->newsBooster_options['license_key'];

        $body_params = array(
            'action' => 'load_pages',
            'license_key' => $license_key,
            'home_url' => home_url()
        );

        $response = wp_remote_post($this->newsBooster_remote_url,$this->get_connection_args($body_params));

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
            die();
        }

        $header = $response['headers']; // array of http header lines
        $body = json_decode($response['body']);
        if($body == null){
            //debug
            print_r($response['body']);
        }else{
            echo json_encode($body);
        }
        wp_die();
    }

    /**
     * @since    1.0.0
     */
    public function activate_license($license_key) {
        global $wp_version;

        $body_params = array(
                'action' => 'activate_license',
                'license_key' => $license_key,
                'message_confirm' => $this->getMessageConfirm(),
                'home_url' => home_url()
        );
        $response = wp_remote_post($this->newsBooster_remote_url,$this->get_connection_args($body_params));
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
            die();
        }
        $header = $response['headers']; // array of http header lines
        $body = json_decode($response['body']);
        if($body == null){
            //debug
            print_r($response['body']);
            wp_die();
        }else{
            if(isset($body->success)){
                $this->write_to_log($body,$body_params);
                $this->updateMetaDataFromResponse($body);
                add_settings_error( 'license_key', esc_attr( 'settings_updated' ), $body->message, "updated" );
                return true;
            }
            if(isset($body->error)){
                add_settings_error( 'license_key', esc_attr( 'settings_updated' ), $body->error, "error" );
                $this->write_to_log($body,$body_params,true);
            }
            return false;
        }
    }

    /**
     * @since 1.0.0
     */
    public function get_connection_args($body_params){
        return array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'sslverify' => false,
            'blocking' => true,
            'headers' => array(),
            'body' => $body_params,
            'cookies' => array()
        );
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        add_options_page( 'newsBooster Configuration', 'newsBooster', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
        );
    }
    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links( $links ) {
        /*
         *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
         */
        $settings_link = array(
            '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge(  $settings_link, $links );
    }
    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page() {
        include_once('partials/newsBooster-admin-display.php');
    }
    /**
     *  Save the plugin options
     *
     *
     * @since    1.0.0
     */
    public function options_update() {
        register_setting( $this->plugin_name, $this->plugin_name, array($this, 'validate') );
    }
    /**
     * Validate all options fields
     *
     * @since    1.0.0
     */
    public function validate($input) {
        //manual updates
        if(isset($input['skip_validation'])){
            unset($input['skip_validation']);
            return $input;
        }

        // settings page
        $valid = get_option($this->plugin_name);

        $valid['display_append_to_posts'] = $input['display_append_to_posts'];
        $valid['display_fixed_box'] = $input['display_fixed_box'];
        $valid['active_fbpage_id'] = $input['active_fbpage_id'];;
        $valid['active_fbpage_name'] = $input['active_fbpage_name'];;
        $valid['last_successful_execution_timestamp'] = $input['last_successful_execution_timestamp'];
        $valid['subscribers_count'] = $input['subscribers_count'];
        $valid['subscribers_limit'] = $input['subscribers_limit'];
        $valid['post_types'] = $input['post_types'];
        $valid['message_subscribe'] = $input['message_subscribe'];
        $valid['message_thankyou'] = $input['message_thankyou'];
        $valid['message_confirm'] = $input['message_confirm'];

        if($valid['license_key'] !== $input['license_key']){
            $valid['license_active'] = $this->activate_license($input['license_key']);
            $valid['license_key'] = $input['license_key'];
            $valid['display_fixed_box'] = true;
        }

        return $valid;
    }

    /**
     * @since    1.2
     */
    private function getMessageThankyou(){
        if(isset($this->newsBooster_options['message_thankyou']) && !empty($this->newsBooster_options['message_thankyou'])){
            return $this->newsBooster_options['message_thankyou'];
        }else{
            return 'Thanks for subscribing! You will hear from us soon!';
        }
    }

    /**
     * @since    1.2
     */
    private function getMessageConfirm(){
        if(isset($this->newsBooster_options['message_confirm']) && !empty($this->newsBooster_options['message_confirm'])){
            return $this->newsBooster_options['message_confirm'];
        }else{
            return 'Thank you for subscribing! You will hear from us soon!';
        }
    }
}
