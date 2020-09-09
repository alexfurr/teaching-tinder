<?php


class teaching_tinder_actions
{


    public static function date_add()
    {
        global $wpdb;
        global $tt_opp_dates_table;



        $opp_id = $_POST['opp_id'];
        $opp_date = $_POST['opp_date'];
        $location = $_POST['location'];
        $spaces = $_POST['spaces'];


        $start_time = $_POST['start_time_hour'].':'.$_POST['start_time_min'];
        $end_time = $_POST['end_time_hour'].':'.$_POST['end_time_min'];

        $start_date = $opp_date.' '.$start_time;
        $end_date = $opp_date.' '.$end_time;

        $item_id = '';

        if($item_id)
        {
            $wpdb->query( $wpdb->prepare(
                "UPDATE   ".$tt_opp_dates_table." SET activity_title=%s, activity_content=%s, activity_date=%s WHERE id = %d;",
                $activity_title,
                $activity_content,
                $activity_date,
                $spaces,
                $item_id
            ));


           //return "timeline_item_updated";

        }
        else
        {

            $wpdb->query( $wpdb->prepare(
            "INSERT INTO ".$tt_opp_dates_table." (opp_id, start_date, end_date, location, spaces, status, visibility)
            VALUES ( %d, %s, %s, %s, %d, %d, %d )",
            array(
                $opp_id,
                $start_date,
                $end_date,
                $location,
                $spaces,
                1,
                1
                )
            ));

            //return "timeline_item_added";
        }

    }

    public static function process_dates_bulk_action()
    {
        global $wpdb;
        global $tt_opp_dates_table;


        $this_action = $_POST['bulk_actions'];


        switch ($this_action)
        {
            case "status_recruiting":
                $this_col = 'status';
                $this_value = 1;
                $feedback = "bulk_dates_recruiting";
            break;

            case "status_waitlist":
                $this_col = 'status';
                $this_value = 0;
                $feedback = "bulk_dates_recruiting";
            break;

            case "vis_shown":
                $this_col = 'visibility';
                $this_value = 1;
                $feedback = "bulk_dates_vis_shown";
            break;

            case "vis_hidden":
                $this_col = 'visibility';
                $this_value = 0;
                $feedback = "bulk_dates_vis_hide";
            break;

            case "delete":
                teaching_tinder_actions::dates_delete();
                $feedback = "bulk_dates_delete";
            break;

        }

        if($this_action<>"delete")
        {

            $check_list = $_POST['check_list'];
            foreach ($check_list as $date_id)
            {

                $wpdb->query( $wpdb->prepare(
                    "UPDATE   ".$tt_opp_dates_table." SET ".$this_col."=%d WHERE id = %d;",
                    $this_value,
                    $date_id
                ));
            }
        }

        return $feedback;

    }




    public static function dates_delete()
    {

        global $wpdb;
        global $tt_opp_dates_table;

        $check_list = $_POST['check_list'];
        foreach ($check_list as $date_id)
        {
            global $wpdb;
            global $dbTable_tutorBookings;

            $SQL = "DELETE FROM  $tt_opp_dates_table WHERE id = ".$date_id;
            $wpdb->query( $SQL );
        }


    }

    // Save the hex keys as options
    public static function save_settings()
    {
        foreach ($_POST as $KEY => $VALUE)
        {
            update_option($KEY, $VALUE);
        }

    }

}
?>
