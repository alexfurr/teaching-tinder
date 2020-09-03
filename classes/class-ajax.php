<?php

$tt_ajax = new tt_ajax();
class tt_ajax
{

	//~~~~~
	public function __construct ()
	{
		$this->addWPActions();


	}


	function addWPActions()
	{


        // Front End
        add_action( 'wp_ajax_express_interest_toggle', array($this, 'express_interest_toggle' ));
        add_action( 'wp_ajax_nopriv_express_interest_toggle', array($this, 'express_interest_toggle' ));

	}
	public function express_interest_toggle()
	{

        global $wpdb;
        global $tt_opp_dates_table_interest;

      // Check the AJAX nonce
		check_ajax_referer( 'icl_tt_ajax_nonce', 'security' );

        $logged_in_username = imperialNetworkUtils::get_current_username();
        $my_interest_dates = teaching_tinder_queries::get_user_interest($logged_in_username);
        $date_id = $_POST['date_id'];

        // have they signed up?
        if(in_array($date_id, $my_interest_dates) )
        {
            // Delete it
           $SQL = "DELETE FROM  $tt_opp_dates_table_interest WHERE date_id = $date_id AND username = '$logged_in_username'";
           $wpdb->query( $SQL );
           $interest_status = 0;

        }
        else
        {
            $wpdb->query( $wpdb->prepare(
            "INSERT INTO ".$tt_opp_dates_table_interest." (date_id, username, date_added)
            VALUES ( %d, %s, %s)",
            array(
                $date_id,
                $logged_in_username,
                date('Y-m-d H:i:s'),
                )
            ));


            /// Email anyone associated with that project
            $date_meta = teaching_tinder_queries::get_opp_date_info($date_id);
            $opp_id = $date_meta->opp_id;
            $start_date = $date_meta->start_date;
            $date_obj = new DateTime($start_date);
            $opp_date_str = $date_obj->format("l jS F, Y, g:i a");
            $interest_email_contact = get_post_meta($opp_id, 'interest_email_contact', true);
            if($interest_email_contact)
            {
                $opp_name = get_the_title($opp_id);
                $headers = array('Content-Type: text/html; charset=UTF-8');

                $current_user_meta = imperialQueries::getUserInfo($logged_in_username);
                $user_fullname = $current_user_meta['first_name'].' '.$current_user_meta['last_name'];
                $user_email = $current_user_meta['email'];

                $title = 'New expression of interest for '.$opp_name;
                $content = $user_fullname.' ('.$user_email.') has shown interest in the date '.$opp_date_str;
                wp_mail($interest_email_contact, $title, $content, $headers);
            }


            $interest_status = 1;



        }

        echo teaching_tinder_draw::draw_interest_button($date_id, $interest_status);

        die();

	}

} // End Class
?>
