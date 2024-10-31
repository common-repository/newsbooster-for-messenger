
// TabWindowVisibilityManager
(function($){var vis=function(){var stateKey,eventKey,keys={hidden:"visibilitychange",webkitHidden:"webkitvisibilitychange",mozHidden:"mozvisibilitychange",msHidden:"msvisibilitychange"};for(stateKey in keys)if(stateKey in document){eventKey=keys[stateKey];break}return function(c){if(c)document.addEventListener(eventKey,c);return!document[stateKey]}}();$.fn.TabWindowVisibilityManager=function(options){var defaults={onFocusCallback:function(){},onBlurCallback:function(){}};var o=$.extend(defaults,options);var notIE= document.documentMode===undefined,isChromium=window.chrome;this.each(function(){vis(function(){if(vis())setTimeout(function(){o.onFocusCallback()},300);else o.onBlurCallback()});if(notIE&&!isChromium)$(window).on("focusin",function(){setTimeout(function(){o.onFocusCallback()},300)}).on("focusout",function(){o.onBlurCallback()});else if(window.addEventListener){window.addEventListener("focus",function(event){setTimeout(function(){o.onFocusCallback()},300)},false);window.addEventListener("blur",function(event){o.onBlurCallback()}, false)}else{window.attachEvent("focus",function(event){setTimeout(function(){o.onFocusCallback()},300)});window.attachEvent("blur",function(event){o.onBlurCallback()})}});return this}})(jQuery);

function getLoaderHtml(message){
    return '<div class="spinner is-active" id="newsBooster-loader" style="float:none;width:auto;height:auto;padding:10px 0 10px 50px;background-position:20px 0;">'+message+'</div>';
}

function prepareToComeback(){
    jQuery('#newsBooster_user_pages tbody').html(
        "Reload this page after permissions has been granted"
    );
    jQuery(window).TabWindowVisibilityManager({
        onFocusCallback: function(){
            location.reload();
            // tween resume() code goes here

        },
        onBlurCallback: function(){

            // tween pause() code goes here

        }
    });
}

function showPages(){
    jQuery('#newsBooster_user_pages td').append(
        getLoaderHtml('Loading your Facebook Pages')
    );

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            'action': 'load_pages'
        },
        success:function(data) {
            //errors
            if(data.error) {
                alert(data.error);
                return;
            }
            //messages
            if(data.message)
                alert(data.message);

            if(data.permissionsNeeded){
                if(jQuery('#newsBooster-permissions').length){
                    jQuery('#newsBooster-loader').remove();
                    jQuery('#newsBooster-permissions').show();
                }else{
                    jQuery( '#newsBooster-active-fbpage-id' ).val('');
                    jQuery( '#newsBooster-active-fbpage-name' ).val('');
                    jQuery('form #submit').click();
                }
            }

            if(data.resetLicense){
                jQuery( '#newsBooster-active-fbpage-id' ).val('');
                jQuery( '#newsBooster-active-fbpage-name' ).val('');
                jQuery( '#newsBooster-license-key' ).val('');
                jQuery('form #submit').click();
            }

            if(data.fbPages){
                var append = "";
                jQuery.each(data.fbPages,function(i,v){
                    var table_class = (i%2)? 'alternate' : '';

                    if(v.fb_id == jQuery( '#newsBooster-active-fbpage-id' ).val())
                        var link ="<strong>Activated</strong>";
                    else
                        var link = "<a class='newsBooster_activate_page' href='#' data-id='"+v.fb_id+"'>Activate</a>";

                    append += "<tr class='"+table_class+"'><td class='row-title'>"+
                        v.fb_name + " " +link
                        +"</td></tr>";
                });
                jQuery('#newsBooster-loader').remove();
                jQuery('#newsBooster_user_pages tbody').append(
                    append
                );
            }
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}

(function( $ ) {
	'use strict';

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
        });

        $('#newsBooster-show_pages').text('Load your Facebook pages (permissions popup will show up)');
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) { return; }
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/"+newsBooster.locale+"/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    function confirmOptIn(user_ref){
        console.log('wysylam');
        var response = FB.AppEvents.logEvent('MessengerCheckboxUserConfirmation', null, {
            'app_id':$('.fb-messenger-checkbox.fb_iframe_widget').attr('messenger_app_id'),
            'page_id':$('.fb-messenger-checkbox.fb_iframe_widget').attr('page_id'),
            'ref':'newsBooster-wordpress-plugin',
            'user_ref':user_ref
        });
        console.log(response);
        console.log('wyslalem');

    }

    $(document).on('click','.newsBooster_activate_page',function(e){
        e.preventDefault();

        var parent = $(this).parent();
        $(this).remove();
        parent.append(getLoaderHtml('Activating...'));

        var page_id = $(this).attr('data-id');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                'action': 'activate_page',
                'fb_page_id': page_id,
            },
            success:function(data) {
                //errors
                if(data.error) {
                    alert(data.error);
                    return;
                }
                //messages
                if(data.message)
                    alert(data.message);

                //$( '#newsBooster-active-fbpage-id' ).val(data.fb_page_id);
                //$( '#newsBooster-active-fbpage-name' ).val(data.fb_page_name);
                //jQuery('form #submit').click();
                location.reload();
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });
    });

    $(document).on('click','#newsBooster-send-template-test',function(e){
        jQuery('#newsBooster-send-template-test').parent().html(getLoaderHtml('Sending, please wait...'));
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                'action': 'send_test',
            },
            success:function(data) {
                //errors
                if(data.error) {
                    alert(data.error);
                }
                //messages
                if(data.message)
                    alert(data.message);

                //jQuery('form #submit').click();
                location.reload();
            },
            error: function(errorThrown){
                alert('Communication error. Please try again.');
                //location.reload();
            }
        });
    });

    $(document).on('click','#newsBooster-refresh-count',function(e){
        jQuery('#newsBooster-refresh-count').parent().html(getLoaderHtml('Refreshing'));
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                'action': 'refresh_count',
            },
            success:function(data) {
                //errors
                if(data.error) {
                    alert(data.error);
                }
                //messages
                if(data.message)
                    alert(data.message);

                //jQuery('form #submit').click();
                location.reload();
            },
            error: function(errorThrown){
                alert('Communication error. Please try again.');
                //location.reload();
            }
        });
    });

	$(function(){
        if($('#newsBooster_user_pages').length && !$( '#newsBooster-active-fbpage-id' ).val().length){
            showPages();
        }

    }); // End of DOM Ready
})( jQuery );
