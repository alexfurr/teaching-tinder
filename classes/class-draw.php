<?php
class teaching_tinder_draw
{
    public static function draw_opps_list()
    {

        $filter_menu = ''; // The filter menu
        $opp_list_str = ''; // Main opp list string
        $search_str_feedback = '';

        $now = date('Y-m-d H:i:s');

        $opp_count = 0; // Counter for the valid opportunities with valid dates

        // Create an array of

        $filter_args = array();
        $this_event_type_filter_id = ''; // Create var for filter to highlight the

        if(isset($_GET['filter']) )
        {
            if(isset($_GET['event_type']) )
            {
                $filter_args['event_type'] = $_GET['event_type'];
                $this_event_type_filter_id = $_GET['event_type'];
            }

            if(isset($_GET['role_type']) )
            {
                $filter_args['role_type'] = $_GET['role_type'];
            }
        }

        if(isset($_POST['tt_search']) )
        {
            $filter_args['search'] = $_POST['tt_search'];
            $search_str_feedback = '<div class="tt_search_feedback">Search results for "'.$_POST['tt_search'].'"</div>';
        }

        $opp_list = teaching_tinder_queries::get_opps($filter_args);

        // Get a series of lookup arrays for the meta data
        $event_types = teaching_tinder_queries::get_cat_items('tt_event_type');
        $cohorts = teaching_tinder_queries::get_cat_items('tt_cohort');
        $modules = teaching_tinder_queries::get_cat_items('tt_module');

        $filter_menu.='<form action="?action=search" method="post">';
        $filter_menu.='<div class="tt_filter">';

        $filter_menu.='<div class="tt_filter_types">Filter : <i class="fas fa-filter"></i> ';

        $filter_menu.='<a href="?filter=off">Show All</a> | ';
        foreach ($event_types as $event_type_id => $event_type_name)
        {
            if($this_event_type_filter_id<>$event_type_id)
            {
                $filter_menu.='<a href="?filter=on&event_type='.$event_type_id.'">';
            }
            else
            {
                $filter_menu.='<strong>';
            }

            $filter_menu.=$event_type_name;
            if($this_event_type_filter_id<>$event_type_id)
            {
                $filter_menu.='</a>';
            }
            else
            {
                $filter_menu.='</strong>';

            }
            $filter_menu.=' | ';
        }
        $filter_menu.='</div>';


        $filter_menu.='<div class="filter_search">';
        $filter_menu.='<input type="text" name="tt_search" class="tt_search_box"><input class="imperial-button" type="submit" value="search">';
        $filter_menu.='</div>';
        $filter_menu.='</div></form>';



        if(count($opp_list)==0 )
        {
            $opp_list_str.='No opporunities found';
        }


        $opp_list_str.= '<div class="tt_list_wrapper" id="tt_listener_wrap">';
        $opp_count=0;
        foreach ($opp_list as $item_id => $item_meta)
        {
            // Get the dates for this opportunity
            $opp_dates = teaching_tinder_queries::get_opp_dates($item_id);


            $date_count = count($opp_dates);
            if($date_count==0) // if there are NO dates then skip
            {
                continue;
            }

            // Go through the dates and do a count of valid dates
            $valid_dates_array = array(); // Create an array for valid dates in the future
            $first_date_added = false;
            foreach ($opp_dates as $date_meta)
            {
                // Check vis settings
                $visibility = $date_meta->visibility;
                if($visibility==0){continue;}

                // Check start date
                $start_date = $date_meta->start_date;
                if($now>$start_date){continue;}

                // It passes and is in the future so we can show it
                $valid_dates_array[] = $start_date;
            }


            $valid_dates_count = count($valid_dates_array);

            if($valid_dates_count==0){continue;} // Skip it if there are no dates
            $permalink = get_the_permalink($item_id);
            $title = get_the_title($item_id);

            $event_type = $item_meta['event_type'];

            $event_type_name = get_term( $event_type )->name;
            $this_background = get_option('event_color_'.$event_type);

            // Get the faded colour
            $faded_color = tt_opps_utils::adjust_brightness($this_background, 50);


            $role_type = $item_meta['role_type'];
            $role_type_name = get_term( $role_type )->name;
            $role_type_img = z_taxonomy_image_url($role_type);




            $opp_list_str.= '<div class="tt_item" id="item_wrap_'.$item_id.'">';

            $opp_list_str.='<a href="'.$permalink.'">';
            $opp_list_str.='<div style="background:#'.$this_background.'">';
            $opp_list_str.='<div class="tt_item_title">';

            // Type image
            $opp_list_str.='<div class="role_type_img">';
            if($role_type_img)
            {
                $opp_list_str.='<img src="'.$role_type_img.'">';
            }
            $opp_list_str.='</div>';

            // Type Title
            $opp_list_str.='<div class="role_type_name">';
            $opp_list_str.=$event_type_name.' : '.$role_type_name;
            $opp_list_str.='</div>';

            // Nav Arrow
            $opp_list_str.='<div class="tt_item_nav">';
            $opp_list_str.='<i class="fas fa-chevron-circle-right fa-3x"></i>';
            $opp_list_str.='</div>';

            $opp_list_str.='</div>';
            $opp_list_str.='</div>';
            $opp_list_str.='</a>';



            $opp_list_str.='<div class="tt_item_meta_wrap" style="background:'.$faded_color.';">';

            $opp_list_str.='<div class="tt_item_subtitle">';
            $opp_list_str.=$title;
            // Show the hide button
            $opp_list_str.='<div class="hide_opp_wrap"><button class="imperial-button"><span data-id="'.$item_id.'" data-method="hide_opp" class="smallText has-click-event" id="hide_event_button_'.$item_id.'">Hide opportunity</span></button></a></div>';

            $opp_list_str.='</div>';

            $opp_list_str.='<div class="tt_item_meta">';
            $opp_list_str.='<h3>Who are the students?</h3>';
            $opp_list_str.= $cohorts[$item_meta['cohort']];
            $opp_list_str.= '<br/><span class="smallText">'.$modules[$item_meta['module']].'</span>';
            $opp_list_str.='<br/>';
            $opp_list_str.='</div>';


            $opp_list_str.='<div class="tt_item_dates">';
            $first_date = $valid_dates_array[0];
            $date_obj = new DateTime($first_date);
            $first_date_str = $date_obj->format("l jS F, Y");
            $other_dates = $valid_dates_count-1;

            $opp_list_str.='<h3>First available date</h3>';

            $opp_list_str.= $first_date_str.'<br/>';
            if($other_dates>=1)
            {
                $opp_list_str.='<span class="smallText">'.$other_dates.' later date(s) available</span>';
            }
            $opp_list_str.='</div>';



            $opp_list_str.='</div>';

            $opp_list_str.='</div>';

            $opp_count++;


        }
        $opp_list_str.='</div>';

        if($opp_count==0)
        {
            $opp_list_str = '<br/>No opportunities found';
        }

        return $filter_menu.$search_str_feedback.$opp_list_str;

   }

