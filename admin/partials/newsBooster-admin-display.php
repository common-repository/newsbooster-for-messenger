<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://chatbooster.pl
 * @since      1.0.0
 *
 * @package    Chatbooster
 * @subpackage Chatbooster/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <?php
    //Grab all options
    $options = get_option($this->plugin_name);

    $active_tab = isset($_GET['tab'])? $_GET['tab'] : 'configuration';
    $active_fbpage_id = $options['active_fbpage_id'];
    $active_fbpage_name = isset($options['active_fbpage_name'])? $options['active_fbpage_name'] : null;
    $license_key = $options['license_key'];
    $license_active = $options['license_active'];
    $display_append_to_posts = isset($options['display_append_to_posts'])? $options['display_append_to_posts'] : null;
    $display_fixed_box = $options['display_fixed_box'];
    $last_successful_execution_timestamp = isset($options['last_successful_execution_timestamp'])? $options['last_successful_execution_timestamp'] : null;
    $subscribers_count = isset($options['subscribers_count']) ? $options['subscribers_count'] : 0;
    $subscribers_limit = isset($options['subscribers_limit']) ? $options['subscribers_limit'] : "N/A";
    $post_types = isset($options['post_types'])? $options['post_types'] : array('post');
    $message_subscribe = (isset($options['message_subscribe']) && !empty($options['message_subscribe']))? $options['message_subscribe'] : 'Subscribe!
