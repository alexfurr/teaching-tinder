<?php

$tt_database = new tt_database();

class tt_database
{
	var $DBversion 		= '1.6';

	//~~~~~
	function __construct ()
	{
        add_action( 'init',  array( $this, 'checkCompat' ) );

        global $wpdb;
        global $tt_opp_dates_table;
        global $tt_opp_dates_table_interest;

        $tt_opp_dates_table = $wpdb->prefix . 'tt_opp_dates';
        $tt_opp_dates_table_interest = $wpdb->prefix . 'tt_opp_dates_interest';
	}

	//~~~~~
	function checkCompat ()
	{

		// Get the Current DB and check against this verion
		$currentDBversion = get_option('ttinder_db_version');
		$thisDBversion = $this->DBversion;


		if($thisDBversion>$currentDBversion)
		{

			$this->createTables();
			update_option('ttinder_db_version', $thisDBversion);
		}
		//$this->createTables();
	}



	function createTables ()
	{


        global $wpdb;
        global $tt_opp_dates_table;
        global $tt_opp_dates_table_interest;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$WPversion = substr( get_bloginfo('version'), 0, 3);
		$charset_collate = ( $WPversion >= 3.5 ) ? $wpdb->get_charset_collate() : $this->getCharsetCollate();

		//Dates table
		$sql = "CREATE TABLE $tt_opp_dates_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
            opp_id int,
            start_date datetime,
            end_date datetime,
			location longtext,
            visibility tinyint,
            status  tinyint,
			INDEX opp_id (opp_id),
			PRIMARY KEY (id)

		) $charset_collate;";

		$feedback = dbDelta( $sql );

       // Date interst table
       //users table
       $sql = "CREATE TABLE $tt_opp_dates_table_interest (
           id mediumint(9) NOT NULL AUTO_INCREMENT,
            date_id int,
            username varchar(50),
            date_added datetime,
            INDEX opp_id (opp_id),
            INDEX username (username),
            INDEX lookup_both (opp_id,username),
            PRIMARY KEY (id)

       ) $charset_collate;";

       $feedback = dbDelta( $sql );

	}


	function getCharsetCollate ()
	{
		global $wpdb;
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) )
		{
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) )
		{
			$charset_collate .= " COLLATE $wpdb->collate";
		}
		return $charset_collate;
	}

}



?>