   public static function draw_feedback()
   {

       $html = '';
       // Hadnle any feedback messages
       if(isset($_GET['feedback']) )
       {
           $feedback = $_GET['feedback'];

           // Firstly check if there is anyt feedback
           if(isset($_GET['errormsg']) )
           {
               $html =  imperialNetworkDraw::imperialFeedback($_GET['errormsg'], 'error');
           }
           else
           {

               switch ($feedback)
               {
                   case "date_added":
                       $html= imperialNetworkDraw::imperialFeedback("Date Added!");
                   break;

                   case "bulk_dates_delete":
                       $html= imperialNetworkDraw::imperialFeedback("Dates Deleted!");
                   break;

                   case "bulk_dates_recruiting":
                       $html= imperialNetworkDraw::imperialFeedback("Dates changed to recruiting");
                   break;

                   case "bulk_dates_waitlist":
                       $html= imperialNetworkDraw::imperialFeedback("Dates changed to waitlist");
                   break;

                   case "bulk_dates_vis_hide":
                       $html= imperialNetworkDraw::imperialFeedback("Dates changed to hidden");
                   break;

                   case "bulk_dates_vis_shown":
                       $html= imperialNetworkDraw::imperialFeedback("Dates changed to visible");
                   break;


               }
           }

       }

       return $html;


   }

   public static function draw_interest_button($date_id, $interest_status)
   {

       $html = '';
       if($interest_status==1)
       {
           $button_text = 'Remove Interest';
           $html.= '<span class="successText"><i class="fas fa-check-square fa-2x"></i></span> ';
           $class="smallText";


       }
       else
       {
           $button_text = 'Express Interest';
           $class="imperial-button";
       }

       $html.= '<a href="" class="'.$class.' has-click-event" data-method="toggle_interest" data-id="'.$date_id.'">'.$button_text.'</a>';;


       return $html;

   }

}

?>