Click button to get daily 
news to your messenger!';
    $message_thankyou = (isset($options['message_thankyou']) && !empty($options['message_thankyou']))? $options['message_thankyou'] : 'Thanks for subscribing! You will hear from us soon!';
    $message_confirm = (isset($options['message_confirm']) && !empty($options['message_confirm']))? $options['message_confirm'] : 'Thanks for subscribing! You will hear from us soon!';

    $log = array();
    if(!empty($options['log']))
        $log = array_reverse($options['log']);

    ?>

    <?php
    do_settings_sections( $this->plugin_name );
    ?>
    <div id="poststuff" class="">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">
                    <section class="pattern" id="tabs">
                        <h2 class="nav-tab-wrapper" style="padding:0">
                            <a href="?page=<?php echo $this->plugin_name; ?>" class="nav-tab <?php echo $active_tab == 'configuration' ? 'nav-tab-active' : ''; ?>">Configuration</a>
                            <a href="?page=<?php echo $this->plugin_name; ?>&tab=stats" class="nav-tab <?php echo $active_tab == 'stats' ? 'nav-tab-active' : ''; ?>">Stats & Log</a>
                        </h2>
                    </section>
                    <?php if($active_tab == 'configuration') : ?>
                    <div class="postbox newsBooster_wrap">
                        <form method="post" name="newsBooster_options" action="options.php">
                            <?php settings_fields( $this->plugin_name ); ?>
                            <h2><span>License key</span></h2>
                            <div class="inside">
                                <input type="text" id="<?php echo $this->plugin_name;?>-license-key" name="<?php echo $this->plugin_name;?>[license_key]" value="<?php echo $license_key;?>" class="regular-text" />
                                <?php if($license_active): ?>
                                <span class="description">License valid</span>
                                <?php else : ?>
                                    <span class="description"><br/>You can find license key in order confirmation email. Get your <strong>FREE</strong> license here: <a target="_blank" href="https://wordpress.chatbooster.pl/pricing/">Get your license</a></span>
                                <?php endif; ?>
                            </div>
                            <?php if($license_active): ?>
                                <?php if(!$active_fbpage_id): ?>
                                <hr>
                                <h2><span>Select Facebook Page</span></h2>
                                <div class="inside">
                                    <table class="widefat" cellspacing="0" id="newsBooster_user_pages">
                                        <tbody>
                                        <tr class="alternate">
                                            <td class="row-title">
                                                <a id="newsBooster-permissions" class="hidden" onclick="prepareToComeback()" target="_blank" href="<?php echo $this->newsBooster_remote_url; ?>/authorize?license=<?php echo $license_key; ?>">
                                                    Grant permissions to load your Facebook Pages
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <hr>
                                <h2>
                                    <span>
                                        Facebook page
                                        <a style="text-decoration:none" id="newsBooster-change-fbpage" href="#show_pages" onclick="showPages()"><small>Change</small></a>
                                    </span>
                                </h2>
                                <div class="inside">
                                    <table class="widefat" cellspacing="0" id="newsBooster_user_pages">
                                        <tbody>
                                        <tr class="alternate">
                                            <td class="row-title">
                                                <a id="newsBooster-activated-fbpage" target="_blank" href="https://facebook.com/<?php echo $active_fbpage_id; ?>">
                                                    <?php echo $active_fbpage_name; ?>
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <h2><span>Select post type(s) to send</span></h2>
                                <div class="inside">
                                    <select id="post_types" name="<?php echo $this->plugin_name;?>[post_types][]" multiple="true">
                                        <?php
                                        foreach ( get_post_types( array('public' => true), 'object' ) as $post_type ) : ?>
                                            <option <?php echo (in_array($post_type->name,$post_types))? 'selected=\'selected\'' : ''; ?> value="<?php echo $post_type->name; ?>"><?php echo $post_type->label." (".$post_type->name; ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php //if(!isset($valid['post_types'])) ; ?>

                                </div>
                                <hr>
                                <h2><span>Display options</span></h2>
                                <div class="inside">
                                    <table class="widefat" cellspacing="0" id="newsBooster_user_pages2">
                                        <tbody>
                                        <tr class="alternate">
                                            <td class="row-title">
                                                Fixed box (default)
                                            </td>
                                            <td class="row-title">
                                                Show below post content
                                            </td>
                                            <td class="row-title">
                                                Shortcode
                                            </td>
                                        </tr>
                                        <tr class="">
                                            <td class="">
                                                <input type="checkbox" id="<?php echo $this->plugin_name;?>-display-fixed-box" name="<?php echo $this->plugin_name;?>[display_fixed_box]" <?php echo ($display_fixed_box)? 'checked' : ''; ?> class="regular-text" />
                                                <label for="<?php echo $this->plugin_name;?>-display-fixed-box">Check to show fixed box</label>
                                            </td>
                                            <td class="">
                                                <input type="checkbox" id="<?php echo $this->plugin_name;?>-display-append-to-posts" name="<?php echo $this->plugin_name;?>[display_append_to_posts]" <?php echo ($display_append_to_posts)? 'checked' : ''; ?> class="regular-text" />
                                                <label for="<?php echo $this->plugin_name;?>-display-append-to-posts">Check to show after post</label>
                                            </td>
                                            <td class="">
                                                In content editor use: <code>[newsBooster]</code><br/>
                                                In your theme sourcecode use: <br/><code><?php echo htmlspecialchars("<?php echo do_shortcode('[newsBooster]'); ?>");?></code>
                                            </td>
                                        </tr>
                                        <tr class="">
                                            <td >
                                                <!--
                                                Preview:<br/>
                                                <img src="https://dummyimage.com/200x150/0084ff/ffffff&text=Fixed box preview" alt=""/>
                                                -->
                                            </td>
                                            <td >
                                                <!--
                                                Preview:<br/>
                                                <img src="https://dummyimage.com/200x150/0084ff/ffffff&text=After box preview" alt=""/>
                                                -->
                                            </td>
                                            <td >
                                                <!--Preview:<br/><br/><br/>
                                                <?php //echo do_shortcode('[newsBooster]'); ?>-->
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                    <h2><span>Edit messages</span> <small>(use Enter to break lines)</small></h2>
                                <div class="inside">
                                    <table class="widefat" cellspacing="0" id="newsBooster_user_pages3">
                                        <tbody>
                                        <tr class="alternate">
                                            <td class="row-title">
                                                Placement
                                            </td>
                                            <td class="row-title">
                                                Text
                                            </td>
                                            <td class="row-title">
                                                Preview
                                            </td>
                                        </tr>
                                        <tr class="">
                                            <td class="">
                                                Call to action message<br>(your website)
                                            </td>
                                            <td class="">
                                                <textarea style="white-space: nowrap;min-height:120px; width:13em;" id="message_subscribe" name="<?php echo $this->plugin_name;?>[message_subscribe]"><?php echo $message_subscribe; ?></textarea>
                                            </td>
                                            <td class="">
                                                <div class="block_msg ">
                                                    <div class="msg_wrap">

                                                        <div class="msg_post">
                                                            <div class="asset"></div>
                                                            <div class="msg">
                                                                <p><?php echo str_replace(PHP_EOL,"<br>",$message_subscribe); ?></p>
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
                                            </td>
                                        </tr>
                                        <tr class="">
                                            <td>
                                                Thank you message<br>(your website)
                                            </td>
                                            <td>
                                                <textarea style="white-space: nowrap;min-height:120px; width:13em;" id="message_thankyou" name="<?php echo $this->plugin_name;?>[message_thankyou]"><?php echo $message_thankyou; ?></textarea>
                                            </td>
                                            <td>
                                                <br><br>
                                                <button onclick="alert('<?php echo $message_thankyou; ?>'); return false;">See preview</button>
                                            </td>
                                        </tr>
                                        <tr class="">
                                            <td>
                                                Confirm subscription <br>message<br>(in Messenger)
                                            </td>
                                            <td>
                                                <textarea style="white-space: nowrap;min-height:120px; width:13em;" id="message_confirm" name="<?php echo $this->plugin_name;?>[message_confirm]"><?php echo $message_confirm; ?></textarea>
                                            </td>
                                            <td>
                                                Click button below and check your Messenger:
                                                <?php echo do_shortcode('[newsBooster]'); ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <h2><span>Subscription message preview <small>(based on your 3 latest posts)</small></span></h2>
                                <div class="inside">

                                    <?php if(!get_theme_support('post-thumbnails')): ?>
                                        <div class="inside message_preview">
                                            <p>Your theme doesn't support post thumbnails so we are unable to get your post images.</p>
                                        </div>
                                    <?php endif; ?>

                                    <?php $the_query = new WP_Query( array('post_status' => 'publish','post_type'=>$post_types, 'posts_per_page' => 3) ); ?>
                                    <?php if ( $the_query->have_posts() ) : ?>
                                        <div class="inside ">
                                            <div class=" message_preview">
                                                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                                                    <div style="width:30%; float:left; margin-right:1%;margin-left:1%;">
                                                        <?php if (has_post_thumbnail( get_the_ID() ) ): ?>
                                                            <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-post-thumbnail' ); ?>
                                                            <div class="message_preview_image_horizontal" style="background-image: url(<?php echo $image[0]; ?>)"></div>
                                                        <?php else: ?>
                                                            This post doesn't have thumbnail.
                                                        <?php endif; ?>
                                                        <h3><?php echo substr(get_the_title(),0,$this->title_character_limit); ?></h3>
                                                        <p><?php echo substr(get_the_excerpt(),0,$this->subtitle_character_limit); ?></p>
                                                    </div>
                                                <?php endwhile; ?>
                                                <?php wp_reset_postdata(); ?>
                                                <div style="clear:both;"></div>
                                            </div>
                                            <br/>
                                            <a class="button-secondary" id="newsBooster-send-template-test" href="#send-template-test"><?php esc_attr_e( 'Send test subscription message to your messenger' ); ?></a>
                                        </div>
                                    <?php else : ?>
                                        <div class="inside message_preview">
                                            <div>
                                                <p><?php _e( 'Seems like you don\'t have any posts we can use as preview. Add some posts to your website.' ); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <!--<img src="https://scontent-amt2-1.xx.fbcdn.net/v/t39.2365-6/13679808_631224743722018_2016203957_n.png?oh=165a94b74298632a4c199b8b6df221a7&oe=5992FDFC" alt=""/>-->
                                <!-- plugin settings -->
                                <input type="hidden" id="<?php echo $this->plugin_name;?>-active-fbpage-id" name="<?php echo $this->plugin_name;?>[active_fbpage_id]" value="<?php echo $active_fbpage_id;?>" />
                                <input type="hidden" id="<?php echo $this->plugin_name;?>-active-fbpage-name" name="<?php echo $this->plugin_name;?>[active_fbpage_name]" value="<?php echo $active_fbpage_name;?>" />
                                <input type="hidden" id="<?php echo $this->plugin_name;?>-last_successful_execution_timestamp" name="<?php echo $this->plugin_name;?>[last_successful_execution_timestamp]" value="<?php echo $last_successful_execution_timestamp;?>" />
                                <input type="hidden" id="<?php echo $this->plugin_name;?>-subscribers_count" name="<?php echo $this->plugin_name;?>[subscribers_count]" value="<?php echo $subscribers_count;?>" />
                                <input type="hidden" id="<?php echo $this->plugin_name;?>-subscribers_limit" name="<?php echo $this->plugin_name;?>[subscribers_limit]" value="<?php echo $subscribers_limit;?>" />

                                <?php endif; ?>
                                <hr>
                                <div class="inside">
                                    <?php submit_button(__('Save all settings', $this->plugin_name), 'primary','submit', TRUE); ?>
                                </div>
                        </form>
                    </div>
                    <?php elseif($active_tab == 'stats'): ?>
                        <p><strong>Logs</strong></p>
                        <table class="widefat">
                            <thead>
                            <tr>
                                <th class="row-title"><?php esc_attr_e( 'Date', 'wp_admin_style' ); ?></th>
                                <th><?php esc_attr_e( 'Action', 'wp_admin_style' ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=0; foreach($log as $log_item) : ?>
                            <tr <?php echo ($i%2)? 'class="alternate"' : ''; ?>>
                                <td class="row-title"><?php echo $log_item['date_readable']; ?></td>
                                <td><?php echo ($log_item['error'])?
                                        '<details class="primer">
                            <summary style="cursor:pointer;">••• More</summary>
                            <section>
                                <pre class=" language-php" style="max-width:300px">
'.$log_item['response_body'].'
                                </pre>
                            </section>
                        </details>'
                                        :
                                        $log_item['message']; ?></td>
                            </tr>
                            <?php $i++; endforeach; ?>
                            </tfoot>
                        </table>
                        <details class="primer">
                            <summary style="cursor:pointer;">••• More</summary>
                            <section>
                                <pre class=" language-php">
<strong>Last successful subscription execution: </strong> <?php echo $this->get_last_execution_readable(); ?><br/>
<strong>Next planned subscription execution: </strong> <?php echo $this->get_next_schedule_readable(); ?><br/>
<strong>Posts to send in next execution: </strong> <?php echo sizeof($this->get_new_posts_to_send()); ?>
                                </pre>
                            </section>
                        </details>
                    <?php endif; ?>
                    <!-- .postbox -->
                </div>
                <!-- .meta-box-sortables .ui-sortable -->
            </div>
            <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">

                <div class="meta-box-sortables">

                    <div class="postbox">

                        <h2><span>Your subscription information</span></h2>

                        <div class="inside">
                            <?php if($license_active && $active_fbpage_id): ?>
                                <h1><?php echo $subscribers_count." of ".$subscribers_limit ?></h1> subscribers (refresh count <span style="cursor:pointer" id="newsBooster-refresh-count" class="dashicons dashicons-update"></span>)
                            <?php else: ?>
                            <p>Configure plugin first.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                    <!-- .postbox -->

                    <div class="postbox">

                        <h2><span>newsBooster</span></h2>

                        <div class="inside">
                            <ul>
                                <li>
                                    <a target="_blank" href="https://wordpress.chatbooster.pl/">newsBooster Homepage</a>
                                </li>
                                <li>
                                    <a target="_blank" href="https://wordpress.chatbooster.pl/pricing">Pricing plans</a>
                                </li>
                                <li>
                                    <a target="_blank" href="https://wordpress.chatbooster.pl/my-account">Your account</a>
                                </li>
                                <li>
                                    <a target="_blank" href="https://wordpress.chatbooster.pl/support">Documentation</a>
                                </li>
                                <li>
                                    <a target="_blank" href="https://wordpress.chatbooster.pl/contact">Contact us</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>