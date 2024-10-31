<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://chatbooster.pl
 * @since      1.0.0
 *
 * @package    Chatbooster
 * @subpackage Chatbooster/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chatbooster
 * @subpackage Chatbooster/public
 * @author     newsBooster <tomasz.zewlakow@chatbooster.pl>
 */
class Chatbooster_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->newsBooster_options = get_option($this->plugin_name);

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chatbooster_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chatbooster_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/newsBooster-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
	    $showFixedBox = false;

        if(!empty($this->newsBooster_options['active_fbpage_id']) && $this->newsBooster_options['display_fixed_box'])
            $showFixedBox = true;

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/newsBooster-public.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'newsBooster', array(
            'message_thankyou' => $this->getMessageThankyou(),
            'message_subscribe' => $this->getMessageSubscribe(),
            'locale' => get_locale(),
            'show_fixed_box' => $showFixedBox,
            'page_id' => $this->newsBooster_options['active_fbpage_id']
        ) );
	}

    /**
	 * @since    1.0.0
	 */
	public function generateUserRef() {
	    return time().rand(1000,9999);
	}

	public function show_after_post_send_to_messenger_plugin($content){
	    if(empty($this->newsBooster_options['active_fbpage_id'])) return $content;
	    if(empty($this->newsBooster_options['display_append_to_posts'])) return $content;
        if(!is_single()) return $content;
        ob_start();
	    ?>
        <div class="block_msg ">
            <div class="msg_wrap">
                
                <div class="msg_post">
                    <div class="asset"></div>
                    <div class="msg">
                        <p><?php echo $this->getMessageSubscribe(); ?></p>
                    </div>
                </div>
                
                <div class="msg_widget_wrap">
                    <div class="msg_widget">
                        <div class="fb-send-to-messenger" 
                          messenger_app_id="1630806237235780"
                          page_id="<?php echo $this->newsBooster_options['active_fbpage_id']; ?>"
                          data-ref="SUBSCRIPTION_CONFIRM"
                          color="blue" 
                          size="xlarge">
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <?php
        $content .= ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * @since    1.2
     */
    private function getMessageSubscribe(){
        if(isset($this->newsBooster_options['message_subscribe']) && !empty($this->newsBooster_options['message_subscribe'])){
            return str_replace(PHP_EOL,'<br>',$this->newsBooster_options['message_subscribe']);
        }else{
            return str_replace(PHP_EOL,'<br>','Subscribe!
Click button to get daily 
news to your messenger!');
        }
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
	public function shortcode_display(){
	    return '
	    <div class="fb-send-to-messenger" 
          messenger_app_id="1630806237235780" 
          page_id="'.$this->newsBooster_options['active_fbpage_id'].'" 
          data-ref="SUBSCRIPTION_CONFIRM" 
          color="blue" 
          size="xlarge">
        </div>
	    ';
    }

    /**
     * @since    1.2
     */
    public function newsBooster_trigger(){

    }

    /**
     * Cleanup functions depending on each checkbox returned value in admin
     *
     * @since    1.0.0
     */
    // Cleanup head
    public function newsBooster_init() {
        
    }
    // Cleanup head
    public function wp_cbf_remove_x_pingback($headers) {
        if(!empty($this->wp_cbf_options['cleanup'])){
            unset($headers['X-Pingback']);
            return $headers;
        }
    }
    // Remove Comment inline CSS
    public function wp_cbf_remove_comments_inline_styles() {
        if(!empty($this->wp_cbf_options['comments_css_cleanup'])){
            global $wp_widget_factory;
            if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
                remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
            }
            if ( isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments']) ) {
                remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
            }
        }
    }
    // Remove gallery inline CSS
    public function wp_cbf_remove_gallery_styles($css) {
        if(!empty($this->wp_cbf_options['gallery_css_cleanup'])){
            return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
        }
    }
    // Add post/page slug
    public function wp_cbf_body_class_slug( $classes ) {
        if(!empty($this->wp_cbf_options['body_class_slug'])){
            global $post;
            if(is_singular()){
                $classes[] = $post->post_name;
            }
        }
        return $classes;
    }
    // Load jQuery from CDN if available
    public function wp_cbf_cdn_jquery(){
        if(!empty($this->wp_cbf_options['jquery_cdn'])){
            if(!is_admin()){
                if(!empty($this->wp_cbf_options['cdn_provider'])){
                    $link = $this->wp_cbf_options['cdn_provider'];
                }else{
                    $link = 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js';
                }
                $try_url = @fopen($link,'r');
                if( $try_url !== false ) {
                    wp_deregister_script( 'jquery' );
                    wp_register_script('jquery', $link, array(), null, false);
                }
            }
        }
    }
}
