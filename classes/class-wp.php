<?php
$icl_tt = new icl_tt();
class icl_tt
{


	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}

/*	---------------------------
	PRIMARY HOOKS INTO WP
	--------------------------- */
	function addWPActions ()
	{
        add_action('init', array($this, 'check_for_actions') );
        add_filter( 'single_template', array($this, 'load_my_custom_template'), 50, 1 );
        add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );

        add_shortcode( 'teaching-tinder', array( 'teaching_tinder_draw', 'draw_opps_list' ) );

	}

    function load_frontend_scripts() {
        wp_enqueue_script( 'jquery' );
       // wp_enqueue_style('lh_quotes_css', plugins_url('../css/lh-crm.css',__FILE__) );
        //wp_enqueue_script('lh_quote_template_js', plugins_url('../js/quote-template.js',__FILE__) ); #
        wp_enqueue_style('tt_styles', ICL_TEACHING_TINDER_URL.'/css/styles.css' );



        // Load JS / AJAX script
        wp_enqueue_script('icl_tt_scripts', ICL_TEACHING_TINDER_URL.'/js/scripts.js' );

        //Localise the JS file
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce('icl_tt_ajax_nonce'),
        );

        wp_localize_script( 'icl_tt_scripts', 'icl_tt_ajax_params', $params );


    }

    function load_admin_scripts( ) {

       // wp_enqueue_script('lh_quotes_js', plugins_url('../js/quote.js',__FILE__) ); #
      //  wp_enqueue_style( 'imperial-font-awesome', '//use.fontawesome.com/releases/v5.2.0/css/all.css' );

        // Calendar stuff
       // wp_enqueue_style('mbm_calendar_style', plugins_url('../css/calendar.css',__FILE__) );
     //   wp_enqueue_script('mbm_calendar_scripts', plugins_url('../js/calendar.js',__FILE__) );

        //Localise the JS file
        $params = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'ajax_nonce' => wp_create_nonce('icl_tt_ajax_nonce'),
        );


        //wp_localize_script( 'icl_tt_scripts', 'icl_tt_ajax_params', $params );


    }

    // Load a custom template for the quotes CPT
    function load_my_custom_template( $template )
    {


        if ( is_singular( 'tt_opp' ) ) {
            $template = ICL_TEACHING_TINDER_PATH.'/templates/tt_template.php';
        }

        return $template;
    }



    public static function check_for_actions()
    {

        if(isset($_GET['action']) )
        {



            $myAction = $_GET['action'];

            $home_url = get_site_url();

            switch ($myAction)
            {
                case "add_tt_date":

                    $opp_id = $_GET['opp_id'];

                    teaching_tinder_actions::date_add();
                    $redirectURL = $home_url.'/wp-admin/options.php?page=tt-opp-dates&opp_id='.$opp_id.'&feedback=date_added';
                    wp_redirect($redirectURL);
                    exit();

                break;

                case "bulk_action_opp_dates":

                    $opp_id = $_GET['opp_id'];
                    $feedback = teaching_tinder_actions::process_dates_bulk_action();
                    $redirectURL = $home_url.'/wp-admin/options.php?page=tt-opp-dates&opp_id='.$opp_id.'&feedback='.$feedback;
                    wp_redirect($redirectURL);
                    exit();

                break;

                case "save-tt-settings":
                    teaching_tinder_actions::save_settings();
                    $redirectURL = $home_url.'/wp-admin/edit.php?post_type=tt_opp&page=tt-settings&feedback=settings_saved';
                    wp_redirect($redirectURL);
                    exit();
                break;


            }

        }


     }


}
?>
