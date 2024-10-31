(function( $ ) {
    'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
    var rendered = false;
    window.fbAsyncInit = function() {
        FB.init({
            appId: "1630806237235780",
            xfbml: true,
            version: "v2.6"
        });

        FB.Event.subscribe('send_to_messenger', function(e) {
            if (e.event == 'opt_in') {
                alert(newsBooster.message_thankyou);
            }
            if(e.event == 'rendered'){
                rendered = true;
                if(document.getElementById('chatBooster-adblock-detected')) document.getElementById('chatBooster-adblock-detected').remove();
                $('.msg_widget_wrap').show();
            }
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) { return; }
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/"+newsBooster.locale+"/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    $(document).ready(function() {
        if(newsBooster.show_fixed_box){
            $('body').append('<a class="floating_avatar">\n' +
                '            <span>1</span>\n' +
                '        </a>\n' +
                '        <div class="floating_msg">\n' +
                '            <div class="msg_wrap">\n' +
                '                <div class="close"><i class="fa fa-times"></i></div>\n' +
                '\n' +
                '                <div class="msg_post">\n' +
                '                    <div class="asset"></div>\n' +
                '                    <div class="msg">\n' +
                '                        <p>'+newsBooster.message_subscribe+'</p>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '\n' +
                '                <div class="msg_widget_wrap">\n' +
                '                    <div class="msg_widget">\n' +
                '                        <div class="fb-send-to-messenger"\n' +
                '                          messenger_app_id="1630806237235780"\n' +
                '                          page_id="'+newsBooster.page_id+'"\n' +
                '                          data-ref="SUBSCRIPTION_CONFIRM"\n' +
                '                          color="blue"\n' +
                '                          size="xlarge">\n' +
                '                        </div>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '\n' +
                '            </div>\n' +
                '        </div>');
        }

        //MSG RWD POPUP SHOW
        $(document).on('click', '.floating_avatar', function (event) {
            if ($('.floating_msg').is(':visible')) {
                $('.floating_msg').fadeOut(300);
            } else {
                $('.floating_msg').fadeIn(300);
            }
        });
        $(document).on('click', '.floating_msg .close', function (event) {
            $('.floating_msg').fadeOut(300);
        });
        window.setTimeout(function() {
            if (!rendered) {
                var adblockMessage = '<div class="msg" id="chatBooster-adblock-detected" style="margin-top: 5px;"><p>⚠️ Disable AdBlock<br>to subscribe 😃<br></p></div>';
                $('.block_msg  .msg_post').append(adblockMessage);
                $('.floating_msg .msg_post').append(adblockMessage);
                $('.msg_widget_wrap').hide();
            }
        }, 5000);
    });
})( jQuery );
