<?php
class teaching_tinder_queries
{
    public static function get_opps($filter_args=array())
    {


        $meta_query_array = array();
        $meta_query_array['relation'] =  "AND";    // create blank array for the filter on meta query if applicable

        if(isset($filter_args['event_type']) )
        {
            $event_type_id = $filter_args['event_type'];
            $meta_query_array[] =  array(
                'key' => 'event_type',
                'value' => $event_type_id,
                'compare' => '='
            );
        }


        $args = array(
            'posts_per_page'   => -1,
            'orderby'           => 'title',
            'order'            => 'ASC',
            'post_type'        => 'tt_opp',
            'post_status'      => 'publish',
            'meta_query'        => $meta_query_array,

        );


        // Check for search
        if(isset($filter_args['search']) )
        {
            $search_str = $filter_args['search'];
            $args["s"] = $search_str;

            /*

            $args['_meta_or_title'] = $search_str; //not using 's' anymore

            $meta_query_array[]   = array(
                array( 'key' => 'who', 'value' => $search_str, 'compare' => 'LIKE' ),
                'relation' => 'OR'
            );
            */


        }


        // Get the list of items they have hidden
        $logged_in_username = imperialNetworkUtils::get_current_username();

        $hidden_opps = teaching_tinder_queries::get_user_hidden_opps($logged_in_username);

        $posts_array = get_posts( $args );
        $items_array = array();

        foreach ($posts_array as $item_info)
        {
            $post_id = $item_info->ID;

            if(in_array($post_id, $hidden_opps) ){continue;} // if its hidden then skip
            $item_meta = get_post_meta($post_id);
            $item_keys = tt_opps::get_meta_items();
            foreach ($item_keys as $this_key)
            {
                $$this_key = ''; // Defualt set to blank
                if(array_key_exists($this_key, $item_meta) )
                {
                    $$this_key = $item_meta[$this_key][0];
                }

                $items_array[$post_id][$this_key] = $$this_key;
            }
        }
        return $items_array;

    }

    public static function get_cat_items($cat_slug)
    {

        $terms =  get_terms([
            'taxonomy' => $cat_slug,
            'hide_empty' => false,
        ]);

        $array = array();


        foreach ($terms as $term_meta)
        {
            $term_name = $term_meta->name;
            $term_id = $term_meta->term_id;
            $array[$term_id] = $term_name;
        }

        return $array;

    }



    public static function get_opp_dates($post_id)
    {
        global $wpdb;
        global $tt_opp_dates_table;

        $sql = "SELECT * FROM $tt_opp_dates_table WHERE opp_id= $post_id ORDER by start_date ASC";

        $opp_dates =  $wpdb->get_results( $sql );
        return $opp_dates;

    }

    public static function get_opp_date_info($date_id)
    {
        global $wpdb;
        global $tt_opp_dates_table;

        $sql = "SELECT * FROM $tt_opp_dates_table WHERE id= $date_id";

        $date_info =  $wpdb->get_row( $sql );
        return $date_info;
    }

    public static function get_users_by_opp_date($date_id)
    {
        global $wpdb;
        global $tt_opp_dates_table_interest;

        $sql = "SELECT * FROM $tt_opp_dates_table_interest WHERE id= $date_id";
        $my_users =  $wpdb->get_results( $sql );
        return $my_users;
    }


    //Gets all dates this user has signe dup for
    public static function get_user_interest($username)
    {
        global $wpdb;
        global $tt_opp_dates_table_interest;

        $sql = "SELECT * FROM $tt_opp_dates_table_interest WHERE username= '$username'";

        $opp_dates =  $wpdb->get_results( $sql );

        $date_array = array();
        foreach ($opp_dates as $date_info)
        {
            $date_array[] = $date_info->date_id;


        }
        return $date_array;

    }

    //Gets opps user has hidden
    public static function get_user_hidden_opps($username)
    {
        global $wpdb;
        global $tt_opp_dates_table_hidden;

        $sql = "SELECT * FROM $tt_opp_dates_table_hidden WHERE username= '$username'";

        $opp_items =  $wpdb->get_results( $sql );

        $opp_items_array = array();
        foreach ($opp_items as $opp_info)
        {
            $opp_items_array[] = $opp_info->opp_id;


        }
        return $opp_items_array;

    }


}
?>
