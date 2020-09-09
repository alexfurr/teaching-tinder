
var TT_JS = {

    //---
    site_wrapper_id:    'tt_listener_wrap',

    //---
    init: function () {
        this.add_listeners();
    },

    //---
    add_listeners: function () {
        jQuery('#' + TT_JS.site_wrapper_id ).on( 'click', '.has-click-event', function ( event ) {
            TT_JS.on_ui_event( event, this );
            event.preventDefault();
        });

    },

    //---
    on_ui_event: function ( event, element ) {
        var method = jQuery( element ).attr('data-method');
        if ( typeof TT_JS[ method ] !== 'undefined' ) {

            TT_JS[ method ]( event, element );
        }
    },

    // List of actual interactions
    //---
    toggle_interest: function ( event, element ) {
        var date_id = jQuery( element ).attr('data-id');

        var button_wrap_id = 'interest_button_wrap_'+date_id;

        document.getElementById(button_wrap_id).innerHTML = 'Please Wait...';

        jQuery.ajax({
            type: 'POST',
            url: icl_tt_ajax_params.ajaxurl,
            data: {
                "action": "express_interest_toggle",
                "date_id": date_id,
                "security":icl_tt_ajax_params.ajax_nonce,

            },
            success: function(data){
            document.getElementById(button_wrap_id).innerHTML = data;
         }
        });

    },

    //---
    hide_opp: function ( event, element ) {
        var opp_id = jQuery( element ).attr('data-id');
        var item_wrap_id = 'item_wrap_'+opp_id;

        var button_id = "hide_event_button_"+opp_id;

        console.log(item_wrap_id);
        console.log(opp_id);
        document.getElementById(button_id).innerHTML = 'Please Wait...';

        jQuery.ajax({
            type: 'POST',
            url: icl_tt_ajax_params.ajaxurl,
            data: {
                "action": "hide_opportunity",
                "opp_id": opp_id,
                "security":icl_tt_ajax_params.ajax_nonce,

            },
            success: function(data){
                jQuery("#"+item_wrap_id).hide("fast");
             }
        });


    },



};

jQuery( document ).ready( function () {
    TT_JS.init();
});
